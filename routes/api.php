<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

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

                /**
                 * Project -> Tasks
                 */
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
                        Route::controller(\App\Http\Controllers\Api\Project\Task\NoteController::class)
                            ->name('notes.')
                            ->group(function () {

                                Route::get('/notes', 'index')
                                    ->name('index');

                                Route::post('/notes', 'store')
                                    ->name('store');

                                Route::middleware('can:ownNote,projectTask,taskNote')
                                    ->prefix('/note/{taskNote}')
                                    ->group(function () {
                                        Route::patch('/', 'update')
                                            ->name('update');

                                        Route::delete('/', 'destroy')
                                            ->name('destroy');
                                    });

                            });

                        /**
                         * Project Task -> Links
                         */
                        Route::controller(\App\Http\Controllers\Api\Project\Task\LinkController::class)
                            ->name('links.')
                            ->group(function () {
                                Route::get('/links', 'index')
                                    ->name('index');

                                Route::post('/links', 'store')
                                    ->name('store');

                                Route::middleware('can:ownLink,projectTask,link')
                                    ->prefix('/link/{link}')
                                    ->group(function () {

                                        Route::patch('/', 'update')
                                            ->name('update');

                                        Route::delete('/', 'destroy')
                                            ->name('destroy');
                                    });
                            });
                    });

            });

    })->middleware(Authenticate::using('sanctum'));
