<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Notification;
use App\Company;
use App\Company_customer;
use App\Project;
use App\Room;
use App\User;
use App\Site;
use App\Project_user;
use App\TaskComment;
use Mail;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function updateTask(Request $request){

        $v = Validator::make($request->all(), [
            'task' =>'required',
            'company_id' => 'required',
            'project_id' => 'required',
            //'site_id' => 'required',
            'room_id' => 'required',
            'due_by_date' => 'required',
            'priority' => 'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }

        $task = array();
        $id = $request->id;
        $task['task'] = $request->task;
        $task['company_id'] = $request->company_id;
        $task['project_id']  = $request->project_id;
        //$task['site_id']  = $request->site_id;
        $task['room_id']  = $request->room_id;
        $task['due_by_date']  = $request->due_by_date;
        $task['priority']  = $request->priority;
        $task['description']  = $request->description;
        if ($request->has('favourite')) {
            $task['favourite'] = $request->favourite;
        }
        if ($request->has('archived')) {
            $task['archived'] = $request->archived;
            $task['archived_day'] = date('Y-m-d');
        }
        $action = "updated";
        if($request->hasFile('task_img')){

            $fileName = time().'task.'.$request->task_img->extension();
            $request->task_img->move(public_path('upload/img/'), $fileName);
            $task['task_img']  = $fileName;
        }
        if(!isset($id) || $id=="" || $id=="null" || $id=="undefined"||strlen($request->id)>10){
            $task['created_by']  = $request->user->id;
            if(strlen($request->id)>10)
            $task['off_id'] = $request->id;
            $result = Task::create($task);
            $id = $result->id;
            $action = "created";
            if($request->has('assign_to'))
            {
                Project_user::where(['project_id'=>$id,'type'=>'2'])->delete();
                $array_res = array();
                $array_res =json_decode($request->assign_to,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>'2']);

                }
            }
        }
        else{

            $task['updated_by'] = $request->user->id;
            if($request->created_by) $task['created_by'] = $request->created_by;
            Task::whereId($id)->update($task);
            if($request->has('assign_to'))
            {
                Project_user::where(['project_id'=>$id,'type'=>'2'])->delete();
                $array_res = array();
                $array_res =json_decode($request->assign_to,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>'2']);

                }
            }
        }
       //$notice_type ={1:pending_user,2:createcustomer 3:project 4:task}
       $insertnotificationndata = array(
        'notice_type'		=> '4',
        'notice_id'			=> $id,
        'notification'		=> $task['task'].' have been '.$action.' by  '.$request->user->first_name.' ('.$request->user->company_name.').',
        'created_by'		=> $request->user->id,
        'company_id'		=> $request->company_id,
        'created_date'		=> date("Y-m-d H:i:s"),
        'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);

        //sending gmail to user
        $array_res =json_decode($request->assign_to,true);
        $users = User::whereIn('id',$array_res)->get();
        foreach($users as $pending_user){
            $to_name = $pending_user['first_name'];
            $to_email = $pending_user['email'];
            $content = $request->user->first_name.' has been '.$action.' task as '.$request->task;
            $data = ['name'=>$pending_user['first_name'], "content" => $content];
            Mail::send('basicmail', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez supprot team');
            });
        }

        $response = ['status'=>'success', 'msg'=>'Task Saved Successfully!', 'req'=>$request->all()];
        return response()->json($response);
    }
    public function setCompleted(Request $request)
    {
        $id = $request->id;
        if(strlen($id)>10){
            Task::where('off_id',$id)->update(['archived'=>$request->archived,'archived_day'=>date('Y-m-d')]);
        }
        else
            Task::whereId($id)->update(['archived'=>$request->archived,'archived_day'=>date('Y-m-d')]);
        //Task::where(['id'=>$request->id])->delete();
        $res["status"] = "success";

        return response()->json($res);
    }
    public function taskList(Request $request){
        $res = array();
        if($request->has('room_id') && $request->room_id != 'undefined' && $request->room_id){
            $res['room_id'] = $request->room_id;
            if($request->user->user_type == 2||$request->user->user_type == 6){
                $res["tasks"] = Task::where('tasks.company_id',$request->user->company_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->where('tasks.room_id',$request->room_id)
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();

                $res['users'] = User::where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::where('id',$request->user->company_id)->get();
                //$res['projects'] = Project::where('id',$request->project_id)->get();
                $res['projects'] = array();
                $res['customerId'] = Room::where('id',$request->room_id)->first()->company_id;
            }
            else{
                $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $res["tasks"] = Task::whereIn('tasks.company_id',$customer_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->where('tasks.room_id',$request->room_id)
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();

                $res['users'] = User::/* whereIn('company_id',$customer_id)->or */Where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::whereIn('id',$customer_id)->get();
                // $res['projects'] = Project::where('id',$request->project_id)->get();
                $res['projects'] = array();
                $res['customerId'] = Room::where('id',$request->room_id)->first()->company_id;
            }
        }
        else if($request->has('customer_id') && $request->customer_id != 'undefined' && $request->customer_id){
            $res['project_id'] = '';
            if($request->user->user_type == 2||$request->user->user_type == 6){
                $res["tasks"] = Task::where('tasks.company_id',$request->customer_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->where('tasks.customer_id',$request->customer_id)
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();

                $res['users'] = User::where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::where('id',$request->customer_id)->get();
                $res['projects'] = Project::where('company_id',$request->customer_id)->get();
                $res['customerId'] = $request->customer_id;
            }
            else{
                
                $res["tasks"] = Task::where('tasks.company_id',$request->customer_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->where('tasks.company_id',$request->customer_id)
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();
                $res['users'] = User::/* whereIn('company_id',$customer_id)->or */Where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::where('id',$request->customer_id)->get();
                $res['projects'] = Project::where('company_id',$request->user->company_id)->get();
                $res['customerId'] = $request->customer_id;
            }
        }
        else if($request->has('project_id') && $request->project_id != 'undefined' && $request->project_id){
            $res['project_id'] = $request->project_id;
            if($request->user->user_type == 2||$request->user->user_type == 6){
                $res["tasks"] = Task::where('tasks.company_id',$request->user->company_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->where('tasks.project_id',$request->project_id)
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();

                $res['users'] = User::where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::where('id',$request->user->company_id)->get();
                $res['projects'] = Project::where('id',$request->project_id)->get();
                $res['customerId'] = Project::where('id',$request->project_id)->first()->company_id;
            }
            else{
                $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $res["tasks"] = Task::whereIn('tasks.company_id',$customer_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->where('tasks.project_id',$request->project_id)
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();
                $res['users'] = User::/* whereIn('company_id',$customer_id)->or */Where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::whereIn('id',$customer_id)->get();
                $res['projects'] = Project::where('id',$request->project_id)->get();
                $res['customerId'] = Project::where('id',$request->project_id)->first()->company_id;
            }
        }
        
        else{
             if($request->user->user_type == 2||$request->user->user_type == 6){
                $res["tasks"] = Task::where('tasks.company_id',$request->user->company_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();

                $res['users'] = User::where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::where('id',$request->user->company_id)->get();
                $res['projects'] = Project::where('company_id',$request->user->company_id)->get();
            }
            else{
                $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $res["tasks"] = Task::whereIn('tasks.company_id',$customer_id)
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                    ->leftJoin('sites','sites.id','=','tasks.site_id')
                    ->leftJoin('rooms','rooms.id','=','tasks.room_id')
                    ->leftJoin('buildings','buildings.id','=','rooms.building_id')
                    ->leftJoin('floors','floors.id','=','rooms.floor_id')
                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                    ->leftjoin('users','users.id','=','tasks.created_by')
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();
                $res['users'] = User::/* whereIn('company_id',$customer_id)->or */Where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::whereIn('id',$customer_id)->get();
                $res['projects'] = Project::whereIn('company_id',$customer_id)->get();
            }
        }
        $res['status'] = "success";
        return response()->json($res);
    }
    public function getTaskInfo(Request $request){
        //return response()->json($request);
        if ($request->has('id')) {
            $id = $request->id;

            $res = array();
            $res['status'] = 'success';
            $res['task'] = Task::whereId($id)->first();
            $res['task']['assign_to'] = Project_user::where(['project_id'=>$id,'type'=>'2'])->pluck('user_id');
        }
        if($request->has('company_id')){
            $company_id = $request->company_id;
            $res['customer'] = Company::where('id',$company_id)->orderBy('id','desc')->get();
            $res['project'] = Project::where('company_id',$company_id)->orderBy('id','desc')->get();
            $res['customer_site'] = Site::where('company_id',$company_id)->orderBy('id','desc')->get();
            $res['room'] = Room::where('company_id',$company_id)->get();$company_id = Company_customer::where('company_id',$request->user->company_id)->orderBy('id','desc')->pluck('customer_id');
        }
        if($request->has('room_id')){
            $room = Room::whereId($request->room_id)->first();
            $res['customer'] = Company::whereId($room->company_id)->orderBy('id','desc')->get();
            $res['project'] = Project::whereId($room->project_id)->orderBy('id','desc')->get();
            $res['customer_site'] = Site::whereId($room->site_id)->orderBy('id','desc')->get();
            $res['room'] = Room::whereId($room->id)->orderBy('id','desc')->get();
        }
        else{
            if($request->user->user_type==1||$request->user->user_type==1)
                $company_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            else
                $company_id = $request->user->company_id;
            $res['customer'] = Company::whereIn('id',$company_id)->orderBy('id','desc')->get();
            $res['project'] = Project::whereIn('company_id',$company_id)->orderBy('id','desc')->get();
            $res['customer_site'] = Site::whereIn('company_id',$company_id)->orderBy('id','desc')->get();
            $res['room'] = Room::whereIn('company_id',$company_id)->orderBy('id','desc')->get();
        }

        if($request->user->user_type ==1||$request->user->user_type ==2)
            $com_id = $request->user->company_id;
        else
            $com_id = Company_customer::where('customer_id',$request->user->company_id)->first()->company_id;

        $res['assign_to'] = User::where('company_id',$com_id)->whereIn('user_type',[1,3])->where('status',1)->get();

        return response()->json($res);
    }
    public function setFavourite(request $request)
    {
        Task::whereId($request->id)->update(['favourite'=>$request->favourite]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function commentSubmit(request $request)
    {
        $v = Validator::make($request->all(), [
            'id' =>'required',
            'message' => 'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input comments in the field!'
            ]);
        }
        $task = array();
        if(strlen($request->id)>10){
            $task['task_id'] = Task::where('off_id',$request->id)->first()->id;
        }else{
            $task['task_id'] = $request->id;
        }
        
        $task['created_by'] = $request->user->id;
        $task['comment']  = $request->message;
        if($request->hasFile('file')){

            $fileName = time().'task_comment.'.$request->file->extension();
            $request->file->move(public_path('upload/file/'), $fileName);
            $task['attach_file']  = $fileName;
        }
        TaskComment::create($task);
        $task = Task::whereId($task['task_id'])->first();
        $res = array();
        $res['comments'] = TaskComment::where('task_comments.task_id',$task['task_id'])
            ->leftJoin('users','users.id','=','task_comments.created_by')
            ->select('task_comments.*','users.first_name','users.last_name','users.profile_pic')
            ->get();

        $insertnotificationndata = array(
            'notice_type'		=> '5',
            'notice_id'			=> $request->id,
            'notification'		=> 'New comment has been add in '.$task['task'].' by  '.$request->user->first_name.' ('.$request->user->company_name.').',
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->user->company_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
            );
        Notification::create($insertnotificationndata);

        $array_res =Project_user::where('project_id',$request->id)->where('type',2)->pluck('user_id');
        $users = User::whereIn('id',$array_res)->get();
        foreach($users as $pending_user){
            $to_name = $pending_user['first_name'];
            $to_email = $pending_user['email'];
            $content = $request->user->first_name.' has been add new comment in the task('.$task['task'].') - '.$request->message;
            $data = ['name'=>$pending_user['first_name'], "content" => $content];
            Mail::send('basicmail', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez supprot team');
            });
        }

        $res['status'] = 'success';
        return response()->json($res);
    }
    public function getComments(request $request)
    {
        $res = array();
        $res['comments'] = TaskComment::where('task_comments.task_id',$request->id)
            ->leftJoin('users','users.id','=','task_comments.created_by')
            ->select('task_comments.*','users.first_name','users.last_name','users.profile_pic')
            ->get();
        $res['status'] = 'success';
        return response()->json($res);
    }
}
