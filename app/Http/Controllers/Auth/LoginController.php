<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use \Auth;
use \Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use \Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash as FacadesHash;
use App\Models\SysUser as User;
use App\Models\SysApplication as Application;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }

    public function login(){
        return view('layouts.app-login');
    }

    public function checking_login(Request $request){
        $user = User::where('username', $request->username)->first();
        // dd($user);
        if($user){
            if(FacadesHash::check($request->password, $user->password)){
                // dd(true);
                $user->role     = $user->role ? $user->role : null;
                $applications   = Application::getAccessMenu($user->role_id, 1);
                // dd($applications);
                if($user->role->permissions){
                    $permissions = [];
                    foreach($user->role->permissions as $rPermission){
                        $row = [
                            'id'                => $rPermission->id,
                            'role_id'           => $rPermission->role_id,
                            'application_id'    => $rPermission->application_id,
                            'permission_id'     => $rPermission->permission_id,
                            'isactive'          => $rPermission->isactive,
                        ];
                        array_push($permissions, $row);
                    }
                }
                Session::put([
                    'status' => 200,
                    'description' => $user->name .' berhasil login!',
                    'user' => $user,
                    'menus' => $applications,
                    'permissions' => $permissions
                ]);
                $route      = 'dashboard';
                $message    = 'User berhasil login!';
                $alert      = 'success';
            }else{
                // dd(false);
                $route      = 'login';
                $message    = 'User gagal login, periksa kembali username atau password!';
                $alert      = 'danger';
            }
        }else{
            $route      = 'login';
            $message    = 'Gagal login, User tersebut tidak ditemukan!';
            $alert      = 'danger';
        }
        return redirect()->route($route)->with([
            'message'       => $message,
            'alert-type'    => $alert
        ]);
    }

    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/')->with([
            'message'       => 'Berhasil logout',
            'alert-type'    => 'success'
        ]);
    }
}
