<?php

namespace App\Http\Middleware;

use Browser;
use Closure;

class DeviceDetection
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
        if(!$this->isSupportedDevice()) {
            $message_heading = "Device Not Supported";
            $message_content = "Your device doesn't meet our minimum requirements.";
            return response()->view('layouts.browser-detection',
                [
                    "message_heading" => $message_heading, 
                    "message_content" => $message_content
                ]
            );
        }

        return $next($request);
    }

    /**
     * Based on Bootstrap 4 device compatibility (https://getbootstrap.com/docs/4.0/getting-started/browsers-devices/)
     */
    function deviceDetection() {
        // Init
        $device = "";
        $operatingSystem = "";
        $theDevices = [];        
        
        // Device 
        $isMobile = Browser::isMobile();
        $isDesktop = Browser::isDesktop();

        // Operating System
        $isWindows = Browser::isWindows();
        $isMac = Browser::isMac();
        $isAndroid = Browser::isAndroid();

        // Browser
        if(Browser::isInApp()) {
            $browserInfo = "webview";
        } else if(Browser::isEdge()) {
            $browserInfo = "edge";
        } else if(Browser::isIE()) {
            $browserInfo = "ie";
        } else {
            $browserInfo = Browser::browserFamily();
        }
        
        if($isMobile) {
            $device = "mobile";
        } else if($isDesktop) {
            $device = "desktop";
        }

        if($isWindows) {
            $operatingSystem = "windows";
        } else if ($isMac) {
            $operatingSystem = "mac";
        } else if($isAndroid) {
            $operatingSystem = "android";
        } else {
            $operatingSystem = "others";
        }

        $theDevices = [
            "device" => $device,
            "operating_system" => $operatingSystem,
            "browser" => $browserInfo
        ];

        return $theDevices;
    }

    function isSupportedDevice() {
        $theDevice = $this->deviceDetection();
        $supported = true;

        switch($theDevice['device']) {
            case 'mobile':
                switch($theDevice['operating_system']) {
                    case 'android':
                        if($theDevice['browser'] == "Safari") {
                            $supported = false;
                        }
                        break;
                    case 'mac':
                        if($theDevice['browser'] == "webview") {
                            $supported = false;
                        }
                        break;
                    case 'others':
                        if($theDevice['browser'] == "edge") {
                            $supported = true;
                        }
                        break;
                }
                break;
            case 'desktop':
                switch($theDevice['operating_system']) {
                    case 'windows':
                        if($theDevice['browser'] == "Safari") {
                            $supported = false;
                        }
                        break;
                    case 'mac':
                        if($theDevice['browser'] == "ie" || $theDevice['browser'] == "edge") {
                            $supported = false;
                        }
                        break;
                }
                break;
        }

        return $supported;
    }
}
