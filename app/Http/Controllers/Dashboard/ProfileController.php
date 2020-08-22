<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.profile.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'email' => 'required|unique:users,email,' . $user->id,
            'name' => 'required',
            'password' => 'sometimes|min:6|max:10|nullable'
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password != '') {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        activity()
            ->causedBy($user)
            ->log('Update profile');
        return back()->with('status', 'Profile saved');
    }
}
