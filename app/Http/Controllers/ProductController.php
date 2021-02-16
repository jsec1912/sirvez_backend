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
use App\Qr_option;
use App\Company_customer;
use App\Company;
use App\Imports\ProductImport;
use App\Barcode_api;
use App\Barcode;
use App\Product_sign;
use App\Product_label;
use App\Product_label_value;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DateTime;

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
        for ($i=0;$i<intval($request->qty);$i++){
            $product = [];
            $id = $request->id;
            if(strlen($request->room_id) > 10){
                $product['room_id'] = Room::where('off_id',$request->room_id)->first()->id;
            }
            else
                $product['room_id'] = $request->room_id;
            $productName = $request->product_name;
            if($i!=0)
                $productName = $productName.'('.$i.')';

            $product['product_name']  = $productName;
            $product['description']  = $request->description;
            $product['action']  = $request->action;
            $product['qty']  = 1;
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
                    'notification'		=> $request->user->first_name.' '.$request->user->last_name.' has created a new product in location: '.$room['room_number'].'',
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
                    $task['task'] = $productName.'_task';
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
        }
        //$product['room_id'] = $request->room_id;


        $response = ['status'=>'success', 'msg'=>'Product Saved Successfully!','product_name'=>$request->product_name];
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
        $company_id = '';
        if($request->user->user_type<5)
            $company_id = $request->user->company_id;
        else
            if(Company_customer::where('customer_id',$request->user->company_id)->count()>0)
                $company_id = Company_customer::where('customer_id',$request->user->company_id)->first()->company_id;
        $comIds = Company_customer::where('company_id',$company_id)->pluck('customer_id');
        $roomIds = Room::whereIn('company_id',$comIds)->pluck('id');
        $res['barcodes'] = product::whereIn('room_id',$roomIds)->whereNotNull('b_barcode')->orderBy('id','desc')->get()->groupBy('b_barcode');
        $products =  product::whereIn('room_id',$roomIds)
                            ->whereNotNull('b_barcode')
                            ->orderBy('id','desc')
                            ->get();
        foreach($products as $key=>$product){
            $room = Room::whereId($product->room_id)->first();
            $products[$key]['customer_name'] ='';
            $products[$key]['project_name'] = '';
            $products[$key]['site_name'] = '';
            $products[$key]['room_number'] = '';
            $now_date = new DateTime();
            $checkin_date = new DateTime($product['checkin_date']);
            $warranty_time = floor(($now_date->diff($checkin_date)->format('%a'))/365);
            if($warranty_time > 0) $products[$key]['in_warranty'] = $warranty_time;
            else $products[$key]['in_warranty'] = 0;

            if($room){
                $products[$key]['customer_name'] = Company::whereId($room->company_id)->first()->name;
                $products[$key]['project_name'] = Project::whereId($room->project_id)->first()->project_name;
                $products[$key]['site_name'] = Site::whereId($room->site_id)->first()->site_name;
                $products[$key]['room_number'] = $room->room_number;
                $products[$key]['project_id'] = $room->project_id;
                $products[$key]['room_id'] = $room->id;
            }
        }
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
        $res = array();
        if ($request->has('csv_file')) {
            $path = $request->file('csv_file')->getRealPath();
            $filename = $request->file('csv_file')->getClientOriginalName();
            $ext = $request->file('csv_file')->getClientOriginalExtension();
            $product_xls = new ProductImport($request->user->id);
            Excel::import($product_xls, $request->file('csv_file'));
            $res = $product_xls->get_value();
        } else {
            $res['status'] = "error";
            $res['msg'] = 'The excel format is not correct.';
        }
        return response()->json($res);
    }

    public function importList(Request $request) {
        $res = array();
        if ($request->has('product_list')) {
            $product_list = array();
            $product_list = json_decode($request->product_list);
            $titles = array();
            $titles = json_decode($request->titles);

            $header = array(
                'product'=>'product_name',
                'product name'=>'product_name',
                'location id'=>'room_id',
                'room id'=>'room_id',
                'qty'=>'qty',
                'quantity'=>'qty',
                'manufacturer'=>'manufacturer',
                'model number'=>'model_number',
                'model'=>'model_number',
                'description'=>'description',
                'product description'=>'description',
                'testing id'=>'test_form_id',
                'commissioning id'=>'com_form_id',
                'commisioning id'=>'com_form_id',
                'warranty'=>'warranty_time',
                'warranty(year)'=>'warranty_time',
                'warranty time'=>'warranty_time',
                'action'=>'action',
                'product_action'=>'action'
            );
            $header_set = array('product_name', 'room_id', 'qty', 'manufacturer',
                'model_number', 'description', 'test_form_id', 'com_form_id', 'action');

            $idx = 0;
            $cnt = 0;
            $product = array();
            $value_id = array();

            $sum = 0; $s = 0; $i = 0;
            $out = '';
            foreach($titles as $row) {
                $srow = strtolower($row);
                if (array_key_exists($srow, $header)) {
                    $frow = $header[$srow];
                    $s = array_search($frow, $header_set);
                    $sum |= 1<<$s;
                    $value_id[$srow] = $frow;
                    $out .= $frow . "|";
                } else {
                    break;
                }
                $i ++;
            }

            if ($i < 9 || $sum != 511) {
                $res['status'] = "error";
                $res['msg'] = 'The excel format is not correct.' . $i . ", " . $sum;
                $res['out'] = $out;
                return response()->json($res);
            }

            $out = array();

            foreach($product_list as $row) {
                $ok = 1;
                foreach ($row as $key => $val) {
                    $out[$key] = $val;
                    $top = strtolower($key);
                    if (array_key_exists($top, $value_id)) {
                        if ($value_id[$top] == 'test_form_id' || $value_id[$top] == 'com_form_id') {
                            if (!is_numeric($val)) {
                                $val = "0";
                            }
                        }
                        if ($value_id[$top] == 'action') {
                            $product['action'] = 0;
                            if (strtolower($val) == 'new product') {
                                $product['action'] = 0;
                            } else if (strtolower($val) == 'dispose') {
                                $product['action'] = 1;
                            } else if (strtolower($val) == 'move to room') {
                                $product['action'] = 2;
                            }
                        } else {
                            $product[$value_id[$top]] = $val;
                        }
                    } else {
                        $ok = 0;
                        break;
                    }
                }
                if ($ok) {
                    $product['signed_off'] = 0;
                    $product['created_by']  = $request->user->id;

                    Product::create($product);
                    $cnt ++;
                }
            }

            $res['total'] = count($product_list);
            $res['cnt'] = $cnt;
            $res['status'] = "success";
            $res['out'] = $out;
            $res['value_id'] = $value_id;

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
        if($request->test_sign_off=='1')
            Product::whereId($id)->update(['test_sign_off'=>1,'test_signoff_date'=>date("Y-m-d H:i:s"),'test_signoff_by'=>$request->user->id]);
        else
        Product::whereId($id)->update(['test_sign_off'=>0,'test_signoff_date'=>null,'test_signoff_by'=>'']);
        $res['status'] = "success";
        return response()->json($res);
    }

    public function comSignOff(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;
        if($request->com_sign_off=='1')
            Product::whereId($id)->update(['com_sign_off'=>1,'com_signoff_date'=>date("Y-m-d H:i:s"),'com_signoff_by'=>$request->user->id]);
        else
            Product::whereId($id)->update(['com_sign_off'=>0,'com_signoff_date'=>null,'com_signoff_by'=>'']);
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

    public function changeManufacturer(request $request) {
        if (strlen($request->id) > 10) {
            $id = Product::where('off_id', $request->id)->first()->id;
        } else {
            $id = $request->id;
        }
        if ($request->has('manufacturer')) {
            Product::whereId($id)->update(['manufacturer'=>$request->manufacturer]);
        }
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function changeModelNumber(request $request) {
        if (strlen($request->id) > 10) {
            $id = Product::where('off_id', $request->id)->first()->id;
        } else {
            $id = $request->id;
        }
        if ($request->has('model_number')) {
            Product::whereId($id)->update(['model_number'=>$request->model_number]);
        }
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

    public function removeTestingImg(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

            Product::whereId($id)->update(['testing_img'=>'']);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function removeTestingVideo(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

            Product::whereId($id)->update(['testing_video'=>'']);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function getQrOption(request $request){
        $res = array();
        $res['status'] = 'success';
        $res['option'] = Qr_option::first();
        return response()->json($res);
    }
    public function updateQrOption(request $request){
        $res = array();
        Qr_option::truncate();
        $option = array();
        $option['company_logo'] = $request->company_logo;
        $option['sirvez_logo'] = $request->sirvez_logo;
        $option['company_name'] = $request->company_name;
        $option['contact'] = $request->contact;
        $option['warranty_date'] = $request->warranty_date;
        $option['product_name'] = $request->product_name;
        $option['product_code'] = $request->product_code;
        Qr_option::create($option);
        $res['status'] = 'success';

        return response()->json($res);
    }
    public function barcodeCheck(request $request){
        $res = array();
        
        if($request->user->user_type < 4){
            $comId = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id');
            $res['sites'] = Site::whereIn('company_id',$comId)->orWhere('company_id',$request->user->company_id)->get();
            $res['locations'] = Room::whereIn('company_id',$comId)->orWhere('company_id',$request->user->company_id)->get();
        }
        else{
            $comId = Company_customer::where('customer_id',$request->user->company_id)->company_id;
            $res['sites'] = Site::where('company_id',$comId)->orWhere('company_id',$request->user->company_id)->get();
            $res['locations'] = Room::where('company_id',$comId)->orWhere('company_id',$request->user->company_id)->get();
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function getBarcodeApi(request $request){
        $res = array();
        $res['api'] = Barcode_api::first();
        $response = Http::get('https://api.barcodelookup.com/v2/rate-limits',
                        ['formatted'=>'y','key'=>$res['api']->api]);
        $res_data =  json_decode($response->body());
        if ($res_data) {
            $res['total'] = $res_data->allowed_calls_per_month;
            $res['remain'] = $res_data->remaining_calls_per_month;
            $res['allow_per_m'] = $res_data->allowed_calls_per_minute;
            $res['remain_per_m'] = $res_data->remaining_calls_per_minute;
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function updateBarcodeApi(request $request){
        $res = array();
        Barcode_api::truncate();
        Barcode_api::create(['api'=>$request->api]);

        $res['status'] = 'success';
        return response()->json($res);
    }
    public function newBarcode(request $request) {
        $res = array();
        $cnt = Barcode::where('barcode', $request->barcode)->count();
        if ($cnt > 0) {
            $res['status'] = 'error';
            $res['msg'] = 'Barcode number ' . $request->barcode . ' already exists in the data base.';
        } else {
            if ($request->hasFile('product_img')) {
                $fileName = time().'barcode.'.$request->product_img->extension();
                $request->product_img->move(public_path('upload/img/'), $fileName);
                $product = json_decode($request->product);
                $product->products[0]->images = array($fileName);
                $product->products[0]->localdb = 1;
                Barcode::create(['barcode'=>$request->barcode,'data'=>json_encode($product)]);
                $res['status'] = 'success';
            } else {
                $res['status'] = 'error';
                $res['msg'] = 'Product image must be attached.';
            }
        }
        return response()->json($res);
    }

    public function getBarcodeInfo(request $request) {
        $res = array();
        $cnt = Barcode::where('barcode',$request->barcode)->count();
        if($cnt)
        {
            $data = Barcode::where('barcode',$request->barcode)->first();
            $res['status'] = 'success';
            $res['product'] = json_decode($data->data)->products[0];
        }
        else
        {
            $api_key = Barcode_api::first()->api;
            $response = Http::get('https://api.barcodelookup.com/v2/products',
                        ['barcode' =>$request->barcode,'formatted'=>'y','key'=>$api_key]);
            $res_data =  json_decode($response->body());
            if ($res_data) {
                $res['status'] = 'success';
                $res['product'] = $res_data->products[0];
                Barcode::create(['barcode'=>$request->barcode,'data'=>$response->body()]);
            } else {
                $res['status'] = 'error';
                $res['msg'] = 'The barcode does not exist in database';
            }
        }
        return response()->json($res);
    }
    public function insertBarcode(request $request){
        $res = array();
        $cnt = Barcode::where('barcode',$request->barcode)->count();
        $product = null;
        if($cnt)
        {
            $data = Barcode::where('barcode',$request->barcode)->first();
            $res['status'] = 'success';
            $product = json_decode($data->data)->products[0];
        }
        else
        {
            $api_key = Barcode_api::first()->api;
            $response = Http::get('https://api.barcodelookup.com/v2/products',
                        ['barcode' =>$request->barcode,'formatted'=>'y','key'=>$api_key]);
            $res_data =  json_decode($response->body());
            if($res_data){
                Barcode::create(['barcode'=>$request->barcode,'data'=>$response->body()]);
                $product = $res_data->products[0];
                $res['status'] = 'success';
            }
            else{
                $res['status'] = 'error';
                $res['msg'] = 'The barcode does not exist in database';
            }
        }
        if($product){
            $data = array();
                $data['b_where'] = $request->b_where;
                $data['room_id'] = $request->b_location;
                $data['product_name'] = $product->product_name;
                $data['b_product_name'] = $product->product_name;
                $data['b_barcode'] = $product->barcode_number;
                $data['b_manufacturer'] = $product->manufacturer;
                $data['b_description'] = $product->description;
                $data['b_image'] = $product->images[0];
                $data['b_type'] = $product->barcode_type;
                $data['b_model'] = $product->model;
                $data['b_height'] = $product->height;
                $data['b_length'] = $product->length;
                $data['b_mpn'] = $product->mpn;
                $data['action'] = 3;
                $data['qty'] = 1;
                $data['project_id'] = $request->project_id;
                $data['created_by'] = $request->user->id;
                $data['project_id'] = $request->project_id;
                $data['checkin_date'] = date("Y-m-d H:i:s");
                $data = Product::create($data);
                Product_sign::Create(['product_id'=>$data->id,'user_id'=>$data->created_by,'sign_date'=>$data->created_at,'sign_type'=>0]);
                $res['product'] = $data;
        }
        return response()->json($res);
    }
    public function AssignProduct(request $request){
        $res = array();

        $productId = $request->product_id;
        if(strlen($request->product_id) > 10)
            if(Product::where('off_id',$product_id)->count() > 0)
                $productId = Product::where('off_id',$product_id)->first()->id;
        $product = Product::whereId($productId)->first();

        $assignId = $request->assign_id;
        if(strlen($request->assign_id) > 10)
        if(Product::where('off_id',$assignId)->count() > 0)
            $assignId = Product::where('off_id',$assignId)->first()->id;

        $assign_product = Product::whereId($assignId)->first();
        $assign_product->b_barcode = $product->b_barcode;
        $assign_product->b_product_name = $product->b_product_name;
        $assign_product->b_manufacturer = $product->b_manufacturer;
        $assign_product->b_description = $product->b_description;
        $assign_product->b_image = $product->b_image;
        $assign_product->b_type = $product->b_type;
        $assign_product->b_model = $product->b_model;
        $assign_product->b_height = $product->height;
        $assign_product->b_length = $product->length;
        $assign_product->b_mpn = $product->b_mpn;
        $assign_product->b_where = $product->b_where;
        $assign_product->checkin_date = $product->checkin_date;
        $assign_product->warranty_time = $product->warranty_time;
        $assign_product->save();
        Product_sign::Create(['product_id'=>$assign_product->id,'user_id'=>$product->created_by,'sign_date'=>$product->created_at,'sign_type'=>1]);
        $product_sign = Product_sign::where('product_id',$product->id)->first();
        if($product_sign){
            $product_sign->product_id = $assign_product->id;
            $product_sign->save();
        }
        Product::whereId($product->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function qrScan(request $request){
        $res = array();
        $productId = $request->product_id;
        if(strlen($request->product_id) > 10)
            if(Product::where('off_id',$product_id)->count() > 0)
                $productId = Product::where('off_id',$product_id)->first()->id;
                
        Product_sign::Create(['product_id'=>$productId,'user_id'=>$request->user->id,'sign_date'=>date("Y-m-d H:i:s"),'sign_type'=>2]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addLabel(request $request){
        $res = array();
        Product_label::Create(['label'=>$request->label,'created_by'=>$request->user->id]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function deleteLabel(request $request){
        $res = array();
        Product_label::whereId($request->id)->delete();
        Product_label_value::where('label_id',$request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function setProductLabel(request $request){
        $id = $request->id;
        if(strlen($request->id) > 10){
            $id = Product::where('off_id',$request->id)->first()->id;
        }
        else
            $id = $request->id;
        Product_label_value::where('product_id',$id)->delete();
        $array_res = array();
        $array_res =json_decode($request->label_value,true);
        if($array_res){
            foreach($array_res as $row)
            {
                Product_label_value::create(['product_id'=>$id,'label_id'=>$row]);
            }
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function uploadTechPdf(request $request){
        $id = $request->id;
        if(strlen($request->id) > 10){
            $id = Product::where('off_id',$request->id)->first()->id;
        }
        else
            $id = $request->id;
        $product = Product::whereId($id)->first();
        $fileName = '';
        if($request->hasFile('upload_file')){
            $fileName = time().'_technique.'.$request->upload_file->extension();
            $request->upload_file->move(public_path('upload/file/'), $fileName);
            $product['technical_pdf'] = $fileName;
            $product->save();
        }
        $res['file_name'] = $fileName;
        $res['status'] = 'success';
        return response()->json($res);
    }

    public function uploadBrochuresPdf(request $request){
        $id = $request->id;
        if(strlen($request->id) > 10){
            $id = Product::where('off_id',$request->id)->first()->id;
        }
        else
            $id = $request->id;
        $product = Product::whereId($id)->first();
        $fileName = '';
        if($request->hasFile('upload_file')){
            $fileName = time().'_brochures.'.$request->upload_file->extension();
            $request->upload_file->move(public_path('upload/file/'), $fileName);
            $product['brochures_pdf'] = $fileName;
            $product->save();
        }
        $res['file_name'] = $fileName;
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function updateScanProduct(request $request){
        $id = $request->id;
        if(strlen($request->id) > 10){
            $id = Product::where('off_id',$request->id)->first()->id;
        }
        else
            $id = $request->id;
        $product = Product::where('id',$id)->first();
        $product->product_name = $request->b_product_name;
        $product->b_product_name = $request->b_product_name;
        $product->b_product_name = $request->b_product_name;
        $product->b_type = $request->b_type;
        $product->b_model = $request->b_model;
        $product->b_manufacturer = $request->b_manufacturer;
        $product->b_mpn = $request->b_mpn;
        $product->b_height = $request->b_height;
        $product->b_length = $request->b_length;
        $product->b_description = $request->b_description;
        $product->save();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function labelList(request $request){
        $res = array();
        $res['labels'] = Product_label::leftJoin('users','users.id','=','product_labels.created_by')
                                        ->select('product_labels.*','users.profile_pic','users.first_name','users.last_name')
                                        ->get();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeWarrantyTime(request $request){
        if(strlen($request->id) > 10)
            $id = Product::where('off_id',$request->id)->first()->id;
        else
            $id = $request->id;

        Product::whereId($id)->update(['warranty_time'=>$request->warranty_time]);
        $res = array();
        $res['status'] = 'success';
        return response()->json($res);

    }
}
