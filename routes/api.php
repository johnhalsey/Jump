<?php

use App\Http\Controllers\ProjectController;

Route::middleware(['auth', 'verified'])->prefix('api/')->group(function () {

    Route::middleware('can:view,project')->group(function () {

        Route::prefix('/project/{project}')->group(function () {

            Route::get(
                '/tasks', [App\Http\Controllers\Api\ProjectTaskController::class, 'index']
            )->name('api.project.tasks.index');

            Route::patch(
                '/tasks/{projectTask}', [App\Http\Controllers\Api\ProjectTaskController::class, 'update']
            )->name('api.project.task.update');
        });
    });
});
