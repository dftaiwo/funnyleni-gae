<?php

/**
  Filename: login.php 
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2, 2013  5:01:40 PM
 */
?>
<script>
var redirectTo = '/submitVideo' ;//this is a temporary hack
</script>
<div class="row">
        <div class="span6 hero-unit">
                <h3>Please log in to continue</h3>
                <div id="progressMessage">
        
</div>
<div id="signinButtonTips">
        <span id="signinButton">
  <span
    class="g-signin"
    data-accesstype="offline"
    data-callback="onSignInCallback"
    data-clientid="<?php echo $this->getAppId();?>"
    data-cookiepolicy="single_host_origin"
    data-requestvisibleactions="http://schemas.google.com/CommentActivity http://schemas.google.com/ReviewActivity http://schemas.google.com/AddActivity"
    data-scope="https://www.googleapis.com/auth/plus.login">
  </span>
                  
  </div>
                
</span>
  
  
</div>
        </div>
</div>
