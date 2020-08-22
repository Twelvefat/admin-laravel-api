<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role index',['only'=>['index', 'data']]);
        $this->middleware('permission:role edit',['only'=>['edit', 'update']]);
        $this->middleware('permission:role create',['only'=>['store']]);
        $this->middleware('permission:role create',['only'=>['destroy']]);
    }
    public function index(Request $request)
    {
        $role = Role::query();
        if($request->sortField && $request->sortOrder){
            $order = $request->sortOrder;
            $orderField = 'asc';
            if($order == 'ascend'){
                $orderField = 'asc';
            }else{
                $orderField = 'desc';
            }
            $role->orderBy($request->sortField, $orderField);
        }else{
            $role->orderBy('name','asc');
        }

        $data = $role->paginate(10);
        return response()->json($data, 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions',
            'permission' => 'required'
        ]);
        DB::beginTransaction();
        try{
            $role = new Role();
            $role->name = $request->name;
            $role->save();
            $permissions = $request->permission;
            foreach($permissions as $permission){
                $p = Permission::where('id', $permission)->first();
                $role->givePermissionTo($p);
            }
            $user = Auth::user();
            activity()
                ->performedOn($role)
                ->causedBy($user)
                ->log('Create role');
            DB::commit();
            return response()->json([
                'message' => 'Role has been created'
            ], 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
    public function detail($id)
    {
        $role = Role::findOrFail($id);
        $role_permissions = $role->permissions;
        return response()->json([
            'role' => $role,
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $this->validate($request,[
            'name' => 'required|unique:roles,name,'.$role->id
        ]);
        DB::beginTransaction();
        try{
            $role->name = $request->name;
            $role->syncPermissions([$request->permission]);
            $role->save();
            $user = Auth::user();
            activity()
                ->performedOn($role)
                ->causedBy($user)
                ->log('Update role');
            DB::commit();
            return response()->json([
                'message' => 'Role has been updated'
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
        $role = Role::findOrFail($id);
        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();
        return response()->json([
            'message' => 'OK'
        ], 200);
    }
}
