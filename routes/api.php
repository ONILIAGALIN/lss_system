<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityLogController;

Route::middleware(['throttle:login'])->group(function () {
    Route::post("/login", action: [AuthController::class,"login"]);
    Route::middleware("auth:sanctum")->get("/checkToken", [AuthController::class, "checkToken"]);
    //Route::post('/logout', action:)

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
        Route::get('/export-categories', [\App\Http\Controllers\FeedbackCategoryController::class, 'exportCategory']);
        Route::get("/", [\App\Http\Controllers\FeedbackCategoryController::class, "index"]);
        Route::post("/", [\App\Http\Controllers\FeedbackCategoryController::class, "store"]);
        Route::get("/{category}", [\App\Http\Controllers\FeedbackCategoryController::class, "show"]);
        Route::patch("/{category}", [\App\Http\Controllers\FeedbackCategoryController::class, "update"]);
        Route::delete("/{category}", [\App\Http\Controllers\FeedbackCategoryController::class, "destroy"]);
    });
 });

  Route::prefix("questions")->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/export-questions', [\App\Http\Controllers\FeedbackQuestionController::class, 'exportQuestion']);
        Route::get("/", [\App\Http\Controllers\FeedbackQuestionController::class, "index"]);
        Route::post("/", [\App\Http\Controllers\FeedbackQuestionController::class, "store"]);
        Route::get("/{question}", [\App\Http\Controllers\FeedbackQuestionController::class, "show"]);
        Route::patch("/{question}", [\App\Http\Controllers\FeedbackQuestionController::class, "update"]);
        Route::delete("/{question}", [\App\Http\Controllers\FeedbackQuestionController::class, "destroy"]);
    });
 });

  Route::prefix("choices")->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/export-choices', [\App\Http\Controllers\FeedbackChoiceController::class, 'exportChoice']);
        Route::get("/", [\App\Http\Controllers\FeedbackChoiceController::class, "index"]);
        Route::post("/", [\App\Http\Controllers\FeedbackChoiceController::class, "store"]);
        Route::get("/{choices}", [\App\Http\Controllers\FeedbackChoiceController::class, "show"]);
        Route::patch("/{choices}", [\App\Http\Controllers\FeedbackChoiceController::class, "update"]);
        Route::delete("/{choices}", [\App\Http\Controllers\FeedbackChoiceController::class, "destroy"]);
    });
 });

 Route::prefix("responses")->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/export-responses', [\App\Http\Controllers\FeedbackResponseController::class, 'exportFeedback']);
        Route::get("/", [\App\Http\Controllers\FeedbackResponseController::class, "index"]);
        Route::post("/", [\App\Http\Controllers\FeedbackResponseController::class, "store"]);
        Route::get("/{responses}", [\App\Http\Controllers\FeedbackResponseController::class, "show"]);
        Route::patch("/{responses}", [\App\Http\Controllers\FeedbackResponseController::class, "update"]);
        Route::delete("/{responses}", [\App\Http\Controllers\FeedbackResponseController::class, "destroy"]);
    });
 });

 Route::prefix("logs")->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/export-logs', [ActivityLogController::class, 'exportLog']);
        Route::get("/", [ActivityLogController::class, "index"]);
        Route::post("/", [ActivityLogController::class, "store"]);
        Route::get("/{logs}", [ActivityLogController::class, "show"]);
        Route::patch("/{logs}", [ActivityLogController::class, "update"]);
        Route::delete("/{logs}", [ActivityLogController::class, "destroy"]);
    });
 });