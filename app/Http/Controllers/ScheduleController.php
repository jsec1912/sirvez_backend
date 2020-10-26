<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Schedule;
use App\Project;
use App\Room;
use App\Site;
use App\Task;
use App\Product;
use App\Project_user;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function UpdateSchedule(Request $request){
        $res = array();
        $v = Validator::make($request->all(), [
            //company info
            'parent_id' => 'required',
            'site_id' => 'required',
            'room_id' => 'required',
            'product_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
           
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $schedule = array();
        $id = $request->id;
        
        if(strlen($request->parent_id)>10)
            $schedule['parent_id'] = Schedule::where('off_id',$request->parent_id)->first()->id;
        else
            $schedule['parent_id'] = $request->parent_id;

        if(strlen($request->site_id)>10)
            $schedule['site_id'] = Site::where('off_id',$request->site_id)->first()->id;
        else
            $schedule['site_id'] = $request->site_id;

        if(strlen($request->room_id)>10)
            $schedule['room_id'] = Room::where('off_id',$request->room_id)->first()->id;
        else
            $schedule['room_id'] = $request->room_id;

        if(strlen($request->product_id)>10)
            $schedule['product_id'] = Product::where('off_id',$request->product_id)->first()->id;
        else
            $schedule['product_id'] = $request->product_id;
        $schedule['start_date'] = $request->start_date;
        if($request->progress)
            $schedule['progress'] = $request->progress;
        $schedule['end_date'] = $request->end_date;
        $schedule['notes'] = $request->notes;
        
        if(strlen($request->id) > 10)
            if(Schedule::where('off_id',$request->id)->count() > 0)
                $id = Schedule::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=="null"|| $id=="0"){
            $schedule['schedule_name'] = $request->schedule_name;
            $schedule['created_by'] = $request->user->id;
            if(strlen($request->id)>10)
                $schedule['off_id'] = $request->id;
            $schedule = Schedule::create($schedule);
            if($request->is_createTask)
            {
                $room = Room::whereId($schedule->room_id)->first();
                $task = array();
                $task['task'] = $schedule['schedule_name'].'_task';
                $task['company_id'] = $room->company_id;
                $task['project_id']  = $room->project_id;
                $task['room_id']  = $room->id;
                $task['due_by_date']  = $request->due_by_date;
                $task['created_by']  = $request->user->id;
                $task = Task::create($task);
                $id = $task->id;
                if($request->has('assign_to'))
                {
                    $array_res = array();
                    $array_res =json_decode($request->assign_to,true);
                    foreach($array_res as $row)
                    {
                        Project_user::create(['project_id'=>$id,'user_id'=>$row,'type'=>'2']);

                    }
                }
            }
        }
        else{
           
            Schedule::whereId($id)->update($schedule);
        }
        $res["status"] = "success";
        return response()->json($res);
    }
    public function DeleteSchedule(Request $request){
        $res = array();
        if(strlen($request->id)>10)
            $id = Schedule::where(['off_id'=>$request->id])->first()->id;
        else
            $id = $request->id;
        if(Schedule::where('parent_id',$id)->count()==0){
            Schedule::where(['id'=>$request->id])->delete();
            $res["status"] = "success";
        }
        else{
            $res["status"] = 'error';
            $res["msg"] = "This schedule has already child. Please first delete child!";
        }
        return response()->json($res);
    }
  
}
