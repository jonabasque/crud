<?php

$crud = $vars['crud'];

$crud_variable = $crud->variables[$vars['name']];

$options_values = array();
foreach($crud_variable->options as $option) {
	$options_values[$option] = elgg_echo("$crud->module:$crud->crud_type:$option"); 
}
$vars['options_values'] = $options_values;

echo elgg_view('input/dropdown', $vars);

