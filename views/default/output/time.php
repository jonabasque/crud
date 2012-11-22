<?php

$value = $vars['value'];
if (is_numeric($value)) {
	$hour = floor($value/60);
	$minute = floor($value%60);
	echo sprintf("%02d:%02d", $hour, $minute);
}
