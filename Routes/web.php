<?php

Route::prefix('epanel/tools')->as('epanel.')->middleware(['auth', 'check.permission:Backup'])->group(function() 
{
    Route::resources([
        'backup' => 'BackupController'
    ]);
});