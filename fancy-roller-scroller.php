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
	wp_enqueue_style( 'material', 'https://fonts.googleapis.com/icon?family=Material+Icons' );
	wp_enqueue_script('fancy-settings.js', plugin_dir_url( __FILE__ ).'js/fancy-settings.js');
	wp_enqueue_style('fancy-style.css', plugin_dir_url( __FILE__ ).'css/fancy-settings.css');
	settings_errors();
	echo '<h1 class="heading"><span class="painted">Fancy Roller Scroller Settings</span><i class="material-icons frs-iconic-head">format_paint</i></h1>';
	echo '<div class="frs-callout"><p id="success-mess"><i class="material-icons frs-iconic-suc">check_circle_outline</i>Your list has been updated.</p>';
	echo '<p class="intro"><i class="material-icons frs-iconic">people</i>Your list must have<strong> at least</strong> two items.</p>';
	echo '<p class="intro"><i class="material-icons frs-iconic">save</i> When you like the way things look, press save.</p>';
	echo '<p class="intro"><i class="material-icons frs-iconic">code</i>Put the shortcode <code>[fancy_roller_scoller]</code> where you want the list to appear.</p>';
	echo '<p class="intro"><i class="material-icons frs-iconic">palette</i>CSS classes to target are <code>.frs-list-item</code> and <code>.frs-top-text</code></p></div>';
	echo '<div class="frs-full-list"><h3><input class="frs-big-input" id="aboveText" value="'.$first.'"> &#11013; Text above rolling list</h3>';
	echo '<div id="list-wrap">';
	for($i=1;$i< $theNum;$i++){
    if($i>2){
      echo '<p><input class="frs-big-input" id="item-'.$i.'" value="'.$listo[$i].'"></input> <label for="item-'.$i.'">#'.$i.' Item</label><button id="'.$i.'" class="remover">X</button></p>';
    }else{
      echo '<p><input class="frs-big-input" id="item-'.$i.'" value="'.$listo[$i].'"></input> <label for="item-'.$i.'">#'.$i.' Item</p>';
    }
	
	}
	
	
	echo '</div></div>';
	echo '<button class="frs-add-item"  id="addItemButton"><i class="material-icons">playlist_add</i> &nbsp; Add Item</button><button class="frs-save-list" id="saveChanges"><i class="material-icons">save</i> &nbsp; SAVE</button>';
	
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