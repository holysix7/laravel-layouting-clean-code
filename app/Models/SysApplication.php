<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SysRolePermission;
use \DB;

class SysApplication extends Model
{
    protected $table = 'sys_applications';
    use HasFactory;
    
    public function getAccessMenu($roleId, $type){
        $parents                = SysApplication::where([
            ['type', 0],
            ['parent_id', 0],
            ['isactive', true]
        ])->orderBy('orders', 'asc')->get();
        $childs                 = SysApplication::where([
            ['parent_id', '<>', 0],
            ['isactive', '=', true]
        ])->orderBy('orders', 'asc')->get();
        $grandChilds            = SysApplication::where([
            ['type', '=', 2],
            ['parent_id', '<>', 0],
            ['isactive', '=', true]
        ])->orderBy('orders', 'asc')->get();
        $records    = [];
        foreach($parents as $parent){
            $rowChild = [];
            foreach($childs as $child){
                $rowGrandChild = [];
                foreach($grandChilds as $grandChild){
                    foreach($grandChild->rPermissions as $rPermissionGrandChild){
                        if($rPermissionGrandChild->role_id == $roleId){
                            if($rPermissionGrandChild->permission->description == 'read'){
                                if($type == 1){
                                    if($rPermissionGrandChild->isactive == true){
                                        if($child->id == $grandChild->parent_id){
                                            $rowGchild = [
                                                'id'            => $grandChild->id,
                                                'isactive'      => $grandChild->isactive,
                                                'name'          => $grandChild->name,
                                                'slug'          => $grandChild->slug,
                                                'description'   => $grandChild->description,
                                                'icon'          => $grandChild->icon,
                                                'order'         => $grandChild->order,
                                                'type'          => $grandChild->type,
                                                'parent_id'     => $grandChild->parent_id
                                            ];
                                            array_push($rowGrandChild, $rowGchild);
                                        }
                                    }
                                }else{
                                    if($child->id == $grandChild->parent_id){
                                        $rowGchild = [
                                            'id'            => $grandChild->id,
                                            'isactive'      => $grandChild->isactive,
                                            'name'          => $grandChild->name,
                                            'slug'          => $grandChild->slug,
                                            'description'   => $grandChild->description,
                                            'icon'          => $grandChild->icon,
                                            'order'         => $grandChild->order,
                                            'type'          => $grandChild->type,
                                            'parent_id'     => $grandChild->parent_id
                                        ];
                                        array_push($rowGrandChild, $rowGchild);
                                    }
                                }
                            }
                        }
                    }
                }
                foreach($child->rPermissions as $rPermission){
                    if($rPermission->role_id == $roleId){
                        if($rPermission->permission->description == 'read'){
                            if($type == 1){
                                if($rPermission->isactive == true){
                                    if($parent->id == $child->parent_id){
                                        $row = [
                                            'id'            => $child->id,
                                            'isactive'      => $child->isactive,
                                            'name'          => $child->name,
                                            'slug'          => $child->slug,
                                            'description'   => $child->description,
                                            'icon'          => $child->icon,
                                            'order'         => $child->order,
                                            'type'          => $child->type,
                                            'parent_id'     => $child->parent_id,
                                            'grand_childs'  => $rowGrandChild
                                        ];
                                        array_push($rowChild, $row);
                                    }
                                }
                            }else{
                                if($parent->id == $child->parent_id){
                                    $row = [
                                        'id'            => $child->id,
                                        'isactive'      => $child->isactive,
                                        'name'          => $child->name,
                                        'slug'          => $child->slug,
                                        'description'   => $child->description,
                                        'icon'          => $child->icon,
                                        'order'         => $child->order,
                                        'type'          => $child->type,
                                        'parent_id'     => $child->parent_id,
                                        'grand_childs'  => $rowGrandChild
                                    ];
                                    array_push($rowChild, $row);
                                }
                            }
                        }
                    }
                }
            }
            foreach($parent->rPermissions as $rPermission){
                if($rPermission->role_id == $roleId){
                    if($rPermission->permission->description == 'Read'){
                        if($type == 1){
                            if($rPermission->isactive == true){
                                $rowParent = [
                                    'id'            => $parent->id,
                                    'isactive'      => $parent->isactive,
                                    'name'          => $parent->name,
                                    'slug'          => $parent->slug,
                                    'description'   => $parent->description,
                                    'icon'          => $parent->icon,
                                    'order'         => $parent->order,
                                    'type'          => $parent->type,
                                    'childs'        => $rowChild
                                ];
                                array_push($records, $rowParent);
                            }
                        }else{
                            $rowParent = [
                                'id'            => $parent->id,
                                'isactive'      => $parent->isactive,
                                'name'          => $parent->name,
                                'slug'          => $parent->slug,
                                'description'   => $parent->description,
                                'icon'          => $parent->icon,
                                'order'         => $parent->order,
                                'type'          => $parent->type,
                                'childs'        => $rowChild
                            ];
                            array_push($records, $rowParent);
                        }
                    }
                }
            }
        }
        return $records;
    }

    public function rPermissions(){
        return $this->hasMany(SysRolePermission::class, 'application_id', 'id');
    }
}
