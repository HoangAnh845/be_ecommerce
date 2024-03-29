<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\PropertiesController;

Route::controller(PropertiesController::class)->group(function () {
    Route::post('properties', 'createProperties')->name('properties.create');
    Route::post('properties/edit/{id}', 'updateProperties')->name('properties.update');
    Route::delete('properties/delete/{id}', 'deleteProperties')->name('properties.destroy');
    // Route::get('properties/products', 'searchKeyWordByProductNew')->name('keyword.search');
});
// Route::middleware(['auth:api'])->group(function () { });
?>
