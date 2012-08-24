<?php
/**
 * Crud
 *
 * @package CRUD
 *
 */

elgg_register_event_handler('init', 'system', 'crud_init');

global $CRUD_HANDLERS;

/**
 * Format and return the URL for crud object.
 *
 * @param ElggObject $entity Assembly object
 * @return string URL of crud object.
 */
function crud_url_handler($entity) {
	if (!$entity->getOwnerEntity()) {
		// default to a standard view if no owner.
		return FALSE;
	}
	/*if (!$entity->testAssembly()) {
		return FALSE;
	}*/
	//$friendly_title = elgg_get_friendly_title($entity->title);

	return $entity->getSubtype()."/view/{$entity->guid}";
}


/**
 * CRUD page handler
 *
 * URLs take the form of
 *  List crud objects in group:   <crud_type>/owner/<guid>
 *  View crud object:             <crud_type>/view/<guid>
 *  Add crud object:              <crud_type>/add/<guid>
 *  Edit crud object:             <crud_type>/edit/<guid>
 *
 * @param array $page Array of url segments for routing
 * @return bool
 */
function crud_page_handler($page) {

	elgg_load_library('elgg:crud');

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	$current_url = $_SERVER['REQUEST_URI'];
	$page_url = current_page_url();
	$site_url = elgg_get_site_url();
	$current_url = str_replace($site_url, "", $page_url);
	$url_parts = explode('/', $current_url);
	$crud_type = $url_parts[0];
	$crud_handler = crud_get_handler($crud_type);
	$crud_module = $crud_handler->module;

	elgg_push_breadcrumb(elgg_echo($crud_module), '');

	switch ($page[0]) {
		case 'owner':
			crud_handle_list_page($crud_handler, $page[1]);
			break;
		case 'add':
			crud_handle_edit_page($crud_handler, 'add', $page[1]);
			break;
		case 'edit':
			crud_handle_edit_page($crud_handler, 'edit', $page[1]);
			break;
		case 'view':
			crud_handle_view_page($crud_handler, $page[1]);
			break;
		default:
			return false;
	}
	return true;
}

function crud_register_type($name) {
	global $CRUD_HANDLERS;
	$object = new CrudObject($name);
	$CRUD_HANDLERS[$name] = $object;

	// Register for search.
	elgg_register_entity_type('object', $name);

	// routing of urls
	elgg_register_page_handler($name, 'crud_page_handler');

	// override the default url to view a crud object
	elgg_register_entity_url_handler('object', $name, 'crud_url_handler');

	return $object;
}

function crud_get_handler($name) {
	global $CRUD_HANDLERS;
	return $CRUD_HANDLERS[$name];
}

/**
 * Init crud plugin.
 */
function crud_init() {
	global $CRUD_HANDLERS;
	elgg_register_library('elgg:crud', elgg_get_plugins_path() . 'crud/lib/crud.php');
	$CRUD_HANDLERS = array();

	// register actions
	$action_path = elgg_get_plugins_path() . 'crud/actions/crud';
	elgg_register_action('crud/save', "$action_path/save.php");
	elgg_register_action('crud/delete', "$action_path/delete.php");


}

