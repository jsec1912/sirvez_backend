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
use App\Product_label_value;
use App\Partner;
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
                        'is_read'	    	=> 0,
                    );
                    Notification::create($insertnotificationndata);
                }
            }
        }
        else{
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
                }
            }
        }

        //$notice_type ={1:pending_user,2:createcustomer 3:project}
        if($request->cusotmer_user){
            $customerUsers= User::whereIn('id',$request->cusotmer_user)->get();
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
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new project: '.$project['project_name'].' for '.$client_name.'.',
                    'created_by'		=> $request->user->id,
                    'company_id'		=> $project['company_id'],
                    'project_id'		=> $id,
                    'created_date'		=> date("Y-m-d H:i:s"),
                    'is_read'	    	=> 0,
                );
            }
            else{
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has updated project : '.$project['project_name'].' for '.$client_name.'.',
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
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new project: '.$project['project_name'].'.',
                    'created_by'		=> $request->user->id,
                    'company_id'		=> $project['company_id'],
                    'project_id'		=> $id,
                    'created_date'		=> date("Y-m-d H:i:s"),
                    'is_read'	    	=> 0,
                );
            }
            else{
                $insertnotificationndata = array(
                    'notice_type'		=> '3',
                    'notice_id'			=> $id,
                    'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has updated project : '.$project['project_name'].'.',
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
            $project_array = Project::where('archived',$request->archived)
            ->leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
            $res['customers'] = Company::get();
            $res['users'] = User::get();
        } else if ($request->user->user_type < 4) {
            $id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
    
            $partner_ids = Project_user::where([
                'user_id' => $request->user->id,
                'type' => 4
            ])->pluck('project_id');

            $project_array = Project::where(function ($q) use($id, $partner_ids) {
                return $q->whereIn('projects.company_id',$id)
                ->orWhereIn('projects.id', $partner_ids);
            })->where('archived',$request->archived)
            ->leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
            $res['customers'] = Company::whereIn('id',$id)->orwhere('id',$request->user->company_id)->get();
            $res['users'] = User::whereIn('company_id',$id)->orwhere('company_id',$request->user->company_id)->get();
        } else {
            $projectIdx = Project_user::where('user_id', $request->user->id)
            ->where(function ($q) {
                return $q->where('type', 3) // team member case
                ->orWhere('type', 4); // partner user case
            })
            ->pluck('project_id');
            $project_array = Project::whereIn('projects.id',$projectIdx)->where('archived',$request->archived)
            ->leftJoin('companies','companies.id','=','projects.company_id')
            ->leftJoin('users','users.id','=','projects.manager_id')
            ->select('projects.*', 'companies.name AS customer','users.first_name AS account_manager','users.profile_pic')->orderBy('id','desc')->get();
        }

        foreach($project_array as $key => $row){
            $project_array[$key]['survey_start_date'] = date('d-m-Y', strtotime($row['survey_start_date']));
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
                ->select('projects.*','companies.logo_img','companies.name AS company_name','users.first_name')->first();
            
            /////  check permission
            if ($this->project_check($project, $request) == 0) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You do not have permission to view this project.'
                ]);
            }
            /////  end check permission

            $project['survey_start_date'] = date('d-m-Y', strtotime($project['survey_start_date']));
            $res['notification'] = Notification::where('notice_type','6')->where('notice_id',$id)->orderBy('id','desc')->get();
            $project['site_count'] = Project_site::where('project_id',$project['id'])->count();
            $project['room_count'] = Room::where('project_id',$project['id'])->count();
            $project['user_notifications'] = Notification::where('notice_type','3')->where('notice_id',$id)->count();
            if(User::where('id',$project['archived_id'])->count()>0)
                $project['archived_name'] = User::where('id',$project['archived_id'])->first()->first_name;
            else
                $project['archived_name'] = '';
            $project['assign_to'] = Project_user::where(['project_id'=>$id,'type'=>'1'])->pluck('user_id');
            $project['customer_users'] = Project_user::where(['project_id'=>$id,'type'=>'3'])->pluck('user_id');
            $project['partner_users'] = Project_user::where(['project_id'=>$id,'type'=>'4'])->pluck('user_id');
            if(Company_customer::where('customer_id',$project->company_id)->count()>0)
                $companyId = Company_customer::where('customer_id',$project->company_id)->first()->company_id;
            else
                $companyId = '';

            $res['partners'] = Partner::where(function($q) use($project,$companyId){
                                        return $q->where('partners.company_id',$project->company_id)
                                        ->orwhere('partners.company_id',$companyId);
                                    })
                                    ->leftJoin('users','users.company_id','=','partners.partner_id')
                                    ->select('users.*')
                                    ->orderBy('partners.id','desc')
                                    ->get();
            if (in_array($request->user->id, $project['partner_users']->toArray())) {
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
                $partner_company_ids = User::whereIn('id', $project['partner_users'])->pluck('company_id');
                $project['partner_logos'] = Company::whereIn('id', $partner_company_ids)->get();
            }

            $res["project"] = $project;

            $res['sites'] = Site::where('company_id',$project->company_id)->orWhere('company_id',$companyId)->orderBy('id','desc')->get();
            $rooms = Room::where('rooms.project_id',$id)
                ->leftJoin('sites','rooms.site_id','=','sites.id')
                ->leftJoin('companies','rooms.company_id','=','companies.id')
                ->leftJoin('buildings','rooms.building_id','=','buildings.id')
                ->select('rooms.*','sites.site_name','companies.name as company_name','buildings.building_name')
                ->orderBy('rooms.id','desc')->get();
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
            $res['assign_to'] = Project_user::where(['project_users.project_id'=>$id,'project_users.type'=>'1'])
                                            ->leftjoin('users','users.id','=','project_users.user_id')
                                            ->select('users.*')
                                            ->get();
            $res['customer_users'] = Project_user::where(['project_users.project_id'=>$id,'project_users.type'=>'3'])
                                            ->leftjoin('users','users.id','=','project_users.user_id')
                                            ->select('users.*')
                                            ->get();
            $products = Product::whereIn('room_id',$room_ids)
                                ->orWhere(function($q) use($id){
                                    return $q->where('project_id',$id)
                                        ->where('action',3);
                                    })
                                ->orderBy('id','desc')
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
                $products[$key]['client_name'] = $res['customer_users'];
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
            $res['team'] = User::where('company_id',$com_id)->whereIn('user_type',[0,1,3])->where('status',1)->whereNotIn('id',$assignId)->get();
            $assignId = Project_user::where(['project_id'=>$id,'type'=>3])->pluck('user_id');
            $res['left_customer_users'] = User::where('company_id',$project->company_id)->whereIn('user_type',[5,6])->where('status',1)->whereNotIn('id',$assignId)->get();
            $res['engineers'] = User::where('company_id',$com_id)->where('user_type',2)->where('status',1)->select('id','first_name','last_name','profile_pic')->get();
            $res['task_assign_to'] = User::where('company_id',$com_id)->whereIn('user_type',[0,1,3])->where('status',1)->select('id','first_name','last_name','profile_pic')->get();
            $res['signed_cnt'] = Room::where('project_id',$id)->where('signed_off','<>','2')->count()-Room::where('project_id',$id)->where('signed_off','1')->count();
            $res['unsigned_room'] = Room::where('project_id',$id)->where('signed_off','0')->get();
            
            $res['customer_userlist'] = User::whereIn('user_type',[5,6])->where('status',1)->where('company_id',$project->company_id)->select('id','first_name','last_name')->get();
            $res['off_form_values'] = Form_value::where('form_type', 3)
                ->where('parent_id',$project['signoff_form_id'])->get();
            $productIds = Product::whereIn('room_id',$room_ids)
            ->orWhere(function($q) use($id){
                return $q->where('project_id',$id)
                    ->where('action',3);
                })
            ->pluck('id');
            $labelIds = Product_label_value::whereIn('product_id',$productIds)->pluck('label_id');
            $res['product_used_labels'] = Product_label::whereIn('id',$labelIds)->get();
            $res['project_id'] = $id;
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
        $res['form_fields'] = Form_field::get();
        $res['location_form_rows'] = New_form::where('form_type',0)->get();
        $res['signoff_form_rows'] = New_form::where('form_type',3)->get();
        $res['test_forms'] = New_form::where('form_type', 1)->get();
        $res['com_forms'] = New_form::where('form_type', 2)->get();
        $res['product_labels'] = Product_label::get();
        $res['qr_option'] = Qr_option::first();
        // for offline mode
        if ( ! $request->project_id) {
            $res['company_customers'] = Company_customer::get();
            $res['all_sites'] = Site::get();
            $res['all_users'] = User::where('status',1)->get();
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

    public function deleteCustomerUser(request $request)
    {
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        Project_user::where('project_id',$id)->where('user_id',$request->user_id)->where('type',3)->delete();
        $project = Project::where('id',$id)->first();
        $remove_user = User::where('id',$request->user_id)->first();
        $client = $remove_user->first_name.' '.$remove_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has removed '.$client.' from the customer user of project: '.$project['project_name'].'.',
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
    public function addCustomerUser(request $request)
    {
        $id = $request->project_id;
        if(strlen($request->project_id)>10)
            $id = Project::where('off_id',$request->project_id)->first()->id;
        Project_user::create(['user_id'=>$request->user_id,'project_id'=>$id,'type'=>3]);
        $project = Project::where('id',$id)->first();
        $add_user = User::where('id',$request->user_id)->first();
        $client = $add_user->first_name.' '.$add_user->last_name;
        $insertnotificationndata = array(
            'notice_type'		=> '3',
            'notice_id'			=> $id,
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has added '.$client.' as a customer user for a new project: '.$project['project_name'].'.',
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
        if($request->hasFile('sign_file')){
            $fileName = time().'.'.$request->sign_file->extension();
            $request->sign_file->move(public_path('upload/file/'), $fileName);
            $project['sign_file']  = $fileName;
        }

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
                //'notification'		=> "Signed Off request was sent to ".$project['customer_user']->first_name." by ".$request->user->first_name.". ".date("d-m-Y H:i:s").'['.$project['project_name'].']',
                //'notification'		=> "Scope of works signed off on ".date("d-m-Y H:i:s")." by ".$request->user->first_name.".",
                'notification'		=> $request->user->first_name.' '.$request->user->last_name." has requested sign off on [".date("d-m-Y H:i:s")."].",
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

        //send mail
        if(Project_user::where('project_id',$request->id)->where('type','3')->count() > 0){
            $content = "";
            if($request->user->user_type <6){
                $customer_users = Project_user::where('project_id',$request->id)->where('type','3')->pluck('user_id');
                foreach($customer_users as $row){
                    $pending_user = User::where('user_id',$row)->first();
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


}
