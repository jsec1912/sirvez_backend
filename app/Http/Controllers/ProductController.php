<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\Project;
use App\Room;
use App\Site;
use App\Task;
use App\Project_user;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Notification;
use App\Form_value;
use App\New_form;
use App\Form_field;

class ProductController extends Controller
{
    public function updateProduct(Request $request){
        $v = Validator::make($request->all(), [
            //company info
            'room_id' => 'required',
            'product_name' => 'required',
            //'description' => 'required',
            'action' => 'required',
            'qty' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $product = array();
        $id = $request->id;
        if(strlen($request->room_id) > 10){
            $product['room_id'] = Room::where('off_id',$request->room_id)->first()->id;
        }
        else
            $product['room_id'] = $request->room_id;
        //$product['room_id'] = $request->room_id;
        $product['product_name']  = $request->product_name;
        $product['description']  = $request->description;
        $product['action']  = $request->action;
        $product['qty']  = $request->qty;
        $product['test_form_id'] = $request->test_form_id;
        $product['com_form_id'] = $request->com_form_id;
        if(strlen($request->to_room_id) > 10){
            $product['to_room_id'] = Room::where('off_id',$request->to_room_id)->first()->id;
        }
        else
            $product['to_room_id'] = $request->to_room_id;

        if(strlen($request->to_site_id) > 10){
            $product['to_site_id'] = Site::where('off_id',$request->to_site_id)->first()->id;
        }
        else
            $product['to_site_id'] = $request->to_site_id;

        //$product['to_site_id']  = $request->to_site_id;
        //$product['to_room_id']  = $request->to_room_id;

        if($request->hasFile('upload_file')){
            $fileName = time().'product.'.$request->upload_file->extension();
            $request->upload_file->move(public_path('upload/file/'), $fileName);
            $product['upload_file']  = $fileName;
        }
        if(strlen($request->id) > 10)
            if(Product::where('off_id',$request->id)->count() > 0)
                $id = Product::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){

            $product['signed_off']  = $request->signed_off;
            $product['created_by']  = $request->user->id;
            if(strlen($request->id) > 10)
                $product['off_id'] = $request->id;
            $product = Product::create($product);
            $room = Room::where('id',$product['room_id'])->first();
            $insertnotificationdata = array(
                'notice_type'		=> '8',
                'notice_id'			=> $product->id,
                //'notification'		=> $room['room_number'].' have been '.$action.' by  '.$request->user->first_name.').',
                'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new product in room['.$room['room_number'].']',
                'created_by'		=> $request->user->id,
                'company_id'		=> $room->company_id,
                'project_id'        =>$room['project_id'],
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
            );
            Notification::create($insertnotificationdata);

            if($request->is_createTask)
            {
                $room = Room::whereId($product->room_id)->first();
                $task = array();
                $task['task'] = $product['product_name'].'_task';
                $task['company_id'] = $room->company_id;
                $task['project_id']  = $room->project_id;
                $task['room_id']  = $room->id;
                $task['due_by_date']  = $request->due_by_date;
                $task['created_by']  = $request->user->id;
                $task['description'] = $request->notes;
                $task['priority'] = $request->snagging;

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
            $product['updated_by'] = $request->user->id;
            Product::whereId($id)->update($product);
            $product = Product::whereId($id)->first();
        }

        if ($request->test_values && $request->test_form_id != "0") {
            $values = array();
            $values = json_decode($request->test_values);
            $value = array();
            foreach($values as $row){
                $value['field_name'] = $row->field_name;
                $value['field_type'] = $row->field_type;
                $value['field_label'] = $row->field_label;
                $value['new_form_id'] = $row->new_form_id;
                $value['field_value'] = $row->field_value;
                $value['is_checked'] = $row->is_checked;
                $value['form_type'] = $row->form_type;
                $value['parent_id'] = $product->id;
                $cnt = Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$product->id)->count();
                if($cnt>0)
                    Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$product->id)
                                ->update(['field_value'=>$row->field_value,'is_checked'=>$row->is_checked]);
                else
                    Form_value::create($value);
            }
        }

        if ($request->com_values && $request->com_form_id != "0") {
            $values = array();
            $values = json_decode($request->com_values);
            $value = array();
            foreach($values as $row){
                $value['field_name'] = $row->field_name;
                $value['field_type'] = $row->field_type;
                $value['field_label'] = $row->field_label;
                $value['new_form_id'] = $row->new_form_id;
                $value['field_value'] = $row->field_value;
                $value['is_checked'] = $row->is_checked;
                $value['form_type'] = $row->form_type;
                $value['parent_id'] = $product->id;
                $cnt = Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$product->id)->count();
                if($cnt>0)
                    Form_value::where('field_name',$row->field_name)
                                ->where('new_form_id',$row->new_form_id)
                                ->where('parent_id',$product->id)
                                ->update(['field_value'=>$row->field_value,'is_checked'=>$row->is_checked]);
                else
                    Form_value::create($value);
            }
        }

