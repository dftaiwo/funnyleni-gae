<?php

/**
  Filename: FunnyLeni.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2  08:42:47 AM
 */
require_once('inc/Basics.php');

class FunnyLeni extends Basics {

        private $appId = '866888311008.apps.googleusercontent.com';
        private $currentUser = false;
        private $currentUserId = 0; //This is a google user thingy
        private $tagId = 0;
        public $isDashboard = false;
        public $pageTitle = "FunnyLeni - Funny Videos, Everyday!";
        public $pageDescription = "We are all about Funny Videos in Nigeria";
        public $pageKeywords = array();
        public $pageImage = "";
        
        
        function __construct() {

                parent::__construct();

                $this->_setUser($this->sRead('user_info'));
        }

        function handleRequest() {
                if (!isset($_SERVER['PHP_SELF'])) {
                        $_SERVER['PHP_SELF'] = '/list';
                }
                //The subpart gets rid of the /
                // i'm fairly certain there's a much better way...

                $args = substr($_SERVER['PHP_SELF'], 1);
                //@TODO, refactor this
                $passedArgs = explode('/', $args);

                for ($a = 0; $a < 4; $a++) {
                        if (!array_key_exists($a, $passedArgs)) {
                                $passedArgs[$a] = "";
                        }
                }
                if ($passedArgs[0] == "") {
                        $this->isDashboard = true;
                }
                $this->passedArgs = $passedArgs;

//                $this->addToLog($this->passedArgs);

                $requestedAction = array_shift($passedArgs);
                if (substr($requestedAction, 0, 1) == '_') {
                        //Don't even dignify this with a response
                        exit;
                }
                if (!method_exists($this, "$requestedAction")) {
                        $requestedAction = 'listVideos';
                }

                $this->addToLog('Requested Action: ' . $requestedAction);
                //I'm sure there are better ways!! but i'm rushing this... so 
                //@TODO, refactor this
                list($arg1, $arg2, $arg3) = $passedArgs;

                $this->$requestedAction($arg1, $arg2, $arg3);
        }

        function loadHeader() {

                $flashMessage = $this->readFlash();
                $this->loadTemplate('header', compact('flashMessage'));
        }

        function loadFooter() {

                $flashMessage = $this->sRead('flash_message');
                $this->addToLog($flashMessage);
                $this->sWrite('flash_message', "");
                $this->loadTemplate('footer');
        }

        /**
         * Lists Submissions in DB
         */
        function listVideos($tagId = 0, $page = 1) {

                $this->setPageTitle("FunnyLeni Videos");

                $conditions = array('videos.active'=>1);
                $limit = $this->listLimit;

                $page = ($page + 0) ? $page + 0 : 1;

                $this->setPageDescription("We are all about Funny Videos in Nigeria: Page $page");

                $order = array('id' => 'DESC');
                $videos = array();
                $totalPages = 1;



                if ($this->tagId) {//Tag id has been passed
                }
                $fields = array('COUNT(*) as total_videos');
                $totalVideos = $this->findOne('videos', compact('conditions', 'fields'));

                $totalVideos = $totalVideos['total_videos'];


                if ($totalVideos) {

                        $totalPages = ceil($totalVideos / $limit);

                        $videos = $this->findAll('videos', compact('conditions', 'limit', 'order', 'page'));
                }

                $tags = $this->_getTags();
                $this->loadTemplate('list_videos', compact('videos', 'tags', 'tagId', 'totalVideos', 'limit', 'page', 'totalPages', 'tagId'));
        }

        function completeLoginProcess() {
                
        }

        function _requiresAuth() {

                if (!$this->currentUser) {
                        $this->redirect("/login?r={$this->currentUrl}", 'Please log in to continue');
                }
        }

        function getLocalGoogleUser($googleUserId = 0) {

                return $this->findOne('users', array('conditions' => array('googleid' => $googleUserId)));
        }

