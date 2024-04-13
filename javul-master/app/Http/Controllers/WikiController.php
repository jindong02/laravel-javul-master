<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Wiki;
use Hashids\Hashids;
use App\Models\SiteActivity;
use App\Models\Fund;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class WikiController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth',['except'=>['home','view']]);
        view()->share('site_activity_text','Unit Activity Log');
    }

    public function home($unit_id = '',$slug = '')
    {
        view()->share("unit_id",$unit_id);
        view()->share("slug",$slug);
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
            $unit_id = $unit_id[0];
            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
                $filter = array(
                    'unit_id' => $unit_id,
                    'is_wikihome' => 1,
                );
                 Wiki::createNotExist($unit,$filter);
                $pages = Wiki::getPage($filter);
                if(empty($pages['pages'])){
                     echo "<pre>"; print_r($unit); echo "</pre>";die;
                }
                    view()->share("wiki_page",$pages['pages'][0]);
                return view("wiki.home");
            }
        }
        return view("errors.404");
    }
    public function menu($unit_id = '',$slug = '')
    {
        view()->share("unit_id",$unit_id);
        view()->share("slug",$slug);
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
            $unit_id = $unit_id[0];
            $unit = Unit::getUnitWithCategories($unit_id);
           // print_r($unit);die;
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
                $filter = array(
                    'unit_id' => $unit_id,
                    'is_wikihome' => 3,
                );

                Wiki::createNotExist($unit,$filter);
                $pages = Wiki::getPage($filter);
                if(empty($pages['pages'])){
                     echo "<pre>"; print_r($unit); echo "</pre>";die;
                }
                    view()->share("wiki_page",$pages['pages'][0]);
                return view("wiki.menu_page");
            }
        }
        return view("errors.404");
    }
    public function pages($unit_id = '',$slug = '')
    {
        view()->share("unit_id",$unit_id);
        view()->share("slug",$slug);
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
            $unit_id = $unit_id[0];
            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
                $filter = array(
                    'unit_id' => $unit_id,
                    'is_wikihome' => 0,
                );
                $pages = Wiki::getPage($filter);
                view()->share("pages",$pages);
                return view("wiki.page_list");
            }
        }
        return view("errors.404");
    }
    public function view($unit_id ,$wiki_page_id, $slug)
    {
        view()->share("slug",$slug);
        view()->share("unit_id",$unit_id);
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
            $unit_id = $unit_id[0];

            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                $filter = array(
                    'unit_id' => $unit_id,
                    'wiki_page_id' => $wiki_page_id,
                    'is_wikihome' => 0,
                );
                $pages = Wiki::getPage($filter);
                if(!empty($pages['pages'])){
                    view()->share("wiki_page",$pages['pages'][0]);
                    return view("wiki.view");
                }
            }
        }
        return view("errors.404");
    }
    public function history_single_page($unit_id ,$wiki_page_id, $slug)
    {
        view()->share("slug",$slug);
        view()->share("unit_id",$unit_id);
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
            $unit_id = $unit_id[0];
            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
                $filter = array(
                    'unit_id' => $unit_id,
                    'wiki_page_id' => $wiki_page_id,
                );
                $pages = Wiki::getPage($filter,false,false);
                if(!empty($pages['pages'])){
                    view()->share("wiki_page",$pages['pages'][0]);
                    $filter = array(
                        'unit_id' => $unit_id,
                        'userlink' => true,
                        'wiki_page_id' => $wiki_page_id,
                    );
                    $changes = Wiki::getChanges($filter);
                    view()->share("changes",$changes);
                    return view("wiki.history_single_page");

                }
            }
        }
        return view("errors.404");
    }

    public function changes($unit_id , $slug)
    {
        view()->share("slug", $slug);
        view()->share("unit_id", $unit_id);
        $unitIDHashID = new Hashids('unit id hash', 10, Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if (!empty($unit_id))
        {
            $unit_id = $unit_id[0];
            $unit = Unit::getUnitWithCategories($unit_id);
            if (!empty($unit))
            {
                $site_activity = SiteActivity::where('unit_id', $unit_id)->orderBy('id', 'desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds = Fund::getUnitDonatedFund($unit_id);
                $awardedFunds = Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj', $unit);
                view()->share('unit_activity_id', $unit_id);
                view()->share('availableFunds', $availableFunds);
                view()->share('awardedFunds', $awardedFunds);
                view()->share('site_activity', $site_activity);

                $filter = array(
                    'unit_id' => $unit_id,
                    'wiki_page_id' => $wiki_page_id,
                );
                $pages = Wiki::getPage($filter, false, false);
                if (!empty($pages['pages'])) {
                    view()->share("wiki_page", $pages['pages'][0]);
                    $filter = array(
                        'unit_id' => $unit_id,
                        'userlink' => true,
                        'wiki_page_id' => $wiki_page_id,
                    );
                    $model = new Wiki();
                    $changes = $model->getChanges($filter);
                    view()->share("changes", $changes);
                    return view("wiki.changes_list");

                    //}
                }
            }
            return view("errors.404");
        }
    }

    public function difference($unit_id ,$revision_id, $slug)
    {
        view()->share("slug",$slug);
        view()->share("unit_id",$unit_id);
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
            $unit_id = $unit_id[0];
            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                $filter = array(
                    'unit_id' => $unit_id,
                    'revision_id' => $revision_id,
                );
                $difference = Wiki::getDifference($filter);

                if(!empty($difference)){
                    view()->share("difference",$difference);
                    return view("wiki.changes_difference");
                }
            }
        }
        return view("errors.404");
    }

    public function difference_selected($unit_id ,$revision_id, $compare_id,$slug)
    {
        view()->share("slug",$slug);
    	view()->share("unit_id",$unit_id);
       // echo $revision_id ." ". $compare_id;die;
    	$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
        	$unit_id = $unit_id[0];
        	$unit = Unit::getUnitWithCategories($unit_id);
	        if(!empty($unit)){
	        	$site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                $filter = array(
                    'unit_id' => $unit_id,
                    'revision_id' => $revision_id,
                    'compare_id' => $compare_id,
                );
                $difference = Wiki::getDifference_idwise($filter);

                if(!empty($difference) && isset($difference['compare']) ){
                    view()->share("difference",$difference);
	        	    return view("wiki.changes_difference");
                }
	        }
        }
    	return view("errors.404");
    }

    public function revision_view($unit_id ,$revision_id, $slug)
    {
        view()->share("slug",$slug);
        view()->share("unit_id",$unit_id);
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
            $unit_id = $unit_id[0];
            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                $filter = array(
                    'unit_id' => $unit_id,
                    'revision_id' => $revision_id,
                );
                $pages = Wiki::getPageFromRevision($filter);

                if(!empty($pages)){
                    view()->share("wiki_page",$pages);
                    return view("wiki.revision_view");
                }
            }
        }
        return view("errors.404");
    }

    public function history($unit_id = '')
    {
    	view()->share("unit_id",$unit_id);
    	$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
        	$unit_id = $unit_id[0];
        	$unit = Unit::getUnitWithCategories($unit_id);
	        if(!empty($unit)){
	        	$site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

	        	$wiki_id = Wiki::createNotExist($unit);
	        	$filter = array('wiki_id' => $wiki_id);
	        	$wiki = Wiki::detail($filter);
	        	$wikiHistory = Wiki::detailHistory($filter);
	        	view()->share("wiki",$wiki);
	        	view()->share("wikiHistory",$wikiHistory);
	        	return view("wiki.history");
	        }
        }
    	return view("errors.404");
    }
    public function edit(  Request $request ,$unit_id , $slug , $wiki_page_id = 0 )
    {

        view()->share("wiki_page_id",$wiki_page_id);
        view()->share("unit_id",$unit_id);
        view()->share("slug",$slug);
        $unit_id_hash = $unit_id;
        $wiki_page_id_return = 0;
        $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id))
        {
            $unit_id = $unit_id[0];
            if($request->isMethod('post')){
                $inputData = $request->all();
                $validation = [];
                $validation['description'] = 'required';
                if($inputData['is_wikihome'] == 0){
                    $validation['title'] = 'required';
                }
                $validator = Validator::make($inputData, $validation);
                if ($validator->fails()){
                    return json_encode(array(
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 200);
                }
                if(/*(int)$inputData['id'] > 0 &&*/ (int)$inputData['wiki_page_rev_id'] == 0 && $inputData['is_wikihome'] = 0 ){

                    $titleAllow = Wiki::checkTitle($unit_id,$inputData);
                    if(!$titleAllow){
                        return json_encode(array(
                            'errors' => array("title" => array("A page with this title already exists in the database. Please try another page title"))
                        ), 200);
                    }
                }
                $inputData['unit_id'] = $unit_id;
                $inputData['slug'] = $slug;
                $wiki_page_id_return = Wiki::updateData($inputData);
                $json['success'] = "Save Successfully";

                if((int)$_POST['is_wikihome'] == 1){
                    $json['location'] = url("wiki/home")."/".$unit_id_hash."/".$slug;
                }
                else if((int)$_POST['is_wikihome'] == 3){
                    $json['location'] = url("wiki/menu")."/".$unit_id_hash."/".$slug;
                }
                else if((int)$_POST['wiki_page_rev_id'] == 0){
                    $json['location'] = url("wiki")."/". $unit_id_hash ."/".$wiki_page_id_return."/".$slug;
                }
                else
                {
                    $json['location'] = url("wiki")."/all_pages/".$unit_id_hash."/".$slug;
                }

                echo json_encode($json);die;
            }
            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                if($wiki_page_id > 0){
                    $filter = array(
                        'unit_id' => $unit_id,
                        'wiki_page_id' => $wiki_page_id,
                    );
                    $pages = Wiki::getPage($filter,false);

                    if(!empty($pages['pages'])){
                        view()->share("wiki_page",$pages['pages'][0]);
                    }
                }
                /*$wiki_id = Wiki::createNotExist($unit);
                $filter = array('wiki_id' => $wiki_id);
                $wiki = Wiki::detail($filter);
                view()->share("wiki",$wiki);*/
                return view("wiki.edit");
            }
        }
        return view("errors.404");
    }
    public function edit_revision(  Request $request ,$unit_id , $slug , $wiki_page_id = 0 )
    {

        view()->share("wiki_page_id",$wiki_page_id);
        view()->share("unit_id",$unit_id);
        view()->share("slug",$slug);
    	$unit_id_hash = $unit_id;
    	$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
        	$unit_id = $unit_id[0];

        	$unit = Unit::getUnitWithCategories($unit_id);
	        if(!empty($unit)){
	        	$site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                if($wiki_page_id > 0){

                    $filter = array(
                        'unit_id' => $unit_id,
                        'revision_id' => $wiki_page_id,
                    );
                    $pages = Wiki::getPageFromRevision($filter,false);
                    if(!empty($pages)){
                        $pages['is_wikihome'] = 0;
                        view()->share("wiki_page",$pages);
                        view()->share("wiki_page_rev_id", $wiki_page_id);
                    }
                }

	        	return view("wiki.edit");
	        }
        }
    	return view("errors.404");
    }
}
