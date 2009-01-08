<?php

  /**
   * Elgg Message board individual item display page
   * 
   * @package ElggMessageBoard
   * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
   * @author Curverider Ltd <info@elgg.com>
   * @copyright Curverider Ltd 2008
   * @link http://elgg.com/
   */
   
?>

<div class="messageboard"><!-- start of messageboard div -->
  
    <!-- display the user icon of the user that posted the message -->
    <div class="message_sender">          
        <?php
            echo elgg_view("profile/icon",array('entity' => get_entity($vars['annotation']->owner_guid), 'size' => 'tiny'));
        ?>
    </div>
    
    <!-- display the user's name who posted and the date/time -->
    <p class="message_item_timestamp">
        <?php echo get_entity($vars['annotation']->owner_guid)->name . " " . friendly_time($vars['annotation']->time_created); ?>
    </p>
        
  <!-- output the actual comment -->
  <div class="message"><?php echo elgg_view("output/longtext",array("value" => parse_urls($vars['annotation']->value))); ?></div>
  <div class="message_buttons">
        
  
  <?php

      // walltowall: this section was rewritten for the wall to wall plugin
      // only display repy to and wall to wall links on profile/dashboard
      if (get_context() != "walltowall") {
               
        // if the user looking at the comment can edit, show the delete link
        if ($vars['annotation']->canEdit()) {
                
          echo "<div class='delete_message'>" . elgg_view("output/confirmlink",array(
                             'href' => $vars['url'] . "action/messageboard/delete?annotation_id=" . $vars['annotation']->id,
                             'text' => elgg_echo('delete'),
                             'confirm' => elgg_echo('deleteconfirm'),
                          )) . "</div>";
    
        } //end of can edit if statement


        $viewer = $_SESSION['guid'];
        $poster = $vars['annotation']->owner_guid;
        $postee = page_owner();  // owns the message board
        // this is a catch for when the page_owner is not set which happens when this file is loaded
        // by the ajax endpoint loader of the messageboard
        if ($postee == 0)
        {
          $postee = $_POST['pageOwner'];
        }  

        // if message is viewed by poster or on the board of the poster, don't show reply                
        if ($viewer != $poster && $poster != $postee) {
                    
          //get the message owner
          $poster_entity = get_entity($poster);
          //create the url to their messageboard
          $user_mb = "pg/messageboard/" . $poster_entity->username;
                    
          echo "<a href=\"" . $vars['url'] . $user_mb . "\">" . elgg_echo('walltowall:replyon') . " " . $poster_entity->name . "'s " . elgg_echo('walltowall:board') . "</a><br />";
                    
        }
        
        $postee_entity = get_entity($postee);
        // don't show wall to wall if viewer is poster and postee or this is a group messageboard      
        if ($poster != $postee && $postee_entity->getType() == 'user') {
                    
          echo "<a href=\"" . $vars['url'] . "mod/walltowall/wall.php?poster=" . $poster . "&postee=" . $postee . "\">" . elgg_echo('walltowall:walllink') . "</a>"; 

        }
      }                                           
                
  ?>
            
  </div>
  <div class="clearfloat"></div>
</div><!-- end of messageboard div -->
