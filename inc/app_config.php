<?php

/**
  Filename: config.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2  08:52:43 AM
 */
//Since I'm developing on my local google app engine and the settings are different online      
if (stripos($_SERVER['HTTP_HOST'], 'localhost') === 0) {
        define('DB_HOST', '127.0.0.1');
        define('DB_USER', 'funnyu');
        define('DB_PASS', 'stackbuffer');
        define('DB_NAME', 'funnyleni_videos');
        define('CFG_IS_DEV',false);
//        define('PATH_SEPARATOR',':');
        define('DEBUG',true);
} else {

        define('DB_HOST', 'xxxxx');
        define('DB_USER', 'funnyu');
        define('DB_PASS', 'stackbuffer');
        define('DB_NAME', 'funnyleni');
        define('CFG_IS_DEV',false);
//        define('PATH_SEPARATOR',';');
        define('DEBUG',false);
}


define('CFG_ANONYMOUS_USER_ID',990000);