<?php

  /**
   * Elgg Message board
   * This plugin allows users and groups to attach a message board to their profile for other users
   * to post comments and media.
   *
   * @todo allow users to attach media such as photos and videos as well as other resources such as bookmarked content
   * 
   * @package ElggMessageBoard
   * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
   * @author Curverider Ltd <info@elgg.com>
   * @copyright Curverider Ltd 2008
   * @link http://elgg.com/
   */

  /**
   * MessageBoard initialisation
   *
   * These parameters are required for the event API, but we won't use them:
   * 
   * @param unknown_type $event
   * @param unknown_type $object_type
   * @param unknown_type $object
   */
   
  function walltowall_init() {
        
    global $CONFIG;
        
    // Load the language file
    register_translations($CONFIG->pluginspath . "walltowall/languages/");                
  }
  
   
  register_action("walltowall/add", false, $CONFIG->pluginspath . "walltowall/actions/add.php");
    
  register_elgg_event_handler('init','system','walltowall_init');
?>
