<?php
//Returns a Catalan approximate representation of the elapsed time since a date
function relative_time($date){
        $ago = (int)(time() - date('U', $date));

	if ($ago==1){
		$ago = lang('date.second_ago');
	}
	else if ($ago<60){
		$ago = sprintf(lang('date.seconds_ago'), $ago);
	}
	else if ($ago<3600){
		$ago = (int)($ago/60);
		if ($ago==1){
                        $ago = lang('date.minute_ago');
		}
		else{
			$ago = sprintf(lang('date.minutes_ago'), $ago);
		}
        }
	else if ($ago<86400){
		$ago = (int)($ago/3600);
		if ($ago==1){
                        $ago = lang('date.hour_ago');
		}
		else{
			$ago = sprintf(lang('date.hours_ago'), $ago);
		}
        }
	else if ($ago<2678400){
		$ago = (int)($ago/86400);
		if ($ago==1){
                        $ago = lang('date.day_ago');
		}
		else{
			$ago = sprintf(lang('date.days_ago'), $ago);
		}
        }
	else{
		$ago = date(lang('date.long_format'), $date);
	}
	return $ago;
}
?>
