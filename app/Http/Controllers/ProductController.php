<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\Project;
use App\Room;
use App\Site;
use Illuminate\Support\Facades\Validator;
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
            Product::create($product);
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
}
