<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use App\User;
use App\Company_customer;

class PageController extends Controller
{
    public function pageList(request $request){
        $res = array();
        $customerIds = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id')->toArray();
        array_push($customerIds, intval($request->user->company_id));
        $res['users'] = User::whereIn('company_id',$customerIds)->get();
        $res['pages'] = Page::whereIn('company_id',$customerIds)->get();
        return response()->json($res);

    }
}
