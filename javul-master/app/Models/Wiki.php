<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ActivityPoint;
use Hashids\Hashids;

class Wiki extends Model
{

    public static function createNotExist($unitDetail,$filter = array()){

        $check =  DB::table("wiki_pages")
                    ->select("wiki_pages.wiki_page_id")
                    ->where("wiki_pages.unit_id","=",$unitDetail->id)
                    ->where("wiki_pages.is_wikihome","=",$filter['is_wikihome'])
                    ->get();
        if($check->count() == 0){

            if($filter['is_wikihome'] == 1){
                 $wiki_pages = array(
                    'unit_id'           => $unitDetail->id,
                    'wiki_page_title'   => $unitDetail->name,
                    'page_content'      => "Welcome to the Wiki Home page for unit ". $unitDetail->name,
                    'edit_comment'      => '',
                    'user_id'           => Auth::user()->id,
                    'is_wikihome'       => $filter['is_wikihome'],
                    'time_stamp'        => date("Y-m-d H:i:s"),
                );
            }
            else if($filter['is_wikihome'] == 3){
                 $wiki_pages = array(
                    'unit_id'           => $unitDetail->id,
                    'wiki_page_title'   => $unitDetail->name,
                    'page_content'      => "Edit these links ",
                    'edit_comment'      => '',
                    'user_id'           => Auth::user()->id,
                    'is_wikihome'       => $filter['is_wikihome'],
                    'time_stamp'        => date("Y-m-d H:i:s"),
                );
             }

            return $wikiId = DB::table('wiki_pages')->insertGetId($wiki_pages);
        }
        else
        {

            return $check[0]->wiki_page_id;

        }
    }

