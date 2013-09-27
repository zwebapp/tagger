<?php
/*
*      OSCLass – software for creating and publishing online classified
*                           advertising platforms
*
*                        Copyright (C) 2010 OSCLASS
*
*       This program is free software: you can redistribute it and/or
*     modify it under the terms of the GNU Affero General Public License
*     as published by the Free Software Foundation, either version 3 of
*            the License, or (at your option) any later version.
*
*     This program is distributed in the hope that it will be useful, but
*         WITHOUT ANY WARRANTY; without even the implied warranty of
*        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*             GNU Affero General Public License for more details.
*
*      You should have received a copy of the GNU Affero General Public
* License along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* Model database for Products tables
* 
* @package OSClass
* @subpackage Model
* @since 3.0
*/

require_once( 'libs/freetag/freetag.class.php' );

class TaggerModel extends DAO
{
  private $model_tables = array( "t_tagger_tags", "t_tagger_tagged_objects" );

  private $freetag;

  /**
   * It references to self object: ModelProducts.
   * It is used as a singleton
   * 
   * @access private
   * @since 3.0
   * @var ModelProducts
   */
  private static $instance ;

  /**
   * It creates a new ModelProducts object class ir if it has been created
   * before, it return the previous object
   * 
   * @access public
   * @since 3.0
   * @return ModelProducts
   */
  public static function newInstance()
  {
      if( !self::$instance instanceof self ) {
          self::$instance = new self ;
      }
      return self::$instance ;
  }

  /**
   * Construct
   */
  function __construct()
  {
      parent::__construct();

      $freetag_options = array(
        'db_user'      => DB_USER,
        'db_pass'      => DB_PASSWORD,
        'db_host'      => DB_HOST,
        'db_name'      => DB_NAME,
        'table_prefix' => DB_TABLE_PREFIX
		  );
      $this->freetag = new freetag( $freetag_options );
  }
  
  
  /**
   * Import sql file
   * @param type $file 
   */
  public function import($file)
  {
		$path = osc_plugin_resource($file) ;
		$sql  = file_get_contents($path);

    if(! $this->dao->importSQL($sql) ){
        throw new Exception( "Error importSQL::TaggerModel<br>".$file ) ;
    }
  }
  
  /**
   *  Remove data and tables related to the plugin.
   */
  public function uninstall()
  {
  	foreach ( $this->model_tables as $table ) {  		
      $this->dao->query('DROP TABLE '. DB_TABLE_PREFIX . $table );
  	}
  }
  


  public function saveTags( $user_id = NULL , $object_id = NULL, $tags = NULL )
  {
    if ( is_null( $tags ) ) {
      return FALSE;
    }

    $tags = explode(',', $tags);
    
    foreach ($tags as $tag) {
      
      $this->freetag->tag_object($user_id, $object_id, $tag );

    }

    // return TRUE;

  }
  
  /**
   * get_tags_on_object
   *
   * You can use this function to show the tags on an object. Since it supports both user-specific
   * and general modes with the $tagger_id parameter, you can use it twice on a page to make it work
   * similar to upcoming.org and flickr, where the page displays your own tags differently than
   * other users' tags.
   *
   * @param int The unique ID of the object in question.
   * @param int The offset of tags to return.
   * @param int The size of the tagset to return. Use a zero size to get all tags.
   * @param int The unique ID of the person who tagged the object, if user-level tags only are preferred.
   *
   * @return array Returns a PHP array with object elements ordered by object ID. Each element is an associative
   * array with the following elements:
   *   - 'tag' => Normalized-form tag
   *   - 'raw_tag' => The raw-form tag
   *   - 'tagger_id' => The unique ID of the person who tagged the object with this tag.
   */ 
  public function getTags( $object_id )
  {
    $tags = $this->freetag->get_tags_on_object( $object_id, 0, 0, NULL );

    return $tags;
  }

  public function getAllTags()
  {
      $this->dao->select( 'tag' );
      $this->dao->from( sprintf("%st_tagger_tags", DB_TABLE_PREFIX ) );
            
      $result = $this->dao->get();
      
      if( ! $result ) {
          return array();
      }
      
      return $result->result();
  }


  public function removeAllTags( $user_id , $object_id  )
  {
      return $this->freetag->delete_all_object_tags_for_user( $user_id, $object_id );
  }
  
  
  // the fabulous way
  function array_pluck ($toPluck, $arr) {
      return array_map(function ($item) use ($toPluck) {
          return $item[$toPluck];
      }, $arr); 
  }

}