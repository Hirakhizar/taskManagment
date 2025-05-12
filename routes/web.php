<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\CommentController;
use App\Http\Controllers\V1\TaskController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])

    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin')->prefix('users')->name('users.')
        ->controller(UserController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'delete')->name('destroy');
        });

    Route::middleware('role:admin,manager')->prefix('categories')->name('categories.')
        ->controller(CategoryController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'delete')->name('destroy');
        });

    Route::middleware('role:admin,manager,user')->prefix('tasks')->name('tasks.')
        ->controller(TaskController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('tasks/{id}', 'delete')->name('destroy');
        });

               Route::middleware('role:admin,manager,user')->prefix('comments')->name('comments.')
          ->controller(CommentController::class)
          ->group(function() {
             Route::get('index', 'index')->name('index');
             Route::post('store', 'store')->name('store');
             Route::get('/latest',  'latest')->name('latest');
});
        

});



require __DIR__ . '/auth.php';
