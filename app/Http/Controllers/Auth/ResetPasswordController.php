<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\ModelsAppUser;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function index($id){
        $user = AppUser::where('user_id', Crypt::decrypt($id))->first();
        $user->status = 3;  
        return view('layouts.app-confirm-pwd', compact('user'));
    }
    
    public function update_password(Request $request){
        $user = AppUser::where('user_id', $request->user_id)->first();
        $user->password = bcrypt($request->password);  
        $user->status = 4; 
        $user->mac_address = getMacAddr(); 
        $date = strval(date('H, d-m-Y h:i'));
        $link = route('login'); 
        $remote = $_SERVER['REMOTE_ADDR'];
        if($user->save()){
            $content['email'] = [
                "TEMPLATE_KEY"  => "JMET005",
                "DESTNUM"       => $user->email,
                "EMAIL"         => $user->email,
                "JAM"           => date("h:i:s"),
                "LOKASI"        => $remote,
                "USER"          => $user->name,
                "LINK"          => "<a href='".$link."'>Silahkan Klik Disini!</a>"
            ];
            $res = sendEmail($content);
            if($res){
                $message    = 'Berhasil, silahkan login kembali!';
                $alert      = 'success';
            }else{
                $message    = 'Gagal mengirim email untuk melakukan reset password!';
                $alert      = 'success';
            }
            $notification = array(
                'message' => $message,
                'alert-type' => $alert
            );
            return redirect('login')->with($notification);
        }else{
            return back()->with(['message' => 'There is problem with your input!', 'alert-type' => 'danger']);
        }
    }
}
