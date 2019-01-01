<?php
/**
 * Plugin Name: Fancy Roller Scroller
 * Plugin URI: https://iwillmakeyour.website/fancy-roller-scroller
 * Description: add a rolling list to your page.
 * Author: Tom Landis
 * Version: 0.0.1
 * Author URI: https://iwillmakeyour.website
 * Text Domain: fancy-roller-scroller
 *

 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** Create Menu */
add_action( 'admin_menu', 'my_plugin_menu' );


function my_plugin_menu() {
	add_options_page( 'My Plugin Options', 'Fancy Roller Scroller', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
}
/** Save Initial values of list into database */
function activateFancyRoller(){
	add_option( 'ListOfStuff', ['Take a look at', 'this', 'that', 'all the stuff']);
}

register_activation_hook( __FILE__, 'activateFancyRoller');
/** pull details from DB */
function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$listo = get_option('ListOfStuff');
	$theNum = count($listo);
	$first = $listo[0];
	echo '<script type="text/javascript" src="'.plugin_dir_url( __FILE__ ).'js/fancy-settings.js"></script>';
	echo '<p>Your list must have<strong> at least</strong> two items. When you like the way things look, press save.</p>
	<button id="saveChanges">SAVE</button>';
	echo '<h3><input id="aboveText" value="'.$first.'"> &#11013; Text above rolling list</h3>';
	echo '<div id="list-wrap">';
	for($i=1;$i< $theNum;$i++){
		echo '<p><input id="item-'.$i.'" value="'.$listo[$i].'"></input> <label for="item-'.$i.'">#'.$i.' Item</p>';
	}
	
	
	echo '</div>';
	echo '<button id="addItemButton">Add Another item to the list</button>';
	
}
/** The next step is to make the save button write the options to the database */
