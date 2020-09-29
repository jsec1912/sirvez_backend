<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use Illuminate\Support\Facades\Validator;
use App\Sticker;

class StickerController extends Controller
{
    public function UpdateStiker(Request $request){
        //$request = {category = {},user_id = {},file,img_flag}
        $res = array();
        $v = Validator::make($request->all(), [
            //company info
            'category_id' => 'required',
            'name' => 'required',
            'status' => 'required',
            'category' => 'required',
           
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data in the field!'
            ]);
        }
        $stiker_info = array();
        $id = $request->id;
        $path = 'pixie/assets/images/stickers/'.$request->category;
        
        $stiker_info['category_id'] = $request->category_id;
        $stiker_info['name']  = $request->name;
        $stiker_info['user_id']  = $request->user->id;
        $stiker_info['status']  = $request->status;
        if(!isset($id) || $id==""|| $id=="null"|| $id=="undefined"){
            $count = Sticker::where('category_id',$request->category_id)->where('name',$request->name)->count();            if($count>0)
            {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'That Sticker has been already exist!'
                ]);
            }
            if ($request->has('stiker_img') && isset($request->stiker_img) && $request->stiker_img!='null') {
                $fileName = $request->name.'.'.$request->stiker_img->extension();  
                $request->stiker_img->move(public_path($path), $fileName);
                $stiker_info['stiker_img'] = $fileName;
            } else {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You must input image file!'
                ]);
            }
            sticker::create($stiker_info);
        }
        else{
            $count = Sticker::where('id','<>',$id)->where('category_id',$request->category_id)->where('name',$request->name)->count() ;
            if($count>0)
            {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'That Sticker has been already exist!'
                ]);
            }
            if ($request->has('stiker_img') && isset($request->stiker_img) && $request->stiker_img!='null') {
                $fileName = $request->name.'.'.$request->stiker_img->extension();  
                $request->stiker_img->move(public_path($path), $fileName);
                $stiker_info['stiker_img'] = $fileName;
            }
            sticker::whereId($id)->update($stiker_info);
        }
        $res["status"] = "success";
        return response()->json($res);
    }
    public function DeleteStiker(Request $request){
        //$stiker = {stiker_id}
        sticker::where(['id'=>$request->id])->delete();
        $res["status"] = "success";
        return response()->json($res);
    }
    public function getStikerInfo(Request $request){
        $res = array();
        $res['status'] = "success";
        $res['stiker'] = Sticker::whereId($request->id)->first();
        
        return response()->json($res);
    }
    
}