        function _setUser($user) {
                
                $this->currentUser = $user;
                if (!$user)
                        return;
                $this->currentUserId = $user['id'];
                
                $videosWatched = $this->sRead('videos_watched',array());
                
                foreach($videosWatched as $videoId){
                        $this->_addWatch($videoId, $this->currentUserId);
                }
                $this->sWrite('videos_watched',array());
        }

        function getUser() {
                return $this->currentUser;
        }

        function logout() {
                foreach($_SESSION as $key=>$value){
                        $this->sWrite($key,false);       
                }
                $this->sWrite('app_logged_out', true);

                $this->redirect('/', 'You have been logged out successfully');
        }

        function setAppSettings($settings) {
                $this->appSettings = $settings;
        }

        function viewVideo($videoId = 0) {

                $video = $this->_getVideo($videoId);

                if (!$video) {
                        $this->redirect('/', "Unable to locate video");
                }

                $this->_runQuery("UPDATE videos set click_count=click_count+1 where id='$videoId'");

                $this->setPageTitle("{$video['name']} : FunnyLeni Video");

                $this->setPageDescription("On FunnyLeni: {$video['description']}");
                if ($this->getUser()) {
                        $data = $this->_addWatch($videoId, $this->currentUserId);
                }else{
                        
                        $videosWatched = $this->sRead('videos_watched',array());
                        $videosWatched[] = $videoId;
                        
                }
                $tags = $this->_getTags();
                $youtubeImg = 'http://img.youtube.com/vi/' . $video['youtubeid'] . '/mqdefault.jpg';
                $this->setPageImage($youtubeImg);
                
                //THis is illegal! All in the bid to randomly pick videos I think this is the most illegal thing I've done 
                $order = array('videos.id'=>'DESC');
                $limit = 30;
                $conditions = array("videos.id NOT IN (SELECT video_id from views WHERE user_id='{$this->currentUserId}') AND videos.id!='{$videoId}'");
                $allVideos = $this->findAll('videos',compact('conditions','order','limit'));
                shuffle($allVideos);
                $chunks = array_chunk($allVideos, 6);
                $videos = $chunks[0];
                $this->loadTemplate('view_video', compact('video', 'tags', 'videoId','videos'));
        }

        function voteVideo($videoId = 0) {

                $this->_requiresAuth();

                $video = $this->_getVideo($videoId);

                if (!$video) {
                        $this->redirect('/', "Unable to locate video");
                }

                $conditions = array(
                    'user_id' => $this->currentUserId,
                    'video_id' => $videoId
                );
                $voted = $this->findOne('votes', compact('conditions'));
                if ($voted) {
                        $this->redirect($this->previousUrl, "You cannot vote more than once for the same video", 0);
                        return;
                }

                $data = array(
                    'user_id' => $this->currentUserId,
                    'video_id' => $videoId
                );
        }

        function _getVideo($videoId = 0) {

                return $this->findOne('videos', $videoId);
        }

