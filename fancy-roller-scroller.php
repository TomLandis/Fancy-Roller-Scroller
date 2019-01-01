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


/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1. */
function my_plugin_menu() {
	add_options_page( 'My Plugin Options', 'Fancy Roller Scroller', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
}

/** Step 3. */
function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<script type="text/javascript" src="'.plugin_dir_url( __FILE__ ).'js/fancy-settings.js"></script>';
	echo '<p>Your list must have<strong> at least</strong> two items. When you like the way things look, press save.</p>
	<button id="saveChanges">SAVE</button>';
	echo '<h3><input id="aboveText" value="things that hurt me"> &#11013; Text above rolling list</h3>';
	echo '<div id="list-wrap">';
	echo '<p><input id="item-1" value="sticks"></input> <label for="item-1">#1 Item</p>';
	echo '<p><input id="item-1" value="stones"></input> <label for="item-1">#2 Item</p>';
	echo '</div>';
	echo '<button id="addItemButton">Add Another item to the list</button>';
	
}

