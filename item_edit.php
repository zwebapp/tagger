<div class="tags_container">
  <label><?php _e('Tags', 'tagger') ; ?></label>
  <?php
    // This lines prevent to clear the field if the form is reloaded
    if( Session::newInstance()->_getForm('tagger-field') != '' ) {
        $detail['tagger-field'] = Session::newInstance()->_getForm('tagger-field');
    }
  ?>
  <input id="tags-input" type="text" name="tagger-field" placeholder="Input tags here" value="<?php echo isset($tags) ? implode(',', $tags) : '' ?>" />
</div>


<script>
jQuery(document).ready( function($) {

  $('#tags-input').tagit({
    placeholderText : 'Input tags here...',
    availableTags : <?php echo json_encode( $allTags ) ?>
  });

});
</script>
