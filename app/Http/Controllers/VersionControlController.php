<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Version_control;
use App\Notification;
use App\Project;
use App\Room;

class VersionControlController extends Controller
{
    public function updateVersion(Request $request){
        $v = Validator::make($request->all(), [
            'room_id' => 'required',
            'version_name' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $version = array();
        $id = $request->id;
        if(strlen($request->room_id) > 10){
            $version['room_id'] = Room::where('off_id',$request->room_id)->first()->id;
        }
        else
            $version['room_id'] = $request->room_id;
        $version['project_id'] = Room::where('id',$version['room_id'])->first()->project_id;
        $version['version_name']  = $request->version_name;
        $version['description']  = $request->description;
        $version['tag']  = $request->version_tag;
        $version['version_number']  = $request->version_number;
        $version['editable_link']  = $request->editable_link;
        $version['pdf_link']  = $request->pdf_link;
        $version['parent_id']  = $request->parent_id;
        $version['created_by']  = $request->user->id;
        
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){

            if(strlen($request->id) > 10)
                $version['off_id'] = $request->id;
            $version = Version_control::create($version);
            if($version->parent_id==0)
                $version->group_id=$version->id;
            else{
                Version_control::whereId($version->parent_id)->update(['version_number'=>$version['version_number']]);
                $version->group_id=$version->parent_id;
            }
            $version->save();
            $room = Room::where('id',$version['room_id'])->first();
            $insertnotificationdata = array(
                'notice_type'		=> '8',
                'notice_id'			=> $version->id,
                'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new version control('.$version['version_name'].') in room['.$room['room_number'].']',
                'created_by'		=> $request->user->id,
                'company_id'		=> $room->company_id,
                'project_id'        => $room['project_id'],
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
            );
            Notification::create($insertnotificationdata);

        }
        else{
            Version_control::whereId($id)->update($version);
            $version = Version_control::whereId($id)->first();
        }

        $response = ['status'=>'success'];
        return response()->json($response);
    }

    public function deleteVersion(Request $request)
    {
        if(strlen($request->id) > 10)
            Version_control::where(['off_id'=>$request->id])->delete();
        else
            Version_control::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        return response()->json($res);
    }

    public function changeVersionName(Request $request){
        if(strlen($request->id) > 10)
            $id = Version_control::where(['off_id'=>$request->id])->first()->id;
        else
            $id = Version_control::where(['id'=>$request->id])->first()->id;
        Version_control::whereId($id)->update(['version_name'=>$request->version_name]);
        $res["status"] = "success";
        return response()->json($res);
    }

    public function changeVersionNumber(Request $request){
        if(strlen($request->id) > 10)
            $id = Version_control::where(['off_id'=>$request->id])->first()->id;
        else
            $id = Version_control::where(['id'=>$request->id])->first()->id;
        Version_control::whereId($id)->update(['version_number'=>$request->version_number]);
        $res["status"] = "success";
        return response()->json($res);
    }

    public function changeVersionTag(Request $request){
        if(strlen($request->id) > 10)
            $id = Version_control::where(['off_id'=>$request->id])->first()->id;
        else
            $id = Version_control::where(['id'=>$request->id])->first()->id;
        Version_control::whereId($id)->update(['tag'=>$request->tag]);
        $res["status"] = "success";
        return response()->json($res);
    }

    public function changeVersionRoom(Request $request){
        if(strlen($request->id) > 10)
            $id = Version_control::where(['off_id'=>$request->id])->first()->id;
        else
            $id = Version_control::where(['id'=>$request->id])->first()->id;
        Version_control::whereId($id)->update(['room_id'=>$request->room_id]);
        $res["status"] = "success";
        return response()->json($res);
    }

    public function changeVersionDescription(Request $request){
        if(strlen($request->id) > 10)
            $id = Version_control::where(['off_id'=>$request->id])->first()->id;
        else
            $id = Version_control::where(['id'=>$request->id])->first()->id;
        Version_control::whereId($id)->update(['description'=>$request->description]);
        $res["status"] = "success";
        return response()->json($res);
    }
       
}
