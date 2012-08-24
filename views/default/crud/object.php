<?php
/**
 * View for crud object
 *
 * @package CRUD
 *
 * @uses $vars['entity']    The crud object
 * @uses $vars['full_view'] Whether to display the full view
 */

elgg_load_library('elgg:crud');

$full = elgg_extract('full_view', $vars, FALSE);
$crud= elgg_extract('entity', $vars, FALSE);

$object_subtype = $crud->getSubtype();

$crud_object = crud_get_handler($object_subtype);
$child_subtype = $crud_object->children_type;

if (!$crud) {
	return TRUE;
}

$icon = elgg_view_entity_icon($crud, 'tiny');

$status = $crud->status;

if(!in_array($status, array('new', 'assigned', 'unassigned', 'active', 'done', 'closed', 'reopened'))){
	$status = 'new';
}

$owner = get_entity($crud->owner_guid);
$owner_link = elgg_view('output/url', array(
	'href' => "crud/owner/$owner->username",
	'text' => $owner->name,
));

$date = elgg_view_friendly_time($crud->time_status_changed);
$strapline = elgg_echo("crud:strapline:$status", array($date, $owner_link));
$tags = elgg_view('output/tags', array('tags' => $crud->tags));

$comments_count = $crud->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $crud->getURL() . '#crud-comments',
		'text' => $text,
	));
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => $object_subtype,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$strapline $categories $comments_link";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {
	$body = elgg_view('output/longtext', array('value' => $crud->description));

	$params = array(
		'entity' => $page,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	$info = elgg_view_image_block($icon, $list_body);

	if (!empty($child_subtype)) {
		$children = crud_list_children($crud);

		$children_content = '<div class="elgg-list">';
		$children_content .= '<h3>'.elgg_echo('assemblies:agenda').'</h3>';
		if (!empty($children))
			$children_content .= $children;
		else
			$children_content .= elgg_echo("crud:$object_subtype:nochildren");
		$children_content .= '</div>';
		$parent_guid = $crud->parent_guid;
		if ($parent_guid) {
			$parent = get_entity($parent_guid);
		}
	}


	echo <<<HTML
$info
$body
$children_content
HTML;

} else {
	// brief view

	$excerpt = elgg_get_excerpt($crud->description);

	$params = array(
		'entity' => $crud,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => false,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body);
}
