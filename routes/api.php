<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\LoanController;

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
Route::post("/login", [LoginController::class, 'login']);

//Route for admin activities
Route::prefix('admin')->middleware('auth:sanctum','isadmin')->group( function () {
		
		 Route::post("/register", [AdminController::class, 'registerCustomer']);

		 Route::get("/list_requests", [AdminController::class, 'listRequests']);

		 Route::post("/approve", [AdminController::class, 'approveLoan']);

});
//Routes for customer activities
Route::middleware('auth:sanctum')->group( function () {
		
		 Route::post("/apply", [LoanController::class, 'applyLoan']);

		 Route::post("/pay", [LoanController::class, 'payLoan']);

		  Route::get("/view_applications", [LoanController::class, 'viewApplications']);

			 

});


