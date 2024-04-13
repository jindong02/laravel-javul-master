<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AlertsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ElfinderController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\FundsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IssuesController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ObjectivesController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserWikiController;
use App\Http\Controllers\WikiController;
use App\Http\Controllers\ZcashController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
// Route::get('/clear-cache', function() {
//     Artisan::call('cache:clear');
//     return "Cache is cleared";
// });

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/global_search', [HomeController::class, 'global_search']);
Route::post('/check_username', [HomeController::class, 'check_username']);
Route::post('/check_email', [HomeController::class, 'check_email']);
//protecting pages from back button after logout
Route::group(['middleware' => 'prevent-back-history', 'auth'], function () {
    Route::get('/account', [AccountController::class, 'index']);

//    Route::post('/account/logout', 'AccountController@logout')->name("logout");
    Auth::routes();
    Route::get('/account/check_user_login', [AccountController::class, 'check_user_login']);
    Route::post('/account/upload_profile', [AccountController::class, 'upload_profile']);
    Route::post('/account/remove_profile_pic', [AccountController::class, 'remove_profile_pic']);
    Route::post('/account/withdraw', [AccountController::class, 'withdraw']);
    Route::post('/account/paypal_email_check', [AccountController::class, 'paypal_email_check']);
    Route::get('/account/get_notifications', [AccountController::class, 'get_notifications']);
    Route::post('/account/update_notifications', [AccountController::class, 'update_notifications']);
    Route::post('/account/update-creditcard', [AccountController::class, 'update_creditcard']);
    Route::post('/account/update_personal_info', [AccountController::class, 'update_personal_info']);

    Route::get('/my_tasks', [UserController::class, 'my_tasks']);
    Route::get('/my_contributions', [UserController::class, 'my_contribution']);

    // message controller route
    Route::get('inbox', [MessageController::class, 'inbox'])->name("message_inbox");
    Route::post('inbox/new_msg', [MessageController::class, 'new_msg']);
    Route::get('message/sent', [MessageController::class, 'sent']);
    Route::get('message/view/{message_id}', [MessageController::class, 'view']);
    Route::any('message/send/{user_id?}', [MessageController::class, 'send']);

    //Wiki pages
    Route::get('users/{slug}/{user_id}/wiki/new_page/{page_id?}', [UserWikiController::class, 'page_create'])->name("user_wiki_newpage")->middleware('auth');
    Route::get('users/{slug}/{user_id}/wiki/edit_page/{page_id}', [UserWikiController::class, 'page_create'])->name("user_wiki_editpage")->middleware('auth');
    Route::post('users/{user_id}/save_page', [UserWikiController::class, 'save_pagedata'])->name("user_wiki_save_page")->middleware('auth');

    //Homecontroller
    Route::get('/my_watchlist', [HomeController::class, 'my_watchlist']);
    Route::get('/remove_from_watchlist', [HomeController::class, 'remove_from_watchlist']);
    Route::get('/my_alerts', [HomeController::class, 'my_alerts']);
    Route::any('/site_admin', [HomeController::class, 'site_admin']);
    Route::any('/skills/get_skill_paginate', [HomeController::class, 'get_skill_paginate']);
    Route::any('/category/get_category_paginate', [HomeController::class, 'get_category_paginate']);
    Route::any('/area_of_interest/get_area_of_interest_paginate', [HomeController::class, 'get_area_of_interest_paginate']);
    Route::any('job_skills/get_skills', [HomeController::class, 'get_skills']);
    Route::any('job_skills/get_next_level_skills', [HomeController::class, 'get_next_level_skills']);
    Route::get('job_skills/approve_skill', [HomeController::class, 'approveSkill']);
    Route::get('job_skills/discard_skill_changes', [HomeController::class, 'discard_skill_change']);
    Route::get('job_skills/browse_skills', [HomeController::class, 'browse_skills']);
    Route::any('/job_skills/add', [HomeController::class, 'skill_add']);
    Route::any('/job_skills/delete', [HomeController::class, 'skill_delete']);
    Route::any('/job_skills/edit', [HomeController::class, 'skill_edit']);
    Route::post('/job_skills/update_user_skill', [HomeController::class, 'update_user_skill']);
    Route::any('unit_category/add', [HomeController::class, 'category_add']);
    Route::any('unit_category/edit', [HomeController::class, 'category_edit']);
    Route::any('unit_category/delete', [HomeController::class, 'category_delete']);
    Route::get('unit_category/approve_category', [HomeController::class, 'approve_category']);
    Route::get('unit_category/discard_category_changes', [HomeController::class, 'discard_category_changes']);
    Route::any('area_of_interest/get_area_of_interest', [HomeController::class, 'get_area_of_interest']);
    Route::any('area_of_interest/get_next_level_area_of_interest', [HomeController::class, 'get_next_level_area_of_interest']);
    Route::any('area_of_interest/add', [HomeController::class, 'area_of_interest_add']);
    Route::any('area_of_interest/edit', [HomeController::class, 'area_of_interest_edit']);
    Route::any('area_of_interest/delete', [HomeController::class, 'area_of_interest_delete']);
    Route::get('area_of_interest/approve_area_of_interest', [HomeController::class, 'approve_area_of_interest']);
    Route::get('area_of_interest/discard_area_of_interest_changes', [HomeController::class, 'discard_area_of_interest_changes']);
    Route::get('area_of_interest/browse_area_of_interest', [HomeController::class, 'browse_area_of_interest']);
    Route::any('/category/add', [HomeController::class, 'category_add']);
    Route::any('/area_of_interest/add', [HomeController::class, 'area_of_interest_add']);
    //Route::any('/job_skills/{skill_id}/edit','HomeController@skill_edit');
    Route::any('/category/{category_id}/edit', [HomeController::class, 'category_edit']);
    Route::any('/area_of_interest/{area_id}/edit', [HomeController::class, 'area_of_interest_edit']);
    Route::get('/category/{category_id}', [HomeController::class, 'category_view']);
    Route::any('/area_of_interest/{area_id}', [HomeController::class, 'area_of_interest_view']);

    //UnitsController route
    Route::any('units/add', [UnitsController::class, 'create']);
    Route::get('units/{unitid}/revisions', [UnitsController::class, 'revison'])->name('unit_revison');
    Route::get('units/{unitid}/revisions/{revision_id}', [UnitsController::class, 'revisonview'])->name('unit_revison_view');
    Route::get('units/{unitid}/diff/{rev1}/{rev2}', [UnitsController::class, 'diff'])->name('unit_revison_cmp');
    Route::get('unit/set_featured_unit', [UnitsController::class, 'set_featured_unit']);
    Route::any('units/{unitid}/edit', [UnitsController::class, 'edit']);
    Route::post('units/get_featured_unit', [UnitsController::class, 'get_featured_unit']);
    Route::get('units/delete_unit', [UnitsController::class, 'delete_unit']);
    Route::get('units/available_bid/{unit_id}', [UnitsController::class, 'available_bids']);
    Route::get('units/search_by_location', [UnitsController::class, 'search_by_location']);

    //TasksController
    Route::any('tasks/add', [TasksController::class,'create']);
    Route::get('tasks/{taskid}/revisions', [TasksController::class,'revison'])->name('tasks_revison');
    Route::get('tasks/{taskid}/revisions/{revision_id}', [TasksController::class,'revisonview'])->name('unit_tasks_view');
    Route::get('tasks/{taskid}/diff/{rev1}/{rev2}', [TasksController::class,'diff'])->name('tasks_revison_cmp');
    Route::any('tasks/{unitid}/{objectiveid}/add', [TasksController::class,'create']);
    Route::post('tasks/get_objective', [TasksController::class,'get_objective']);
    Route::post('tasks/get_tasks', [TasksController::class,'get_tasks']);
    Route::get('tasks/get_biding_details', [TasksController::class,'get_biding_details']);
    Route::get('tasks/check_assigned_task', [TasksController::class,'check_assigned_task']);
    Route::get('tasks/accept_offer', [TasksController::class,'accept_offer']);
    Route::get('tasks/reject_offer', [TasksController::class,'reject_offer']);
    Route::any('tasks/remove_task_document', [TasksController::class,'remove_task_documents']);
    Route::any('tasks/submit_for_approval', [TasksController::class,'submit_for_approval']);
    Route::get('tasks/delete_task', [TasksController::class,'delete_task']);
    Route::get('tasks/assign', [TasksController::class,'assign_task']);
    Route::any('tasks/cancel_task/{task_id}', [TasksController::class,'cancel_task']);
    Route::any('tasks/complete_task/{task_id}', [TasksController::class,'complete_task']);
    Route::any('tasks/re_assign/{task_id}', [TasksController::class,'re_assign']);
    Route::post('tasks/mark_task_complete/{task_id}', [TasksController::class,'mark_as_complete']);
    Route::any('tasks/{taskid}/edit', [TasksController::class,'edit']);
    Route::any('tasks/bid_now/{task_id}', [TasksController::class,'bid_now']);
    Route::any('tasks/{taskid}/edit/{task_status}', [TasksController::class,'edit']);

    //ForumController
    //Route::get('forum/{unit_id}/{section_id}', 'ForumController@view');
    Route::post('forum/submit', [ForumController::class, 'submit']);
    Route::post('forum/submitauto', [ForumController::class, 'submitauto']);
    Route::post('forum/loadObjectiveComment', [ForumController::class, 'loadObjectiveComment']);
    Route::post('forum/postSubmit', [ForumController::class, 'postSubmit']);
    Route::post('forum/postLoad', [ForumController::class, 'postLoad']);
    Route::post('forum/postUpDown', [ForumController::class, 'postUpDown']);
    Route::post('forum/ideapoint', [ForumController::class, 'ideapoint']);
    Route::post('forum/post_ideapoint', [ForumController::class, 'post_ideapoint']);
    Route::post('forum/topicUpDown', [ForumController::class, 'topicUpDown']);
    Route::get('forum/create/{unit_id}/{section_id}', [ForumController::class, 'create']);
    Route::get('forum/post/{unit_id}/{slug}', [ForumController::class, 'post']);
    Route::get('forum/{unit_id}', [ForumController::class, 'index']);
    Route::get('forum/{unit_id}/{section_id}', [ForumController::class, 'view']);

    //IssuesController
    Route::get('issues/remove_issue_document', [IssuesController::class, 'remove_document']);
    Route::get('issues/{issue_id}/revisions', [IssuesController::class, 'revison'])->name('issues_revison');
    Route::get('issues/{issue_id}/revisions/{revision_id}', [IssuesController::class, 'revisonview'])->name('unit_issues_view');
    Route::get('issues/{issue_id}/diff/{rev1}/{rev2}', [IssuesController::class, 'diff'])->name('issues_revison_cmp');
    Route::get('/issues/get_issues_paginate', [IssuesController::class, 'get_issues_paginate']);
    Route::any('/issues/add', [IssuesController::class, 'add']);
    Route::post('issues/importance', [IssuesController::class, 'add_importance']);
    Route::post('issues/sort_issue', [IssuesController::class, 'sort_issues']);
    Route::any('issues/{unit_id}/add', [IssuesController::class, 'create']);
    Route::any('issues/{issue_id}/edit', [IssuesController::class, 'edit']);
    Route::any('issues/{unit_id}/{objective_id}/add', [IssuesController::class, 'create']);
    Route::any('issues/{unit_id}/{objective_id}/{task_id}/add', [IssuesController::class, 'create']);

    //WikiController
    Route::get('wiki/menu/{unit_id}/{slug}', [WikiController::class, 'menu']);
    Route::get('wiki/all_pages/{unit_id}/{slug}', [WikiController::class, 'pages']);
    //Route::get('wiki/view_history/{unit_id}/{slug}', 'WikiController@changes');
    Route::any('wiki/edit/{unit_id}/{slug}/{wiki_page_id?}', [WikiController::class, 'edit']);
    Route::any('wiki/edit_revision/{unit_id}/{slug}/{wiki_page_rev_id?}', [WikiController::class, 'edit_revision']);
    Route::get('wiki/diff/{unit_id}/{revision_id}/{slug}', [WikiController::class, 'difference']);
    Route::get('wiki/diff/{unit_id}/{revision_id}/{compare_id}/{slug}', [WikiController::class, 'difference_selected']);
    Route::get('wiki/revision_view/{unit_id}/{revision_id}/{slug}', [WikiController::class, 'revision_view']);
    Route::get('wiki/recent_changes/{unit_id}/{slug}', [WikiController::class, 'changes']);
    Route::get('wiki/history/{unit_id}/{wiki_page_id}/{slug}', [WikiController::class, 'history_single_page']);
    Route::any('wiki/history/{unit_id?}/{slug}', [WikiController::class, 'history']);

    //ObjectivesController
    Route::any('objectives/add', [ObjectivesController::class, 'create']);
    Route::get('objectives/{objectiveid}/revisions', [ObjectivesController::class, 'revison'])->name('objectives_revison');
    Route::get('objectives/{objectiveid}/revisions/{revision_id}', [ObjectivesController::class, 'revisonview'])->name('unit_objectives_view');
    Route::get('objectives/{objectiveid}/diff/{rev1}/{rev2}', [ObjectivesController::class, 'diff'])->name('objectives_revison_cmp');
    Route::any('objectives/{unitid}/add', [ObjectivesController::class, 'create']);
    Route::any('objectives/{objectiveid}/edit', [ObjectivesController::class, 'edit'])->name('objectives_edit');
    Route::post('objectives/importance', [ObjectivesController::class, 'add_importance']);
    Route::get('objectives/delete_objective', [ObjectivesController::class, 'delete_objective']);

    //ChatController
    Route::post('chat/create_room', [ChatController::class, 'create_room']);
    Route::post('chat/sendmsg', [ChatController::class, 'sendmsg']);
    Route::post('chat/loaduser/{flag?}', [ChatController::class, 'loaduser']);
    Route::post('chat/loadmsg', [ChatController::class, 'loadmsg']);
    Route::post('chat/online', [ChatController::class, 'online']);
    Route::get('chat/{roomid}', [ChatController::class, 'chatroom']);

    //ElfinderController
    Route::get('elfinder/connectorex', [ElfinderController::class, 'showConnector'])->name("elfinder.connectorex")->middleware('auth');
    Route::post('elfinder/connectorex', [ElfinderController::class, 'showConnector'])->name("elfinder.connectorex")->middleware('auth');
});

