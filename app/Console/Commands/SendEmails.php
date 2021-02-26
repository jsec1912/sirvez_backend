<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Task;
use App\Project_user;
use App\TaskComment;
use App\Task_comment_user;
use Mail;
use Log;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('schedule:'.date('Y-m-d h:i:s'));
        
        //get task due
        $task_todos = Task::where('due_by_date',date("Y-m-d", strtotime( '+1 days' ) ))
                            ->where('archived',0)->get();
        Log::info('task_overdue:'.count($task_todos));
        foreach($task_todos as $task_todo){
            $users = Project_user::where('project_users.project_id',$task_todo->id)
                                ->where('project_users.type',2)
                                ->leftJoin('users','users.id','=','project_users.id')
                                ->select('users.*')
                                ->get();
            foreach($users as $pending_user){
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $content = 'There is a due task('.$task_todo->task.') in sirvez.';
                $task_img = '';
                $invitationURL = "https://app.sirvez.com/app/task-manager/my-task";
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$task['task'],"description" =>$task['description'],"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view task'];

                Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','sirvez support team');
                });
                Log::info('email send to:'.$to_name);
            }
        }
        //get task overdue
        $task_todos = Task::where('due_by_date',date("Y-m-d", strtotime( '-1 days' ) ))
                            ->where('archived',0)->get();
        Log::info('task_due:'.count($task_todos));
        foreach($task_todos as $task_todo){
            $users = Project_user::where('project_users.project_id',$task_todo->id)
                                ->where('project_users.type',2)
                                ->leftJoin('users','users.id','=','project_users.id')
                                ->select('users.*')
                                ->get();
            foreach($users as $pending_user){
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $content = 'There is a overdue task('.$task_todo->task.') in sirvez.';
                $task_img = '';
                $invitationURL = "https://app.sirvez.com/app/task-manager/my-task";
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$task['task'],"description" =>$task['description'],"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view task'];

                Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','sirvez support team');
                });                
            }
            Log::info('email send to:'.$to_name);
            
        }
        //get todo due
        $task_todos = TaskComment::where('deadline',date("Y-m-d", strtotime( '-1 days' ) ))
                        ->where('complete',0)->get();
        Log::info('todo_overdue:'.count($task_todos));
        foreach($task_todos as $task_todo){
            $task = Task::whereId($task_todo->task_id)->first();
            $users = Task_comment_user::where('task_comment_users.comment_id',$task_todo->id)
                                ->leftJoin('users','users.id','=','task_comment_users.user_id')
                                ->select('users.*')
                                ->get();
            foreach($users as $pending_user){
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $content = 'There is a overdue todo thing('.$task_todo->comment.') in sirvez.';
                $task_img = '';
                $invitationURL = "https://app.sirvez.com/app/task-manager/my-task";
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$task['task'],"description" =>$task['description'],"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view task'];

                Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','sirvez support team');
                });
                Log::info('email send to:'.$to_name);
            }
        }
        //get todo due
        $task_todos = TaskComment::where('deadline',date("Y-m-d", strtotime( '+1 days' ) ))
                            ->where('complete',0)->get();
        Log::info('todo_due:'.count($task_todos));
        foreach($task_todos as $task_todo){
            $task = Task::whereId($task_todo->task_id)->first();
            $users = Task_comment_user::where('task_comment_users.comment_id',$task_todo->id)
                                ->leftJoin('users','users.id','=','task_comment_users.user_id')
                                ->select('users.*')
                                ->get();
            foreach($users as $pending_user){
                $to_name = $pending_user['first_name'];
                $to_email = $pending_user['email'];
                $content = 'There is a due todo thing('.$task_todo->comment.') in sirvez.';
                $task_img = '';
                $invitationURL = "https://app.sirvez.com/app/task-manager/my-task";
                $data = ['name'=>$pending_user['first_name'], "content" => $content,"title" =>$task['task'],"description" =>$task['description'],"img"=>$task_img,"invitationURL"=>$invitationURL,"btn_caption"=>'Click here to view task'];

                Mail::send('temp', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('sirvez notification.');
                    $message->from('support@sirvez.com','sirvez support team');
                });
                Log::info('email send to:'.$to_name);
            }
        }
                        
        return 1;
    }
}
