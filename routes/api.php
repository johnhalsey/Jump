<?php

use App\Http\Controllers\ProjectController;

Route::middleware(['auth', 'verified'])->prefix('api/')->group(function () {

    Route::middleware('can:view,project')->group(function () {

        Route::get(
            '/project/{project}/tasks', [App\Http\Controllers\Api\ProjectTaskController::class, 'index']
        )->name('api.project.tasks.index');



    });


});
