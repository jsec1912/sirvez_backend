<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\New_form;
use App\Form_field;
use App\Form_value;
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
        if ($request->has('form_id') && $request->form_id == '0') {
            $form = array();
            $form['created_by'] = $request->user->company_id;
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
            New_form::whereId($request->form_id)->update(['form_data'=>$request->form_data]);
            $fields = array();
            $fields = json_decode($request->form_fields);
            $field = array();
            Form_field::where('new_form_id', $request->form_id)->delete();
            foreach($fields as $row) {
                $field['field_name'] = $row->field_name;
                $field['field_type'] = $row->field_type;
                $field['field_label'] = $row->field_label;
                $field['new_form_id'] = $request->form_id;
                $field['form_type'] = $request->form_type;
                Form_field::create($field);
            }
        }
        return response()->json(['status'=>'success']);
    }

    public function infoForm(request $request) {
        $res = array();
        $res['forms'] = New_form::where('created_by', $request->user->company_id)->get();
        $res['status'] = "success";
        return response()->json($res);
    }

    public function deleteForm(request $request){
        $res = array();
        New_form::whereId($request->id)->delete();
        Form_field::where('new_form_id',$request->id)->delete();
        $res['status'] = "success";
        return response()->json($res);
    }
}
