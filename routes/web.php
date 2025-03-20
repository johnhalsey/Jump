<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return Inertia::render('Welcome', []);
})->middleware(['guest']);

Route::middleware(['auth', 'verified', \App\Http\Middleware\FullProfile::class])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('project/{project}')
        ->middleware(\App\Http\Middleware\CanViewProject::class)->group(function () {

        Route::get('/', [\App\Http\Controllers\ProjectController::class, 'show'])
            ->name('project.show');

        Route::get('/settings', [\App\Http\Controllers\Project\SettingsController::class, 'index'])
            ->name('project.settings.index');

        Route::get('/task/{projectTask}', [\App\Http\Controllers\Project\TaskController::class, 'show'])
            ->name('project.task.show');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