        function submitVideo() {
                
                $this->_requiresAuth();
                
                $errors = array();


                if (!empty($_POST)) {
                        $requiredFields = array(
                            'youtube_url' => 'YouTube Video URL'
                        );



                        //let's force a trim
                        foreach ($_POST as $field => $value) {
                                $_POST[$field] = trim($value);
                        }

                        foreach ($requiredFields as $fieldKey => $fieldName) {

                                if (!array_key_exists($fieldKey, $requiredFields) || empty($requiredFields[$fieldKey])) {
                                        $errors[] = "$fieldName is required";
                                        continue;
                                }
                        }

                        if ($errors) {
                                
                        } else {

                                $youtubeVideoId = $this->_getYouTubeId($_POST['youtube_url']);

                                if (!$youtubeVideoId || strlen($youtubeVideoId) < 6 || strlen($youtubeVideoId) > 15) {
                                        $errors[] = "Invalid YouTube URL. Please check and try again";
                                } else {
                                        $youtubeInfo = $this->_getYouTubeInfo($youtubeVideoId);
echo json_encode($youtubeInfo);
exit;
                                        if ($youtubeInfo) {

                                                $title = $youtubeInfo['title'];

                                                $description = $youtubeInfo['description'];

                                                $this->addToLog("YouTube ID:" . $youtubeVideoId);

                                                //Let's check that it's not been submitted already

                                                $conditions = array('youtubeid' => $youtubeVideoId);

                                                $exists = $this->findOne('videos', compact('conditions'));

                                                if ($exists) {
                                                        if($exists['active']){
                                                                
                                                                $this->redirect("/viewVideo/{$exists['id']}", "Someone else has already submitted this video", 0);
                                                                
                                                        }
                                                }

                                                $data = array(
                                                    'youtubeid' => $youtubeVideoId,
                                                    'user_id' => $this->currentUserId,
                                                    'name' => $title,
                                                    'description' => $description,
                                                    'created' => $this->_now()
                                                );
                                                $saveConditions = array(
                                                    
                                                );
                                                
                                                if($exists){
                                                        $saveConditions['videos.id'] = $exists['id'];
                                                }
                                                $videoId = $this->saveData('videos', $data,$saveConditions);
                                                if (!$videoId) {
                                                        $this->redirect("/submitVideo", "An unexpected error occurred. Please try again later", 0);
                                                }
                                                if(!$saveConditions){
                                                        $this->redirect("/viewVideo/{$videoId}", "Submission was successful!");
                                                }else{
                                                        $this->redirect("/viewVideo/{$videoId}", "Submission was successful!< br /> PS: This video was previously deactivated. It has now been reactivated");
                                                }
                                        } else {
                                                $errors[] = "We were unable to retrieve any information on the video you submitted. <br />Are you sure this is a YouTube video? Please check and try again.";
                                        }
                                }
                        }
                }
                $this->loadTemplate('submit_video', compact('errors'));
        }

