<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Page;
use App\ProjectPage;
use App\ProjectTender;
use App\ProjectHealthy;
use App\Document;
use App\DocumentPage;
use App\User;
use App\Company_customer;
use App\PageLabel;
use App\PageLabelValue;
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
        $page['page_name'] = $request->page_name;
        if($request->page_count > 0)
            $page['page_count'] = $request->page_count;
        else
            $page['page_count'] = 1; 
        $page['content'] = $request->content;
        $page['lock_page'] = $request->lock_page;
        if($request->link_url)
            $page['link_url'] = $request->link_url;
        
        $page['company_id'] = $request->user->company_id;
        if($request->id > 0){
            if ($request->has('project_id')) {
                if($request->doc_type==1){
                    $cur_page = ProjectPage::whereId($request->id)->first();
                }
                else if($request->doc_type==2){
                    $cur_page = ProjectTender::whereId($request->id)->first();
                }
                else if($request->doc_type==3){
                    $cur_page = ProjectHealthy::whereId($request->id)->first();
                }
                $cur_page->root_type = 2;
                $cur_page->page_name = $page['page_name'];
                $cur_page->page_count = $page['page_count'];
                $cur_page->content = $page['content'];
                $cur_page->lock_page = $page['lock_page'];
                $cur_page->link_url = $page['link_url'];
                if ($request->is_complete)
                $cur_page->is_complete = $request->is_complete;
                $cur_page->save();

            } else {
                $page['updated_by'] = $request->user->id;
                Page::whereId($request->id)->update($page);
                $data = Page::whereId($request->id)->first();
                PageLabelValue::where('page_id',$data->id)->delete();
                $array_res = array();
                $array_res =json_decode($request->label_value,true);
                if($array_res){
                    foreach($array_res as $row)
                    {
                        PageLabelValue::create(['page_id'=>$data->id,'label_id'=>$row]);
                    }
                }
            }
        }
        else{
            $page['created_by'] = $request->user->id;
            $page['updated_by'] = $request->user->id;
            $data = Page::create($page);
            $array_res = array();
            $array_res =json_decode($request->label_value,true);
            if($array_res){
                foreach($array_res as $row)
                {
                    PageLabelValue::create(['page_id'=>$data->id,'label_id'=>$row]);
                }
            }
        }
       
            
      
        $res = array();
        $res['status'] = 'success';
        //$res['page'] = $data;
        return response()->json($res);
    }

    public function pageList(request $request){
        $res = array();
        $customerIds = Company_customer::where('company_id',$request->user->company_id)
                                        ->pluck('customer_id')->toArray();
        array_push($customerIds, intval($request->user->company_id));
        $res['users'] = User::whereIn('company_id',$customerIds)->get();
        $pages = Page::whereIn('company_id',$customerIds)->where('id','>',1)->get();
        foreach($pages as $key=>$page){
            $pages[$key]['label_value']= PageLabelValue::where('page_id',$page->id)->pluck('label_id');
        }
        $res['pages']=$pages;
        $res['page_labels'] = PageLabel::get();
        $pageIds = Page::whereIn('company_id',$customerIds)->where('id','>',1)->pluck('id');
        $labelIds = PageLabelValue::whereIn('page_id',$pageIds)->pluck('label_id');
        $res['page_used_labels'] = PageLabel::whereIn('id',$labelIds)->get();
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
    public function deleteDocument(request $request){
        $res = array();
        Document::whereId($request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);

    }
    public function duplicateDocument(request $request){
        $res = array();
        $page = Document::whereId($request->id)->first()->toArray();
        unset($page['id']);
        unset($page['created_at']);
        unset($page['updated_at']);
        $page['updated_by']=$request->user->id;
        Document::create($page);
        $res['status'] = 'success';
        return response()->json($res);
        
    }
    public function uploadPdf(request $request){
        $res = array();
        if($request->id>0){
            if (!$request->has('doc_type'))
                $page = Page::whereId($request->id)->first();
            else if($request->doc_type==1)
                $page = ProjectPage::whereId($request->id)->first();
            else if($request->doc_type==2)
                $page = ProjectTender::whereId($request->id)->first();
            else if($request->doc_type==3)
                $page = ProjectHealthy::whereId($request->id)->first();

            if($page&&$page->link_url){
                $file_path = public_path('upload/file').'/'.$page->link_url;
                File::delete($file_path);
            }
        }
        if ($request->has('maked_pdf') && isset($request->maked_pdf) && $request->maked_pdf!='null') {
            $fileName = time().'.'.$request->maked_pdf->extension();
            $request->maked_pdf->move(public_path('upload/file'), $fileName);
            $res['status'] = 'success';
            $res['link_url'] = $fileName;
        } else {
            $res['status'] = 'error';
        }
        return response()->json($res);
    }
    public function documentList(request $request){
        $res = array();
        $customerIds = Company_customer::where('company_id',$request->user->company_id)->pluck('customer_id')->toArray();
        array_push($customerIds, intval($request->user->company_id));
        $documents = Document::whereIn('company_id',$customerIds)->get();
        foreach($documents as $key=>$document)
        {
            $documents[$key]['pages'] = DocumentPage::where('document_pages.document_id',$document->id)
                                                    ->orderBy('document_pages.order_no')
                                                    ->leftJoin('pages','document_pages.page_id','=','pages.id')
                                                    ->select('pages.*','document_pages.id','pages.id as page_id')
                                                    ->get();
        }
        $res['documents']  = $documents;
        $res['users'] = User::whereIn('company_id',$customerIds)->get();
        $res['pages'] = Page::whereIn('company_id',$customerIds)->where('id','>',1)
                            ->select('pages.*','pages.id as page_id')
                            ->get();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function updateDocument(request $request){
        $res = array();
        $document = array();
        $id = $request->id;
        $document['document_name'] = $request->document_name;
        $document['page_count'] = $request->page_count;
        $document['lock_page'] = $request->lock_page;
        $document['company_id'] = $request->user->company_id;
        if($request->hasFile('link_file')){

            $fileName = time().'.'.$request->link_file->extension();
            $request->link_file->move(public_path('upload/file/'), $fileName);
            $document['link_url']  = $fileName;
        }
        if($request->id > 0){
            $document['updated_by'] = $request->user->id;
            Document::where('id',$request->id)->update($document);
        }
        else{
            $document['created_by'] = $request->user->id;
            $document['updated_by'] = $request->user->id;
            $document = Document::create($document);
            $id = $document->id;
        }
        $pages = json_decode($request->pages,true);
        DocumentPage::where('document_id',$id)->delete();
        foreach($pages as $key=> $pageId)
        {
            DocumentPage::create([
                'document_id'=>$id,
                'page_id'=>$pageId,
                'order_no'=>$key+1,
                'created_by'=>$request->user->id
                ]);
        }
        
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function removeDocumentPage(request $request){
        $res = array();
        $document_page = DocumentPage::whereId($request->id)->first();
        if($document_page)
        {
            $page = Page::where('id',$document_page->page_id)->first();
            $document = Document::where('id',$request->document_id)->first();
            $document->page_count=intval($document->page_count)-intval($page->page_count);
            $document->save();
            DocumentPage::whereId($request->id)->delete();
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    
    public function changeOrderDocumentPage(request $request){
        $res = array();
        $ordered_id = json_decode($request->ordered_id,true);
        foreach($ordered_id as $key=> $orderId)
        {
            DocumentPage::whereId($orderId)->update(['order_no'=>$key+1]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function labelList(request $request){
        $res = array();
        $res['labels'] = PageLabel::leftJoin('users','users.id','=','page_labels.created_by')
                                        ->select('page_labels.*','users.profile_pic','users.first_name','users.last_name')
                                        ->get();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function deleteLabel(request $request){
        $res = array();
        PageLabel::whereId($request->id)->delete();
        PageLabelValue::where('label_id',$request->id)->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addLabel(request $request){
        $res = array();
        PageLabel::Create(['label'=>$request->label,'created_by'=>$request->user->id]);
        $res['status'] = 'success';
        return response()->json($res);
    }
    // public function setPageLabel(request $request){
    //     $id = $request->id;
    //     if(strlen($request->id) > 10){
    //         $id = page::where('off_id',$request->id)->first()->id;
    //     }
    //     else
    //         $id = $request->id;
    //     PageLabelValue::where('page_id',$id)->delete();
    //     $array_res = array();
    //     $array_res =json_decode($request->label_value,true);
    //     if($array_res){
    //         foreach($array_res as $row)
    //         {
    //             PageLabelValue::create(['page_id'=>$id,'label_id'=>$row]);
    //         }
    //     }
    //     $res['status'] = 'success';
    //     return response()->json($res);

    // }
}
