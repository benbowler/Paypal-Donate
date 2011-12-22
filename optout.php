<?php

/////////////////////////////////////////////////
////  Paypal Donations Module by Ben Bowler  ////
////               benbowler.com             ////
/////////////////////////////////////////////////
// USERS SHOULD ONLY NEED TO EDIT SETTINGS.PHP //
// which is included here:
require_once('settings.php');

require_once("../../config.php");
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/tag/lib.php');

$change = $_GET['change'];
$moodleuserid = $_GET['moodleuserid'];

execute_sql("UPDATE {$CFG->prefix}donators SET optout=$change WHERE moodleuserid=$moodleuserid");

echo "Your donations have been updated. <a href=\"{$CFG->dirroot}{$blockpath}\">Back to donations</a>";
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=index.php">';  

?>