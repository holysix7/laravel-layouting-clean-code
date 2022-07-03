<?php

namespace App\Http\Middleware;

use Browser;
use Closure;

class BrowserDetection
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
        if(!$this->isSupportedBrowser()) {
            $message_heading = "Browser Not Supported";
            $message_content = "Your browser doesn't meet our minimum requirements.";
            return response()->view('layouts.browser-detection',
                [
                    "message_heading" => $message_heading, 
                    "message_content" => $message_content
                ]
            );
        }

        return $next($request);
    }

    function isSupportedBrowser() {
        $currentBrowser = Browser::browserFamily();
        $currentMajorVersion = Browser::browserVersionMajor();
        $minMajorVersion = config('browser-detect.min_major_version.'.$currentBrowser);
        $supported = ($currentMajorVersion < $minMajorVersion) ? false : true;

        return $supported;
    }
}
