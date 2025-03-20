<?php

use Illuminate\Auth\Middleware\Authenticate;

Route::middleware(['auth', 'verified'])
    ->name('api.')
    ->group(function () {

        Route::post('projects', [\App\Http\Controllers\Api\ProjectController::class, 'store'])
            ->name('projects.store');

        Route::middleware(['can:view,project'])
            ->prefix('/project/{project}')
            ->name('project.')
            ->group(function () {

                Route::get(
                    '/tasks', [\App\Http\Controllers\Api\Project\TaskController::class, 'index']
                )->name('tasks.index');

                Route::post(
                    '/tasks', [\App\Http\Controllers\Api\Project\TaskController::class, 'store']
                )->name('tasks.store');

                Route::middleware(['can:update,project'])->group(function () {

                    Route::patch(
                        '/settings', [\App\Http\Controllers\Api\Project\SettingsController::class, 'update']
                    )->name('settings.update');

                    Route::post(
                        '/invitations', [\App\Http\Controllers\Api\Project\InvitationController::class, 'store']
                    )->name('invitations.store');

                    Route::delete(
                        '/user/{user}', [\App\Http\Controllers\Api\Project\UserController::class, 'destroy']
                    )->name('users.destroy');

                });

                Route::middleware(['can:ownTask,project,projectTask'])
                    ->prefix('/task/{projectTask}')
                    ->name('task.')
                    ->group(function () {

                        Route::patch(
                            '/', [\App\Http\Controllers\Api\Project\TaskController::class, 'update']
                        )->name('update');

                        Route::delete(
                            '/', [\App\Http\Controllers\Api\Project\TaskController::class, 'destroy']
                        )->name('destroy');

                        /**
                         * Project Task -> Notes
                         */
                        Route::post(
                            '/notes', [\App\Http\Controllers\Api\Project\Task\NoteController::class, 'store']
                        )->name('notes.store');

                        Route::patch(
                            '/note/{taskNote}', [\App\Http\Controllers\Api\Project\Task\NoteController::class, 'update']
                        )->name('notes.update');

                        /**
                         * Project Task -> Links
                         */
                        Route::get(
                            '/links', [\App\Http\Controllers\Api\Project\Task\LinkController::class, 'index']
                        )->name('links.index');

                        Route::post(
                            '/links', [\App\Http\Controllers\Api\Project\Task\LinkController::class, 'store']
                        )->name('links.store');

                        Route::patch(
                            '/link/{link}', [\App\Http\Controllers\Api\Project\Task\LinkController::class, 'update']
                        )->name('links.update');

                        Route::delete(
                            '/link/{link}', [\App\Http\Controllers\Api\Project\Task\LinkController::class, 'destroy']
                        )->name('links.destroy');

                    });

            });

    })->middleware(Authenticate::using('sanctum'));
