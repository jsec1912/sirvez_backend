<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Building;
use App\Floor;
use App\Room;
use App\Site;
use App\Company_customer;
use App\Company;
use Illuminate\Support\Facades\Validator;
class DepartmentController extends Controller
{
    public function updateDepartment(request $request){
      
        $v = Validator::make($request->all(), [
            'department_name' => 'required',
            'colour' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $department = array();
        $id = $request->id;
        $department['company_id']  = $request->user->company_id;
        $department['department_name']  = $request->department_name;
        $department['colour']  = $request->colour;
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"||strlen($request->id) > 10){
            $department['created_by']  = $request->user->id;
            if(strlen($request->id) > 10)
            $department['off_id'] = $request->id;
            Department::create($department);
        }
        else{
            $department['updated_by']  = $request->user->id;
            Department::whereId($id)->update($department);
        }
        $res["status"] = "success";
        return response()->json($res);
    }
    public function deleteDepartment(Request $request){
        //$stiker = {stiker_id}
        if(strlen($request->id) > 10)
            Department::where(['off_id'=>$request->id])->delete();
        else
            Department::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        
        return response()->json($res);
    }
    public function departmentList(Request $request){
        $res = array();
        if($request->user->user_type ==1){
            $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $departments= Department::withCount('rooms')->whereIn('company_id',$customer_id)->orderBy('id','desc')->get();
        }
        else
            $departments= Department::withCount('rooms')->where('company_id',$request->user->company_id)->orderBy('id','desc')->get();
        // foreach($departments as $key =>$department){
        //     $departments[$key]['customer_name'] = Company::whereId($department->company_id)->first()->name;
        //     $departments[$key]['site_name'] = Site::whereId($department->site_id)->first()->site_name;
        //     $departments[$key]['floors_count'] = Floor::where('department_id',$department->id)->count();
        //     $departments[$key]['rooms_count'] = Room::where('department_id',$department->id)->count();
        // }
        $res['departments'] = $departments;
        $res["status"] = "success";
        return response()->json($res);
    }
    public function departmentInfo(Request $request){
        $res = array();
        if ($request->has('id')) {
            $department = Department::where('departments.id',$request->id)
            ->leftJoin('companies','companies.id','=','departments.company_id')
            ->select('departments.*','companies.name')->first(); 
            $res["department"] = $department;
        }
        if($request->user->user_type ==1){
            $customer_id = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['customers'] = Company::whereIn('id',$customer_id)->get();
            $res['sites'] = Site::whereIn('id',$customer_id)->get();
        }
        else{
            $res['customers'] = Company::where('id',$request->user->company_id)->get();
            $res['sites'] = Site::where('id',$request->user->company_id)->get();
        }
       
        $res['status'] = "success";
        return response()->json($res);
    }
}
