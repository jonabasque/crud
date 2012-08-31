<?php
/**
 * Edit crud form
 *
 * @package CRUD
 */

$crud = $vars['crud'];
$crud_type = $crud->crud_type;
$object = get_entity($vars['guid']);
$vars['entity'] = $object;

$container_guid = $vars['container_guid'];
$parent_guid = $vars['parent_guid'];

$fields = elgg_get_config($crud_type);

foreach ($fields as $name => $field) {
	
	if (!is_array($field)) {
		$type = $field;
		$default_value = "";
		$field = array();
	} else {
		$type = $field['input_type'];
		$default_value = $field['default_value'];
	}
	
	echo '<div>';
	if ($type != 'hidden') {
		echo "<label>" . elgg_echo("$crud->module:$crud->crud_type:$name") . "</label>";
		if ($type != 'longtext') {
			echo '<br />';
		}
	}
	echo elgg_view("input/$type", array_merge($field, array(
		'crud' => $crud,
		'name' => $name,
		'value' => $vars[$name] ? $vars[$name] : $default_value,
	)));
	echo '</div>';
}

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/crud/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete elgg-state-disabled float-alt'
	));
}

$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));
$action_buttons = $save_button . $delete_link;

// hidden inputs
$container_guid_input = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $container_guid));
if (!empty($parent_guid)) {
	$parent_guid_input = elgg_view('input/hidden', array('name' => 'parent_guid', 'value' => $parent_guid));
}
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));
$crud_input = elgg_view('input/hidden', array('name' => 'crud', 'value' => $crud_type));

echo <<<___HTML

<div class="elgg-foot">
	$guid_input
	$container_guid_input
	$parent_guid_input
	$crud_input

	$action_buttons
</div>

___HTML;
