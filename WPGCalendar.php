<?php
/*
Plugin Name: WPGCalendar
Plugin URI: http://www.customcalendarmaker.com/google-calendar-wordpress/
Description: Google Calendar plugin for Wordpress
Version: 1.1
Author: Ruben Asensi
Author URI: http://www.customcalendarmaker.com
*/
include_once("calendar-class.php");
define("CALENDAR_TAG","WP-GCALENDAR");

if (!class_exists("WPGCalendar")) {
	class WPGCalendar {
		function WPGCalendar() {

		}
	}
} 

if (class_exists("WPGCalendar")) {
	$cal_plugin = new WPGCalendar();
}

//Actions and Filters	
if (isset($cal_plugin)) {
	register_activation_hook( __FILE__, Plugin_activation);
	add_action('admin_menu','Plugin_settings_menu');
	add_action('init','Enqueue_scripts');
	add_action('wp_head','Output_header_code');
	add_shortcode( CALENDAR_TAG , 'Filter_text');	
}

	function Plugin_settings_menu() {
	  add_options_page('WPGCalendar Options', 'WPGCalendar', 'manage_options', 'wpgcalendar-options', 'WPGCalendar_settings');
	}

	function Plugin_activation() {
		$cal = new GCalendar();
		$cal->WriteSettingsToDB();
	}

	function WPGCalendar_settings() {
		$cal = new GCalendar();
		$cal->ReadSettingsFromDB();
		if(isset($_POST['feedurl'])) {
			$cal->feedURL=$_POST['feedurl'];
			if ($_POST['group1']=='Sunday') {
				$cal->firstDay=0;
			} else {$cal->firstDay=1;}
			if(isset($_POST['credits'])) {$cal->showCredits=true;} else {$cal->showCredits=false;}
		}
		$cal->WriteSettingsToDB();
		if ($cal->firstDay==0) {
			$sundayCheck='checked';
			$mondayCheck='';
		} else {
			$sundayCheck='';
			$mondayCheck='checked';
		}
		if ($cal->showCredits) {$creditsCheck='checked';} else {$creditsCheck='';}
		echo '<form name="WPGCalendar-Options" action="'. get_permalink() .'" method="POST">
			<p>First day of the week is:<br />
				<input type="radio" name="group1" value="Sunday"'.$sundayCheck.'>Sunday<br />
				<input type="radio" name="group1" value="Monday"'.$mondayCheck.'>Monday<br />
				<br />Your Google Calendar feed url (<a href="http://www.customcalendarmaker.com/google-calendar-wordpress/">click here for help</a>):
				<input style="width:450px;" type="text" name="feedurl" value="'.$cal->feedURL.'"/><br />
				<br /><input type="checkbox" name="credits" value="showcredits" '.$creditsCheck.' />Link to WPGCalendar (not needed but appreciated)<br />
				<br /><input type="submit" value="Save Changes" />
			</p>
			</form>
		';
	}

	function Enqueue_scripts() {
		if (function_exists('wp_enqueue_script')) {
			wp_enqueue_script('fullcalendar', get_bloginfo('wpurl') . '/wp-content/plugins/WPGCalendar/js/fullcalendar.min.js', array('jquery'));
			wp_enqueue_script('fullcalendar-gcal', get_bloginfo('wpurl') . '/wp-content/plugins/WPGCalendar/js/gcal.js', array('jquery'));
		}
	}

	function Output_header_code() {
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/WPGCalendar/css/fullcalendar.css" />' . "\n";
			$cal = new GCalendar();
			$cal->ReadSettingsFromDB();
			$cal->JavascriptOut();
	}
	
	function Filter_text($att) {
		$cal = new GCalendar();
		$cal->ReadSettingsFromDB();
		if ($cal->showCredits) {
			$replacement='<div id=\'calendar\'></div><p style="font-size:9px;position:relative;bottom:-10px;">Powered by <a style="text-decoration:none;" href="http://www.customcalendarmaker.com/google-calendar-wordpress/">Google Calendar Plugin for Wordpress</a><br/><br/></p>';
		} else {
			$replacement='<div id=\'calendar\'></div><p style="position:relative">&nbsp;</p>';
		}
			return $replacement;
	}
?>