        function _getYouTubeId($fromUrl) {
//Copied from somewhere on StackOverflow
                preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $fromUrl, $matches);
                if (!($matches && isset($matches[1]))) {
                        return false;
                }
                return trim($matches[1]);
        }

        function _getTags() {
                return $this->findAll('tags', array('order' => array('tags.used' => 'DESC')));
        }

        public function getPageTitle() {
                return $this->pageTitle;
        }

        public function setPageTitle($pageTitle) {
                $this->pageTitle = $pageTitle;
                return $this;
        }

        public function getPageImage() {
                return $this->pageImage;
        }

        public function setPageImage($pageImage) {
                $this->pageImage = $pageImage;
                return $this;
        }

        public function getPageDescription() {
                return $this->pageDescription;
        }

        public function setPageDescription($pageDescription) {
                $this->pageDescription = $pageDescription;
                return $this;
        }

        public function getPageKeywords() {
                return $this->pageKeywords;
        }

        public function setPageKeywords($pageKeywords) {
                $this->pageKeywords = $pageKeywords;
                return $this;
        }

        function silentAuth() {
                
                $result = array('success' => false, 'message' => 'Unexpected error');
                 
                $subData = $_POST;
               
                $me = isset($subData['profile']) ? $subData['profile'] : array();

                if (!$me) {
                        echo json_encode($result);
                        exit;
                }
                $userId = $this->_saveGoogleProfile($me);

                $result['success'] = true;
                $result['message'] ='Successful';
                echo json_encode($result);
                exit;
        }

        function _saveFriendsList($userId, $friendsList,&$plus) {
                $this->_requiresAuth();
                if (!$userId || !$friendsList)
                        return;

                $totalFriends = $friendsList['totalItems'];
                if (!$totalFriends) {
                        return;
                }
                $conditions = array(
                    'friends.user_id' => $userId
                );
                $currentFriends = $this->findAll('friends', compact('conditions'));
                $indexedFriends = array();
                foreach ($currentFriends as $friend) {
                        $indexedFriends[$friend['googleid']] = $friend['id'];
                }

                foreach ($friendsList['items'] as $friend) {
                        if ($friend['objectType'] != 'person')
                                continue;
                        $dbData = array(
                            'user_id' => $userId,
                            'googleid' => $friend['id'],
                            'display_name' => $friend['displayName'],
                            'profile_url' => $friend['url'],
                            'profile_image' => isset($friend['image']['url']) ? $friend['image']['url'] : '',
                            'network' => 1,
                        );
                        $conditions = array();
                        if (array_key_exists($friend['id'], $indexedFriends)) {
                                //Then we're updating
                                $conditions = array(
                                    'friends.id' => $indexedFriends[$friend['id']],
                                    'friends.user_id' => $userId,
                                );
                                $dbData['modified'] = $this->_now();
                        } else {
                                //we are inserting
                                $dbData['created'] = $dbData['modified'] = $this->_now();
                        }
                        $newFriendId = $this->saveData('friends', $dbData, $conditions);
                        $indexedFriends[$friend['id']] = $newFriendId;
                }
                
                if(isset($friendsList['nextPageToken']) && $friendsList['nextPageToken']){
                        $friendsList = $plus->people->listPeople('me','visible',array('orderBy'=>'best','pageToken'=>$friendsList['nextPageToken']));
                        $this->_saveFriendsList($userId, $friendsList, $plus);
                }
        }

        function _getConnectedFriends($userId, $userIdsOnly = false) {
                $this->_requiresAuth();
                
                $sql = "SELECT users.id,users.display_name,users.profile_url,users.profile_image FROM users WHERE users.id!=$userId AND googleid IN (SELECT googleid FROM friends where user_id=$userId)";
                $result = $this->_runQuery($sql);
                
                if(!mysql_num_rows($result)){
                        return array();
                }
                
                $connectedUsers = array();
                while($row= mysql_fetch_assoc($result)){
                        $connectedUsers[] = ($userIdsOnly)? $row['id']:$row;
                }
                return $connectedUsers;
//                $conditions = array(
//                    "googleid IN (SELECT googleid from users)",
//                    'friends.user_id' => $userId
//                );
//                $fields = false;
//                if ($userIdsOnly) {
//                        $fields = array('user_id');
//                }
//                return $this->findAll('friends', compact('conditions', 'fields'));
        }

        function _loginUser($userInfo) {
                $this->sWrite('app_logged_out', false);
                $this->sWrite('user_info', $userInfo);
                $this->_setUser($userInfo);
        }

        function login() {
                if ($this->getUser()) {
                        $this->redirect('/submitVideo', "You are already logged in");
                }

                $redirectUrl = isset($_GET['r']) ? $_GET['r'] : '/submitVideo';

                $this->sWrite('redirectUrl', $redirectUrl);

                $this->_doGoogleLogin();
                
                $this->redirect($redirectUrl);
        }

        function _doGoogleLogin() {

                require_once('Google_Client.php');
                require_once('Google_PlusService.php');
                $client = new Google_Client();
                $client->setRedirectUri($this->url('/login'));
                $plus = new Google_PlusService($client);
                
                $authUrl = $client->createAuthUrl();
                
                
                if (isset($_GET['code'])) {
                        try {

                                $client->authenticate($_GET['code']);

                                $this->sWrite('access_token', $client->getAccessToken());
                        } catch (Exception $e) {
                                //Maybe this was refreshed ?
                                $this->addToLog(' Token Exception!');
//                                exit;
                        }
                }

                $accessToken = $this->sRead('access_token');

                if ($accessToken) {
                        $client->setAccessToken($accessToken);
                }
    
                if (!$client->getAccessToken()) {
                        $this->redirect($authUrl);
                }
                
                $me = $plus->people->get('me');
                
                if (!$me) {
                        //Unexpected error
                        exit;
                }

                $userId = $this->_saveGoogleProfile($me);
                //Let's get his friends
                $friendsList = $plus->people->listPeople('me','visible',array('orderBy'=>'best'));
                $this->_saveFriendsList($userId,$friendsList,$plus);
                
                
        }
        
        function _saveGoogleProfile($me){
                //Let's create an array of his data
                $userData = array();

                $profileMappings = array(
                    'id' => 'googleid',
                    'displayName' => 'display_name',
                    'url' => 'profile_url',
                    'gender' => 'gender',
                    'birthday' => 'birthday',
                );

                foreach ($profileMappings as $googleField => $dbField) {

                        if (!array_key_exists($googleField, $me)) {
                                continue;
                        }
                        $userData[$dbField] = $me[$googleField];
                }
                if (!$userData) {
                        //For some wonderful reason!
                        $result['message'] = "Unable to find relevant user fields to save";
                        echo json_encode($result);
                        exit;
                }

                $userData['profile_image'] = isset($me['image']['url']) ? $me['image']['url'] : "";

                $userData['created'] = $this->_now();

                $userData['last_logon'] = $this->_now();

                $userData['access_token'] = $accessToken;

                $googleUserId = $me['id'];

                $conditions = array('users.googleid' => $googleUserId);
                $userExists = $this->findOne('users', compact('conditions'));

                if (!$userExists) {
                        $userId = $this->saveData('users', $userData);
                }else{
                        $this->saveData('users', $userData,$conditions);
                       $userId = $userExists['id'];
                }
                

                if (!$userId) {
                        $result['message'] = "An unexpected error occurred while trying to save your data. Please refresh and try again";
                        exit;
                }
                
                $conditions = array('users.id'=>$userId);
                $userInfo = $this->findOne('users', compact('conditions'));
                
                $this->_loginUser($userInfo);
                return $userId;
        }

        function byFriends($page = 1) {
                $this->_requiresAuth();
                $userIds = $this->_getConnectedFriends($this->currentUserId,true);
                if (!$userIds) {
                        $this->redirect('/listVideos', "There are currently no friends on your network who have watched funnylenies", 0);
                }

                $response = $this->_getVideosWatched($userIds, $page);

                extract($response);
                $this->loadTemplate('by_friends', compact('videos', 'friends', 'page', 'totalPages','users','indexedVideoMap'));
        }

        function _addWatch($videoId, $userId) {

                $data = array(
                    'video_id' => $videoId,
                    'user_id' => $userId,
                    'created' => $this->_now()
                );

                $this->saveData('views', $data);
        }

        function _getYouTubeInfo($youtubeVideoId) {
                require_once('Google_Client.php');
                require_once('Google_YouTubeService.php');
                $client = new Google_Client();
                $youtubeClient = new Google_YouTubeService($client);
                $listResponse = $youtubeClient->videos->listVideos("snippet", array('id' => $youtubeVideoId));
                $videoList = $listResponse['items'];
                if (empty($videoList)) {
                        return false;
                }

                $video = $videoList[0];

                $videoSnippet = $video['snippet'];
                
                return $videoSnippet;
        }

        function _getVideosWatched($userIds = array(), $page = 1) {

                if (!is_array($userIds)) {
                        $userIds = array($userIds);
                }
                if(!$userIds){
                        $userIds  =array(0);
                }
                $userIds = join(',',$userIds);
                
                $conditions = array("videos.id IN (SELECT DISTINCT video_id FROM views WHERE user_id IN ($userIds))",'videos.active'=>1);
                $limit = $this->listLimit;
                $page = ($page + 0) ? $page + 0 : 1;

                $order = array('id' => 'DESC');
                $videos = array();
                $totalPages = 1;



                if ($this->tagId) {//Tag id has been passed
                }
                $fields = array('COUNT(*) as total_videos');
                $totalVideos = $this->findOne('videos', compact('conditions', 'fields'));

                $totalVideos = $totalVideos['total_videos'];
                
                $users = array();
                $indexedVideoMap = array();
                
                if ($totalVideos) {

                        $totalPages = ceil($totalVideos / $limit);

                        $videos = $this->findAll('videos', compact('conditions', 'limit', 'order', 'page'));
                        $videoIds  = array();
                        
                        foreach($videos as $video){
                                $videoIds[] = $video['id'];
                        }
                        
                        
                        
                        
                        $conditions  = array(
                                "user_id IN ($userIds)",
                            "video_id"=>$videoIds
                            );
                        
                            $fields = array(
                                'user_id','video_id'
                            );
                            $group = array(
                                'user_id','video_id'
                            );
                            $videoWatchMap = $this->findAll('views',compact('conditions','group'));

                            

                            foreach($videoWatchMap as $row){
                                    $indexedVideoMap[$row['video_id']][] = $row['user_id'];
                            }
                            
                             $conditions  = array(
                                "id IN ($userIds)"
                            );
                             $fields = array(
                                 'id','display_name','profile_url','profile_image'
                             );
                             $index = 'id';
                            $users = $this->findAll('users',compact('conditions','fields','index'));
                            
                            
                            
                }


                return compact('videos', 'page', 'totalVideos', 'totalPages','users','indexedVideoMap');
        }

        function getAppId() {
                return $this->appId;
        }

        /*
          function hackPopulate(){

          require_once('Google_Client.php');
          require_once('Google_YouTubeService.php');
          $client = new Google_Client();
          $youtubeClient = new Google_YouTubeService($client);
          $listResponse = $youtubeClient->search->listSearch("id,snippet", array('q' => "comedy nigeria",
          'maxResults' =>50,'order'=>'viewcount','type'=>'video','videoDuration'=>'medium'
          ));
          $videoList = $listResponse['items'];
          if (empty($videoList)) {
          return false;
          }
          foreach($videoList as $video){
          $youtubeVideoId = $video['id']['videoId'];
          $title = $video['snippet']['title'];
          $description = $video['snippet']['description'];
          $data = array(
          'youtubeid' => $youtubeVideoId,
          'user_id' => 1,
          'name' => $title,
          'description' => $description,
          'created' => $this->_now()
          );
          $videoId = $this->saveData('videos', $data);
          }
          exit;
          $video = $videoList[0];

          $videoSnippet = $video['snippet'];

          }
         * 
         */
        
        function me(){
                
                $this->_requiresAuth();
                $conditions = array('videos.user_id'=>$this->currentUserId);
                $videos = $this->findAll('videos',compact('conditions'));
                $connectedFriends = $this->_getConnectedFriends($this->currentUserId);
                $user = $this->currentUser;
                
                
                $this->loadTemplate('me',compact('videos','user','connectedFriends'));
                
        }
        
        function fakeLogin(){
                //Disable this in production o!
                
                if($_SERVER['HTTP_HOST']=='localhost:9080'){
                        $user = $this->findOne('users');
                        $this->_loginUser($user);
                        $this->redirect('/','You have now been logged in');

                }else{
                        $this->redirect('/','The requested method is not available in production',0);
                }
                
        }
        
        function deleteVideo($videoId){
                $this->_requiresAuth();
                $video = $this->findOne('videos',$videoId);
                if(!$video || $video['user_id']!=$this->currentUserId){
                        $this->redirect('/me','Unable to locate video. Please try again',0);
                }

                $newData = array(
                    'user_id'=>CFG_ANONYMOUS_USER_ID,
                    'active'=>0
                    );
                $conditions = array(
                    'videos.id'=>$videoId
                );
                
                        $this->saveData('videos',$newData, $conditions);
                
                $this->redirect('/me','This video has been removed');

        }
        
        function editVideo($videoId){
                
                $this->_requiresAuth();
                $video = $this->findOne('videos',$videoId);
                if(!$video || $video['user_id']!=$this->currentUserId){
                        
                        $this->redirect('/me','Unable to locate video. Please try again',0);
                        
                }
           
                $this->loadTemplate('edit_video',compact('video'));
                
        }
}

