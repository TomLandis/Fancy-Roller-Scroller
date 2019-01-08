<?php
/**
 * Plugin Name: Fancy Roller Scroller
 * Plugin URI: https://iwillmakeyour.website/fancy-roller-scroller
 * Description: add a rolling list to your page.
 * Author: Tom Landis
 * Version: 0.0.1
 * Author URI: https://iwillmakeyour.website
 * License: GPLv2 or later
 * Text Domain: fancy-roller-scroller
 *

 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** Create Menu */
add_action( 'admin_menu', 'fancy_roller_scroller_menu' );


function fancy_roller_scroller_menu() {
	add_options_page( 'My Plugin Options', 'Fancy Roller Scroller', 'manage_options', 'fancy-roller-scroller-setup', 'fancy_roller_scroller_options' );
}
/** Save Initial values of list into database */
function activateFancyRoller(){
	add_option( 'ListOfStuff', ['Gimmie', 'some truth', 'a break']);
	update_option('ListOfStuff', ['Gimmie', 'some truth', 'a break']);
}

register_activation_hook( __FILE__, 'activateFancyRoller');
/** pull details from DB */
function fancy_roller_scroller_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$listo = get_option('ListOfStuff');
	$theNum = count($listo);
	$first = $listo[0];
	echo '<script type="text/javascript" src="'.plugin_dir_url( __FILE__ ).'js/fancy-settings.js"></script>';
  echo '<p class="into" style="font-size:1.6em;color:#003489;">Your list must have<strong> at least</strong> two items. When you like the way things look, press save.</p>
	';
	echo '<h3><input style="font-size:1.5em;" id="aboveText" value="'.$first.'"> &#11013; Text above rolling list</h3>';
	echo '<div id="list-wrap">';
	for($i=1;$i< $theNum;$i++){
    if($i>3){
      echo '<p><input style="font-size:1.5em;" id="item-'.$i.'" value="'.$listo[$i].'"></input> <label for="item-'.$i.'">#'.$i.' Item<button id="'.$i.'" class="remover">X</button></p>';
    }else{
      echo '<p><input style="font-size:1.5em;" id="item-'.$i.'" value="'.$listo[$i].'"></input> <label for="item-'.$i.'">#'.$i.' Item</p>';
    }
		
	}
	
	
	echo '</div>';
	echo '<button style="font-size:1.6em; background-color:#296d51; color:white; padding: 12px; margin: 5px; border-radius:4px;"  id="addItemButton">Add Another item to the list</button><button style="font-size:1.6em; background-color:#003489; color:white; padding: 12px; margin: 5px; border-radius:4px;" id="saveChanges">SAVE</button>';
	
}
/** The next step is to make the save button write the options to the database */
/** I 100% agree that the 'custom post type' route is a MUCH better approach. 
 *  I just want the quickest path to a working prototype so I starting building 
 * in a 'one list for the whole site' way.  I know this is far from idea. 
 *  My plan is to refactor once I have a working prototype. 
 *  If you think it's better to rip out what I've got so far that's totally cool too.  
 * I'm down for whatever approach you think is best.  Thanks so much for your help on this. 
 *  The ideas you've thrown out already have been super helpful and 
 * I'm very motiviated to complete this project now!  Thanks! */


add_action( 'wp_ajax_fancy_roll_scroll_update', 'fancy_roll_scroll_update' );

function fancy_roll_scroll_update() {
	global $wpdb; // this is how you get access to the database

	$frs_new_list =  $_POST['frs_new_list'];
/**Time to Sanitize the data.  For safety, for security, for the republic. */
$white_listed_list = [];
$frs_list_len = count($frs_new_list);
for($i=0;$i<$frs_list_len;$i++){
  $safe = sanitize_text_field($frs_new_list[$i]);
$white_listed_list[] = $safe;
}
if(current_user_can('edit_pages')){
  update_option('ListOfStuff', $white_listed_list);

  echo 'List updated!';

wp_die(); // this is required to terminate immediately and return a proper response
}else{

  echo 'You lack permission to edit pages, contact your administrator.';

}  

}

/** Now I'll implement a shortcode with the scroller itself imbeded within */

//[fancy_roller_scroller]
function fancy_roller_scroller( $atts ){
	wp_enqueue_script('jquery');
	$thelist = get_option('ListOfStuff');
	$theNum = count($thelist);
	$formatedList = '';
	for($i=1;$i< $theNum;$i++) {
$formatedList = $formatedList.'<p class="frs-list-item" id="thing'.$i.'">'.$thelist[$i].'</p>';
	}
	return '<style>
.outer,
#listOfThings p {
  text-align: center;
}
.outer h1 {
  padding-top: 100px;
}
#inHere {
  position: relative;
  opacity: 0;
  top: 50px;
  font-size: 200%;
}
#listOfThings {
  position: relative;
}
#listOfThings p {
  margin: 0 !important;
  padding: 0;
  position: absolute;
  font-size: 1em;
  margin-bottom: -2em;
  opacity: 0;
}
.outer{
}
	</style>
	
	<div class="outer frs-list-wrap">
	<h1 id="topText" class="topText frs-top-text">'.$thelist[0].'</h1>
	<div id="listOfThings">
	  <div id="inHere" class="listItem"> &nbsp; </div>
	 '.$formatedList.'
	</div>
	<script type="text/javascript">
	window.onload = function() {
	var listo = document.getElementById("listOfThings");

var numOfItems = listo.children.length - 1;
let iter = 1;


	
function sliderOut() {
  jQuery("#inHere").animate(
    {
      opacity: 0,
      top: "-50px"
    },
    250,
    function() {
      document.getElementById("inHere").style.top = "50px";
      setTimeout(function() {changer();}, 10);
    }
  );
}
function sliderUp() {
  jQuery("#inHere").animate(
    {
      opacity: 1,
      top: "0px"
    },
    250,
    function() {
    }
  );
}
function changer() {
 
  let target = "thing" + iter;
  let targ = document.getElementById(target);
  document.getElementById("inHere").innerHTML = targ.innerHTML;

  sliderUp();

  setTimeout(function() {
    sliderOut();
    if (iter == numOfItems) {
      iter = 1;
    } else {
      iter++;
    }
  }, 1400);
}

changer();
}
</script>	
	';
}
add_shortcode( 'fancy_roller_scroller', 'fancy_roller_scroller' );