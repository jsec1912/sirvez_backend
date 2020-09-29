<?php

namespace App\Http\Controllers;
use App\Notification;
use App\Company_customer;
use DB;
use Illuminate\Http\Request;
class NotificationController extends Controller
{
    public function getNotification(request $request){
        $res = array();
        if($request->has('isread')){
            if($request->user->user_type >1){
                $notification = DB::table('notifications')
                    ->leftJoin('users','users.id','=','notifications.created_by')
                    ->leftJoin('companies','companies.id','=','notifications.company_id')
                    ->where('notifications.company_id','=',$request->user->company_id)
                    ->where('notifications.is_read',$request->isread)
                    ->select('notifications.*','users.first_name','users.profile_pic','companies.name as company_name')
                    ->orderBy('notifications.id','desc')
                    ->get();
            }
            else
            {
                $idx = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $notification = DB::table('notifications')
                    ->leftJoin('users','users.id','=','notifications.created_by')
                    ->leftJoin('companies','companies.id','=','notifications.company_id')
                    ->whereIn('notifications.company_id',$idx)
                    ->where('notifications.is_read',$request->isread)
                    ->select('notifications.*','users.first_name','users.profile_pic','companies.name as company_name')
                    ->orderBy('notifications.id','desc')
                    ->get();
            }
        }
        else
        {
            if($request->user->user_type >1){
                $notification = DB::table('notifications')
                    ->leftJoin('users','users.id','=','notifications.created_by')
                    ->leftJoin('companies','companies.id','=','notifications.company_id')
                    ->where('notifications.company_id','=',$request->user->company_id)
                    ->select('notifications.*','users.first_name','users.profile_pic','companies.name as company_name')
                    ->orderBy('notifications.id','desc')
                    ->get();
            }
            else
            {
                $idx = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $notification = DB::table('notifications')
                    ->leftJoin('users','users.id','=','notifications.created_by')
                    ->leftJoin('companies','companies.id','=','notifications.company_id')
                    ->whereIn('notifications.company_id',$idx)
                    ->select('notifications.*','users.first_name','users.profile_pic','companies.name as company_name')
                    ->orderBy('notifications.id','desc')
                    ->get();
            }
        }
        
        //$notification = Notification::where('company_id',$request->user->company_id)->get()
        $res['status'] = "success";
        $res['notifications'] = $notification;
        return response()->json($res);

    }
    public function deleteNotification(request $request){
        $res = array();
        $res['status'] = "success";
        Notification::whereId($request->id)->delete();
        return response()->json($res);
    }
    public function readNotification(request $request){
        $res = array();
        $res['status'] = "success";
        Notification::whereId($request->id)->update(['is_read'=>1]);
        return response()->json($res);
    }
}
