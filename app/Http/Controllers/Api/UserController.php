<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\donation;
use App\Models\donations_type;
use App\Models\family;
use App\Models\event;
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
            'ads' => $var,
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
            'Families' => $var,
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
            'users' => $users,
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

    public function showAds()
    {
        $ads = ad::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'ads' => $ads,
        ], 200);
    }

    public function addDonationType(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        donations_type::create($validatedData);
        $types = donations_type::get();

        return response([
            'status' => true,
            'message' => 'done Successfully',
            'types' => $types
        ]);
    }

    public function showDonationsTypes()
    {
        $types = donations_type::get();

        return response([
            'status' => true,
            'types' => $types
        ]);
    }

    public function deleteDonationType($id)
    {
        if (!(donations_type::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        donations_type::where('id', $id)->delete();
        $data = donations_type::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'ads' => $data,
        ], 200);
    }

    public function addDonation(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'donation_type_id' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        if (!(User::where('id', $request->user_id)->exists())) {
            return response([
                'status' => false,
                'message' => 'user not found, wrong id'
            ], 200);
        }

        if (!(donations_type::where('id', $request->donation_type_id)->exists())) {
            return response([
                'status' => false,
                'message' => 'type not found, wrong id'
            ], 200);
        }

        $user = User::find($request->user_id);
        if ($user->badget < $request->amount) {
            return response([
                'status' => true,
                'message' => "your badget is not enough",
            ], 200);
        }

        donation::create($validatedData);
        $data = donation::join('users as u', 'u.id', 'donations.user_id')
            ->join('donations_types as t', 't.id', 'donations.donation_type_id')
            ->leftjoin('families as f', 'f.id', 'u.family_id')
            ->get([
                'donations.id',
                'u.id as user_id',
                'u.name as user_name',
                'u.email',
                'u.phone_no',
                'u.family_id',
                'f.name as family',
                'donation_type_id',
                't.name as type',
                'amount',
                'date'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'donations' => $data,
        ], 200);
    }

    public function showDonations()
    {
        $data = donation::join('users as u', 'u.id', 'donations.user_id')
            ->join('donations_types as t', 't.id', 'donations.donation_type_id')
            ->leftjoin('families as f', 'f.id', 'u.family_id')
            ->get([
                'donations.id',
                'u.id as user_id',
                'u.name as user_name',
                'u.email',
                'u.phone_no',
                'u.family_id',
                'f.name as family',
                'donation_type_id',
                't.name as type',
                'amount',
                'date'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'donations' => $data,
        ], 200);
    }

    public function deleteDonation($id)
    {
        if (!(donation::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        donation::where('id', $id)->delete();
        $data = donation::join('users as u', 'u.id', 'donations.user_id')
            ->join('donations_types as t', 't.id', 'donations.donation_type_id')
            ->leftjoin('families as f', 'f.id', 'u.family_id')
            ->get([
                'donations.id',
                'u.id as user_id',
                'u.name as user_name',
                'u.email',
                'u.phone_no',
                'u.family_id',
                'f.name as family',
                'donation_type_id',
                't.name as type',
                'amount',
                'date'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'donations' => $data,
        ], 200);
    }

    public function deleteFamily($id)
    {
        if (!(family::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        family::where('id', $id)->delete();
        $data = family::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'families' => $data,
        ], 200);
    }

    public function showFamilies()
    {
        $data = family::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'families' => $data,
        ], 200);
    }

    public function addEvent(Request $request)
    {
        $validatedData = $request->validate([
            'img_url' => 'required|image|mimes:jpg,webp,png,jpeg,gif,svg|max:2048',
            'name' => 'required',
            'date' => 'required',
        ]);

        $image1 = Str::random(32) . "." . $request->img_url->getClientOriginalExtension();
        Storage::disk('public_htmlEvents')->put($image1, file_get_contents($request->img_url));

        $image1 = asset('api/Events/' . $image1);

        $validatedData['img_url'] = $image1;

        event::create($validatedData);
        $var = event::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'events' => $var,
            'image_path' => $image1,
        ], 200);
    }

    public function showEvents()
    {
        $var = event::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'events' => $var
        ], 200);
    }

    public function deleteEvent($id)
    {
        if (!(event::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        event::where('id', $id)->delete();
        $events = event::get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'events' => $events,
        ], 200);
    }
}
