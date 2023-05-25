<?php

namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Http\Request;

class CheckCommon {

    public function handle(Request $request, Closure $next){

        if(Session::get('asn')){

            return $next($request);

        }else{

            return redirect("/login");

        }

    }

}
