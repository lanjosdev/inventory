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

//autenticação
Route::post('login', [LoginController::class, 'login']);

// Rotas protegidas por autenticação Sanctum
Route::middleware('auth:sanctum')->group(function () {

    //logs
    Route::get('system-logs', [SystemLogController::class, 'index']);
    Route::get('system-logs/{id_system_log}', [SystemLogController::class, 'show']);

    //autenticação
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('logout', [LogoutController::class, 'logout']);

    //usuarios
    Route::post('users/{id_user}/assign', [UserController::class, 'assignRolesPermissions']);
    Route::post('users/{id_user}/restore', [UserController::class, 'restore']);
    Route::post('users/update-password', [UserController::class, 'updatePassword']);
    Route::post('users/{id_user}/update-password-admin', [UserController::class, 'updatePasswordAdmin']);
    Route::apiResource('users', UserController::class, [
        'parameters' => ['users' => 'id_user']
    ]);

    //redes
    Route::apiResource('companies', CompaniesController::class, [
        'parameters' => ['companies' => 'id_company']
    ]);

    //setores
    Route::apiResource('sectors', SectorController::class, [
        'parameters' => ['sectors' => 'id_sector']
    ]);

    //lojas
    Route::apiResource('stores', StoreController::class, [
        'parameters' => ['stores' => 'id_store']
    ]);

    //status
    Route::apiResource('status', StatusController::class, [
        'parameters' => ['status' => 'id_status']
    ]);

    //ações
    Route::apiResource('action', ActionController::class, [
        'parameters' => ['action' => 'id_action']
    ]);
});