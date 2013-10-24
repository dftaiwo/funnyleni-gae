<?php
/**
  Filename: tags_column.php
  @author: Femi TAIWO [dftaiwo@gmail.com]
  Created: Oct 2, 2013  11:58:44 AM
 */
?>
<div class="hero-unit">
      <?php If(!$this->isDashboard) $this->loadTemplate('sign_in'); ?>
        <hr />
        <!-- 
        <h3>Tags</h3>
        This feature is not yet available :)
        <ul class="themes">
                <?php
                if (isset($tags) && $tags) {
                        foreach ($tags as $tag) {
                                ?>
                                <li><a href="/listVideos/<?php echo $tag['id']; ?>"><?php echo $tag['name']; ?></a></li>        
                                <?php
                        }
                }
                ?>
        </ul>
        -->
</div>