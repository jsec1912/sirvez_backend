<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use Illuminate\Support\Facades\Validator;
use App\Sticker;
use App\Sticker_category;
use Illuminate\Support\Facades\File; 
use ZipArchive;

class StickerController extends Controller
{
    public function UpdateStiker(Request $request){
        //$request = {category = {},user_id = {},file,img_flag}
        $res = array();
        $v = Validator::make($request->all(), [
            //company info
            'category_id' => 'required',
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
        $favourite_path = 'pixie/assets/images/stickers/favourites';
        if(strlen($request->category_id)>10)
            $category_id = Sticker_category::where('off_id',$request->category_id)->first()->id;
        else
            $category_id = $request->category_id;
        $stiker_info['name']  = $request->name;
        $stiker_info['user_id']  = $request->user->id;
        $stiker_info['status']  = $request->status;
        $stiker_info['is_favourite'] = $request->is_favourite;
        if(strlen($request->id) > 10)
            if(Sticker::where('off_id',$request->id)->count() > 0)
                $id = Sticker::where('off_id',$request->id)->first()->id;
            else $id = '';
        if(!isset($id) || $id==""|| $id=="null"|| $id=="0"){
            $count = Sticker::where('category_id',$request->category_id)
                            ->where('name',$request->name)->count();
            if($count>0)
            {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'The sticker name has been already exist. Please change sticker name!'
                ]);
            }
            if ($request->has('stiker_img') && isset($request->stiker_img) && $request->stiker_img!='null') {
                $fileName = $request->name.'.'.$request->stiker_img->extension();  
                $request->stiker_img->move(public_path($path), $fileName);
                if($request->is_favourite=='1'){
                    File::copy(public_path($path).'/'.$fileName,public_path($favourite_path).'/'.$fileName);
                }
                $stiker_info['stiker_img'] = $fileName;
            } else if($request->has('lib_file') && isset($request->lib_file) && $request->lib_file!='null') {
                $images = $request->lib_file;
                foreach($images as $img_file) {
                    if (isset($img_file)) {
                        $fileName = $img_file->getClientOriginalName();
                        $img_file->move(public_path($path), $fileName);
                        if($request->is_favourite=='1'){
                            File::copy(public_path($path).'/'.$fileName,public_path($favourite_path).'/'.$fileName);
                        }
                        $stiker_info['category_id'] = $category_id;
                        $stiker_info['parent_id']=0;
                        $stiker_info['name']  = explode(".", $fileName)[0];
                        $stiker_info['stiker_img'] = $fileName;
                        $stiker = sticker::create($stiker_info);
                        if($request->is_favourite=='1'){
                            $stiker_info['category_id']=1;
                            $stiker_info['parent_id']=$stiker->id;
                            sticker::create($stiker_info);
                        }
                    }
                }
                // $zip = new ZipArchive;
                // $zip->open($request->lib_file);
                // $zip->extractTo($path);
                // if($request->is_favourite=='1'){
                //     $zip->extractTo($favourite_path);
                // }
                // for ($i=0; $i<$zip->numFiles;$i++) {
                //     $cur_file=$zip->statIndex($i);
                //     $result[$i] = $cur_file['name'];
                //     if($cur_file['size'] > 0){
                //         $fileName = $cur_file['name'];
                //         $stiker_info['category_id'] = $category_id;
                //         $stiker_info['parent_id']=0;
                //         $stiker_info['name']  = explode(".", $fileName)[0];
                //         $stiker_info['stiker_img'] = $fileName;
                //         $stiker = sticker::create($stiker_info);
                //         if($request->is_favourite=='1'){
                //             $stiker_info['category_id']=1;
                //             $stiker_info['parent_id']=$stiker->id;
                //             sticker::create($stiker_info);
                //         }
                //     }
                // }
                // $res["status"] = "success";
                // return response()->json($res);
                // $zip->close();
            } else{
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You must input image file!'
                ]);
            }
            if(strlen($request->id)>10)
                $stiker_info['off_id'] = $request->id;
            $stiker_info['category_id'] = $category_id;
            $stiker = sticker::create($stiker_info);
            if($request->is_favourite=='1'){
                $stiker_info['off_id'] = '';
                $stiker_info['category_id']=1;
                $stiker_info['parent_id']=$stiker->id;
                sticker::create($stiker_info);
            }
            
        }
        else{
            $count = Sticker::where('id','<>',$id)
                            ->where('category_id',$request->category_id)
                            ->where('name',$request->name)->count() ;
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
                if($request->is_favourite=='1'){
                    File::copy(public_path($path).'/'.$fileName,public_path($favourite_path).'/'.$fileName);
                }
                $stiker_info['stiker_img'] = $fileName;
            }
            sticker::whereId($id)->update($stiker_info);
            $sticker =  sticker::whereId($id)->first();
            if($request->is_favourite=='1'){
                $stiker_info['stiker_img'] = $sticker->stiker_img;
                $stiker_info['category_id'] = 1;
                $stiker_info['parent_id'] = $id;
                sticker::create($stiker_info);
                if($request->stiker_img=='null'){
                    $stiker_img= $sticker->stiker_img;
                    if($stiker_img)
                    File::copy(public_path($path).'/'.$stiker_img,public_path($favourite_path).'/'.$stiker_img);
                }
            }else{
                if(sticker::where('parent_id',$id)->count() > 0)
                {
                    $file_name = sticker::where('parent_id',$id)->first()->stiker_img;
                    $file_path = public_path($favourite_path).'/'.$file_name;
                    File::delete($file_path);
                    sticker::where('parent_id',$id)->delete();

                }
            }
        }
        $res["status"] = "success";
        return response()->json($res);
    }
    public function DeleteStiker(Request $request){
        //$stiker = {stiker_id}
        if(strlen($request->id)>10)
            sticker::where(['off_id'=>$request->id])->delete();
        else
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
