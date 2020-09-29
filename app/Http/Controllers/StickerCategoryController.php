<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Sticker_category;
use App\Sticker;
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
        $category['description']  = $request->description;
        $category['created_by']  = $request->user->id;
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){
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
        Sticker_category::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        
        return response()->json($res);
    }
    public function categoryList(Request $request){
        $res = array();
        $res['category'] = Sticker_category::withCount('stickers')->where('created_by',$request->user->id)->orderBy('id','desc')->get();
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
