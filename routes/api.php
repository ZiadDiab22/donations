<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::post("login", [UserController::class, "login"]);
Route::get("showDonationsTypes", [UserController::class, "showDonationsTypes"]);

Route::group(["middleware" => ["auth:api"]], function () {
    Route::post("register", [UserController::class, "register"])->middleware('checkAdminId');
    Route::post("addAd", [UserController::class, "addAd"])->middleware('checkAdminId');
    Route::post("addFamily", [UserController::class, "addFamily"])->middleware('checkAdminId');
    Route::post("addCash", [UserController::class, "addCash"])->middleware('checkAdminId');
    Route::get("deleteAcc/{id}", [UserController::class, "deleteAcc"])->middleware('checkAdminId');
    Route::get("deleteAd/{id}", [UserController::class, "deleteAd"])->middleware('checkAdminId');
    Route::post("addDonationType", [UserController::class, "addDonationType"])->middleware('checkAdminId');
    Route::get("deleteDonationType/{id}", [UserController::class, "deleteDonationType"])->middleware('checkAdminId');
    Route::post("addDonation", [UserController::class, "addDonation"])->middleware('checkAdminId');
    Route::get("showDonations", [UserController::class, "showDonations"])->middleware('checkAdminId');
    Route::get("deleteDonation/{id}", [UserController::class, "deleteDonation"])->middleware('checkAdminId');
});

Route::get('Ads/{filename}', function ($filename) {
    $path = base_path('public_html/Ads/' . $filename);
    if (!File::exists($path)) {
        abort(404, 'File not found');
    }
    return response()->file($path);;
});

Route::get('Families/{filename}', function ($filename) {
    $path = base_path('public_html/Families/' . $filename);
    if (!File::exists($path)) {
        abort(404, 'File not found');
    }
    return response()->file($path);;
});
