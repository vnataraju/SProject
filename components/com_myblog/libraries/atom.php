<?php
defined('_JEXEC') or die('Restricted access');

/**
* MyBlog Atom API 
*
* @author Johny Susanto
*/

//Error types for MyBlog Atom
define('MYATOM_OK', 0);
define('MYATOM_ERR_BAD_AUTH', 1);
define('MYATOM_ERR_INVALID_PARAM', 2);
define('MYATOM_ERR_NOT_FOUND', 3);
define('MYATOM_ERR_COMMENT_DISALLOW', 4);

class MYAtom{

        var $allowedmethod = array('GET','POST','PUT','DELETE');
        var $result;
        var $errmsg;

        var $method;
        var $data;
        var $accepttype;
        var $db;
        var $itemid;
        var $allowcomment;
        var $nonce = '43nife93430jfmem';
        var $default_catid;
        var $baseurl;
        var $authorname = NULL;
        var $authorid;
        
        /**
        * Constructor
        */
        function MYAtom($itemid = ''){
                
                jimport( 'joomla.environment.uri' );
                
                global $_MY_CONFIG;
				
				if(!$_MY_CONFIG->get('allowAtom')) {
                        header('HTTP/1.1 405 Method Not Allowed')  ;
                        echo "MyBlog Atom Publishing is currently disabled. This feature can be enabled from the backend under the 'General Settings' tab";
						exit;
                }
                                
                $x =& JURI::getInstance( JURI::root() );
                //$x->setPort(8888);
                                
                $this->baseurl = $x->toString();
                
                                
                //check if jomcomment exist and allow blog comments
                jimport('joomla.application.component.helper');
                $this->allowcomment = JComponentHelper::isEnabled('com_jomcomment', true);
                
                //init DB
                $this->db =& JFactory::getDBO();
                
                $this->itemid = $itemid;
                
                //determine the request method + validate
                $this->method = (!empty($method)) ? $method : $_SERVER['REQUEST_METHOD'] ;

                if(!$this->checkMethod($this->method)) return false;

                /* JOHNY if(!$this->checkMethod($this->method)) {
                        header('HTTP/1.1 405 Method Not Allowed')  ;
                        exit;
                }*/

                //$this->accepttype = $_SERVER['HTTP_ACCEPT']; //probably need a function to validate accept types
                
                $this->default_catid = $_MY_CONFIG->get('catid');
		if(empty($this->default_catid) && JVERSION >= 1.6 ) $this->default_catid = $_MY_CONFIG->get('postSection');	
        }
                
