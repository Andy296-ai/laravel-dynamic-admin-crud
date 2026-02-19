<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share table names with all admin views
        View::composer('layouts.admin', function ($view) {
            try {
                $database = config('database.connections.mysql.database');
                $tables = DB::select("SHOW TABLES");
                
                $tableKey = "Tables_in_{$database}";
                $tableNames = array_map(function($table) use ($tableKey) {
                    return $table->$tableKey;
                }, $tables);
                
                // Filter out system tables
                $tableNames = array_filter($tableNames, function($table) {
                    return !in_array($table, ['migrations', 'password_reset_tokens', 'password_resets', 'failed_jobs', 'personal_access_tokens']);
                });
                
                $view->with('tableNames', array_values($tableNames));
            } catch (\Exception $e) {
                $view->with('tableNames', []);
            }
        });
    }
}
