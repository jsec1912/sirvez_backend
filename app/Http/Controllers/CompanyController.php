<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Company_customer;
use App\Site;
use App\Project;
use App\Room;
use App\Site_room;
use App\User;
use App\Partner;
use Illuminate\Support\Facades\Validator;
use Mail;

class CompanyController extends Controller
{
    public function saveCompany(request $request){

        $v = Validator::make($request->all(), [
            //company info
            //'name' => 'required',
            // 'address' => 'required',
            // 'city' => 'required',
            // 'postcode' => 'required',
            // 'telephone' => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $company = array();
        $id = $request->user->company_id;
        if($request->hasFile('logo_img')){
            $fileName = time().'logo.'.$request->logo_img->extension();
            $request->logo_img->move(public_path('upload/img/'), $fileName);
            $company['logo_img']  = $fileName;
        }
        if($request->hasFile('bg_image')){
            $fileName = time().'bg.'.$request->bg_image->extension();
            $request->bg_image->move(public_path('upload/img/'), $fileName);
            $company['bg_image']  = $fileName;
        }
        if($request->hasFile('front_cover')){
            $fileName = $request->post("name").'_front.'.$request->front_cover->extension();
            $request->front_cover->move(public_path('upload/img/'), $fileName);
            $company['front_cover']  = $fileName;
        }
        if($request->hasFile('back_cover')){
            $fileName = $request->post("name").'_back.'.$request->back_cover->extension();
            $request->back_cover->move(public_path('upload/img/'), $fileName);
            $company['back_cover']  = $fileName;
        }
        if ($request->has('name')) {
            $company['name'] = $request->name;
        }
        if ($request->has('website')) {
            $company['website']  = $request->website;
        }
        if ($request->has('parent_id')) {
            $company['parent_id']  = $request->parent_id;
        }
        //$company['manager']  = $request->post("manager");
        if ($request->has('company_email')) {
            $company['company_email']  = $request->company_email;
        }
        if ($request->has('address')) {
            $company['address']  = $request->address;
        }
        if ($request->has('address2')) {
            $company['address2']  = $request->address2;
        }
        if ($request->has('city')) {
            $company['city']  = $request->city;
        }
        if ($request->has('postcode')) {
            $company['postcode']  = $request->postcode;
        }
        if ($request->has('country')) {
            $company['country']  = $request->country;
        }
        //$company['status']  = $request->post("status");
        if ($request->has('telephone')) {
            $company['telephone']  = $request->telephone;
        }
        if ($request->has('is_upload')) {
            $company['is_upload']  = $request->is_upload;
        }

        $count = 0;
        if ($request->has('website')) {
            Company::where('id','<>',$id)->where('website',$request->website)->count();
        }

        if($count>0) {
            $res = array();
            $res['status'] = "error";
            $res['msg'] = 'The website has already been taken!';
            return response()->json($res);
        }
        
        company::whereId($id)->update($company);
        return response()->json(['status'=>'success','company'=>$company]);



    }
    public function getCompanyInfo(request $request){
        $id = $request->user->company_id;
        $res = array();
        $res['status'] = 'success';
        $company = Company::whereId($id)->first();
        $res['company'] = $company

        $sites = Site::where('company_id',$id)->orderBy('id','desc')->get();
        foreach($sites as $key=>$site){
            $sites[$key]['rooms'] = Site_room::where('site_id',$site->id)->count();
            $sites[$key]['projects'] = Project::where('company_id',$id)->count();
        }

        $res['sites'] =$sites;
        $res['rooms'] = Site_room::where('site_rooms.company_id',$id)
            ->leftJoin('sites','site_rooms.site_id','=','sites.id')->select('site_rooms.*','sites.site_name')->orderBy('id','desc')->get();
        return response()->json($res);
    }
    public function getCompanyImg(request $request){
        $name = str_replace('-',' ',$request->company_name);
        $res = array();
        $res['company'] = Company::where('name',$name)->first();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeLogo(request $request){
        $fileName = time().'logo.'.$request->logo_img->extension();
        $request->logo_img->move(public_path('upload/img/'), $fileName);
        $company['logo_img']  = $fileName;
        Company::whereId($request->company_id)->update($company);
        $res['status'] = 'success';
        $res['logo_img'] = $fileName;
        return response()->json($res);
    }
    public function changeName(request $request) {
        Company::whereId($request->company_id)->update([
            'name' => $request->company_name
        ]);
        $res['name'] = $request->company_name;
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function setPrimaryUser(request $request){
        $id = $request->id;
        if(strlen($request->id) > 10)
            if(company::where('off_id',$request->id)->count() > 0)
                $id = company::where('off_id',$request->id)->first()->id;
            else $id = '';
        company::where('id',$id)->update(['primary_user'=>$request->primary_id]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
            
    }
    public function customerList(request $request){
        $res = array();
        $customers= Company::where('company_type',1)->get();
        foreach($customers as $key=>$customer){
            $customerIds = Company_customer::where('company_id',$customer->id)->pluck('customer_id');
            $customers[$key]['customers'] = User::where('company_id',$customer->id)->count();
            $customers[$key]['users'] = Company_customer::where('company_id',$customer->id)->count();
            $customers[$key]['customer_users'] = User::whereIn('company_id',$customerIds)->count();
            $customers[$key]['projects'] = Project::whereIn('company_id',$customerIds)
                                        ->orWhere('company_id',$customer->id)->count();
            $customers[$key]['rooms'] = Room::whereIn('company_id',$customerIds)
                                        ->orWhere('company_id',$customer->id)->count();
            $customers[$key]['sites'] = Site::whereIn('company_id',$customerIds)
                                        ->orWhere('company_id',$customer->id)->count();
        }
        $res['customers'] = $customers;
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function updatePartnerCompany(request $request) {
        $v = Validator::make($request->all(), [
            //user info
            'email' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You need to input a user email.'
            ]);
        }
        $user = User::where('id', $request->user->id)->first();
        if(!$user)
        return response()->json([
            'status' => 'error',
            'msg' => 'That user is not Sirvez user!'
        ]);
        if (User::where('email', $request->email)->count() == 0) {
            return response()->json([
                'status' => 'error',
                'msg' => 'The email you entered is not a registered user.'
            ]);
        }
        $user = User::where('email', $request->email)->first();
        if($user->user_type<4)
            $company_id = $user->company_id;
        else
            $company_id = Company_customer::where('customer_id',$user->company_id)->first()->company_id;
        if (Partner::where([
            'company_id' => $request->user->company_id,
            'partner_id' => $company_id,
        ])->count() > 0) {
            $partner = Partner::where(['company_id' => $request->user->company_id,'partner_id' => $company_id])
            ->update(
                [
                    'from_user' => $request->user->email,
                    'to_user' => $user->email,
                    'is_allowed' => '0',
                    'modify_site' => $request->modify_site,
                    'modify_location' => $request->modify_location,
                    'modify_product' => $request->modify_product,
                    'modify_task' => $request->modify_task,
                    'modify_user' => $request->modify_user,
                    'modify_form' => $request->modify_form,
                    'modify_sticker' => $request->modify_sticker
                ]
            );
            $partner = Partner::where(['company_id' => $company_id,'partner_id' => $request->user->company_id])
            ->update(
                [
                    'from_user' => $user->email,
                    'to_user' => $request->user->email,
                    'is_allowed' => '1',
                    'modify_site' => $request->modify_site,
                    'modify_location' => $request->modify_location,
                    'modify_product' => $request->modify_product,
                    'modify_task' => $request->modify_task,
                    'modify_user' => $request->modify_user,
                    'modify_form' => $request->modify_form,
                    'modify_sticker' => $request->modify_sticker
                ]
            );
           
        }
        else{
            Partner::create([
                'from_user' => $request->user->email,
                'to_user' => $user->email,
                'company_id' => $request->user->company_id,
                'partner_id' => $company_id,
                'is_allowed' => '0',
                'modify_site' => $request->modify_site,
                'modify_location' => $request->modify_location,
                'modify_product' => $request->modify_product,
                'modify_task' => $request->modify_task,
                'modify_user' => $request->modify_user,
                'modify_form' => $request->modify_form,
                'modify_sticker' => $request->modify_sticker
            ]);
            Partner::create([
                'from_user' => $user->email,
                'to_user' => $request->user->email,
                'company_id' => $company_id,
                'partner_id' => $request->user->company_id,
                'is_allowed' => '1',
                'modify_site' => $request->modify_site,
                'modify_location' => $request->modify_location,
                'modify_product' => $request->modify_product,
                'modify_task' => $request->modify_task,
                'modify_user' => $request->modify_user,
                'modify_form' => $request->modify_form,
                'modify_sticker' => $request->modify_sticker
            ]);
        }
        $company = Company::where('id',$request->user->company_id)->first();
        $to_name = $user->first_name . ' ' . $user->last_name;
        $to_email = $user['email'];
        $content = $request->user->first_name.' '.$request->user->last_name.' from '.$company->name.' wants your company to be a partner with his company. Please click here to confirm partnership.';
        $invitationURL = "https://app.sirvez.com/app/settings/partners";
        $img = 'https://app.sirvez.com/upload/img/'.$company['logo_img'];
        $data = ['name'=>$to_name, "content" => 'Invite Partner',"title" =>'Dear '.$to_name,"description" =>$content,"img"=>$img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to confirm partnership'];
        Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('sirvez notification.');
            $message->from('support@sirvez.com','sirvez support team');
        });
        return response()->json([
            'status' => 'success',
            'msg' => 'You have sent an invitation to apply for a partner company. Please wait until approved.'
        ]);
    }

    public function partnerlist(request $request) {
        $company_id = Company_customer::where('customer_id',$request->user->company_id)->pluck('id');
        $partners = Partner::leftJoin('companies', 'companies.id', '=', 'partners.partner_id')
                        ->where('company_id', $request->user->company_id)
                        ->orWhereIn('company_id', $company_id)
                        ->select('partners.*', 'companies.logo_img', 'companies.company_email', 'companies.name', 'companies.website')
                        ->get();
        $res = array();
        $res['partners'] = $partners;
        $res['status'] = 'success';
        return response()->json($res);
    } 

    public function deletePartner(request $request) {
        $partner_id = $request->id;
        Partner::where([
            'company_id' => $request->user->company_id,
            'partner_id' => $partner_id
        ])->delete();
        Partner::where([
            'company_id' => $partner_id,
            'partner_id' => $request->user->company_id
        ])->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'You deleted selected partner!'
        ]);
    }

    public function setAllowPartner(request $request){
        if($request->is_allowed==2){
            Partner::where([
                'company_id' => $request->company_id,
                'partner_id' => $request->partner_id
            ])->update(['is_allowed'=>2]);
            Partner::where([
                'company_id' => $request->partner_id,
                'partner_id' => $request->company_id
            ])->update(['is_allowed'=>2]);
        }
        else{
            Partner::where([
                'company_id' => $request->company_id,
                'partner_id' => $request->partner_id
            ])->update(['is_allowed'=>1]);
            Partner::where([
                'company_id' => $request->partner_id,
                'partner_id' => $request->company_id
            ])->update(['is_allowed'=>0]);
        }
        return response()->json(['status' => 'success']);
    }
    public function setAllowPartnerRequest(request $request){
        $company = Company::whereId($request->partner->company_id)->first();
        $partner = Company::whereId($request->partner->partner_id)->first();
        $users = User::where('users.company_id',$company->id)
                        ->leftJoin('companies','companies,id','=','users.company_id')
                        ->select('users.*','companies.name as company_name')
                        ->get();
        foreach($users as $user){
            $to_name = $user->first_name . ' ' . $user->last_name;
            $to_email = $user['email'];
            $content = $request->user->first_name.' '.$request->user->last_name.' from '.$user->company_name.' wants your company to be a partner with '.$partner->name.'. Please click here to confirm partnership.';
            $invitationURL = "https://app.sirvez.com/app/settings/partners";
            $img = 'https://app.sirvez.com/upload/img/'.$partner['logo_img'];
            $data = ['name'=>$to_name, "content" => 'Invite Partner',"title" =>'Dear '.$to_name,"description" =>$content,"img"=>$img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to confirm partnership'];
            Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('sirvez notification.');
                $message->from('support@sirvez.com','sirvez support team');
            });
        }
        $res =array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    

}