    public static function getChangesFor($filter){
        $extraWhere = array();
        if(isset($filter['unit_id'])){
            $extraWhere[] = array("wiki_arch_revisions.unit_id","=",$filter['unit_id']);
        }
        if(isset($filter['wiki_page_id'])){
            $extraWhere[] = array("wiki_arch_revisions.wiki_page_id","=",$filter['wiki_page_id']);
        }
        $wiki = DB::table("wiki_arch_revisions")
                    ->select(['wiki_arch_revisions.*','users.first_name','users.last_name','wiki_pages.wiki_page_title'])
                    ->join('users', 'users.id', '=', 'wiki_arch_revisions.user_id')
                    ->join('wiki_pages', 'wiki_pages.wiki_page_id', '=', 'wiki_arch_revisions.wiki_page_id')
                    ->where($extraWhere)
                    ->orderBy("wiki_arch_revisions.revision_id","DESC")
                    ->paginate(15);

        $changes = array('changes' => array(), 'links' =>  $wiki->links() );
        if(!empty($wiki->items())){
            if(isset($filter['userlink'])){
                $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
            }
            foreach ($wiki->items() as $key => $pageChanges) {
                $user_id = $userIDHashID->encode($pageChanges->user_id);
                $changes['changes'][] = array(
                    'revision_id'       => $pageChanges->revision_id,
                    'wiki_page_id'      => $pageChanges->wiki_page_id,
                    'wiki_page_title'      => $pageChanges->wiki_page_title,
                    'unit_id'           => $pageChanges->unit_id,
                    'change_byte'       => $pageChanges->change_byte >= 0 ? '+' . $pageChanges->change_byte : $pageChanges->change_byte,
                    'edit_comment'      => $pageChanges->edit_comment,
                    'user_id'           => $pageChanges->user_id,
                    'user_name'         => $pageChanges->first_name." ".$pageChanges->last_name,
                    'userlink'          => isset($filter['userlink']) ? url('userprofiles/'. $user_id .'/'.strtolower($pageChanges->first_name."_".$pageChanges->last_name)) : '',
                    'user_url'         => $pageChanges->first_name."_".$pageChanges->last_name,
                    'time_stamp'        =>  Carbon::createFromFormat('Y-m-d H:i:s', $pageChanges->time_stamp)->diffForHumans(),
                );
            }
        }
        return $changes;
    }
    public static function getPage($filter,$parse = true,$paginate = true){
        $extraWhere = array();
        if(isset($filter['unit_id'])){
            $extraWhere[] = array("wiki_pages.unit_id","=",$filter['unit_id']);
        }
        if(isset($filter['wiki_page_id'])){
            $extraWhere[] = array("wiki_pages.wiki_page_id","=",$filter['wiki_page_id']);
        }
        if(isset($filter['is_wikihome'])){
            $extraWhere[] = array("wiki_pages.is_wikihome","=",$filter['is_wikihome']);
        }
        if($paginate){
            $wiki = DB::table("wiki_pages")
                        ->where($extraWhere)
                        ->orderBy('wiki_page_id','DESC')
                        ->paginate(15);
            $pages = array('pages' => array(), 'links' =>  $wiki->links() );
            $wikiData = $wiki->items();
        }
        else
        {
            $wiki = DB::table("wiki_pages")
                        ->where($extraWhere)
                        ->orderBy('wiki_page_id','DESC')
                        ->get();
            $pages = array('pages' => array(), 'links' =>  array() );
            $wikiData = $wiki;
        }

        if(!empty($wikiData)){
            foreach ($wikiData as $key => $page) {
                $pages['pages'][] = array(
                    'wiki_page_id'      => $page->wiki_page_id,
                    'unit_id'           => $page->unit_id,
                    'wiki_page_title'   => $page->wiki_page_title,
                    'page_content'      => $parse ?  Wiki::parse($page->page_content) : $page->page_content,
                    'edit_comment'      => $page->edit_comment,
                    'user_id'           => $page->user_id,
                    'is_wikihome'           => $page->is_wikihome,
                    'time_stamp'        =>  Carbon::createFromFormat('Y-m-d H:i:s', $page->time_stamp)->diffForHumans(),
                );
            }
        }
        return $pages;
    }
    public static function parse($input,$analyze=false) {
        $patterns=array(
            "/\r\n/",

            // Headings
            "/^====== (.+?) ======$/m",                     // Subsubheading
            "/^===== (.+?) =====$/m",                     // Subsubheading
            "/^==== (.+?) ====$/m",                     // Subsubheading
            "/^=== (.+?) ===$/m",                       // Subheading
            "/^== (.+?) ==$/m",                     // Heading

            // Formatting
            "/\'\'\'\'\'(.+?)\'\'\'\'\'/s",                 // Bold-italic
            "/\'\'\'(.+?)\'\'\'/s",                     // Bold
            "/\'\'(.+?)\'\'/s",                     // Italic

            // Special
            "/^----+(\s*)$/m",                      // Horizontal line
            "/\[\[(file|img):((ht|f)tp(s?):\/\/(.+?))( (.+))*\]\]/i",   // (File|img):(http|https|ftp) aka image
            "/\[((news|(ht|f)tp(s?)|irc):\/\/(.+?))( (.+))\]/i",        // Other urls with text
            "/\[((news|(ht|f)tp(s?)|irc):\/\/(.+?))\]/i",           // Other urls without text

            // Indentations
            "/[\n\r]: *.+([\n\r]:+.+)*/",                   // Indentation first pass
            "/^:(?!:) *(.+)$/m",                        // Indentation second pass
            "/([\n\r]:: *.+)+/",                        // Subindentation first pass
            "/^:: *(.+)$/m",                        // Subindentation second pass

            // Ordered list
            "/[\n\r]?#.+([\n|\r]#.+)+/",                    // First pass, finding all blocks
            "/[\n\r]#(?!#) *(.+)(([\n\r]#{2,}.+)+)/",           // List item with sub items of 2 or more
            "/[\n\r]#{2}(?!#) *(.+)(([\n\r]#{3,}.+)+)/",            // List item with sub items of 3 or more
            "/[\n\r]#{3}(?!#) *(.+)(([\n\r]#{4,}.+)+)/",            // List item with sub items of 4 or more

            // Unordered list
            "/[\n\r]?\*.+([\n|\r]\*.+)+/",                  // First pass, finding all blocks
            "/[\n\r]\*(?!\*) *(.+)(([\n\r]\*{2,}.+)+)/",            // List item with sub items of 2 or more
            "/[\n\r]\*{2}(?!\*) *(.+)(([\n\r]\*{3,}.+)+)/",         // List item with sub items of 3 or more
            "/[\n\r]\*{3}(?!\*) *(.+)(([\n\r]\*{4,}.+)+)/",         // List item with sub items of 4 or more

            // List items
            "/^[#\*]+ *(.+)$/m",                        // Wraps all list items to <li/>

            // Newlines (TODO: make it smarter and so that it groupd paragraphs)
            "/^(?!<li|dd).+(?=(<a|strong|em|img)).+$/mi",           // Ones with breakable elements (TODO: Fix this crap, the li|dd comparison here is just stupid)
            "/^[^><\n\r]+$/m",                      // Ones with no elements
        );
        $replacements=array(
            "\n",

            // Headings
            "<h5>$1</h5>",
            "<h4>$1</h4>",
            "<h3>$1</h3>",
            "<h2>$1</h2>",
            "<h1>$1</h1>",

            //Formatting
            "<strong><em>$1</em></strong>",
            "<strong>$1</strong>",
            "<em>$1</em>",

            // Special
            "<hr/>",
            "<img src=\"$2\" alt=\"$6\"/>",
            "<a href=\"$1\">$7</a>",
            "<a href=\"$1\">$1</a>",

            // Indentations
            "\n<dl>$0\n</dl>", // Newline is here to make the second pass easier
            "<dd>$1</dd>",
            "\n<dd><dl>$0\n</dl></dd>",
            "<dd>$1</dd>",

            // Ordered list
            "\n<ol>\n$0\n</ol>",
            "\n<li>$1\n<ol>$2\n</ol>\n</li>",
            "\n<li>$1\n<ol>$2\n</ol>\n</li>",
            "\n<li>$1\n<ol>$2\n</ol>\n</li>",

            // Unordered list
            "\n<ul>\n$0\n</ul>",
            "\n<li>$1\n<ul>$2\n</ul>\n</li>",
            "\n<li>$1\n<ul>$2\n</ul>\n</li>",
            "\n<li>$1\n<ul>$2\n</ul>\n</li>",

            // List items
            "<li>$1</li>",

            // Newlines
            "$0<br/>",
            "$0<br/>",
        );
        if($analyze) {
            foreach($patterns as $k=>$v) {
                $this->patterns[$k].="S";
            }
        }
        return preg_replace($patterns,$replacements,$input);
    }
    public static function getDifference_idwise($filter){
        $extraWhere = array();

        if(isset($filter['unit_id'])){
            $extraWhere[] = array("wiki_arch_revisions.unit_id","=",$filter['unit_id']);
        }
        if(isset($filter['revision_id'])){
            $extraWhere[] = array("wiki_arch_revisions.revision_id","=",$filter['revision_id']);
        }
        $wikiMain = DB::table("wiki_arch_revisions")
                    ->where($extraWhere)
                    ->get();

        $pages = array();
        if(!empty($wikiMain)){
            $filter_page = array(
                'unit_id' => $wikiMain[0]->unit_id,
                'wiki_page_id' => $wikiMain[0]->wiki_page_id,
            );
            $pages_data = Wiki::getPage($filter_page);
            if(!empty($pages_data['pages'])){
                $pages['title'] = $pages_data['pages'][0]['wiki_page_title'];
            }
            else{
                $pages['title'] = "Untitle";
            }
            $pages['main'] = array(
                'revision_id'      => $wikiMain[0]->revision_id,
                'wiki_page_id'      => $wikiMain[0]->wiki_page_id,
                'unit_id'           => $wikiMain[0]->unit_id,
                'page_content'      => html_entity_decode( $wikiMain[0]->rev_page_content ),
                'edit_comment'      => $wikiMain[0]->edit_comment,
                'time_stamp'        => $wikiMain[0]->time_stamp,
            );
            $extraWhere = array();
            $extraWhere[] = array("wiki_arch_revisions.unit_id","=",$filter['unit_id']);
            $extraWhere[] = array("wiki_arch_revisions.revision_id","=",$filter['compare_id']);
            $extraWhere[] = array("wiki_arch_revisions.wiki_page_id","=",$wikiMain[0]->wiki_page_id);
            $wiki = DB::table("wiki_arch_revisions")
                    ->where($extraWhere)
                    ->limit(1)
                    ->get();

            $compareData = array();
            if(!empty($wiki)){
                $compareData = $wiki[0];
            }
            if(!empty($compareData)){
                $pages['compare'] = array(
                    'revision_id'      => $compareData->revision_id,
                    'wiki_page_id'      => $compareData->wiki_page_id,
                    'unit_id'           => $compareData->unit_id,
                    'page_content'      => isset($compareData->page_content) ? $compareData->page_content : $compareData->rev_page_content,
                    'edit_comment'      => $compareData->edit_comment,
                    'time_stamp'      => $compareData->time_stamp,
                );
            }
        }
        return $pages;
    }
    public static function getDifference($filter){
        $extraWhere = array();

        if(isset($filter['unit_id'])){
            $extraWhere[] = array("wiki_arch_revisions.unit_id","=",$filter['unit_id']);
        }
        if(isset($filter['revision_id'])){
            $extraWhere[] = array("wiki_arch_revisions.revision_id","=",$filter['revision_id']);
        }
        $wikiMain = DB::table("wiki_arch_revisions")
                    ->where($extraWhere)
                    ->get();

        $pages = array();
        if(!empty($wikiMain)){
            $filter_page = array(
                'unit_id' => $wikiMain[0]->unit_id,
                'wiki_page_id' => $wikiMain[0]->wiki_page_id,
            );
            $pages_data = Wiki::getPage($filter_page);
            if(!empty($pages_data['pages'])){
                $pages['title'] = $pages_data['pages'][0]['wiki_page_title'];
            }
            else{
                $pages['title'] = "Untitle";
            }
            $pages['main'] = array(
                'wiki_page_id'      => $wikiMain[0]->wiki_page_id,
                'unit_id'           => $wikiMain[0]->unit_id,
                'page_content'      => html_entity_decode( $wikiMain[0]->rev_page_content ),
                'edit_comment'      => $wikiMain[0]->edit_comment,
                'time_stamp'        => $wikiMain[0]->time_stamp,
            );
            $extraWhere = array();
            $extraWhere[] = array("wiki_arch_revisions.unit_id","=",$filter['unit_id']);
            $extraWhere[] = array("wiki_arch_revisions.revision_id","<",$filter['revision_id']);
            $extraWhere[] = array("wiki_arch_revisions.wiki_page_id","=",$wikiMain[0]->wiki_page_id);
            $wiki = DB::table("wiki_arch_revisions")
                    ->where($extraWhere)
                    ->limit(1)
                    ->get();

            $compareData = array();
            if(!empty($wiki)){
                $compareData = $wiki[0];
            }
            else
            {
                $extraWhere = array();
                $extraWhere[] = array("wiki_pages.unit_id","=",$filter['unit_id']);
                $extraWhere[] = array("wiki_pages.wiki_page_id","=",$wikiMain[0]->wiki_page_id);
                $wiki = DB::table("wiki_pages")
                        ->where($extraWhere)
                        ->limit(1)
                        ->get();
                $compareData = $wiki[0];
            }
            if(!empty($compareData)){
                $pages['compare'] = array(
                    'wiki_page_id'      => $compareData->wiki_page_id,
                    'unit_id'           => $compareData->unit_id,
                    'page_content'      => isset($compareData->page_content) ? $compareData->page_content : $compareData->rev_page_content,
                    'edit_comment'      => $compareData->edit_comment,
                    'time_stamp'      => $compareData->time_stamp,
                );
            }
        }
        return $pages;
    }
    public static function getPageFromRevision($filter,$parse = true){
        $extraWhere = array();

        if(isset($filter['unit_id'])){
            $extraWhere[] = array("wiki_arch_revisions.unit_id","=",$filter['unit_id']);
        }
        if(isset($filter['revision_id'])){
            $extraWhere[] = array("wiki_arch_revisions.revision_id","=",$filter['revision_id']);
        }
        $wikiMain = DB::table("wiki_arch_revisions")
                    ->select(['wiki_arch_revisions.*','users.first_name','users.last_name'])
                    ->where($extraWhere)
                    ->join('users', 'users.id', '=', 'wiki_arch_revisions.user_id')
                    ->get();

        $pages = array();
        if(!empty($wikiMain)){
            $filter = array(
                'unit_id' => $wikiMain[0]->unit_id,
                'wiki_page_id' => $wikiMain[0]->wiki_page_id,
            );
            $pages = Wiki::getPage($filter);
            if(!empty($pages['pages'])){
                $page_title = $pages['pages'][0]['wiki_page_title'];
            }
            else{
                $page_title = "Untitle";
            }

            $pages = array(
                'wiki_page_title'      => $page_title,
                'wiki_page_id'      => $wikiMain[0]->wiki_page_id,
                'revision_id'      => $wikiMain[0]->revision_id,
                'unit_id'           => $wikiMain[0]->unit_id,
                'username'           => $wikiMain[0]->first_name . ' ' .  $wikiMain[0]->last_name,
                'page_content'      => $parse ? Wiki::parse( $wikiMain[0]->rev_page_content ) : $wikiMain[0]->rev_page_content,
                'edit_comment'      => $wikiMain[0]->edit_comment,
                'time_stamp'        => $wikiMain[0]->time_stamp,
            );
        }
        return $pages;
    }
    public static function checkTitle($unit_id,$data){

        $title = strtolower( trim($data['title']) );
        $extraWhere = array();
        $extraWhere[] = array("wiki_pages.unit_id","=", $unit_id);
        if((int)$data['id'] > 0){
            $extraWhere[] = array("wiki_pages.wiki_page_id","!=", (int)$data['id']);
        }
        $extraWhere[] = array("wiki_pages.wiki_page_title","=",$title);

        $wiki = DB::table("wiki_pages")
                    ->where($extraWhere)
                    ->get();
        return empty($wiki);

    }
    public static function updateData($data){

        $wiki_pages = array(
            'unit_id'           => $data['unit_id'],
            'wiki_page_title'   => $data['title'],
            'page_content'      => $data['description'],
            'edit_comment'      => isset($data['edit_comment']) ? $data['edit_comment'] : '',
            'user_id'           => Auth::user()->id,
        );

        $wiki_page_id = 0;
        if((int)$data['wiki_page_rev_id'] > 0){
            $wiki_arch_revisions = array(
                'rev_page_content'      => $data['description'],
                'user_id'               => Auth::user()->id,
                'time_stamp'            => date("Y-m-d H:i:s"),
            );

            $oldWikiRev = DB::table("wiki_arch_revisions")
                    ->where("wiki_arch_revisions.revision_id","=",$data['wiki_page_rev_id'])
                    ->get();
            $oldWikiRev = (array)$oldWikiRev[0];
            DB::table('wiki_arch_revisions')
            ->where("wiki_arch_revisions.revision_id","=",(int)$data['wiki_page_rev_id'])
            ->update($wiki_arch_revisions);
            $wiki_page_id = $data['id'];
            $bytes = Wiki::strBytes( str_replace(' ', '', strip_tags($data['description'])) );
            $oldBytes = Wiki::strBytes( str_replace(' ', '', strip_tags($oldWikiRev['rev_page_content'])) );
            $wiki_arch_revisions = array(
                'unit_id'               => $data['unit_id'],
                'wiki_page_id'          => $wiki_page_id,
                'rev_page_content'      => $oldWikiRev['rev_page_content'],
                'edit_comment'          => isset($oldWikiRev['edit_comment']) ? $oldWikiRev['edit_comment'] : '',
                'change_byte'           => (  $bytes - $oldBytes ),
                'user_id'               => Auth::user()->id,
                'time_stamp'            => date("Y-m-d H:i:s"),
            );
            DB::table('wiki_arch_revisions')->insertGetId($wiki_arch_revisions);

        }
        else if((int)$data['id'] > 0){
            $filter = array(
                'unit_id' => $data['unit_id'],
                'wiki_page_id' => $data['id'],
            );
            $oldWiki = Wiki::getPage($filter,false);
            $oldWiki = $oldWiki['pages'][0];
            DB::table('wiki_pages')
            ->where("wiki_pages.wiki_page_id","=",$data['id'])
            ->update($wiki_pages);
            $wiki_page_id = $data['id'];
            $bytes = Wiki::strBytes( str_replace(' ', '', strip_tags($data['description'])) );
            $oldBytes = Wiki::strBytes( str_replace(' ', '', strip_tags($oldWiki['page_content'])) );
            //if($data['is_wikihome'] != 3){}
            $wiki_arch_revisions = array(
                'unit_id'               => $data['unit_id'],
                'wiki_page_id'          => $wiki_page_id,
                'rev_page_content'      => $oldWiki['page_content'],
                'edit_comment'          => isset($oldWiki['edit_comment']) ? $oldWiki['edit_comment'] : '',
                'change_byte'           => (  $bytes - $oldBytes ),
                'user_id'               => Auth::user()->id,
                'time_stamp'            => date("Y-m-d H:i:s"),
            );
            DB::table('wiki_arch_revisions')->insertGetId($wiki_arch_revisions);
            ActivityPoint::create([
                'user_id' => Auth::user()->id,
                'unit_id' => $data['unit_id'],
                'points'  => 1,
                'comments'=>'Wiki Edit',
                'type'=>'wiki'
            ]);
            $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);
            $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->encode($data['unit_id']);

            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
            if(!empty(Auth::user()->username))
                $loggedinUsername = Auth::user()->username;

            SiteActivity::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=> $data['unit_id'],
                'objective_id'=>0,
                'task_id'=>0,
                'issue_id'=>0,
                'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                    .$loggedinUsername.'</a>
            edited wiki page <a href="'.url('wiki/'.$unit_id.'/'.(int)$data['id']).'/'. $data['slug'] .'">'. $data['title'] .'</a>'
            ]);
        }
        else
        {
            $wiki_pages['time_stamp'] = date("Y-m-d H:i:s");
            $wiki_page_id =  DB::table('wiki_pages')->insertGetId($wiki_pages);
            $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);
            $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->encode($data['unit_id']);
            ActivityPoint::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=> $data['unit_id'],
                'points'=> 1,
                'comments'=>'Wiki Create',
                'type'=>'wiki'
            ]);
            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
            if(!empty(Auth::user()->username))
                $loggedinUsername = Auth::user()->username;
            SiteActivity::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=> $data['unit_id'],
                'objective_id'=>0,
                'task_id'=>0,
                'issue_id'=>0,
                'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                    .$loggedinUsername.'</a>
                    created wiki page <a href="'.url('wiki/'.$unit_id.'/'.$wiki_page_id.'/'. $data['slug']) .'">'. $data['title'] .'</a>'
            ]);
        }

        return $wiki_page_id;
    }
    public static function strBytes($str){
        $strlen_var = strlen($str);
        $d = 0;
        for($c = 0; $c < $strlen_var; ++$c){
            $ord_var_c = ord($str[$c]);
            switch(true){
                case(($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                $d++;
                break;
                case(($ord_var_c & 0xE0) == 0xC0):
                    $d+=2;
                break;
                case(($ord_var_c & 0xF0) == 0xE0):
                    $d+=3;
                break;
                case(($ord_var_c & 0xF8) == 0xF0):
                    $d+=4;
                break;
                case(($ord_var_c & 0xFC) == 0xF8):
                    $d+=5;
                break;
                case(($ord_var_c & 0xFE) == 0xFC):
                    $d+=6;
                break;
                default:
                    $d++;
            };
        };
        return $d;
    }
    /*public static function detail($filter = array()){

        $extraWhere = array();
        if(isset($filter['wiki_id'])){
            $extraWhere[] = array("wiki_detail.wiki_id","=",$filter['wiki_id']);
        }
        $wiki = DB::table("wiki_detail")
                    ->where($extraWhere)
                    ->get();
        if(!empty($wiki)){
            return array(
                'wiki_id'           => $wiki[0]->wiki_id,
                'unit_id'           => $wiki[0]->unit_id,
                'name'              => $wiki[0]->name,
                'description'       => $wiki[0]->description,
                'slug'              => $wiki[0]->slug,
                'user_id'           => $wiki[0]->user_id,
                'created_datetime'  => $wiki[0]->created_datetime,
                'modify_datetime'   => $wiki[0]->modify_datetime,
            );
        }
        return array();
    }*/
    /*public static function detailHistory($filter = array()){

    	$extraWhere = array();
        if(isset($filter['wiki_id'])){
            $extraWhere[] = array("wiki_history.wiki_id","=",$filter['wiki_id']);
        }
        $wiki = DB::table("wiki_history")
                    ->select(['wiki_history.*','users.first_name','users.last_name'])
                    ->join('users', 'users.id', '=', 'wiki_history.user_id')
                    ->where($extraWhere)
                    ->orderBy("wiki_history.datetime",'desc')
                    ->paginate(10);

        $history = array();
        if(!empty($wiki)){
            foreach ($wiki->items() as $key => $value) {
                $history['item'][] = array(
                    'wiki_id' => $value->wiki_id,
                    'change_byte' => $value->change_byte,
                    'user_id' => $value->user_id,
                    'datetime' => Carbon::createFromFormat('Y-m-d H:i:s', $value->datetime)->diffForHumans(),
                    'user_name' => $value->first_name ." ". $value->last_name,
                );
            }
        }
        $history['links'] = $wiki->links();
        return $history;
    }*/
}
