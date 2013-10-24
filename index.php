<?php

/**
  Filename: index.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Sep 29, 2013  1:00:50 PM
 * So this is like the controller for the app
 */

/**
 * I haven't figured out how to pass the class and objects around, so I'm putting this code here....
 * 29-09-2013. Dft
 */
session_start();
session_name('funnyleni');
require_once('inc/app_config.php');
require_once('inc/shortcuts.php');
require_once('inc/googly_things.php');
 
require_once('inc/FunnyLeni.php');

ob_start(); //Still looking for a better yet simpler way to do redirects after outputs adn update meta tags, so I'm using ob_ things
$funnyLeni = new FunnyLeni();
$funnyLeni->handleRequest();
$pageContents = ob_get_contents();
ob_end_clean();

$funnyLeni->loadHeader();
echo $pageContents;
$funnyLeni->loadFooter();
