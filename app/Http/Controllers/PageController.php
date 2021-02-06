<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Page;
use App\User;
use App\Company_customer;
use Illuminate\Support\Facades\File; 

class PageController extends Controller
{
    public function updatePage(request $request){
        $v = Validator::make($request->all(), [
            'page_name' => 'required',
            'content' => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input page name or content!'
            ]);
        }
        $page = array();
        $page['name'] = $request->page_name;
        if($request->page_count>0)
            $page['page_count'] = $request->page_count;
        else
            $page['page_count'] = 1; 
        $page['content'] = $request->content;
        $page['lock_page'] = $request->lock_page;
        if($request->link_url)
            $page['link_url'] = $request->link_url;
        
        $page['company_id'] = $request->user->company_id;
        if($request->id > 0){
            $page['updated_by'] = $request->user->id;
            Page::whereId($request->id)->update($page);
            $data = Page::whereId($request->id);
        }
        else{
            $page['created_by'] = $request->user->id;
            $page['updated_by'] = $request->user->id;
            $data = Page::create($page);
        }
        $res = array();
        $res['status'] = 'success';
        $res['page'] = $data;
        return response()->json($res);

    }

    public function pageList(request $request){
        $res = array();
        $customerIds = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id')->toArray();
        array_push($customerIds, intval($request->user->company_id));
        $res['users'] = User::whereIn('company_id',$customerIds)->get();
        $res['pages'] = Page::whereIn('company_id',$customerIds)->get();
        $res['status'] = 'success';
        return response()->json($res);

    }
    public function deletePage(request $request){
        $res = array();
        Page::whereId($request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);

    }
    public function duplicatePage(request $request){
        $res = array();
        $page = Page::whereId($request->id)->first()->toArray();
        unset($page['id']);
        unset($page['created_at']);
        unset($page['updated_at']);
        $page['updated_by']=$request->user->id;
        Page::create($page);
        $res['status'] = 'success';
        return response()->json($res);
        
    }
    public function uploadPdf(request $request){
        $res = array();
        if($request->id>0){
            $page = Page::whereId($request->id)->first();
            if($page->link_url){
                $file_path = public_path('upload/file').'/'.$page->link_url;
                File::delete($file_path);
            }
        }
        if ($request->has('maked_pdf') && isset($request->maked_pdf) && $request->maked_pdf!='null') {
            $fileName = time().'.'.$request->maked_pdf->extension();
            $request->maked_pdf->move(public_path('upload/file'), $fileName);
            $res['status'] = 'success';
            
            $res['link_url'] = $fileName;
        } 
        else{
            $res['status'] = 'error';
        }
        return response()->json($res);
    }
}