//HomeController
Route::get('/activities', [HomeController::class, 'global_activities']);
Route::get('/add_to_watchlist', [HomeController::class, 'add_to_watchlist'])->name("add.watchlist");
Route::get('/get_unit_site_activity_paginate', [HomeController::class, 'get_unit_site_activity_paginate']);
Route::get('/get_site_activity_paginate', [HomeController::class, 'get_site_activity_paginate']);
Route::any('unit_category/get_categories', [HomeController::class, 'get_categories']);
Route::get('unit_category/browse_categories', [HomeController::class, 'browse_categories']);
Route::any('unit_categories/get_next_level_categories', [HomeController::class, 'get_next_level_categories']);
Route::get('/job_skills/{skill_id}', [HomeController::class, 'skill_view']);


//UserController
Route::any('/userprofiles/{user_id}', [UserController::class, 'user_profile']);
Route::any('/userprofiles/{user_id}/{slug}', [UserController::class, 'user_profile']);

//NotificationController
Route::any('/notification/success', [NotificationController::class, 'success_payment']);
Route::any('/notification/error', [NotificationController::class, 'error_payment']);
Route::any('/notification/ipn_payment', [NotificationController::class, 'ipn_payment']);
Route::any('/notification/ipn_donation', [NotificationController::class, 'ipn_donation']);
Route::auth();


