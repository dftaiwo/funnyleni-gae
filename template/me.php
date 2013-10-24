<?php

/**
  Filename: me.php 
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 4, 2013  9:39:58 AM
 */

?>
<div class="page-header">
  <h1>My Account</h1>
</div>

<div class="row">
                <div class="span9 funnies row">
                        <?php if ($videos) { ?>
                                <ul class="thumbnails">
                                        <?php
                                        foreach ($videos as $video) {

                                                $itemLink = $this->url("/viewVideo/{$video['id']}");
                                                $youtubeImg = 'http://img.youtube.com/vi/' . $video['youtubeid'] . '/mqdefault.jpg';
                                                $videoFriends = isset($indexedVideoMap[$video['id']])? $indexedVideoMap[$video['id']]:array();
                                                $countFriends = count($videoFriends);
                                                ?>
                                                <li class="span3 imageSpans">
                                                        <div class="thumbnail">
                                                                <div class="imageContainer">
                                                                        <a href="<?php echo $itemLink; ?>">
                                                                                <img class="thumbnailImg" src="<?php echo $youtubeImg; ?>" alt="<?php echo $video['name']; ?>" width="100%"/>
                                                                        </a>
                                                                </div>
                                                                <div class="caption">
                                                                                        <h3>
                                                                                                <a  class="item" href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>">
                                                                                                        <?php echo $video['name']; ?>
                                                                                                </a>
                                                                                        </h3>
                                                                                        <br />
                                                                                        <p class="quickDesc">
                                                                                                <a href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>"><?php echo $video['description']; ?></a>
                                                                                        </p>
                                                                                        <p class="item">&nbsp;
                                                                                        </p>
                                                                                                                                                                        
                                                                                        
                                                                                 

                                                                </div>
                                                                <hr  class="thin" />
                                                                <div class="itemButtons">
                                                                        <span class="floatRight"><?php if($video['click_count']){ ?>
                                                                <?php echo $video['click_count']; ?> Leni View<?php echo ($video['click_count']>1)? 's':''; ?>
                                                        <?php } ?></span>
                                                                        <a href="<?php echo $this->url("/viewVideo/{$video['id']}"); ?>" class="btn btn-mini btn-inverse">View</a> | 
                                                                        <a href="<?php echo $this->url("/deleteVideo/{$video['id']}"); ?>" class="btn btn-mini btn-danger" onclick="return confirm('Are you sure you want to remove this video?');">Delete</a>
                                                                </div>
                                                        </div>
                                                </li>
                                        <?php } ?>
                                </ul>


                                <br />
                                <strong>
                                        Page <?php echo $page; ?> of <?php echo $totalPages; ?></strong>
                                <div class="pagination">

                                        <ul>
                                                <?php
                                                if ($totalPages > 1) {
                                                        if ($page > 1) {
                                                                ?>
                                                                <li><a href="/byFriends/<?php echo $page - 1; ?>">Previous</a></li>
                                                                <?php
                                                        }
                                                        for ($a = 1; $a <= $totalPages; $a++) {
                                                                ?>
                                                                <li>
                                                                        <a href="/byFriends/<?php echo $a; ?>">
                                                                                <?php echo $a; ?>
                                                                        </a>
                                                                </li>
                                                                <?php
                                                        }

                                                        if ($page < $totalPages) {
                                                                ?>
                                                                <li><a href="/byFriends/<?php echo $page + 1; ?>">Next</a></li>
                                                                <?php
                                                        }
                                                }
                                                ?>
                                        </ul>
                                </div>

                                <br />
                        <?php } else { ?>
                                <div class="alert alert-block alert-info">
                                        You have not uploaded a FunnyLeni. <u><a href="<?php echo $this->url("/submitVideo"); ?>">Submit a FunnyLeni Now!</a></u>
                                </div>
                        <?php } ?>
                </div>
                <div class="span3">
                        <img src="<?php echo $user['profile_image']; ?>" align="left" />
                        <h3><?php echo $user['display_name']; ?></h3>
                               <hr />
                               <?php if($connectedFriends){ ?>
                               
                               <h4>Your Friends on FunnyLeni</h4>
                               <ul class="friendsList">
                               <?php foreach($connectedFriends as $friend){ ?>
                                       <li>
                                               <a href="#" title="<?php echo $friend['didplay_name']; ?>">
                                                       <img src="<?php echo $friend['profile_image']; ?>" />
                                               </a>
                                       </li>
                               <?php } ?>
                               </ul>
                               <?php } ?>
                </div>
        </div>