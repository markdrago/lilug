<?php

$wgExtensionFunctions[] = "register_lilug";

#register the extension with mediawiki
function register_lilug() {
    global $wgParser;

    #register the function which will handle these hooks
    $wgParser->setHook("meetingdatetime", "render_meetingdatetime");
    $wgParser->setHook("nextmeetingdesc", "render_nextmeetingdesc");
}

#function that is called by mediawiki when someone uses the
#<meetingdatetime> tag.  It takes the input from the tag and
#returns the text that should replace the tag.
function render_meetingdatetime($input, $argv) {
    global $wgTitle;

    #disable cache for pages using this tag
    wfPurgeSquidServers(array($wgTitle->getInternalURL()));
    $wgTitle->invalidateCache();

    #the 'group' attribute is required
    if (!isset($argv["group"])) {
	return "ERROR: A 'group' attribute is needed with 'meetingdatetime'";
    }

    #zeros mean use the current month & year
    $month = 0;
    $year = 0;

    if (isset($argv["month"])) {
	$month = $argv["month"];
    }

    if (isset($argv["year"])) {
	$year = $argv["year"];
    }

    $meeting_time = get_groups_meeting($argv["group"], $month, $year);

    return get_nice_text_for_time($meeting_time);

}

function render_nextmeetingdesc($input, $argv) {
    global $wgParser;
    global $wgTitle;

    #disable cache for pages using this tag
    wfPurgeSquidServers(array($wgTitle->getInternalURL()));
    $wgTitle->invalidateCache();

    #the 'group' attribute is required
    if (!isset($argv["group"])) {
	return "ERROR: A 'group' attribute is needed with 'meetingdatetime'";
    }

    #decide if we're making a link or if we're including a page
    $begin_tags = "";
    $end_tags = "";

    $is_link = false;
    if ($argv["type"] == "link") {
	$is_link = true;
	$begin_tags = "[[";
	$end_tags = "]]";
    } elseif ($argv["type"] == "include") {
	$begin_tags = "{{";
	$end_tags = "}}";
    }

    #figure out the month and year of the meeting
    $meeting_time = get_groups_meeting($argv["group"], 0, 0);
    $month = date("m", $meeting_time);
    $year = date("Y", $meeting_time);

    $group = ucfirst($argv["group"]);

    $string = $begin_tags . "Template:$group Meeting $year $month";
    
    #potentially add link title
    if (is_link == true) {
	if ((isset($input)) && ($input != "")) {
	    $string .= "|$input";
	} else {
	    $string .= "|$group Meeting $year $month";
	}
    }

    $string .= $end_tags;

    return $wgParser->recursiveTagParse($string);
}

#Get the meeting time for different groups depending on when their meetings
#are held.  This returns a unix timestamp representing the time that the
#meeting starts.
function get_groups_meeting($group, $month, $year) {

    switch ($group) {
    case "lilugirc":
	if (($month != 0) or ($year != 0)) {
	    print "ERROR: 'lilugirc' will only get the next meeting time";
	    return 0;
	}
	$meeting_time = lilug_irc_meeting_time();
	break;
    case "lilugsisig":
	$meeting_time = monthly_meeting_time(-1, 4, 20, 0, $month, $year);
	break;
    case "lilugdevsig":
	$meeting_time = monthly_meeting_time(3, 4, 20, 0, $month, $year);
	break;
    default:
    case "lilug":
	$meeting_time = monthly_meeting_time(2, 2, 20, 0, $month, $year);
	break;
    }

    return $meeting_time;
}

