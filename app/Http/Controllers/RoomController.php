<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Project_site;
use App\Room;
use App\Site;
use App\Task;
use App\Product;
use App\Project;
use App\Project_user;
use App\Room_photo;
use App\Notification;
use App\Company_customer;
use App\Department;
use App\Building;
use App\Floor;
use App\Sticker_category;
use Illuminate\Support\Facades\Storage;
class RoomController extends Controller
{
    public function updateRoom(Request $request){
       
        $room = array();
        $id = $request->id;
        if($request->has('project_id'))
            $room['company_id'] = Project::whereId($request->project_id)->first()->company_id;
        else if($request->has('building_id'))
        {
            $siteId = Building::whereId($request->building_id)->first()->site_id;
            $room['company_id'] = Site::whereId($siteId)->first()->company_id;
        }
        
        $room['project_id']  = $request->project_id;
        $room['site_id']  = $request->site_id;
        $room['department_id']  = $request->department_id;
        $room['building_id']  = $request->building_id;
        $room['floor_id']  = $request->floor_id;
        if($request->room_number!==null&&$request->room_number!=="")
            $room['room_number']  = $request->room_number;
        $room['estimate_day']  = $request->estimate_day;
        $room['estimate_time']  = $request->estimate_time;
        $room['notes']  = $request->notes;
        if($request->has('ceiling_height'))
            $room['ceiling_height']  = $request->ceiling_height;
        if($request->has('wall'))
            $room['wall']  = $request->wall;
        if($request->has('asbestos'))
            $room['asbestos']  = $request->asbestos;
        $action = "updated";
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){
            $room['created_by']  = $request->user->id;
            $room['signed_off'] = 0;
            $room = Room::create($room);
            $id = $room->id;
            $action = "created";
        }
        else{
            $room['updated_by'] = $request->user->id;
            Room::whereId($id)->update($room);
        }
        
        //remove room_photh using room_array
        $imgs = Room_photo::where('room_id',$id)->get();
        $res_val = array();
        foreach($imgs as $key => $row){
            if(strpos($request->img_array,$row->img_name)===false) 
            Room_photo::whereId($row->id)->delete();
        }

        $images = $request->file('room_img');
        $n = 0;
        if(isset($images) && count($images) > 0 ){
            foreach($images as $img_file) {
                if (isset($img_file)) {
                    $n++;
                    $fileName = time().'_'.$n.'.'.$img_file->extension();  
                    $img_file->move(public_path('upload/img/'), $fileName);
                    Room_photo::create(['room_id'=>$id,'user_id'=>$request->user->id,'img_name'=>$fileName]);
                }
            }
        }

