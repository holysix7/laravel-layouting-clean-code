<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SysApplication;
use App\Models\SysPermission;
use App\Models\SysRole;

class SysRolePermission extends Model
{
    protected $table = 'sys_role_permissions';
    use HasFactory;

    public function applications(){
        return $this->belongsTo(SysApplication::class, 'application_id', 'id');
    }

    public function permission(){
        return $this->belongsTo(SysPermission::class, 'permission_id', 'id');
    }

    public function role(){
        return $thos->belongsTo(SysRole::class, 'role_id', 'id');
    }
}
