<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Site;
use App\Site_room;
use App\Project_site;
use App\Room;
use App\Building;
use App\Department;
use App\Floor;
use App\Company_customer;
use App\Company;
use App\Notification;
class SiteController extends Controller
{
    public function updateSite(Request $request){
      
        $v = Validator::make($request->all(), [
            //company info
            'customer_id' => 'required',
            'site_name' => 'required',
            'contact_number' => 'required',
            'contact_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'postcode' => 'required',
            'site_instructions' => 'required',
            //'parking_instructions' => 'required',
            'access_hour' => 'required',
            'comment' => 'required',
            //'status' => 'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $site = array();
        $id = $request->id;
        if(strlen($request->customer_id) > 10){
            $site['company_id'] = Company::where('off_id',$request->customer_id)->first()->id;
        }
        else
            $site['company_id'] = $request->customer_id;
        $site['site_name']  = $request->site_name;
        $site['contact_number']  = $request->contact_number;
        $site['contact_name']  = $request->contact_name;
        $site['address']  = $request->address;
        $site['city']  = $request->city;
        $site['postcode']  = $request->postcode;
        $site['site_instructions']  = $request->site_instructions;
        $site['parking_instructions']  = $request->parking_instructions;
        $site['access_hour']  = $request->access_hour;
        $site['comment']  = $request->comment;
        $site['status']  = $request->status;
        $action = "updated";
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"||strlen($request->id) > 10){
            if (strlen($request->id) > 10)
                $site['off_id']  = $request->id;
            $site['created_by']  = $request->user->id;
            $site = Site::create($site);
            $id = $site->id;
        }
        else{
            $site['updated_by'] = $request->user->id;
            Site::whereId($id)->update($site);
        }
        //$notice_type ={1:pending_user,2:createcustomer 3:project 4:site}  
        $insertnotificationndata = array(
            'notice_type'		=> '4',
            'notice_id'			=> $id,
            'notification'		=> $site['site_name'].' have been '.$action.' by  '.$request->user->first_name.').',
            'created_by'		=> $request->user->id,
            'company_id'		=> $site['company_id'],
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );

        $response = ['status'=>'success', 'msg'=>'Site Saved Successfully!'];  
        return response()->json($response);
    }
    public function deleteSite(Request $request)
    {
        //$request = {'id':{}}
        if(strlen($request->id)>10){
            Site::where(['off_id'=>$request->id])->delete();
            $id = Site::where('off_id',$request->id)->first()->id;
        }
        else{
            Site::where(['id'=>$request->id])->delete();
            $id = $request->id;
        }
        $site_id = Project_site::where('site_id',$id)->pluck('id');
        Project_site::whereIn('id',$site_id)->delete();
        Site_room::whereIn('site_id',$site_id)->delete();
        $res["status"] = "success";
        return response()->json($res);
    }
    public function siteList(Request $request){
        $res = array();
        if($request->has('company_id'))
            $sites = Site::where('company_id',$request->company_id)->orderBy('id','desc')->get();
        else{
            if($request->user->user_type==1)
            {
                $company_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $sites = Site::where('company_id',$company_id)->orderBy('id','desc')->get();
            }
            else
                $sites = Site::where('company_id',$request->user->company_id)->orderBy('id','desc')->get();
        }
        foreach($sites as $key =>$site){
            $sites[$key]['buildings_count'] = Building::where('site_id',$site->id)->count();
            $building_id = Building::where('site_id',$site->id)->pluck('id');
            $sites[$key]['floors_count'] = Floor::whereIn('building_id',$building_id)->count();
            $floor_id = Floor::whereIn('building_id',$building_id)->pluck('id');
            $sites[$key]['rooms_count'] = Site_room::whereIn('floor_id',$floor_id)->count();
        }
        $res["sites"] = $sites;
        $res['status'] = "success";
        return response()->json($res);
    }
    public function siteInfo(Request $request){
        $res = array();
        $site = Site::whereId($request->id)->first();   
        $buildings = Building::where('site_id',$site->id)->orderBy('id','desc')->get();
        foreach($buildings as $key =>$building){
            $buildings[$key]['floors_count'] = Floor::where('building_id',$building->id)->count();
            $floor_id = Floor::where('building_id',$building->id)->pluck('id');
            $buildings[$key]['rooms_count'] = Site_room::whereIn('floor_id',$floor_id)->count();
        }
        $res["site"] = $site;
        $res['buildings'] = $buildings;
        $res['status'] = "success";
        return response()->json($res);
    }
    public function getSiteInfo(Request $request){
        $res = array();
        if ($request->has('id')) {
            $res['site'] = Site::where('id',$request->id)->first();   
        }    
        if($request->user->user_type ==1){
            $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['customers'] = Company::whereIn('id',$customer_id)->orderBy('id','desc')->get();
        }
        else{
            $res['customers'] = Company::where('id',$request->user->company_id)->orderBy('id','desc')->get();
        }
        return response()->json($res);
    }
    public function updateRoom(Request $request){
        $res = array();
        $v = Validator::make($request->all(), [
            //company info
            'site_id' => 'required',
            'department_id' => 'required',
            'building_id' => 'required',
            'floor_id' => 'required',
            
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $room = array();
        $id = $request->id;
        $room['room_number'] = $request->room_number;
        if(strlen($request->site_id) > 10)
            $room['site_id'] = Site::where('off_id',$request->site_id)->first()->id;
        else
            $room['site_id'] = $request->site_id;
        
        if(strlen($request->department_id) > 10)
            $room['department_id'] = Department::where('off_id',$request->department_id)->first()->id;
        else
            $room['department_id'] = $request->department_id;
        
        if(strlen($request->building_id) > 10)
            $room['building_id'] = Building::where('off_id',$request->building_id)->first()->id;
        else
            $room['building_id'] = $request->building_id;

        if(strlen($request->floor_id) > 10)
            $room['floor_id'] = Floor::where('off_id',$request->floor_id)->first()->id;
        else
            $room['floor_id'] = $request->floor_id;

        
        $room['company_id'] = Site::whereId($room['site_id'])->first()->company_id;
        
        
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"||strlen($request->id) > 10){
            if (strlen($request->id) > 10)
                $room['off_id']  = $request->id;
            $room['created_by']  = $request->user->id;
            $room = Site_room::create($room);
            $id = $room->id;
        }
        else{
            $room['updated_by'] = $request->user->id;
            Site_room::whereId($id)->update($room);
        }
        $res['status'] = "success";
        return response()->json($res);
    }

    public function deleteRoom(Request $request){
        $res = array();
        if(strlen($request->id)>10){
            Site_room::where(['off_id'=>$request->id])->delete();
        }
        else{
            Site_room::where(['id'=>$request->id])->delete();
        }
        $res['status'] = "success";
        return response()->json($res);
    }
    public function roomInfo(Request $request)
    {
        $res = array();
        if(strlen($request->id)>10)
            $id = Site_room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        
        $res['departments'] = Department::where('company_id',$request->user->company_id)->orderBy('id','desc')->get();
        $res['room'] = Site_room::where('site_rooms.id',$request->id)->first(); 
        $res['status'] = 'success';
        return response()->json($res);
    }
}
