<?php

namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Http\Request;

class CheckAdmin {

    public function handle(Request $request, Closure $next){

        if(Session::get('asn') and (Session::get("adminType")==1 or Session::get("adminType")==5  or Session::get("adminType")==6  or Session::get("adminType")==7)){

            return $next($request);

        }else{

            return redirect("/login");

        }

    }

}
