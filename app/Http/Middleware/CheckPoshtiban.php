<?php

namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Http\Request;

class CheckPoshtiban {

    public function handle(Request $request, Closure $next){

        if(Session::get('asn') and (Session::get("adminType")==2 or Session::get("adminType")==3)){

            return $next($request);

        }else{

            return redirect("/login");

        }

    }

}
