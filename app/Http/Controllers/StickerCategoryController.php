<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Sticker_category;
use App\Sticker;
use App\Company_customer;
use App\Partner;
use Illuminate\Support\Facades\File;

class StickerCategoryController extends Controller
{
    public function updateCategory(request $request){
        $v = Validator::make($request->all(), [
            //company info
            'name' => 'required',
            'description' => 'required'
           
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $category = array();
        $id = $request->id;
        $category['name']  = $request->name;
        $category['description'] = $request->description;
        $category['created_by'] = $request->user->id;
        if(strlen($request->id) > 10)
            if(Sticker_category::where('off_id',$request->id)->count() > 0)
                $id = Sticker_category::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){
            $category['company_id'] = $request->user->company_id;
            $v = Validator::make($request->all(), [
                'name' => 'unique:sticker_categories',
            ]);
            if ($v->fails())
            {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'That Category has been already exist!'
                ]);
            }
            $sticker_path = 'pixie/assets/images/stickers/'.$request->name;
            if (! File::exists($sticker_path)) {
                File::makeDirectory($sticker_path);
            }
            if(strlen($request->id)>10)
                $category['off_id'] = $request->id;

            if ($request->has('category_img') && isset($request->category_img) && $request->category_img!='null') {
                $fileName = time().'category.'.$request->category_img->extension();  
                $request->category_img->move(public_path('pixie/assets/images/ui/'), $fileName);
                $category['category_img'] = $fileName;
            }
            Sticker_category::create($category);
        }
        else{
            $count = Sticker_category::where('id','<>',$id)->where('name',$request->name)->count() ;
            if($count>0)
            {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'That Category has been already exist!'
                ]);
            }
            $sticker_path = 'pixie/assets/images/stickers/'.$request->name;
            if (! File::exists($sticker_path)) {
                File::makeDirectory($sticker_path);
            }

            if ($request->has('category_img') && isset($request->category_img) && $request->category_img!='null') {
                $fileName = time().'category.'.$request->category_img->extension();  
                $request->category_img->move(public_path('pixie/assets/images/ui/'), $fileName);
                $category['category_img'] = $fileName;
            }
            Sticker_category::whereId($id)->update($category);
        }
        $res["status"] = "success";
        return response()->json($res);
    }
    public function deleteCategory(Request $request){
        //$stiker = {stiker_id}
        if(strlen($request->id)>10)
            Sticker_category::where(['off_id'=>$request->id])->delete();
        else
            Sticker_category::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        
        return response()->json($res);
    }
    public function categoryList(Request $request){
        $res = array();
        //// super company id
        $company_id = $request->user->company_id;
        if (Company_customer::where('customer_id', $company_id)->count() > 0) {
            $company_id = Company_customer::where('customer_id', $company_id)->first()->company_id;
        }
        $partner_ids = Partner::where([
            'company_id' => $company_id,
            'is_allowed' => 2,
            'modify_sticker' => '1'
        ])->pluck('partner_id')->toArray();
        array_push($partner_ids, $company_id);
        
        $res['category'] = Sticker_category::whereIn('company_id', $partner_ids)->withCount('stickers')->orderBy('id','desc')->get();
        $res["status"] = "success";
        return response()->json($res);
    }
    public function getCategoryInfo(Request $request){
        $res = array();
        $res['category'] = Sticker_category::whereId($request->id)->first();
        $res['stickers'] =Sticker::where('category_id',$request->id)->orderBy('id','desc')->get();
        $res["status"] = "success";
        return response()->json($res);
    }
}
