<?php

use Illuminate\Auth\Middleware\Authenticate;

Route::middleware(['auth', 'verified'])->name('api.')->group(function () {

    Route::post('projects', [\App\Http\Controllers\Api\ProjectController::class, 'store'])
        ->name('projects.store');

    Route::middleware(['can:view,project'])->group(function () {

        Route::prefix('/project/{project}')->group(function () {

            Route::get(
                '/tasks', [\App\Http\Controllers\Api\Project\TaskController::class, 'index']
            )->name('project.tasks.index');

            Route::post(
                '/tasks', [\App\Http\Controllers\Api\Project\TaskController::class, 'store']
            )->name('project.tasks.store');

            Route::middleware(['can:update,project'])->group(function () {

                Route::patch(
                    '/settings', [\App\Http\Controllers\Api\Project\SettingsController::class, 'update']
                )->name('project.settings.update');

                Route::post(
                    '/invitations', [\App\Http\Controllers\Api\Project\InvitationController::class, 'store']
                )->name('project.invitations.store');

                Route::delete(
                    '/user/{user}', [\App\Http\Controllers\Api\Project\UserController::class, 'destroy']
                )->name('project.users.destroy');

            });

            Route::middleware(['can:ownTask,project,projectTask'])->prefix('/task/{projectTask}')->group(function () {

                Route::patch(
                    '/', [\App\Http\Controllers\Api\Project\TaskController::class, 'update']
                )->name('project.task.update');

                Route::delete(
                    '/', [\App\Http\Controllers\Api\Project\TaskController::class, 'destroy']
                )->name('project.task.destroy');

                /**
                 * Project Task -> Notes
                 */
                Route::post(
                    '/notes', [\App\Http\Controllers\Api\Project\Task\NoteController::class, 'store']
                )->name('task.notes.store');

                Route::patch(
                    '/note/{taskNote}', [\App\Http\Controllers\Api\Project\Task\NoteController::class, 'update']
                )->name('task.note.update');

                /**
                 * Project Task -> Links
                 */
//                Route::get(
//                    '/links',
//                    []
//                )->name('task.links.index');

            });
        });
    });

})->middleware(Authenticate::using('sanctum'));
