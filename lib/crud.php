<?php
/**
 * Crud function library
 */

/**
 * List assemblies in a group
 *
 * @param int $guid Group entity GUID
 */
function crud_handle_list_page($crud, $guid) {

	elgg_set_page_owner_guid($guid);

	$crud_type = $crud->crud_type;

	$group = get_entity($guid);
	if (!$group) {
		register_error(elgg_echo('groups:notfound'));
		forward();
	}
	elgg_push_breadcrumb($group->name);

	elgg_register_title_button();

	group_gatekeeper();

	$title = elgg_echo('item:object:' . $crud_type);
	
	$options = array(
		'type' => 'object',
		'subtype' => $crud_type,
		'limit' => 20,
		'order_by' => 'e.last_action desc',
		'container_guid' => $guid,
		'full_view' => false,
	);

	$content = elgg_view($crud->module.'/general', array('entity'=>$guid));

	$content .= elgg_list_entities($options);
	if (!$content) {
		$content = elgg_echo($crud->module.':none');
	}


	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Edit or add an assembly
 *
 * @param string $type 'add' or 'edit'
 * @param int    $guid GUID of group or assembly
 */
function crud_handle_edit_page($crud, $type, $guid) {
	gatekeeper();

	$crud_type = $crud->crud_type;

	if ($type == 'add') {
		$group = get_entity($guid);
		if (!$group) {
			register_error(elgg_echo('groups:notfound'));
			forward();
		}

		// make sure user has permissions to add an assembly to container
		if (!$group->canWriteToContainer(0, 'object', $crud_type)) {
			register_error(elgg_echo('assembies:permissions:error'));
			forward($group->getURL());
		}

		$title = elgg_echo('assembly:add');

		elgg_push_breadcrumb($group->name, $crud_type."/owner/$group->guid");
		elgg_push_breadcrumb($title);

		$body_vars = crud_prepare_form_vars($crud);
		$content = elgg_view_form('crud/save', array('crud' => $crud), $body_vars);
	} else {
		$entity = get_entity($guid);
		if (!$entity || !$entity->canEdit()) {
			register_error(elgg_echo('groups:notfound'));
			forward();
		}
		$group = $entity->getContainerEntity();
		if (!$group) {
			register_error(elgg_echo('groups:notfound'));
			forward();
		}

		$title = elgg_echo($crud_type . ':edit');

		elgg_push_breadcrumb($group->name, $crud_type . "/owner/$group->guid");
		elgg_push_breadcrumb($entity->title, $entity->getURL());
		elgg_push_breadcrumb($title);

		$body_vars = crud_prepare_form_vars($crud, $entity);
		$content = elgg_view_form('crud/save', array('crud' => $crud), $body_vars);
	}

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * View an assembly
 *
 * @param int $guid GUID of assembly
 */
function crud_handle_view_page($crud, $guid) {
	// We now have RSS on assemblies
	global $autofeed;
	$autofeed = true;

	$group = get_entity($guid);
	if (!$group) {
		register_error(elgg_echo('noaccess'));
		$_SESSION['last_forward_from'] = current_page_url();
		forward('');
	}

	/*$group = $entity->getContainerEntity();
	if (!$group) {
		register_error(elgg_echo('groups:notfound'));
		forward();
	}*/
	$entity = $group;

	elgg_set_page_owner_guid($group->container_guid);

	group_gatekeeper();

	elgg_push_breadcrumb($group->name, "assembly/owner/$group->guid");
	elgg_push_breadcrumb($entity->title);

	$content = elgg_view_entity($entity, array('full_view' => true));
	
	$content .= elgg_view_comments($entity);

	$params = array(
		'content' => $content,
		'title' => $entity->title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($entity->title, $body);
}

/**
 * Prepare assembly form variables
 *
 * @param ElggObject $assembly Assembly object if editing
 * @return array
 */
function crud_prepare_form_vars($crud, $assembly = NULL) {
	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'access_id' => ACCESS_DEFAULT,
		'tags' => '',
		'crud' => $crud,
		'container_guid' => elgg_get_page_owner_guid(),
		'guid' => null,
		'entity' => $assembly,
	);

	if ($assembly) {
		foreach (array_keys($values) as $field) {
			if (isset($entity->$field)) {
				$values[$field] = $assembly->$field;
			}
		}
	}

	if (elgg_is_sticky_form('assemblies')) {
		$sticky_values = elgg_get_sticky_values('assemblies');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('assemblies');

	return $values;
}

