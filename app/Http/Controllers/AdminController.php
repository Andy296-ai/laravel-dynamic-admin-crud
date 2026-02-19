<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of all database tables.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Get all table names for sidebar navigation.
     *
     * @return array
     */
    private function getTableNames()
    {
        $database = config('database.connections.mysql.database');
        $tables = DB::select("SHOW TABLES");
        
        // Extract table names from the result
        $tableKey = "Tables_in_{$database}";
        $tableNames = array_map(function($table) use ($tableKey) {
            return $table->$tableKey;
        }, $tables);
        
        // Filter out system tables if needed
        $tableNames = array_filter($tableNames, function($table) {
            return !in_array($table, ['migrations', 'password_reset_tokens', 'password_resets', 'failed_jobs', 'personal_access_tokens']);
        });
        
        return array_values($tableNames);
    }

    public function index()
    {
        $tableNames = $this->getTableNames();
        return view('admin.index', compact('tableNames'));
    }

    /**
     * Display all rows from a specific table.
     *
     * @param  string  $table
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showTable($table, Request $request)
    {
        // Validate table name to prevent SQL injection
        if (!$this->isValidTableName($table)) {
            abort(404, 'Table not found');
        }

        // Get table columns
        $columns = $this->getTableColumns($table);
        
        // Get search query
        $search = $request->get('search', '');
        
        // Get sort parameters
        $sortColumn = $request->get('sort', null);
        $sortDirection = $request->get('direction', 'asc');
        
        // Build query
        $query = DB::table($table);
        
        // Apply search
        if ($search) {
            $query->where(function($q) use ($columns, $search) {
                foreach ($columns as $column) {
                    // Skip binary/blob columns and numeric columns for text search
                    if (!in_array($column['type'], ['blob', 'longblob', 'binary', 'varbinary']) &&
                        !in_array($column['type'], ['int', 'bigint', 'tinyint', 'smallint', 'mediumint', 'decimal', 'float', 'double'])) {
                        $q->orWhere($column['name'], 'LIKE', "%{$search}%");
                    } elseif (in_array($column['type'], ['int', 'bigint', 'tinyint', 'smallint', 'mediumint']) && is_numeric($search)) {
                        // Allow numeric search for integer columns
                        $q->orWhere($column['name'], '=', $search);
                    }
                }
            });
        }
        
        // Apply sorting
        if ($sortColumn && in_array($sortColumn, array_column($columns, 'name'))) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            // Default sort by first column (usually id)
            $query->orderBy($columns[0]['name'], 'asc');
        }
        
        // Paginate results
        $perPage = 15;
        $rows = $query->paginate($perPage)->withQueryString();
        
        $tableNames = $this->getTableNames();
        return view('admin.table', compact('table', 'columns', 'rows', 'search', 'sortColumn', 'sortDirection', 'tableNames'));
    }

    /**
     * Show the form for creating a new row.
     *
     * @param  string  $table
     * @return \Illuminate\View\View
     */
    public function create($table)
    {
        if (!$this->isValidTableName($table)) {
            abort(404, 'Table not found');
        }

        $columns = $this->getTableColumns($table);
        
        // Filter out auto-increment primary keys and timestamps
        $formColumns = array_filter($columns, function($column) {
            return !$column['auto_increment'] && 
                   !in_array($column['name'], ['created_at', 'updated_at', 'deleted_at']);
        });
        
        $tableNames = $this->getTableNames();
        return view('admin.create', compact('table', 'formColumns', 'tableNames'));
    }

    /**
     * Store a newly created row in storage.
     *
     * @param  string  $table
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($table, Request $request)
    {
        if (!$this->isValidTableName($table)) {
            abort(404, 'Table not found');
        }

        $columns = $this->getTableColumns($table);
        
        // Build validation rules
        $rules = [];
        $formColumns = array_filter($columns, function($column) {
            return !$column['auto_increment'] && 
                   !in_array($column['name'], ['created_at', 'updated_at', 'deleted_at']);
        });
        
        foreach ($formColumns as $column) {
            $rule = [];
            
            if ($column['nullable'] === false && $column['default'] === null) {
                $rule[] = 'required';
            }
            
            // Type-specific validation
            if (strpos($column['type'], 'int') !== false) {
                $rule[] = 'integer';
            } elseif (strpos($column['type'], 'decimal') !== false || strpos($column['type'], 'float') !== false || strpos($column['type'], 'double') !== false) {
                $rule[] = 'numeric';
            } elseif (strpos($column['type'], 'date') !== false) {
                $rule[] = 'date';
            } elseif (strpos($column['type'], 'datetime') !== false || strpos($column['type'], 'timestamp') !== false) {
                $rule[] = 'date';
            } elseif (strpos($column['type'], 'email') !== false || $column['name'] === 'email') {
                $rule[] = 'email';
            }
            
            if ($column['max_length']) {
                $rule[] = "max:{$column['max_length']}";
            }
            
            $rules[$column['name']] = implode('|', $rule);
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->route('admin.create', $table)
                ->withErrors($validator)
                ->withInput();
        }
        
        // Prepare data for insertion
        $data = [];
        foreach ($formColumns as $column) {
            $value = $request->input($column['name']);
            
            if ($value === null && $column['nullable']) {
                $data[$column['name']] = null;
            } elseif ($value !== null) {
                $data[$column['name']] = $value;
            }
        }
        
        // Add timestamps if columns exist
        if (Schema::hasColumn($table, 'created_at') && Schema::hasColumn($table, 'updated_at')) {
            $data['created_at'] = now();
            $data['updated_at'] = now();
        }
        
        try {
            DB::table($table)->insert($data);
            
            return redirect()->route('admin.table', $table)
                ->with('success', 'Row created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.create', $table)
                ->with('error', 'Error creating row: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified row.
     *
     * @param  string  $table
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($table, $id)
    {
        if (!$this->isValidTableName($table)) {
            abort(404, 'Table not found');
        }

        $columns = $this->getTableColumns($table);
        $primaryKey = $this->getPrimaryKey($table);
        
        $row = DB::table($table)->where($primaryKey, $id)->first();
        
        if (!$row) {
            abort(404, 'Row not found');
        }
        
        // Filter out auto-increment primary keys and timestamps
        $formColumns = array_filter($columns, function($column) {
            return !in_array($column['name'], ['created_at', 'updated_at', 'deleted_at']);
        });
        
        $tableNames = $this->getTableNames();
        return view('admin.edit', compact('table', 'formColumns', 'row', 'id', 'primaryKey', 'tableNames'));
    }

    /**
     * Update the specified row in storage.
     *
     * @param  string  $table
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($table, $id, Request $request)
    {
        if (!$this->isValidTableName($table)) {
            abort(404, 'Table not found');
        }

        $columns = $this->getTableColumns($table);
        $primaryKey = $this->getPrimaryKey($table);
        
        // Check if row exists
        $row = DB::table($table)->where($primaryKey, $id)->first();
        if (!$row) {
            abort(404, 'Row not found');
        }
        
        // Build validation rules
        $rules = [];
        $formColumns = array_filter($columns, function($column) use ($primaryKey) {
            return $column['name'] !== $primaryKey && 
                   !in_array($column['name'], ['created_at', 'updated_at', 'deleted_at']);
        });
        
        foreach ($formColumns as $column) {
            $rule = [];
            
            if ($column['nullable'] === false && $column['default'] === null) {
                $rule[] = 'required';
            }
            
            // Type-specific validation
            if (strpos($column['type'], 'int') !== false) {
                $rule[] = 'integer';
            } elseif (strpos($column['type'], 'decimal') !== false || strpos($column['type'], 'float') !== false || strpos($column['type'], 'double') !== false) {
                $rule[] = 'numeric';
            } elseif (strpos($column['type'], 'date') !== false) {
                $rule[] = 'date';
            } elseif (strpos($column['type'], 'datetime') !== false || strpos($column['type'], 'timestamp') !== false) {
                $rule[] = 'date';
            } elseif (strpos($column['type'], 'email') !== false || $column['name'] === 'email') {
                $rule[] = 'email';
            }
            
            if ($column['max_length']) {
                $rule[] = "max:{$column['max_length']}";
            }
            
            $rules[$column['name']] = implode('|', $rule);
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->route('admin.edit', [$table, $id])
                ->withErrors($validator)
                ->withInput();
        }
        
        // Prepare data for update
        $data = [];
        foreach ($formColumns as $column) {
            $value = $request->input($column['name']);
            
            if ($value === null && $column['nullable']) {
                $data[$column['name']] = null;
            } elseif ($value !== null) {
                $data[$column['name']] = $value;
            }
        }
        
        // Update timestamps if columns exist
        if (Schema::hasColumn($table, 'updated_at')) {
            $data['updated_at'] = now();
        }
        
        try {
            DB::table($table)->where($primaryKey, $id)->update($data);
            
            return redirect()->route('admin.table', $table)
                ->with('success', 'Row updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.edit', [$table, $id])
                ->with('error', 'Error updating row: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified row from storage.
     *
     * @param  string  $table
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($table, $id)
    {
        if (!$this->isValidTableName($table)) {
            abort(404, 'Table not found');
        }

        $primaryKey = $this->getPrimaryKey($table);
        
        try {
            DB::table($table)->where($primaryKey, $id)->delete();
            
            return redirect()->route('admin.table', $table)
                ->with('success', 'Row deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.table', $table)
                ->with('error', 'Error deleting row: ' . $e->getMessage());
        }
    }

    /**
     * Get all columns for a table with metadata.
     *
     * @param  string  $table
     * @return array
     */
    private function getTableColumns($table)
    {
        $database = config('database.connections.mysql.database');
        
        $columns = DB::select("
            SELECT 
                COLUMN_NAME as name,
                DATA_TYPE as type,
                IS_NULLABLE as nullable,
                COLUMN_DEFAULT as default_value,
                CHARACTER_MAXIMUM_LENGTH as max_length,
                COLUMN_KEY as column_key,
                EXTRA as extra
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$database, $table]);
        
        return array_map(function($column) {
            return [
                'name' => $column->name,
                'type' => $column->type,
                'nullable' => $column->nullable === 'YES',
                'default' => $column->default_value,
                'max_length' => $column->max_length,
                'primary' => $column->column_key === 'PRI',
                'auto_increment' => strpos($column->extra, 'auto_increment') !== false,
            ];
        }, $columns);
    }

    /**
     * Get the primary key column name for a table.
     *
     * @param  string  $table
     * @return string
     */
    private function getPrimaryKey($table)
    {
        $columns = $this->getTableColumns($table);
        $primaryKeyColumn = array_filter($columns, function($column) {
            return $column['primary'];
        });
        
        if (count($primaryKeyColumn) > 0) {
            return reset($primaryKeyColumn)['name'];
        }
        
        // Fallback to 'id' if no primary key found
        return 'id';
    }

    /**
     * Validate table name to prevent SQL injection.
     *
     * @param  string  $table
     * @return bool
     */
    private function isValidTableName($table)
    {
        // Only allow alphanumeric characters and underscores
        return preg_match('/^[a-zA-Z0-9_]+$/', $table) === 1 && Schema::hasTable($table);
    }
}
