<?php

/**
  Filename: sign_in.php 
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2, 2013  1:03:01 PM
 */

?>
<?php if(1>2){ ?>
<?php if(!$this->getUser()){ ?>
<div id="signinButtonTips">
  <p>Find out which of your friends have used FunnyLeni to see what videos they liked.</p>
        <span id="signinButton">
                <?php if(!$this->sRead('app_logged_out')){ ?>
  <span
    class="g-signin"
    data-callback="onSignInCallback"
    data-clientid="<?php echo $this->getAppId(); ?>"
    data-cookiepolicy="single_host_origin"
    data-requestvisibleactions="http://schemas.google.com/CommentActivity http://schemas.google.com/ReviewActivity http://schemas.google.com/AddActivity"
    data-scope="https://www.googleapis.com/auth/plus.login">
  </span>
                 <?php }else{ ?>               
                <div class="g-signin" data-callback="onSignInCallback"
    data-approvalprompt="force"
    data-clientid="<?php echo $this->getAppId(); ?>"
    data-requestvisibleactions="http://schemas.google.com/CommentActivity http://schemas.google.com/ReviewActivity http://schemas.google.com/AddActivity"
    data-cookiepolicy="single_host_origin"
    >

  </div>
                
                <?php } ?>
</span>
  
  
</div>
<?php }else{ ?>

<?php } ?>
<div id="authOps" style="display:none">
    <!--<button id="disconnect" >Disconnect your Google account from this app</button>-->
</div>
<?php } ?>
<a href="/byFriends" class="btn btn-large btn-success">See Videos Watched By Friends</a>
 