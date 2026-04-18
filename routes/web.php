<?php

use App\Http\Controllers\Api\ApiCertificateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\FrontendDashboardController;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\CollectionDisposalController;

Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/forgotPassword', [AuthController::class, 'forgotPassword'])->name('admin.forgotPassword');
    Route::post('/forgotPasswordOtp', [AuthController::class, 'forgotPasswordOtp'])->name('admin.forgotPasswordOtp');
    Route::post('/resetPassword', [AuthController::class, 'resetPassword'])->name('admin.resetPassword');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/saveToken', [DashboardController::class, 'saveToken'])->name('admin.saveToken');

    Route::get('/changePassword', [DashboardController::class, 'changePassword'])->name('admin.changePassword');
    Route::post('/changePassword', [DashboardController::class, 'changePasswordProcess'])->name('admin.changePassword.submit');

    Route::get('/users/{param}', [UserController::class, 'users'])->name('admin.users.param');
    Route::get('/addUser/{param}', [UserController::class, 'addUser'])->name('admin.addUser.param');
    Route::post('/addUser', [UserController::class, 'addUserProcess'])->name('admin.addUser.submit');
    Route::get('/editUser/{param}/{param2}', [UserController::class, 'editUser'])->name('admin.editUser.param.param2');
    Route::post('/editUser', [UserController::class, 'editUserProcess'])->name('admin.editUser.submit');
    Route::get('/deleteUser/{param}', [UserController::class, 'deleteUser'])->name('admin.deleteUser.param');

    Route::get('/ward', [MunicipalityController::class, 'ward'])->name('admin.ward');
    Route::post('/addWard', [MunicipalityController::class, 'addWard'])->name('admin.addWard');
    Route::get('/deleteWard/{param}', [MunicipalityController::class, 'deleteWard'])->name('admin.deleteWard.param');

    Route::get('/road', [MunicipalityController::class, 'road'])->name('admin.road');
    Route::get('/addRoad', [MunicipalityController::class, 'addRoad'])->name('admin.addRoad');
    Route::post('/addRoad', [MunicipalityController::class, 'addRoadProcess'])->name('admin.addRoad.submit');
    Route::get('/editRoad/{param}', [MunicipalityController::class, 'editRoad'])->name('admin.editRoad.param');
    Route::post('/editRoad', [MunicipalityController::class, 'editRoadProcess'])->name('admin.editRoad.submit');
    Route::get('/deleteRoad/{param}', [MunicipalityController::class, 'deleteRoad'])->name('admin.deleteRoad.param');

    Route::get('/stpFstps', [MunicipalityController::class, 'stpFstps'])->name('admin.stpFstps');
    Route::get('/addStpFstp', [MunicipalityController::class, 'addStpFstp'])->name('admin.addStpFstp');
    Route::get('/getMunicipalityWards', [MunicipalityController::class, 'getMunicipalityWards'])->name('admin.getMunicipalityWards');
    Route::post('/addStpFstp', [MunicipalityController::class, 'addStpFstpProcess'])->name('admin.addStpFstp.submit');
    Route::get('/taggedStpFstps', [MunicipalityController::class, 'taggedStpFstps'])->name('admin.taggedStpFstps');
    Route::get('/tagStpFstp', [MunicipalityController::class, 'tagStpFstp'])->name('admin.tagStpFstp');
    Route::get('/getMunicipalityPlants', [MunicipalityController::class, 'getMunicipalityPlants'])->name('admin.getMunicipalityPlants');
    Route::post('/tagStpFstp', [MunicipalityController::class, 'tagStpFstpAddProcess'])->name('admin.tagStpFstp.submit');    
    
    
    Route::get('/editStpFstp/{param}', [MunicipalityController::class, 'editStpFstp'])->name('admin.editStpFstp.param');
    Route::post('/editStpFstp', [MunicipalityController::class, 'editStpFstpProcess'])->name('admin.editStpFstp.submit');
    Route::get('/deleteStpFstp/{param}', [MunicipalityController::class, 'deleteStpFstp'])->name('admin.deleteStpFstp.param');

    Route::get('/vehicles', [MunicipalityController::class, 'vehicles'])->name('admin.vehicles');
    Route::get('/addVehicle', [MunicipalityController::class, 'addVehicle'])->name('admin.addVehicle');
    Route::post('/addVehicle', [MunicipalityController::class, 'addVehicleProcess'])->name('admin.addVehicle.submit');

    Route::get('/fieldWorkers', [MunicipalityController::class, 'fieldWorkers'])->name('admin.fieldWorkers');
    Route::get('/addFieldWorkers', [MunicipalityController::class, 'addFieldWorkers'])->name('admin.addFieldWorkers');
    Route::post('/addFieldWorkers', [MunicipalityController::class, 'addFieldWorkersProcess'])->name('admin.addFieldWorkers.submit');
    
    Route::get('/moduleMaster', [ModuleController::class, 'moduleMaster'])->name('admin.moduleMaster');
    Route::get('/addModuleMaster', [ModuleController::class, 'addModuleMaster'])->name('admin.addModuleMaster');
    Route::post('/addModuleMaster', [ModuleController::class, 'addModuleMasterProcess'])->name('admin.addModuleMaster.submit');
    Route::get('/editModuleMaster/{param}', [ModuleController::class, 'editModuleMaster'])->name('admin.editModuleMaster.param');
    Route::post('/editModuleMaster', [ModuleController::class, 'editModuleMasterProcess'])->name('admin.editModuleMaster.submit');
    Route::get('/deleteModuleMaster/{param}', [ModuleController::class, 'deleteModuleMaster'])->name('admin.deleteModuleMaster.param');

    Route::get('/agencies', [MunicipalityController::class, 'agencies'])->name('admin.agencies');
    Route::get('/addAgency', [MunicipalityController::class, 'addAgency'])->name('admin.addAgency');
    Route::post('/addAgency', [MunicipalityController::class, 'addAgencyProcess'])->name('admin.addAgency.submit');
    
    Route::get('/attendanceList', [AttendanceReportController::class, 'attendanceList'])->name('admin.attendanceList');
    Route::post('/getAttendanceSessions', [AttendanceReportController::class, 'getAttendanceSessions'])->name('admin.getAttendanceSessions');

    Route::get('/collectionsList', [CollectionDisposalController::class, 'collectionsList'])->name('admin.collectionsList');
    Route::get('/disposalsList', [CollectionDisposalController::class, 'disposalsList'])->name('admin.disposalsList');

    //scripts
    Route::get('/runWardScripts', [ScriptController::class, 'runWardScripts'])->name('admin.runWardScripts');
});


// frontend routes
Route::get('/{any}', function () {
    return file_get_contents(public_path('index.html'));
})->where('any', '.*');