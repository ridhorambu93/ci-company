<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

function timaAgo($timestamp) {
	$time_ago	= strtotime($timestamp);
	$time_now	= time();
	$time_diff	= $time_now - $time_ago;

	$minute		= round($time_diff / 60 );		// value 60 = detik
	$hour		= round($time_diff / 3600);		// value 3600 = 60 menit * 60 detik
	$day		= round($time_diff / 86400);	// value 86400 = 24 * 60 * 60;
	$week		= round($time_diff / 604800);	// value 604800 = 7 * 24 * 60 * 60;
	$month		= round($time_diff / 2629440);	// value 2629440 = ((365 + 365 + 365 + 365 + 366) / 5 / 12) * 24 * 60 * 60
	$year		= round($time_diff / 31553280);	// value 31553280 =(365 + 365 + 365 + 365 + 366) / 5 * 24 * 60 * 60

	if ($time_diff <= 60) {
		return 'Just now';
	} else if($minute <= 60) {
		return $minute . ' minute(s) ago';
	} else if($hour <= 24) {
		return $hour . ' hour(s) ago';
	} else if($day <= 7) {
		return $day . ' day(s) ago';
	} else if($week <= 4.3) { // value 4.3 == 52/12
		return $week . ' week(s) ago';
	} else if($month <= 12) {
		return $month . ' month(s) yang lalu';
	} else {
		return $year . ' year(s) yang lalu';
	}
}