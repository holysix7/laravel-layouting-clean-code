<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\ModelsAppUser;
use App\ModelsMstMerchant;
use App\ModelsMstMerchantInfo;
use App\ModelsMstProvince;
use App\ModelsMstCity;
use App\ModelsMstDistrict;
use App\ModelsMstSubDistrict;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // return User::create([
        //     'name' => $data['name'],
        //     'email' => $data['email'],
        //     'password' => Hash::make($data['password']),
        // ]);
    }

    public function checking_register(Request $request)
    {
        $request->validate([
            // 'captcha' => 'required|captcha',
            'merchant_name' => 'required',
            'owner_name'    => 'required',
            'email'         => 'required',
            'phone'         => 'required',
            'address'       => 'required',
            'province_id'   => 'required',
            'city_id'       => 'required',
            'district_id'   => 'required',
            'subdistrict_id' => 'required',
            'terms'         => 'required'
        ]);
        $user = AppUser::where('email', strtolower($request->email))->first();
        if (!$user) {
            DB::beginTransaction();
            try {
                $password = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
                $vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", " ");
                $mst_merchant = new MstMerchant;
                $mst_merchant->merchant_name = $request->merchant_name;
                $mst_merchant->code          = strtoupper(str_replace($vowels, "", $request->merchant_name));
                $mst_merchant->status        = "1";
                if (!$mst_merchant->save()) {
                    return back()->withErrors('Check Mst Merchant');
                }
                $user = new AppUser;
                $user->username         = strtolower($request->username);
                $user->name             = ucwords($request->owner_name);
                $user->email            = $request->email;
                $user->password         = bcrypt($password);
                $user->address          = $request->address;
                $user->phone            = $request->phone;
                $user->role_id          = 3;
                $user->merchant_id      = $mst_merchant->merchant_id;
                $user->status           = "1";
                $user->mac_address      = getMacAddr();
                if (!$user->save()) {
                    return back()->withErrors('Check User');
                }
                $mst_merchant_info = new MstMerchantInfo;
                $mst_merchant_info->merchant_id   = intval($mst_merchant->merchant_id);
                $mst_merchant_info->owner_name    = $mst_merchant->owner_name;
                $mst_merchant_info->province_id   = intval($mst_merchant->province_id);
                $mst_merchant_info->city_id       = intval($mst_merchant->city_id);
                $mst_merchant_info->district_id   = intval($mst_merchant->district_id);
                $mst_merchant_info->subdistrict_id = intval($mst_merchant->subdistrict_id);
                if (!$mst_merchant_info->save()) {
                    return back()->withErrors('Check Mst Merchant Info');
                }
                $link = url('/') . "/change-password/" . Crypt::encrypt($user->user_id);
                $param['email'] = [
                    "TEMPLATE_KEY" => "JMET004",
                    "USER" => $request->username,
                    "EMAIL" => $request->email,
                    "PWD" => $password,
                    "LINK" => '<a href="'.$link.'">Silahkan klik disini!</a>',
                    "DESTNUM" => $request->email,
                ];
                $records = sendEmail($param);
                if ($records['RC'] == '0000') {
                    DB::commit();
                    $message = 'Registrasi berhasil, silahkan cek email';
                    $alert = 'success';
                    $notification = array(
                        'message' => $message,
                        'alert-type' => $alert
                    );
                    return redirect('login')->with($notification);
                } else {
                    DB::rollback();
                    $message = 'Gagal mengirim email!';
                    $alert = 'danger';
                    $notification = array(
                        'message' => $message,
                        'alert-type' => $alert
                    );
                    return back()->with($notification);
                }
            } catch (\Throwable $th) {
                DB::rollback();
                $notification = array(
                    'message' => 'Error!!',
                    'alert-type' => 'danger'
                );
                return back()->with($notification);
            }
        } else {
            $notification = array(
                'message' => 'Email sudah terdaftar!',
                'alert-type' => 'danger'
            );
            return redirect('register')->with($notification);
        }
    }

    public function province()
    {
        $records = MstProvince::all();
        return response()->json([
            'status'    => 200,
            'message'   => 'berhasil',
            'records'   => $records
        ]);
    }

    public function city(Request $request)
    {
        $records = MstCity::where('province_id', $request->province_id)->get();
        return response()->json([
            'status'    => 200,
            'message'   => 'berhasil',
            'records'   => $records
        ]);
    }

    public function district(Request $request)
    {
        $records = MstDistrict::where('city_id', $request->city_id)->get();
        return response()->json([
            'status'    => 200,
            'message'   => 'berhasil',
            'records'   => $records
        ]);
    }

    public function subdistrict(Request $request)
    {
        $records = MstSubDistrict::where('district_id', $request->district_id)->get();
        return response()->json([
            'status'    => 200,
            'message'   => 'berhasil',
            'records'   => $records
        ]);
    }
}
