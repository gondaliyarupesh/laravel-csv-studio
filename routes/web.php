<?php

use Illuminate\Support\Facades\Route;
use AestheticStudio\CsvImporter\Http\Controllers\CsvImportController;

$prefix = config('csv-importer.route_prefix', 'csv-importer');
$middleware = config('csv-importer.middleware', ['web']);

Route::group(['prefix' => $prefix, 'middleware' => $middleware], function () {
    Route::get('/', [CsvImportController::class, 'index'])->name('csv-importer.index');
    Route::post('/upload', [CsvImportController::class, 'upload'])->name('csv-importer.upload');
    Route::post('/import', [CsvImportController::class, 'import'])->name('csv-importer.import');
    Route::get('/export/{table}', [CsvImportController::class, 'export'])->name('csv-importer.export');
    Route::get('/preview/{table}', [CsvImportController::class, 'preview'])->name('csv-importer.preview');
});
