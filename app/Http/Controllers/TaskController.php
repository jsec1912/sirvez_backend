<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use App\Task;
use App\Notification;
use App\Company;
use App\Company_customer;
use App\Project;
use App\Room;
use App\User;
use App\Site;
use App\Room_photo;
use App\Project_user;
use App\TaskComment;
use App\Task_comment_user;
use App\Task_label;
use App\Task_top_menu;
use App\Task_label_value;
use App\TaskBoard;
use App\TaskBucket;
use App\Partner;
use App\Customer_partner;
use Mail;
use App\Events\NotificationEvent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File; 

class TaskController extends Controller
{
    public function updateTask(Request $request){
        $v = Validator::make($request->all(), [
            'task' =>'required',
            //'company_id' => 'required',
            //'project_id' => 'required',
            //'site_id' => 'required',
            //'room_id' => 'required',
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
        if($request->room_id>0)
        $task['room_id']  = $request->room_id;
        $task['due_by_date']  = $request->due_by_date;
        $task['priority']  = $request->priority;
        $task['snagging']  = $request->snagging;
        $task['description']  = $request->description;
        if ($request->has('favourite')) {
            $task['favourite'] = $request->favourite;
        }
        if ($request->has('archived')) {
            if($request->archived=='1'){
                $task['archived'] = $request->archived;
                $task['archived_day'] = date('Y-m-d');
                $task['archived_id'] = $request->user->id;
            }
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
            if($request->hasFile('task_img')){
                Room_photo::create(['room_id'=>$task['room_id'],'user_id'=>$request->user->id,'task_id'=>$id,'img_name'=>$task['task_img'] ]);
            }
            $action = "created";
            if($request->has('assign_to'))
            {
                Project_user::where(['project_id'=>$id,'type'=>'2'])->delete();
                $array_res = array();
                $array_res =json_decode($request->assign_to,true);
                if($array_res){
                    foreach($array_res as $row)
                    {
                        Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>'2']);

                    }
                }
            }

            $notification = array();
            $project = Project::where('id', $task->project_id)->first();
            if($project)
            $project_name = $project->project_name;
            else
            $project_name = '';
            $text = $request->user->first_name . ' ' . $request->user->last_name . ' has added you to a new task: ' . $task->task . ' for project: ' . $project_name;
            $notification['title'] = 'Sirvez | New Task';
            $notification['text1'] = $text;
            $notification['text2'] = '.';
            $notification['id'] = 'task-' . $task->id;
            $notification['image'] = $request->user->profile_pic;
            $users = Project_user::where('project_id', $task->id)->where('type', '2')->pluck('user_id')->toArray();
            $users = array_map(function ($value) {
                return intval($value);
            }, $users);
            $notification['user_id'] = $users;
            $notification['created_by'] = $request->user->id;
            $notification['action_link'] = '/app/task-manager/my-tasks/' . $task->id;

            broadcast(new NotificationEvent($notification))->toOthers();
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
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new task : '.$task['task'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->company_id,
            'project_id'		=> $request->project_id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
            );
            Notification::create($insertnotificationndata);
        }
        //sending gmail to user
        if($request->assign_to){
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
        }
        $res = array();
        $res['task'] = $task;
        $res['status']='success';
        $res['msg'] = 'Task Saved Successfully!';
        return response()->json($res);
    }
    public function setCompleted(Request $request)
    {
        $id = $request->id;
        if(strlen($id)>10){
            Task::where('off_id',$id)->update([
                'archived'=>$request->archived,
                'archived_id'=>$request->user->id,
                'archived_day'=>date('Y-m-d')]);
            $task = Task::where('off_id',$id)->first();
        }
        else{
            Task::whereId($id)->update([
                'archived'=>$request->archived,
                'archived_id'=>$request->user->id,
                'archived_day'=>date('Y-m-d')]);
            $task = Task::where(['id'=>$request->id])->first();
            }

        if ($request->archived == 1) {
            $notification = array();
            $project = Project::where('id', $task->project_id)->first();
            $text = $request->user->first_name . ' ' . $request->user->last_name . ' has completed task ' . $task->task . ' for project: ' . $project->project_name . ' for ';
            $notification['title'] = 'Sirvez | Completed Task';
            $notification['text1'] = $text;
            $notification['text2'] = '.';
            $notification['id'] = 'task-complete-' . $task->id;
            $notification['image'] = $request->user->profile_pic;
            $users = Project_user::where('project_id', $task->id)->where('type', '2')->pluck('user_id')->toArray();
            $users = array_map(function ($value) {
                return intval($value);
            }, $users);
            $notification['user_id'] = $users;
            $notification['created_by'] = $request->user->id;
            $notification['action_link'] = '/app/task-manager/my-tasks/' . $task->id;

            broadcast(new NotificationEvent($notification))->toOthers();
        }
        $res["status"] = "success";

        return response()->json($res);
    }

    public function tasklist_of_room_check($room, $request) {
        $check = app('App\Http\Controllers\RoomController')->room_check($room, $request);
        if ($check == 4) {
            // partner
            $user_company_id = $request->user->company_id;
            if (Company_customer::where('customer_id', $request->user->company_id)->count() > 0) {
                $user_company_id = Company_customer::where('customer_id', $request->user->company_id)
                    ->first()->company_id;
            }
            $room_company_id = $room->company_id;
            if (Company_customer::where('customer_id', $room->company_id)->count() > 0) {
                $room_company_id = Company_customer::where('customer_id', $room->company_id)
                    ->first()->company_id;
            }
            if (Partner::where([
                'company_id' => $room_company_id,
                'partner_id' => $user_company_id
            ])->count() > 0) {
                $partner_row = Partner::where([
                    'company_id' => $room_company_id,
                    'partner_id' => $user_company_id
                ])->first();
                if ($partner_row->is_allowed == '2' && $partner_row->modify_task == '1') {
                    return 4;
                }
            }
            return 0;
        }
        return $check;
    }

    public function tasklist_of_company_check($request) {
        if ($request->user->user_type == 0) {
            return 1; // super super admin
        } else if ($request->user->user_type < 4) {
            $company_ids = Company_customer::where('company_id', $request->user->company_id)
                ->pluck('customer_id')->toArray();
            $partnerIds = Partner::where('partner_id',$request->user->company_id)->where('is_allowed',2)->pluck('id');
            $partner_comIds = Customer_partner::where('partner_id',$partnerIds)->pluck('customer_id')->toArray();
            $company_ids = array_merge($company_ids,$partner_comIds);
            array_push($company_ids, $request->user->company_id);
            if (in_array($request['customer_id'], $company_ids)) {
                return 2; // super admin
            }
        } else {
            if ($request->customer_id == $request->user->company_id) {
                return 3; // end user
            }
        }
        return 0;
    }

    public function tasklist_of_project_check($project, $request) {
        $check = app('App\Http\Controllers\ProjectController')->project_check($project, $request);
        if ($check == 4) {
            // partner case
            $user_company_id = $request->user->company_id;
            if (Company_customer::where('customer_id', $request->user->company_id)->count() > 0) {
                $user_company_id = Company_customer::where('customer_id', $request->user->company_id)
                    ->first()->company_id;
            }
            $project_company_id = $project->company_id;
            if (Company_customer::where('customer_id', $project->company_id)->count() > 0) {
                $project_company_id = Company_customer::where('customer_id', $project->company_id)
                    ->first()->company_id;
            }
            if (Partner::where([
                'company_id' => $project_company_id,
                'partner_id' => $user_company_id
            ])->count() > 0) {
                $partner_row = Partner::where([
                    'company_id' => $project_company_id,
                    'partner_id' => $user_company_id
                ])->first();
                if ($partner_row->is_allowed == '2' && $partner_row->modify_task == '1') {
                    return 4;
                }
            }
            return 0;
        }
        return $check;
    }

    public function taskList(Request $request){
        
        $res = array();
        if($request->has('room_id') && $request->room_id != 'undefined' && $request->room_id){
            $res['room_id'] = $request->room_id;
            $room = Room::whereId($request->room_id)->first();
            if ($this->tasklist_of_room_check($room, $request) == 0) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You do not have permission to view tasks of this location.'
                ]);
            }
            if ($request->user->user_type > 0) {
                $taskIdx = Project_user::where(['user_id'=>$request->user->id,'type'=>'2'])->pluck('project_id');
                $taskIds = Task::where(function($q) use($taskIdx,$request){
                                return $q->whereIn('tasks.id',$taskIdx)
                                ->orwhere('tasks.created_by',$request->user->id);
                            })
                            ->where('tasks.room_id',$request->room_id)
                            ->where(function($q){
                                return $q->where('tasks.archived',0)
                                ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                            })
                            ->pluck('id');
                $tasks = Task::where(function($q) use($taskIdx,$request){
                                    return $q->whereIn('tasks.id',$taskIdx)
                                    ->orwhere('tasks.created_by',$request->user->id);
                                })
                    ->where('tasks.room_id',$request->room_id)
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
                //$res['users'] = User::where('company_id',$request->user->company_id)->get();
                if($request->user->type>4)
                    $res['customers'] = Company::where('id',$com_id)->get();    
                else{
                    $customer_id = Company_customer::where('company_id',$com_id)->pluck('customer_id');
                    $res['customers'] = Company::whereIn('id',$customer_id)->get();
                }
                //$res['projects'] = Project::where('id',$request->project_id)->get();
                $res['projects'] = array();
                $res['customerId'] = Room::where('id',$request->room_id)->first()->company_id;
            } else {
                $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $taskIds = Task::where('tasks.room_id',$request->room_id)
                            ->where(function($q){
                                return $q->where('tasks.archived',0)
                                ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                            })
                            ->pluck('id');
                $tasks = Task::where('tasks.room_id',$request->room_id)
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

                //$res['users'] = User::whereIn('company_id',$customer_id)->orwhere('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::whereIn('id',$customer_id)->get();
                // $res['projects'] = Project::where('id',$request->project_id)->get();
                $res['projects'] = array();
                $res['customerId'] = Room::where('id',$request->room_id)->first()->company_id;
            }
        } else if ($request->has('customer_id') && $request->customer_id != 'undefined' && $request->customer_id != 'null' && $request->customer_id) {
            $res['project_id'] = '';
            if ($this->tasklist_of_company_check($request) == 0) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You do not have permission to view tasks of this location.'
                ]);
            }
            if ($request->user->user_type > 0) {
                $taskIdx = Project_user::where(['user_id'=>$request->user->id,'type'=>'2'])->pluck('project_id');
                $taskIds = Task::where(function($q) use($taskIdx,$request){
                                    return $q->whereIn('tasks.id',$taskIdx)
                                    ->orwhere('tasks.created_by',$request->user->id);
                                })
                                ->where(function($q){
                                    return $q->where('tasks.archived',0)
                                    ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                                })
                                ->where('tasks.company_id',$request->customer_id)
                                ->pluck('id');
                $tasks = Task::where(function($q) use($taskIdx,$request){
                                    return $q->whereIn('tasks.id',$taskIdx)
                                    ->orwhere('tasks.created_by',$request->user->id);
                                })
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->where('tasks.company_id',$request->customer_id)
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

                //$res['users'] = User::where('company_id',$request->user->company_id)->get();
                $com_id=$request->user->company_id;
                if($request->user->type>4)
                    $res['customers'] = Company::where('id',$com_id)->get();    
                else{
                    $customer_id = Company_customer::where('company_id',$com_id)->pluck('customer_id');
                    $res['customers'] = Company::whereIn('id',$customer_id)->get();
                }
                $res['projects'] = Project::where('company_id',$request->customer_id)->get();
                $res['customerId'] = $request->customer_id;
            } else {
                $taskIds = Task::where('tasks.company_id',$request->customer_id)
                        ->where(function($q){
                            return $q->where('tasks.archived',0)
                            ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                        })
                        ->pluck('id');
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
                    ->select('tasks.*','projects.project_name','companies.name as company_name','sites.site_name','rooms.room_number','floors.floor_name','buildings.building_name','users.first_name AS account_manager','users.profile_pic')
                    ->orderBy('archived','asc')
                    ->orderBy('tasks.id','desc')
                    ->get();
                //$res['users'] = User::where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::where('id',$request->customer_id)->get();
                $res['projects'] = Project::where('company_id',$request->user->company_id)->get();
                $res['customerId'] = $request->customer_id;
            }
        } else if ($request->has('project_id') && $request->project_id != 'undefined' && $request->project_id){
            $project = Project::whereId($request->project_id)->first();
            if ($this->tasklist_of_project_check($project, $request) == 0) {
                return response()->json([
                    'status' => 'success',
                    'msg' => 'You do not have permission to view tasks of this location.'
                ]);
            }
            $res['project_id'] = $request->project_id;
            if ($request->user->user_type >0) {
                $taskIdx = Project_user::where(['user_id'=>$request->user->id,'type'=>'2'])->pluck('project_id');
                $taskIds = Task::where(function($q) use($taskIdx,$request){
                                return $q->whereIn('tasks.id',$taskIdx)
                                ->orwhere('tasks.created_by',$request->user->id);
                                })
                                ->where(function($q){
                                    return $q->where('tasks.archived',0)
                                    ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                                })
                                ->where('tasks.project_id',$request->project_id)
                                ->pluck('id');
                $tasks = Task::where(function($q) use($taskIdx,$request){
                                    return $q->whereIn('tasks.id',$taskIdx)
                                    ->orwhere('tasks.created_by',$request->user->id);
                                })
                    ->where(function($q){
                        return $q->where('tasks.archived',0)
                        ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                    })
                    ->where('tasks.project_id',$request->project_id)
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

                //$res['users'] = User::where('company_id',$request->user->company_id)->get();
                $com_id=$request->user->company_id;
                if($request->user->type>4)
                    $res['customers'] = Company::where('id',$com_id)->get();    
                else{
                    $customer_id = Company_customer::where('company_id',$com_id)->pluck('customer_id');
                    $res['customers'] = Company::whereIn('id',$customer_id)->get();
                }
                $res['projects'] = Project::where('id',$request->project_id)->get();
                $res['customerId'] = Project::where('id',$request->project_id)->first()->company_id;
            } else {
                $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
                $taskIds = Task::where('tasks.project_id',$request->project_id)
                        ->where(function($q){
                            return $q->where('tasks.archived',0)
                            ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                        })
                        ->pluck('id');
                $tasks = Task::where('tasks.project_id',$request->project_id)
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
                //$res['users'] = User::where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::whereIn('id',$customer_id)->get();
                $res['projects'] = Project::where('id',$request->project_id)->get();
                $res['customerId'] = Project::where('id',$request->project_id)->first()->company_id;
            }
        } else {
            if ($request->user->user_type > 0) { 
                $taskIdx = Project_user::where(['user_id'=>$request->user->id,'type'=>'2'])->pluck('project_id');
                $taskIds = Task::where(function($q)use($taskIdx,$request){
                            return $q->whereIn('tasks.id',$taskIdx)
                            ->orwhere('tasks.created_by',$request->user->id);
                        })
                        ->where(function($q){
                            return $q->where('tasks.archived',0)
                            ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                        })
                        ->pluck('id');
                $tasks = Task::where(function($q)use($taskIdx,$request){
                                    return $q->whereIn('tasks.id',$taskIdx)
                                    ->orwhere('tasks.created_by',$request->user->id);
                                })
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

                //$res['users'] = User::where('company_id',$request->user->company_id)->get();
                $com_id=$request->user->company_id;
                if($request->user->type>4){
                    $res['customers'] = Company::where('id',$com_id)->get();   
                    $res['projects'] = Project::where('company_id',$com_id)->get(); 
                    $res['rooms'] = Room::where('company_id',$com_id)->get(); 
                }
                else{
                    $customer_id = Company_customer::where('company_id',$com_id)->pluck('customer_id');
                    $res['customers'] = Company::whereIn('id',$customer_id)->get();
                    $res['projects'] = Project::whereIn('company_id',$customer_id)->get();
                    $res['rooms'] = Room::whereIn('company_id',$customer_id)->get();  
                }
            } else {
                $customer_id = Company_customer::where('company_id',$request->user->company_id)
                ->pluck('customer_id')->toArray();
                array_push($customer_id, $request->user->company_id);

                $user_company_id = $request->user->company_id;
                if (Company_customer::where('customer_id', $user_company_id)->count() > 0) {
                    $user_company_id = Company_customer::where('customer_id', $user_company_id)
                        ->first()->company_id;
                }

                $partner_company_ids = Partner::where([
                    'company_id' => $user_company_id,
                    'is_allowed' => '2',
                    'modify_task' => '1'
                ])->pluck('partner_id')->toArray();

                $company_ids = array_merge($customer_id, $partner_company_ids);

                $taskIds = Task::whereIn('tasks.company_id',$company_ids)
                                ->where(function($q){
                                    return $q->where('tasks.archived',0)
                                    ->orwhere('tasks.archived_day', '>', date('Y-m-d', strtotime("-15 days")));
                                })
                                ->pluck('id');
                
                $tasks = Task::whereIn('tasks.id',$taskIds)
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
                //$res['users'] = User::where('company_id',$request->user->company_id)->get();
                $res['customers'] = Company::whereIn('id',$customer_id)->get();
                $res['projects'] = Project::whereIn('company_id',$customer_id)->get();
                $res['rooms'] = Room::whereIn('company_id',$customer_id)->get();  
            }
        }
        
        foreach($tasks as $key=>$row){            
            $tasks[$key]['label_value'] = Task_label_value::where('task_id',$row->id)->pluck('label_id');
            $room = Room::where('id',$row->site_id)->first();
            if(Site::where('id',$row->site_id)->count() > 0 )
                $tasks[$key]['site_name'] = Site::where('id',$row->site_id)->first()->site_name;
            else
                $tasks[$key]['site_name'] = '';
            $assign_to= Project_user::where(['project_users.project_id'=>$row->id,'type'=>'2'])
            ->leftJoin('users','users.id','=','project_users.user_id')
            ->select('users.first_name as assign_name')->pluck('assign_name');
            $assign = array();
            foreach($assign_to as $assign_item) {
                array_push($assign, (string)$assign_item);
            }
            $assign_str = implode(',',$assign);
            $tasks[$key]['assign_to'] = $assign_str;
            $tasks[$key]['assign_users'] = Project_user::where(['project_users.project_id'=>$row->id,'type'=>'2'])
            ->leftJoin('users','users.id','=','project_users.user_id')->get();
            $comment_number = TaskComment::where('task_id',$tasks[$key]['id'])->count();
            $tasks[$key]['comment_number'] = $comment_number;
            $task_comments = TaskComment::where('task_comments.task_id',$tasks[$key]['id'])
                ->leftJoin('users','users.id','=','task_comments.created_by')
                ->select('task_comments.*','users.first_name','users.last_name','users.profile_pic')
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
                $tasks[$key]['comment'.$i.'_user'] = $task_comments[$i-1]['first_name'].' '.$task_comments[$i-1]['last_name'];
            }
            
            foreach($task_comments as $key1 => $comment){
                $task_comments[$key1]['assign_users'] = Task_comment_user::where('task_comment_users.comment_id',$comment->id)
                    ->leftJoin('users','users.id','=','task_comment_users.user_id')
                    ->select('users.*')->get();
                $task_comments[$key1]['now_time'] = date("Y-m-d H:i:s");
            }
            $tasks[$key]['comments'] = $task_comments;
        } 
        $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
        $res['users'] = User::whereIn('company_id',$customer_id)->orwhere('company_id',$request->user->company_id)->get();
        $labelIds = Task_label_value::whereIn('task_id',$taskIds)->pluck('label_id');
        $res['task_used_labels'] = Task_label::whereIn('id',$labelIds)->get();
        $res['task_labels'] = Task_label::get();
        $res['all_users'] = User::get();
        $res['project_users'] = Project_user::where('type', '!=', '2')->get();
        $res['tasks'] = $tasks;
        if($request->user->user_type>4)
            $com_id = Company_customer::where('customer_id',$request->user->company_id)->first()->company_id;
        else 
            $com_id = $request->user->company_id;
        $res['boards'] = TaskBoard::where('company_id',$com_id)->get();
        $board_ids = TaskBoard::where('company_id',$com_id)->pluck('id');
        $res['buckets'] = TaskBucket::whereIn('board_id',$board_ids)->get();
        $res['top_menus_value'] = Task_top_menu::get();
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
            $res['room'] = Room::where('company_id',$company_id)->get();
        }
        if($request->has('room_id')){
            $room = Room::whereId($request->room_id)->first();
            $res['customer'] = Company::whereId($room->company_id)->orderBy('id','desc')->get();
            $res['project'] = Project::whereId($room->project_id)->orderBy('id','desc')->get();
            $res['customer_site'] = Site::whereId($room->site_id)->orderBy('id','desc')->get();
            $res['room'] = Room::whereId($room->id)->orderBy('id','desc')->get();
        }
        else{
            $companyId = array();
            if($request->user->user_tpe==0)
                $companyId = Company::pluck('id');
            else if($request->user->user_type<6)
                $companyId = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['customer'] = Company::whereIn('id',$companyId)->orWhere('id',$request->user->company_id)->orderBy('id','desc')->get();
            $res['project'] = Project::whereIn('company_id',$companyId)->orWhere('id',$request->user->company_id)->orderBy('id','desc')->get();
            $res['customer_site'] = Site::whereIn('company_id',$companyId)->orWhere('id',$request->user->company_id)->orderBy('id','desc')->get();
            $res['room'] = Room::whereIn('company_id',$companyId)->orWhere('id',$request->user->company_id)->orderBy('id','desc')->get();
        }

        if($request->user->user_type <=3)
            $com_id = $request->user->company_id;
        else
            $com_id = Company_customer::where('customer_id',$request->user->company_id)->first()->company_id;

        $res['assign_to'] = User::where('company_id',$com_id)->whereIn('user_type',[1,3])->where('status',1)->get();
        $res['project_users'] = Project_user::where('type', '!=', '2')->get();
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
        $comment_array = array();
        $comment_array = explode('@',$request->message);
        $task['comment']  = $comment_array[0];
        $task['deadline']  = $request->deadline;
        if($request->parent_id){
            $task['parent_id'] = $request->parent_id;
            $task['complete'] = 1;
        }
       
        if($request->hasFile('file')){

            $fileName = time().'comment.'.$request->file->extension();
            $request->file->move(public_path('upload/file/'), $fileName);
            $task['attach_file']  = $fileName;
            $task['file_size'] = number_format($request->file->getSize()/1024,2);
        }
        $task = TaskComment::create($task);
        if(count($comment_array)>0){
            $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $com_id = $request->user->company_id;
            foreach($comment_array as $key=>$row){
                if($key==0) continue;
                $name = array();
                $name = preg_split('/(?=[A-Z])/',$row);
                $first_name =$name[1];
                $last_name =$name[2];
                $assign_user = User::where(function($q) use($customer_id, $com_id){
                            return $q->whereIn('company_id',$customer_id)
                            ->orwhere('company_id',$com_id);
                        })
                        ->whereRaw("UPPER(first_name) = '". strtoupper($first_name)."'")
                        ->whereRaw("UPPER(last_name) = '". strtoupper($last_name)."'")
                        ->first();
                if($assign_user){
                    Task_comment_user::create(['comment_id'=>$task->id,'user_id'=>$assign_user->id]);
                }
            }
        }
        $notification = array();

        if($request->has('commentUserList'))
        {
            Task_comment_user::where(['comment_id'=>$task->id])->delete();
            $array_res = array();
            $array_res =json_decode($request->commentUserList,true);
            if($array_res){
                $users = array();
                foreach($array_res as $row)
                {
                    Task_comment_user::create(['comment_id'=>$task->id,'user_id'=>$row]);
                    $users[] = $row;
                }

                $project = Task::where('tasks.id', $task->task_id)
                    ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                    ->select('tasks.*', 'projects.project_name')->first();
                $text = $request->user->first_name . ' ' . $request->user->last_name . ' has added you to a new task: To Do for project: ' . $project->project_name . ' for ';
                $notification['title'] = 'Sirvez | New To Do';
                $notification['text1'] = $text;
                $notification['text2'] = ' Deadline: ' . $task->deadline;
                $notification['id'] = 'task-comment-user-' . $task->id;
                $notification['image'] = $request->user->profile_pic;
                $notification['user_id'] = $users;
                $notification['created_by'] = $request->user->id;
                $notification['action_link'] = '/app/task-manager/my-tasks/' . $task->task_id;
    
                broadcast(new NotificationEvent($notification))->toOthers();
            }
        }

        if (count($notification) == 0) {
            $project = Task::where('tasks.id', $task->task_id)
                ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                ->select('tasks.*', 'projects.project_name')->first();
            $text = $request->user->first_name . ' ' . $request->user->last_name . ' has added a new task comment to ' . $project->task . ' for project: ' . $project->project_name . ' for ';
            $notification['title'] = 'Sirvez | New Task comment';
            $notification['text1'] = $text;
            $notification['text2'] = '.';
            $notification['id'] = 'task-comment-' . $task->id;
            $notification['image'] = $request->user->profile_pic;
            $users = Project_user::where('project_id', $task->task_id)->where('type', '2')->pluck('user_id')->toArray();
            $users = array_map(function ($value) {
                return intval($value);
            }, $users);
            $notification['user_id'] = $users;
            $notification['created_by'] = $request->user->id;
            $notification['action_link'] = '/app/task-manager/my-tasks/' . $task->task_id;

            broadcast(new NotificationEvent($notification))->toOthers();
        }
        $res = array();
        $res['comments'] = TaskComment::where('task_comments.task_id',$task['task_id'])
            ->leftJoin('users','users.id','=','task_comments.created_by')
            ->select('task_comments.*','users.first_name','users.last_name','users.profile_pic')
            ->get();

        $insertnotificationndata = array(
            'notice_type'		=> '4',
            'notice_id'			=> $task->id,
            //'notification'		=> 'New comment has been add in '.$task['task'].' by  '.$request->user->first_name.' ('.$request->user->company_name.').',
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has added a new comment in task: '.$task['task'].'.',
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
    public function saveImage(request $request){
        $res = array();
        if(strlen($request->id)>10){
            $id = Task::where('off_id',$request->id)->first()->id;
        }else{
            $id = $request->id;
        }
        $task = Task::whereId($id)->first();
        if($request->hasFile('task_img')){
            $fileName = time().'task.'.$request->task_img->extension();
            $request->task_img->move(public_path('upload/img/'), $fileName);
            Task::whereId($id)->update(['task_img'=>$fileName]);
            Room_photo::where('room_id',$task['room_id'])->where('task_id',$id)->delete();
            Room_photo::create(['room_id'=>$task['room_id'],'user_id'=>$request->user->id,'task_id'=>$id,'img_name'=>$fileName ]);
        }
        $res['status'] = 'success';
        $res['id'] = $id;
        return response()->json($res);

    }
    public function addLabel(request $request){
        $res = array();
        Task_label::Create(['label'=>$request->label,'created_by'=>$request->user->id]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function deleteLabel(request $request){
        $res = array();
        Task_label::whereId($request->id)->delete();
        Task_label_value::where('label_id',$request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setTaskLabel(request $request){
        $id = $request->id;
        if(strlen($request->id) > 10){
            $id = Task::where('off_id',$request->id)->first()->id;
        }
        else
            $id = $request->id;
        Task_label_value::where('task_id',$id)->delete();
        $array_res = array();
        $array_res = json_decode($request->label_value,true);
        if ($array_res) {
            foreach($array_res as $row)
            {
                Task_label_value::create(['task_id'=>$id,'label_id'=>$row]);
            }
        }
        $res['status'] = 'success';
        return response()->json($res);

    }
    public function labelList(request $request){
        $res = array();
        $res['labels'] = Task_label::leftJoin('users','users.id','=','task_labels.created_by')
                                        ->select('task_labels.*','users.profile_pic','users.first_name','users.last_name')
                                        ->get();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function commentComplete(request $request){
        $res = array();
        TaskComment::where('id',$request->id)->update(['complete'=>$request->complete]);
        if ($request->complete == 1) {
            $notification = array();
            $task_comment = TaskComment::where('id', $request->id)->first();
            $project = Task::where('tasks.id', '=', $task_comment->task_id)
                ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                ->select('tasks.*', 'projects.project_name')->first();
            $text = $request->user->first_name . ' ' . $request->user->last_name . ' has completed task to do ' . $task_comment->comment . ' for project: ' . $project->project_name . ' for ';
            $notification['title'] = 'Sirvez | Completed To Do';
            $notification['text1'] = $text;
            $notification['text2'] = ' Deadline: ' . $task_comment->deadline . '.';
            $notification['id'] = 'task-comment-complete-' . $task_comment->task_id;
            $notification['image'] = $request->user->profile_pic;
            $users = Project_user::where('project_id', $task_comment->task_id)->where('type', '2')->pluck('user_id')->toArray();
            $users = array_map(function ($value) {
                return intval($value);
            }, $users);
            $notification['user_id'] = $users;
            $notification['created_by'] = $request->user->id;
            $notification['action_link'] = '/app/task-manager/my-tasks/' . $task_comment->task_id;

            broadcast(new NotificationEvent($notification))->toOthers();
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addCommentUser(request $request){
        $res = array();
        Task_comment_user::Create(['comment_id'=>$request->comment_id,'user_id'=>$request->user_id]);

        $res['status'] = 'success';
        return response()->json($res);
    }
    public function deleteCommentUser(request $request){
        $res = array();
        Task_comment_user::where(['comment_id'=>$request->comment_id,'user_id'=>$request->user_id])->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setDueByDate(request $request){
        $id = $request->id;
        if(strlen($id)>10){
            Task::where('off_id',$id)->update(['due_by_date'=>$request->due_by_date]);
        }
        else
            Task::whereId($id)->update(['due_by_date'=>$request->due_by_date]);
        $res["status"] = "success";
        return response()->json($res);
    }
    public function changeTopMenu(request $request){
        $id = $request->id;
        if(strlen($id)>10){
            Task_top_menu::where('off_id',$id)->update(['is_show'=>$request->is_show]);
        }
        else
            Task_top_menu::whereId($id)->update(['is_show'=>$request->is_show]);
        $res["status"] = "success";
        return response()->json($res);
    }
    public function modifyComment(request $request){
        $res = array();
        TaskComment::where(['id'=>$request->id])->update(['comment'=>$request->comment]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addNewBoard(request $request){
        $order_no = intval(TaskBoard::where('company_id',$request->company_id)->max('order_no'));
        $res = array();
        if ($request->board_id > 0) {
            TaskBoard::whereId($request->board_id)->update([
                'board_name' => $request->board_name
            ]);
        } else {
            TaskBoard::create([
                'board_name'=>$request->board_name,
                'company_id'=>$request->user->company_id,
                'created_by'=>$request->user->id,
                'order_no'=>$order_no+1,
            ]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addNewBucket(request $request){
        $order_no = intval(TaskBucket::where('board_id',$request->board_id)->max('order_no'));
        $res = array();
        TaskBucket::create([
            'board_id'=>$request->board_id,
            'bucket_name'=>$request->bucket_name,
            'created_by'=>$request->user->id,
            'order_no'=>$order_no+1,
        ]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function duplicateTask(request $request){
        $res = array();
        $task = Task::whereId($request->id)->first()->toArray();
        unset($task['id']);
        unset($task['created_at']);
        unset($task['updated_at']);
        unset($task['off_id']);
        $task['updated_by']=$request->user->id;
        $task['task'] = $task['task'].'_copy';
        $task = Task::create($task);
        $project_users = Project_user::where('project_id',$request->id)->where('type',2)->get();
        foreach($project_users as $row){
            $field['project_id'] = $task->id;
            $field['user_id'] = $row->user_id;
            $field['type'] = 2;
            Project_user::create($field);
        }
        $comments = TaskComment::where('task_id',$request->id)->get();
        foreach($comments as $row){
            $comment['task_id'] = $task->id;
            $comment['comment'] = $row->comment;
            $comment['deadline'] = $row->deadline;
            $comment['complete'] = $row->complete;
            $comment['created_by'] = $row->created_by;
            $comment['attach_file'] = $row->attach_file;
            $comment['file_size'] = $row->file_size;
            $comment['parent_id'] = $row->parent_id;
            $added_comment = TaskComment::create($comment);
            $comment_users = Task_comment_user::where('comment_id',$row->id)->get();
            foreach($comment_users as $user){
                $comment_user['comment_id'] = $added_comment->id;
                $comment_user['user_id'] = $user->user_id;
                Task_comment_user::create($comment_user);
            }

        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setBucket(request $request){
        $res = array();
        $order_no = 0;
        if($request->bucket_id>0)
        $order_no = intval(Task::where('bucket_id',$request->bucket)->max('order_no'));
        Task::whereId($request->id)->update([
            'board_id'=>$request->board_id,
            'bucket_id'=>$request->bucket_id,
            'order_no'=>$order_no+1
        ]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeBucketOrder(request $request){
        $res = array();
        $ordered_id = json_decode($request->ordered_id,true);
        foreach($ordered_id as $key=> $orderId)
        {
            TaskBucket::whereId($orderId)->update(['order_no'=>$key+1]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeTaskOrder(request $request){
        $res = array();
        $ordered_id = json_decode($request->ordered_id,true);
        foreach($ordered_id as $key=> $orderId)
        {
            Task::whereId($orderId)->update([
                'bucket_id' => $request->bucket_id,
                'order_no' => $key+1
            ]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function saveBucketName(request $request) {
        $res = array();
        if (TaskBucket::whereId($request->bucket_id)->count() > 0) {
            TaskBucket::whereId($request->bucket_id)->update([
                'bucket_name' => $request->bucket_name
            ]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function saveBucketLock(request $request) {
        $res = array();
        if (TaskBucket::whereId($request->bucket_id)->count() > 0) {
            TaskBucket::whereId($request->bucket_id)->update([
                'is_lock' => $request->is_lock
            ]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function deleteBucket(request $request) {
        $res = array();
        $res['status'] = 'success';
        if (TaskBucket::whereId($request->bucket_id)->count() > 0) {
            TaskBucket::whereId($request->bucket_id)->delete();
        }
        Task::where('bucket_id',$request->bucket)->update(['bucket_id'=>0]);
        return response()->json($res);
    }

    public function deleteBoard(request $request) {
        $res = array();
        $res['status'] = 'success';
        if (TaskBoard::whereId($request->board_id)->count() > 0) {
            TaskBoard::whereId($request->board_id)->delete();
        }
        $bucketIdx = TaskBucket::where('board_id',$request->board_id)->pluck('id');
        TaskBucket::where('board_id',$request->board_id)->delete();
        Task::whereIn('bucket_id',$bucketIdx)->orWhere('board_id',$request->board_id)->update(['board_id'=>0,'bucket_id'=>0]);
        return response()->json($res);
    }

    public function updateTaskProject(request $request) {
        $res = array();
        if (Task::whereId($request->task_id)->count() > 0) {
            Task::whereId($request->task_id)->update([
                'project_id' => $request->project_id
            ]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function updateTaskCustomer(request $request) {
        $res = array();
        if (Task::whereId($request->task_id)->count() > 0) {
            Task::whereId($request->task_id)->update([
                'company_id' => $request->customer_id
            ]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function updateTaskRoom(request $request) {
        $res = array();
        if (Task::whereId($request->task_id)->count() > 0) {
            Task::whereId($request->task_id)->update([
                'room_id' => $request->room_id
            ]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addAttachFile(request $request){
        $comment = TaskComment::whereId($request->id)->first();
        $file_name = $comment->attach_file;
        if($file_name){
            $file_path = public_path('upload/file/').'/'.$file_name;
            File::delete($file_path);
        }
        if($request->hasFile('attach_file')){
            $fileName = time().'comment.'.$request->attach_file->extension();
            $request->attach_file->move(public_path('upload/file/'), $fileName);
            $comment->attach_file = $fileName;
            $comment->file_size = number_format($request->attach_file->getSize()/1024,2);
            $comment->save();
        }
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeDeadline(request $request){
        TaskComment::whereId($request->id)->update(['deadline'=>$request->deadline]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function removeComment(request $request){
        TaskComment::whereId($request->id)->delete();
        Task_comment_user::where('comment_id',$request->id)->delete();
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
}

