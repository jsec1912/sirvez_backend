<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Project_site;
use App\Room;
use App\Site;
use App\Site_room;
use App\Task;
use App\User;
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
use App\ScheduleEngineer;
use App\Form_value;
use App\New_form;
use App\Form_field;
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
            if($request->duplicate > 0){
                $room = Room::whereId($request->duplicate)->first()->toArray();
                $room['room_number'] =$request->room_number;
                unset($room["updated_at"]);
                unset($room["created_at"]);
                unset($room["id"]);
            }
            $room['created_by']  = $request->user->id;
            $room['signed_off']  = $request->signed_off;
            if(strlen($request->id) > 10)
                $room['off_id'] = $request->id;
            $room = Room::create($room);
            $id = $room->id;
            if($request->duplicate > 0){
                $values = array();
                $values = Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$request->duplicate)->get();
                foreach($values as $value){
                    $value['parent_id'] = $id;
                    Form_value::create($value);
                }
            }
            $room = Room::where('rooms.id',$id)
                        ->leftJoin('projects','projects.id','=','rooms.project_id')
                        ->leftJoin('users','users.id','=','projects.user_id')
                        ->select('rooms.*','projects.project_name','users.first_name','users.last_name')
                        ->first();
            $action = "created";
            if(!$request->room_site_id)
            Site_room::create(['company_id'=>$room['company_id'],'site_id'=>$room['site_id'],'room_number'=>$room['room_number']]);

            $insertnotificationdata = array(
                'notice_type'		=> '5',
                'notice_id'			=> $id,
                'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new location ['.$room['room_number'].']'.' in ['.$room['project_name'].' ] for ['.$room['first_name'].' '.$room['last_name'].'].',
                'created_by'		=> $request->user->id,
                'company_id'		=> $room['company_id'],
                'project_id'        => $room['project_id'],
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
            );

            Notification::create($insertnotificationdata);
        }
        else{
            // $room_cnt = Room::where('project_id',$room['project_id'])
            //                 ->where('room_number',$room['room_number'])
            //                 ->where('id','<>',$id)->count();
            // if($room_cnt > 0)
            // {
            //     $res['status'] = 'error';
            //     $res['msg'] = 'The room number is already exist!';
            //     return response()->json($res);
            // }
            $room['updated_by'] = $request->user->id;
            Room::whereId($id)->update($room);
            $room = Room::whereId($id)->first();
        }
        if($request->field_values){
            $values = array();
            $values = json_decode($request->field_values);
            $value = array();
            foreach($values as $row){
                $value['field_name'] = $row->field_name;
                $value['field_type'] = $row->field_type;
                $value['field_label'] = $row->field_label;
                $value['new_form_id'] = $row->new_form_id;
                $value['field_value'] = $row->field_value;
                $value['is_checked'] = $row->is_checked;
                $value['form_type'] = $row->form_type;
                $value['parent_id'] = $room->id;
                $cnt = Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$room->id)->count();
                if($cnt>0)
                    Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$room->id)
                                ->update(['field_value'=>$row->field_value,'is_checked'=>$row->is_checked]);
                else
                    Form_value::create($value);
            }
        }

        //remove room_photh using room_array
        if($request->duplicate>0)
        {
            $imgs = Room_photo::where('room_id',$request->duplicate)->get();
            foreach($imgs as $key => $row){
                Room_photo::create(['room_id'=>$id,'user_id'=>$request->user->id,'img_name'=>$row->img_name]);
            }
        }
        else
        {
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
        }

        //$notice_type ={1:pending_user,2:createcustomer 3:project 4:site 5:room}

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

            $room_id = $request->id;
            $projectId = $request->project_id;

            $room = Room::where('rooms.id',$room_id)
            ->leftJoin('departments','departments.id','=','rooms.department_id')
            ->leftJoin('projects','projects.id','=','rooms.project_id')
            ->leftJoin('companies','companies.id','=','rooms.company_id')
            ->leftJoin('sites','sites.id','=','rooms.site_id')
            ->select('rooms.*','projects.project_name','projects.location_form_id','projects.survey_start_date','companies.name as company_name','companies.logo_img as logo_img','sites.site_name as site_name','departments.department_name')
            ->first();

            $room['img_files'] = Room_photo::where('room_id',$room_id)->get();
            $room['form_value'] = Form_value::where('parent_id',$room_id)->where('form_type',0)->get();
            $room['form_id'] = Project::whereId($projectId)->first()->location_form_id;
            if($room['form_id']>0)
                $room['form_style'] = json_decode(New_form::whereId($room['form_id'])->first()->form_data);
            else
                $room['form_style'] = [];
            $res['form_fields'] = Form_field::get();
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
                    ->orderBy('schedules.root_id')
                    ->select('schedules.*','sites.site_name','rooms.room_number','products.product_name')
                    ->get();

            foreach($schedules as $key => $row) {
                $schedules[$key]['product_id'] = ScheduleProduct::where([
                    'schedule_products.schedule_id' => $row->id
                ])->get()->pluck('product_id');
                $product_name= Product::whereIn('id',$schedules[$key]['product_id'])->pluck('product_name');
                $products = array();
                foreach($product_name as $product_item) {
                    array_push($products, (string)$product_item);
                }
                $schedules[$key]['product_name'] = implode(',',$products);
                $schedules[$key]['engineer_id'] = ScheduleEngineer::where([
                    'schedule_engineers.schedule_id' => $row->id
                ])->get()->pluck('engineer_id');
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
            $res['customer_userlist'] = User::whereIn('user_type',[2,6])->where('status',1)->where('company_id',$room->company_id)->select('id','first_name','last_name')->get();
            $res['task_assign_to'] = User::where('company_id',$room->company_id)->whereIn('user_type',[1,3])->where('status',1)->select('id','first_name','last_name','profile_pic')->get();

        }
        if(($request->has('project_id') && $request->project_id != 'null')||$request->has('project_name')){
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
            $com_id = Company_customer::where('customer_id',$company_id)->first()->company_id;
            $res['engineers'] = User::where('company_id',$com_id)->where('user_type',2)->where('status',1)->select('id','first_name','last_name','profile_pic')->get();
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
        $res['test_forms'] = New_form::where('created_by', $request->user->company_id)
            ->where('form_type', 1)->get();
        $res['com_forms'] = New_form::where('created_by', $request->user->company_id)
            ->where('form_type', 2)->get();
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
        $v = Validator::make($request->all(), [
            'change_notes' =>'required',

        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input require in the field!'
            ]);
        }
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
            'notification'		=> $request->user->first_name.' '.$request->user->last_name. " has sent request to change location[".$room->room_number."]",
            'created_by'		=> $request->user->id,
            'company_id'		=> $room->company_id,
            'project_id'		=> $room->project_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationdata);
        //add task
        $task = array();
        if($request->hasFile('task_img')){

            $fileName = time().'task.'.$request->task_img->extension();
            $request->task_img->move(public_path('upload/img/'), $fileName);
            $task['task_img']  = $fileName;
        }
        $task['task'] = $request->change_notes;
        $task['company_id'] = $room->company_id;
        $task['project_id']  = $room->project_id;
        $task['room_id']  = $room->id;
        $task['due_by_date']  = date("Y-m-d H:i:s");
        $task['description']  = $request->change_notes;
        $task['created_by']  = $request->user->id;
        $task['is_require'] = 1;
        Task::create($task);

        //sending gmail to user
        $pending_user = User::where('id',$room->created_by)->first();
        $to_name = $pending_user['first_name'];
        $to_email = $pending_user['email'];
        $content = $request->user->first_name.' '.$request->user->last_name. ' has been sent request to change location['.$room->room_number.']';
        $invitationURL = "https://app.sirvez.com/app/app/project/live/".$room['project_name'].'/'.$room['room_number'];
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
            $invitationURL = "https://app.sirvez.com/app/app/project/live/".$room['project_name'].'/'.$room['room_number'];
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
    public function changeRoomNumber(request $request){
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::whereId($id)->update(['room_number'=>$request->room_number]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeInstall(request $request){
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        $asbestos = $request->asbestos;

        Room::whereId($id)->update(['estimate_day'=>$request->day,'estimate_time'=>$request->time]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeCeiling(request $request){
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::whereId($id)->update(['ceiling_height'=>$request->ceiling_height]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeWall(request $request){
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::whereId($id)->update(['wall'=>$request->wall]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeAsbestos(request $request){
        if(strlen($request->id) > 10)
            $id = Room::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        if($request->asbestos =='yes')
            $asbestos = 1;
        else if($request->asbestos =='no')
            $asbestos = 0;
        else
            $asbestos = 2;
        Room::whereId($id)->update(['asbestos'=>$asbestos]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
}
