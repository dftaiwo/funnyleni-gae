<?php

/**
  Filename: Basics.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2  08:41:02 AM
 */
class Basics {

        private $dbConnection;
        public $listLimit = 15;
        public $appSettings = array(
            'loginUrl' => 'http://google.com', //Just to have something sha,
            'logoutUrl' => 'abs',
            'uploadUrl' => '/'
        );
        public $gsServeOptions = array(
            'size' => 400,
        );
        public $basePath = '';
        public $currentUrl = '';
        public $gUserService;
        public $gCloudService;
        public $passedArgs = array(
            0 => '', 1 => '', 2 => '', 3 => '', //Just to avoid error checking troubles 
        );
        public $devLog = array();
        var $previousUrl = '/';

        function __construct() {
                
                $this->currentUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

                $this->basePath='http://'.$_SERVER['HTTP_HOST'];
                
                $this->dbConnection = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Service Unavailable. Please try again later. Err 1");

                mysql_select_db(DB_NAME, $this->dbConnection) or die("Service Unavailable. Please try again later. Err 2");

                $this->previousUrl = $this->sRead('previousUrl');
        }

        function __destruct() {

                $currentUrl = $_SERVER['PHP_SELF'];
                $this->sWrite('previousUrl', $currentUrl);
        }

        function sRead($key,$defaultValue=NULL) {
                if (!array_key_exists($key, $_SESSION)) {

                        return $defaultValue;
                }
                return $_SESSION[$key];
        }

        function sWrite($key, $value) {
                $_SESSION[$key] = $value;
        }

        function setFlash($message, $messageType) {
                $this->sWrite('flash_message', array('message' => $message, 'messageType' => $messageType));
        }

        function readFlash() {

                $flashMessage = $this->sRead('flash_message');
                return $flashMessage;
        }

        function redirect($toUrl, $message = '', $messageType = 1) {
                $this->setFlash($message, $messageType);
                if (!$toUrl) {
                        $toUrl = '/';
                }

                header("location: $toUrl");

                echo "<a href='$toUrl'>$message</a>";
                exit;
        }

        function loadTemplate($templateName, $viewVariables = array()) {
                foreach ($viewVariables as $field => $value) {
                        $$field = $value;
                }

                $fullPath = "template/{$templateName}.php";

                if (!file_exists($fullPath)) {
                        echo "<span class='appError'>Unable to locate requested template <u>{$templateName}</u></span>";
                        return;
                }

                include($fullPath);
        }

        /**
         * Generates select statements for ONE table
         * I could have done this in many better ways, but I'm aiming for SIMPLICITY not FLEXIBILITY or SMARTY
         * What to do next is to reach joefazee for his ORM
         * @param type $table - name of the database table to select from
         * @param type $options- array of options - for where conditions, order by clause,fields, limit and page/offset
         */
        function findAll($table, $options = array()) {
                if (!$table) {
                        $this->addToLog('Missing Table Name');
                        return array();
                }
                $sql = array();

                $sql[] = "SELECT";
                
                if (!is_array($options)) {
                        if (is_numeric($options)) {
                                $c = array();
                                $c["{$table}.id"] = $options;
                        } else {
                                $c = $options;
                        }
                        $options = array('conditions' => $c);
                }
                $fields = (isset($options['fields']) && $options['fields']) ? $options['fields'] : '*';

                if (is_array($fields)) {
                        $fields = join(",", $fields);
                }

                $sql[] = $fields;

                $sql[] = "FROM $table";

                $argConditions = isset($options['conditions']) ? $options['conditions'] : array();
                $conditions = array();
                if (is_array($argConditions)) {

                        foreach ($argConditions as $field => $value) {
                                if (is_numeric($field)) {//Meaning the field+value was completely defined
                                        $conditions[] = $value;
                                        continue;
                                }

                                $equalitySeparator = '='; //For future things like >=, <=, > , <, '!=',<>
//                                if (!(is_numeric($value) || is_string($value))) {//Um... we can't handle such right now
//                                        if(is_array($value))
//                                        continue;
//                                }
                                if (!is_numeric($value)) {
                                        if(is_array($value)){
                                                $value = join(',',$value);
                                                $condition = "{$field} IN ({$value})";
                                                $conditions[] = $condition;
                                                continue;
                                        }
                                        $value = addslashes($value);
                                        $value = "'$value'";
                                }

                                $condition = "{$field}{$equalitySeparator}{$value}";

                                $conditions[] = $condition;
                        }
                } else {
                        $conditions[] = $argConditions;
                }

                if ($conditions) {
                        $conditions = 'WHERE ' . join(' AND ', $conditions);
                } else {
                        $conditions = '';
                }

                $sql[] = $conditions;

                $groupByConditions = isset($options['group']) ? $options['group'] : array();
                
                if($groupByConditions){
                        if(!is_array($groupByConditions)){
                                $groupByConditions = array($groupByConditions);
                        }
                        $groupByConditions = join(',',$groupByConditions);
                        $sql[]="GROUP BY $groupByConditions";
                        
                }

                $argOrder = isset($options['order']) ? $options['order'] : false;
                
                $order = array();
                if ($argOrder) {
                        if (is_array($argOrder)) {
                                foreach ($argOrder as $field => $direction) {

                                        if (!is_string($field) && !is_string($direction)) {
                                                trigger_error("Invalid order arguments for sql", E_WARNING);
                                                continue;
                                        }

                                        if (!is_string($field) && is_string($direction)) {
                                                $order[] = $direction;
                                                continue;
                                        }

                                        $order[] = "$field $direction";
                                }
                        } else {
                                $order = array($argOrder);
                        }

                        if ($order) {

                                $sql[] = "ORDER BY " . join(', ', $order);
                        }
                }
                $limitPoint = isset($options['limit']) ? $options['limit'] : false;
                $page = isset($options['page']) ? $options['page'] : false;
                $limit = '';
                if ($page && $limitPoint) {
                        $startFrom = ($page - 1) * $limitPoint;
                        $limit = " LIMIT $startFrom,$limitPoint";
                } elseif ($limitPoint) {
                        $limit = "LIMIT $limitPoint";
                }

                $sql[] = $limit;

                $sql = join(' ', $sql);


                $result = $this->_runQuery($sql);
                if (!$result) {
                        trigger_error("Unable to complete request. $sql " . mysql_error(), E_USER_NOTICE);
                        return array();
                }
                $records = array();

                if (!mysql_num_rows($result)) {
                        return $records;
                }

                if(isset($options['index'])){
                        
                        while ($row = mysql_fetch_assoc($result)) {
                                $index = $options['index'];
                                $records[$row["$index"]] = $row;
                                
                        }
                }else{
                        while ($row = mysql_fetch_assoc($result)) {
                                $records[] = $row;
                        }
                }

                return $records;
        }

