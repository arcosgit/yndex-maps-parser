<?php
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function(){
    Route::post('/parse', [App\Http\Controllers\ParseController::class, 'parse'])->name('parse');
    Route::post('/organization-reviews/{org_id}', [App\Http\Controllers\OrganizationController::class, 'reviews'])->name('org.reviews');
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});