        //$notice_type ={1:pending_user,2:createcustomer 3:project 4:site 5:room}  
        $insertnotificationndata = array(
            'notice_type'		=> '4',
            'notice_id'			=> $id,
            'notification'		=> $room['room_number'].' have been '.$action.' by  '.$request->user->first_name.').',
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->customer_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        $res = array();
        $res['status'] = 'success';
        $res['msg'] = 'Room Saved Successfully!';
        $res['rooms'] = Room_photo::where('room_id',$id)->get();
        //$response = ['status'=>'success', 'msg'=>'Room Saved Successfully!'];  
        return response()->json($res);
    }
    public function deleteRoom(Request $request)
    {
        //$request = {'id':{}}
        Room::where(['id'=>$request->id])->delete();
        Room_photo::where(['room_id'=>$request->id])->delete();
        Task::where(['room_id'=>$request->id])->delete();
        $res["status"] = "success";
        return response()->json($res);
    }
    public function roomInfo(Request $request){
        $res = array();
        if ($request->has('id')) {
            $room = Room::where('rooms.id',$request->id)
            ->leftJoin('departments','departments.id','=','rooms.department_id')
            ->leftJoin('projects','projects.id','=','rooms.project_id')
            ->leftJoin('companies','companies.id','=','rooms.company_id')
            ->select('rooms.*','projects.project_name','companies.name as company_name','departments.department_name')
            ->first(); 
            
            $room['img_files'] = Room_photo::where('room_id',$request->id)->get();
            $res["room"] = $room;
            
            $products= Product::where('room_id',$request->id)->orderBy('id','desc')->get();
            foreach($products as $key => $product)
            {
                $products[$key]['room_name'] = Room::whereId($product->room_id)->first()->room_number;
                //$products[$key]['to_room_name'] = Room::whereId($product->to_room_id)->first()->room_number;
                //$products[$key]['to_site_name'] = Site::whereId($product->to_site_id)->first()->site_name;
            }
            $res['products'] = $products;
            $tasks = Task::where('room_id',$request->id)->get();
            foreach($tasks as $key=>$row){
                $tasks[$key]['assign_to'] = Project_user::leftJoin('users','users.id','=','project_users.user_id')->where(['project_users.project_id'=>$row->id,'type'=>'2'])->pluck('users.first_name');
            }
            $res['tasks'] = $tasks;
            $res['notification'] = Notification::where('notice_type',7)
                                    ->where('notice_id',$request->id)
                                    ->orderBy('id','desc')
                                    ->first();
            
        }
        
        
        if(isset($request->project_id)&& $request->project_id>0){
            
            $company_id = Project::whereId($request->project_id)->first()->company_id;
            $res['sites'] = Site::where('company_id',$company_id)->orderBy('id','desc')->get();
            $res['projects'] = Project::where('company_id',$company_id)->orderBy('id','desc')->get();
            $site_id = Site::where('company_id',$company_id)->pluck('id');
            $res['departments'] = Department::where('company_id',$company_id)->orderBy('id','desc')->get();
            $res['buildings'] = Building::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
            $res['rooms'] = Room::where('company_id',$company_id)/* ->whereNull('project_id') */->get();
            $res['floors'] = Floor::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
        }
        else if(isset($request->customer_id)&& $request->customer_id>0){
            $res['projects'] = Project::where('company_id',$request->customer_id)->orderBy('id','desc')->get();
            $res['sites'] = Site::where('company_id',$request->customer_id)->orderBy('id','desc')->get();
            $site_id = Site::where('company_id',$request->customer_id)->pluck('id');
            $res['departments'] = Department::where('company_id',$request->customer_id)->orderBy('id','desc')->get();
            $res['buildings'] = Building::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
            $res['floors'] = Floor::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
            $res['rooms'] = Room::where('company_id',$request->customer_id)/* ->whereNull('project_id') */->get();
        }
        else{
            if($request->user->user_type ==1||$request->user->user_type ==3){
                $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $res['sites'] = Site::whereIn('id',$customer_id)->orderBy('id','desc')->get();
                $res['projects'] = Project::whereIn('company_id',$customer_id)->orderBy('id','desc')->get();
                $res['departments'] = Department::whereIn('company_id',$customer_id)->orderBy('id','desc')->get();
            }
            else{
                $res['sites'] = Site::where('id',$request->user->company_id)->orderBy('id','desc')->get();
                $res['projects'] = Project::where('company_id',$request->user->$company_id)->orderBy('id','desc')->get();
                $res['departments'] = Department::where('company_id',$request->user->company_id)->orderBy('id','desc')->get();
            }
            
            $res['buildings'] = Building::where('site_id',$request->site_id)->orderBy('id','desc')->get();
            $res['floors'] = Floor::where('building_id',$request->building_id)->orderBy('id','desc')->get();
        }
        $res['status'] = "success";
        return response()->json($res);
    }
    
    public function editPhoto(request $request)
    {
        $res = array();
        $res['status'] = "success";
        $res['categories'] = Sticker_category::with('stickers')->get();
        return response()->json($res);
    }

    public function saveimage(request $request)
    {
        $res = array();
        $res['status'] = "success";
        $fileName = '';
        $image = $request->image;
        $extension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        $image = $request->image;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $fileName = time().'_change.'.$extension;
        file_put_contents(public_path('upload/img/'). $fileName, base64_decode($image));
        if($request->id > 0)
            Room_photo::whereId($request->id)->update(['img_name'=>$fileName]);
        else
            Room_photo::create(['room_id'=>$request->room_id,'img_name'=>$fileName]);
        return response()->json($res);

    }
    public function signoff(request $request){
        $res = array();
        $res['status'] = "success";
        Room::whereId($request->id)->update(['signed_off'=>1,'completed_date'=>date("d-m-Y H:i:s")]);
        $room = Room::whereId($request->id)->first();
        $insertnotificationdata = array(
            'notice_type'		=> '7',
            'notice_id'			=> $request->id,
            'notification'		=> "The room(".$room->room_number.") was signed off by ".$request->user->first_name."  on date ".date("d-m-Y H:i:s"),
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->user->company_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationdata);
        return response()->json($res);
    }
}
