<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Company_customer;
use App\Site;
use App\User;
use App\Task;
use App\Site_room;
use App\Project;
use App\Product;
use App\Room;
use App\Product_label;
use App\Product_sign;
use App\Product_label_value;
use App\Qr_option;
use App\New_form;
use App\Task_label_value;
use App\Project_user;
use App\Notification;
use App\Customer_partner;
use App\Partner;
//use Illuminate\Support\Facades\Mail;
use Mail;
use Illuminate\Support\Facades\Validator;
class CompanyCustomerController extends Controller
{
    public function addCompanyCustomer(Request $request){


        $v = Validator::make($request->all(), [
            //company info
            'company_name' => 'required',
            'manager' => 'required'
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $company = array();
        $id = $request->id;
        $flag = 0;
        if($request->hasFile('logo_img')){
            $fileName = time().'.'.$request->logo_img->extension();
            $request->logo_img->move(public_path('upload/img/'), $fileName);
            $company['logo_img']  = $fileName;
        }
        //$company['name'] = str_replace(' ','',$request->post("company_name"));
        $company['name'] = $request->post("company_name");
        $company['website']  = $request->post("website");
        $company['parent_id']  = $request->post("parent_id");
        $company['company_email']  = $request->post("company_email");
        $company['address']  = $request->post("address");
        $company['city']  = $request->post("city");
        $company['postcode']  = $request->post("postcode");
        $company['company_type']  = 3;
        $company['status']  = 1;
        $company['manager']  = $request->post("manager");

        //return response()->json(strlen($request->id));
        if(strlen($request->id) > 10)
            if(company::where('off_id',$request->id)->count() > 0)
                $id = company::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){
            $count = 0;
            if($request->website)
                $count += Company::where('website',$request->website)->count();
            // if($request->company_email)
            //     $count+= Company::where('company_email',$request->company_email)->count();

            if($count>0)
            {
                $res = array();
                $res['status'] = "error";
                $res['msg'] = 'The website has already been taken!';
                return response()->json($res);
            }


            // $v = Validator::make($request->all(), [
            //     'website' => 'required|unique:companies',
            //     'company_email' => 'email|required|unique:companies'
            // ]);
            // if ($v->fails())
            // {
            //     return response()->json([
            //         'status' => 'error',
            //         'msg' => 'The website or company email has already been taken!'
            //     ]);
            // }
            if (strlen($request->id) > 10)
                $company['off_id']  = $request->id;
            $company = Company::create($company);
            $id = $company->id;
            //User::whereId($company['manager'])->update(['company_id'=>$id,'user_type'=>3]);
            $flag = 1;
        }
        else{
            $count = 0;
            if($request->website)
                $count += Company::where('id','<>',$id)->where('website',$request->website)->count();
            // if($request->company_email)
            //     $count += Company::where('id','<>',$id)->where('company_email',$request->company_email)->count();
            $sel_company = Company::whereId($id)->first();
            //User::whereId($sel_company['manager'])->update(['company_id'=>$request->user->company_id,'user_type'=>1]);
            //User::whereId($company['manager'])->update(['company_id'=>$id,'user_type'=>3]);
            if($count>0)
            {
                $res = array();
                $res['status'] = "error";
                $res['msg'] = 'The website has already been taken!';
                return response()->json($res);
            }

            company::whereId($request->id)->update($company);
        }

        //insert company_customer
        $companyCustomer = array();
        $companyCustomer['company_id'] = $request->user->company_id;
        $companyCustomer['customer_id'] = $id;
        $action = "";

        if($flag > 0){
            $companyCustomer['created_by'] = $request->user->id;
            $companyCustomer = Company_customer::create($companyCustomer);
            $action = "created";
        }
        else{
            $companyCustomer =Company_customer::where('customer_id',$request->id)->first();
            $companyCustomer['updated_by'] = $request->user->id;
            $companyCustomer->save();
            $action = "updated";
        }
        //insert notification
        //$notice_type ={1:pending_user,2:createcustomer}

        $insertnotificationndata = array(
            'notice_type'		=> '2',
            'notice_id'			=> $companyCustomer->id,
            //'notification'		=> $company['name'].' have been '.$action.' by  '.$request->user->first_name.' ('.$request->user->company_name.').',
            'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new company: '.$company['name'].'.',
            'created_by'		=> $request->user->id,
            'company_id'		=> $id,
            'created_date'		=> date("Y-m-d H:i:s"),
            'is_read'	    	=> 0,
        );

        Notification::create($insertnotificationndata);

        //insert site
        if($request->is_site)
        {
            $site['company_id'] = $id;
            $site['site_name'] = $request->post("company_name")." Head Office";
            $site['address'] = $request->post("address");
            $site['city'] = $request->post("city");
            $site['postcode'] = $request->post("postcode");
            $site['created_by'] = $request->user->id;
            $site['updated_by'] = $request->user->id;
            if($flag > 0){
                if (strlen($request->id)>10)
                    $site['off_id'] = $request->site_off_id;
                $site = Site::create($site);
            }
            else
                $site = Site::where('company_id',$request->id)->update($site);

        }


        $response = ['status'=>'success', 'id'=>$id];
        return response()->json($response);

    }

    public function getCompanyCustomer(Request $request){

        $res = array();
        $companyid = Company_customer::where(['company_id'=>$request->user->company_id])->pluck('customer_id');

        $customers = array();
        $company_array = Company::whereIn('id',$companyid)->orderBy('id','desc')->get();
        foreach($company_array as $key => $row){
            $row['user_count'] = User::where('company_id',$row['id'])->where('status',1)->count();
            $row['project_count'] = Project::where('company_id',$row['id'])->count();
            $row['site_count'] = Site::where('company_id',$row['id'])->count();
            $row['room_count'] = Site_room::where('company_id',$row['id'])->count();
            //// partners /////
            $partner_ids = Customer_partner::where('customer_id', $row->id)->pluck('partner_id');
            $row['partners'] = Partner::leftJoin('companies', 'companies.id', '=', 'partners.partner_id')
                            ->WhereIn('partners.id', $partner_ids)
                            ->select('partners.*', 'companies.logo_img', 'companies.company_email', 'companies.name', 'companies.website')
                            ->get();                                

            $customers[$key] = $row;
        }
        // $customers = Company::with(['company_customers'=> function ($query) use($userid) {
        //     $query->with('company')->where('company_id', $userid);
        // }])->get();
        $res["customers"] = $customers;
        $res['account_manager'] = User::whereIn('user_type',[0,1,3])->where('status',1)->get();
        $res['parent_customers'] = Company::get();
        $res['status'] = "success";
        return response()->json($res);
    }
    public function DeleteCompanyCustomer(Request $request){
        $company_id = $request->id;
        if(strlen($request->id)>10){
            $company_id = Company::where('off_id',$company_id)->first()->id;
        }
        
        Company::whereId($company_id)->delete();
        Company_customer::where('company_id',$company_id)->delete();
        Project::where('company_id',$company_id)->delete();
        Site::where('company_id',$company_id)->delete();
        Site_room::where('company_id',$company_id)->delete();
        
        $res['status'] = "success";
        return response()->json($res);
    }
    public function CompanyCustomerInfo(Request $request){
        $company_id = $request->id;
        $res = array();
        $res['status'] = 'success';
        $res['company'] = Company::whereId($company_id)->first();
        $comIds = Company_customer::where('company_id',$company_id)->pluck('customer_id');
        
        $res['users'] = User::whereIn('company_id',$comIds)->orWhere('company_id',$company_id)->whereIn('status',[1,3])->get();
        $projects = Project::whereIn('company_id',$comIds)->orWhere('company_id',$company_id)->orderBy('id','desc')->get();
        if(!is_null($projects)){
            foreach($projects as $key=>$project){
                if(User::whereId($project->manager_id)->count() > 0)
                    $projects[$key]['account_manager'] = User::whereId($project->manager_id)->first()->first_name;
                else
                    $projects[$key]['account_manager'] = '';
                $projects[$key]['rooms'] = Room::where('project_id',$project->id)->count();
            }
        }
        $res['projects'] = $projects;
        $sites = Site::whereIn('company_id',$comIds)->orWhere('company_id',$company_id)->orderBy('id','desc')->get();
        foreach($sites as $key=>$site){

            $sites[$key]['rooms'] = Site_room::where('site_id',$site->id)->count();
            $sites[$key]['projects'] = Project::where('company_id',$company_id)->count();
        }

        $res['sites'] =$sites;
        $res['rooms'] = Site_room::whereIn('site_rooms.company_id',$comIds)->orWhere('site_rooms.company_id',$company_id)
            ->leftJoin('sites','site_rooms.site_id','=','sites.id')->select('site_rooms.*','sites.site_name')->orderBy('id','desc')->get();
        $roomIdx = Room::whereIn('company_id',$comIds)->orWhere('company_id',$company_id)->pluck('id');
        $products = Product::whereIn('products.room_id',$roomIdx)->where('action','<','3')->get();
        $productIds = Product::whereIn('products.room_id',$roomIdx)->where('action','<','3')->pluck('id');
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
            $products[$key]['company_info'] = Company::whereId($company_id)->first();
            $products[$key]['website'] = Company::whereId($company_id)->first()->website;
            $products[$key]['company_name'] = Company::whereId($company_id)->first()->name;
            $products[$key]['sign_in'] = Product_sign::where('product_signs.product_id',$product->id)
                                                    ->leftJoin('users','users.id','=','product_signs.user_id')
                                                    ->select('product_signs.*','users.first_name','users.profile_pic')
                                                    ->get();
            $products[$key]['label_value'] = Product_label_value::where('product_id',$product->id)->pluck('label_id');
            $products[$key]['client_name'] = Project_user::where(['project_users.project_id'=>$products[$key]['project_id'],'project_users.type'=>'3'])
                                    ->leftjoin('users','users.id','=','project_users.user_id')
                                    ->select('users.*')
                                    ->get();
            $products[$key]['install_date'] = date('d-m-Y',strtotime($project['survey_start_date']));

            if($product['action'] ==0)
                $products[$key]['product_action'] = "New Product";
            else if($product['action'] ==1)
                $products[$key]['product_action'] = "Dispose";
            else
                $products[$key]['product_action'] = "Move To Room";
        }
        $res['products'] = $products;
        $tasks = Task::whereIn('company_id',$comIds)->orWhere('company_id',$company_id)->orderBy('id','desc')->get();
        foreach($tasks as $key=>$row){
            $tasks[$key]['assign_to'] = Project_user::leftJoin('users','users.id','=','project_users.user_id')->where(['project_users.project_id'=>$row->id,'project_users.type'=>'2'])->pluck('users.first_name');
        }
        $res['tasks'] = $tasks;
        $res['childs'] = Company::where('companies.parent_id',$request->id)
                        ->leftJoin('users','users.id','=','companies.manager')
                        ->select('companies.*','users.first_name as manager_name')
                        ->get();
        $labelIds = Product_label_value::whereIn('product_id',$productIds)->pluck('label_id');
        $res['product_used_labels'] = Product_label::whereIn('id',$labelIds)->get();
        $res['product_labels'] = Product_label::get();
        $res['task_assign_to'] = User::where('company_id',$company_id)->whereIn('user_type',[0,1,3])->where('status',1)->select('id','first_name','last_name','profile_pic')->get();
        $res['test_forms'] = New_form::where('form_type', 1)->get();
        $res['com_forms'] = New_form::where('form_type', 2)->get();
        $res['qr_option'] = Qr_option::first();
        $partner_ids = Customer_partner::where('customer_id', $company_id)->pluck('partner_id');

        $partners = Partner::leftJoin('companies', 'companies.id', '=', 'partners.partner_id')
                        ->WhereIn('partners.id', $partner_ids)
                        ->select('partners.*', 'companies.logo_img', 'companies.company_email', 'companies.name', 'companies.website')
                        ->get();
        $res['partners'] = $partners;

        return response()->json($res);
    }
    public function getCustomerInfo(Request $request){
        $res['customers'] = Company::get();
        if ($request->has('id')) {
            $customer_id = $request->id;

            $res = array();
            $res['status'] = 'success';
            $res['company'] = Company::whereId($customer_id)->first();
            $res['customers'] = Company::where('id','<>',$customer_id)->get();
        }

        $res['account_manager'] = User::whereIn('user_type',[0,1,3])->where('status',1)->where('company_id',$request->user->company_id)->get();
       
        return response()->json($res);
    }
    public function userList(Request $request){
        $res = array();
        $res['status'] = 'success';
        if($request->has('company_id'))
        {
            $res['users'] = User::where('company_id',$request->company_id)->where('id','<>',$request->user->id)->where('status',1)->leftJoin('companies','users.company_id','=','companies.id')->select('users.*','companies.name')->get();
            $res['customers'] = Company::where('id',$request->company_id)->get();
        }
        else{
            $company_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['customers'] = Company::whereIn('id',$company_id)->get();
            $res['users'] = User::where('users.company_id',$request->user->company_id)->where('users.id','<>',$request->user->id)->where('users.status',1)->leftJoin('companies','users.company_id','=','companies.id')->select('users.*','companies.name')->get();
        }
        return response()->json($res);
    }
    public function pendingUser(request $request){
        $success_key = array();
        foreach($request->pendingUser as $key => $pending_user){
            $v = Validator::make($pending_user, [
                //company info
                'customer' => 'required',
                'email' => 'email|required|unique:users',
                'first_name' => 'required',
                'user_role' => 'required'
            ]);
            if ($v->fails())
            {
                $success_key[$key] = 0;
                continue;
            }
            $success_key[$key] = 1;
            if(Company::whereId($pending_user['customer'])->count()>0)
                $company_name = Company::whereId($pending_user['customer'])->first()->name;
            else
                $company_name = Company::whereId($request->user->company_id)->first()->name;
            //add usertable new user by pending
            $user = array();
            $user['email'] = $pending_user['email'];
            $user['first_name'] = $pending_user['first_name'];
            $user['user_type'] = $pending_user['user_role'];
            $user['company_id'] = $pending_user['customer'];
            $user['company_name'] = $company_name;
            $invite_code = bcrypt($pending_user['email'].$company_name);
            $user['invite_code'] = str_replace('/', '___', $invite_code);
            $user['status'] = '0';
            $res['status'] = "success";

            $user = User::create($user);
            $user_role = ['1'=>'super admin','2'=> 'engineer','3'=>'account admin','4'=>'admin','6'=>'nomal'];

            $insertnotificationndata = array(
                'notice_type'		=> '1',
                'notice_id'			=> $user->id,
                'notification'		=> $user['first_name'].' '.$user['last_name'].' has added '.$pending_user['first_name'].' as '.$user_role[$pending_user['user_role']].' by  '.$request->user->first_name.' '.$request->user->last_name.' in company: '.$company_name.'.',
                'created_by'		=> $request->user->id,
                'company_id'		=> $user['company_id'],
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
            );
            Notification::create($insertnotificationndata);
            $invitationURL = env('APP_URL')."/company/usersignup/".$user['invite_code'];

            //sending gmail to user
            $to_name = $pending_user['first_name'];
            $to_email = $pending_user['email'];
            $data = ['name'=>$pending_user['first_name'], "pending_user" => $pending_user,'user_info'=>$request->user,'invitationURL'=>$invitationURL];
            Mail::send('mail', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez support team invite you. please join our site.');
                $message->from('support@sirvez.com','support team');
            });
        }