Route::post('login', [AuthController::class,'login']);

//UserWikiController
Route::get('users/{slug}/{user_id}/wiki', [UserWikiController::class, 'home'])->name("user_wiki_home");
Route::get('users/{slug}/{user_id}/wiki/recent_changes', [UserWikiController::class, 'recent_changes'])->name("user_wiki_recent_changes");
Route::get('users/{slug}/{user_id}/wiki/pages', [UserWikiController::class, 'pagelist'])->name("user_wiki_page_list");
Route::get('users/{slug}/{user_id}/wiki/history/{page_id}', [UserWikiController::class, 'page_history'])->name("user_wiki_history");
Route::get('users/{slug}/{user_id}/wiki/diff/{rev1?}/{rev2?}', [UserWikiController::class, 'page_diff'])->name("user_wiki_rev_diff");
Route::get('users/{slug}/{page_id}/wiki/{page_slug}', [UserWikiController::class, 'view'])->name("user_wiki_view");

//UnitsController
Route::any('units/{unitid}/{slug}', [UnitsController::class, 'view']);
Route::get('units/get_units_paginate', [UnitsController::class, 'get_units_paginate']);
Route::post('units/search_units', [UnitsController::class, 'search_units']);
Route::get('units/search_by_category', [UnitsController::class, 'search_by_category']);
Route::post('units/get_state', [UnitsController::class, 'get_state']);
Route::post('units/get_city', [UnitsController::class, 'get_city']);
Route::get('units/category={type}', [UnitsController::class, 'categoryView']);

