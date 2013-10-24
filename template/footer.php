<?php
/**
  Filename: footer.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
    Created: Oct 2  08:45:17 AM
 */
?>
<footer>
        <a target="_blank" href="https://developers.google.com/appengine/">
                <img src="https://developers.google.com/appengine/images/appengine-noborder-120x30.gif" 
                     alt="Powered by Google App Engine" />
        </a> | <a href="http://gplus.to/dft">@dftaiwo</a>
</footer>

<?php
if (DEBUG) {
        ?>
        <table width="100%" border="1" class="debugTable">
                <tr>
                        <th width="10"></th>
                        <th>Query</th>
                        <th>Caller</th>
                        <!--<th>Caller Loc</th>-->
                </tr>

                <?php
                foreach ($this->getLogs() as $index => $logMessage) {
                        ?>
                        <tr>
                                <th><?php echo $index + 1; ?></th>
                                <th><?php if(is_array($logMessage['message'])){ 
                                        pr($logMessage['message']) ;
                                }else{ 
                                        echo $logMessage['message'];
                                } 
                                ?></th>
                                <th><?php echo($logMessage['line']); ?></th>
                                <!--<th><?php echo($logMessage['file']); ?></th>-->
                        </tr>
                <?php } ?>
        </table>
<?php } ?>
</div> <!-- /container -->
<div class="notVisible hidden" >
        <?php if(!$this->getUser() && !$this->sRead('app_logged_out')){ ?>
         <span
    class="g-signin"
    data-callback="onSignInCallback"
    data-clientid="<?php echo $this->getAppId(); ?>"
    data-cookiepolicy="single_host_origin"
    data-requestvisibleactions="http://schemas.google.com/CommentActivity http://schemas.google.com/ReviewActivity http://schemas.google.com/AddActivity"
    data-scope="https://www.googleapis.com/auth/plus.login">
  </span>
        <?php } ?>
</div>
<script src="<?php if (isset($baseUrl)) echo ($baseUrl); ?>/assets/js/bootstrap.min.js"></script>
<!-- Place this asynchronous JavaScript just before your </body> tag -->
     
    <script type="text/javascript">
  (function() {
   var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
   po.src = 'http://apis.google.com/js/client:plusone.js';
   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
 })();
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-44620354-1', 'funnyleni.appspot.com');
  ga('send', 'pageview');

</script>
</body>
</html>

