<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SysUser;
use App\Models\SysPermission;
use App\Models\SysApplication;
use App\Models\SysRolePermission;
use Illuminate\Support\Facades\DB;

class SysRole extends Model
{
    protected $table = 'sys_roles';
    use HasFactory;

    public function user(){
        return $this->hasOne(SysUser::class, 'role_id', 'id');
    }

    public function permissions(){
        return $this->hasMany(SysRolePermission::class, 'role_id', 'id');
    }

    public function batch_permissions($request){
        DB::beginTransaction();
        try {
            $role = new SysRole();
            $role->name         = ucwords($request->name);
            $role->isactive     = true;
            $role->branch_id    = $request->branch_id;
            if(!$role->save()){
                DB::rollback();
                return false;
            }
            $permissions            = SysPermission::all();
            $applicationParents    = SysApplication::where([
                ['type', '=', 0],
                ['parent_id', '=', 0],
            ])->get();
            $applicationChilds     = SysApplication::where([
                ['parent_id', '<>', 0]
            ])->get();
            foreach($applicationParents as $applicationParent){
                $rolePermission = new SysRolePermission();
                $rolePermission->role_id        = $role->id;
                $rolePermission->application_id = $applicationParent->id;
                $rolePermission->permission_id  = 1;
                if(!$rolePermission->save()){
                    DB::rollback();
                    return false;
                }
            }
            foreach($permissions as $permission){
                foreach($applicationChilds as $applicationChild){
                    $rolePermission = new SysRolePermission();
                    $rolePermission->role_id        = $role->id;
                    $rolePermission->application_id = $applicationChild->id;
                    $rolePermission->permission_id  = $permission->id;
                    if(!$rolePermission->save()){
                        DB::rollback();
                        return false;
                    }
                }
            }
            DB::commit();
            return true;
        } catch(\Thrown $e){
            DB::rollback();
            return false;
        }
    }

    public function branch(){
        return $this->hasOne(SysBranch::class, 'id', 'branch_id');
    }
}
