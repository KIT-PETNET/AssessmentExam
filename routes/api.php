<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CsvUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Check user
Route::middleware(['cors', 'json.response', 'auth:api'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public auth
Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::post('/auth', [ApiAuthController::class, 'getToken']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/reset-users', [UserController::class, 'resetUsers']);
});

// Protected APIs
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    
    //USER ATUHENTICATION
    // superAdmin (ID: 1)
    Route::middleware('checkUserRole:superAdmin')->group(function () {
        Route::resource('/users', UserController::class)->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]);

        Route::resource('/commissions', DataController::class)->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]);
    });

    // Admin (IDs: 2, 3)
    Route::middleware('checkUserRole:admin')->group(function () {
        Route::resource('/users', UserController::class)->only([
            'index', 'store', 'show', 'update'
        ]);

        Route::resource('/commissions', DataController::class)->only([
            'index', 'store', 'show', 'update'
        ]);
    });

    // Member (IDs: 4 and up)
    Route::middleware('checkUserRole:member')->group(function () {
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::get('/commissions/{id}', [DataController::class, 'show']);
    });

    // Report route
    Route::post('/reports/calculate-commissions', [ReportController::class, 'calculateCommissionPerTransaction']);
    Route::post('/reports/save-commissions', [ReportController::class, 'saveCommissionsToDatabase']);
    Route::get('/reports/compute-commissions', [ReportController::class, 'computeGrossAndNetCommission']);
    Route::get('/reports/generate', [ReportController::class, 'generateReport']);

    // Upload csv route
    Route::post('/upload-csv', [CsvUploadController::class, 'uploadCsv']);
});
