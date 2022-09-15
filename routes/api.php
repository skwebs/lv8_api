<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Public routes
Route::post("/register", [AuthController::class, "register"]);
Route::post("/users", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);
Route::get("/users", [AuthController::class, "index"]);
Route::get("/users/{id}", [AuthController::class, "show"]);

Route::get("/products", [ProductController::class, "index"]);
Route::get("/products/{id}", [ProductController::class, "show"]);
Route::get("/products/search/{name}", [ProductController::class, "search"]);

// Route::resource("products", ProductController::class);

// Protected routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Route::get("/users", [AuthController::class, "index"]);
    Route::post("/logout", [AuthController::class, "logout"]);
    // Route::post("/users", [AuthController::class, "store"]);
    Route::put("/users/{id}", [AuthController::class, "update"]);
    Route::delete("/users/{id}", [AuthController::class, "destroy"]);

    Route::post("/products", [ProductController::class, "store"]);
    Route::put("/products/{id}", [ProductController::class, "update"]);
    Route::delete("/products/{id}", [ProductController::class, "destroy"]);
});

// commented codes are for testing purposes ===========================================================


// Route::middleware('auth:sanctum')->get('/user', function () {
//     Route::get("/products/search/{name}", [ProductController::class, "search"]);
// });

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });