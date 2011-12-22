<?php

/////////////////////////////////////////////////
////  Paypal Donations Module by Ben Bowler  ////
/////////////////////////////////////////////////
// USERS SHOULD ONLY NEED TO EDIT SETTINGS.PHP //
// which for some reason does not need to be included here

class block_paypal_donate extends block_base {
	function init() {
		$this->title   = 'Paypal Donate';//get_string('paypal_donate', 'block_paypal_donate');
		$this->version = 2008033000;
	}
	function hide_header() {
		return true;
	}
	function get_content() {
		if ($this->content !== NULL) {
			return $this->content;
		}
		require_once('settings.php');
		
		$this->content         =  new stdClass;
		$this->content->text   =  "$paypalbuttoncode";
		$this->content->footer = "<a href=\"{$CFG->dirroot}{$blockpath}\" alt=\"See {$SITE->fullname} Donations\">See all $SITE->fullname Donations</a>";
	
		return $this->content;
	}
}

?>