#A special function which determines when the next irc meeting is.  It is
#special because irc meetings follow a special schedule in that they're every
#Tuesday night, except for nights when we have a regular meeting.  It currently
#only supports getting the datetime of the very next meeting.  It returns that
#time as a unix timestamp.
function lilug_irc_meeting_time() {
    #general constants used
    $length_of_week = 60 * 60 * 24 * 7;

    #constants that define the meeting time
    $day_of_meeting = 2;     # tuesdays
    $hour_of_meeting = 20;   # 8pm
    $minutes_of_meeting = 0; # 8:_00_ pm

    $meeting_time = next_weekly_meeting_time(time(),
					     $day_of_meeting,
					     $hour_of_meeting,
					     $minutes_of_meeting);

    #check for conflict with regular meeting
    $meeting_date = intval(date("d", $meeting_time));
    $meeting_month = intval(date("m", $meeting_time));
    $meeting_year = intval(date("Y", $meeting_time));

    #constants that define the day of conflict (regular lilug meetings)
    $reg_week_of_meeting = 2;    #_second_ tuesday
    $reg_day_of_meeting = 2;     # tuesday
    $reg_hour_of_meeting = 20;   # 8pm
    $reg_minutes_of_meeting = 0; # 8:_00_ pm

    $regular_meeting = monthly_meeting_time($reg_week_of_meeting,
					    $reg_day_of_meeting,
					    $reg_hour_of_meeting,
					    $reg_minutes_of_meeting);

    $reg_meeting_date = intval(date("d", $regular_meeting));
    $reg_meeting_month = intval(date("m", $regular_meeting));
    $reg_meeting_year = intval(date("Y", $regular_meeting));

    if (($meeting_date == $reg_meeting_date) &&
	($meeting_month == $reg_meeting_month) &&
	($meeting_year == $reg_meeting_year)) {
      #skip to the next week in this case
      $meeting_time = next_weekly_meeting_time(time() + $length_of_week,
					       $day_of_meeting,
					       $hour_of_meeting,
					       $minutes_of_meeting);

    }

    return $meeting_time;
}

#This function figures out the datetime of a monthly meeting given the
#number of the week, the day of the week, the hours and the minutes that a
#meeting is held.  It will get the time of the meeting in the current month
#if the month and year parameters are zeros.  Otherwise, it will get the
#time of the meeting using the given month and year.  This function can also
#calculate the time of a meeting whose schedule is relative to the end of
#the month (ie the _last_ thursday of the month) if a negative number is passed
#in for the number of the week.  It's pretty sweet.
function time_of_monthly_meeting($week_of_meeting, $day_of_meeting,
				 $hour_of_meeting, $minutes_of_meeting,
				 $month = 0, $year = 0) {
    global $wgOut;

    #use current month if zero is passed in
    if ($month == 0) {
	$month = intval(date("m"));
    }

    #use current year if zero is passed in
    if ($year == 0) {
	$year = intval(date("Y"));
    }

    #figure out which direction we'll be moving in
    if ($week_of_meeting > 0) {
	$direction = 1;
    } else {
	$direction = -1;
    }

    #get the starting day (first or last of month)
    if ($direction > 0) {
	$starting_date = 1;
    } else {
        #to figure out the number of days in the month, we need a datetime
        #that exists in that month, so we use the first of the month
	$first_of_month = mktime(0, 0, 0, $month, 1, $year);
	$starting_date = intval(date("t", $first_of_month));
    }

    #get the unix time of the day we'll be starting from
    $starting_day = mktime(0, 0, 0, $month, $starting_date, $year);

    #get the day of the week of the starting_day
    $day_of_starting_day = intval(date("w", $starting_day));

    #find the distance to the meeting day_of_the_week
    if ($day_of_starting_day == $day_of_meeting) {
	$days_until_next_day = 0;
    } elseif (($direction * $day_of_starting_day) < 
	      ($direction * $day_of_meeting)) {
	$days_until_next_day = $day_of_meeting - $day_of_starting_day;
    } else {
	$days_until_next_day = ((7 * $direction) -
				($day_of_starting_day - $day_of_meeting));
    }

    #move to the correct week
    $date_of_meeting = ((($week_of_meeting - $direction) * 7) +
			($starting_date + $days_until_next_day));

    #exact time of next meeting
    $exact_time = mktime($hour_of_meeting, $minutes_of_meeting, 0, $month,
			 $date_of_meeting, $year);

    return $exact_time;
}

