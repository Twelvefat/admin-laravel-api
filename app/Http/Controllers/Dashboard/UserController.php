<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Exception;
use DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user index',['only'=>['index', 'data']]);
        $this->middleware('permission:user edit',['only'=>['edit', 'update']]);
        $this->middleware('permission:user create',['only'=>['store']]);
        $this->middleware('permission:user delete',['only'=>['destroy']]);
    }
    public function index(Request $request)
    {
        $user = User::query();
        if($request->sortField && $request->sortOrder){
            $order = $request->sortOrder;
            $orderField = 'asc';
            if($order == 'ascend'){
                $orderField = 'asc';
            }else{
                $orderField = 'desc';
            }
            $user->orderBy($request->sortField, $orderField);
        }else{
            $user->orderBy('name','asc');
        }

        $data = $user->paginate(10);
        return response()->json($data, 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|min:6|max:10|confirmed',
            'active' => 'sometimes',
        ]);
        DB::beginTransaction();
        try{
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->active = $request->has('active');
            $user->assignRole($request->role);
            $user->save();

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('Create User');

            DB::commit();
            return response()->json([
                'message' => 'User has been created!'
            ]);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
    public function detail($id)
    {
        $user = User::findOrFail($id);
        $role = $user->getRoleNames();
        return response()->json($user, 200);
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'email' => 'required|unique:users,email,' . $user->id,
            'name' => 'required',
            'password' => 'sometimes|min:6|max:10|nullable',
            'active' => 'sometimes',
        ]);

        DB::beginTransaction();
        try{
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->password != '') {
                $user->password = Hash::make($request->password);
            }
            $user->active = $request->has('active');
            $user->syncRoles($request->role);
            $user->save();

            DB::commit();
            return response()->json([
                'message' => 'Ok'
            ], 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'message' => 'OK'
        ], 200);
    }
}
