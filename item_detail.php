<div class="tag-lists">
  <h5 class="tagger-head"><?php _e('Tags', 'tagger') ; ?> : </h5>
  <?php if ( empty( $tags ) ) : ?>
    <p class="tagger-no-tag" > <em>No tags available </em></p>
  <?php else : foreach( $tags as $tag ) : ?>
    <a href="<?php echo osc_search_url( array( 'tag' => urlencode( $tag['tag'] ) ) ) ?>"><small><?php echo $tag['tag'] ?></small></a><?php echo $tag != end( $tags ) ? ', ' : '' ?>
  <?php endforeach; endif; ?>

</div>