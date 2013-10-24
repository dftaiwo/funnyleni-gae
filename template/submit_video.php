<?php

/**
  Filename: submit_video.php 
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2, 2013  10:23:08 AM
 */
?>

<form  class="form-horizontal" action="/submitVideo" method="POST" autocompleted="off">
         <fieldset>
                <legend>Submit YouTube Video</legend>
        <div class="control-group">
                <label class="control-label" for="videoId">
                        YouTube Video URL
                </label>
                <div class="controls">
                        <input class="fullWidth"  type="text" value="<?php echo isset($_POST['youtube_url'])?$_POST['youtube_url']:''; ?>" name="youtube_url" id="youtube_url" placeholder="Provide enter full YouTube URL" required/>
                        
                        <br />
                         <span >You can copy it from the address bar on youtube and paste here<br />
                         e.g <example class="label label-info">https://www.youtube.com/watch?v=NU_wNR_UUn4</example>
                         </span>
                </div>
        </div>
         
        <input type="submit" class="btn btn-success" />
        
         </fieldset>
</form>