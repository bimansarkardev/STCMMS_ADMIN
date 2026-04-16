<?php

use App\Http\Controllers\ModuleSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiDashboardController;
use App\Http\Controllers\Api\ApiAttendanceController;
use App\Http\Controllers\Api\ApiMasterController;
use App\Http\Controllers\Api\ApiCollectionController;

Route::get('/getMunicipalities', [ApiAuthController::class, 'getMunicipalities']);
Route::post('/getWard', [ApiAuthController::class, 'getWard']);
Route::post('/getRoads', [ApiAuthController::class, 'getRoads']);

Route::post('/registration', [ApiAuthController::class, 'registration']);
Route::post('/verifyRegistrationOtp', [ApiAuthController::class, 'verifyRegistrationOtp']);
Route::post('/resendOtp', [ApiAuthController::class, 'resendOtp']);

Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/verifyLoginOtp', [ApiAuthController::class, 'verifyLoginOtp']);

//forgetpasssword
Route::post('/forgetPasswordGetOTP',[ApiAuthController::class,'forgetPasswordGetOTP']);
Route::post('/forgetPasswordVerifyOTP',[ApiAuthController::class,'forgetPasswordVerifyOTP']);
Route::post('/resetPassword',[ApiAuthController::class,'resetPassword']);

Route::middleware(['verify.auth'])->group(function () {
    //basics
    Route::get('/dashboard', [ApiDashboardController::class, 'dashboard']);
    Route::get('/profile', [ApiDashboardController::class, 'profile']);
    Route::post('/updateProfile', [ApiDashboardController::class, 'updateProfile']);
    Route::post('/changePassword', [ApiDashboardController::class, 'changePassword']);

    //attendance module
    Route::get('/getFieldWorkersForAttendance', [ApiAttendanceController::class, 'getFieldWorkersForAttendance']);
    Route::get('/getVehiclesForAttendance', [ApiAttendanceController::class, 'getVehiclesForAttendance']);
    Route::post('/attendanceIn', [ApiAttendanceController::class, 'attendanceIn']);
    Route::post('/attendanceOut', [ApiAttendanceController::class, 'attendanceOut']);
    Route::get('/logoutFromApplication', [ApiDashboardController::class, 'logoutFromApplication']);

    //masters
    Route::get('/services', [ApiMasterController::class, 'services']);
    Route::get('/accessibilityTypes', [ApiMasterController::class, 'accessibilityTypes']);
    Route::get('/natureOfServices', [ApiMasterController::class, 'natureOfServices']);
    Route::get('/serviceBoundaryTypes', [ApiMasterController::class, 'serviceBoundaryTypes']);
    Route::get('/tankOpenDurations', [ApiMasterController::class, 'tankOpenDurations']);
    Route::get('/typesOfBuildings', [ApiMasterController::class, 'typesOfBuildings']);

    //collection
    Route::post('/collection_submit', [ApiCollectionController::class, 'collection_submit']);
    Route::post('/collection_list', [ApiCollectionController::class, 'collection_list']);
    Route::post('/collection_details', [ApiCollectionController::class, 'collection_details']);

    //disposal
    Route::post('/collection_summary', [ApiCollectionController::class, 'collection_summary']);
    Route::post('/plants_list', [ApiCollectionController::class, 'plants_list']);
    Route::post('/disposal_submit', [ApiCollectionController::class, 'disposal_submit']);
    Route::post('/disposal_list', [ApiCollectionController::class, 'disposal_list']);
    Route::post('/disposal_details', [ApiCollectionController::class, 'disposal_details']);
}); 