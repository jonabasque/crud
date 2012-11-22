<?php

$value = $vars['value'];
if (is_numeric($value)) {
	$hour = floor($value/60);
	$minute = floor($value%60);
} else {
	$hour = 0;
	$minute = 0;
}

$hours = array();
$minutes = array();

for ($h=0; $h<=23; $h++) {
	$hours[$h] = $h;
}

for($m=0; $m<60; $m+=5) {
	$mt = sprintf("%02d",$m);
	$minutes[$m] = $mt;
}

echo elgg_view('input/dropdown', array(
	'name' => $vars['name'] . '_hour',
	'value' => $hour,
	'options_values' => $hours,
));
echo " <b>:</b> ";
echo elgg_view('input/dropdown', array(
	'name' => $vars['name'] . '_minute',
	'value' => $minute,
	'options_values' => $minutes,
));
