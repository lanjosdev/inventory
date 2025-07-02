<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\ContactCompanyController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ContactStoreController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\SystemLogController;

Route::get('/', function () {
    return "online";
});

// Rotas públicas
Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);

// Rotas protegidas por autenticação Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LogoutController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/assign', [UserController::class, 'assignRolesPermissions']);
    Route::apiResource('companies', CompaniesController::class);
    Route::apiResource('sectors', SectorController::class);
    Route::apiResource('stores', StoreController::class);
    Route::apiResource('status', StatusController::class);
    Route::apiResource('action', ActionController::class);
    Route::get('system-logs', [SystemLogController::class, 'index']);
    Route::get('system-logs/{systemLog}', [SystemLogController::class, 'show']);
});