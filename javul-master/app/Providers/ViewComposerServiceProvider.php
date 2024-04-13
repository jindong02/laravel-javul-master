<?php


namespace App\Providers;


use App\Http\Controllers\Mc;
use App\Models\Fund;
use App\Models\Issue;
use App\Models\Objective;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserMessages;
use App\Models\UserNotification;
use Hashids\Hashids;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*',function($view){
            $view->with('authUserObj',auth()->user());
            $view->with('totalUnits',Unit::count());
            $view->with('totalObjectives',Objective::count());
            $view->with('totalTasks',Task::count());
            $view->with('totalIssues',Issue::count());
            $view->with('totalFundsAvailable',Fund::where('status', 'approved')->where('transaction_type', 'donated')->sum('amount'));
        });

        //----------------- for footer ---------------------------
        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unitCategoryIDHashID = new Hashids('unit category id hash',10,Config::get('app.encode_chars'));
        $objectiveIDHashID = new Hashids('objective id hash',10,Config::get('app.encode_chars'));
        $taskIDHashID = new Hashids('task id hash',10,Config::get('app.encode_chars'));
        $taskDocumentIDHashID = new Hashids('task document id hash',10,Config::get('app.encode_chars'));
        $issueIDHashID = new Hashids('issue id hash',10,Config::get('app.encode_chars'));
        $issueDocumentIDHashID = new Hashids('issue document id hash',10,Config::get('app.encode_chars'));
        $jobSkillIDHashID = new Hashids('job skills id hash',10,Config::get('app.encode_chars'));
        $areaOfInterestIDHashID = new Hashids('area of interest id hash',10,Config::get('app.encode_chars'));
        $btcTransactionIDHashID = new Hashids('btc transaction id hash',10,Config::get('app.encode_chars'));

        $loggedInUser = 0;
        if(DB::connection()->getSchemaBuilder()->hasTable('users')){
            $loggedInUser = \DB::table('users')->whereRaw('unix_timestamp() - loggedin < 30')->count();
        }

        view()->share('totalLoggedinUsers',$loggedInUser);

        //Get all system messages
        $user_msg = new UserMessages;
        $user_messages = $user_msg->getAllMessages();
        view()->share('user_messages',json_encode($user_messages));
        //end

        $totalUsers = 0;
        if(DB::connection()->getSchemaBuilder()->hasTable('users')){
            $totalUsers = User::count();
        }
        view()->share('totalRegisteredUsers',$totalUsers );
        view()->share('userIDHashID',$userIDHashID );
        view()->share('unitIDHashID',$unitIDHashID );
        view()->share('unitCategoryIDHashID',$unitCategoryIDHashID);
        view()->share('objectiveIDHashID',$objectiveIDHashID );
        view()->share('taskIDHashID',$taskIDHashID );
        view()->share('taskDocumentIDHashID',$taskDocumentIDHashID);
        view()->share('issueIDHashID',$issueIDHashID);
        view()->share('issueDocumentIDHashID',$issueDocumentIDHashID);
        view()->share('jobSkillIDHashID',$jobSkillIDHashID);
        view()->share('areaOfInterestIDHashID',$areaOfInterestIDHashID);
        view()->share('btcTransactionIDHashID',$btcTransactionIDHashID);

        view()->composer('elements.header',function($view){
            Mc::putMcData();
            $question=Mc::getMcQuestion();
            $view->with('report_question',$question);

            $notificationCount = 0;
            if(auth()->check()) {
                $notificationCount = UserNotification::where('user_id',auth()->user()->id)->where('message_read',0)->count();
            }
            $view->with('notificationCount',$notificationCount);
        });

//        dd($unitIDHashID);
        /*view()->composer('elements.site_activities',function($view){
            $view->with('site_activity',SiteActivity::take(10)->orderBy('created_at','desc')->get());
        });*/
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
