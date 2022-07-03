<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppLayoutBody;
use App\Models\SysUser;

class AppLayout extends Model
{
    protected $table = 'app_layouts';
    use HasFactory;

    public function app_layout_bodies(){
        return $this->hasMany(AppLayoutBody::class, 'app_layout_id', 'id');
    }

    public function sys_user_created(){
        return $this->hasOne(SysUser::class, 'id', 'created_by');
    }

    public function sys_user_updated(){
        return $this->hasOne(SysUser::class, 'id', 'updated_by');
    }
}
