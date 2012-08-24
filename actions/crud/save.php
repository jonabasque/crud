<?php
/**
 * Create or edit a crud object
 *
 * @package Assemblies
 */

$crud_type = get_input('crud');
$crud = crud_get_handler($crud_type);

$msg_prefix = $crud->module.":$crud_type";

$variables = elgg_get_config($crud_type);
$input = array();
foreach ($variables as $name => $type) {
	$input[$name] = get_input($name);
	if ($name == 'title') {
		$input[$name] = strip_tags($input[$name]);
	}
	if ($type == 'tags') {
		$input[$name] = string_to_tag_array($input[$name]);
	}
	if ($type == 'date') {
		$input[$name] = strtotime($input[$name]." ".date_default_timezone_get());
	}
}

// Get guids
$entity_guid = (int)get_input('guid');
$container_guid = (int)get_input('container_guid');
$parent_guid = (int)get_input('parent_guid');

elgg_make_sticky_form($crud_type);

/*if (!$input['title']) {
	register_error(elgg_echo($msg_prefix.':error:no_title'));
	forward(REFERER);
}*/

if ($entity_guid) {
	$entity = get_entity($entity_guid);
	if (!$entity || !$entity->canEdit()) {
		register_error(elgg_echo($msg_prefix.':error:no_save'));
		forward(REFERER);
	}
	$new_entity = false;
} else {
	$entity = new ElggObject();
	$entity->subtype = $crud_type;
	$new_entity = true;
}

if (sizeof($input) > 0) {
	foreach ($input as $name => $value) {
		$entity->$name = $value;
	}
}

// set parent if set
if (!empty($parent_guid)) {
	$entity->parent_guid = $parent_guid;
}

// need to add check to make sure user can write to container
$entity->container_guid = $container_guid;

if ($entity->save()) {

	elgg_clear_sticky_form($crud_type);

	system_message(elgg_echo($msg_prefix.':saved'));

	if ($new_entity) {
		add_to_river('river/object/crud/create', 'create', elgg_get_logged_in_user_guid(), $entity->guid);
	}

	forward($entity->getURL());
} else {
	register_error(elgg_echo($msg_prefix.':error:no_save'));
	forward(REFERER);
}
