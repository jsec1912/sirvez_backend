<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Building;
use App\Floor;
use App\Room;
use App\Site_room;
use App\Site;
use App\Company_customer;
use Illuminate\Support\Facades\Validator;
class FloorController extends Controller
{
    public function updateFloor(request $request){
        $v = Validator::make($request->all(), [
            //company info
            'site_id' => 'required',
            'building_id' => 'required',
            'floor_name' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $floor = array();
        $id = $request->id;
        $floor['site_id']  = $request->site_id;
        if(strlen($request->building_id) > 10)
            $floor['building_id'] = Building::where('off_id',$request->building_id)->first()->id;
        else
            $floor['building_id'] = $request->building_id;
        $floor['floor_name']  = $request->floor_name;
        if($request->hasFile('upload_img')){
            $fileName = time().'floor.'.$request->upload_img->extension();  
            $request->upload_img->move(public_path('upload/img/'), $fileName);
            $floor['upload_img']  = $fileName;
        }
        if(strlen($request->id) > 10)
            if(Floor::where('off_id',$request->id)->count() > 0)
                $id = Floor::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){
            $floor['created_by']  = $request->user->id;
            if(strlen($request->id) > 10)
            $floor['off_id'] = $request->id;
            Floor::create($floor);
        }
        else{
            $floor['updated_by']  = $request->user->id;
            Floor::whereId($id)->update($floor);
        }
        $res["status"] = "success";
        return response()->json($res);
    }
    public function deleteFloor(Request $request){
        //$stiker = {stiker_id}
        if(strlen($request->id) > 10)
            Floor::where(['off_id'=>$request->id])->delete();
        else
            Floor::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        
        return response()->json($res);
    }
    public function FloorList(Request $request){
        $res = array();
        $floors= Floor::where('building_id',$request->building_id)->orderBy('id','desc')->get();
        foreach($floors as $key =>$floor){
            $floors[$key]['rooms_count'] = Site_room::where('floor_id',$floor->id)->count();
        }
        $res['floors'] = $floors;
        $res["status"] = "success";
        return response()->json($res);
    }
    public function floorInfo(Request $request){
        $res = array();
        $res['floor'] = Floor::whereId($request->id)->first();
        $rooms = Site_room::where('site_rooms.floor_id',$request->id)
        ->leftJoin('buildings','buildings.id','=','site_rooms.building_id')
        ->leftJoin('floors','floors.id','=','site_rooms.floor_id')
        ->leftJoin('departments','departments.id','=','site_rooms.department_id')
        ->select('site_rooms.*','buildings.building_name','floors.floor_name','departments.department_name')
        ->orderBy('site_rooms.id','desc')
        ->get();
        $res['rooms'] = $rooms;
        $res["status"] = "success";
        return response()->json($res);
    }
    public function getFloorInfo(Request $request){
        $res = array();
        if ($request->has('id')) {
            $floor = Floor::whereId($request->id)->first();
            $res["floor"] = $floor;
        }
        
        $res['status'] = "success";
        return response()->json($res);
    }
}
