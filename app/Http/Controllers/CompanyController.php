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
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function saveCompany(request $request){

        $v = Validator::make($request->all(), [
            //company info
            'name' => 'required',
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
        $res['company'] = Company::whereId($id)->first();

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
}
