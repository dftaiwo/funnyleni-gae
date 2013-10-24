<?php
/**
  Filename: by_friends.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2, 2013  5:20:10 PM
 */
?>
<div id="videosListContainer">
        <div class="page-header"><h1>Videos Watched by Friends</h1></div>
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
                                                                <div class="caption tabbable">
                                                                        <div class="tab-content">
                                                                                <div class="tab-pane active" id="home<?php echo $video['id']; ?>">
                                                                                        <h3>
                                                                                                <a  class="item" href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>">
                                                                                                        <?php echo $video['name']; ?>
                                                                                                </a>
                                                                                        </h3>
                                                                                        <p><a href="#friends<?php echo $video['id']; ?>" data-toggle="tab"  class="btn-mini floatRight">Watched by <?php echo $countFriends ?> Friend<?php echo ($countFriends>1)? 's':''; ?></a></p>
                                                                                        <br />
                                                                                        <p class="quickDesc">
                                                                                                <a href="<?php echo $itemLink; ?>" title="<?php echo $video['name']; ?>"><?php echo $video['description']; ?></a>
                                                                                        </p>
                                                                                        <p class="item">
                        <!--                                                                        <a href="<?php echo $itemLink; ?>">
                                                                                                        @<?php echo 'Uploader Name'; ?>
                                                                                                </a>-->
                                                                                                
                                                                                        </p>
                                                                                                                                                                        
                                                                                        
                                                                                </div>
                                                                                <div class="tab-pane friendsThumbs" id="friends<?php echo $video['id']; ?>">
                                                                                        <?php foreach($videoFriends as $userId){
                                                                                                
                                                                                                if(!isset($users[$userId])) continue;
                                                                                                $videoFriend = $users[$userId];
                                                                                        ?>        
                                                                                        <a href="<?php echo $videoFriend['profile_url'];   ?>" target="_blank" title="<?php echo $videoFriend['display_name']; ?>">
                                                                                                <img src="<?php echo $videoFriend['profile_image'];   ?>" alt="<?php echo $videoFriend['display_name']; ?>"  />
                                                                                        </a>
                                                                                        

                                                                                        
                                                                                        
                                                                                        <?php
                                                                                        } ?>
                                                                                        <br />
                                                                                        
                                                                                        <button href="#home<?php echo $video['id']; ?>" data-toggle="tab" class="btn btn-mini">Back to Info</button>
                                                                                </div>
                                                                        </div>

                                                                </div>
                                                                <hr  class="thin" />
                                                                <div class="itemButtons">
                                                                        
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
                                        There are currently no videos here. Be the first to <u><a href="<?php echo $this->url("/submitVideo"); ?>">Submit a FunnyLeni!</a></u>
                                </div>
                        <?php } ?>
                </div>
                <div class="span3">
                        <?php $this->loadTemplate('right_column', compact('tags', 'friends')); ?>
                </div>
        </div>

</div>