//ObjectivesController route
Route::any('objectives/{objectiveid}/{slug}', [ObjectivesController::class, 'view']);
Route::get('objectives/get_objectives_paginate', [ObjectivesController::class, 'get_objectives_paginate']);
Route::get('objectives/{unitid}/lists', [ObjectivesController::class, 'lists']);

//ChatController route
Route::get('chat', [ChatController::class, 'index']);

//WikiController route
Route::get('wiki/home/{unit_id}/{slug}', [WikiController::class, 'home']);
Route::get('wiki/{unit_id}/{wiki_page_id}/{slug}', [WikiController::class, 'view']);

// chat controller route
//ForumController
Route::get('forum', [ForumController::class,'index']);

//TasksController route
Route::any('tasks/{taskid}/{slug}', [TasksController::class,'view']);
Route::get('tasks/get_tasks_paginate', [TasksController::class,'get_tasks_paginate']);
Route::get('tasks/{unitid}/lists', [TasksController::class,'lists']);
Route::post('tasks/search_tasks', [TasksController::class,'search_tasks']);
Route::get('tasks/search_by_skills', [TasksController::class,'search_by_skills']);
Route::get('tasks/search_by_status', [TasksController::class,'search_by_status']);

//FundsController
Route::get('funds/donate/unit/{unit_id}', [FundsController::class, 'donate_to_unit_objective_task']);
Route::get('funds/donate/objective/{objective_id}', [FundsController::class, 'donate_to_unit_objective_task']);
Route::get('funds/donate/task/{task_id}', [FundsController::class, 'donate_to_unit_objective_task']);
Route::get('funds/donate/issue/{issue_id}', [FundsController::class, 'donate_to_unit_objective_task']);
Route::get('funds/donate/user/{user_id}', [FundsController::class, 'donate_to_unit_objective_task']);
Route::get('funds/get-card-name', [FundsController::class, 'get_card_name']);
Route::post('funds/donate-amount', [FundsController::class, 'donate_amount']);
Route::post('funds/transfer-from-unit', [FundsController::class, 'transfer_from_unit']);
Route::get('funds/success', [FundsController::class, 'success']);
Route::get('funds/cancel', [FundsController::class, 'cancel']);

