<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\Project;
use App\Room;
use App\Site;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Notification;

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
            $product['created_by']  = $request->user->id;
            if(strlen($request->id) > 10)
                $product['off_id'] = $request->id;
            $product = Product::create($product);
            $room = Room::where('id',$product['room_id'])->first();
            $insertnotificationdata = array(
                'notice_type'		=> '8',
                'notice_id'			=> $product->id,
                //'notification'		=> $room['room_number'].' have been '.$action.' by  '.$request->user->first_name.').',
                'notification'		=> $request->user->first_name.' '.$request->user->last_name.' have been added new product to['.$room['room_number'].']',
                'created_by'		=> $request->user->id,
                'company_id'		=> $room->company_id,
                'project_id'        =>$room['project_id'],
                'created_date'		=> date("Y-m-d H:i:s"),
                'is_read'	    	=> 0,
            );
            Notification::create($insertnotificationdata);
            
        }
        else{
            $product['updated_by'] = $request->user->id;
            Product::whereId($id)->update($product);
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
        }
        if($request->has('project_id')){
            $company_id = Project::whereId($request->project_id)->pluck('company_id');
            $res['sites'] = Site::where('company_id',$company_id)->get();
            $res['rooms'] = Room::where('project_id',$request->project_id)->get();
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

                    $project_id = Project::where('project_name', $request->project_name)->first()->id;
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
}
