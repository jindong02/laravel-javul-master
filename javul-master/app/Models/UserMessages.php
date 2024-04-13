<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserMessages extends Model
{
	public $message = [
		//Issue
		'ISSUE_CREATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Issue created successfully'),
		'ISSUE_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Issue updated successfully'),
		//Objective
		'OBJECTIVE_CREATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Objective created successfully'),
		'OBJECTIVE_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Objective updated successfully'),
		//Task
		'TASK_CREATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task created successfully'),
		'TASK_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task updated successfully'),
		'TASK_STATUS_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task status updated successfully'),
		'TASK_BID' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task bid successfully'),
		'TASK_COMPLETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task completed successfully'),
		'TASK_ASSIGNED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task assigned successfully'),
		'TASK_WHERE_NOT_FOUND' => array('type'=>'info','auto-dismiss'=>true,'text'=>'No tasks found'),
		'TASK_CANCELLED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task cancelled successfully'),
		'TASK_HAS_BEEN_ASSIGNED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task Assigned!','continue_text'=>' Your bid has been selected and task','continue_text_too'=>'has been assigned to you.'),
		'TASK_ASSIGNED_BID_SELECTED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task Assigned!','continue_text'=>' Your bid has been selected and task'),
		'TASK_RE_ASSIGNED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task Re-Assigned!','continue_text'=>'The task','continue_text_too'=>'has been re-assigned to you.'),
		//Unit
		'UNIT_CREATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Unit created successfully'),
		'UNIT_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Unit updated successfully'),
		//area of interest
		'PLEASE_SELECT_AREA_OF_INTEREST' => array('type'=>'info','auto-dismiss'=>true,'text'=>'Please select area of interest'),
		'AREA_OF_INTEREST_ADDED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Area of interest added successfully'),
		'AREA_OF_INTEREST_DELETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Area of interest deleted successfully'),
		'AREA_OF_INTEREST_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Area of interest updated successfully'),
		'AREA_OF_INTEREST_APPROVED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Area of interest approved successfully'),
		//Unit
		'PLEASE_SELECT_CATEGORY' => array('type'=>'info','auto-dismiss'=>true,'text'=>'Please select category'),
		'UNIT_CATEGORY_ADDED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Unit category added successfully'),
		'UNIT_CATEGORY_DELETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Unit category deleted successfully'),
		'UNIT_CATEGORY_APPROVED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Unit category approved successfully'),
		'FEATURED_UNIT_SET_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Featured unit set successfully'),
		'FEATURED_UNIT_REMOVED_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Featured unit removed successfully'),
		'UNIT_DELETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Unit deleted successfully'),
		//Objective
		'OBJECTIVE_NOT_FOUND' => array('type'=>'info','auto-dismiss'=>true,'text'=>'No Objectives found'),
		'OBJECTIVE_DELETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Objective deleted successfully'),
		//Task
		'TASK_DELETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task deleted successfully'),
		'TASK_SUMMARY_CAN_ACCEPT_MAXIMUM_1000_CHARACTER' => array('type'=>'info','auto-dismiss'=>true,'text'=>'Task summary is limited to 1000 characters'),
		'TASK_ASSIGN_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Task assigned successfully'),
		//Jobs
		'JOB_CATEGORY_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Job category updated successfully'),
		'JOB_SKILL_ADDED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Job skill added successfully'),
		'JOB_SKILL_DELETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Job skill deleted successfully'),
		'JOB_SKILL_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Job skill updated successfully'),
		'JOB_SKILL_APPROVED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Job skill approved successfully'),
		'JOB_SKILL_CHANGES_DISCARDED' => array('type'=>'info','auto-dismiss'=>true,'text'=>'Job skill changes discarded'),
		//Skill
		'PLEASE_SELECT_SKILL' => array('type'=>'info','auto-dismiss'=>true,'text'=>'Please select skill'),
		'DOCUMENT_DELETED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Document deleted successfully'),
		'REQUEST_SUBMITTED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Request submitted successfully'),
		'SOMETHING_GOES_WRONG' => array('type'=>'error','auto-dismiss'=>true,'text'=>'Unknown error. Please try again later'),
		'REMOVED_FROM_WATCH_LIST' => array('type'=>'success','auto-dismiss'=>true,'text'=>' removed from your watchlist'),
		'ENABLED_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>' enabled successfully'),
		'DISABLED_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>' disabled successfully'),
		'AMOUNT_TRANSFERED_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Amount transferred successfully'),
		'REQUEST_SEND_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Request sent successfully'),
		'CREDIT_CARD_DETAILS_UPDATED' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Credit card details updated'),
		'PROFILE_UPDATED_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Profile updated successfully'),
		//Site Message
		'THANK_YOU_YOUR_MESSAGE_WAS_SENT_TO_JAVUL' => array('type'=>'success','auto-dismiss'=>true,'text'=>'Thank you! Your message was sent to the Javul.org admin.'),
		'PLEASE_ENTER_VALID_SEARCH_TERM' => array('type'=>'info','auto-dismiss'=>true,'text'=>'Please enter valid search term and try again.'),
		'PLEASE_FILL_PROPER_DETAILS' => array('type'=>'error','auto-dismiss'=>true,'text'=>'Please fill proper details and try again.'),
		// Messages /Inbox
		'MESSAGE_SENT_SUCCESSFULLY' => array('type'=>'success','auto-dismiss'=>true,'text'=>"Message sent successfully"),
		'ERROR_IN_SENDING_MESSAGE' => array('type'=>'success','auto-dismiss'=>true,'text'=>"Error in Sending Message"),
	];

	public function getMessage($key){
		if(isset($this->message[$key])){
			return $this->message[$key];
		}else
			return '';
	}
	public function getAllMessages(){
		return $this->message;
	}
}
