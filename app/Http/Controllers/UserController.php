<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Cache;
use App\User;
use App\Partner;
use App\Company;
use App\Company_customer;
use App\Notification;
use App\User_feedback;
use JWTAuth;
use JWTAuthException;
use Illuminate\Support\Facades\Validator;
use Mail;

class UserController extends Controller
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    private function getToken($email, $password)
    {
        $token = null;
        try {
            if (!$token = JWTAuth::attempt( ['email'=>$email, 'password'=>$password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token'=>$token
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }
        return $token;
    }

    public function login(Request $request)
    {
        $user = \App\User::where('email', $request->email)->get()->first();
        if (is_null($user)) return response()->json(['status'=>'error','msg'=>'That email does not exist!']);
        //if($user->status !='1') return response()->json(['status'=>'error','msg'=>'Please wait until allow!']);
        if ($user && \Hash::check($request->password, $user->password)) // The passwords match...
        {
            
            $token = self::getToken($request->email, $request->password);
            JWTAuth::setToken($token);
            $user->auth_token = $token;
            $user->rate ='0';
            $user->save();
            if($user->user_type <=4)
            {
                if(Company::where('id',$user->company_id)->count()>0){
                    $user->co_name = str_replace(' ','-',Company::where('id',$user->company_id)->first()->name);
                    $user->logo_img = Company::where('id',$user->company_id)->first()->logo_img;
                    $user->bg_image = Company::where('id',$user->company_id)->first()->bg_image;
                    $user->is_upload = Company::where('id',$user->company_id)->first()->is_upload;
                    $user->back_cover = Company::where('id',$user->company_id)->first()->back_cover;
                    $user->front_cover = Company::where('id',$user->company_id)->first()->front_cover;
                }
                else
                {
                    $user->co_name = '';
                    $user->logo_img = '';
                    $user->bg_image = '';
                    $user->is_upload = '';
                    $user->back_cover = '';
                    $user->front_cover = '';
                }
            }
            else{
                if(Company_customer::where('customer_id',$user->company_id)->count()>0){
                    $company_id = Company_customer::where('customer_id',$user->company_id)->first()->company_id;
                    $user->co_name = str_replace(' ','-',Company::where('id',$company_id)->first()->name);
                    $user->logo_img = Company::where('id',$company_id)->first()->logo_img;
                    $user->bg_image = Company::where('id',$company_id)->first()->bg_image;
                    $user->is_upload = Company::where('id',$company_id)->first()->is_upload;
                    $user->back_cover = Company::where('id',$company_id)->first()->back_cover;
                    $user->front_cover = Company::where('id',$company_id)->first()->front_cover;
                }else{
                    $company_id = '';
                    $user->co_name =  '';
                    $user->logo_img =  '';
                    $user->bg_image =  '';
                    $user->is_upload =  '';
                    $user->back_cover = '';
                    $user->front_cover = '';
                }
            }
            $response = ['status'=>'success', 'data'=>$user];           
        }
        else 
          $response = ['status'=>'error', 'msg'=>'Password is incorrect!'];
        

        return response()->json($response, 201);
    }
    public function register(Request $request)
    { 
        //validate
        $v = Validator::make($request->all(), [
            //company info
            'company_name' => 'required',
            'user_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'postcode' => 'required',

            //user info
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required',

            'job_title' => 'required',
            'mobile' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $v = Validator::make($request->all(), [
            //company info
            'email' => 'email|required|unique:users',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'The User email has been already exist!'
            ]);
        }
        $v = Validator::make($request->all(), [
            'website' => 'required|unique:companies',
            'company_email' => 'email|required|unique:companies'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'The website or company email has been already exist!'
            ]);
        }
        //register country
        
        $fileName = '';
        if($request->hasFile('logo_img')){
            $fileName = time().'logo.'.$request->logo_img->extension();  
            $request->logo_img->move(public_path('upload/img/'), $fileName);
        }

        $response = array();
        $company = new Company;
        $company->name = str_replace(' ','',$request->post("company_name"));
        $company->user_name = $request->post("user_name");
        $company->image = $request->post("image");
        $company->bg_image = $request->post("bg_image");

        $company->logo_img = $fileName;
        $company->website = $request->post("website");
        $company->company_email = $request->post("company_email");
        $company->address = $request->post("address");
        $company->city = $request->post("city");
        $company->postcode = $request->post("postcode");
        $company->company_type = 1;
        $company->status = 1;
        $company->save();     

        //User Register
        $fileName = '';
        if($request->hasFile('profile_pic')){

            $fileName = time().'pic.'.$request->profile_pic->extension();  
            $request->profile_pic->move(public_path('upload/img/'), $fileName);
        }

        $payload = [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'password'=>\Hash::make($request->password),
            'email'=>$request->email,
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'job_title' => $request->job_title,
            'mobile' => $request->mobile,
            'profile_pic' => $fileName,
            'status' => 1,
            'user_type' => 1,
            'auth_token'=> ''
        ];
      
        $user = new \App\User($payload);
        $user->save();
        $token = self::getToken($request->email, $request->password); // generate user token
        JWTAuth::setToken($token);
        if (!is_string($token))  return response()->json(['status'=>'success','msg'=>'Token generation failed'], 201);
        
        $user = \App\User::where('email', $request->email)->get()->first();
        $user->auth_token = $token; // update user token
        $user->save();
        $response = ['status'=>'success', 'msg'=>'You are Registered!'];        
      
        
        return response()->json($response, 201);
    }

    public function CustomerUpdateUser(Request $request)
    {
        //return response()->json($request);
        $v = Validator::make($request->all(), [
            //user info
            'first_name' => 'required',
            'last_name' => 'required',
            'job_title' => 'required',
            'user_type' => 'required',
            'status' => 'required',
            'mobile' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $user_info = array();
        $fileName = '';
        if($request->hasFile('profile_pic')){

            $fileName = time().'pic.'.$request->profile_pic->extension();  
            $request->profile_pic->move(public_path('upload/img/'), $fileName);
            $user_info['profile_pic'] = $fileName;
        }
        
        if($request->password!=""){
            $user_info['password'] = bcrypt($request->password);
        }
        
        if($request->has('customer_id')){
            if(strlen($request->customer_id) > 10){
                $off_data = Company::where('off_id',$request->customer_id)->first();
                $user_info['company_id'] = $off_data->id;
                $user_info['company_name'] = $off_data->name;
            }
            else{
                $user_info['company_id'] = $request->customer_id;
                $user_info['company_name'] = Company::whereId($request->customer_id)->first()->name;
            }
        }
        else{
            $user_info['company_id'] = $request->user->company_id;
            $user_info['company_name'] = $request->user->company_name;
        }
        
        $user_info['email'] = $request->email;
        $user_info['first_name'] = $request->first_name;
        $user_info['last_name'] = $request->last_name;
        $user_info['job_title'] = $request->job_title;
        $user_info['user_type'] = $request->user_type;
        $user_info['status'] = $request->status;
        $user_info['mobile'] = $request->mobile;
        $token = self::getToken($request->email, $request->password);
        $user_info['auth_token'] = $token;
        $id = $request->id;
        if(strlen($request->id) > 10)
            if(User::where('off_id',$request->id)->count() > 0)
                $id = User::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=='null'|| $id=='undefined'|| $id < 1){
            if(!$request->has('password'))
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You must input password!'
                ]);

            $v = Validator::make($request->all(), [
                'email' => 'email|required|unique:users',
            ]);
            if ($v->fails())
            {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'The email has already been taken!'
                ]);
            }
            if(strlen($request->id) > 10)
                $user_info['off_id'] = $request->id;
            User::create($user_info);        
        }
        else{
            $count = User::where('id','<>',$id)->where('email',$request->email)->count() ;
            
            if($count>0)
            {
                $res = array();
                $res['status'] = "error";
                $res['msg'] = 'The email has already been taken!';
                return response()->json($res);
            }
            User::whereId($id)->update($user_info);
        }
        return response()->json(['status' => "success",'msg'=>'Save success']);
        
    }

    public function DeleteUser(Request $request)
    {
        //$request = {'id':{},'company_id':{}}
        if(strlen($request->id)>10){
            user::where(['off_id'=>$request->id])->delete();
        }
        else
            user::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        return response()->json($res);
    }
    
    public function userList(request $request){
        $res = array();
        $res['status'] = 'success';
      
        $company_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
        $res['users'] = User::whereIn('company_id',$company_id)->orwhere('company_id',$request->user->company_id)->where('id','<>',$request->user->id)->get();
       
        return response()->json($res);
    }
    public function userInfo(request $request){
        if($request->has('id'))
            $id = $request->id;
        else
            $id = $request->user->id;
        $res = array();
        $res['status'] = 'success';
        $res['user'] = User::whereId($id)->first();
        return response()->json($res);
    }
    public function totalUserlist(request $request){
        $res = array();
        $res['status'] = 'success';
        if($request->user->user_type == 0){
            $res['users'] = User::where('users.id','<>',$request->user->id)
                            ->leftJoin('companies','users.company_id','=','companies.id')
                            ->select('users.*','companies.company_type')
                            ->get();
            $res['customers'] = Company::get();
        }
        else if($request->user->user_type <6)
        {
            $com_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['users'] = User::where(function($q) use($com_id,$request){
                                    return $q->whereIn('users.company_id',$com_id)
                                        ->orwhere('users.company_id', $request->user->company_id);
                                    })
                                ->where('users.id','<>',$request->user->id)
                                ->leftJoin('companies','users.company_id','=','companies.id')
                                ->select('users.*','companies.company_type')
                                ->get();
            $res['customers'] = Company::whereIn('id',$com_id)
                            ->orwhere('id', $request->user->company_id)->get();
        }
        else{
            $res['users'] = User::Where('users.company_id',$request->user->company_id)
                                ->where('users.id','<>',$request->user->id)
                                ->leftJoin('companies','users.company_id','=','companies.id')
                                ->select('users.*','companies.company_type')
                                ->get();
            $res['customers'] = [];
        }
        
        return response()->json($res);
    }
    public function saveUser(request $request){
        $v = Validator::make($request->all(), [
            //user info
            'first_name' => 'required',
            'last_name' => 'required',
            'job_title' => 'required',
            'user_type' => 'required',
            'status' => 'required',
            'mobile' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $user_info = array();
        $fileName = '';
        if($request->hasFile('profile_pic')){

            $fileName = time().'pic.'.$request->profile_pic->extension();  
            $request->profile_pic->move(public_path('upload/img/'), $fileName);
            $user_info['profile_pic'] = $fileName;
        }
        
        if($request->password!=""){
            $user_info['password'] = bcrypt($request->password);
        }
       
        $user_info['email'] = $request->email;
        $user_info['first_name'] = $request->first_name;
        $user_info['last_name'] = $request->last_name;
        $user_info['job_title'] = $request->job_title;
        $user_info['user_type'] = $request->user_type;
        $user_info['status'] = $request->status;
        $user_info['mobile'] = $request->mobile;
        $token = self::getToken($request->email, $request->password);
        $user_info['auth_token'] = $token;
        $id = $request->user->id;
        
        
        $count = User::where('id','<>',$id)->where('email',$request->email)->count() ;
        
        if($count>0)
        {
            $res = array();
            $res['status'] = "error";
            $res['msg'] = 'The email has already been taken!';
            return response()->json($res);
        }
        User::whereId($id)->update($user_info);
        $user = User::whereId($id)->first();
        
        return response()->json(['status' => "success",'msg'=>'Save success','user'=>$user]);
    }
    public function checkValidate(request $request){
        $res = array();
        $user = User::where('invite_code', $request->invite_code)->first();
        if($user){
            $res['status'] = 'success';
            $res['invitecode'] = $request->invite_code;
            $res['user'] = $user;
        }
        else{
            $res['status'] = 'error';
            $res['invitecode'] = $request->invite_code;
            $res['msg'] = 'InviteCode is not valid!';
        }

        return response()->json($res);
    }
    public function companyUserRegister(request $request){
        
        $v = Validator::make($request->all(), [
            //user info
            'first_name' => 'required',
            'last_name' => 'required',
            'job_title' => 'required',
            'password' => 'required',
            'mobile' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $user_info = array();
        $user_info['email'] = $request->email;
        $user_info['first_name'] = $request->first_name;
        $user_info['last_name'] = $request->last_name;
        $user_info['job_title'] = $request->job_title;
        $user_info['password'] = bcrypt($request->password);
        $user_info['status'] = 1;
        $user_info['mobile'] = $request->mobile;
        $token = self::getToken($request->email, $request->password);
        $user_info['auth_token'] = $token;
        $fileName = '';
        if($request->hasFile('profile_pic')){

            $fileName = time().'pic.'.$request->profile_pic->extension();  
            $request->profile_pic->move(public_path('upload/img/'), $fileName);
            $user_info['profile_pic'] = $fileName;
        }
        User::where('email',$request->email)->update($user_info);
        $res = array();
        $res['status'] = "success";
        return response()->json($res);
    }
    public function forgot(request $request)
    {
        $res = array();
        $pending_user = User::where('email',$request->email)->first();
       
        if($pending_user){
            $token = $pending_user->auth_token;

            $to_name = $request->name;
            $to_email = $pending_user['email'];
            $content = 'Did you Forgot password? Please click button if you want to change your password!';
            $invitationURL = "https://app.sirvez.com/register/".$token;
            $data = ['name'=>$to_name, "content" => 'Forgot password',"title" =>'Forgot password',"description" =>$content,"img"=>'',"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to change password'];
            Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez support team');
            });
            $res['status'] = "success";
        }
        else{
            $res['status'] = "error";
        }
        return response()->json($res);
    }
    public function checkToken(request $request){
        $res = array();
        $user = User::where('auth_token',$request->token)->first();
        if($user){
            $res['status'] = 'success';
            $res['user'] = $user;
        }
        else
            $res['status'] = 'error';
        return response()->json($res);
    }
    public function changePassword(request $request){
        $res = array();
        User::where('email',$request->email)->update(['password'=>bcrypt($request->password)]);
        $user = User::where('email',$request->email)->first();
        $res['data'] = $user;
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function sendFeedback(request $request){
        $feedback = array();
        $feedback['feedback_email'] = $request->user->email;
        $feedback['feedback_type'] = $request->feedback_type;
        $feedback['feedback_msg'] = $request->feedback_msg;
        $feedback['user_id'] = $request->user->id;
        $feedback['is_read'] = 0;
        User_feedback::create($feedback);

        $pending_user = User::where('id',1)->first();
        $to_name = $pending_user['first_name'];
        $to_email = $pending_user['email'];
        $content = $request->user->first_name.' '.$request->user->last_name. ' remains feedback.';
        $invitationURL = "https://app.sirvez.com/app/feedbacks";
        $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>'Feedback',"description" =>$request->feedback_msg,"img"=>'',"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view feedback'];
        Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('sirvez notification.');
            $message->from('support@sirvez.com','sirvez support team');
        });

        $res['status'] = 'success';
        return response()->json($res);
    }
    public function deleteFeedback(request $request){
        $id = $request->id;
        if(strlen($request->id) > 10){
            $id = User_feedback::where('off_id',$request->id)->first()->id;
        }
        else
            $id = $request->id;
        User_feedback::whereId($id)->delete();
    }
    public function getFeedbackList(request $request){
        $res = array();
        $res['feedbacks'] = User_feedback::orderBy('user_feedbacks.id','desc')
                        ->leftJoin('users','user_feedbacks.user_id','=','users.id')
                        ->select('user_feedbacks.*','users.profile_pic','users.first_name','users.last_name')
                        ->get();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setFeedback(request $request){
        User::where('id',$request->id)->update(['is_feedback'=>$request->is_feedback]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function userOnlineStatus()
    {
        $res = array();
        $online_users = array();
        $users = User::get();
        foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id))
                array_push($online_users,$user->id);
        }
        $res['result'] = 'success';
        $res['online_users'] = $online_users;
        return response()->json(res);
    }
    public function partnerlist(request $request)
    {        
        $res = array();
        $res['status'] = 'success';
        if($request->user->user_type == 0){
            $res['users'] = Partner::leftJoin('users','users.id','=','partners.partner_id')
                            ->leftJoin('companies','partners.company_id','=','companies.id')
                            ->select('users.*','companies.company_type','companies.name as company_name','companies.website')
                            ->get();
            $res['customers'] = Company::get();
        }
        else if($request->user->user_type <6)
        {
            $userIds = Partner::where('company_id',$request->user->company_id)->select('partner_id')->distinct()->pluck('partner_id');
            $res['users'] = Partner::where('partners.company_id',$request->user->company_id)
                                    ->leftJoin('users','users.id','=','partners.partner_id')
                                    ->leftJoin('companies','partners.company_id','=','companies.id')
                                    ->select('users.*','companies.company_type','companies.name as company_name','companies.website')
                                    ->get();
            $comId = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['customers'] = Company::whereIn('id',$com_id)
                            ->orwhere('id', $request->user->company_id)->get();
        }
        return response()->json($res);
    }
    public function setModifySite(request $request){
        User::where('id',$request->id)->update(['modify_site'=>$request->modify_site]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setModifyLocation(request $request){
        User::where('id',$request->id)->update(['modify_location'=>$request->modify_location]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setModifyProduct(request $request){
        User::where('id',$request->id)->update(['modify_product'=>$request->modify_product]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setModifyTask(request $request){
        User::where('id',$request->id)->update(['modify_task'=>$request->modify_task]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setModifyUser(request $request){
        User::where('id',$request->id)->update(['modify_user'=>$request->modify_user]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function updatePartnerUser(Request $request)
    {
        $v = Validator::make($request->all(), [
            //user info
            'email' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $user_info = array();
        if(Partner::where('company_id',$request->user->company_id)->where('partner_id',$request->partner_id)->count()>0)
            return response()->json([
                'status' => 'error',
                'msg' => 'This partner is already exist!'
            ]);
        
        $user_info = User::where('email',$request->email)->first();
        if(!$user_info)
            return response()->json([
                'status' => 'error',
                'msg' => 'That email is not exist in Sirvez!'
            ]);
        $user_info['modify_site'] = $request->modify_site;
        $user_info['modify_location'] = $request->modify_location;
        $user_info['modify_product'] = $request->modify_product;
        $user_info['modify_task'] = $request->modify_task;
        $user_info['modify_user'] = $request->modify_user;
        $user_info->save();
        $id = $request->id;
        if(!isset($id) || $id==""|| $id=='null'|| $id=='undefined'){
            Partner::create(['user_id'=>$request->user->id,'partner_id'=>$user_info->id,'company_id'=>$request->user->company_id]);        
        }
        $company_name = Company::where('id',$request->user->company_name)->first()->name;
        $to_name = $user_info->name;
        $to_email = $user_info['email'];
        $content = $request->user->first_name.' '.$request->user->last_name.' from '.$company_name.' wants to partner with '.$company_name.'. Please click here to confirm partnership.';
        $invitationURL = "https://app.sirvez.com/app/dashboard";
        $data = ['name'=>$to_name, "content" => 'Invite Partner',"title" =>'Dear '.$to_name,"description" =>$content,"img"=>'',"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to confirm partnership'];
        Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('sirvez notification.');
            $message->from('support@sirvez.com','sirvez support team');
        });
        return response()->json(['status' => "success",'msg'=>'Save success']);
    }
    public function deletePartner(request $request){
        Partner::where('user_id',$requeset->id)
                ->where('company_id',$request->user->company_id)
                ->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'You deleted selected partner!'
        ]);
    }
}
