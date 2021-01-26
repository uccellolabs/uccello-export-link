<?php

Route::middleware('web')
->namespace('Uccello\ExportLink\Http\Controllers')
->name('export-link.')
->group(function() {

    // This makes it possible to adapt the parameters according to the use or not of the multi domains
    if (!uccello()->useMultiDomains()) {
        $domainAndModuleParams = '{module}';
    } else {
        $domainAndModuleParams = '{domain}/{module}';
    }

    // This route does not use the "auth" middleware. So it is accessible by everyone who know the link
    Route::get($domainAndModuleParams.'/export/{uuid}', 'ExportController@export')
        ->name('export');

    Route::post($domainAndModuleParams.'/export/generate-url', 'LinkController@generateExportLink')
        ->middleware('auth')
        ->name('generate');

    Route::post($domainAndModuleParams.'/export/delete-url/{uuid}', 'LinkController@deleteExportLink')
        ->middleware('auth')
        ->name('delete');
});
