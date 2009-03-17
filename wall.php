<?php

  /**
   * Elgg message board wall to wall page
   *
   * The wall to wall feature only shows the last ten messages between the two 
   * users (with the exception of the oddity of messages that they post on their
   * own board). There is no delete message button when on wall to wall. I haven't 
   * tried to add any additional security over the current messageboard and the 
   * current messageboard has little or no security. This means anyone can view 
   * the messages if they click on "reply" or "wall to wall" even if that person 
   * had made their messageboard private or restricted to a collection of friends. 
   *
   * @package ElggMessageBoard Walltowall Extension
   * @license GNU Lesser General Public License version 3
   * @author Cash Costello
   */

  global $CONFIG;   

  include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
  include_once $CONFIG->pluginspath . 'walltowall/lib/walltowall_annotations.php';

  // set context for special handling of comments for wall to wall  
  $prev_context = get_context();
  set_context('walltowall');


  $poster = get_input('poster'); // poster is the person who made the post
  $postee = get_input('postee'); // postee is the person the messageboard belongs to

  $users_array = array($poster, $postee);

  // pagination data
  $offset = get_input('offset', 0);
  $limit = get_input('limit', 10);
//  $num_msgs = count_annotations($users_array, "user", "", "messageboard", "", "", $users_array);
  $num_msgs = walltowall_count_annotations($users_array, "user", "", "messageboard", $users_array);
      
  // this is picking up posts by a user on his own board - no way to know whether these belong in the conversation
  $contents = get_annotations($users_array, "user", "", "messageboard", "", $users_array, $limit, $offset, "desc");
    
  $viewer = $_SESSION['guid'];
  
  // heading for wall to wall
  $poster_entity = get_entity($poster);
  $poster_name = $poster_entity->name;
  $postee_entity = get_entity($postee);
  $postee_name = $postee_entity->name;
  
  // sidebar creation - reusing owner block divs even though they are ids and content_... because there is so little to work with in the css
	$area1  = '<div id="content_area_user_title"><h2>' . elgg_echo('walltowall:title') . '</h2></div><br />';
  $icon   = elgg_view("profile/icon",array('entity' => $poster_entity, 'size' => 'small'));
  $area1 .= "<div id=\"owner_block_icon\">" . $icon . "</div>";
	$info   = '<a href="' . $poster_entity->getURL() . '">' . $poster_entity->name . '</a>';
	$area1 .= "<div id=\"owner_block_content\">" . $info . "</div>";  
	$icon   = elgg_view("profile/icon",array('entity' => $postee_entity, 'size' => 'small'));
  $area1 .= "<br /><div id=\"owner_block_icon\">" . $icon . "</div>";
	$info   = '<a href="' . $postee_entity->getURL() . '">' . $postee_entity->name . '</a>';
	$area1 .= "<div id=\"owner_block_content\">" . $info . "</div>";

  // special handling if viewer was part of conversation
  if ($viewer == $poster)
   $poster_name = elgg_echo('walltowall:me');
   
  if ($viewer == $postee)
   $postee_name = elgg_echo('walltowall:my');
  else
   $postee_name .= elgg_echo('walltowall:possessive'); 
  
  $area2 = elgg_view_title( sprintf(elgg_echo('walltowall:heading'), $postee_name, $poster_name) );

  // include section for adding new message if the viewer is poster or postee
  if ($offset == 0 && ($viewer == $poster || $viewer == $postee)) {

    $orig_page_owner = page_owner();
    
    // set page owner so any comment added here goes to the correct messageboard
    ( $viewer == $poster ) ? set_page_owner($postee) : set_page_owner($poster);

    $area2 .= elgg_view("walltowall/forms/add", array('poster' => $poster, 'postee' => $postee,) );
    
    // back to original page owner if needed downstream
    set_page_owner($orig_page_owner);
  }
    
  $area2 .= elgg_view("messageboard/messageboard", array('annotation' => $contents));
  
  
  set_context($prev_context);

        
  $nav = elgg_view('navigation/pagination', array(
                                                  'baseurl' => $_SERVER['REQUEST_URI'],
                                                  'offset' => $offset,
                                                  'count' => $num_msgs,
                                                  'limit' => $limit,
                                                  ) );
           
  $area2 .= $nav;
      
  $body = elgg_view_layout("two_column_left_sidebar", $area1, $area2);

    
  page_draw(elgg_echo('walltowall:title'), $body);    
?>