//IssuesController
Route::any('issues/{unit_id}/lists', [IssuesController::class, 'lists']);
Route::any('issues/{issue_id}/view', [IssuesController::class, 'view']);
Route::post('reportconc', [IssuesController::class, 'report_concern_email']);
Route::get('close_report', [IssuesController::class, 'reset_captcha_after_close']);

//AlertsController
Route::post('alerts/set_alert', [AlertsController::class, 'set_alert']);

Route::resource('/issues', IssuesController::class);
Route::resource('/objectives', ObjectivesController::class);
Route::resource('/tasks', TasksController::class);
Route::resource('/units', UnitsController::class);
Route::resource('/user', UserController::class);
Route::resource('/funds', FundsController::class);
Route::resource('/alerts', AlertsController::class);

/**
 * Zcash Routes
 */
Route::any('zcash/check_zcash_payment', [ZcashController::class, 'check_zcash_payment']); // it will check payment has been done or not for donation
Route::any('zcash/webhook', [ZcashController::class, 'webhook_notification']);
//Perform action to Proceed and Cancel Request
Route::any('zcash/proceed/{zcash_transaction_id}', [ZcashController::class, 'transfer_zcash']);
Route::any('zcash/cancel/{zcash_transaction_id}', [ZcashController::class, 'cancel_transfer_request']);
Route::post('/account/request-to-transfer-zcash', [AccountController::class, 'request_to_transfer_zcash']);
