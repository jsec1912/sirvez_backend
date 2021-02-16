<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project_chat;
use App\User;
use App\Events\ChatEvent;
use App\Events\OnUserEvent;
use App\ProjectChatgroup;
use App\ProjectChatgroupUser;
use App\Events\NotificationEvent;
use App\Project;
use Mail;

class ProjectChatController extends Controller
{
    public function sendMessage(Request $request){
        $res = array();
        $chat = array();
        $onlineUsers = json_decode($request->onlineUsers);
        $users = array();
        if($request->group_id==0){
            $users[] = intval($request->receiver_id);
            $chat['project_id'] = $request->project_id;
            $chat['sender_id'] = $request->user->id;
            $chat['send_user'] = $request->user->first_name;
            $chat['receiver_id'] = $request->receiver_id;
            $chat['message'] = $request->message;
            $chat['group_id'] = $request->group_id;
            $chat = Project_chat::create($chat);
            broadcast(new ChatEvent($chat))->toOthers();
            broadcast(new OnUserEvent($chat))->toOthers();
            $cnt = 0;
            foreach($onlineUsers as $key){
                if(strval($key->id)==$request->receiver_id)
                {
                    $cnt=1;
                    break;
                }
            }
           
            if($cnt==0 && User::whereId($request->receiver_id)->count()>0){
                $pending_user=User::whereId($request->receiver_id)->first();
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $content = $request->user->first_name.' '.$request->user->last_name.' sent message to you in the project.';
                $task_img = '';
                $invitationURL = "https://app.sirvez.com/app/project/live/".$request->project_id;
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>'Project Chat',"description" =>$request->message,"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view message'];

                Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','sirvez support team');
                });
            }
        }
        else{
            $can_send = ProjectChatGroupUser::where('group_id',$request->group_id)
                                        ->where('user_id',$request->user->id)
                                        ->count();
            if($can_send==0){
                $res['status'] = 'error';
                $res['msg'] = "You can't send message here.";
                return response()->json($res);
            }

            $users = ProjectChatGroupUser::where('group_id',$request->group_id)
                                        ->where('user_id','!=',$request->user->id)
                                        ->get();
            
            foreach($users as $key=>$user){
                $users[] = intval($user->user_id);
                $chat = array();
                $chat['project_id'] = $request->project_id;
                $chat['sender_id'] = $request->user->id;
                $chat['send_user'] = $request->user->first_name;
                $chat['receiver_id'] = $user->user_id;
                $chat['message'] = $request->message;
                $chat['group_id'] = $request->group_id;
                $chat['send_num'] = $key;
                $chat = Project_chat::create($chat);
                broadcast(new ChatEvent($chat))->toOthers();
                broadcast(new OnUserEvent($chat))->toOthers();

                $cnt =0;
                foreach($onlineUsers as $key){
                    if($key->id==$user->user_id)
                    {
                        $cnt=1;
                        break;
                    }
                }
                if($cnt ==0 && User::whereId($user->user_id)->count()>0){
                    $pending_user=User::whereId($user->user_id)->first();
                    $to_name = $pending_user['first_name'];
                    $to_email = $pending_user['email'];
                    $content = $request->user->first_name.' '.$request->user->last_name.' sent message to you in the project.';
                    $task_img = '';
                    $invitationURL = "https://app.sirvez.com/app/project/live/".$request->project_id;
                    $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>'Project Chat',"description" =>$request->message,"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view message'];
    
                    Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                        $message->to($to_email, $to_name)
                                ->subject('sirvez notification.');
                        $message->from('support@sirvez.com','sirvez support team');
                    });
                }
            }
        }

        $project = Project::where('id', $request->project_id)->first();
        
        $text = $request->user->first_name . ' ' . $request->user->last_name . ' has sent a message to ';
        $notification['title'] = 'Sirvez | Chat';
        $notification['text1'] = $text;
        $notification['text2'] = ' for ' . $project->project_name . ' "' . $request->message . '".';
        $notification['id'] = 'chat-' . $chat->id;
        $notification['image'] = $request->user->profile_pic;
        $notification['user_id'] = $users;
        $notification['created_by'] = $request->user->id;
        $notification['action_link'] = '/app/project/live/' . $project->id;

        broadcast(new NotificationEvent($notification))->toOthers();

        $res['status'] = 'success';
        return response()->json($res);
    }
    public function getChats(Request $request){
        $res = array();
        $chats = Project_chat::where('project_id',$request->project_id)->get();
        $groups = ProjectChatgroup::where('project_id',$request->project_id)->get();
        foreach($groups as $key=>$group){
            $groups[$key]['group_users'] = ProjectChatgroupUser::where('group_id',$group->id)->pluck('user_id');
        }
        $res['chats'] = $chats;
        $res['groups'] = $groups;
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function readMessage(request $request){
        $res = array();
        if($request->group_id > 0){
            Project_chat::where('group_id',$request->group_id)
                ->where('receiver_id',$request->receiver_id)
                ->update(['is_read'=>1]);
        }
        else{
            Project_chat::where('project_id',$request->project_id)
                    ->where('sender_id',$request->sender_id)
                    ->where('receiver_id',$request->receiver_id)
                    ->where('group_id',0)
                    ->update(['is_read'=>1]);
        }
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function createGroup(request $request){
        $res = array();
        $data = array();
        $data['group_name'] = $request->group_name;
        $data['project_id'] = $request->project_id;
        $data['created_by'] = $request->user->id;
        $data = ProjectChatGroup::create($data);
        ProjectChatGroupUser::create(['group_id'=>$data['id'],'user_id'=>$request->user->id,'created_by'=>$request->user->id]);

        $chats = Project_chat::where('project_id',$request->project_id)->get();
        $groups = ProjectChatgroup::where('project_id',$request->project_id)->get();
        foreach($groups as $key=>$group){
            $groups[$key]['group_users'] = ProjectChatgroupUser::where('group_id',$group->id)->pluck('user_id');
        }
        $res['chats'] = $chats;
        $res['groups'] = $groups;

        $res['status'] = 'success';
        return response()->json($res);
    }
    public function addUserToGroup(request $request){
        $res = array();
        $data = array();
        $data['group_id'] = $request->group_id;
        $data['user_id'] = $request->user_id;
        $data['created_by'] = $request->user->id;
        ProjectChatGroupUser::create($data);

        $project_group = ProjectChatGroup::where('id', $request->group_id)->first();
        $project = Project::where('id', $project_group->project_id)->first();
        
        $text = $request->user->first_name . ' ' . $request->user->last_name . ' has added you to a new chat: ' . $project_group->group_name . ' for project: ' . $project->project_name . ' for ';
        $notification['title'] = 'Sirvez | New Chat';
        $notification['text1'] = $text;
        $notification['text2'] = '.';
        $notification['id'] = 'group-chat-' . $project_group->id;
        $notification['image'] = $request->user->profile_pic;
        $notification['user_id'] = array(intval($request->user_id));
        $notification['created_by'] = $request->user->id;
        $notification['action_link'] = '/app/project/live/' . $project->id;

        broadcast(new NotificationEvent($notification))->toOthers();


        $res['status'] = 'success';
        return response()->json($res);
    }
    public function removeUserToGroup(request $request){
        $res = array();
        $data = array();
        $data['group_id'] = $request->group_id;
        $data['user_id'] = $request->user_id;
        $data['created_by'] = $request->user->id;
        ProjectChatGroupUser::where(['group_id'=>$request->group_id,'user_id'=>$request->user_id])->delete();
        $res['status'] = 'success';
        return response()->json($res);
    }
    public function changeGroupName(request $request){
        $res = array();
        ProjectChatGroup::where('id',$request->group_id)->update(['group_name'=>$request->group_name]);
        $res['status'] = 'success';
        return response()->json($res);
    }
}
 