#This function wraps time_of_monthly_meeting and handles the instance that
#this month's meeting has already passed.  When this happens this function
#handles figuring out the time of the meeting next month.  So, if a meeting
#was scheduled for May 16th and it is May 20th, this function is the one
#that decides that we should return the time for the meeting in June.
function monthly_meeting_time($week_of_meeting, $day_of_meeting,
			      $hour_of_meeting, $minutes_of_meeting, $month=0,
			      $year=0)
{
    #estimated constant length of meetings
    $meeting_length = 60 * 60 * 4;  # 4 hours

    #test to see if we want to know when the next meeting is
    $next_meeting = true;
    if (($month != 0) or ($year != 0)) {
	$next_meeting = false;
    }
	
    $meeting_time = time_of_monthly_meeting($week_of_meeting, $day_of_meeting,
					    $hour_of_meeting,
					    $minutes_of_meeting, $month,
					    $year);

    #if we're not trying to figure out the very next meeting we
    #just return the time we got already
    if ($next_meeting == false) {
	return $meeting_time;
    }

    #check to see if this meeting time has passed already
    if ($meeting_time < (time() - $meeting_length)) {
	$meeting_month = intval(date("m", $meeting_time));

	if ($meeting_month == 12) {
	    $month = 1;
	    $this_year = date("Y", time());
            $year=++$this_year;

	} else {
	    $month = $meeting_month + 1;
	}

	$meeting_time = time_of_monthly_meeting($week_of_meeting,
						$day_of_meeting,
						$hour_of_meeting,
						$minutes_of_meeting, $month,
						$year);
    }

    return $meeting_time;
}

#This function returns the datetime of a meeting that is held weekly.  It only
#handles figuring out the next weekly meeting.  It returns the datetime as a
#unix timestamp.
function next_weekly_meeting_time($starting_time, $day_of_meeting,
				  $hour_of_meeting, $minutes_of_meeting)
{
    #estimated constant length of meetings
    $length_of_day = 60 * 60 * 24;

    $current_day = intval(date("w", $starting_time));
    $current_date = intval(date("d", $starting_time));
    $current_month = intval(date("m", $starting_time));
    $current_year = intval(date("Y", $starting_time));

    if ($current_day == $day_of_meeting) {
	$days_until_meeting = 0;
    } elseif ($current_day > $day_of_meeting) {
	$days_until_meeting = 7 - ($current_day - $day_of_meeting);
    } else {
	$days_until_meeting = $day_of_meeting - $current_day;
    }

    $today = mktime($hour_of_meeting, $minutes_of_meeting, 0, $current_month,
		    $current_date, $current_year);

    $meeting_time = $today + ($length_of_day * $days_until_meeting);

    return $meeting_time;
}

#This function takes a unix datetime and converts it to a nice friendly string
#depending on what day today is.  So, if the datetime won't happen for the
#a while it returns a string like, "Tuesday, March 16th @ 8:00pm", but if the
#meeting is tomorrow it will return, "Tomorrow @ 8:00pm" and if the meeting is
#tonight it will return, "Tonight @ 8:00pm".
function get_nice_text_for_time($meeting_time) {
    $today = getdate();
    $meeting = getdate($meeting_time);
  
    $text = "";
    if (($today["year"] == $meeting["year"]) &&
	($today["month"] == $meeting["month"])) {

	if ($today["mday"] == $meeting["mday"]) {
	    if ($meeting["hours"] >= 18) {
		$text .= "Tonight, ";
	    } else {
		$text .= "Today, ";
	    }
	} elseif (($today["mday"] + 1) == $meeting["mday"]) {
	    $text .= "Tomorrow, ";
	} else {
	    $text .= date("l, ", $meeting_time);
        }
    }

    $text .= date("F jS @ g:ia", $meeting_time);

    return $text;
}

?>
