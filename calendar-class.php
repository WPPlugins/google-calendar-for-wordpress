<?php

class GCalendar {

	const firstDayOptionName='WPGCalendar_firstDay'; 
	const feedURLOptionName='WPGCalendar_feedURL'; 
	const showCreditsOptionName='WPGCalendar_showCredits'; 

	var $firstDay; //Sunday=0, Monday=1, Tuesday=2, etc.
	var $feedURL;
	var $showCredits; //boolean

	function GCalendar() {
		$this->firstDay=0;
		$this->showCredits=true;
		$this->feedURL='';
	}

	function JavascriptOut() {
		echo '
		<script type=\'text/javascript\'>
			jQuery(document).ready(function() {
				var date = new Date();
				var d = date.getDate();
				var m = date.getMonth();
				var y = date.getFullYear();
					jQuery(\'#calendar\').fullCalendar({
						firstDay: '.$this->firstDay.' ,
						events: jQuery.fullCalendar.gcalFeed('."\"$this->feedURL\"".' )
					});
				});
		</script>
		';
	}

	function ReadSettingsFromDB() {
		$this->firstDay = get_option(firstDayOptionName);
		$this->feedURL = get_option(feedURLOptionName);
		$this->showCredits = get_option(showCreditsOptionName);
	}

	function WriteSettingsToDB() {
		update_option(firstDayOptionName,$this->firstDay);
		update_option(feedURLOptionName,$this->feedURL);
		update_option(showCreditsOptionName,$this->showCredits);
	}
}
?>