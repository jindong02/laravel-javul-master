<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\TaskEditor;
use Illuminate\Console\Command;

class SubmitForApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SubmitForApproval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit for approval all task editors after 7 days of edit.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $all_tasks = TaskEditor::groupBy('task_id')->select('task_id')->get();
        if(!empty($all_tasks)){
            foreach($all_tasks as $task){
                $first_editor = TaskEditor::where('task_id','=',$task->task_id)
                                                ->where('first_user_to_submit','=','yes')
                                                ->select('task_id','updated_at')
                                                ->first();
                $all_other_editors = TaskEditor::where('task_id','=',$task->task_id)->where('first_user_to_submit','=','no')->get();
                if(!empty($all_other_editors)){
                    foreach($all_other_editors as $editor){
                        if(date('Y-m-d',strtotime('+ 7 days',strtotime($first_editor->updated_at))) <= date('Y-m-d')){
                            TaskEditor::where('id','=',$editor->id)->update(['submit_for_approval'=>'submitted']);
                        }
                    }
                }
                $taskObj = Task::find($task->task_id);
                if(!empty($taskObj ))
                    $taskObj->update(['status'=>'approval']);
            }
        }
    }
}
