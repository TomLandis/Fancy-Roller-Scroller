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
/** I 100% agree that the 'custom post type' route is a MUCH better approach. 
 *  I just want the quickest path to a working prototype so I starting building 
 * in a 'one list for the whole site' way.  I know this is far from idea. 
 *  My plan is to refactor once I have a working prototype. 
 *  If you think it's better to rip out what I've got so far that's totally cool too.  
 * I'm down for whatever approach you think is best.  Thanks so much for your help on this. 
 *  The ideas you've thrown out already have been super helpful and 
 * I'm very motiviated to complete this project now!  Thanks! */


add_action( 'wp_ajax_my_action', 'my_action' );

function my_action() {
	global $wpdb; // this is how you get access to the database

	$whatever =  $_POST['whatever'];

	update_option('ListOfStuff', $whatever);

        echo 'List updated!';

	wp_die(); // this is required to terminate immediately and return a proper response
}

/** Now I'll implement a shortcode with the scroller itself imbeded within */

//[fancy_roller_scroller]
function fancy_roller_scroller( $atts ){
	$thelist = get_option('ListOfStuff');
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
	<h1 id="topText" class="topText frs-top-text">' $thelist[0]; '</h1>
	<div id="listOfThings">
	  <div id="inHere" class="listItem"> &nbsp; </div>
	  <p class="frs-list-item" id="thing1">products</p>
	  <p class="frs-list-item" id="thing2">solutions</p>
	  <p class="frs-list-item" id="thing3">connections</p>
	   <p class="frs-list-item" id="thing4">designs</p>
	  <p class="frs-list-item" id="thing5">game changers</p>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript">
	var listo = document.getElementById("listOfThings");

var numOfItems = listo.children.length - 1;
let iter = 1;
function sliderOut() {
  $("#inHere").animate(
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
  $("#inHere").animate(
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


	</script>	
	';
}
add_shortcode( 'fancy_roller_scroller', 'fancy_roller_scroller' );