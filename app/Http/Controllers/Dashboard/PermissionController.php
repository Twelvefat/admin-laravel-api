<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Exception;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permission index',['only'=>['index']]);
        $this->middleware('permission:permission edit',['only'=>['edit', 'update']]);
        $this->middleware('permission:permission create',['only'=>['store']]);
        $this->middleware('permission:permission delete',['only'=>['destroy']]);
    }
    public function data(){
        $permission = Permission::all();
        return response()->json($permission, 200);
    }
    public function index(Request $request)
    {
        $permission = Permission::query();
        if($request->sortField && $request->sortOrder){
            $order = $request->sortOrder;
            $orderField = 'asc';
            if($order == 'ascend'){
                $orderField = 'asc';
            }else{
                $orderField = 'desc';
            }
            $permission->orderBy($request->sortField, $orderField);
        }else{
            $permission->orderBy('name','asc');
        }

        $data = $permission->paginate(10);
        return response()->json($data, 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions'
        ]);
        DB::beginTransaction();
        try{
            $permission = new Permission();
            $permission->name = $request->name;
            $permission->save();
            $user = Auth::user();
            activity()
                ->performedOn($permission)
                ->causedBy($user)
                ->log('Create permission');
            DB::commit();
            return response()->json([
                'message' => 'Permission has been created'
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
        $permission = Permission::findOrFail($id);
        return response()->json($permission, 200);
    }
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);
        DB::beginTransaction();
        try{
            $permission->name = $request->name;
            $permission->save();
            $user = Auth::user();
            activity()
                ->performedOn($permission)
                ->causedBy($user)
                ->log('Update permission');
            DB::commit();
            return response()->json([
                'message' => 'Permissions has been updated'
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
        $permission = Permission::findOrFail($id);
        DB::beginTransaction();
        try{
            $permission->delete();
            $user = Auth::user();
            activity()
                ->performedOn($permission)
                ->causedBy($user)
                ->log('Delete permission');
            DB::commit();
            return response()->json([
                'message' => 'Permissions has been deleted'
            ], 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
