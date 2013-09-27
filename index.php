<?php  
/*
Plugin Name: Tagger
Plugin URI: http://zeyhi.com
Description: This plugin adds a tagging and search function for each tags
Version: 1.0
Author: Zeus Camua
Author URI: http://zeyhi.com
Short Name: tagger
*/

require_once 'TaggerModel.php';


/* Initialize plugin
---------------------------------------------------------------------------*/

// This is to add the admin link for this page.
osc_add_hook("init", "_tagger_load_scripts");
osc_add_hook("init_admin", "_tagger_load_scripts");

// Initialize plugin informations and other necessary objects.
function _tagger_load_scripts() {

  osc_enqueue_style('tagit', osc_base_url() . 'oc-content/plugins/tagger/css/jquery.tagit.css');
  
  osc_register_script('tagit', osc_base_url() . 'oc-content/plugins/tagger/js/tag-it.min.js', 'jquery-ui');

  osc_enqueue_script('tagit');

}

/* end initialize plugin */

/* Install Tagger plugin.
---------------------------------------------------------------------------*/

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), '_tagger_install');

// Set plugin preferences
function _tagger_install() {

  TaggerModel::newInstance()->import('tagger/struct.sql');

}

/* end Install Ads Rating plugin. */


/* Uninstall Ads Rating plugin.
---------------------------------------------------------------------------*/

// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '_tagger_uninstall');

// Delete plugin preferences
function _tagger_uninstall() {
 
  TaggerModel::newInstance()->uninstall();

}

/* end Uninstall Ads Rating plugin. */


/* Admin Configuration
---------------------------------------------------------------------------*/

// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_configure", '_tagger_admin_configuration');

function _tagger_admin_configuration() {

    // Standard configuration page for plugin which extend item's attributes
    osc_plugin_configure_view(osc_plugin_path(__FILE__));

}

/* end Admin Configuration */

/* Item Form
---------------------------------------------------------------------------*/
 
// When publishing an item we show an extra form with more attributes
osc_add_hook('item_form', 'tag_form_control');

function tag_form_control( $catId ) {

  $plugin_info = osc_plugin_get_info('tagger/index.php');

  // Fail-early validation, terminate the function if not allowed in the category.
  if ( ! osc_is_this_category( $plugin_info[ 'short_name' ], $catId ) ) return;
  
    // This is used as a default available tags for tag-it plugin.
  $allTags = TaggerModel::newInstance()->getAllTags();

  $allTags = TaggerModel::newInstance()->array_pluck( 'tag', $allTags );

  require_once 'item_edit.php';

}
/* end Item Form */


/* Post form
---------------------------------------------------------------------------*/
 
// To add that new information to our custom table
osc_add_hook('posted_item', 'save_tags');

function save_tags( $item ) {

  TaggerModel::newInstance()->saveTags( $item[ 'fk_i_user_id' ], $item[ 'pk_i_id' ], Params::getParam( 'tagger-field' ) );

}

/* end Post Form */


/* Edit form
---------------------------------------------------------------------------*/
 
// Edit an item special attributes
osc_add_hook('item_detail', 'show_tags_on_view');

function show_tags_on_view( $item ) {
  
  $plugin_info = osc_plugin_get_info('tagger/index.php');

  if ( ! osc_is_this_category( $plugin_info[ 'short_name' ], osc_item_category_id() ) ) return;
  
  // var_dump($item);

  $tags = TaggerModel::newInstance()->getTags( $item[ 'pk_i_id' ] );
  
  require_once( 'item_detail.php' );
  
}

/* end Edit Form */

/* Item Details
---------------------------------------------------------------------------*/
 
// Edit an item special attributes

osc_add_hook('item_edit', 'show_edit_form_control');

function show_edit_form_control( $cat_id, $item_id ) {

  $tags = TaggerModel::newInstance()->getTags( $item_id );

  $tags = TaggerModel::newInstance()->array_pluck('tag', $tags);

  // This is used as a default available tags for tag-it plugin.
  $allTags = TaggerModel::newInstance()->getAllTags();

  $allTags = TaggerModel::newInstance()->array_pluck( 'tag', $allTags );

  require_once 'item_edit.php';

}

/* end Edit Form */

/* Item Update
---------------------------------------------------------------------------*/
 
// Edit an item special attributes
osc_add_hook('edited_item', 'update_tags');

function update_tags( $item ) {

  // Remove all tags assigned to user first
  TaggerModel::newInstance()->removeAllTags( $item[ 'fk_i_user_id' ], $item[ 'pk_i_id' ] );

  // Resave the tags
  TaggerModel::newInstance()->saveTags( $item[ 'fk_i_user_id' ], $item[ 'pk_i_id' ], Params::getParam( 'tagger-field' ) );

}



/* Tag Search
---------------------------------------------------------------------------*/
 
// When searching, add some conditions
osc_add_hook('search_conditions', 'search_condition');


function search_condition( $params ) {

  // Fail early validation.
  if ( ! array_key_exists( 'tag', $params ) ) {
    return;
  }
  
  Search::newInstance()->addConditions(sprintf("%st_tagger_tags.tag LIKE '%s'", DB_TABLE_PREFIX, $params[ 'tag' ]));
  Search::newInstance()->addConditions(sprintf("%st_tagger_tags.id = %st_tagger_tagged_objects.tag_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
  Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_tagger_tagged_objects.object_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
  Search::newInstance()->addTable(sprintf("%st_tagger_tagged_objects", DB_TABLE_PREFIX));
  Search::newInstance()->addTable(sprintf("%st_tagger_tags", DB_TABLE_PREFIX));

}

/* end Tag Search */

