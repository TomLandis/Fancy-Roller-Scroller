<?php
/**
 * Plugin Name: Fancy Roller Scroller
 * Plugin URI: https://iwillmakeyour.website/fancy-roller-scroller
 * Description: add a rolling list to your page.
 * Author: Tom Landis
 * Version: 1.0.0
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
  wp_enqueue_script('fancy-settings.js', plugin_dir_url( __FILE__ ).'js/fancy-settings.js');
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
  wp_enqueue_style('frs.css', plugin_dir_url( __FILE__).'/css/frs.css');
  wp_enqueue_script('frs.js', plugin_dir_url( __FILE__ ).'js/frs.js');
	$thelist = get_option('ListOfStuff');
	$theNum = count($thelist);
	$formatedList = '';
	for($i=1;$i< $theNum;$i++) {
$formatedList = $formatedList.'<p class="frs-list-item" id="thing'.$i.'">'.$thelist[$i].'</p>';
	}
	return '
	<div class="outer frs-list-wrap">
	<h1 id="topText" class="topText frs-top-text">'.$thelist[0].'</h1>
	<div id="listOfThings">
	  <div id="inHere" class="listItem"> &nbsp; </div>
	 '.$formatedList.'
	</div>';
}
add_shortcode( 'fancy_roller_scroller', 'fancy_roller_scroller' );