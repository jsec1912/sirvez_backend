<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Project;
use App\Project_site;
use App\Site;
use App\Site_room;
use App\Task;
use App\Notification;
use App\User;
use App\Room;
use App\Room_photo;
use App\Company;
use App\Product;
use App\Company_customer;
use App\Project_user;
use Mail;
class ProjectController extends Controller
{
    public function updateProject(Request $request){
        
        $v = Validator::make($request->all(), [
            //company info
            'customer_id' => 'required',
            'project_name' => 'required|unique:projects',
            'manager_id' => 'required',
            'user_id' => 'required',
            'contact_number' => 'required',
            'survey_start_date' => 'required',
            'project_summary' => 'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $project = array();
       
        $id = $request->id;
        if($request->hasFile('upload_doc')){

            $fileName = time().'.'.$request->upload_doc->extension();
            $request->upload_doc->move(public_path('upload/file/'), $fileName);
            $project['upload_doc']  = $fileName;
        }
        if(strlen($request->customer_id) > 10){
            $project['company_id'] = Company::where('off_id',$request->customer_id)->first()->id;
        }
        else
            $project['company_id'] = $request->customer_id;
        //$project['company_id'] = $request->customer_id;
        $project['project_name']  = $request->project_name;
        $project['user_id']  = $request->user_id;
        $project['manager_id']  = $request->manager_id;
        $project['contact_number']  = $request->contact_number;
        $project['survey_start_date']  = $request->survey_start_date;
        $project['created_by']  = $request->user->id;
        $project['project_summary']  = $request->project_summary;
        $action = "updated";
        if(strlen($request->id) > 10)
            if(Project::where('off_id',$request->id)->count() > 0)
                $id = Project::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id=="" || $id=="null" || $id=="undefined"){
            if(strlen($request->id) > 10)
                $project['off_id'] = $request->id;
            $project = Project::create($project);
            $action = "created";
            $id = $project->id;
            if($request->has('assign_to'))
            {
                $array_res = array();
                $array_res =json_decode($request->assign_to,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>1]);
                }
            }
        }
        else{
            Project::whereId($id)->update($project);

            if($request->has('assign_to'))
            {
                Project_user::where(['project_id'=>$id,'type'=>'1'])->delete();
                $array_res = array();
                $array_res =json_decode($request->assign_to,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>1]);
                }
            }
        }

        //$notice_type ={1:pending_user,2:createcustomer 3:project}
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $project['project_name'].' have been '.$action.' by  '.$request->user->first_name.' ('.$request->user->company_name.').',
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->company_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);

        //sending gmail to user
        $pending_user = User::where('id',$request->user_id)->first();
        
        $to_name = $pending_user['first_name'];
        $to_email = $pending_user['email'];
        $content = $request->user->first_name.' has been '.$action.' project as '.$request->project_name;
        $data = ['name'=>$pending_user['first_name'], "content" => $content];
        Mail::send('basicmail', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('sirvez notification.');
            $message->from('support@sirvez.com','sirvez supprot team');
        });
        

        $response = ['status'=>'success', 'msg'=>'Project Saved Successfully!'];
        return response()->json($response);
    }
    public function deleteProject(Request $request)
    {
        $id = $request->id;
        if(strlen($request->id)>10)
            $id = Project::where('off_id',$request->id)->first()->id;
        Project::whereId($id)->update(['archived'=>1,'archived_day'=>date('Y-m-d')]);
        $project = Project::whereId($id)->first();
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $project['project_name'].' have been completed by  '.$request->user->first_name.' ('.$request->user->company_name.').',
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->user->company_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);

        $array_res =Project_user::where('project_id',$request->id)->where('type',1)->pluck('user_id');
        $users = User::whereIn('id',$array_res)->get();
        foreach($users as $pending_user){
            $to_name = $pending_user['first_name'];
            $to_email = $pending_user['email'];
            $content = 'Project('.$project->project_name.') has been archived by '.$request->user->first_name;
            $data = ['name'=>$pending_user['first_name'], "content" => $content];
            Mail::send('basicmail', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez supprot team');
            });
        }

         $res["status"] = "success";
        return response()->json($res);
    }
    public function projectList(Request $request){
        $res = array();
        if($request->user->user_type==1){
            $id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $project_array = Project::whereIn('projects.company_id',$id)->where('archived',$request->archived)
            ->leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
        }
        else{
            $id = $request->user->company_id;
            $project_array = Project::where('projects.company_id',$id)->where('archived',$request->archived)
            ->leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
        }
        foreach($project_array as $key => $row){
            $project_array[$key]['site_count'] = Project_site::where('project_id',$row['id'])->count();
            $project_array[$key]['room_count'] = Room::where('project_id',$row['id'])->count();
            $project_array[$key]['messages'] = Notification::where('notice_type','3')->where('notice_id',$row['id'])->count();
        }
        $res["projects"] = $project_array;
        $res['status'] = "success";
        return response()->json($res);
    }
    public function projectDetail(Request $request){
        $res = array();
        if(!$request->has('id') || $request->id =='null')
            $id = Project::where('project_name',$request->project_name)->first()->id;
        else
            $id = $request->id;
        $project = Project::where('projects.id',$id)
        ->leftJoin('companies','projects.company_id','=','companies.id')
        ->leftJoin('users','users.id','=','projects.created_by')
        ->select('projects.*','companies.logo_img','companies.name AS company_name','users.first_name')->first();
        $res['notification'] = Notification::where('notice_type','6')->where('notice_id',$id)->orderBy('id','desc')->get();
        $user_cnt = User::where('id',$project->user_id)->count();
        if($user_cnt>0)
            $project['customer_user'] = User::where('id',$project->user_id)->first()->first_name;
        else
            $project['customer_user'] = '';
      
        $project['site_count'] = Project_site::where('project_id',$project['id'])->count();
        $project['room_count'] = Room::where('project_id',$project['id'])->count();
        $project['user_notifications'] = Notification::where('notice_type','3')->where('notice_id',$id)->count();
        $company_id = Project::whereId($id)->first()->company_id;
        $res['sites'] = Site::where('company_id',$company_id)->orderBy('id','desc')->get();
        // $res['sites'] = Project_site::where('project_id',$project['id'])
        //     ->leftJoin('sites','project_sites.site_id','=','sites.id')->select('project_sites.*','sites.site_name','sites.city','sites.address','sites.postcode')->withCount('rooms')->orderBy('project_sites.id','desc')->get();
        $rooms = Room::where('rooms.project_id',$project['id'])
            ->leftJoin('sites','rooms.site_id','=','sites.id')
            ->leftJoin('buildings','rooms.building_id','=','buildings.id')
            ->select('rooms.*','sites.site_name','buildings.building_name')
            ->orderBy('rooms.id','desc')->get();
        foreach($rooms as $key => $room)
        {
            $rooms[$key]['products'] = Product::where('room_id',$room->id)->count();
            $rooms[$key]['total_tasks'] = Task::where('room_id',$room->id)->count();
            $rooms[$key]['complete_tasks'] =Task::where('room_id',$room->id)->where('archived',1)->count();
            $rooms[$key]['img_files'] = Room_photo::where('room_id',$room->id)->get();
        }
        $res['rooms'] = $rooms;
        $room_ids = Room::where('project_id',$project['id'])->pluck('id');
        $products = Product::whereIn('room_id',$room_ids)->orderBy('id','desc')->get();
        foreach($products as $key => $product)
        {
            $products[$key]['room_name'] = Room::whereId($product->room_id)->first()->room_number;
            //$products[$key]['to_room_name'] = Room::whereId($product->to_room_id)->first()->room_number;
            //$products[$key]['to_site_name'] = Site::whereId($product->to_site_id)->first()->site_name;
        }
        $res['products'] = $products;
        $tasks = Task::where('project_id',$project['id'])->orderBy('id','desc')->get();
        foreach($tasks as $key=>$row){
            $tasks[$key]['assign_to'] = Project_user::leftjoin('users','users.id','=','project_users.user_id')->where(['project_users.project_id'=>$row->id,'type'=>'2'])->pluck('users.first_name');
        }
        $res['tasks'] = $tasks;
        $res["project"] = $project;

        $res['customer_sites']= Site::where('company_id',$project->company_id)->orderBy('id','desc')->get();

        $res['assign_to'] = Project_user::where(['project_users.project_id'=>$id,'project_users.type'=>'1'])
                                        ->leftjoin('users','users.id','=','project_users.user_id')
                                        ->select('users.*')
                                        ->get();
        $assignId = Project_user::where(['project_id'=>$id,'type'=>1])->pluck('user_id');
        $com_id = Company_customer::where('customer_id',$project->company_id)->first()->company_id;
        $res['team'] = User::where('company_id',$com_id)->whereIn('user_type',[1,5])->where('status',1)->whereNotIn('id',$assignId)->get();
        $res['signed_cnt'] = Room::where('project_id',$id)->count()-Room::where('project_id',$id)->where('signed_off','1')->count();
        $res['status'] = "success";
        $res['project_id'] = $id;
        return response()->json($res);
    }
    public function getProjectInfo(Request $request){
        //return response()->json($request);
        $res = array();
        if ($request->has('id')) {
            $id = $request->id;
            $res['project'] = Project::whereId($id)->first();
            $res['project']['assign_to'] = Project_user::where(['project_id'=>$id,'type'=>'1'])->pluck('user_id');
        }
        if($request->user->user_type ==1||$request->user->user_type ==3){
            $company_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['customer'] = Company::whereIn('id',$company_id)->orderBy('id','desc')->get();
            $res['account_manager'] = User::whereIn('user_type',[1,3])->where('status',1)->where('company_id',$request->user->company_id)->select('id','first_name','last_name')->get();
            $res['customer_user'] = User::whereIn('user_type',[2,6])->where('status',1)->whereIn('company_id',$company_id)->select('id','first_name','last_name')->get();
        }
        else{
            $res['customer'] = Company::where('id',$request->user->company_id)->orderBy('id','desc')->get();
            $com_id = Company_customer::where('customer_id',$request->user->company_id)->first()->company_id;
            $res['account_manager'] = User::whereIn('user_type',[1,3])->where('status',1)->where('company_id',$com_id)->select('id','first_name','last_name')->get();
            $res['customer_user'] = User::whereIn('user_type',[2,6])->where('status',1)->where('company_id',$request->user->company_id)->select('id','first_name','last_name')->get();

        }
        if($request->user->user_type ==1||$request->user->user_type ==3)
            $com_id = $request->user->company_id;
        else
            $com_id = Company_customer::where('customer_id',$request->user->company_id)->first()->company_id;
        $res['assign_to'] = User::where('company_id',$com_id)->whereIn('user_type',[1,5])->where('status',1)->get();
        $res['sites'] = Site::orderBy('id','desc')->get();
        $res['rooms'] = Site_room::orderBy('id','desc')->get();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setFavourite(request $request)
    {
        Project::whereId($request->id)->update(['favourite'=>$request->favourite]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function deleteAssignUser(request $request)
    {
        Project_user::where('project_id',$request->project_id)->where('user_id',$request->user_id)->where('type',1)->delete();
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function signOff(request $request)
    {
        Project::whereId($request->id)->update(['signed_off'=>1]);
        $project=Project::where('projects.id',$request->id)
                ->leftJoin('companies','companies.id','=','projects.company_id')
                ->leftJoin('users','users.id','=','projects.created_by')
                ->select('projects.*','companies.name as company_name','users.first_name','users.last_name','users.email','users.password')
                ->first();
        $project['account_manager'] = User::where('id',$project['manager_id'])->first()->first_name;
        $project['customer_user'] = User::where('id',$project['user_id'])->first();
        if($request->user->user_type<6)
            $insertnotificationdata = array(
                'notice_type'		=> '6',
                'notice_id'			=> $request->id,
                'notification'		=> "Signed Off request was sent to ".$project['customer_user']->first_name." by ".$request->user->first_name.". ".date("d-m-Y H:i:s"),
                'created_by'		=> $request->user->id,
                'company_id'		=> $project->company_id,
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
            );
        else
            $insertnotificationdata = array(
                'notice_type'		=> '6',
                'notice_id'			=> $request->id,
                'notification'		=> "Project was signed off by ".$request->user->first_name.". ".date("d-m-Y H:i:s"),
                'created_by'		=> $request->user->id,
                'company_id'		=> $project->company_id,
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 1,
            );
        Notification::create($insertnotificationdata);
        
        //send mail
        $content = "";
        if($request->user->user_type <6){
            $pending_user = $project['customer_user'];
            $content = "Signed Off request was sent to ".$project['customer_user']->first_name." by ".$request->user->first_name.". ".date("d-m-Y H:i:s");
        }
        else{
            $pending_user = User::where('id',$project->created_by)->first();
            $content = "Project was signed off by ".$request->user->first_name.". ".date("d-m-Y H:i:s");
        }

        $to_name = $pending_user['first_name'];
        $to_email = $pending_user['email'];
        $data = ['name'=>$pending_user['first_name'], "content" => $content];
        Mail::send('basicmail', $data, function($message) use ($to_name, $to_email,$project) {
            $message->to($to_email, $to_name)
                    ->subject('sirvez notification.');
            $message->from('support@sirvez.com','supprot team');
        });
       

        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addAssignUser(request $request)
    {
        Project_user::create(['user_id'=>$request->user_id,'project_id'=>$request->project_id,'type'=>1]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
}
