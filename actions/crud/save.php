<?php
/**
 * Create or edit an assembly
 *
 * @package Assemblies
 */

$variables = elgg_get_config('assembly');
$input = array();
foreach ($variables as $name => $type) {
	$input[$name] = get_input($name);
	if ($name == 'title') {
		$input[$name] = strip_tags($input[$name]);
	}
	if ($type == 'tags') {
		$input[$name] = string_to_tag_array($input[$name]);
	}
}

// Get guids
$assembly_guid = (int)get_input('guid');
$container_guid = (int)get_input('container_guid');

elgg_make_sticky_form('assembly');

/*if (!$input['title']) {
	register_error(elgg_echo('assemblies:assembly:error:no_title'));
	forward(REFERER);
}*/

if ($assembly_guid) {
	$assembly = get_entity($assembly_guid);
	if (!$assembly || !$assembly->canEdit()) {
		register_error(elgg_echo('assemblies:assembly:error:no_save'));
		forward(REFERER);
	}
	$new_assembly = false;
} else {
	$assembly = new ElggAssembly();
	$assembly->subtype = 'assembly';
	$new_assembly = true;
}

if (sizeof($input) > 0) {
	foreach ($input as $name => $value) {
		$assembly->$name = $value;
	}
}

// need to add check to make sure user can write to container
$assembly->container_guid = $container_guid;

if ($assembly->save()) {

	elgg_clear_sticky_form('assembly');

	system_message(elgg_echo('assemblies:assembly:saved'));

	if ($new_assembly) {
		add_to_river('river/object/crud/create', 'create', elgg_get_logged_in_user_guid(), $assembly->guid);
	}

	forward($assembly->getURL());
} else {
	register_error(elgg_echo('assemblies:assembly:error:no_save'));
	forward(REFERER);
}