        $res['status'] = "success";
        $res['success_key'] = $success_key;

        return response()->json($res);
    }
    public function getDashboard(Request $request){
        $res = array();
        $res['status'] = "success";
        $id = $request->user->company_id;
        if($request->user->user_type <=3){}
        $customerId = Company_customer::where('company_id',$id)->pluck('customer_id');
        $projects = Project::whereNull('projects.signed_off')
                                ->where(function($q) use($customerId,$id){
                                    return $q->whereIn('projects.company_id',$customerId)
                                    ->orwhere('projects.company_id',$id);
                                })->leftJoin('companies','companies.id','=','projects.company_id')
                                ->select('projects.*','companies.name as company_name')
                                ->get();
        foreach($projects as $key => $row){
            $projects[$key]['survey_start_date'] = date('d-m-Y', strtotime($row['survey_start_date']));
            $projects[$key]['room_count'] = Room::where('project_id',$row['id'])->count();
        }
        $res['project_list'] = $projects;
        $res['lives'] = Project::whereIn('company_id',$customerId)->orwhere('company_id',$id)->count();
        $res['messages'] = Notification::whereIn('company_id',$customerId)->orwhere('company_id',$id)->count();
        $recent_messages = Notification::where('notifications.notice_type','>',2)
                                                ->where(function($q) use($customerId,$id){
                                                    return $q->whereIn('notifications.company_id',$customerId)
                                                    ->orwhere('notifications.company_id',$id);
                                                })->orderBy('id','desc')->take(10)
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
        $res['recent_messages'] = $recent_messages;
        $res['tasks'] = Task::whereIn('company_id',$customerId)->orwhere('company_id',$id)->count();
        $tasks= Task::where('tasks.favourite',1)
                                    ->where(function($q) use($customerId,$id){
                                        return $q->whereIn('tasks.company_id',$customerId)
                                        ->orwhere('tasks.company_id',$id);
                                    })
                                    ->leftJoin('projects','projects.id','=','tasks.project_id')
                                    ->leftJoin('companies','companies.id','=','tasks.company_id')
                                    ->leftjoin('users','users.id','=','tasks.created_by')
                                    ->select('tasks.*','projects.project_name','companies.name as company_name','users.profile_pic')
                                    ->get();
        foreach($tasks as $key=>$row){
            $tasks[$key]['label_value'] = Task_label_value::where('task_id',$row->id)->pluck('label_id');
            $tasks[$key]['client_users'] = Project_user::where(['project_users.project_id'=>$row->id,'type'=>'2'])->pluck('user_id');
            $tasks[$key]['assign_users'] = Project_user::where(['project_users.project_id'=>$row->id,'type'=>'2'])
            ->leftJoin('users','users.id','=','project_users.user_id')->get();
        }
        $res['tasks_favourite'] =$tasks;
        //$res['project_list'] = array();
        // $res['lives'] = array();
        //$res['messages'] = array();
        // $res['recent_messages'] = array();
        // $res['tasks'] = array();
        // $res['tasks_favourite'] = array();

        return response()->json($res);
    }
    
    public function setFavourite(request $request)
    {
        Company::whereId($request->id)->update(['favourite'=>$request->favourite]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function partnerlist(request $request) {
        $partner_ids = Customer_partner::where('customer_id', $request->id)->pluck('partner_id');

        $partners = Partner::leftJoin('companies', 'companies.id', '=', 'partners.partner_id')
                        ->WhereIn('partners.id', $partner_ids)
                        ->select('partners.*', 'companies.logo_img', 'companies.company_email', 'companies.name', 'companies.website')
                        ->get();
        $res = array();
        $res['partners'] = $partners;
        $res['status'] = 'success';

        $company_id = Company_customer::where('customer_id',$request->user->company_id)->pluck('id')->toArray();
        array_push($company_id, $request->user['company_id']);
        $allpartners = Partner::leftJoin('companies', 'companies.id', '=', 'partners.partner_id')
                        ->where('is_allowed', 2)
                        ->WhereIn('company_id', $company_id)
                        ->select('partners.*', 'companies.logo_img', 'companies.company_email', 'companies.name', 'companies.website')
                        ->get();
        $res['allpartners'] = $allpartners;

        return response()->json($res);
    }

    public function addPartner(request $request) {
        $res = array();
        $res['status'] = 'success';
        $partner = Customer_partner::create([
            'customer_id' => $request->customer_id,
            'partner_id' => $request->partner_id
        ]);
        $res['partner'] = $partner;
        return response()->json($res);
    }

    public function deletePartner(request $request) {
        $res = array();
        $res['status'] = 'success';
        Customer_partner::where([
            'customer_id' => $request->customer_id,
            'partner_id' => $request->partner_id
        ])->delete();
        return response()->json($res);
    }
}
