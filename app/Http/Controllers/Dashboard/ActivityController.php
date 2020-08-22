<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:activity index',['only'=>['index', 'data']]);
    }
    public function index()
    {
        return view('dashboard.activity.index');
    }
    public function data()
    {
        $activity = Activity::query()->with('causer');
        return datatables()->of($activity)
            ->addIndexColumn()
            ->toJson();
    }
}
