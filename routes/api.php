<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::post("login", [UserController::class, "login"]);
Route::get("showDonationsTypes", [UserController::class, "showDonationsTypes"]);
Route::get("home", [UserController::class, "home"]);
Route::get("showHomeInfo", [UserController::class, "showHomeInfo"]);
Route::get('export', [UserController::class, 'export']);

Route::group(["middleware" => ["auth:api"]], function () {
    Route::post("register", [UserController::class, "register"])->middleware('checkAdminId');
    Route::post("addAd", [UserController::class, "addAd"])->middleware('checkAdminId');
    Route::get("showAds", [UserController::class, "showAds"]);
    Route::get("deleteAd/{id}", [UserController::class, "deleteAd"])->middleware('checkAdminId');
    Route::post("addFamily", [UserController::class, "addFamily"])->middleware('checkAdminId');
    Route::get("deleteFamily/{id}", [UserController::class, "deleteFamily"])->middleware('checkAdminId');
    Route::get("showFamilies", [UserController::class, "showFamilies"])->middleware('checkAdminId');
    Route::post("addCash", [UserController::class, "addCash"])->middleware('checkAdminId');
    Route::get("deleteAcc/{id}", [UserController::class, "deleteAcc"])->middleware('checkAdminId');
    Route::post("addDonationType", [UserController::class, "addDonationType"])->middleware('checkAdminId');
    Route::get("deleteDonationType/{id}", [UserController::class, "deleteDonationType"])->middleware('checkAdminId');
    Route::post("addDonation", [UserController::class, "addDonation"])->middleware('checkAdminId');
    Route::get("showDonations", [UserController::class, "showDonations"])->middleware('checkAdminId');
    Route::get("deleteDonation/{id}", [UserController::class, "deleteDonation"])->middleware('checkAdminId');
    Route::post("addEvent", [UserController::class, "addEvent"])->middleware('checkAdminId');
    Route::get("deleteEvent/{id}", [UserController::class, "deleteEvent"])->middleware('checkAdminId');
    Route::get("showEvents", [UserController::class, "showEvents"]);
    Route::post("editAcc", [UserController::class, "editAcc"]);
    Route::post("editFamily", [UserController::class, "editFamily"])->middleware('checkAdminId');
    Route::post("editDonationType", [UserController::class, "editDonationType"])->middleware('checkAdminId');
    Route::post("editDonation", [UserController::class, "editDonation"])->middleware('checkAdminId');
    Route::post("addExpense", [UserController::class, "addExpense"])->middleware('checkAdminId');
    Route::get("showExpenses", [UserController::class, "showExpenses"])->middleware('checkAdminId');
    Route::get("deleteExpense/{id}", [UserController::class, "deleteExpense"])->middleware('checkAdminId');
    Route::post("editExpense", [UserController::class, "editExpense"])->middleware('checkAdminId');
    Route::post("addZaka", [UserController::class, "addZaka"])->middleware('checkAdminId');
    Route::get("deleteZaka/{id}", [UserController::class, "deleteZaka"])->middleware('checkAdminId');
    Route::get("showZaka", [UserController::class, "showZaka"])->middleware('checkAdminId');
    Route::get("showUsersAndFamilies", [UserController::class, "showUsersAndFamilies"])->middleware('checkAdminId');
    Route::post("getReport", [UserController::class, "getReport"])->middleware('checkAdminId');
    Route::post("getUserReport", [UserController::class, "getUserReport"]);
    Route::post("editHome", [UserController::class, "editHome"])->middleware('checkAdminId');
    Route::post("addExpenseType", [UserController::class, "addExpenseType"])->middleware('checkAdminId');
    Route::get("showExpenseTypes", [UserController::class, "showExpenseTypes"])->middleware('checkAdminId');
    Route::get("deleteExpenseType/{id}", [UserController::class, "deleteExpenseType"])->middleware('checkAdminId');
    Route::post("editExpenseType", [UserController::class, "editExpenseType"])->middleware('checkAdminId');
    Route::get("showSubscriptionData", [UserController::class, "showSubscriptionData"])->middleware('checkAdminId');
    Route::get("showUserSubs", [UserController::class, "showUserSubs"]);
    Route::get("deleteSubscription/{id}", [UserController::class, "deleteSubscription"])->middleware('checkAdminId');
    Route::post("addSubscription", [UserController::class, "addSubscription"])->middleware('checkAdminId');
    Route::post("addPayment", [UserController::class, "addPayment"])->middleware('checkAdminId');
    Route::get("deletePayment/{id}", [UserController::class, "deletePayment"])->middleware('checkAdminId');
    Route::post("editPayment", [UserController::class, "editPayment"])->middleware('checkAdminId');
    Route::post("editSubscription", [UserController::class, "editSubscription"])->middleware('checkAdminId');
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

Route::get('Events/{filename}', function ($filename) {
    $path = base_path('public_html/Events/' . $filename);
    if (!File::exists($path)) {
        abort(404, 'File not found');
    }
    return response()->file($path);;
});

Route::get('Users/{filename}', function ($filename) {
    $path = base_path('public_html/Users/' . $filename);
    if (!File::exists($path)) {
        abort(404, 'File not found');
    }
    return response()->file($path);;
});

Route::get('Home/{filename}', function ($filename) {
    $path = base_path('public_html/Home/' . $filename);
    if (!File::exists($path)) {
        abort(404, 'File not found');
    }
    return response()->file($path);;
});
