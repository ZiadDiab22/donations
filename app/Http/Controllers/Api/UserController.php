<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\donation;
use App\Models\donations_type;
use App\Models\family;
use App\Models\event;
use App\Models\expenses;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_type;
use App\Models\zaka;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        if (User::where('phone_no', $request->phone_no)->exists()) {
            return response()->json([
                'status' => false,
                'message' => "phone_no is taken"
            ], 200);
        }

        $validatedData['password'] = bcrypt($request->password);

        if ($request->has('img_url')) {
            $image1 = Str::random(32) . "." . $request->img_url->getClientOriginalExtension();
            Storage::disk('public_htmlUsers')->put($image1, file_get_contents($request->img_url));
            $image1 = asset('api/Users/' . $image1);
            $validatedData['img_url'] = $image1;
        }

        if ($request->has('family_id')) {
            if (!(family::where('id', $request->family_id)->exists())) {
                return response()->json([
                    'status' => false,
                    'message' => "wrong id , family not exist"
                ], 200);
            }
            $validatedData['family_id'] = $request->family_id;
        }

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
        $donations = donation::join('users as u', 'u.id', 'donations.user_id')
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
            'donations' => $donations,
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

    public function editAcc(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'email' => 'email',
        ]);

        if (!(User::where('id', $request->id)->exists())) {
            return response()->json([
                'status' => false,
                'message' => "Wrong ID , user not exist"
            ], 200);
        }

        if ($request->has('type_id')) {
            if (!(users_type::where('id', $request->type_id)->exists())) {
                return response()->json([
                    'status' => false,
                    'message' => "Wrong type ID"
                ], 200);
            }
        }

        if ($request->has('family_id')) {
            if (!(family::where('id', $request->family_id)->exists())) {
                return response()->json([
                    'status' => false,
                    'message' => "Wrong family ID"
                ], 200);
            }
        }

        $user = User::find($request->id);

        $input = $request->all();

        foreach ($input as $key => $value) {
            if (in_array($key, ['name', 'type_id', 'family_id', 'email', 'phone_no'])) {
                $user->$key = $value;
            }
        }

        if ($request->has('img_url')) {
            $image1 = Str::random(32) . "." . $request->img_url->getClientOriginalExtension();
            Storage::disk('public_htmlUsers')->put($image1, file_get_contents($request->img_url));
            $image1 = asset('api/Users/' . $image1);
            $user->img_url = $image1;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $users = User::join('users_types as t', 'type_id', 't.id')
            ->join('families as f', 'family_id', 'f.id')
            ->get([
                'users.id',
                'users.name',
                'type_id',
                't.name as type',
                'family_id',
                'f.name as family',
                'email',
                'phone_no',
                'badget',
                'users.img_url',
                'password as user_password',
                'users.created_at',
                'users.updated_at',
            ]);

        return response([
            'status' => true,
            'users' => $users,
        ], 200);
    }

    public function editFamily(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        if (!(family::where('id', $request->id)->exists())) {
            return response()->json([
                'status' => false,
                'message' => "Wrong ID , family not exist"
            ], 200);
        }

        $family = family::find($request->id);

        if ($request->has('img_url')) {
            $image1 = Str::random(32) . "." . $request->img_url->getClientOriginalExtension();
            Storage::disk('public_htmlFamilies')->put($image1, file_get_contents($request->img_url));
            $image1 = asset('api/Families/' . $image1);
            $family->img_url = $image1;
        }

        if ($request->has('name')) {
            $family->name = $request->name;
        }

        $family->save();

        $families = family::get();

        return response([
            'status' => true,
            'families' => $families,
        ], 200);
    }

    public function editDonationType(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
        ]);

        if (!(donations_type::where('id', $request->id)->exists())) {
            return response()->json([
                'status' => false,
                'message' => "Wrong ID , type not exist"
            ], 200);
        }

        $type = donations_type::find($request->id);
        $type->name = $request->name;
        $type->save();

        $types = donations_type::get();

        return response([
            'status' => true,
            'families' => $types,
        ], 200);
    }

    public function editDonation(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'date' => 'date',
        ]);

        if (!(donation::where('id', $request->id)->exists())) {
            return response()->json([
                'status' => false,
                'message' => "Wrong ID , donation not exist"
            ], 200);
        }

        if ($request->has('user_id')) {
            if (!(User::where('id', $request->user_id)->exists())) {
                return response()->json([
                    'status' => false,
                    'message' => "Wrong user ID"
                ], 200);
            }
        }

        if ($request->has('donation_type_id')) {
            if (!(donations_type::where('id', $request->donation_type_id)->exists())) {
                return response()->json([
                    'status' => false,
                    'message' => "Wrong type ID"
                ], 200);
            }
        }

        $donation = donation::find($request->id);

        $input = $request->all();

        foreach ($input as $key => $value) {
            if (in_array($key, ['donation_type_id', 'user_id', 'amount', 'date'])) {
                $donation->$key = $value;
            }
        }

        $donation->save();

        $donations = donation::join('users as u', 'u.id', 'donations.user_id')
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
            'donations' => $donations,
        ], 200);
    }

    public function addExpense(Request $request)
    {
        $validatedData = $request->validate([
            'donation_type_id' => 'required',
            'amount' => 'required',
            'date' => 'date|required',
        ]);

        if (!(donations_type::where('id', $request->donation_type_id)->exists())) {
            return response([
                'status' => false,
                'message' => 'type not found, wrong id'
            ], 200);
        }

        expenses::create($validatedData);
        $data = expenses::join('donations_types as t', 't.id', 'expenses.donation_type_id')
            ->get([
                'expenses.id',
                'donation_type_id',
                't.name as type',
                'amount',
                'date'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'expenses' => $data,
        ], 200);
    }

    public function showExpenses()
    {
        $data = expenses::join('donations_types as t', 't.id', 'expenses.donation_type_id')
            ->get([
                'expenses.id',
                'donation_type_id',
                't.name as type',
                'amount',
                'date'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'expenses' => $data,
        ], 200);
    }

    public function deleteExpense($id)
    {
        if (!(expenses::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        expenses::where('id', $id)->delete();
        $data = expenses::join('donations_types as t', 't.id', 'expenses.donation_type_id')
            ->get([
                'expenses.id',
                'donation_type_id',
                't.name as type',
                'amount',
                'date'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'expenses' => $data,
        ], 200);
    }

    public function editExpense(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'date' => 'date',
        ]);

        if (!(expenses::where('id', $request->id)->exists())) {
            return response()->json([
                'status' => false,
                'message' => "Wrong ID , not exist"
            ], 200);
        }

        if ($request->has('donation_type_id')) {
            if (!(donations_type::where('id', $request->donation_type_id)->exists())) {
                return response()->json([
                    'status' => false,
                    'message' => "Wrong type ID"
                ], 200);
            }
        }

        $expense = expenses::find($request->id);

        $input = $request->all();

        foreach ($input as $key => $value) {
            if (in_array($key, ['donation_type_id', 'amount', 'date'])) {
                $expense->$key = $value;
            }
        }

        $expense->save();

        $data = expenses::join('donations_types as t', 't.id', 'expenses.donation_type_id')
            ->get([
                'expenses.id',
                'donation_type_id',
                't.name as type',
                'amount',
                'date'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'expenses' => $data,
        ], 200);
    }

    public function home()
    {
        $ads = Ad::get();
        $events = event::get();
        $top = DB::table('donations')
            ->join('users as u', 'u.id', 'donations.user_id')->where('u.type_id', 2)
            ->select('user_id', DB::raw('COUNT(*) as donation_count'))
            ->groupBy('user_id')
            ->orderBy('donation_count', 'desc')
            ->take(3)
            ->get();

        foreach ($top as &$item) {
            $item->user = User::where('id', $item->user_id)->get();
        }

        return response([
            'status' => true,
            'ads' => $ads,
            'events' => $events,
            'top_users' => $top,
        ], 200);
    }

    public function addZaka(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required',
            'date' => 'required',
            'user_id' => 'required',
        ]);

        if (!(User::where('id', $request->user_id)->exists())) {
            return response()->json([
                'status' => false,
                'message' => "Wrong user ID"
            ], 200);
        }

        zaka::create($validatedData);

        $data = zaka::join('users as u', 'u.id', 'zakas.user_id')
            ->leftjoin('families as f', 'f.id', 'u.family_id')
            ->get([
                'zakas.id',
                'amount',
                'date',
                'user_id',
                'u.name',
                'family_id',
                'f.name as family',
                'email',
                'phone_no',
                'u.img_url',
                'created_at',
                'updated_at'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'zakas' => $data,
        ], 200);
    }

    public function deleteZaka($id)
    {
        if (!(zaka::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        zaka::where('id', $id)->delete();
        $data = zaka::join('users as u', 'u.id', 'zakas.user_id')
            ->leftjoin('families as f', 'f.id', 'u.family_id')
            ->get([
                'zakas.id',
                'amount',
                'date',
                'user_id',
                'u.name',
                'family_id',
                'f.name as family',
                'email',
                'phone_no',
                'u.img_url',
                'created_at',
                'updated_at'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'zakas' => $data,
        ], 200);
    }

    public function showZaka()
    {
        $data = zaka::join('users as u', 'u.id', 'zakas.user_id')
            ->leftjoin('families as f', 'f.id', 'u.family_id')
            ->get([
                'zakas.id',
                'amount',
                'date',
                'user_id',
                'u.name',
                'family_id',
                'f.name as family',
                'email',
                'phone_no',
                'u.img_url',
                'created_at',
                'updated_at'
            ]);

        return response([
            'status' => true,
            'message' => "done successfully",
            'zakas' => $data,
        ], 200);
    }

    public function showUsersAndFamilies()
    {
        $users = User::leftjoin('families as f', 'f.id', 'family_id')
            ->join('users_types as t', 't.id', 'type_id')
            ->get([
                'users.id',
                'users.name',
                'family_id',
                'f.name as family',
                'f.img_url as family_img_url',
                'type_id',
                't.name as type',
                'email',
                'phone_no',
                'users.img_url',
                'created_at',
                'updated_at'
            ]);

        $data = family::get();
        return response([
            'status' => true,
            'users' => $users,
            'families' => $data,
        ], 200);
    }

    public function getReport(Request $request)
    {
        $request->validate([
            'date1' => 'date|required',
            'date2' => 'date|required',
        ]);

        $date1 = Carbon::parse($request->input('date1'));
        $date2 = Carbon::parse($request->input('date2'));

        $users_count = DB::table('users')
            ->whereDate('created_at', '>=', $date1)
            ->whereDate('created_at', '<=', $date2)
            ->count();

        $users = DB::table('users as u')
            ->whereDate('created_at', '>=', $date1)
            ->whereDate('created_at', '<=', $date2)
            ->leftjoin('families as f', 'f.id', 'family_id')
            ->join('users_types as t', 't.id', 'type_id')
            ->get([
                'u.id',
                'u.name',
                'family_id',
                'f.name as family',
                'f.img_url as family_img_url',
                'type_id',
                't.name as type',
                'email',
                'phone_no',
                'u.img_url',
                'created_at',
                'updated_at'
            ]);

        $total_zaka = DB::table('zakas')
            ->whereDate('date', '>=', $date1)
            ->whereDate('date', '<=', $date2)
            ->sum('amount');

        $total_expenses = DB::table('expenses')
            ->whereDate('date', '>=', $date1)
            ->whereDate('date', '<=', $date2)
            ->sum('amount');

        $pastDate2 = $date2->subYears(1);
        $pastDate1 = $date1->subYears(1);

        $total_donations = DB::table('donations')
            ->whereDate('date', '>=', $pastDate1)
            ->whereDate('date', '<=', $pastDate2)
            ->sum('amount');

        return response()->json([
            'status' => true,
            'users_count' => $users_count,
            'users' => $users,
            'total_zaka' => $total_zaka,
            'total_expenses' => $total_expenses,
            'total_donations' => $total_donations,
            'zaka_on_total_donations' => ($total_donations * 2.5 / 100)
        ]);
    }
}
