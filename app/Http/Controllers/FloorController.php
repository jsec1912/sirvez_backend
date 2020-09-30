<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Building;
use App\Floor;
use App\Room;
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
        $floor['building_id']  = $request->building_id;
        $floor['floor_name']  = $request->floor_name;
        if($request->hasFile('upload_img')){
            $fileName = time().'floor.'.$request->upload_img->extension();  
            $request->upload_img->move(public_path('upload/img/'), $fileName);
            $floor['upload_img']  = $fileName;
        }

        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"||strlen($request->id) > 10){
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
        $floors= Floor::withCount('rooms')->where('building_id',$request->building_id)->orderBy('id','desc')->get();
        $res['floors'] = $floors;
        $res["status"] = "success";
        return response()->json($res);
    }
    public function floorInfo(Request $request){
        $res = array();
        $res['floor'] = Floor::whereId($request->id)->first();
        $rooms = Room::where('rooms.floor_id',$request->id)
        ->leftJoin('buildings','buildings.id','=','rooms.building_id')
        ->leftJoin('floors','floors.id','=','rooms.floor_id')
        ->leftJoin('projects','projects.id','=','rooms.project_id')
        ->leftJoin('departments','departments.id','=','rooms.department_id')
        ->select('rooms.*','buildings.building_name','floors.floor_name','departments.department_name','projects.project_name')
        ->orderBy('rooms.id','desc')
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
