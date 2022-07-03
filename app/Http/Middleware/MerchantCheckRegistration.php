<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use QS;

class MerchantCheckRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $res = QS::SqlExec("merchant.selsinglemerchant", [
            "merchant_id" => Session::get('user')->merchant_id
        ]);

        $merchant = null;
        if ($res['response']) {
            $merchant = $res['data'][0];
        }

        if (!$merchant) {
            return redirect()->route('dashboard.main')->with([
                'message' => 'Maaf, saat ini anda tidak dapat mengakses form kelengkapan registrasi.',
                'alert-type' => 'danger'
            ]);
        } else {

            $route = is_null($merchant['parent_id']) ? "merchant" : "submerchant";

            // saat status 167 masih bisa melakukan submit konfirmasi
            if (in_array($merchant['status'], [1, 6, 7])) {
                return $next($request);
            } else if ($merchant['status'] == 3) {

                return redirect()->route("crm.$route.informasi")->with([
                    'message' => 'Proses kelengkapan registrasi anda sedang dalam proses verifikasi.',
                    'alert-type' => 'info'
                ]);
            } else {
                return redirect()->route("crm.$route.dashboard")->with([
                    'message' => 'Maaf, saat ini anda tidak dapat mengakses form kelengkapan registrasi.',
                    'alert-type' => 'danger'
                ]);
            }
        }
    }
}
