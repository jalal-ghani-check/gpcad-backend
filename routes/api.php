<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\API\v1\Laws\APILawController;
use \App\Http\Controllers\API\v1\Auth\APIAuthController;
use \App\Http\Controllers\API\v1\Chat\ChannelsController;
use \App\Http\Controllers\API\v1\Users\APIUsersController;
use \App\Http\Controllers\API\v1\Houses\APIHouseController;
use \App\Http\Controllers\API\v1\Reports\APIReportController;
use \App\Http\Controllers\API\v1\Profile\APIProfileController;
use \App\Http\Controllers\API\v1\Warrants\APIWarrantController;
use \App\Http\Controllers\API\v1\PoliceReport\APIPoliceReportController;
use \App\Http\Controllers\API\v1\MedicalReport\APIMedicalReportController;
use \App\Http\Controllers\API\v1\APIFileUploadController;
use \App\Http\Controllers\API\v1\Vehicle\APIVehicleController;
use \App\Http\Controllers\API\v1\Chat\MessagesController;
use \App\Http\Controllers\API\v1\Chat\ChatUsersController;
use \App\Http\Controllers\APILatestProfileSearchController;

Route::group(['prefix' => 'v1/'], function () {
    Route::get('meta-data', [APIUsersController::class, 'fetchMetaData']);
    Route::prefix('auth')->group(function () {
        Route::post('login', [APIAuthController::class,'authenticateUser']);
        Route::post('register', [APIAuthController::class, 'createUser']);
//        Route::post('forgot-password', [APIAuthController::class, 'forgotPassword']);
//        Route::post('reset-password', [APIAuthController::class, 'resetPassword']);

        Route::middleware(['validate.web_api_token'])->group(function () {
            Route::post('logout', [APIAuthController::class, 'logout']);
        });

    });
    Route::get('fetch-house-picture/{house_id}', [APIUsersController::class, 'fetchHousePicture'])->name('house-picture');
    Route::middleware(['validate.web_api_token'])->group(function () {
        Route::post('upload-file', [APIFileUploadController::class,'uploadFile']);
        Route::get('fetch-house-picture-base/{house_id}', [APIUsersController::class, 'fetchHousePictureBase'])->name('house-picture-base64');

        Route::prefix('profile')->group(function () {
            Route::get('fetch-all', [APIProfileController::class, 'fetchAllProfiles']);
            Route::get('fetch-all-list', [APIProfileController::class, 'fetchAllProfilesList']);
            Route::get('fetch/{profile_id}', [APIProfileController::class, 'fetchProfileData']);
            Route::post('save-settings', [APIProfileController::class, 'saveProfileSettings']);
            Route::post('delete', [APIProfileController::class, 'deleteProfile']);
            Route::post('add', [APIProfileController::class, 'addProfile']);
        });
        Route::prefix('warrants')->group(function () {
            Route::get('fetch-all', [APIWarrantController::class, 'fetchWarrants']);
            Route::post('update-status', [APIWarrantController::class, 'updateWarrantStatus']);
            Route::post('delete', [APIWarrantController::class, 'deleteWarrant']);
            Route::post('add', [APIWarrantController::class, 'addWarrant']);
        });
        Route::prefix('users')->group(function () {
            Route::get('fetch-data', ['uses' => '\App\Http\Controllers\API\v1\Users\APIUsersController@fetchUserData' , 'hh' => 'aa']);
            Route::get('fetch-all', [APIUsersController::class, 'fetchAllUsers']);
            Route::post('update', [APIUsersController::class, 'UpdateUserData']);
            Route::post('delete', [APIUsersController::class, 'DeleteUserData']);
            Route::post('add', [APIUsersController::class, 'addUserData']);
        });

      Route::prefix('reports')->group(function () {
        Route::get('fetch-all', [APIReportController::class, 'fetchReports']);
        Route::get('fetch-profile/{profileId}', [APIReportController::class, 'fetchProfileReports']);
      });

      Route::prefix('vehicles')->group(function () {
        Route::post('manage', [APIVehicleController::class, 'manageVehicle']);
        Route::get('fetch-all', [APIVehicleController::class, 'fetchAllVehicles']);
        Route::delete('delete/{delete_id}', [APIVehicleController::class, 'deleteVehicle']);
      });

        Route::prefix('police-report')->group(function () {
          Route::get('fetch-all', [APIPoliceReportController::class, 'fetchPoliceReports']);
          Route::get('fetch/{report_id}', [APIPoliceReportController::class, 'fetchPoliceReportData']);
          Route::post('save-settings', [APIPoliceReportController::class, 'savePoliceReportSettings']);
          Route::post('delete', [APIPoliceReportController::class, 'deletePoliceReport']);
        });

        Route::prefix('medical-report')->group(function () {
          Route::get('fetch/{report_id}', [APIMedicalReportController::class, 'fetchMedicalReportData']);
          Route::post('save-settings', [APIMedicalReportController::class, 'saveMedicalReportSettings']);
          Route::post('delete', [APIMedicalReportController::class, 'deleteMedicalReport']);
        });

        Route::prefix('laws')->group(function () {
          Route::get('fetch-all', [APILawController::class, 'fetchLaws']);
          Route::post('add', [APILawController::class, 'manageLaw']);
          Route::delete('delete/{law_id}', [APILawController::class, 'deleteLaw']);
        });

        Route::prefix('houses')->group(function () {
          Route::get('fetch-all', [APIHouseController::class, 'fetchHouses']);
          Route::post('manage', [APIHouseController::class, 'manageHouse']);
          Route::post('link', [APIHouseController::class, 'manageHouseOwner']);
          Route::post('unlink', [APIHouseController::class, 'manageHouseOwner']);
          Route::delete('delete/{house_id}', [APIHouseController::class, 'deleteHouse']);

        });

        Route::prefix('latest-searches')->group(function () {
          Route::get('/fetch', [APILatestProfileSearchController::class, 'fetchLatestProfileSearches']);
          Route::get('/manage/{profile_id}', [APILatestProfileSearchController::class, 'manageLatestProfileSearch']);
        });



      // --------------------- Chat APIs ----------------------
      Route::prefix('chat')->group(function () {
        Route::prefix('channels')->group(function () {
          Route::post('/fetch-one', [ChannelsController::class, 'add']);
        });
        Route::prefix('users')->group(function () {
          Route::get('/fetch-all', [ChatUsersController::class, 'fetchAllUsers']);
        });
        Route::prefix('messages')->group(function () {
          Route::post('/add-new', [MessagesController::class, 'addNewMessage']);
          Route::get('/fetch/{channel_id}', [MessagesController::class, 'fetchMessages']);
        });
      });

    });


  Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
  });
    });

