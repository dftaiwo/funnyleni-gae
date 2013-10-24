<?php
/**
  Filename: header.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
    Created: Oct 2  08:43:17 AM
 */
?><?php $baseUrl = '/'; 
$user = $this->getUser();
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
        <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

                <title><?php echo $this->getPageTitle();?></title>
                <meta name="description" content="<?php echo $this->getPageDescription();?>">
                <meta name="author" content="FunnyLeni Team">
                <meta name="viewport" content="width=device-width,initial-scale=1">
                <?php $pageImage = $this->getPageImage(); 
                if($pageImage){ ?>
                <meta property="og:image" content="<?php echo $pageImage; ?>" />
                <?php } ?>
                <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/bootstrap.min.css">
                <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/bootstrap-responsive.min.css">
                <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/funnyleni.css">
                <script src="<?php echo ($baseUrl); ?>assets/js/jquery-1.7.2.min.js"></script>
                <script src="<?php echo ($baseUrl); ?>assets/js/funnyleni.js"></script>

                <script src="<?php echo ($baseUrl); ?>assets/js/modernizr-2.5.3-respond-1.1.0.min.js"></script>
                        <script>
                                var isAFunnyLeni = <?php echo ($user)? 'true':'false' ; ?>;//This guy let's the client side scripts know if we know this guy
                        </script>
        </head>
        <body>
                <div class="navbar" id="navBarArea">
                        <div class="navbar-inner"> 
                                <div class="container">
                                        <a href="/" class="brand">FunnyLeni</a> 
                                        <a class="btn btn-navbar navI" data-toggle="collapse" data-target=".nav-collapse">  
                                                <span class="icon-bar"></span>  
                                                <span class="icon-bar"></span>  
                                                <span class="icon-bar"></span>  
                                        </a>  
                                        <div class="nav-collapse navI">  
                                                <ul class="nav medium">  

                                                        <li>
                                                                <a href="/">Home</a>
                                                        </li>
                                                        <li>
                                                                <a href="/listVideos">FunnyLenies</a>
                                                        </li>

                                                        <li>
                                                                <a href="/submitVideo">
                                                                        Submit a FunnyLeni&nbsp;
                                                                        <span class="highlight">*</span>
                                                                </a>
                                                        </li>  
                                                        <?php if (isset($user) && $user) { ?>
                                                        <li class="dropdown">
                                                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $user['display_name']; ?></a>
                                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                                        <!--<li class=""><a href="/me">My Account</a></li>-->
                                                                        <li><a href="/me">My Account</a></li>
                                                                        <li><a href="/logout">Log Out</a></li>

                                                                </ul>
                                                        </li> 
                                                                
                                                        <?php } ?>
                                                                
                                                </ul>  
                                        </div>   
                                </div>  
                        </div>
                </div>
                <div class="headSeparator"></div>
                <div class="container "> 
                        <?php if ($flashMessage) {
                                $messageClass = ($flashMessage['messageType'])? 'alert-info':'alert-danger';
                                ?>
                                <div class="alert <?php echo $messageClass; ?>">
                                        <?php 
                                                echo $flashMessage['message']; 
                                        
                                        ?>
                                </div>

                        <?php } ?>
