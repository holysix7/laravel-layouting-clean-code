<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use \Session;
use App\Models\SysApplication;
use App\Models\SysRole;
use App\Models\SysRolePermission;
use App\Models\SysPermission;
use App\Models\AppLayout;
use App\Models\AppLayoutBody;
use Illuminate\Support\Facades\DB;

class LayoutingController extends Controller
{
    public function index(){
        saveLogActivity('User melihat list layouting');
        return view('layouting-menu.index');
    }

    public function ajax(Request $request){
        saveLogActivity('User access ajax list layouting');
        $appLayouts = AppLayout::where('isactive', true)->get();
        $resCount   = count($appLayouts);
        $no         = $request->start;
        $records    = [];
        foreach($appLayouts as $row){
            $row = [
                'rownum'            => ++$no,
                'id'                => $row->id,
                'name'              => $row->name,
                'app_layout_bodies' => $row->app_layout_bodies ? $row->app_layout_bodies : [],
                'created_by_name'   => $row->sys_user_created ? $row->sys_user_created->name : null,
                'created_at'        => date("Y-m-d H:i:s", strtotime($row->created_at)),
                'updated_by_name'   => $row->sys_user_updated ? $row->sys_user_updated->name : null,
                'updated_at'        => date("Y-m-d H:i:s", strtotime($row->updated_at)),
                'isactive'          => $row->isactive,
                'routeshow'         => route('layouting.show', Crypt::encrypt($row->id))
            ];
            array_push($records, $row);
        }
        $response = [
            "draw"              => $request->draw,
            "recordsTotal"      => $resCount,
            "recordsFiltered"   => $resCount,
            "data"              => $records
        ];
    
        return response()->json($response);
    }

    public function new(){
        saveLogActivity('User membuka halaman tambah layout');
        return view('layouting-menu.index');
    }

    public function create(Request $request){
        saveLogActivity('User menambahkan layout baru');
        DB::beginTransaction();
        try {
            $layout             = new AppLayout();
            $layout->created_by = Session::get('user')->id;
            $layout->created_at = date("Y-m-d H:i:s");
            $layout->name       = $request->name;
            if(!$layout->save()){
                DB::rollback();
            }
            $childs             = $request->bodies;
            foreach($childs as $child){
                $test[] = $child;
                $layoutBody = new AppLayoutBody();
                $layoutBody->app_layout_id  = $layout->id;
                $layoutBody->created_by     = Session::get('user')->id;
                $layoutBody->created_at     = date("Y-m-d H:i:s");
                $layoutBody->left           = $child['left'];
                $layoutBody->top            = $child['top'];
                $layoutBody->stroke         = $child['stroke'];
                $layoutBody->stroke_width   = $child['stroke_width'];
                $layoutBody->width          = $child['width'];
                $layoutBody->height         = $child['height'];
                if(!$layoutBody->save()){
                    DB::rollback();
                }
            }
            $status     = 200;
            $message    = 'Success insert data';
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            $status     = 500;
            $message    = 'There is problem in controller: '. $e;
        }

        $result = [
            "status"    => $status,
            "message"   => $message,
            "bodies"   => $request->all()
        ];
        
        return response()->json($result);
    }

    public function show(){
        saveLogActivity('User membuka halaman rincian layout');
        return view('layouting-menu.index');
    }

    public function show_ajax(Request $request){
        saveLogActivity('User access ajax rincian layout');
        $id = Crypt::decrypt($request->id);
        $layout = AppLayout::where('id', $id)->first();
        $layout->app_layout_bodies = $layout->app_layout_bodies ? $layout->app_layout_bodies : [];
        $response = [
            "status"    => $layout ? 200 : 500,
            "data"      => $layout ? $layout : null
        ];
    
        return response()->json($response);
    }
}