        function findOne($table, $options = array()) {

                $result = $this->findAll($table, $options);

                if (!$result)
                        return array();

                return $result[0];
        }

        function saveData($tableName, $data, $argConditions = array()) {
                $sql = array();
                $isInsert = false;
                $conditions = array();
                if (!$data)
                        return false;
                if ($argConditions) {
                        $sql[] = "UPDATE $tableName SET ";
                } else {
                        $sql[] = "INSERT INTO $tableName SET ";
                        $isInsert = true;
                }
                
                $setData = array();
                
                foreach ($data as $field => $value) {

                        $value = addslashes(trim($value));
                        $setData[] = "$field='$value'";
                }
                $sql[] = join(', ', $setData);

                if (is_array($argConditions)) {

                        foreach ($argConditions as $field => $value) {
                                if (is_numeric($field)) {//Meaning the field+value was completely defined
                                        $conditions[] = $value;
                                        continue;
                                }

                                $equalitySeparator = '='; //For future things like >=, <=, > , <, '!=',<>
                                if (!(is_numeric($value) || is_string($value))) {//Um... we can't handle such right now
                                        continue;
                                }
                                if (!is_numeric($value)) {
                                        $value = addslashes($value);
                                        $value = "'$value'";
                                }

                                $condition = "{$field}{$equalitySeparator}{$value}";

                                $conditions[] = $condition;
                        }
                } else {
                        $conditions[] = $argConditions;
                }

                if ($conditions) {
                        $conditions = 'WHERE ' . join(' AND ', $conditions);
                } else {
                        $conditions = '';
                }

                $sql[] = $conditions;

                $sql = join(' ', $sql);

                $result = $this->_runQuery($sql);
                if (!$result) {
                        return false;
                }
                if ($isInsert) {
                        return mysql_insert_id();
                }else{
                        return $argConditions;
                }
                return true;
        }

        function _runQuery($sql) {
                //Done this way so that we're able to log all queries from one place
                $this->addToLog($sql);
                return mysql_query($sql);
        }

        function setAppSettings($settings) {
                $this->appSettings = $settings;
        }

        function url($relUrl) {
                return $this->getBasePath() . $relUrl;
        }

        function gsServeUrl($gsUrl, $mimeType) {

                if (!$gsUrl)
                        return false;

                return $this->gCloudService->getImageServingUrl($gsUrl, $this->gsServeOptions);

                /*
                 * Previously, I didn't have the Python module PIL installed, so I had to 'hack' it
                  if(!CFG_IS_DEV){
                  return $this->gCloudService->getImageServingUrl($gsUrl,$this->gsServeOptions );
                  }else{
                  if(file_exists($gsUrl)){
                  $imageContents = file_get_contents($gsUrl);
                  $imageStr = "data:{$mimeType};base64,". base64_encode($imageContents);
                  return $imageStr;
                  }
                  }

                  return false;
                 */
        }

        function addToLog($message) {
                $bt = debug_backtrace();

                $caller = array_shift($bt);
                $file = pathinfo($caller['file']);

                $this->devLog[] = array('line' => $caller['line'], 'message' => $message, 'file' => $file['basename']);
        }

        function getLogs() {
                return $this->devLog;
        }

        function _now() {
                return date("Y-m-d H:i:s");
        }
        public function getBasePath() {
                return $this->basePath;
        }

        public function getCurrentUrl() {
                return $this->currentUrl;
        }


}