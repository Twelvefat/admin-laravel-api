<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Dashboard\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:setting index',['only'=>['index']]);
        $this->middleware('permission:setting update',['only'=>['index','update']]);
    }

    public function index()
    {
        $env = DotenvEditor::getKeys();
        $timezones = file_get_contents(storage_path('dashboard/timezones.json'));
        $timezones = json_decode($timezones, true);
        return view('dashboard.setting', compact('env', 'timezones'));
    }
    public function update(Request $request)
    {
        $title = Setting::where('key', 'title')->first();
        $title->value = $request->title;
        $title->save();
        $email = Setting::where('key', 'email')->first();
        $email->value = $request->email;
        $email->save();
        $phone = Setting::where('key', 'phone')->first();
        $phone->value = $request->phone;
        $phone->save();
        $address = Setting::where('key', 'address')->first();
        $address->value = $request->address;
        $address->save();
        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('Update setting');
        return back()->with('status', 'Setting saved');
    }
    public function updateMail(Request $request)
    {
        DotenvEditor::setKey('MAIL_HOST', $request->MAIL_HOST);
        DotenvEditor::setKey('MAIL_USERNAME', $request->MAIL_USERNAME);
        DotenvEditor::setKey('MAIL_PASSWORD', $request->MAIL_PASSWORD);
        DotenvEditor::setKey('MAIL_FROM_ADDRESS', $request->MAIL_FROM_ADDRESS);
        DotenvEditor::setKey('MAIL_FROM_NAME', $request->MAIL_FROM_NAME);
        DotenvEditor::setKey('MAIL_PORT', $request->MAIL_PORT);
        DotenvEditor::setKey('MAIL_ENCRYPTION', $request->MAIL_ENCRYPTION);
        DotenvEditor::setKey('MAIL_DRIVER', $request->MAIL_DRIVER);
        DotenvEditor::save();
        Artisan::call('config:clear');
        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('Update setting');
        return back()->with('status', 'Setting saved');
    }
    public function updateAdvanced(Request $request)
    {
        $request->validate([
            'APP_DEBUG' => 'in:true,false',
            'ACTIVITY_LOGGER_ENABLED'  => 'in:true,false'
        ], [
            'APP_DEBUG.in' => 'Value only true or false',
            'ACTIVITY_LOGGER_ENABLED.in' => 'Value only true or false'
        ]);
        DotenvEditor::setKey('APP_URL', $request->APP_URL);
        DotenvEditor::setKey('TIMEZONE', $request->TIMEZONE);
        DotenvEditor::setKey('APP_DEBUG', $request->APP_DEBUG);
        DotenvEditor::setKey('ACTIVITY_LOGGER_ENABLED', $request->ACTIVITY_LOGGER_ENABLED);
        DotenvEditor::save();
        Artisan::call('config:clear');
        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('Update setting');
        return back()->with('status', 'Setting saved');
    }
}
