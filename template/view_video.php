<?php
/**
  Filename: view_video.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2, 2013  11:24:19 AM
 */
$itemLink = $this->url("/viewVideo/{$video['id']}");
?>
<script>
        
        redirectTo=false;
        </script>
<div class="row">

        <div class="span9">
                <h2><?php echo $video['name']; ?></h2>

                 <iframe id="player" type="text/html" width="100%" height="390"
                        src="http://www.youtube.com/embed/<?php echo $video['youtubeid']; ?>?enablejsapi=1&origin=<?php echo $this->url('/'); ?>&rel=0"
                        frameborder="0" allowfullscreen></iframe>

                <hr />
                <div class="socialButtons">
                        <div id="sharePost" class="floatRight"> <g:plusone size="medium"data-href="<?php echo $itemLink; ?>"></g:plusone></div>
                        <span id="myBtn" class="demo g-interactivepost"
                                data-clientid="<?php echo $this->getAppId(); ?>"
                                data-contenturl="<?php echo $this->getCurrentUrl(); ?>"
                                data-calltoactionlabel="WATCH"
                                data-callback="onSignInCallback"
                                data-calltoactionurl="<?php echo $this->getCurrentUrl(); ?>?invite=true"
                                data-cookiepolicy="single_host_origin"
                                data-scope="https://www.googleapis.com/auth/plus.login"
                                data-accesstype="offline"
                                data-prefilltext="Watch ' <?php echo str_replace('"',"'",$video['name']); ?> ' on FunnyLeni!">
                                <span class="icon">&nbsp;</span>
                                <span>Share or Recommend to friends!</span>
                                </span>
                        </div>

                        
                        <?php if($videos) { ?>
                                        <hr />

                        <div class="row-fluid">
                                <h3>More FunnyLenies</h3>
                                 <ul class="thumbnails">
                                <?php
                                foreach ($videos as $video) {

                                        $itemLink = $this->url("/viewVideo/{$video['id']}");
                                        $youtubeImg = 'http://img.youtube.com/vi/' . $video['youtubeid'] . '/mqdefault.jpg';
                                        ?>
                                        <li class="span4 imageSpans smaller">
                                                <div class="thumbnail">
                                                        <div class="imageContainer">
                                                                <a href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>">
                                                                        <img class="thumbnailImg" src="<?php echo $youtubeImg; ?>" alt="<?php echo $video['name']; ?>" width="100%"/>
                                                                </a>
                                                        </div>
                                                        <div class="caption">
                                                                        <a  class="item" href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>">
                                                                                <?php echo $video['name']; ?>
                                                                        </a>
                                                        </div>
                                                </div>
                                        </li>
                                <?php } ?>
                                 </ul>
                        </div>
                        <?php
                        }
                        
                        ?>
                 
 
        </div>
        <div class="span3">
                <p>&nbsp;</p>
                <div>
                <h4>About Video</h4>
                        <p><?php echo $video['description']; ?></p>
                </div>
                <hr />
                <?php echo $this->loadTemplate('right_column', $tags); ?>
        </div>
</div>