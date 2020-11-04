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
use App\ScheduleProduct;
use App\ScheduleEngineer;
use Illuminate\Support\Facades\Validator;
use DateTime;

class ScheduleController extends Controller
{
    public function UpdateSchedule(Request $request){
        $res = array();
        $product_name = '';
        $v = Validator::make($request->all(), [
            //company info
            'parent_id' => 'required',
            'site_id' => 'required',
            'room_id' => 'required',
            'start_date' => 'required',
            //'end_date' => 'required',

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

        $schedule['start_date'] = $request->start_date;
        $schedule['end_date'] = $request->end_date;
        if($request->progress)
            $schedule['progress'] = $request->progress;
        $schedule['duration_day'] = $request->duration_day;
        $schedule['duration_hour'] = $request->duration_hour;
        $schedule['notes'] = $request->notes;
        $schedule['schedule_name'] = $request->schedule_name;

        if(strlen($request->id) > 10)
            if(Schedule::where('off_id',$request->id)->count() > 0)
                $id = Schedule::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=="null"|| $id=="0"){
            $schedule['created_by'] = $request->user->id;
            if(strlen($request->id)>10)
                $schedule['off_id'] = $request->id;
            if($schedule['parent_id']!=0){
                $schedule['root_id'] =Schedule::whereId($schedule['parent_id'])->first()->root_id;
                $schedule = Schedule::create($schedule);
            }
            else{
                $schedule = Schedule::create($schedule);
                $schedule->root_id = $schedule->id;
                $schedule->save();
            }
            if ($request->has('product_id')) {
                $array_res = array();
                $array_res = json_decode($request->product_id, true);
                foreach ($array_res as $row) {
                    ScheduleProduct::create([
                        'schedule_id' => $schedule->id,
                        'product_id' => $row
                    ]);
                    $product_name.=Product::whereId($row)->first()->product_name.' ';
                }
            }
            if ($request->has('engineer_id')) {
                $array_res = array();
                $array_res = json_decode($request->engineer_id, true);
                foreach ($array_res as $row) {
                    ScheduleEngineer::create([
                        'schedule_id' => $schedule->id,
                        'engineer_id' => $row
                    ]);
                }
            }
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
                $task['description'] = $request->description;
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
            if ($request->has('product_id')) {
                ScheduleProduct::where('schedule_id', $id)->delete();
                $array_res = array();
                $array_res = json_decode($request->product_id, true);
                foreach($array_res as $row) {
                    ScheduleProduct::create([
                        'schedule_id' => $id,
                        'product_id' => $row
                    ]);
                }
            }
            if ($request->has('engineer_id')) {
                ScheduleEngineer::where('engineer_id', $id)->delete();
                $array_res = array();
                $array_res = json_decode($request->engineer_id, true);
                foreach ($array_res as $row) {
                    ScheduleEngineer::create([
                        'schedule_id' => $id,
                        'engineer_id' => $row
                    ]);
                }
            }
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
            ScheduleProduct::where('schedule_id', $id)->delete();
        }
        else{
            $res["status"] = 'error';
            $res["msg"] = "This schedule has already child. Please first delete child!";
        }
        return response()->json($res);
    }
    public function changeStart(Request $request){
        Schedule::whereId($request->schedule_id)->update(['start_date'=>$request->start_date,'end_date'=>$request->end_date]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

}
