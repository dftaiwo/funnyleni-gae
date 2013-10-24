<?php
/**
  Filename: list_videos.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2, 2013  10:08:16 AM
 */

if(!$tagId) $tagId = 0;
?>
<div id="videosListContainer">
<?php if ($this->isDashboard) { ?>
        <div class="hero-unit">
                <img src="/assets/img/funny.png" align="right" />
                <h2>Welcome to <span>FunnyLeni</span></h2>
                &nbsp;
                <p>
                        We are all about Funny Videos in Nigeria
                </p>
                <?php $this->loadTemplate('sign_in'); ?>
        </div>

<?php } ?>
<div class="row">
        <div class="span9 funnies row">
                <?php if ($videos) { ?>
                        <ul class="thumbnails">
                                <?php
                                foreach ($videos as $video) {

                                        $itemLink = $this->url("/viewVideo/{$video['id']}");
                                        $youtubeImg = 'http://img.youtube.com/vi/' . $video['youtubeid'] . '/mqdefault.jpg';
                                        ?>
                                        <li class="span3 imageSpans">
                                                <div class="thumbnail">
                                                        <div class="imageContainer">
                                                                <a href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>">
                                                                        <img class="thumbnailImg" src="<?php echo $youtubeImg; ?>" alt="<?php echo $video['name']; ?>" width="100%"/>
                                                                </a>
                                                        </div>
                                                        <div class="caption">

                                                                <h3>
                                                                        <a  class="item" href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>">
                                                                                <?php echo $video['name']; ?>
                                                                        </a>
                                                                </h3>
                                                                <p class="quickDesc">
                                                                        <a href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>"><?php echo $video['description']; ?></a>
                                                                </p>
                                                                <p class="item">
<!--                                                                        <a href="<?php echo $itemLink; ?>">
                                                                                @<?php echo 'Uploader Name'; ?>
                                                                        </a>-->
                                                                </p>
                                                        </div>
                                                        
                                                        <hr  class="thin" />
                                                        <div class="itemButtons">
                                                                <span class="floatRight"><?php if($video['click_count']){ ?>
                                                                <?php echo $video['click_count']; ?> Leni View<?php echo ($video['click_count']>1)? 's':''; ?>
                                                        <?php } ?></span>
                                                                <g:plusone size="medium"data-href="<?php echo $itemLink; ?>"></g:plusone>
                                                                
                                                        </div>
                                                </div>
                                        </li>
                                <?php } ?>
                        </ul>


                        <br />
                        <strong>Total Videos Here: <?php echo $totalVideos; ?> | 
                        Page <?php echo $page; ?> of <?php echo $totalPages; ?></strong>
                        <div class="pagination">
                                
                                <ul>
                        <?php if($totalPages>1){ 
                                if($page>1){
                                ?>
                                        <li><a href="/listVideos/<?php echo $tagId; ?>/<?php echo $page-1; ?>">Previous</a></li>
                                        <?php
                                }
                        for($a=1;$a<=$totalPages;$a++){        
                                   ?>
                                           <li>
                                                   <a href="/listVideos/<?php echo $tagId; ?>/<?php echo $a; ?>">
                                                   <?php echo $a; ?>
                                                   </a>
                                           </li>
                        <?php }  
                        
                        if($page<$totalPages){
                                ?>
                                        <li><a href="/listVideos/<?php echo $tagId; ?>/<?php echo $page+1; ?>">Next</a></li>
                                        <?php
                                }
                        }
                        ?>
                                </ul>
                        </div>
              
                        <br />
                <?php } else { ?>
                        <div class="alert alert-block alert-info">
                                There are currently no videos here. Be the first to <u><a href="<?php echo $this->url("/submitVideo"); ?>">Submit a FunnyLeni!</a></u>
                        </div>
                <?php } ?>
        </div>
        <div class="span3">
                <?php $this->loadTemplate('right_column', $tags); ?>
        </div>
</div>

</div>