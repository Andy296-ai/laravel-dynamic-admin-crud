<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Admin Panel Routes
Route::prefix('admin')->middleware(['admin'])->group(function () {
    // List all tables
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    
    // Show table rows
    Route::get('/table/{table}', [AdminController::class, 'showTable'])->name('admin.table');
    
    // Create new row
    Route::get('/table/{table}/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/table/{table}/store', [AdminController::class, 'store'])->name('admin.store');
    
    // Edit row
    Route::get('/table/{table}/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/table/{table}/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    
    // Delete row
    Route::delete('/table/{table}/delete/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
});

require __DIR__.'/auth.php';
