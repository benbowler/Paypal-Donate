<?php

/////////////////////////////////////////////////
////  Paypal Donations Module by Ben Bowler  ////
////               benbowler.com             ////
/////////////////////////////////////////////////
// USERS SHOULD ONLY NEED TO EDIT SETTINGS.PHP //
// which is included here:
require_once('settings.php');

////////// Personal/Developer information
// Core layout eliments of this page are sourced from /user/view.php (The profile view)
////////// To do
// Put repeated elimenent into functions.
// Unify use of mysql_query() and execute_sql()
// Get settings.php editable in GUI
// Reconsider layout > class="userinfobox donationsbox" is good and redundant for the moment
// Handler for 'magical logout while at paypal' cases.
// Other notes (Search NOTE TO SELF)

//////////
// MOODLE CODE
// $Id: view.php,v 1.168.2.18 2008/07/05 14:53:32 skodak Exp $

//  Moodle header and nav code

    require_once("../../config.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/tag/lib.php');

    $id      = optional_param('id',     0,      PARAM_INT);   // user id
    $course  = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
    $enable  = optional_param('enable', '');                  // enable email
    $disable = optional_param('disable', '');                 // disable email

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }

    if (! $user = get_record("user", "id", $id) ) {
        error("No such user in this course");
    }

    if (! $course = get_record("course", "id", $course) ) {
        error("No such course id");
    }

    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $coursecontext));


    $navlinks[] = array('name' => $donationpagetitle, 'link' => null, 'type' => 'misc');

    $navigation = build_navigation($navlinks);

    print_header("$course->fullname: $donationpagetitle: $fullname", $course->fullname,
                 $navigation, "", "", true, "&nbsp;", navmenu($course));
    
    echo '<table width="100%" class="userinfobox donationsbox" summary="">';
    echo '<tr>';
    echo '<td class="content">';
// MOODLE CODE
//////////

$business = $_GET['business'];
$status = $_GET['payment_status'];

// Get the Logged in Moodle users information
$moodleuserid = $user->id;
$moodlename = "$user->firstname $user->lastname";

// Hide the multiple succes displays
echo "<style>.notifysuccess {display:none}</style>";

// Create the table if it doesn’t exist already
$checkfortable = mysql_query("SELECT * FROM {$CFG->prefix}donators LIMIT 0,1");

if (!$checkfortable){
// No table present so try to create it
	execute_sql("CREATE TABLE `$CFG->dbname`.`{$CFG->prefix}donators` (
			`id` int( 9 ) unsigned NOT NULL AUTO_INCREMENT ,
			`date` timestamp NOT NULL default CURRENT_TIMESTAMP ,
			`donation` decimal( 10, 2 ) NOT NULL ,
			`currency` varchar( 3 ) NOT NULL ,
			`moodleuserid` int( 9 ) NOT NULL ,
			`paypalname` varchar( 100 ) NOT NULL ,
			`paypalemail` varchar( 100 ) NOT NULL ,
			`optout` int( 1 ) NOT NULL ,
			PRIMARY KEY ( `id` )
			) ENGINE = MYISAM DEFAULT CHARSET = utf8;");
	echo 'Donators Table Created';
}