        $response = ['status'=>'success', 'msg'=>'Product Saved Successfully!'];
        return response()->json($response);
    }
    public function deleteProduct(Request $request)
    {
        //$request = {'id':{}}
        if(strlen($request->id) > 10)
            Product::where(['off_id'=>$request->id])->delete();
        else
            Product::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        return response()->json($res);
    }
    public function productList(Request $request){
        $res = array();
        $products = product::where('project_id',$request->project_id)->orderBy('id','desc')->get();
        $res["products"] = $products;
        $res['status'] = "success";
        return response()->json($res);
    }
    public function productInfo(Request $request){
        $res = array();
        $product = product::where('id',$request->id)->first();
        $res["product"] = $product;
        $res['status'] = "success";
        return response()->json($res);
    }
    public function getProductInfo(Request $request){
        $res = array();
        if ($request->has('id')) {
            $id = $request->id;
            $res['product'] = Product::whereId($id)->first();
            $room = Room::whereId($res['product']->room_id)->first();
            $companyId = Project::whereId($room->project_id)->first()->company_id;
            $res['test_values'] = Form_value::where('parent_id',$id)->where('form_type',1)->get();
            $res['com_values'] = Form_value::where('parent_id',$id)->where('form_type',2)->get();
            $res['test_forms'] = New_form::where('form_type', 1)->get();
            $res['com_forms'] = New_form::where('form_type', 2)->get();
        }
        if($request->has('project_id')){
            $company_id = Project::whereId($request->project_id)->pluck('company_id');
            $res['sites'] = Site::where('company_id',$company_id)->get();
            $res['rooms'] = Room::where('rooms.project_id',$request->project_id)
                                ->leftJoin('sites','rooms.site_id','=','sites.id')
                                ->select('rooms.*','sites.site_name')->get();
        }
        $res['status'] = "success";
        return response()->json($res);
    }
    public function importProduct(Request $request) {
        if ($request->has('csv_file')) {
            $path = $request->file('csv_file')->getRealPath();
            $filename = $request->file('csv_file')->getClientOriginalName();
            $ext = $request->file('csv_file')->getClientOriginalExtension();
            $excel_data = Excel::toCollection(null, $request->file('csv_file'));

            $idx = 0;
            $cnt = 0;
            $product = array();
            foreach($excel_data[0] as $row) {
                $idx ++;
                if ($idx == 1) continue;
                // $row->count()
                if ($row->count() > 5) {
                    if ($row[0] == '')
                        continue;
                    $product['product_name'] = $row[0];
                    $product['description'] = $row[1];

                    if ($row[2] == 'New Product')
                        $product['action'] = 0;
                    else if ($row[2] == 'Dispose')
                        $product['action'] = 1;
                    else if ($row[2] == 'Move To Room')
                        $product['action'] = 2;
                    else
                        continue;
                    if(strlen($request->project_id) > 10)
                        $project_id = Project::where('off_id',$request->project_id)->first()->id;
                    else
                        $project_id = $request->project_id;

                    //$project_id = Project::where('project_name', $request->project_name)->first()->id;
                    $product_room = Room::where('project_id', $project_id)->where('room_number', $row[3])->get();
                    if ($product_room->count() != 1)
                        continue;
                    $product['room_id'] = $product_room->first()->id;


                    $product['qty'] = $row[4];
                    if ($row[5] == 'N/A')
                        $product['signed_off'] = 0;
                    else
                        $product['signed_off'] = 1;

                    $product['created_by']  = $request->user->id;

                    Product::create($product);
                    $cnt ++;
                }
            }
            $res['total'] = $idx - 1;
            $res['cnt'] = $cnt;
            $res['status'] = "success";
        } else {
            $res['status'] = "error";
            $res['msg'] = 'The excel format is not correct.';
        }
        return response()->json($res);
    }

    public function signOff(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Product::whereId($id)->update(['signed_off'=>1,'signoff_date'=>date("Y-m-d H:i:s"),'signoff_by'=>$request->user->id]);
        $res['status'] = "success";
        return response()->json($res);
    }

    public function testSignOff(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Product::whereId($id)->update(['test_sign_off'=>1,'test_signoff_date'=>date("Y-m-d H:i:s"),'test_signoff_by'=>$request->user->id]);
        $res['status'] = "success";
        return response()->json($res);
    }

    public function comSignOff(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        Product::whereId($id)->update(['com_sign_off'=>1,'com_signoff_date'=>date("Y-m-d H:i:s"),'com_signoff_by'=>$request->user->id]);
        $res['status'] = "success";
        return response()->json($res);
    }

    public function saveTestingForm(request $request){
        
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        $values = array();
        $values = json_decode($request->test_values);
        $value = array();
        foreach($values as $row){
            $value['field_name'] = $row->field_name;
            $value['field_type'] = $row->field_type;
            $value['field_label'] = $row->field_label;
            $value['new_form_id'] = $row->new_form_id;
            $value['field_value'] = $row->field_value;
            $value['is_checked'] = $row->is_checked;
            $value['form_type'] = $row->form_type;
            $value['parent_id'] = $id;
            $cnt = Form_value::where('field_name',$row->field_name)
                            ->where('new_form_id',$row->new_form_id)
                            ->where('parent_id',$id)->count();
            if($cnt>0)
                Form_value::where('field_name',$row->field_name)
                            ->where('new_form_id',$row->new_form_id)
                            ->where('parent_id',$id)
                            ->update(['field_value'=>$row->field_value,'is_checked'=>$row->is_checked]);
            else
                Form_value::create($value);
        }
        if($request->hasFile('testing_img')){
            $fileName = time().'.'.$request->testing_img->extension();
            $request->testing_img->move(public_path('upload/img/'), $fileName);
            Product::whereId($id)->update(['testing_img'=>$fileName]);
        }

        if($request->hasFile('testing_video')){
            $fileName = time().'.'.$request->testing_video->extension();
            $request->testing_video->move(public_path('upload/img/'), $fileName);
            Product::whereId($id)->update(['testing_video'=>$fileName]);
        }

        if($request->is_task)
        {
            $product = Product::whereId($id)->first();
            $room = Room::whereId($product->room_id)->first();
            $form_name = New_form::whereId($request->test_form_id)->first()->form_name;
            $task = array();
            $task['task'] = $product['product_name'].'['.$form_name.']_task';
            $task['company_id'] = $room->company_id;
            $task['project_id']  = $room->project_id;
            $task['room_id']  = $room->id;
            $task['due_by_date']  = $request->due_by_date;
            $task['created_by']  = $request->user->id;
            $task['description'] = $request->notes;
            $task['priority'] = $request->snagging;

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

        $res['status'] = "success";
        return response()->json($res);

    }

    public function savecommissioningForm(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        $values = array();
        $values = json_decode($request->com_values);
        $value = array();
        foreach($values as $row){
            $value['field_name'] = $row->field_name;
            $value['field_type'] = $row->field_type;
            $value['field_label'] = $row->field_label;
            $value['new_form_id'] = $row->new_form_id;
            $value['field_value'] = $row->field_value;
            $value['is_checked'] = $row->is_checked;
            $value['form_type'] = $row->form_type;
            $value['parent_id'] = $id;
            $cnt = Form_value::where('field_name',$row->field_name)
                            ->where('new_form_id',$row->new_form_id)
                            ->where('parent_id',$id)->count();
            if($cnt>0)
                Form_value::where('field_name',$row->field_name)
                            ->where('new_form_id',$row->new_form_id)
                            ->where('parent_id',$id)
                            ->update(['field_value'=>$row->field_value,'is_checked'=>$row->is_checked]);
            else
                Form_value::create($value);
        }

        if($request->is_task)
        {
            $product = Product::whereId($id)->first();
            $room = Room::whereId($product->room_id)->first();
            $form_name = New_form::whereId($request->com_form_id)->first()->form_name;
            $task = array();
            $task['task'] = $product['product_name'].'['.$form_name.']_task';
            $task['company_id'] = $room->company_id;
            $task['project_id']  = $room->project_id;
            $task['room_id']  = $room->id;
            $task['due_by_date']  = $request->due_by_date;
            $task['created_by']  = $request->user->id;
            $task['description'] = $request->notes;
            $task['priority'] = $request->snagging;

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

        $res['status'] = "success";
        return response()->json($res);
    }

    public function changeProductName(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

            Product::whereId($id)->update(['product_name'=>$request->product_name]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeProductDescription(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

            Product::whereId($id)->update(['description'=>$request->product_description]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeTestingFormId(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Product::whereId($id)->update(['test_form_id'=>$request->test_form_id]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeCommissioningFormId(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

            Product::whereId($id)->update(['com_form_id'=>$request->com_form_id]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
}
