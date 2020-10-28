<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Project_site;
use App\Room;
use App\Site;
use App\Site_room;
use App\Task;
use App\TaskComment;
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
use App\Schedule;
use App\ScheduleProduct;
use Mail;
use Illuminate\Support\Facades\Storage;
class RoomController extends Controller
{
    public function updateRoom(Request $request){
        $res = array();
        $project = array();
        $room = array();
        $id = $request->id;
        if(strlen($request->project_id) > 10)
            $room['project_id'] = Project::where('off_id',$request->project_id)->first()->id;
        else
            $room['project_id']  = $request->project_id;
        if($request->has('project_id'))
            $room['company_id'] = Project::whereId($room['project_id'])->first()->company_id;
        else if($request->has('building_id'))
        {
            $siteId = Building::whereId($request->building_id)->first()->site_id;
            $room['company_id'] = Site::whereId($siteId)->first()->company_id;
        }

        $room['site_id']  = $request->site_id;
        if($request->room_site_id > 0){
            $res_data = array();
            $res_data = Site_room::whereId($request->room_site_id)->first();
            $room['room_number'] = $res_data->room_number;
            $room['department_id']  = $res_data->department_id;
            $room['building_id']  = $res_data->building_id;
            $room['floor_id']  = $res_data->floor_id;
        }
        $room['room_site_id'] = $request->room_site_id;
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
        if(strlen($request->id) > 10)
            if(Room::where('off_id',$request->id)->count() > 0)
                $id = Room::where('off_id',$request->id)->first()->id;
            else $id = '';

        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){
            $room_cnt = Room::where('project_id',$room['project_id'])
                        ->where('room_number',$room['room_number'])->count();
            if($room_cnt > 0)
            {
                $res['status'] = 'error';
                $res['msg'] = 'The room number is already exist!';
                return response()->json($res);
            }
            $room['created_by']  = $request->user->id;
            $room['signed_off']  = $request->signed_off;
            if(strlen($request->id) > 10)
                $room['off_id'] = $request->id;
            $room = Room::create($room);
            $id = $room->id;
            $action = "created";
            if(!$request->room_site_id)
            Site_room::create(['company_id'=>$room['company_id'],'site_id'=>$room['site_id'],'room_number'=>$room['room_number']]);
        }
        else{
            $room_cnt = Room::where('project_id',$room['project_id'])
                            ->where('room_number',$room['room_number'])
                            ->where('id','<>',$id)->count();
            if($room_cnt > 0)
            {
                $res['status'] = 'error';
                $res['msg'] = 'The room number is already exist!';
                return response()->json($res);
            }
            $room['updated_by'] = $request->user->id;
            Room::whereId($id)->update($room);
            $room = Room::whereId($id)->first();
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
                    $fileName = $img_file->getClientOriginalName();
                    $img_file->move(public_path('upload/img/'), $fileName);
                    Room_photo::create(['room_id'=>$id,'user_id'=>$request->user->id,'img_name'=>$fileName]);
                }
            }
        }

        //$notice_type ={1:pending_user,2:createcustomer 3:project 4:site 5:room}
        $insertnotificationdata = array(
            'notice_type'		=> '5',
            'notice_id'			=> $id,
            //'notification'		=> $room['room_number'].' have been '.$action.' by  '.$request->user->first_name.').',
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' have been '.$action.' location['.$room['room_number'].']',
            'created_by'		=> $request->user->id,
            'company_id'		=> $room['company_id'],
            'project_id'        => $room['project_id'],
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationdata);
        $res['status'] = 'success';
        $res['msg'] = 'Room Saved Successfully!';
        $res['room'] = $room;
        $res['rooms'] = Room_photo::where('room_id',$id)->get();
        //$response = ['status'=>'success', 'msg'=>'Room Saved Successfully!'];
        return response()->json($res);
    }
    public function deleteRoom(Request $request)
    {
        //$request = {'id':{}}
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::where(['id'=>$id])->delete();
        Room_photo::where(['room_id'=>$id])->delete();
        Task::where(['room_id'=>$id])->delete();
        $res["status"] = "success";
        return response()->json($res);
    }
    public function roomInfo(Request $request){
        $res = array();
        $res['schedules'] = array();
        if ($request->has('id')||$request->has('room_number')) {
            if((!$request->has('id') || $request->id =='null') && $request->has('room_number')){
                if($request->has('project_name')){
                    $projectId = Project::where('project_name',$request->project_name)->first()->id;
                    $room_id = Room::where('room_number',$request->room_number)->where('project_id',$projectId)->first()->id;
                }
                else
                $room_id = Room::where('room_number',$request->room_number)->first()->id;

            }
            else
                $room_id = $request->id;

            $room = Room::where('rooms.id',$room_id)
            ->leftJoin('departments','departments.id','=','rooms.department_id')
            ->leftJoin('projects','projects.id','=','rooms.project_id')
            ->leftJoin('companies','companies.id','=','rooms.company_id')
            ->leftJoin('sites','sites.id','=','rooms.site_id')
            ->select('rooms.*','projects.project_name','projects.survey_start_date','companies.name as company_name','companies.logo_img as logo_img','sites.site_name as site_name','departments.department_name')
            ->first();

            $room['img_files'] = Room_photo::where('room_id',$room_id)->get();
            $res["room"] = $room;
            $res['assign_to'] = Project_user::where(['project_users.project_id'=>$room->project_id,'project_users.type'=>'1'])
                                ->leftjoin('users','users.id','=','project_users.user_id')
                                ->select('users.*')
                                ->get();
            // $products= Product::where('room_id',$room_id)->orderBy('id','desc')->get();
            // foreach($products as $key => $product)
            // {
            //     $products[$key]['room_name'] = Room::whereId($product->room_id)->first()->room_number;
            // }
            // $res['products'] = $products;
            $tasks = Task::where('room_id',$room_id)->get();
            foreach($tasks as $key=>$row){
                $tasks[$key]['assign_to'] = Project_user::leftJoin('users','users.id','=','project_users.user_id')->where(['project_users.project_id'=>$row->id,'type'=>'2'])->pluck('users.first_name');
            }
            $res['tasks'] = $tasks;
            $schedules = Schedule::where('schedules.room_id',$room_id)
                    ->leftJoin('sites','sites.id','=','schedules.site_id')
                    ->leftJoin('rooms','rooms.id','=','schedules.room_id')
                    ->leftJoin('products','products.id','=','schedules.product_id')
                    ->select('schedules.*','sites.site_name','rooms.room_number','products.product_name')
                    ->get();

            foreach($schedules as $key => $row) {
                $schedules[$key]['product_id'] = ScheduleProduct::where(['schedule_products.schedule_id' => $row->id])->get()->pluck('product_id');
            }
            $res['schedules'] = $schedules;
            $res['notification'] = Notification::where('notice_type',7)
                                    ->where('notice_id',$room_id)
                                    ->orderBy('id','desc')
                                    ->first();
            $res['sign_request'] = Notification::where('notice_type',6)
                                    ->where('notice_id',$room->project_id)
                                    ->orderBy('id','desc')
                                    ->first();
            $res['room_id'] = $room_id;

        }
        if($request->has('project_id')||$request->has('project_name')){
            if((!$request->has('project_id') || $request->project_id =='null') && $request->has('project_name'))
                $project_id = Project::where('project_name',$request->project_name)->first()->id;
            else
                $project_id = $request->project_id;
            $room_ids = Room::where('project_id',$project_id)->pluck('id');
            $products = Product::whereIn('room_id',$room_ids)->orderBy('id','desc')->get();
            foreach($products as $key => $product)
            {
                $products[$key]['room_name'] = Room::whereId($product->room_id)->first()->room_number;
                if($product['action'] ==0)
                    $products[$key]['product_action'] = "New Product";
                else if($product['action'] ==1)
                    $products[$key]['product_action'] = "Dispose";
                else
                    $products[$key]['product_action'] = "Move To Room";
            }
            $res['products'] = $products;
            $company_id = Project::whereId($project_id)->first()->company_id;
            $res['sites'] = Site::where('company_id',$company_id)->orderBy('id','desc')->get();
            $res['projects'] = Project::where('company_id',$company_id)->orderBy('id','desc')->get();
            $site_id = Site::where('company_id',$company_id)->pluck('id');
            $res['departments'] = Department::where('company_id',$company_id)->orderBy('id','desc')->get();
            $res['buildings'] = Building::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
            $res['project_rooms'] = Room::where('project_id',$project_id)->get();
            $res['rooms'] = Site_room::where('company_id',$company_id)/* ->whereNull('project_id') */->get();
            $res['floors'] = Floor::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
            $res['project_id'] = $project_id;
            $res['project_signoff'] = Project::whereId($project_id)->first()->signed_off;
        }
        else if(isset($request->customer_id)&& $request->customer_id>0){
            $res['projects'] = Project::where('company_id',$request->customer_id)->orderBy('id','desc')->get();
            $res['sites'] = Site::where('company_id',$request->customer_id)->orderBy('id','desc')->get();
            $site_id = Site::where('company_id',$request->customer_id)->pluck('id');
            $res['departments'] = Department::where('company_id',$request->customer_id)->orderBy('id','desc')->get();
            $res['buildings'] = Building::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
            $res['floors'] = Floor::whereIn('site_id',$site_id)->orderBy('id','desc')->get();
            $res['rooms'] = Site_room::where('company_id',$request->customer_id)/* ->whereNull('project_id') */->get();
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
                $res['projects'] = Project::where('company_id',$request->user->company_id)->orderBy('id','desc')->get();
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
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        $res = array();
        $res['status'] = "success";
        Room::whereId($id)->update(['signed_off'=>1,'completed_date'=>date("d-m-Y H:i:s")]);

        Product::where('room_id',$id)->update(['signed_off'=>1]);
        $room = Room::whereId($id)->first();
        if(Room::where('project_id',$room->project_id)->where('signed_off','0')->count()==0)
            Project::whereId($room->project_id)->update(['signed_off'=>2]);
        $insertnotificationdata = array(
            'notice_type'		=> '7',
            'notice_id'			=> $id,
            //'notification'		=> "The room(".$room->room_number.") was signed off by ".$request->user->first_name."  on date ".date("d-m-Y H:i:s"),
            'notification'		=> $request->user->first_name.' '.$request->user->last_name. " has signed off location[".$room->room_number."] on [".date("d-m-Y H:i:s").']',
            'created_by'		=> $request->user->id,
            'company_id'		=> $room->company_id,
            'project_id'		=> $room->project_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationdata);
        return response()->json($res);
    }

    public function changeRequest(request $request){
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        $res = array();
        $res['status'] = "success";
        $room = Room::where('rooms.id',$id)
                ->leftJoin('projects','projects.id','=','rooms.project_id')
                ->select('rooms.*','projects.project_name')->first();
        $insertnotificationdata = array(
            'notice_type'		=> '5',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name. " has been sent request to change location[".$room->room_number."]",
            'created_by'		=> $request->user->id,
            'company_id'		=> $room->company_id,
            'project_id'		=> $room->project_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationdata);

        //sending gmail to user
        $pending_user = User::where('id',$room->created_by)->first();
        $to_name = $pending_user['first_name'];
        $to_email = $pending_user['email'];
        $content = $request->user->first_name.' '.$request->user->last_name. ' has been sent request to change location['.$room->room_number.']';
        $invitationURL = "https://app.sirvez.com/app/app/project/live/"+$room['project_name']+'/'+$room['room_number'];
        $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$room['room_number'],"description" =>$room['notes'],"img"=>'',"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view location'];
        Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('sirvez notification.');
            $message->from('support@sirvez.com','sirvez support team');
        });
        
        $team= Project_user::where(['project_id'=>$room->project_id,'type'=>'1'])->get();
        foreach($team as $team_user){
            $pending_user = User::where('id',$team_user->user_id)->first();
            $to_name = $pending_user['first_name'];
            $to_email = $pending_user['email'];
            $content = $request->user->first_name.' '.$request->user->last_name. ' has been sent request to change location['.$room->room_number.']';
            $invitationURL = "https://app.sirvez.com/app/app/project/live/"+$room['project_name']+'/'+$room['room_number'];
            $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$room['room_number'],"description" =>$room['notes'],"img"=>'',"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view location'];
            Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez support team');
            });
        }

        return response()->json($res);
    }

    public function setFavourite(request $request)
    {
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::whereId($id)->update(['favourite'=>$request->favourite]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeNotes(request $request){
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::whereId($id)->update(['notes'=>$request->notes]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
}
