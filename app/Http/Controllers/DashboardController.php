<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Session;
use App\Models\SysApplication;
use App\Models\SysRole;
use App\Models\SysRolePermission;
use App\Models\SysPermission;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        // dd(Session::all());
        return view('layouts.app-dashboard');
    }

    // fungsi untuk menambahkan module di role to permission
    public function insert_batch($application_id, $type){
        $application    = SysApplication::where('id', $application_id)->first();
        $applications   = SysApplication::where(function($query){
            $query->where('type', '=', 0)
            ->where('parent_id', '=', 0);
        })->get();
        if (!empty($application)) {
            $roles          = SysRole::all();
            $permissions    = SysPermission::all();
            DB::beginTransaction();
            try {
                foreach($roles as $role){
                    if(strtolower($type) == 'child'){
                        foreach($permissions as $permission){
                            $roleToPermissionShow = SysRolePermission::where([
                                ['role_id', '=', $role->id],
                                ['application_id', '=', $application_id],
                                ['permission_id', '=', $permission->id],
                            ])->first();
                            if(empty($roleToPermissionShow)){
                                $roleToPermission = new SysRolePermission();
                                $roleToPermission->isactive = $role->id == 1 ? true : false;
                                $roleToPermission->role_id = $role->id;
                                $roleToPermission->permission_id = $permission->id;
                                $roleToPermission->application_id = $application_id;
                                if($roleToPermission->save()){
                                    DB::commit();
                                }
                            }else{
                                DB::rollback();
                                echo json_encode('failed because (application_id = ' . $application_id . ') have been recorded in our system');
                                die;
                            }
                        }
                    }else{
                        $roleToPermissionShow = SysRolePermission::where([
                            ['role_id', '=', $role->id],
                            ['application_id', '=', $application_id]
                        ])->first();
                        if (empty($roleToPermissionShow)) {
                            $roleToPermission = new SysRolePermission();
                            $roleToPermission->isactive = false;
                            $roleToPermission->role_id = $role->id;
                            $roleToPermission->permission_id = 1;
                            $roleToPermission->application_id = $application_id;
                            if ($roleToPermission->save()) {
                                DB::commit();
                            }
                        } else {
                            DB::rollback();
                            echo json_encode('failed because (application_id = ' . $application_id . ') have been recorded in our system');
                            die;
                        }
                    }
                }
                echo json_encode('success tambah permission '. $application->name);
                die;
            } catch (\Throwable $e) {
                DB::rollback();
                echo json_encode($e);
                die;
            }
        } else {
            echo json_encode("failed because (modules_id = $application_id in sys_applications) haven't recorded in our system");
            die;
        }
    }

    // fungsi apabila ada permission baru
    public function insert_batch_permission($permission_id){
        $permission = SysPermission::where('id', $permission_id)->first();
        if(!empty($permission)){
            $roles          = SysRole::all();
            $applications   = SysApplication::where(function($query){
                $query->where('type', '<>', 0)
                ->where('parent_id', '<>', 0);
            })->get();
            DB::beginTransaction();
            try {
                foreach($roles as $role){
                    foreach($applications as $application){
                        $roleToPermissionShow = SysRolePermission::where([
                            ['role_id', '=', $role->id],
                            ['application_id', '=', $application->id],
                            ['permission_id', '=', $permission_id]
                        ])->first();
                        if(empty($roleToPermissionShow)){
                            $roleToPermission = new SysRolePermission();
                            $roleToPermission->isactive = false;
                            $roleToPermission->role_id = $role->id;
                            $roleToPermission->permission_id = $permission_id;
                            $roleToPermission->application_id = $application->id;
                            if ($roleToPermission->save()) {
                                DB::commit();
                            }
                        }else{
                            DB::rollback();
                            echo "failed because (permission_id = $permission_id) have been recorded in our system";
                        }
                    }
                }
                echo 'berhasil tambah role permission ' . $permission->name;
            } catch (\Throwable $e){
                DB::rollback();
                echo $e;
            }
        }else{
            echo "failed because (permission_id = $permission_id in sys_permissions) haven't recorded in our system";
        }
    }
}
