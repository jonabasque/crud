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
$crud = elgg_extract('entity', $vars, FALSE);

$object_subtype = $crud->getSubtype();

$expanded_text = get_input($object_subtype.'_expanded');
if ($expanded_text == 'yes') {
	$expanded = true;
}

$crud_object = $crud->getCrudTemplate();
$child_subtype = $crud_object->children_type;

if (!$crud) {
	return TRUE;
}

$icon = elgg_view_entity_icon($crud, 'tiny');

if ($crud_object->icon_var) {
	$var_name = $crud_object->icon_var;
	$status = $crud->$var_name;

	if(empty($status)) {
		$status = 'new';
	}

	$icon = elgg_view('output/img', array('src'=>"/mod/$crud_object->module/graphics/$crud_object->crud_type-icons/$status.png", 'title' => elgg_echo("$crud_object->module:$object_subtype:$status")));
}
else {
	$icon = '';
}

$owner = get_entity($crud->owner_guid);
$owner_link = elgg_view('output/url', array(
	'href' => "crud/owner/$owner->username",
	'text' => $owner->name,
));

$date = elgg_view_friendly_time($crud->time_status_changed);
//$strapline = elgg_echo("crud:strapline:$status", array($date, $owner_link));
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

if ($full || $expanded) {
	//$icon = '';
	$body = elgg_view($crud_object->crud_type . '/profile_extra', $vars);
	$body .= elgg_view('output/longtext', array('value' => $crud->description));

	$params = array(
		'entity' => $crud,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => '',
		'tags' => false,
	);
	$variables = elgg_view('crud/object_variables', array('entity'=>$crud));

	$params = $params;
	$list_body = elgg_view('object/elements/summary', $params);


	$info = elgg_view_image_block($icon, $list_body);

	// Owner
	$body .= "<b>".elgg_echo("$crud_object->module:$crud_object->crud_type:owner", array($owner_link))."</b>";
	$body .= $variables;

	// Children
	if (!empty($child_subtype)) {
		$numchildren = crud_count_children($crud);
		if ($crud_object->embed == 'firstchild' && $numchildren == 1) {
			$child = crud_get_embedded_child($crud);
			$title = elgg_echo("$crud_object->module:$crud_object->crud_type:child");
			$content = elgg_view_entity($child, array('full_view'=>true));
		}
		else {
			$children = crud_list_children($crud);

			$title = elgg_echo("$crud_object->module:$crud_object->crud_type:children");
			if (!empty($children))
				$content .= $children;
			else
				$content .= elgg_echo("crud:$object_subtype:nochildren");
		}
		$children_content = '<div class="crud-children">';
		$children_content .= "<h3><b>$title</b></h3>";
		$children_content .= $content;
		$children_content .= '</div>';
	}


	echo <<<HTML
$info
$body
$children_content
HTML;

} else {
	// brief view
	$children_count = crud_count_children($crud);
	//only display if there are commments
	if ($children_count != 0) {
		$text = elgg_echo("$crud_object->module:$crud_object->crud_type:children") . " ($children_count)";
		$children_link = elgg_view('output/url', array(
			'href' => $crud->getURL() . '#crud-children',
			'text' => $text,
		));
	} else {
		$children_link = '';
	}

	$subtitle = $children_link . "" . $subtitle; 
	$subtitle .= elgg_view($crud_object->crud_type . '/profile_extra', $vars);


	$excerpt = elgg_get_excerpt($crud->description);

	$params = array(
		'entity' => $crud,
		'metadata' => $metadata,
		'tags' => false,
	);


	// Format title
	$title_link = $crud->getTitleLink();

	$params['title'] = $title_link;

	// Format parent link
	if (elgg_get_context() == $crud_object->crud_type && $crud->parent_guid) {
		$parent = get_entity($crud->parent_guid);
		$parent_title = $parent->getTitleLink();

		$subtitle = elgg_echo("$crud_object->crud_type:childof", array($parent_title_link))."<br />".$subtitle;
	}
	$params['subtitle'] = $subtitle;
	//$params['content'] = $content;

	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	#echo $list_body;
	echo elgg_view_image_block($icon, $list_body);
}
