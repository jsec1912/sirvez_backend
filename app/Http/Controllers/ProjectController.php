<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Project;
use App\Project_site;
use App\Site;
use App\Site_room;
use App\Task;
use App\TaskComment;
use App\Task_comment_user;
use App\Notification;
use App\User;
use App\Room;
use App\Room_photo;
use App\Company;
use App\Product;
use App\Company_customer;
use App\Project_user;
use App\ProjectModule;
use App\Schedule;
use App\ScheduleProduct;
use App\ScheduleEngineer;
use App\New_form;
use App\Form_field;
use App\Form_value;
use App\Room_comment;
use App\Version_control;
use App\Qr_option;
use App\Product_sign;
use App\Product_label;
use App\Task_label_value;
use App\Product_label_value;
use App\Partner;
use App\Calendar_event;
use App\CalendarEventSync;
use App\ProjectPage;
use App\ProjectTender;
use App\ProjectHealthy;
use App\ProjectTopMenu;
use App\Events\NotificationEvent;
use App\Events\ChatEvent;
use Mail;

class ProjectController extends Controller
{
    public function updateProject(Request $request){

        $v = Validator::make($request->all(), [
            //company info
            'customer_id' => 'required',
            'project_name' => 'required',
            'manager_id' => 'required',
            //'user_id' => 'required',
            //'contact_number' => 'required',
            'survey_start_date' => 'required',
            //'project_summary' => 'required'
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
        //$project['user_id']  = $request->user_id;
        $project['manager_id']  = $request->manager_id;
        $project['contact_number']  = $request->contact_number;
        $project['survey_start_date']  = $request->survey_start_date;
        $project['created_by']  = $request->user->id;
        $project['project_ref']  = $request->project_ref;
        $project['project_summary']  = $request->project_summary;
        $project['end_enable']  = $request->end_enable;
        if ($request->has('location_form_id') && $request->location_form_id) {
            $project['location_form_id'] = $request->location_form_id;
        } else {
            $project['location_form_id'] = 0;
        }
        if ($request->has('signoff_form_id') && $request->signoff_form_id) {
            $project['signoff_form_id'] = $request->signoff_form_id;
        } else {
            $project['signoff_form_id'] = 0;
        }
        $action = "updated";
        if(strlen($request->id) > 10)
            if(Project::where('off_id',$request->id)->count() > 0)
                $id = Project::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id=="" || $id=="null" || $id=="undefined"){
            if(strlen($request->id) > 10)
                $project['off_id'] = $request->id;
            // $project_cnt = Project::where('project_name',$project['project_name'])->count();
            // if($project_cnt > 0)
            // {
            //     $res['status'] = 'error';
            //     $res['msg'] = 'The project name is already exist!';
            //     return response()->json($res);
            // }
            $project = Project::create($project);
            $action = "created";
            $id = $project->id;

            if($request->has('customer_user'))
            {
                $array_res = array();
                $array_res =json_decode($request->customer_user,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>3]);
                }
            }

            if($request->has('assign_to'))
            {
                $array_res = array();
                $array_res =json_decode($request->assign_to,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>1]);
                    if(User::where('id',$row)->count()>0)
                        $client = User::where('id',$row)->first()->first_name;
                    else
                        $client = '';
                    $insertnotificationndata = array(
                        'notice_type'		=> '3',
                        'notice_id'			=> $id,
                        'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has added '.$client.' as a team member for a new project: '.$request->project_name.'.',
                        'created_by'		=> $request->user->id,
                        'company_id'		=> $project['company_id'],
                        'project_id'		=> $id,
                        'created_date'		=> date("Y-m-d H:i:s"),
                        'is_read'       	=> 0,
                    );
                    Notification::create($insertnotificationndata);
                }
            }

            $notification = array();
            $text = $request->user->first_name.' '.$request->user->last_name.' has added you to '.$project['project_name'] . ' for ';
            $notification['title'] = 'Sirvez | New Project';
            $notification['text1'] = $text;
            $notification['text2'] = '.';
            $notification['id'] = 'project-'. $id;
            $notification['image'] = $request->user->profile_pic;
            $users = Project_user::where('project_id', $id)->where('type', '!=', '2')->pluck('user_id')->toArray();
            $users = array_map(function ($value) {
                return intval($value);
            }, $users);
            $notification['user_id'] = $users;
            $notification['created_by'] = $request->user->id;
            $notification['action_link'] = '/app/project/live/' . $id;

            broadcast(new NotificationEvent($notification))->toOthers();

        } else {
            // $project_cnt = Project::where('project_name',$project['project_name'])->where('id','<>',$id)->count();
            // if($project_cnt > 0)
            // {
            //     $res['status'] = 'error';
            //     $res['msg'] = 'The project name is already exist!';
            //     return response()->json($res);
            // }
            Project::whereId($id)->update($project);

            if($request->has('customer_user'))
            {
                Project_user::where(['project_id'=>$id,'type'=>'3'])->delete();
                $array_res = array();
                $array_res =json_decode($request->customer_user,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>3]);
                    $users[] = $row;
                }
            }

            if($request->has('assign_to'))
            {
                Project_user::where(['project_id'=>$id,'type'=>'1'])->delete();
                $array_res = array();
                $array_res =json_decode($request->assign_to,true);
                foreach($array_res as $row)
                {
                    Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>1]);
                    $users[] = $row;
                }
            }
        }

        //$notice_type ={1:pending_user,2:createcustomer 3:project}
        if($request->customer_user){
            $customer_users = json_decode($request->customer_user, true);
            $customerUsers= User::whereIn('id',$customer_users)->get();
            $c_users = array();
            foreach($customerUsers as $item) {
                array_push($c_users, (string)$item->first_name.' '.(string)$item->last_name);
            }
            $client_name = implode(',',$c_users);
            //$customer_user = User::whereId($request->user_id)->first();
            //$client_name = $customer_user->first_name.' '.$customer_user->last_name;
        }else{
            $client_name = '';
        }
        if($client_name!=''){
            if($action =='created'){
                $text = $request->user->first_name.' '.$request->user->last_name.' has created a new project: '.$project['project_name'].' for '.$client_name.'.';
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $text,
                    'created_by'		=> $request->user->id,
                    'company_id'		=> $project['company_id'],
                    'project_id'		=> $id,
                    'created_date'		=> date("Y-m-d H:i:s"),
                    'is_read'	    	=> 0,
                );
            }
            else{
                $text = $request->user->first_name.' '.$request->user->last_name.' has updated project : '.$project['project_name'].' for '.$client_name.'.';
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $text,
                    'created_by'		=> $request->user->id,
                    'company_id'		=> $project['company_id'],
                    'project_id'		=> $id,
                    'created_date'		=> date("Y-m-d H:i:s"),
                    'is_read'	    	=> 0,
                );
            }
        }
        else{
            if($action =='created'){
                $text = $request->user->first_name.' '.$request->user->last_name.' has created a new project: '.$project['project_name'].'.';
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $text,
                    'created_by'		=> $request->user->id,
                    'company_id'		=> $project['company_id'],
                    'project_id'		=> $id,
                    'created_date'		=> date("Y-m-d H:i:s"),
                    'is_read'	    	=> 0,
                );
            }
            else{
                $text = $request->user->first_name.' '.$request->user->last_name.' has updated project : '.$project['project_name'].'.';
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $text,
                    'created_by'		=> $request->user->id,
                    'company_id'		=> $project['company_id'],
                    'project_id'		=> $id,
                    'created_date'		=> date("Y-m-d H:i:s"),
                    'is_read'	    	=> 0,
                );
            }
        }
        Notification::create($insertnotificationndata);

        //sending gmail to user
        if($request->has('cusotmer_user'))
        {
            $array_res = array();
            $array_res =json_decode($request->cusotmer_user,true);
            foreach($array_res as $row){
                $pending_user = User::where('id',$row)->first();
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $content = $request->user->first_name.' has been '.$action.' project as '.$request->project_name;
                $invitationURL = "https://app.sirvez.com/app/project/live/".$id;
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$project['project_name'],"description" =>$project['project_summary'],"img"=>'',"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view project'];
                Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','sirvez support team');
                });
            }
        }

        $response = ['status'=>'success', 'msg'=>'Project Saved Successfully!'];
        return response()->json($response);
    }
    public function deleteProject(Request $request)
    {
        $id = $request->id;
        if(strlen($request->id)>10)
            $id = Project::where('off_id',$request->id)->first()->id;
        Project::whereId($id)->update(['archived'=>1,'archived_day'=>date('Y-m-d'),'archived_id'=>$request->user->id]);
        $project = Project::whereId($id)->first();
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            //'notification'		=> 'Project '.$project['project_name'].' have been completed by  '.$request->user->first_name.' '.$request->user->last_name.' ('.$request->user->company_name.').',
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has completed project: '.$project['project_name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $request->user->company_id,
            'project_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);

        $array_res =Project_user::where('project_id',$request->id)->whereIn('type',[1,3])->pluck('user_id');
        $users = User::whereIn('id',$array_res)->get();
        foreach($users as $pending_user){
            $to_name = $pending_user['first_name'];
            $to_email = $pending_user['email'];
            $content = 'Project('.$project->project_name.') was been archived by '.$request->user->first_name.' on ['.date("d-m-Y H:i:s")."].";
            $invitationURL = "https://app.sirvez.com/app/project/live/".$id;
            $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$project['project_name'],"description" =>$project['project_summary'],"img"=>'',"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view project'];
            Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez support team');
            });
        }

         $res["status"] = "success";
        return response()->json($res);
    }
    public function projectList(Request $request){
        $res = array();
        $partner_ids = array();

        if($request->user->user_type == 0){
            $project_array = Project::leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
            $res['customers'] = Company::get();
            $res['users'] = User::get();
        } else if ($request->user->user_type < 2) {

            $id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $partner_ids = Project_user::where([
                'user_id' => $request->user->id,
                'type' => 4
            ])->pluck('project_id');

            $project_array = Project::where(function ($q) use($id, $partner_ids) {
                return $q->whereIn('projects.company_id',$id)
                ->orWhereIn('projects.id', $partner_ids);
            })
            ->leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
            $res['customers'] = Company::whereIn('id',$id)->orwhere('id',$request->user->company_id)->get();
            $res['users'] = User::whereIn('company_id',$id)->orwhere('company_id',$request->user->company_id)->get();
        } else if ($request->user->user_type < 4) {
            
            $id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $partner_ids = Project_user::whereIn('type',[1,4])
                            ->where('user_id', $request->user->id)
                            ->pluck('project_id');

            $project_array = Project::where('manager_id',$request->user->id)
                ->orWhereIn('projects.id', $partner_ids)
                ->leftJoin('companies','companies.id','=','projects.company_id')
                ->leftJoin('users','users.id','=','projects.manager_id')
                ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
            $res['customers'] = Company::whereIn('id',$id)->orwhere('id',$request->user->company_id)->get();
            $res['users'] = User::whereIn('company_id',$id)->orwhere('company_id',$request->user->company_id)->get();
        }
         else {
            $projectIdx = Project_user::where('user_id', $request->user->id)
            ->where(function ($q) {
                return $q->where('type', 3) // team member case
                ->orWhere('type', 4); // partner user case
            })
            ->pluck('project_id');
            $project_array = Project::whereIn('projects.id',$projectIdx)
            ->leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
        }

        foreach($project_array as $key => $row){
            //$project_array[$key]['survey_start_date'] = date('d-m-Y', strtotime($row['survey_start_date']));
            $project_array[$key]['site_count'] = Project_site::where('project_id',$row['id'])->count();
            $room_idx = Room::where('project_id',$row['id'])->pluck('id');
            $project_array[$key]['product_count'] = Product::whereIn('room_id',$room_idx)->count();
            $project_array[$key]['room_count'] = Room::where('project_id',$row['id'])->count();
            $project_array[$key]['messages'] = Notification::where('notice_type','3')->where('notice_id',$row['id'])->count();
            ///////  partner part
            $partner_users = Project_user::where([
                'project_id' => $row['id'],
                'type' => '4'
            ])->pluck('user_id');
            if (in_array($request->user->id, $partner_users->toArray())) {
                $project_array[$key]['is_partner'] = 1;
            } else {
                $project_array[$key]['is_partner'] = 0;
            }
            if ($project_array[$key]['is_partner'] == 1){
                
                $companyIds = Company_customer::where('customer_id',$row['company_id'])->pluck('company_id');
                if(count($companyIds)>0)
                    $project_array[$key]['partner_logos'] = Company::whereIn('id', $companyIds)->get();
                else
                    $project_array[$key]['partner_logos'] = Company::where('id',$row['company_id'])->get();
            }else{
                $partner_company_ids = User::whereIn('id', $partner_users)->pluck('company_id');
                $project_array[$key]['partner_logos'] = Company::whereIn('id', $partner_company_ids)->get();
            }
        }
        $res['top_memus_value'] = ProjectTopMenu::get();
        $res["projects"] = $project_array;
        $res['status'] = "success";
        return response()->json($res);
    }

    public function project_check($project, $request) {
        if (!$project) {
            return 0;
        }
        if ($request->user->user_type == 0) {
            return 1; // super super admin
        } else if ($request->user->user_type < 4) {
            // case : user admin
            $company_ids = Company_customer::where('company_id',$request->user->company_id)
            ->pluck('customer_id')->toArray();
            array_push($company_ids, $request->user->company_id);
            if (in_array($project['company_id'], $company_ids)) {
                return 2; // super admin
            }
        }
        
        $partner_ids = Project_user::where([
            'user_id' => $request->user->id,
            'type' => 3
        ])->pluck('project_id');

        if (in_array($project['id'], $partner_ids->toArray())) {
            return 3; // end user
        }

        $partner_ids = Project_user::where([
            'user_id' => $request->user->id,
            'type' => 4
        ])->pluck('project_id');
        
        if (in_array($project['id'], $partner_ids->toArray())) {
            return 4; // partner user
        }
        return 0;
    }

    public function projectInfo(Request $request){
        $res = array();
        $res['status'] = "success";
        if($request->project_id){
        
            if(strlen($request->project_id) > 10)
                $id = Project::where('off_id',$request->project_id)->first()->id;
            else
                $id = $request->project_id;
            $project = Project::where('projects.id',$id)
                ->leftJoin('companies','projects.company_id','=','companies.id')
                ->leftJoin('users','users.id','=','projects.created_by')
                ->select('projects.*','companies.logo_img','companies.name AS company_name','companies.website AS company_website','companies.telephone AS company_phone','users.first_name')->first();
            
            /////  check permission
            if ($this->project_check($project, $request) == 0) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You do not have permission to view this project.'
                ]);
            }
            /////  end check permission

            
            //$project['survey_start_date'] = date('d-m-Y', strtotime($project['survey_start_date']));
            $res['notification'] = Notification::where('notice_type','6')->where('notice_id',$id)->orderBy('id','desc')->get();
            $project['site_count'] = Project_site::where('project_id',$project['id'])->count();
            $project['room_count'] = Room::where('project_id',$project['id'])->count();
            $project['user_notifications'] = Notification::where('notice_type','3')->where('notice_id',$id)->count();
            if(User::where('id',$project['archived_id'])->count()>0)
                $project['archived_name'] = User::where('id',$project['archived_id'])->first()->first_name;
            else
                $project['archived_name'] = '';
            $project_users = Project_user::where('project_id', $id)->where('type', '!=', '2')->pluck('user_id')->toArray();
            array_push($project_users,strval($project->manager_id));
            $project['project_users'] = $project_users;
            
            if(Company_customer::where('customer_id',$project->company_id)->count()>0)
                $companyId = Company_customer::where('customer_id',$project->company_id)->first()->company_id;
            else
                $companyId = '';

            $res['module_status'] = array();
            $res['module_lock'] = array();
            if(ProjectModule::where(['user_id'=>$request->user->id,'project_id'=>$id])->count()>0){
                $res['module_status'] = ProjectModule::where(['user_id'=>$request->user->id,'project_id'=>$id])->first();
                if ($request->user->user_type < 4) {
                    if (ProjectModule::where(['company_id' => $request->user->company_id])->count() > 0) {
                        $res['module_lock'] = ProjectModule::where([
                            'company_id' => $request->user->company_id
                        ])->first();
                    }
                } else {
                    if (ProjectModule::where(['company_id' => $companyId])->count() > 0) {
                        $res['module_lock'] = ProjectModule::where([
                            'company_id' => $companyId
                        ])->first();
                    }
                }
            } else {
                if ($request->user->user_type < 4) {
                    //admin
                    if(ProjectModule::where(['company_id' => $request->user->company_id])->count() > 0)
                        $res['module_status'] = ProjectModule::where([
                            'company_id' => $request->user->company_id
                        ])->first();

                } else {
                    // end user
                    if(ProjectModule::where(['company_id' => $companyId])->count()>0) {
                        $res['module_status'] = ProjectModule::where([
                            'company_id' => $companyId
                        ])->first();
                        $res['module_lock'] = $res['module_status'];
                    }
                }
            }

            $company_ids = Company_customer::where('company_id',$request->user->company_id)
            ->pluck('customer_id')->toArray();
            array_push($company_ids, $request->user->company_id);

            $partner_ids = Partner::where('company_id', $companyId)
                ->pluck('partner_id')->toArray();
            $allow_partners = User::where('status', 1)
                ->whereIn('company_id', $partner_ids)
                ->pluck('id')->toArray();
            $allow_teams = User::where('company_id', $companyId)
                ->where('status', 1)->whereIn('user_type', [0, 1, 3])
                ->pluck('id')->toArray();
            $allow_customers = User::where('company_id', $project->company_id)
                ->where('status', 1)->whereIn('user_type', [5, 6])
                ->pluck('id')->toArray();

            $res['allow_users'] = User::whereIn('users.id', $allow_partners)
                ->orWhereIn('users.id', $allow_teams)
                ->orWhereIn('users.id', $allow_customers)
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
                ->select('users.*', 'companies.name as company_name')
                ->get();
            foreach($res['allow_users'] as $key => $user) {
                if (in_array($user['id'], $allow_partners)) {
                    $res['allow_users'][$key]['type'] = 4;
                } else if (in_array($user['id'], $allow_teams)) {
                    $res['allow_users'][$key]['type'] = 1;
                } else {
                    $res['allow_users'][$key]['type'] = 3;
                }
            }

            $partner_users = Project_user::where([
                'project_id' => $id,
                'type' => '4'
            ])->pluck('user_id');

            if (in_array($request->user->id, $partner_users->toArray())) {
                $project['is_partner'] = 1;
            } else {
                $project['is_partner'] = 0;
            }
            if ($project['is_partner'] == 1){
                
                $companyIds = Company_customer::where('customer_id',$project['company_id'])->pluck('company_id');
                if(count($companyIds)>0)
                    $project['partner_logos'] = Company::whereIn('id', $companyIds)->get();
                else
                    $project['partner_logos'] = Company::where('id',$project['company_id'])->get();
            }else{
                $partner_company_ids = User::whereIn('id', $partner_users)->pluck('company_id');
                $project['partner_logos'] = Company::whereIn('id', $partner_company_ids)->get();
            }
            $owner_company = Company_customer::where('customer_id',$project['company_id'])->first();
            if($owner_company)
                $project['owner'] = Company::whereId($owner_company->company_id)->first();
            else
                $project['owner'] = Company::whereId($project->company_id)->first();
            $res["project"] = $project;

            $res['sites'] = Site::where('company_id',$project->company_id)->orWhere('company_id',$companyId)->orderBy('id','desc')->get();
            $rooms = Room::where('rooms.project_id',$id)
                ->leftJoin('sites','rooms.site_id','=','sites.id')
                ->leftJoin('companies','rooms.company_id','=','companies.id')
                ->leftJoin('buildings','rooms.building_id','=','buildings.id')
                ->select('rooms.*','sites.site_name','companies.name as company_name','buildings.building_name')
                ->orderBy('order_no')->get();
            foreach($rooms as $key => $room)
            {
                $rooms[$key]['products'] = Product::where('room_id',$room->id)->count();
                $rooms[$key]['total_tasks'] = Task::where('room_id',$room->id)->count();
                $rooms[$key]['complete_tasks'] =Task::where('room_id',$room->id)->where('archived',1)->count();
                $images = Room_photo::where('room_id',$room->id)->get();
                foreach($images as $k => $image){
                    $images[$k]['comments'] = Room_comment::where('photo_id',$image->id) 
                        ->leftJoin('users','users.id','=','room_comments.created_by')
                        ->select('room_comments.*','users.first_name','users.last_name','users.profile_pic')
                        ->get();
                }
                $rooms[$key]['img_files'] = $images;
            }
            $res['rooms'] = $rooms;
            $room_ids = Room::where('project_id',$id)->pluck('id');
            $schedules = Schedule::whereIn('schedules.room_id',$room_ids)
                        ->leftJoin('sites','sites.id','=','schedules.site_id')
                        ->leftJoin('rooms','rooms.id','=','schedules.room_id')
                        ->orderBy('schedules.root_id')
                        ->select('schedules.*','sites.site_name','rooms.room_number')
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
            
            $tasks = Task::where('project_id',$id)->orderBy('id','desc')->get();
            foreach($tasks as $key=>$row){
                $tasks[$key]['assign_to'] = Project_user::leftjoin('users','users.id','=','project_users.user_id')
                    ->where(['project_users.project_id'=>$row->id,'type'=>'2'])
                    ->pluck('users.first_name');
            }
            $res['tasks'] = $tasks;
            $versions = Version_control::whereIn('version_controls.room_id',$room_ids)
                                        ->leftJoin('rooms','rooms.id','=','version_controls.room_id')
                                        ->leftJoin('users','users.id','=','version_controls.created_by')
                                        ->select('version_controls.*','rooms.room_number','users.profile_pic','users.first_name','users.last_name')
                                        ->orderBy('version_controls.group_id','asc')
                                        ->get();
            foreach($versions as $key => $version)
            {
                if($version['tag'] ==0)
                    $versions[$key]['version_tag'] = "Drawing";
                else if($version['tag'] ==1)
                    $versions[$key]['version_tag'] = "Document";
                else
                    $versions[$key]['version_tag'] = "SpreadSheet";
            }
            $res['versions'] = $versions;
            $products = Product::whereIn('room_id',$room_ids)
                                ->orWhere(function($q) use($id){
                                    return $q->where('project_id',$id)
                                        ->where('action',3);
                                    })
                                ->get();
            foreach($products as $key => $product)
            {
                if(Room::whereId($product->room_id)->count()>0){
                    $products[$key]['room_name'] = Room::whereId($product->room_id)->first()->room_number;
                    $products[$key]['project_id'] = Room::whereId($product->room_id)->first()->project_id;
                }
                else
                    $products[$key]['room_name'] = '';
                $products[$key]['signoff_user'] =User::whereId($product->signoff_by)->first();
                $products[$key]['test_signoff_user'] =User::whereId($product->test_signoff_by)->first();
                $products[$key]['com_signoff_user'] =User::whereId($product->com_signoff_by)->first();
                $products[$key]['company_info'] = Company::whereId($project->company_id)->first();
                $products[$key]['website'] = Company::whereId($project->company_id)->first()->website;
                $products[$key]['company_name'] = Company::whereId($project->company_id)->first()->name;
                $products[$key]['sign_in'] = Product_sign::where('product_signs.product_id',$product->id)
                                                        ->leftJoin('users','users.id','=','product_signs.user_id')
                                                        ->select('product_signs.*','users.first_name','users.profile_pic')
                                                        ->get();
                $products[$key]['label_value'] = Product_label_value::where('product_id',$product->id)->pluck('label_id');
                $products[$key]['install_date'] = date('d-m-Y',strtotime($project['survey_start_date']));

                if($product['action'] ==0)
                    $products[$key]['product_action'] = "New Product";
                else if($product['action'] ==1)
                    $products[$key]['product_action'] = "Dispose";
                else
                    $products[$key]['product_action'] = "Move To Room";
            }
            $res['products'] = $products;
            $assignId = Project_user::where(['project_id'=>$id,'type'=>1])->pluck('user_id');
            $com_id = Company_customer::where('customer_id',$project->company_id)->first()->company_id;
            $assignId = Project_user::where(['project_id'=>$id,'type'=>3])->pluck('user_id');
            $res['engineers'] = User::where('company_id',$com_id)->where('user_type',2)->where('status',1)->select('id','first_name','last_name','profile_pic')->get();
            $res['task_assign_to'] = User::where('company_id',$com_id)->whereIn('user_type',[0,1,3])->where('status',1)->select('id','first_name','last_name','profile_pic')->get();
            $res['signed_cnt'] = Room::where('project_id',$id)->where('signed_off','<>','2')->count()-Room::where('project_id',$id)->where('signed_off','1')->count();
            $res['unsigned_room'] = Room::where('project_id',$id)->where('signed_off','0')->get();
            
            $res['customer_userlist'] = User::whereIn('user_type',[5,6])->where('status',1)->where('company_id',$project->company_id)->select('id','first_name','last_name')->get();
            $res['off_form_values'] = Form_value::where('form_type', 3)
                ->where('parent_id',$request->project_id)->get();
            $productIds = Product::whereIn('room_id',$room_ids)
            ->orWhere(function($q) use($id){
                return $q->where('project_id',$id)
                    ->where('action',3);
                })
            ->pluck('id');
            $labelIds = Product_label_value::whereIn('product_id',$productIds)->pluck('label_id');
            $res['product_used_labels'] = Product_label::whereIn('id',$labelIds)->get();
            $res['project_id'] = $id;
            $res['project_pages'] = ProjectPage::where('project_id',$id)->orderBy('order_no')->get();
            $res['project_tenders'] = ProjectTender::where('project_id',$id)->orderBy('order_no')->get();
            $res['project_healthys'] = ProjectHealthy::where('project_id',$id)->orderBy('order_no')->get();
        } else {
            $user_company_id = $request->user->company_id;
            if (Company_customer::where('customer_id', $user_company_id)->count() > 0) {
                $user_company_id = Company_customer::where('customer_id', $user_company_id)->first()->company_id;
            }
            $customerIds = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id')->toArray();
            array_push($customerIds,intval($request->user->company_id));
            $partner_ids = Partner::where('company_id', $user_company_id)
                ->pluck('partner_id')->toArray();
            $allow_partners = User::where('status', 1)
                ->whereIn('company_id', $partner_ids)->pluck('id')->toArray();
            $allow_teams = User::where('company_id', $user_company_id)
                ->where('status', 1)->whereIn('user_type', [0, 1, 3])->pluck('id')->toArray();
            $allow_customers = User::whereIn('company_id', $customerIds)
                ->where('status', 1)->whereIn('user_type', [5, 6])->pluck('id')->toArray();

            $res['allow_users'] = User::whereIn('users.id', $allow_partners)
                ->orWhereIn('users.id', $allow_teams)
                ->orWhereIn('users.id', $allow_customers)
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
                ->select('users.*', 'companies.name as company_name')
                ->get();
                
            foreach($res['allow_users'] as $key => $user) {
                if (in_array($user['id'], $allow_partners)) {
                    $res['allow_users'][$key]['type'] = 4;
                } else if (in_array($user['id'], $allow_teams)) {
                    $res['allow_users'][$key]['type'] = 1;
                } else {
                    $res['allow_users'][$key]['type'] = 3;
                }
            }
        }
        
        if($request->user->user_type <=3){
            $company_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['customers'] = Company::whereIn('id',$company_id)->orderBy('id','desc')->get();
            $res['account_managers'] = User::whereIn('user_type',[0,1,3])
                                ->where('status',1)
                                ->where(function($q) use($company_id,$request){
                                    return $q->whereIn('company_id',$company_id)
                                        ->orwhere('company_id', $request->user->company_id);
                                    })
                                ->get();
            $res['client_users'] = User::whereIn('user_type',[5,6])->where('status',1)->whereIn('company_id',$company_id)->get();
            $res['assign_users'] = User::where(function($q) use($company_id,$request){
                        return $q->whereIn('company_id',$company_id)
                            ->orwhere('company_id', $request->user->company_id);
                        })
                        ->whereIn('user_type',[0,1,5])->where('status',1)->get();
        } else {
            $res['customers'] = Company::where('id',$request->user->company_id)->orderBy('id','desc')->get();
            $res['account_managers'] = User::whereIn('user_type',[0,1,3])->where('status',1)->where('company_id',$request->user->company_id)->get();
            $res['client_users'] = User::whereIn('user_type',[5,6])->where('status',1)->where('company_id',$request->user->company_id)->get();
            $res['assign_users'] = User::where('company_id',$request->user->company_id)->whereIn('user_type',[0,1,3])->where('status',1)->get();
        }
        $user_company_id = $request->user->company_id;
        if (Company_customer::where('customer_id', $user_company_id)->count() > 0) {
            $user_company_id = Company_customer::where('customer_id', $user_company_id)->first()->company_id;
        }
        $res['form_fields'] = Form_field::get();
        $res['location_form_rows'] = New_form::where('created_by',$user_company_id)->where('form_type',0)->get();
        $res['signoff_form_rows'] = New_form::where('created_by',$user_company_id)->where('form_type',3)->get();
        $res['test_forms'] = New_form::where('created_by',$user_company_id)->where('form_type', 1)->get();
        $res['com_forms'] = New_form::where('created_by',$user_company_id)->where('form_type', 2)->get();
        $res['product_labels'] = Product_label::get();
        $res['qr_option'] = Qr_option::first();
        // for offline mode
        $res['all_users'] = User::where('status',1)->get();
        if ( ! $request->project_id) {
            $res['company_customers'] = Company_customer::get();
            $res['all_sites'] = Site::get();
            $res['all_site_rooms'] = Site_room::get();
            $res['all_partners'] = Partner::get();
        }
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
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        Project_user::where('project_id',$id)->where('user_id',$request->user_id)->where('type',1)->delete();
        $project = Project::where('id',$id)->first();
        $remove_user = User::where('id',$request->user_id)->first();
        $client = $remove_user->first_name.' '.$remove_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has removed '.$client.' from the team member of project: '.$project['project_name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $project['company_id'],
            'project_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);

        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addAssignUser(request $request)
    {
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        Project_user::create(['user_id'=>$request->user_id,'project_id'=>$id,'type'=>1]);
        $project = Project::where('id',$id)->first();
        $add_user = User::where('id',$request->user_id)->first();
        $client = $add_user->first_name.' '.$add_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has added '.$client.' as a team member for project: '.$project['project_name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $project['company_id'],
            'project_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function deleteProjectUser(request $request)
    {
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        Project_user::where('project_id',$id)->where('user_id',$request->user_id)->where('type',$request->user_type)->delete();
        $project = Project::where('id',$id)->first();
        $remove_user = User::where('id',$request->user_id)->first();
        $client = $remove_user->first_name.' '.$remove_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has removed '.$client.' from ' . ($request->user_type == 1 ? 'team member' : ($request->user_type == 3 ? 'customer user' : 'partner user')) . ' of project: '.$project['project_name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $project['company_id'],
            'project_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);

        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addProjectUser(request $request)
    {
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        Project_user::create([
            'user_id' => $request->user_id,
            'project_id' => $id,
            'type' => $request->user_type
        ]);
        $project = Project::where('id',$id)->first();
        $add_user = User::where('id',$request->user_id)->first();
        $client = $add_user->first_name.' '.$add_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has added '.$client.' as a ' . ($request->user_type == 1 ? 'team member' : ($request->user_type == 3 ? 'customer user' : 'partner user')) . ' for a new project: '.$project['project_name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $project['company_id'],
            'project_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function signOff(request $request)
    {
        
        $fileName = '';
        $project = array();

        if ($request->user->user_type ==6)
            $project['signed_off'] = 2;
        else
            $project['signed_off'] = 1;
        $project['signoff_date'] = date("Y-m-d H:i:s");
        if($request->hasFile('sign_file')){
            $fileName = time().'.'.$request->sign_file->extension();
            $request->sign_file->move(public_path('upload/file/'), $fileName);
            $project['sign_file']  = $fileName;
        }
        $project['signoff_user'] = $request->user->id;
        $project['signoff_form_id'] = $request->signoff_form;

        if($request->sign_user_id) $project['sign_user_id'] = $request->sign_user_id;
        if($request->sign_first_name) $project['sign_first_name'] = $request->sign_first_name;
        if($request->sign_last_name) $project['sign_last_name'] = $request->sign_last_name;
        if($request->sign_contact_email) $project['sign_contact_email'] = $request->sign_contact_email;
        if($request->sign_contact_number) $project['sign_contact_number'] = $request->sign_contact_number;
        if($request->sign_parking) $project['sign_parking'] = $request->sign_parking;
        if($request->sign_ram_require) $project['sign_ram_require'] = $request->sign_ram_require;
        if($request->sign_comments) $project['sign_comments'] = $request->sign_comments;
        if($request->sign_print_name) $project['sign_print_name'] = $request->sign_print_name;

        Project::whereId($request->id)->update($project);
        
        if($request->field_values){
            $values = array();
            $values = json_decode($request->field_values,false);
            $value = array();
            foreach($values as $row){
                $value['field_name'] = $row->field_name;
                $value['field_type'] = $row->field_type;
                $value['field_label'] = $row->field_label;
                $value['new_form_id'] = $row->new_form_id;
                $value['field_value'] = $row->field_value;
                $value['is_checked'] = $row->is_checked;
                $value['form_type'] = $row->form_type;
                $value['parent_id'] = $request->id;
                $value['is_final'] = 0;
                $cnt = Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('is_final',0)
                                ->where('parent_id',$request->id)->count();
                if($cnt>0)
                    Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$request->id)
                                ->where('is_final',0)
                                ->update(['field_value'=>$row->field_value,'is_checked'=>$row->is_checked]);
                else
                    Form_value::create($value);
            }
        }
        
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
                'notification'		=> $request->user->first_name.' '.$request->user->last_name." has requested sign off on ".date("d-m-Y H:i:s").".",
                'created_by'		=> $request->user->id,
                'company_id'		=> $project['company_id'],
                'project_id'		=> $project->id,
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
                'is_signed'	    	=> 0,
            );
        else
            $insertnotificationdata = array(
                'notice_type'		=> '6',
                'notice_id'			=> $request->id,
                'notification'		=> $request->user->first_name.' '.$request->user->last_name." has signed off scope of works for ".$project['project_name']." on ".date("d-m-Y H:i:s").".",
                'created_by'		=> $request->user->id,
                'company_id'		=> $project['company_id'],
                'project_id'		=> $project->id,
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
                'is_signed'	    	=> 1,
            );
        Notification::create($insertnotificationdata);

        $notification = array();
        if ($request->user->user_type == 6) {
            $text = $request->user->first_name.' '.$request->user->last_name.' has signed off scope of works: '. $project['project_name'] . ' for ';
            $notification['title'] = 'Sirvez | Project signed off (SOW)';
            $notification['id'] = 'project-signoff-'. $project->id;
        } else {
            $text = $request->user->first_name.' '.$request->user->last_name.' has request scope of work sign off: '. $project['project_name'] . ' for ';
            $notification['title'] = 'Sirvez | Project sign off (SOW)';
            $notification['id'] = 'project-signoff-request-'. $project->id;
        }

        $notification['text1'] = $text;
        $notification['text2'] = '.';
        $notification['image'] = $request->user->profile_pic;
        $users = Project_user::where('project_id', $project->id)->where('type', '!=', '2')->pluck('user_id')->toArray();
        $users = array_map(function ($value) {
            return intval($value);
        }, $users);
        array_push($users, intval($project['created_by']));
        $notification['user_id'] = $users;
        $notification['created_by'] = $request->user->id;
        $notification['action_link'] = '/app/project/live/' . $project->id;

        broadcast(new NotificationEvent($notification))->toOthers();

        //send mail
        if(Project_user::where('project_id',$request->id)->where('type','3')->count() > 0){
            $content = "";
            if($request->user->user_type <6){
                $customer_users = Project_user::where('project_id',$request->id)->where('type','3')->pluck('user_id');
                foreach($customer_users as $row){
                    $pending_user = User::where('id',$row)->first();
                    //$pending_user = $project['customer_user'];
                    $content = $request->user->first_name. " would like you to sign off the scope of works for ".$project['project_name'];
                    $to_name = $pending_user['first_name'];
                    $to_email = $pending_user['email'];
                    $Link_pdf = 'https://app.sirvez.com/upload/file/'.$project['sign_file'];
                    $data = ['name'=>$pending_user['first_name'], "content" => $content,"project"=>$project,"Link_pdf"=>$Link_pdf];
                    Mail::send('projectSign', $data, function($message) use ($to_name, $to_email,$project) {
                        $message->to($to_email, $to_name)
                                ->subject('sirvez notification.');
                        $message->from('support@sirvez.com','support team');
                    });
                }

            }
            else{
                $pending_user = User::where('id',$project->created_by)->first();
                $content = "Project was signed off by ".$request->user->first_name.". ".date("d-m-Y H:i:s");
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $Link_pdf = 'https://app.sirvez.com/upload/file/'.$project['sign_file'];
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"project"=>$project,"Link_pdf"=>$Link_pdf];
                Mail::send('projectSign', $data, function($message) use ($to_name, $to_email,$project) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','support team');
                });
            }
        }

        //invite user
        if($request->signoff_form==0){
            if($request->user->user_type ==6){
                if ($request->sign_user_id>0){
                    $pending_user = User::whereId($request->sign_user_id)->first();
                    $to_name = $pending_user['first_name'];
                    $to_email = $pending_user['email'];
                    $content = 'you have been added as a site contact for '.$project['project name'].' Please view details here.. ';
                    $Link_pdf = 'https://app.sirvez.com/upload/file/'.$project['sign_file'];
                    $data = ['name'=>$pending_user['first_name'], "content" => $content,"project"=>$project,"Link_pdf"=>$Link_pdf];
                    Mail::send('projectSign', $data, function($message) use ($to_name, $to_email,$project) {
                        $message->to($to_email, $to_name)
                                ->subject('sirvez notification.');
                        $message->from('support@sirvez.com','support team');
                    });
    
                }
                else{
                    $company_name = Company::whereId($project['company_id'])->first()->name;
                    $user = array();
                    $user['email'] = $project['sign_contact_email'];
                    $user['first_name'] = $project['sign_first_name'];
                    $user['user_type'] = 6;
                    $user['company_id'] = $project['company_id'];
                    $user['company_name'] = $company_name;
                    $invite_code = bcrypt($user['email'].$company_name);
                    $user['invite_code'] = str_replace('/', '___', $invite_code);
                    $user['status'] = '0';
    
                    $user = User::create($user);
                    $invitationURL = env('APP_URL')."/company/usersignup/".$user['invite_code'];
    
                    //sending gmail to user
                    $to_name = $user['first_name'];
                    $to_email = $user['email'];
                    $data = ['name'=>$user['first_name'], "pending_user" => $user,'user_info'=>$request->user,'invitationURL'=>$invitationURL];
                    Mail::send('mail', $data, function($message) use ($to_name, $to_email) {
                        $message->to($to_email, $to_name)
                                ->subject('sirvez support team invite you. please join our site.');
                        $message->from('support@sirvez.com','support team');
                    });
                }
            }
        }
        
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function finalSignOff(request $request)
    {
        
        $fileName = '';
        $project = array();
        if ($request->user->user_type ==6)
            $project['final_signoff'] = 2;
        else
            $project['final_signoff'] = 1;
        if($request->hasFile('sign_file')){
            $fileName = time().'.'.$request->sign_file->extension();
            $request->sign_file->move(public_path('upload/file/'), $fileName);
            $project['final_sign_file']  = $fileName;
        }
        if($request->hasFile('sign_xlsx')){
            $fileXlsx = time().'.'.$request->sign_xlsx->extension();
            $request->sign_xlsx->move(public_path('upload/file/'), $fileXlsx);
            $project['final_sign_xlsx']  = $fileXlsx;
        }
        $project['final_signoff_date'] = date("Y-m-d H:i:s");
        $project['final_signoff_user'] = $request->user->id;
        $project['final_signoff_form_id']= $request->signoff_form;

        Project::whereId($request->id)->update($project);
        
        if($request->field_values){
            $values = array();
            $values = json_decode($request->field_values,false);
            $value = array();
            foreach($values as $row){
                $value['field_name'] = $row->field_name;
                $value['field_type'] = $row->field_type;
                $value['field_label'] = $row->field_label;
                $value['new_form_id'] = $row->new_form_id;
                $value['field_value'] = $row->field_value;
                $value['is_checked'] = $row->is_checked;
                $value['form_type'] = $row->form_type;
                $value['parent_id'] = $request->id;
                $value['is_final'] = 1;
                $cnt = Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('is_final',0)
                                ->where('parent_id',$request->id)->count();
                if($cnt>0)
                    Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$request->id)
                                ->where('new_form_id',$row->new_form_id)
                                ->update(['field_value'=>$row->field_value,'is_checked'=>$row->is_checked]);
                else
                    Form_value::create($value);
            }
        }
        
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
                'notification'		=> $request->user->first_name.' '.$request->user->last_name." has requested final sign off on ".date("d-m-Y H:i:s").".",
                'created_by'		=> $request->user->id,
                'company_id'		=> $project['company_id'],
                'project_id'		=> $project->id,
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
                'is_signed'	    	=> 0,
            );
        else
            $insertnotificationdata = array(
                'notice_type'		=> '6',
                'notice_id'			=> $request->id,
                'notification'		=> $request->user->first_name.' '.$request->user->last_name." has final signed off scope of works for ".$project['project_name']." on ".date("d-m-Y H:i:s").".",
                'created_by'		=> $request->user->id,
                'company_id'		=> $project['company_id'],
                'project_id'		=> $project->id,
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
                'is_signed'	    	=> 1,
            );
        Notification::create($insertnotificationdata);

        $notification = array();
        if ($request->user->user_type == 6) {
            $text = $request->user->first_name.' '.$request->user->last_name.' has signed off: ' . $project['project_name'] . ' for ';
            $notification['title'] = 'Sirvez | Project signed off (final)';
            $notification['id'] = 'project-final-'. $project->$id;
        } else {
            $text = $request->user->first_name.' '.$request->user->last_name.' has requested Final Sign Off: ' . $project['project_name'] . 'for';
            $notification['title'] = 'Sirvez | Project sign off (final)';
            $notification['id'] = 'project-final-request-'. $project->id;
        }

        $notification['text1'] = $text;
        $notification['text2'] = '.';
        $notification['image'] = $request->user->profile_pic;
        $users = Project_user::where('project_id', $project->id)->where('type', '!=', '2')->pluck('user_id')->toArray();
        $users = array_map(function ($value) {
            return intval($value);
        }, $users);
        array_push($users, intval($project['created_by']));
        $notification['user_id'] = $users;
        $notification['created_by'] = $request->user->id;
        $notification['action_link'] = '/app/project/live/' . $project->id;

        broadcast(new NotificationEvent($notification))->toOthers();

        //send mail
        if(Project_user::where('project_id',$request->id)->where('type','3')->count() > 0){
            $content = "";
            if($request->user->user_type <6){
                $customer_users = Project_user::where('project_id',$request->id)->where('type','3')->pluck('user_id');
                foreach($customer_users as $row){
                    $pending_user = User::where('id',$row)->first();
                    //$pending_user = $project['customer_user'];
                    $content = $request->user->first_name. " would like you to sign off the scope of works for ".$project['project_name'];
                    $to_name = $pending_user['first_name'];
                    $to_email = $pending_user['email'];
                    $Link_pdf = 'https://app.sirvez.com/upload/file/'.$project['final_sign_file'];
                    $Link_xlsx = 'https://app.sirvez.com/upload/file/'.$project['final_sign_xlsx'];
                    $data = ['name'=>$pending_user['first_name'], "content" => $content,"project"=>$project,"Link_pdf"=>$Link_pdf,"Link_xlsx"=>$Link_xlsx];
                    Mail::send('projectSign', $data, function($message) use ($to_name, $to_email,$project) {
                        $message->to($to_email, $to_name)
                                ->subject('sirvez notification.');
                        $message->from('support@sirvez.com','support team');
                    });
                }

            }
            else{
                $pending_user = User::where('id',$project->created_by)->first();
                $content = "Project was signed off by ".$request->user->first_name.". ".date("d-m-Y H:i:s");
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $Link_pdf = 'https://app.sirvez.com/upload/file/'.$project['final_sign_file'];
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"project"=>$project,"Link_pdf"=>$Link_pdf];
                Mail::send('projectSign', $data, function($message) use ($to_name, $to_email,$project) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','support team');
                });
            }
        }
        
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeSummary(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Project::whereId($id)->update(['project_summary'=>$request->project_summary]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeProjectName(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Project::whereId($id)->update(['project_name'=>$request->project_name]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeProjectRef(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        if($request->project_ref)
        Project::whereId($id)->update(['project_ref'=>$request->project_ref]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeProjectDate(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Project::whereId($id)->update(['survey_start_date'=>$request->project_date]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeProjectContactNumber(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Project::whereId($id)->update(['contact_number'=>$request->contact_number]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeLocationForm(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Project::whereId($id)->update(['location_form_id'=>$request->location_form_id]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeSignoffForm(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Project::whereId($id)->update(['signoff_form_id'=>$request->signoff_form_id]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeFinalSignoffForm(request $request){
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Project::whereId($id)->update(['final_signoff_form_id'=>$request->signoff_form_id]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function deletePartnerUser(request $request)
    {
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        Project_user::where('project_id',$id)->where('user_id',$request->user_id)->where('type',4)->delete();
        $project = Project::where('id',$id)->first();
        $remove_user = User::where('id',$request->user_id)->first();
        $client = $remove_user->first_name.' '.$remove_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has removed '.$client.' from the partner user of project: '.$project['project_name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $project['company_id'],
            'project_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);

        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addPartnerUser(request $request)
    {
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        
        Project_user::create(['user_id'=>$request->user_id,'project_id'=>$id,'type'=>4]);
        $project = Project::where('id',$id)->first();
        $add_user = User::where('id',$request->user_id)->first();
        $client = $add_user->first_name.' '.$add_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has added '.$client.' as a partner user for a new project: '.$project['project_name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $project['company_id'],
            'project_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );
        Notification::create($insertnotificationndata);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function getEvents(request $request){
        $res = array();
        $customerIds = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id')->toArray();
        array_push($customerIds, intval($request->user->company_id));
      
        $res['customers'] = Company::get();
        $res['projects'] = project::get();
        $res['users'] = User::get();
        $res['rooms'] = Room::get();
        $res['project_users'] = Project_user::where('type', '!=', '2')->get();
        $res['all_users'] = User::where('status',1)->get();
        $res['status'] = 'success';
        $events = array();
        if($request->project_id)
            $roomIds = Room::where('project_id',$request->project_id)->pluck('id');
        else
            $roomIds = Room::whereIn('company_id',$customerIds)->pluck('id');
        $scheduleIds = ScheduleEngineer::where('engineer_id',$request->user->id)->pluck('schedule_id');
        $schedules = Schedule::whereIn('schedules.room_id',$roomIds)
                    ->where(function($q) use($request,$scheduleIds){
                        return $q->whereIn('schedules.id',$scheduleIds)
                        ->orwhere('schedules.created_by',$request->user->id);
                    })
                    ->where('schedules.created_by',$request->user->id)
                    ->leftJoin('rooms','rooms.id','=','schedules.room_id')
                    ->leftJoin('users','users.id','=','schedules.created_by')
                    ->leftJoin('projects','projects.id','=','rooms.project_id')
                    ->select('schedules.*','rooms.project_id','users.first_name','users.last_name','users.profile_pic','projects.project_name')
                    ->get();
        foreach($schedules as $key => $schedule){
            $engineers = ScheduleEngineer::where('schedule_id',$schedule->id)->pluck('engineer_id')->toArray();
            array_push($engineers, intval($schedule->created_by));
            array_push($events,['title'=>$schedule->schedule_name,
                                'start'=>date('Y-m-d H:i:s', strtotime($schedule['start_date'])),
                                'end'=>date('Y-m-d H:i:s', strtotime($schedule['end_date'])),
                                'desc'=>$schedule->note,
                                'created_by'=>$schedule->created_by,
                                'project_id'=>$schedule->project_id,
                                'room_id'=>$schedule->room_id,
                                'user_name'=>$schedule->first_name.' '.$schedule->last_name,
                                'profile_pic'=>$schedule->profile_pic,
                                'users'=>join(',',$engineers),
                                'project_name'=>$schedule->project_name,
                                'id'=>$schedule->id,
                                'type' => 1
                                ]);
        }
        $taskIds = Project_user::where('user_id',$request->user->id)->where('type',2)->pluck('project_id');
        if($request->project_id)
            $tasks = Task::where('tasks.project_id',$request->project_id)
                                ->whereNotNull('tasks.due_by_date')
                                ->where(function($q) use($request,$taskIds){
                                    return $q->whereIn('tasks.id',$taskIds)
                                    ->orwhere('tasks.created_by',$request->user->id);
                                })
                                ->leftJoin('users','users.id','=','tasks.created_by')
                                ->leftJoin('projects','projects.id','=','tasks.project_id')
                                ->leftJoin('companies','companies.id','=','tasks.company_id')
                                ->select('tasks.*','users.first_name','users.last_name','users.profile_pic','companies.name as company_name','projects.project_name')
                                ->get();
        else
            $tasks = Task::whereIn('tasks.company_id',$customerIds)
                                ->whereNotNull('tasks.due_by_date')
                                ->where(function($q) use($request,$taskIds){
                                    return $q->whereIn('tasks.id',$taskIds)
                                    ->orwhere('tasks.created_by',$request->user->id);
                                })
                                ->leftJoin('users','users.id','=','tasks.created_by')
                                ->leftJoin('projects','projects.id','=','tasks.project_id')
                                ->leftJoin('companies','companies.id','=','tasks.company_id')
                                ->select('tasks.*','users.first_name','users.last_name','users.profile_pic','companies.name as company_name','projects.project_name')
                                ->get();
        foreach($tasks as $key=>$row){
            $tasks[$key]['label_value'] = Task_label_value::where('task_id',$row->id)->pluck('label_id');
            $tasks[$key]['client_users'] = Project_user::where(['project_users.project_id'=>$row->id,'type'=>'2'])->pluck('user_id');
            $tasks[$key]['assign_users'] = Project_user::where(['project_users.project_id'=>$row->id,'type'=>'2'])
            ->leftJoin('users','users.id','=','project_users.user_id')->get();
        }      
        $res['tasks'] = $tasks;
        foreach($tasks as $key => $task){
            array_push($events,['title'=>$task->task,
                                'start'=>date('Y-m-d H:i:s', strtotime($task['due_by_date'])),
                                'end'=>date('Y-m-d H:i:s', strtotime($task['due_by_date'])),
                                'desc'=>$task->description,
                                'created_by'=>$task->created_by,
                                'project_id'=>$task->project_id,
                                'room_id'=>$task->room_id,
                                'task_id'=>$task->id,
                                'user_name'=>$task->first_name.' '.$task->last_name,
                                'profile_pic'=>$task->profile_pic,
                                'users'=>$task->created_by.','.$task->archived_id,
                                'project_name'=>$task->project_name,
                                'id'=>$task->id,
                                'type' => 2
                                ]);
        }
        if($request->project_id)
            $taskIds = Task::where('project_id',$request->project_id)->pluck('id');
        else
            $taskIds = Task::whereIn('company_id',$customerIds)->pluck('id');
        $commentIds = Task_comment_user::where('user_id',$request->user->id)->pluck('comment_id');
        $tasks = TaskComment::whereIn('task_comments.task_id',$taskIds)
                            ->where(function($q) use($request,$commentIds){
                                return $q->whereIn('task_comments.id',$commentIds)
                                ->orwhere('task_comments.created_by',$request->user->id);
                            })
                            ->whereNotNull('task_comments.deadline')
                            ->leftJoin('tasks','tasks.id','=','task_comments.task_id')
                            ->leftJoin('users','users.id','=','task_comments.created_by')
                            ->leftJoin('projects','projects.id','=','tasks.project_id')
                            ->select('tasks.*','users.first_name','users.last_name','users.profile_pic','projects.project_name')
                            ->get();
        foreach($tasks as $key => $task){
            $comment_users = Task_comment_user::where('comment_id',$task->id)->pluck('user_id')->toArray();
            array_push($comment_users,$task->created_by);
            array_push($events,['title'=>$task->comment,
                                'start'=>date('Y-m-d H:i:s', strtotime($task->created_at)),
                                'end'=>date('Y-m-d H:i:s', strtotime($task['deadline'])),
                                'desc'=>'',
                                'created_by'=>$task->created_by,
                                'project_id'=>$task->project_id,
                                'room_id'=>$task->room_id,
                                'task_id'=>$task->task_id,
                                'user_name'=>$task->first_name.' '.$task->last_name,
                                'profile_pic'=>$task->profile_pic,
                                'users'=>join(',',$comment_users),
                                'project_name'=>$task->project_name,
                                'id'=>$task->id,
                                'type' => 3
                                ]);
        }
        $projectIds = Project_user::where('user_id',$request->user->id)->where('type',3)->pluck('project_id');
        if($request->project_id)
            $projects = Project::where('projects.id',$request->project_id)
                                ->where(function($q) use($request,$projectIds){
                                    return $q->whereIn('projects.id',$projectIds)
                                    ->orwhere('projects.created_by',$request->user->id);
                                })
                                ->leftJoin('users','users.id','=','projects.created_by')
                                ->select('projects.*','users.first_name','users.last_name','users.profile_pic')
                                ->get();
        else
            $projects = Project::whereIn('projects.company_id',$customerIds) 
                                ->where(function($q) use($request,$projectIds){
                                    return $q->whereIn('projects.id',$projectIds)
                                    ->orwhere('projects.created_by',$request->user->id);
                                })
                                ->leftJoin('users','users.id','=','projects.created_by')
                                ->select('projects.*','users.first_name','users.last_name','users.profile_pic')
                                ->get();;
        foreach($projects as $key => $project){
            $project_users = Project_user::where(['project_id'=>$project->id,'type'=>[1,3,4]])->pluck('user_id')->toArray();
            array_push($project_users,$project->created_by);
            array_push($project_users,$project->manager_id);
            array_push($events,['title'=>$project->project_name,
                                'start'=>date('Y-m-d H:i:s', strtotime($project['survey_start_date'])),
                                'end'=>date('Y-m-d H:i:s', strtotime($project['survey_start_date'])),
                                'desc'=>$project->project_summary,
                                'created_by'=>$project->created_by,
                                'project_id'=>$project->id,
                                'room_id'=>'',
                                'user_name'=>$project->first_name.' '.$project->last_name,
                                'profile_pic'=>$project->profile_pic,
                                'users'=>join(',',$project_users),
                                'project_name'=>$project->project_name,
                                'id'=>$project->id,
                                'type' => 4
                                ]);
        }
        $projectIds = Project_user::where('user_id',$request->user->id)->where('type',3)->pluck('project_id');
        if($request->project_id)
             $projects = Project::where('projects.id',$request->project_id)
                            ->where(function($q) use($request,$projectIds){
                                return $q->whereIn('projects.id',$projectIds)
                                ->orwhere('projects.created_by',$request->user->id);
                            })
                            ->where('projects.signed_off',2)
                            ->whereNotNull('projects.signoff_date')
                            ->leftJoin('users','users.id','=','projects.created_by')
                            ->select('projects.*','users.first_name','users.last_name','users.profile_pic')
                            ->get();
        else
            $projects = Project::whereIn('projects.company_id',$customerIds)
                            ->where(function($q) use($request,$projectIds){
                                return $q->whereIn('projects.id',$projectIds)
                                ->orwhere('projects.created_by',$request->user->id);
                            })
                            ->where('projects.signed_off',2)
                            ->whereNotNull('projects.signoff_date')
                            ->leftJoin('users','users.id','=','projects.created_by')
                            ->select('projects.*','users.first_name','users.last_name','users.profile_pic')
                            ->get();
        foreach($projects as $key => $project){
            $project_users = Project_user::where(['project_id'=>$project->id,'type'=>3])->pluck('user_id')->toArray();
            array_push($project_users,$project->created_by);
            array_push($project_users,$project->manager_id);
            array_push($events,['title'=>$project->project_name,
                                'start'=>date('Y-m-d H:i:s', strtotime($project['signoff_date'])),
                                'end'=>date('Y-m-d H:i:s', strtotime($project['signoff_date'])),
                                'desc'=>$project->project_summary,
                                'created_by'=>$project->created_by,
                                'project_id'=>$project->id,
                                'room_id'=>'',
                                'user_name'=>$project->first_name.' '.$project->last_name,
                                'profile_pic'=>$project->profile_pic,
                                'users'=>join(',',$project_users),
                                'project_name'=>$project->project_name,
                                'id'=>$project->id,
                                'type' => 5
                                ]);
        }
        if($request->project_id)
            $calendar_events = Calendar_event::where('calendar_events.users','like','%,'.$request->user->id.',%')
                                ->where('calendar_events.project_id',$request->project_id)
                                ->leftJoin('users','users.id','=','calendar_events.created_by')
                                ->leftJoin('projects','projects.id','=','calendar_events.project_id')
                                ->select('calendar_events.*','users.first_name','users.last_name','users.profile_pic','projects.project_name')
                                ->get();
        else
            $calendar_events = Calendar_event::where('calendar_events.users','like','%,'.$request->user->id.',%')
                                ->leftJoin('users','users.id','=','calendar_events.created_by')
                                ->leftJoin('projects','projects.id','=','calendar_events.project_id')
                                ->select('calendar_events.*','users.first_name','users.last_name','users.profile_pic','projects.project_name')
                                ->get();
        foreach($calendar_events as $key => $calendar_event){
            array_push($events,['title'=>$calendar_event->title,
                                'start'=>date('Y-m-d H:i:s', strtotime($calendar_event->start)),
                                'end'=>date('Y-m-d H:i:s', strtotime($calendar_event->end)),
                                'desc'=>$calendar_event->description,
                                'created_by'=>$calendar_event->created_by,
                                'project_id'=>$calendar_event->project_id,
                                'room_id'=>$calendar_event->room_id,
                                'project_name'=>$calendar_event->project_name,
                                'user_name'=>$calendar_event->first_name.' '.$calendar_event->last_name,
                                'profile_pic'=>$calendar_event->profile_pic,
                                'users'=>$calendar_event->users.',',
                                'id'=>$calendar_event->id,
                                'type' => 6
                                ]);
        }
        $res['events'] = $events;
        $res['eventsyncs'] = CalendarEventSync::all();
        return response()->json($res);
    }
    public function saveEventID(request $request) {
        $res = array();
        if ($request->has('google_event_id')) {
            if (CalendarEventSync::where([
                'event_id' => $request->event_id,
                'event_type' => $request->event_type
            ])->count() > 0) {
                CalendarEventSync::where([
                    'event_id' => $request->event_id,
                    'event_type' => $request->event_type,
                ])->update([
                    'google_event_id' => $request->google_event_id
                ]);
            } else {
                $sync = CalendarEventSync::create([
                    'event_id' => $request->event_id,
                    'event_type' => $request->event_type,
                    'google_event_id' => $request->google_event_id
                ]);
                $res['sync'] = $sync;
            }
        }
        if ($request->has('office365_event_id')) {
            if (CalendarEventSync::where([
                'event_id' => $request->event_id,
                'event_type' => $request->event_type
            ])->count() > 0) {
                CalendarEventSync::where([
                    'event_id' => $request->event_id,
                    'event_type' => $request->event_type
                ])->update([
                    'office365_event_id' => $request->office365_event_id
                ]);
            } else {
                $sync = CalendarEventSync::creat([
                    'event_id' => $request->event_id,
                    'event_type' => $request->event_type,
                    'office365_event_id' => $request->office365_event_id
                ]);
                $res['sync'] = $sync;
            }
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function saveEvent(request $request){
        $res = array();
        $event_users = json_decode($request->added_users,true);
        $deleted_users = json_decode($request->deleted_users,true);
        if($request->type==1){
            Schedule::whereId($request->id)->update([
                'schedule_name'=>$request->title,
                'note'=>$request->desc,
                'start_date'=>$request->start,
                'end_date'=>$request->end]);
            foreach($deleted_users as $user){
                ScheduleEngineer::where(['schedule_id'=>$request->id,'engineer_id'=>$user])->delete();
            }    
            foreach($event_users as $user){
                ScheduleEngineer::create(['schedule_id'=>$request->id,'engineer_id'=>$user]);
            }
        }else if($request->type==2){
            Task::whereId($request->id)->update([
                'task'=>$request->title,
                'description'=>$request->desc,
                'due_by_date'=>$request->start
                ]);
                foreach($deleted_users as $user){
                    Project_user::where(['schedule_id'=>$request->id,'user_id'=>$user,'type'=>2])->delete();
                }    
                foreach($event_users as $user){
                    Project_user::create(['project_id'=>$request->id,'user_id'=>$user,'type'=>2]);
                }
        }else if($request->type==3){
            TaskComment::whereId($request->id)->update([
                'comment'=>$request->title,
                'deadline'=>$request->start]);
            foreach($deleted_users as $user){
                Task_comment_user::where(['comment_id'=>$request->id,'user_id'=>$user])->delete();
            }   
            foreach($event_users as $user){
                Task_comment_user::create(['comment_id'=>$request->id,'user_id'=>$user]);
            }
        }else if($request->type==4){
            Project::whereId($request->id)->update([
                'project_name'=>$request->title,
                'project_summary'=>$request->desc,
                'survey_start_date'=>$request->start]);
            foreach($deleted_users as $user){
                Project_user::where(['project_id'=>$request->id,'user_id'=>$user,'type'=>3])->delete();
            }   
            foreach($event_users as $user){
                Project_user::create(['project_id'=>$request->id,'user_id'=>$user,'type'=>3]);
            }
        }else if($request->type==5){
            Project::whereId($request->id)->update([
                'project_name'=>$request->title,
                'project_summary'=>$request->desc,
                'signoff_date'=>$request->start]);
            foreach($deleted_users as $user){
                Project_user::where(['project_id'=>$request->id,'user_id'=>$user,'type'=>3])->delete();
            }   
            foreach($event_users as $user){
                Project_user::create(['project_id'=>$request->id,'user_id'=>$user,'type'=>3]);
            }
        }
        else if($request->type==6){
            if($request->id==''){
                Calendar_event::create([
                    'title'=>$request->title,
                    'description'=>$request->desc,
                    'start'=>$request->start,
                    'end'=>$request->is_fullDay==0?$request->end:$request->start,
                    'created_by'=>$request->user->id,
                    ]);
            }
            else{
                $event = Calendar_event::whereId($request->id)->first();
                //$calender_user = $event->users;
                // foreach($deleted_users as $user){
                //     str_replace(','.$user.',',',',$calender_user);
                // }
                $users =  explode(',',$event->users);
                $users = array_diff($user,$deleted_users);
                $event_users = array_merge($users,$event_users);
                Calendar_event::whereId($request->id)->update([
                    'title'=>$request->title,
                    'start'=>$request->start,
                    'end'=>$request->is_fullDay==0?$request->end:$request->start,
                    'created_by'=>$request->user->id,
                    'users'=>join(',',$event_users).','
                    ]);
            }
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function deleteEvent(request $request){
        $res = array();
        if($request->type==1){
            if(strlen($request->id)>10)
                $id = Schedule::where(['off_id'=>$request->id])->first()->id;
            else
                $id = $request->id;
            if(Schedule::where('parent_id',$id)->count()==0){
                Schedule::where(['id'=>$request->id])->delete();
                $res["status"] = "success";
                ScheduleProduct::where('schedule_id', $id)->delete();
            }
            else{
                $res["status"] = 'error';
                $res["msg"] = "This schedule has already child. Please first delete child!";
            }
        }else if($request->type==2){
            Task::whereId($request->id)->update(['due_by_date'=>null]);
        }else if($request->type==3){
            TaskComment::whereId($request->id)->update(['deadline'=>null]);
        }else if($request->type==4){
            Project::whereId($request->id)->update(['survey_start_date'=>null]);
        }else if($request->type==5){
            Project::whereId($request->id)->update(['signoff_date'=>null]);
        }
        else if($request->type==6){
            Calendar_event::whereId($request->id)->delete();
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function getProjectModule(request $request){
        
        $res = array();
        if(ProjectModule::where([
            'company_id' => $request->user->company_id
        ])->count()>0)
            $res['module_status'] = ProjectModule::where('company_id',$request->user->company_id)->first();
        else
            $res['module_status'] = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function updateProjectModule(request $request){
        
        $data = array();
        if($request->in_project==1){
            $data[$request->caption] = $request->value;
            if(ProjectModule::where(['user_id'=>$request->user->id,'project_id'=>$request->project_id])->count()>0)
                ProjectModule::where(['user_id'=>$request->user->id,'project_id'=>$request->project_id])->update($data);
            else{
                $company_id=$request->user->company_id;
                if (Company_customer::where('customer_id',$company_id)->count() > 0) {
                    $company_id = Company_customer::where('customer_id',$company_id)->first()->company_id;
                }

                if(ProjectModule::where(['company_id'=>$company_id])->count()>0){
                    $data = ProjectModule::where(['company_id'=>$company_id])->first();
                    $data[$request->caption] = $request->value;
                    unset($data['id']);
                    unset($data['created_at']);
                    unset($data['updated_at']);
                    $data['user_id']= $request->user->id;
                    $data['project_id']= $request->project_id;
                    ProjectModule::create($data->toArray());
                } else {
                    $data['user_id']= $request->user->id;
                    $data['project_id']= $request->project_id;
                    ProjectModule::create($data);
                }
            }
            if ($request->has('option') && $request->option == 2) {
                $project = Project::whereId($request->project_id)->first();
                // type == 1 ; team member, type == 3 ; customer users, type == 4; partner users
                $project_users = Project_user::where('project_id', $project->id)
                    ->where('type', '!=', '2')->pluck('user_id')->toArray();
                
                // super admin
                $project_company = $project->company_id;
                Company_customer::where('customer_id',$project->company_id)->first()->company_id;
                $admin_users = User::where('company_id', $project_company)
                    ->where('status', 1)->whereIn('user_type', [1, 3])
                    ->pluck('id')->toArray();

                // super super admin
                $super_users = User::where('status', 1)->where('user_type', 0)
                    ->pluck('id')->toArray();

                // account manager
                $account_manager = $project->manager_id;

                array_push($project_users, $admin_users);
                array_push($project_users, $super_users);
                array_push($project_users, $account_manager);

                foreach ($project_users as $user) {
                    $data = array();
                    $data[$request->caption] = $request->value;
                    if(ProjectModule::where(['user_id'=>$user,'project_id'=>$request->project_id])->count()>0)
                        ProjectModule::where(['user_id'=>$user,'project_id'=>$request->project_id])->update($data);
                    else{
                        $company_id=$request->user->company_id;
                        if (Company_customer::where('customer_id',$company_id)->count() > 0) {
                            $company_id = Company_customer::where('customer_id',$company_id)->first()->company_id;
                        }
        
                        if(ProjectModule::where(['company_id'=>$company_id])->count()>0){
                            $data = ProjectModule::where(['company_id'=>$company_id])->first();
                            $data[$request->caption] = $request->value;
                            unset($data['id']);
                            unset($data['created_at']);
                            unset($data['updated_at']);
                            $data['user_id']= $user;
                            $data['project_id']= $request->project_id;
                            ProjectModule::create($data->toArray());
                        } else {
                            $data['user_id']= $user;
                            $data['project_id']= $request->project_id;
                            ProjectModule::create($data);
                        }
                    }
                }
            }
        } else {
            $data['user_id'] = $request->user->id;
            $data['task'] = $request->task;
            $data['task_lock'] = $request->task_lock;
            $data['version'] = $request->version;
            $data['version_lock'] = $request->version_lock;
            $data['schedule'] = $request->schedule;
            $data['schedule_lock'] = $request->schedule_lock;
            $data['calendar'] = $request->calendar;
            $data['calendar_lock'] = $request->calendar_lock;
            $data['chat'] = $request->chat;
            $data['chat_lock'] = $request->chat_lock;
            $data['product'] = $request->product;
            $data['product_lock'] = $request->product_lock;
            $data['activity'] = $request->activity;
            $data['activity_lock'] = $request->activity_lock;
            $data['tender'] = $request->tender;
            $data['tender_lock'] = $request->tender_lock;
            $data['healthy'] = $request->healthy;
            $data['healthy_lock'] = $request->healthy_lock;
            $data['company_id'] = $request->user->company_id;
            $data['user_id']= $request->user->id;
            
            if(ProjectModule::where([
                'company_id' => $request->user->company_id
            ])->count() > 0)
                ProjectModule::where([
                    'company_id' => $request->user->company_id
                ])->update($data);
            else{
                ProjectModule::create($data);
            }
        }
        
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function getActivity(request $request){
        $res = array();
        $recent_messages = Notification::where('notifications.notice_type','>',2)
                        ->where('notifications.project_id',$request->project_id)
                        ->orderBy('id','desc')
                        ->leftJoin('projects','projects.id','=','notifications.project_id')
                        ->leftJoin('users','users.id','=','projects.user_id')
                        ->select('notifications.*','projects.project_name','projects.id as project_id','users.first_name','users.last_name')
                        ->get();
        foreach($recent_messages as $key => $row){
            $recent_messages[$key]['user_img'] = '';
            $recent_messages[$key]['current_time'] = date("Y-m-d H:i:s");
            $recent_messages[$key]['room_number'] = '';
            $recent_messages[$key]['room_id'] = '';
            if(User::where('id',$row['created_by'])->count()==0) continue;
            $user = User::where('id',$row['created_by'])->first();
            $recent_messages[$key]['user_img'] = $user['profile_pic'];
            if(($row->notice_type==5)||($row->notice_type==7))
            {
                if(Room::whereId($row['notice_id'])->count()==0) continue;
                $recent_messages[$key]['room_number'] = Room::whereId($row['notice_id'])->first()->room_number;
                $recent_messages[$key]['room_id'] = Room::whereId($row['notice_id'])->first()->id;
            }
            else if($row->notice_type==8){
                if(Product::whereId($row['notice_id'])->count()==0) continue;
                $roomId = Product::whereId($row['notice_id'])->first()->room_id;
                if(Room::whereId($roomId)->count()==0) continue;
                $recent_messages[$key]['room_number']  = Room::whereId($roomId)->first()->room_number;
                $recent_messages[$key]['room_id']  = Room::whereId($roomId)->first()->id;
            }
            else if($row->notice_type==4){
                if(Task::whereId($row['notice_id'])->count()==0) continue;
                $roomId = Task::whereId($row['notice_id'])->first()->room_id;
                if(Room::whereId($roomId)->count()==0) continue;
                $recent_messages[$key]['room_number']  = Room::whereId($roomId)->first()->room_number;
                $recent_messages[$key]['room_id']  = Room::whereId($roomId)->first()->id;
            }
        }
        $res['activities'] = $recent_messages;
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function saveNewEvent(request $request){
        
        $event_users = json_decode($request->event_users,true);
        array_push($event_users,strval($request->user->id));
        Calendar_event::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'start'=>$request->start_date,
            'end'=>$request->is_fullDay==0?$request->end_date:$request->start_date,
            'created_by'=>$request->user->id,
            'project_id'=>$request->project_id,
            'room_id'=>$request->room_ids,
            'users'=>','.join(',',$event_users).','
            ]);
       
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addProjectPage(request $request){
        $order_no = intval(ProjectPage::where('project_id',$request->project_id)->max('order_no'));
        $page = array();
        $page['project_id'] = $request->project_id;
        $page['page_name'] = $request->page_name;
        $page['root_type'] = $request->root_type;
        $page['sub_id'] = $request->sub_id;
        $page['option_1'] = $request->option_1;
        $page['option_2'] = $request->option_2;
        $page['option_3'] = $request->option_3;
        $page['option_4'] = $request->option_4;
        $page['page_count'] = $request->page_count;
        $page['content'] = $request->content;
        $page['lock_page'] = $request->lock_page;
        $page['link_url'] = $request->link_url;
        $page['created_by'] = $request->user->id;
        $page['updated_by'] = $request->user->id;
        $page['order_no'] = $order_no+1;
        $page = ProjectPage::create($page);
        $res = array();
        $res['status'] = 'success';
        $res['page'] = $page;
        return response()->json($res);
    }
    public function removeProjectPage(request $request){
        $res = array();
        ProjectPage::whereId($request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeOrderRoom(request $request){
        $res = array();
        $ordered_id = json_decode($request->ordered_id,true);
        foreach($ordered_id as $key=> $orderId)
        {
            Room::whereId($orderId)->update(['order_no'=>$key+1]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeOrderProjectPage(request $request){
        $res = array();
        $ordered_id = json_decode($request->ordered_id,true);
        foreach($ordered_id as $key=> $orderId)
        {
            ProjectPage::whereId($orderId)->update(['order_no'=>$key+1]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addProjectTender(request $request){
        $order_no = intval(ProjectTender::where('project_id',$request->project_id)->max('order_no'));
        $page = array();
        $page['project_id'] = $request->project_id;
        $page['page_name'] = $request->page_name;
        $page['root_type'] = $request->root_type;
        $page['sub_id'] = $request->sub_id;
        $page['option_1'] = $request->option_1;
        $page['option_2'] = $request->option_2;
        $page['option_3'] = $request->option_3;
        $page['option_4'] = $request->option_4;
        $page['page_count'] = $request->page_count;
        $page['content'] = $request->content;
        $page['lock_page'] = $request->lock_page;
        $page['link_url'] = $request->link_url;
        $page['created_by'] = $request->user->id;
        $page['updated_by'] = $request->user->id;
        $page['order_no'] = $order_no+1;
        $page = ProjectTender::create($page);
        $res = array();
        $res['status'] = 'success';
        $res['page'] = $page;
        return response()->json($res);
    }
    public function removeProjectTender(request $request){
        $res = array();
        ProjectTender::whereId($request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeOrderProjectTender(request $request){
        $res = array();
        $ordered_id = json_decode($request->ordered_id,true);
        foreach($ordered_id as $key=> $orderId)
        {
            ProjectTender::whereId($orderId)->update(['order_no'=>$key+1]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addProjectHealthy(request $request){
        $order_no = intval(ProjectHealthy::where('project_id',$request->project_id)->max('order_no'));
        $page = array();
        $page['project_id'] = $request->project_id;
        $page['page_name'] = $request->page_name;
        $page['root_type'] = $request->root_type;
        $page['sub_id'] = $request->sub_id;
        $page['option_1'] = $request->option_1;
        $page['option_2'] = $request->option_2;
        $page['option_3'] = $request->option_3;
        $page['option_4'] = $request->option_4;
        $page['page_count'] = $request->page_count;
        $page['content'] = $request->content;
        $page['lock_page'] = $request->lock_page;
        $page['link_url'] = $request->link_url;
        $page['created_by'] = $request->user->id;
        $page['updated_by'] = $request->user->id;
        $page['order_no'] = $order_no+1;
        $page = ProjectHealthy::create($page);
        $res = array();
        $res['status'] = 'success';
        $res['page'] = $page;
        return response()->json($res);
    }
    public function removeProjectHealthy(request $request){
        $res = array();
        ProjectHealthy::whereId($request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeOrderProjectHealthy(request $request){
        $res = array();
        $ordered_id = json_decode($request->ordered_id,true);
        foreach($ordered_id as $key=> $orderId)
        {
            ProjectHealthy::whereId($orderId)->update(['order_no'=>$key+1]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    
    public function createSubscription(request $request) {
        $res = array();
        $res['subscription'] = json_decode($request->subscription);
        $res['status'] = "success";
        return response()->json($res);
    }
    public function changeTopMenu(request $request){
        $res = array();
        $menu = array();
        $menu['quote'] = $request->quote;
        $menu['install'] = $request->install;
        $menu['signoff'] = $request->signoff;
        $menu['archive'] = $request->archive;
        ProjectTopMenu::whereId($request->id)->update($menu);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeLockProjectPage(request $request){
        $res = array();
        $project_id = $request->project_id;
        $lock_page = $request->lock_page;
        if($request->doc_type=='1')
            Project::where('id',$project_id)
                ->update(['lock_page'=>$lock_page,'lock_page_user'=>$request->user->id,'lock_page_date'=>date("Y-m-d H:i:s")]);
        else if($request->doc_type=='2')
        Project::where('id',$project_id)
                ->update(['lock_tender'=>$lock_page,'lock_tender_user'=>$request->user->id,'lock_tender_date'=>date("Y-m-d H:i:s")]);
        else if($request->doc_type=='3')
        Project::where('id',$project_id)
                ->update(['lock_healthy'=>$lock_page,'lock_healthy_user'=>$request->user->id,'lock_healthy_date'=>date("Y-m-d H:i:s")]);
        $res['status'] = 'success';
        return response()->json($res);   
    }
    public function signOffAllLocaton(request $request){
        $res = array();
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::where('project_id',$id)->where('signed_off',0)->update([
            'signed_off'=>1,
            'completed_date'=>date("Y-m-d H:i:s"),
            'completed_by'=>$request->user->id
        ]);
        $res['status'] = "success";
        return response()->json($res);
    }
    public function moveInstallStage(request $request){
        $res = array();
        if(strlen($request->id) > 10)
            $id = Project::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Room::where('project_id',$id)->where('signed_off',0)->update([
            'signed_off'=>1,
            'completed_date'=>date("Y-m-d H:i:s"),
            'completed_by'=>$request->user->id
        ]);
        Project::whereId($id)->update([
            'signed_off'=>2,
            'signoff_date'=>date("Y-m-d H:i:s"),
            'signoff_user'=>$request->user->id,
        ]);
        $res['status'] = "success";
        return response()->json($res);
    }
}
