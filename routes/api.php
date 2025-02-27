<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Auth\Middleware\Authenticate;

Route::middleware(['auth', 'can:view,project'])->group(function () {

    Route::prefix('/project/{project}')->group(function () {

        Route::get(
            '/tasks', [App\Http\Controllers\Api\ProjectTaskController::class, 'index']
        )->name('api.project.tasks.index');

        Route::post(
            '/tasks', [App\Http\Controllers\Api\ProjectTaskController::class, 'store']
        )->name('api.project.tasks.store');

        Route::prefix('/task/{projectTask}')->group(function () {

            Route::patch(
                '/', [App\Http\Controllers\Api\ProjectTaskController::class, 'update']
            )->name('api.project.task.update');

            Route::post(
                '/notes', [App\Http\Controllers\Api\TaskNoteController::class, 'store']
            )->name('api.task.notes.store');

            Route::patch(
                '/note/{taskNote}', [App\Http\Controllers\Api\TaskNoteController::class, 'update']
            )->name('api.task.note.update');

        });
    });
})->middleware(Authenticate::using('sanctum'));
