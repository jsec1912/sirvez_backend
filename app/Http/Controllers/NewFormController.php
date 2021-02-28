<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\New_form;
use App\Form_field;
use App\Form_value;
use App\User;
use App\Company_customer;
use Illuminate\Support\Facades\Validator;

class NewFormController extends Controller
{
    public function saveForm(request $request){

        $v = Validator::make($request->all(), [
            'form_type' => 'required',
            'form_name' => 'required',
            'form_data' => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'msg' => 'You must input data form name!'
            ]);
        }
        $id = $request->form_id;
        if(strlen($request->form_id) > 10)
            if(New_form::where('off_id',$id)->count() > 0)
                $id = New_form::where('off_id',$id)->first()->id;
            else $id = '';
        if ($id == '0'||$id =='') {
            $form = array();
            if(strlen($id) > 10)
                $form['off_id'] = $id;
            $form['created_by'] = $request->user->company_id;
            $form['user_id'] = $request->user->id;
            $form['updated_by'] = $request->user->id;
            $form['form_type'] = $request->form_type;
            $form['form_name'] = $request->form_name;
            $form['form_data']  = $request->form_data;
            $form = New_form::create($form);

            $fields = array();
            $fields = json_decode($request->form_fields);
            $field = array();
            foreach($fields as $row){
                $field['field_name'] = $row->field_name;
                $field['field_type'] = $row->field_type;
                $field['field_label'] = $row->field_label;
                $field['new_form_id'] = $form->id;
                $field['form_type'] = $form->form_type;
                Form_field::create($field);
            }
        } else {
            New_form::whereId($id)->update([
                'form_data'=>$request->form_data,
                'form_name'=>$request->form_name,
                'form_type'=>$request->form_type,
                'updated_by'=>$request->user->id
                ]);
            $fields = array();
            $fields = json_decode($request->form_fields);
            $field = array();
            Form_field::where('new_form_id', $id)->delete();
            foreach($fields as $row) {
                $field['field_name'] = $row->field_name;
                $field['field_type'] = $row->field_type;
                $field['field_label'] = $row->field_label;
                $field['new_form_id'] = $id;
                $field['form_type'] = $request->form_type;
                Form_field::create($field);
            }
        }
        return response()->json(['status'=>'success']);
    }

    public function infoForm(request $request) {
        $res = array();
        $comId = array();
        if($request->user->user_type < 6)
            $comId = Company_customer::where('company_id',$request->company_id)->pluck('customer_id');
        $forms = New_form::whereIn('new_forms.created_by',$comId)
                                ->orWhere('new_forms.created_by', $request->user->company_id)
                                ->leftJoin('users','users.id','=','new_forms.user_id')
                                ->select('new_forms.*','users.profile_pic','users.first_name')
                                ->get();
        foreach($forms as $key=>$form){
            $forms[$key]['created_user'] = User::where('id',$form->user_id)->first();
            $forms[$key]['updated_user'] = User::where('id',$form->updated_by)->first();
        }
        $res['forms'] =$forms;
        $res['status'] = "success";
        return response()->json($res);
    }

    public function saveFormPartner(request $request) {
        $res = array();
        $res['status'] = 'success';
        $form = New_form::whereId($request->form_id)->first();
        if ($request->form_partner) {
            $form->form_partner = 1;
        } else {
            $form->form_partner = 0;
        }
        $form->update();
        return response()->json($res);
    }

    public function deleteForm(request $request){
        $res = array();
        if(strlen($request->id) > 10)
            $id = New_form::where(['off_id'=>$request->id])->first()->id;
        else
            $id = $request->id;
        New_form::whereId($id)->delete();
        Form_field::where('new_form_id',$id)->delete();
        $res['status'] = "success";
        return response()->json($res);
    }
    public function duplicateForm(request $request){
        $form = array();
        if(strlen($request->id) > 10)
            $id = New_form::where(['off_id'=>$request->id])->first()->id;
        else
            $id = $request->id;
        $sel_form = New_form::whereId($id)->first();
        $form = array();
        $form['created_by'] = $sel_form->created_by;
        $form['user_id'] = $request->user->id;
        $form['form_type'] = $sel_form->form_type;
        $form['form_name'] = $sel_form->form_name.'(1)';
        $form['form_data']  = $sel_form->form_data;
        $form = New_form::create($form);

        $fields = Form_field::where('new_form_id',$form->id)->get();
        foreach($fields as $row){
            $field['field_name'] = $row->field_name;
            $field['field_type'] = $row->field_type;
            $field['field_label'] = $row->field_label;
            $field['new_form_id'] = $form->id;
            $field['form_type'] = $form->form_type;
            Form_field::create($field);
        }
        $res = array();
        $res['status'] = "success";
        return response()->json($res);

    }
}
