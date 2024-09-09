<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\family;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'type_id' => 'required',
            'email' => 'email|required',
            'password' => 'required',
            'phone_no' => 'required',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'message' => "email is taken"
            ], 200);
        }

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        $user_data = User::where('id', $user->id)->first();

        return response()->json([
            'status' => true,
            'access_token' => $accessToken,
            'user_data' => $user_data
        ]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'password' => 'required',
            'name' => 'required'
        ]);

        if (!Auth::guard('web')->attempt(['password' => $loginData['password'], 'name' => $loginData['name']])) {
            return response()->json(['status' => false, 'message' => 'Invalid User'], 404);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        $user_data = auth()->user();

        return response()->json([
            'status' => true,
            'access_token' => $accessToken,
            'user_data' => $user_data
        ]);
    }

    public function addAd(Request $request)
    {
        $validatedData = $request->validate([
            'img_url' => 'required|image|mimes:jpg,webp,png,jpeg,gif,svg|max:2048',
            'name' => 'required',
        ]);

        $image1 = Str::random(32) . "." . $request->img_url->getClientOriginalExtension();
        Storage::disk('public_htmlAds')->put($image1, file_get_contents($request->img_url));

        $image1 = asset('api/Ads/' . $image1);

        $validatedData['img_url'] = $image1;

        Ad::create($validatedData);
        $var = Ad::get();


        return response([
            'status' => true,
            'message' => "done successfully",
            'data' => $var,
            'image_path' => $image1,
        ], 200);
    }

    public function addFamily(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        if ($request->has('img_url')) {
            $image1 = Str::random(32) . "." . $request->img_url->getClientOriginalExtension();
            Storage::disk('public_htmlFamilies')->put($image1, file_get_contents($request->img_url));

            $image1 = asset('api/Families/' . $image1);

            $validatedData['img_url'] = $image1;
        }
        family::create($validatedData);
        $var = family::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'ads' => $var,
        ], 200);
    }

    public function addCash(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'cash' => 'required',
        ]);

        $user = User::where('id', $request->id)->get();

        User::where('id', $request->id)->update([
            'badget' => $user[0]['badget'] + $request->cash,
        ]);

        $user = User::where('id', $request->id)->get();

        return response([
            'status' => true,
            'message' => 'done successfully',
            'user_data' => $user,
        ], 200);
    }

    public function deleteAcc($id)
    {
        if (!(User::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        User::where('id', $id)->delete();
        $users = User::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'ads' => $users,
        ], 200);
    }

    public function deleteAd($id)
    {
        if (!(ad::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        ad::where('id', $id)->delete();
        $ads = ad::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'ads' => $ads,
        ], 200);
    }

}
