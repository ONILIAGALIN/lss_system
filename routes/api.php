<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware(['throttle:login'])->group(function () {
    Route::post("/login", action: [AuthController::class,"login"]);
    Route::middleware("auth:sanctum")->get("/checkToken", [AuthController::class, "checkToken"]);

});

//Route::middleware(['auth:sanctum'])->resource('users', \App\Http\Controllers\FeedbackCategoryController::class);
//Route::resource('users', \App\Http\Controllers\UserController::class);

Route::prefix("users")->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/export-users', [\App\Http\Controllers\UserController::class, 'exportUsersWithProfiles']);
        Route::get("/", [\App\Http\Controllers\UserController::class, "index"]);
        Route::post("/", [\App\Http\Controllers\UserController::class, "store"]);
        Route::get("/{user}", [\App\Http\Controllers\UserController::class, "show"]);
        Route::patch("/{user}", [\App\Http\Controllers\UserController::class, "update"]);
        Route::delete("/{user}", [\App\Http\Controllers\UserController::class, "destroy"]);
    });
 });

 Route::prefix("categories")->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/export-category', [\App\Http\Controllers\FeedbackCategoryController::class, 'exportCategory']);
        Route::get("/", [\App\Http\Controllers\FeedbackCategoryController::class, "index"]);
        Route::post("/", [\App\Http\Controllers\FeedbackCategoryController::class, "store"]);
        Route::get("/{category}", [\App\Http\Controllers\FeedbackCategoryController::class, "show"]);
        Route::patch("/{category}", [\App\Http\Controllers\FeedbackCategoryController::class, "update"]);
        Route::delete("/{category}", [\App\Http\Controllers\FeedbackCategoryController::class, "destroy"]);
    });
 });