        /**
		* List of categories 
		*/
		function categories(){
			$sql = "SELECT c.slug, c.name, count(c.name) frequency 
					FROM #__myblog_categories c,#__myblog_content_categories c2 
					WHERE c.id=c2.category GROUP BY c.name ORDER BY frequency ASC";
			$catlist = myGetTagClouds($sql);
			
			$catxml = '';
			foreach($catlist as $cat){
				$catxml .= "<category term='".$cat['name']."' />\n";
			}
						
			$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n".
					"<app:categories xmlns:app=\"http://www.w3.org/2007/app\" xmlns=\"http://www.w3.org/2005/Atom\" fixed=\"no\" scheme=\"".$this->baseurl."\">\n".
					$catxml.
					'</app:categories>';
			
			header('Connection: close');
            header('Content-Length: '. strlen($xml));
            header('Content-Type: application/atomcat+xml');
            header('Content-Disposition: attachment; filename=atomcat.xml');
			echo $xml;
			exit;
		}
		
		
        /**
        * Generate Service/Introspection document for ATOM autodiscovery
        */
        function service(){

                jimport( 'joomla.environment.uri' );
				
				if(JVERSION >= 1.6){
					$app = JFactory::getApplication();
					$sitename = $app->getCfg('sitename');
				}else{
					global $mainframe;		
					$sitename = $mainframe->getCfg('sitename');
				}
								
				$sql = "SELECT c.slug, c.name, count(c.name) frequency 
					FROM #__myblog_categories c,#__myblog_content_categories c2 
					WHERE c.id=c2.category GROUP BY c.name ORDER BY frequency ASC";
				$catlist = myGetTagClouds($sql);
			
				$catxml = '';
				foreach($catlist as $cat){
					$catxml .= "<atom:category term='".$cat['name']."' />\n";
				}
				
				$catxml = (empty($catxml)) ? '' : '<categories fixed="no">'.$catxml.'</categories>';
				
				//<categories href="'.$this->baseurl.'index.php?option=com_myblog&amp;task=atomcat" />
						
				$xml = '<?xml version="1.0" encoding="utf-8"?>
						<service xmlns="http://www.w3.org/2007/app" xmlns:atom="http://www.w3.org/2005/Atom">
						  <workspace>
							<atom:title>'.$sitename.'</atom:title>
							<collection href="'.$this->baseurl.'index.php?option=com_myblog&amp;task=atom">
							  <atom:title>MyBlog Entries</atom:title>'.
							  $catxml.
							'</collection>
							<collection href="'.$this->baseurl.'index.php?option=com_myblog&amp;task=atommedia">
							  <atom:title>MyBlog Pictures</atom:title>
							  <accept>image/*</accept>
							</collection>
						  </workspace>
						</service>';

                header('Connection: close');
                header('Content-Length: '. strlen($xml));
                header('Content-Type: application/atomsvc+xml');
                header('Content-Disposition: attachment; filename=atom.xml');
                echo $xml;
                exit;
        }
        
        
 		/**
        * Upload media into MyBlog
        */
        function upload(){

				global $_MY_CONFIG;
				$dir = $_MY_CONFIG->get('imgFolderRoot');
				
				if($dir[(strlen($dir) -1)] != DS) $dir = $dir.DS; //check if format of directory given is missing a separator in the end
				
                $media = JRequest::getVar( 'media' , '' , 'GET' );
                $targetdir = JPATH_SITE . DS . $dir . DS;

                //if its a get method, redirect to image location
                if(strtolower($this->method) == 'get' || strtolower($this->method) == 'head'){
                        $files = scandir($targetdir);
                        foreach($files as $f){
                                if($f == $media){
                                   header("Location: ".$this->baseurl.$dir.$f);
                                   exit;
                                }
                        }
                        header('HTTP/1.1 404 Not Found');
                        exit('Media cannot be found');
                }

                $headers = getallheaders();

                $slug = isset($headers['Slug']) ? $headers['Slug'] : (empty($media) ? md5(time()) : $media ) ;
                $filename = JFile::makeSafe($slug);

                //try to find the file extension
                $ext = strrchr($filename, '.');

                $origname = $filename;

                //if theres no extension found in filename, lets add it according to the MIME Type
                if(empty($ext)){
                        $translate = array(
                                        'image/gif' => '.gif',
                                        'image/jpeg' => '.jpeg',
                                        'image/png' => '.png',
                                        'image/bmp' => '.bmp',
                                        'image/tiff' => '.tiff'
                                );

                        $ext = (isset($translate[strtolower($headers['Content-Type'])]))
                                ? $translate[strtolower($headers['Content-Type'])]
                                : '' ;

                        //unallowed extension.
                        if(empty($ext)){
                                header('HTTP/1.1 415 Unsupported Media Type');
                                exit;
                        }
                        $filename .= $translate[strtolower($headers['Content-Type'])];
                }else{
                        $origname = substr($filename, 0, strrpos($filename, '.'));
                }

                $fullpath = $targetdir.$filename;

                //check of file already exist to avoid file overwriting on create entry
                if(file_exists($fullpath)){
                        if(empty($media)){ //CREATE MODE

                        //try to create a new filename by adding underscore at the beginning of filename
                        while(file_exists( $targetdir.$filename)){
                                $filename = '_'.$filename;
                        }
                        $fullpath = $targetdir.$filename;

                        }else{  //UPDATE MODE
                                @unlink($fullpath);
                        }
                }

                //lets create a file
                $putdata = fopen("php://input", "r");

                /* Open a file for writing */
                $fp = fopen($fullpath, "w");

                /* Read the data 1 KB at a time and write to the file */
                while ($data = fread($putdata, 1024))
                        fwrite($fp, $data);

                /* Close the streams */
                fclose($fp);
                fclose($putdata);

                //=====================================

                //let's compose the returned XML
                $xml = '<?xml version="1.0"?>
                <entry xmlns="http://www.w3.org/2005/Atom">
                        <id>'.$this->baseurl.$dir.$filename.'</id>
                        <title>MyBlog Media - '.$filename.'</title>
                        <link rel="edit-media" type="'.str_replace('/','.', $headers['Content-Type']).'" href="'.$this->baseurl.'index.php?option=com_myblog&amp;task=atommedia&amp;media='.$filename.'" />
                        <updated>'.gmdate("Y-m-d\TH:i:s\Z", time()).'</updated>
                        <author><name>'.$this->authorname.'</name></author>
                        <content type="'.$headers['Content-Type'].'" src="'.$this->baseurl.$dir.$filename.'" />
                </entry>';
				

                if(empty($media)){
                        header('HTTP/1.1 201 Created');
                        header('Content-Location: '.$this->baseurl.$dir.$filename);
                        header('Location: '.$this->baseurl.$dir.$filename);
                }else{
                        header('HTTP/1.1 200 OK');
                }

                echo $xml;
                exit();
        }

        
        /**
        * Authenticate user doing the request
        *
        * @param string username
        * @param string password
        * @return booloean (?)
        */
        function auth(){ 
                
                if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                                explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
                } else if (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
                        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                                explode(':', base64_decode(substr($_SERVER['REDIRECT_REMOTE_USER'], 6)));
                }

                // If Basic Auth is working...
                if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {                
                        jimport('joomla.user.authentication');
                    $auth = &JAuthentication::getInstance();
                        $res = $auth->authenticate(array('username' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']), array());
                        if($res->status !== JAUTHENTICATE_STATUS_SUCCESS) {
                                return false;
                        } 
                        
                        $this->authorname = $res->fullname;
                        jimport( 'joomla.user.helper' );
			if(JVERSION >= 1.6){
				$this->authorid = JUserHelper::getUserId($res->username);			
			}else{
                        	$userhelper = new JUserHelper();
                        	$this->authorid = $userhelper->getUserId($res->username);
                        }

                        //set the current user
                        $user        =& JFactory::getUser($this->authorid);                
                        $session =& JFactory::getSession();
                        $session->set('user', $user);
                        
                        return myGetUserCanPost($this->authorid);
                }
                return false;
        }
                
        /**
        * Respond to Atom request
        *
        */
        function respond(){

                //collect the sent data
                $this->getData();

                $headers = array();
                $result = NULL;
                                
                switch(strtoupper($this->method)){
                        case "GET"        : 
                                $result = $this->retrieve();
                                if($result == MYATOM_OK){
                                        header('HTTP/1.1 200 OK');
                                        //header("Content-Type: application/atom+xml;type=feed");
                                        echo $this->composeRespond($this->result);
                                }else{
                                        header('HTTP/1.1 404 Not Found');
                                        echo 'No such result found';
                                        // $this->composeRespond($this->result);
                                }
                                exit;
                                break;
                        case "POST"        : 
                        case "PUT"  : 
                                //authenticate user first
                                $authres = $this->auth();
                                if(!$authres){
                                        header('WWW-Authenticate: Basic realm="MyBlog Atom Protocol"');
                                        header("HTTP/1.1 401 Unauthorized");
                                        header('Status: 401 Unauthorized');
                                        exit;
                                }
                                
                                $result = $this->update($this->data);
                                if($result == MYATOM_OK){
                                        if($this->data->id == 0 || $this->data['id'] == 0) {
                                                $id = $this->result[0]->id;
                                                $redirecturl = $this->baseurl."index.php?option=com_myblog&amp;task=atom&amp;id=".$id;
                                                header('HTTP/1.1 201 Created');        
                                                header("Content-Location: ".$redirecturl);                                        
                                                header("Location: ".$redirecturl); //need to redirect ?
                                        }else{
                                                header('HTTP/1.1 200 OK');
                                        }
                                        echo $this->composeRespond($this->result);
                                        exit;
                                }
                                break;
                        case  "DELETE":
                                //authenticate user first
                                $authres = $this->auth();
                                if(!$authres){
                                        header('WWW-Authenticate: Basic realm="MyBlog Atom Protocol"');
                                        header("HTTP/1.1 401 Unauthorized");
                                        header('Status: 401 Unauthorized');
                                        exit;
                                }        
                                $result = $this->delete();
                                header('HTTP/1.1 200 OK');
                                exit;
                                break;
                        default:
                                exit;
                }
                exit;        
                //return $result;
        }
        
        

        /**
        * Retrieve blog entry
        *
        * @ param string entry ID
        */
        function retrieve(){
        
                global $_MY_CONFIG;
        
                //filters
                $date           =& JFactory::getDate();
                $blogger        = JRequest::getVar('blogger','','POST');
                $keyword        = JRequest::getVar('keyword','','POST');
                $tags           = JRequest::getVar('tags','','POST');
        
                $show           = JRequest::getVar( 'show' , '' , 'GET' );
                $id             = JRequest::getVar( 'id' , '' , 'GET' );
                $limit          = JRequest::getVar( 'limit' , $_MY_CONFIG->get('numEntry') , 'GET');
                $limitstart     = JRequest::getVar( 'limitstart' , 0 , 'GET' );
                
                $uid        = (!empty( $show ) ) ? $show : $id;

                $selectMore     = "";
                $searchWhere    = "";
                $primaryOrder   = "";
                $use_tables     = "";
                
                $entries = NULL;

                // Get blog entry(s)
                if(empty($uid)){ //no specific entry is specified, grab all
                
                        $sections       = $_MY_CONFIG->get('managedSections');
                        $query = " SELECT a.*, round(r.rating_sum/r.rating_count) as rating, r.rating_count $selectMore
                                FROM (#__content as a $use_tables ) 
                                        left outer join #__content_rating as r 
                                                on (r.content_id=a.id) 
                                WHERE a.state=1 AND a.publish_up < '" . $date->toMySQL() . "' 
                                        and a.sectionid in ($sections) 
                                        $searchWhere ORDER BY $primaryOrder a.created DESC,a.id DESC LIMIT $limitstart,$limit";

                        $query = " SELECT a.*
                                   FROM (#__content as a)
                                   WHERE a.state=1
                                   AND a.publish_up < '".$date->toMySQL()."'
                                   AND a.sectionid IN ($sections)
                                   $searchWhere
                                   ORDER BY $primaryOrder a.created DESC,a.id DESC
                                   LIMIT $limitstart,$limit";

                        $this->db->setQuery($query);
                
                        $entries = $this->db->loadObjectList();

                        $tmp = array();
                        foreach($entries as $e){
                                $row = JTable::getInstance( 'BlogContent' , 'Myblog' );
                                $row->load( $e->id );
                                $tmp[] = $row;
                        }
                        $entries = $tmp;
                        unset($tmp);
                        //error_log(print_r($entries,true));
                }
                
                //grab a single entry
                elseif (is_numeric($uid))
                {                        
                        $query = "SELECT
                                c.*,p.permalink, '" . $date->toMySQL() ."' as curr_time,
                                r.rating_sum/r.rating_count as rating, r.rating_count
                                FROM
                                (#__content as c,#__myblog_permalinks as p)
                                LEFT OUTER JOIN
                                #__content_rating as r on (r.content_id=c.id)
                                WHERE
                                c.id=p.contentid AND
                                c.id='$uid'";
                                                        
                        $this->db->setQuery( $query );
                        $row = $this->db->loadObject();

                        $row =& JTable::getInstance( 'BlogContent' , 'Myblog' );
                        $row->load( $uid );

                        
                        $entries = array($row);
                        header('HTTP/1.1 200 OK');
                        header("Content-Type: application/atom+xml");
                        echo $this->composeRespond($entries, TRUE);
                        exit;
                }
                else
                {
                        $uid = stripslashes($uid);
                        $uid = $this->db->getEscaped($uid);
                        $row =& JTable::getInstance( 'BlogContent' , 'Myblog' );
                        $row->load($uid);
                        $entries = array($row);
                        header('HTTP/1.1 200 OK');
                        header("Content-Type: application/atom+xml");
                        echo $this->composeRespond($entries, TRUE);
                        exit;
                }
                
                $this->result = $entries;
                
                if(empty($entries[0]->title) && empty($entries[0]->created_by)){
                        return MYATOM_ERR_NOT_FOUND;
                }
                
                return MYATOM_OK ;        
        }
        
        
        /**
        * Insert a new comment on an entry. Refer to com_jomcomment/ajax.jomcomment.php 
        *
        * @param array entry
        * @return boolean ?
        */
        function comment($entry){                
                /*
                JCTableComment Object
                (
                        [id] => 0
                        [parentid] => 0
                        [status] => 
                        [contentid] => 89
                        [ip] => ::1
                        [name] => JOHNY
                        [title] => LALLA
                        [comment] => lalala
                        [preview] => 
                        [date] => 2010-10-12 03:57:59
                        [published] => 1
                        [ordering] => 
                        [email] => your email
                        [website] => your website
                        [updateme] => 
                        [custom1] => 
                        [custom2] => 
                        [custom3] => 
                        [custom4] => 
                        [custom5] => 
                        [star] => 
                        [user_id] => 0
                        [option] => com_myblog
                        [voted] => 0
                        [referer] => 
                        [type] =>
                */
                
                $path = JC_LIB_PATH . DS . 'comments.php'; //JCCommentsHelper path
                require_once( $path );
                
                // lets use jomcomment's table to process the comment                
                JTable::addIncludePath(dirname( __FILE__ ).DS.'../com_jomcomment'.DS.'tables');
                $comm =& JTable::getInstance( 'Comment' , 'JCTable' );
                
                //JCTableComment looks for specific array element names for its bind(), so lets translate the information from the XML input to suit this
                if(isset($entry['email']))         $entry['jc_email'] = $entry['email'];
                if(isset($entry['uri']))         $entry['jc_website'] = $entry['uri'];
                if(isset($entry['title']))        $entry['jc_title'] = $entry['title'];
                if(isset($entry['name']))        $entry['jc_name'] = (!empty($entry['name'])) ? $entry['name'] : 'ANONYMOUS'; //this should be changed to the logged in user
                
                $entry['jc_comment']        = $entry['fulltext'];
                $entry['jc_contentid']        = $entry['id'];
                $entry['jc_parentid']        = 0;
                $entry['jc_sid'] = '';
                
                $comm->bind( $entry );
                
                //validation
                /*if(jcContentPublished($comm->contentid) != 1)
                {
                        $resultMsg = JText::_('JC CANNOT ADD COMMENT TO UNPUBLISH CONTENT');
                }*/
                
                $comm->store();
                return MYATOM_OK;
        }
                
        
        /**
        * Add new blog entry
        *
        * @param array entry
        * @return object MyBlogBlogContent
        */
        function update($entry){
                                                
                //exit;
                // ID=0 is adding a new record, otherwise its going to update the entry with such ID
                //$id = empty($entry->id) ? 0 : $entry['id'];
                
                $id          = JRequest::getVar( 'id' , 0 , 'GET' );
                $iscomment = JRequest::getVar( 'comment' , '' , 'GET' );
                
                $row =& JTable::getInstance( 'BlogContent' , 'Myblog' );
                $date =& JFactory::getDate();
                
                //validate params
                $errmsg = array();
                if(empty($entry['title'])) $errmsg[] = "Blog entry Title is required";
                if(empty($entry['fulltext'])) $errmsg[] = "Blog entry Content is required";
                
                if(!empty($errmsg)){
                        $this->errmsg = $errmsg;
                        return MYATOM_ERR_INVALID_PARAM;
                }
                
                //check if it's a comment entry instead of a blog entry
                if($iscomment){
                        return (!$this->allowcomment) ? MYATOM_ERR_COMMENT_DISALLOW : $this->comment($entry);
                }
                
                //lets load the current blog detail first if its an already existing one.
                if($id != 0) $row->load($id);
                
                //if its a new entry, lets pick a default category (the first on the section)
                if($id == 0) {
                        $row->catid = $this->default_catid;
                        $row->created = $date->toMySQL();
                }
                
                //all ok, lets try saving/creating the entry
                $row->id= $id;
                $row->title = $entry['title'];                
                $row->introtext = '';
                $row->fulltext = $entry['fulltext'];                
                $row->state = $entry['publish'];
                $row->metakey = '';
                $row->metadesc = '';
                $row->publish_up = $date->toMySQL();
                $row->store();

                $row->load($row->id);
                //add tags if there's any
                if(!empty($entry['tags'])){
                        myAddTags($row->id, $entry['tags']);
                        $row->tags = implode(',', $entry['tags']);
                }
                
                mySortOrder($row);
                
                $this->result = array($row);
                
                return MYATOM_OK;        
        }
        
        
        /**
        * Remove an entry record
        */
        function delete(){
                $blog =& JTable::getInstance( 'BlogContent' , 'Myblog' );
                $show = JRequest::getVar( 'show' , '' , 'GET' );
                $id          = JRequest::getVar( 'id' , '' , 'GET' );
                $uid  = (!empty( $show ) ) ? $show : $id;
                $blog->load($uid);
                
                $res = $blog->delete(); //NOTE: Delete() always returns TRUE, need to know if there's no such record to delete
                $this->result = '';
                return MYATOM_OK;
        }
        
        
        
        /**
        * Compose the respond text - taken from task/browse.base.php
        *
        * @param array data
        * @return string XML for feed
        */
        function composeRespond($data = NULL,  $entryonly = FALSE){
                
                jimport( 'joomla.environment.uri' );
                
                $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
                
                if(!$entryonly){
                        $xml .= "<feed xmlns=\"http://www.w3.org/2005/Atom\" xml:base=\"".$this->baseurl."\">\n";
                        $xml .= "<id>".$this->baseurl."</id>\n".
                                        "<title>MyBlog entries</title>\n".
                                        "<link rel=\"service.post\" href=\"index.php?option=com_myblog&amp;task=atom\" title=\"MyBlog entries\" />";
                                        "<updated>".gmdate("Y-m-d\TH:i:s\Z")."</updated>\n";
                }
                
                while(!empty($data)){
                        $ent = array_shift($data);
                        $url = $this->baseurl."index.php?option=com_myblog&amp;task=show&amp;id=".$ent->id;
                        
                        $xml .= "\n\n<entry xmlns=\"http://www.w3.org/2005/Atom\">\n";
                        $xml .= "<link rel=\"alternate\" href=\"$url\" type=\"application/xhtml+xml\" hreflang=\"en\" />\n";
                        $xml .=        "<link rel=\"service.edit\" href=\"index.php?option=com_myblog&amp;task=atom&amp;id=".$ent->id."\" title=\"".$ent->title."\" />\n";
                        $xml .=        "<link rel=\"service.post\" href=\"index.php?option=com_myblog&amp;task=atom&amp;id=".$ent->id."\" title=\"".$ent->title."\" />\n";
                        $xml .=        "<link rel=\"edit\" href=\"index.php?option=com_myblog&amp;task=atom&amp;id=".$ent->id."\" title=\"".$ent->title."\" />\n";
                        $xml .=        "<link rel=\"post\" href=\"index.php?option=com_myblog&amp;task=atom&amp;id=".$ent->id."\" title=\"".$ent->title."\" />\n";
                        if($this->allowcomment) 
                                $xml .=        "<link rel=\"comment\" href=\"index.php?option=com_myblog&amp;task=atom&amp;id=".$ent->id."&amp;comment=1\" title=\"".$ent->title."\" />\n";
                        
                        $xml .= "<id>".$ent->id."</id>\n";
                        $xml .= "<title>".$ent->title."</title>\n";
                        $xml .= "<issued>".gmdate("Y-m-d\TH:i:s\Z", strtotime($ent->created))."</issued>\n";
                        $xml .= "<updated>".gmdate("Y-m-d\TH:i:s\Z", strtotime($ent->modified))."</updated>\n";
                        $xml .= "<content type=\"html\">".(!empty($ent->introtext) ? htmlspecialchars($ent->introtext) : htmlspecialchars($ent->fulltext))."</content>\n";
                        
                        //draft extension
                        $isdraft = ($ent->state == 1) ? 'no' : 'yes';
                        $xml .= "<app:control xmlns:app='http://www.w3.org/2007/app'>\n";
                        $xml .= "<app:draft>".$isdraft."</app:draft>\n";
                        $xml .= "</app:control>\n";
                        
                        //check if author information is included
                        if($ent->created_by != 0){
                                $user =& JFactory::getUser($ent->created_by);

                                $xml .= "<author>\n".
                                        "<name>".$user->name."</name>\n".
                                        "</author>\n";
                                unset($user);
                        }
                        //tags
                        if(!empty($ent->tags)){
                                //<category scheme="http://localhost:8888/wordpress" term="Uncategorized" />
								$tags = explode(',', $ent->tags);
                                foreach($tags as $t){
                                        $xml .= "<category term='".$t."' />\n";
                                }
                        }
                        
                        $xml .= "</entry>\n";
                        unset($ent);
                }
                
                
                if(!$entryonly){
                        $xml .= "</feed>\n";
                }
                return $xml;
        }
        
        
        
        /**
        * Get the sent data / request variables
        * 
        * @return array data
        */
        function getData(){
                switch(strtoupper($this->method)){
                        case "GET"        : 
                                $this->data = $_GET;
                                break;
                        case "POST"        : 
                                $this->data = (isset($_POST['id'])) ? $_POST : $this->parseXmlInput(file_get_contents('php://input'));
                                break;
                        case "PUT"  :
                                $put_vars = file_get_contents('php://input'); 
                     $this->data = $this->parseXmlInput($put_vars);  
                                break;
                        case  "DELETE": 
                                $this->data = NULL;
                                break;
                }
                return $this->data;
        }
        
        
        /**
        * Read an XML string and translate it into an associative array
        *
        * @param string XML string
        * @return assoc array 
        */
        function parseXmlInput($xmlstr){
                                
                //error_log($xmlstr);
                
                $doc = new DOMDocument();
                
                //freaking ampersand problem
                $xmlstr = str_replace("&","&amp;",$xmlstr);
                
                $ok = $doc->loadXML($xmlstr);
                $entries = $doc->getElementsByTagName('entry');
                
                foreach($entries as $entry){
                        $data['title'] = $entry->getElementsByTagName('title')->item(0)->nodeValue;
                        $data['id'] = $entry->getElementsByTagName('id')->item(0)->nodeValue;
                        $data['fulltext'] = html_entity_decode($entry->getElementsByTagName('content')->item(0)->nodeValue);

                        //$data['created'] = $entry->getElementsByTagName('issued')->item(0)->nodeValue;
                        //$data['modified'] = $entry->getElementsByTagName('modified')->item(0)->nodeValue;
                        //$data['catid'] = $entry->getElementsByTagName('category')->item(0)->nodeValue;
                        //$data['author'] = $entry->getElementsByTagName('author')->item(0)->getElementsByTagName('name')->item(0)->nodeValue;                                
                        
                        //check if its draft by looking at <app:draft>no</app:draft>
                        $data['publish'] = 1;
                        $drafttag = $doc->getElementsByTagNameNS('http://www.w3.org/2007/app', 'draft');
                        foreach($drafttag as $d){
                                $data['publish'] = (strtolower($d->nodeValue) == 'yes') ? 0 : 1 ;
                        }
                        
                        //check entry tag from <category term='...'></category>
                        $tags = $entry->getElementsByTagName('category');
                        $arrtag = array();
                        if(!empty($tags)){
                                foreach($tags as $tag){
                                        $arrtag[] = $tag->getAttribute('term');
                                }
                        }
                        $data['tags'] = $arrtag;
                        
                        $this->data = $data;
                }                
                return $this->data;        
        }
        
        
        /**
        * Check if the HTTP method is allowed
        * 
        * @param string method
        * @return boolean
        */
        function checkMethod($method){
                return (in_array(strtoupper($method), $this->allowedmethod));
        }

}