// Check for absence of Paypals GET data
if(!isset($_GET['mc_gross'])){
	// If your not from paypal (No GET information) have you donated before? > NOTE TO SELF: STREAMLINE AND STANDARDISE THE MYSQL
	
	if(mysql_num_rows(mysql_query("SELECT moodleuserid FROM {$CFG->prefix}donators WHERE moodleuserid='$moodleuserid'"))){
	// Here the personal donations are displayed
		$selectpersonallist = mysql_query("SELECT moodleuserid,date,donation,currency FROM {$CFG->prefix}donators WHERE moodleuserid=$moodleuserid");
		echo "<h2>Personal Donations</h2><table border='1'><tr><th>Date</th><th>Donation</th></tr>";
		while($personalrow = mysql_fetch_array($selectpersonallist)){
			$personaldonation = $personalrow['donation'];
			$personalcurrency = $personalrow['currency'];
			$date = $personalrow['date'];
			echo "<tr><td>$date</td><td>$personaldonation $personalcurrency</td></tr>";
		}
		echo '</table>';
		
		if($allowoptout=='YES'){

			$optoutvalue = mysql_query("SELECT optout FROM {$CFG->prefix}donators WHERE moodleuserid=$moodleuserid LIMIT 0,1");

			while($optoutrow = mysql_fetch_array($optoutvalue)){
				$optoutint = $optoutrow['optout'];
					if($optoutint==0){
						$optoutname = 'Make Donations Anonymous';
						$optoutmessage = 'Your donations are currently shown publicly.';
						$change = 1;
					} else {
						$optoutname = 'Make Donations Public';
						$optoutmessage = 'Your donations are currently not shown publicly.';
						$change = 0;
					}
			}
			echo "<form action=\"optout.php\" method=\"get\"><input type=\"hidden\" name=\"change\" value=\"$change\" /><input type=\"hidden\" name=\"moodleuserid\" value=\"$moodleuserid\" /> $optoutmessage <input type=\"submit\" value=\"$optoutname\" /></form><br />";
				
		}

		if(isset($donatorszonecode)){
			echo "<strong>As a Donator you get access to the $donatorszonename</strong><br />Access the <a href=\"$donatorszoneurl\" alt=\"$donatorszonename\">$donatorszonename here</a> and enter the code <strong>$donatorszonecode</strong><br />";
		}

		echo "If you are have any problems with the donation system please email <a href=\"mailto: $CFG->supportemail\" alt=\"Support email\">$CFG->supportemail</a>.<br />";	
		
	} else {
	// Donate button showed
		echo "$paypalbuttoncode";
	}
	
	// List of donators
	$selectdonators = mysql_query("SELECT moodleuserid,date,donation,currency,optout FROM {$CFG->prefix}donators");

	echo "<h2>$SITE->fullname Donations</h2><table border='1'><tr><th>Full Name</th><th>Date</th><th>Donation</th></tr>";

	while($donatorrow = mysql_fetch_array($selectdonators)){
	
		$selectedid = $donatorrow['moodleuserid'];
		
		$donatoroptout = $donatorrow['optout'];
		if($donatoroptout==0){
		
		$selectuser = mysql_query("SELECT id,firstname,lastname FROM {$CFG->prefix}user WHERE id=$selectedid");
		
		while($userrow = mysql_fetch_array($selectuser)){
			$userfirstname = $userrow['firstname'];
			$userlastname = $userrow['lastname'];
			echo "<tr><td>$userfirstname $userlastname</td>";
		}
		
		$donatordonation = $donatorrow['donation'];
		$donatorcurrency = $donatorrow['currency'];
		$date = $donatorrow['date'];
		echo "<td>$date</td><td>$donatordonation $donatorcurrency</td></tr>";
		}
	}
	
	echo "</table>";
	
} else {

	// Check for mismatched merchantid's > Added protection to stop peoples donations being registered on the wrong site.
	// Or the payment is not Complete > NOT TO SELF: Add more specific errors
	if($merchantid!=$business || $status!='Completed'){
	
		echo "There has been a problem with your donation. Either your payment has not been confirmed or your donation may have been ment for another site.<br />If you believe there is a problem with $SITE->fullname email <a href=\"mailto: $CFG->supportemail\" alt=\"Support email\">$CFG->supportemail</a>.";
	
	} else {

		// Variablise all of the useful GET data from Paypal incase we might need it
		$donation = $_GET['mc_gross'];
		$currency = $_GET['mc_currency'];

		$date = $_GET['payment_date'];

		$paypalfirstname = $_GET['first_name'];
		$paypallastname = $_GET['last_name'];
		$paypalname = "$paypalfirstname $paypallastname";

		$payer = $_GET['payer_email'];

		// Do the database stuff > NOTE TO SELF: ADD MORE MYSQL FAILURES
		// Write in the donator
		execute_sql("INSERT INTO {$CFG->prefix}donators (donation, currency, moodleuserid, paypalname, paypalemail, optout) VALUES ('$donation', '$currency', '$moodleuserid', '$paypalname', '$payer', '0')");

		// Show the user the information

		echo "Thank you $moodlename for your donation to $SITE->fullname <br />We have received your donation of $donation $currency from the paypal account $payer <br />";

		if($allowoptout=='YES'){

			$optoutvalue = mysql_query("SELECT optout FROM {$CFG->prefix}donators WHERE moodleuserid=$moodleuserid LIMIT 0,1");

			while($optoutrow = mysql_fetch_array($optoutvalue)){
				$optoutint = $optoutrow['optout'];
					if($optoutint==0){
						$optoutname = 'Make Donations Anonymous';
						$optoutmessage = 'Your donations are currently shown publicly.';
						$change = 1;
					} else {
						$optoutname = 'Make Donations Public';
						$optoutmessage = 'Your donations are currently not shown publicly.';
						$change = 0;
					}
			}
			echo "<form action=\"optout.php\" method=\"get\"><input type=\"hidden\" name=\"change\" value=\"$change\" /><input type=\"hidden\" name=\"moodleuserid\" value=\"$moodleuserid\" /> $optoutmessage <input type=\"submit\" value=\"$optoutname\" /></form><br />";
				
		}

		if(isset($donatorszonecode)){
			echo "<strong>As a Donator you get access to the $donatorszonename</strong><br />Access the <a href=\"$donatorszoneurl\" alt=\"$donatorszonename\">$donatorszonename here</a> and enter the code <strong>$donatorszonecode</strong><br />";
		}

		echo "You may return here at anytime to review your donation information.<br />If you are have any problems with the donation system please email <a href=\"mailto: $CFG->supportemail\" alt=\"Support email\">$CFG->supportemail</a>.";

	}

}

//////////
// MOODLE CODE

    echo "</td></tr></table>";

    print_footer($course);

/// Functions /////// NOTE TO SELF: Do I need to keep this

function print_row($left, $right) {
    echo "\n<tr><td class=\"label c0\">$left</td><td class=\"info c1\">$right</td></tr>\n";
}

?>