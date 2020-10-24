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
        if(strlen($request->id) > 10)
            if(Task::where('off_id',$request->id)->count() > 0)
                $id = Task::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id=="" || $id=="null" || $id=="undefined"){
            
            $task['created_by']  = $request->user->id;
            if(strlen($request->id)>10)
            $task['off_id'] = $request->id;
            $task = Task::create($task);
            $id = $task->id;
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
            $task = Task::whereId($id)->first();
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
       if($action == "created"){
        $insertnotificationndata = array(
            'notice_type'		=> '4',
            'notice_id'			=> $id,
            //'notification'		=> $task['task'].' have been '.$action.' by  '.$request->user->first_name.' ('.$request->user->company_name.').',
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' '.$action.' a new task['.$task['task'].']',
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->company_id,
            'project_id'		=> $request->project_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
            );
            Notification::create($insertnotificationndata);
        }
        //sending gmail to user
        $array_res =json_decode($request->assign_to,true);
        $users = User::whereIn('id',$array_res)->get();
        foreach($users as $pending_user){
            $to_name = $pending_user['first_name'];
            $to_email = $pending_user['email'];
            $content = $request->user->first_name.' '.$request->user->last_name.' has assigned you a task.';
           $task_img = 'https://app.sirvez.com/upload/img/'.$task['task_img'];
            $invitationURL = "https://app.sirvez.com/app/task-manager/my-task";
            $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$task['task'],"description" =>$task['description'],"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view task'];
            
            Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez support team');
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
                $tasks = Task::where('tasks.company_id',$request->user->company_id)
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
                $tasks = Task::whereIn('tasks.company_id',$customer_id)
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
                $tasks = Task::where('tasks.company_id',$request->customer_id)
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
                
                $tasks = Task::where('tasks.company_id',$request->customer_id)
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
                $tasks = Task::where('tasks.company_id',$request->user->company_id)
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
                $tasks = Task::whereIn('tasks.company_id',$customer_id)
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
                $tasks = Task::where('tasks.company_id',$request->user->company_id)
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
                $tasks = Task::whereIn('tasks.company_id',$customer_id)
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
        foreach($tasks as $key=>$row){
            $room = Room::where('id',$row->room_id)->first();
            $tasks[$key]['site_name'] = Site::where('id',$room->site_id)->first()->site_name;
            $assign_to= Project_user::where(['project_users.project_id'=>$row->id,'type'=>'2'])
            ->leftJoin('users','users.id','=','project_users.user_id')
            ->select('users.first_name as assign_name')->pluck('assign_name');
            $assign = array();
            foreach($assign_to as $assign_item) {
                array_push($assign, (string)$assign_item);
            }
            $assign_str = implode(',',$assign);
            $tasks[$key]['assign_to'] = $assign_str;
            $comment_number = TaskComment::where('task_id',$tasks[$key]['id'])->count();
            $tasks[$key]['comment_number'] = $comment_number;
            $task_comments = TaskComment::where('task_comments.task_id',$tasks[$key]['id'])
                ->leftJoin('users','users.id','=','task_comments.created_by')
                ->select('task_comments.*','users.first_name as created_user_f','users.last_name as created_user_l')
                ->get();
            for($i = 1;$i<=3;$i++){
                $tasks[$key]['comment'.$i] = '';
                $tasks[$key]['comment'.$i.'_date'] = '';
                $tasks[$key]['comment'.$i.'_user'] = '';
            }
            for($i = 1;$i<=$comment_number;$i++){
                if($i>3) break;
                $tasks[$key]['comment'.$i] = $task_comments[$i-1]['comment'];
                $tasks[$key]['comment'.$i.'_date'] = date('d-m-Y',strtotime($task_comments[$i-1]['created_at']));
                $tasks[$key]['comment'.$i.'_user'] = $task_comments[$i-1]['created_user_f'].' '.$task_comments[$i-1]['created_user_l'];
            }
        }
        $res['tasks'] = $tasks;
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
            'notice_type'		=> '4',
            'notice_id'			=> $request->id,
            //'notification'		=> 'New comment has been add in '.$task['task'].' by  '.$request->user->first_name.' ('.$request->user->company_name.').',
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has been add a new comment in '.$task['task'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $task->company_id,
            'project_id'		=> $task->project_id,
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
            $task_img = 'https://app.sirvez.com/upload/img/'.$task['task_img'];
            $invitationURL = "https://app.sirvez.com/app/task-manager/my-task";
            $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$task['task'],"description" =>$task['description'],"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view task'];
            Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez support team');
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
