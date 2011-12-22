<?php

/////////////////////////////////////////////////
////  Paypal Donations Module by Ben Bowler  ////
////               benbowler.com             ////
/////////////////////////////////////////////////
// All the settings you should ever need are in this file
// For all the support I can possibly provide email me: paypaldonate@benbowler.com

// This module has the same GPL Copyright as Moodle http://docs.moodle.org/en/License.

//////// Paypal Info
// Define the email address of your Paypal account.
// this prevents peoples donations acidently being registered on the wrong site.
$merchantid = 'email@example.com';
// Once you have created your donation button with Paypal (https://www.paypal.com/uk/cgi-bin/webscr?cmd=_button-management)
// enter all of the <form> code you are given here
$paypalbuttoncode = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>';
// NOTE: While setting up your button with make sure to set the success and cancel URL's to 'http://example.com/blocks/paypal_donate/'

//////// Donators Zone
// If you have a course for donators only put the enrolment code, course name (eg. Donators only Zone) and link to the course.
// This is simply how it's refered to on the page
$donatorszonename = 'Donators Zone';
// Set the enrolment key in the course settings
$donatorszonecode = 'XXXXXXXX'; // If you don't have a Donators Zone leave this blank (= '';)
// This is the url of your donators zone. Go to the zone and copy the part '/courses/view.php?id=X' below
$donatorszoneurl = $CFG->wwwroot.'/course/view.php?id=X';

//////// Anonymous Donations
// You can allow donations which don't appear in the site donations
$allowoptout = 'YES'; // 'YES' to enable opt outs, any other value disables it

//////// General Settings
// Set the title to be displayed in the breadcrumbs and page title
$donationpagetitle = 'Donations';
// If you set up a soft link to the block (eg. example.com/donations points to the block) set the path for links to use
$blockpath = '/blocks/paypal_donate/';
// NOTE: Remember to update the buttons success and cancell URL's with paypal to keep consistancy

?>