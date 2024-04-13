<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hashids\Hashids;
use Illuminate\Http\Request;
class IssueDocuments extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'issue_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['issue_id','file_name','file_path'];

    /**
     * Get Parent Objective of Tasks..
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issue(){
        return  $this->belongsTo('App\Issue');
    }

    public static function uploadDocuments($issue_id,$request){
        $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
        $issue_id_encoded = $issueIDHashID->encode($issue_id);
        if($request->hasFile('documents')) {
            $files = $request->file('documents');
            if(count($files) > 0){
                $totalAvailableDocs = IssueDocuments::where('issue_id',$issue_id)->get();
                $totalAvailableDocs= count($totalAvailableDocs) + 1;
                foreach($files as $index=>$file){
                    if(!empty($file)){
                        $rules = ['document' => 'required', 'extension' => 'required|in:doc,docx,pdf,txt,jpg,png,ppt,pptx,jpeg,doc,xls,xlsx'];
                        $fileData = ['document' => $file, 'extension' => strtolower($file->getClientOriginalExtension())];

                        // doing the validation, passing post data, rules and the messages
                        $validator = \Validator::make($fileData, $rules);
                        if (!$validator->fails()) {
                            if ($file->isValid()) {
                                $destinationPath = base_path().'/uploads/issue/'.$issue_id_encoded; // upload path
                                if(!\File::exists($destinationPath)){
                                    $oldumask = umask(0);
                                    @mkdir($destinationPath, 0775); // or even 01777 so you get the sticky bit set
                                    umask($oldumask);
                                }
                                $file_name =$file->getClientOriginalName();
                                $extension = $file->getClientOriginalExtension(); // getting image extension
                                //$fileName = $task_id.'_'.$index . '.' . $extension; // renaming image
                                $fileName = $issue_id_encoded.'_'.$totalAvailableDocs . '.' . $extension; // renaming image
                                $file->move($destinationPath, $fileName); // uploading file to given path

                                // insert record into task_documents table
                                $path = $destinationPath.'/'.$fileName;
                                IssueDocuments::create([
                                    'issue_id'=>$issue_id,
                                    'file_name'=>$file_name,
                                    'file_path'=>'uploads/issue/'.$issue_id_encoded.'/'.$fileName
                                ]);
                                $totalAvailableDocs++;
                            }
                        }
                    }
                }
            }
        }
    }
}
