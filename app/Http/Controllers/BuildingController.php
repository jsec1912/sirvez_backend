<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Building;
use App\Floor;
use App\Room;
use App\Site;
use App\Site_room;
use Illuminate\Support\Facades\Validator;
class BuildingController extends Controller
{
    public function updateBuilding(request $request){
        $v = Validator::make($request->all(), [
            //company info
            'site_id' => 'required',
            'building_name' => 'required'
           
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $building = array();
        $id = $request->id;
        if(strlen($request->site_id) > 10){
            if(Site::where('off_id',$request->site_id)->count()>0){
                $building['site_id'] = Site::where('off_id',$request->site_id)->first()->id;
            }
            else
                return response()->json([
                    'status' => 'error',
                    'msg' => 'The Site is not exist!'
                ]);
        }
        else
            $building['site_id'] = $request->site_id;
        $building['building_name']  = $request->building_name;
        if($request->hasFile('upload_img')){
            $fileName = time().'.'.$request->upload_img->extension();  
            $request->upload_img->move(public_path('upload/img/'), $fileName);
            $building['upload_img']  = $fileName;
        }
        if(strlen($request->id) > 10)
            if(Building::where('off_id',$request->id)->count() > 0)
                $id = Building::where('off_id',$request->id)->first()->id;
            else $id = '';

        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined" ){
            $building['created_by']  = $request->user->id;
            if(strlen($request->id) > 10)
            $building['off_id'] = $request->id;
            Building::create($building);
        }
        else{
            $building['updated_by']  = $request->user->id;
            Building::whereId($id)->update($building);
        }
        $res["status"] = "success";
        $res['msg'] = "Data is saved";
        return response()->json($res);
    }
    public function deleteBuilding(Request $request){
        //$stiker = {stiker_id}
        if(strlen($request->id) > 10)
            Building::where(['off_id'=>$request->id])->delete();
        else
            Building::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        
        return response()->json($res);
    }
    public function buildingList(Request $request){
        $res = array();
        $buildings= Building::withCount('floors')->where('department_id',$request->department_id)->orderBy('id','desc')->get();
        foreach($buildings as $key =>$building){
            $buildings[$key]['rooms_count'] = Site_room::where('building_id',$building->id)->count();
        }
        $res['building'] = $buildings;
        $res["status"] = "success";
        return response()->json($res);
    }
    public function buildingInfo(Request $request){
        $res = array();
        $res['building'] = Building::where('buildings.id',$request->id)
                                ->leftJoin('sites','sites.id','=','buildings.site_id')
                                ->select('buildings.*','sites.site_name','sites.country','sites.county','sites.city','sites.postcode','sites.address','sites.address2')
                                ->first();
        
        $floors =Floor::where('building_id',$request->id)->orderBy('id','desc')->get();
        foreach($floors as $key =>$floor){
            $floors[$key]['rooms_count'] = Site_room::where('floor_id',$floor->id)->count();
        }
        $res['floors'] = $floors;
        $res['rooms'] = Site_room::where('site_rooms.building_id',$request->id)
            ->leftJoin('departments','departments.id','=','site_rooms.department_id')
            ->leftJoin('floors','floors.id','=','site_rooms.floor_id')
            ->select('site_rooms.*','departments.department_name','floors.floor_name')
            ->orderBy('id','desc')->get();
            
        $res["status"] = "success";
        return response()->json($res);
    }
    public function getBuildingInfo(Request $request){
        $res = array();
        if ($request->has('id')) {
            $building = Building::where('buildings.id',$request->id)->first(); 
            $res["building"] = $building;
        }
        $res['status'] = "success";
        return response()->json($res);
    }
}
