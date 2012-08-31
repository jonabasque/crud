<?php

/**
 * Entity class for crud objects.
 */
class CrudObject extends ElggObject  {
	/*
	 * Get the crud template for current entity
	 */
	function getCrudTemplate() {
		return crud_get_handler($this->getSubType());
	}

	/*
	 * Returns the parent entity
	 */
	function getParentEntity() {
		if ($this->parent_guid)
			return get_entity($this->parent_guid);
	}

	/*
	 * Get the title for current entity
	 */
	function getTitle($full_view=false) {
		$template = $this->getCrudTemplate();
		$title = $this->title;
		if (empty($title)) {
			$title = $template->getDefaultValue('title', '');
		}

		if ($template->title_extend && !$full_view) {
			$varname = $template->title_extend;
			$value = date(elgg_echo('crud:date_format'), $this->$varname);
			if ($title)
				$title .= ", $value";
			else
				$title = $value;
		}
		return $title;

	}

	/*
	 * Get the title formatted with a link to current entity
	 */
	function getTitleLink($full_view=false) {
		$title = $this->getTitle($full_view);
		$title_link = elgg_view('output/url', array(
                        'href' => $this->getURL(),
                        'text' => $title,
                ));
		return $title_link;
	}

	/**
	 * List children for given entity
	 *
	 * @param entity $entity Entity to operate on
	 */
	function listChildren() {
		$crud = $this->getCrudTemplate();
		$child_subtype = $crud->children_type;
		$child_options = array('full_view' => FALSE,
				'types' => 'object',
				'subtypes' => $child_subtype,
				'limit' => 10,
				'metadata_name_value_pairs' => array(
					array('name' => 'parent_guid',
						'value' => $this->guid)
					),
				);

		$children = elgg_list_entities_from_metadata($child_options);
		return $children;
	}

	/**
	 * Get children for given entity
	 */
	function getChildren($count=FALSE) {
		$limit = 10;
		if ($count)
			$limit = 0;
		$crud = $this->getCrudTemplate();
		$child_subtype = $crud->children_type;
		$child_options = array('full_view' => FALSE,
				'types' => 'object',
				'subtypes' => $child_subtype,
				'limit' => $limit,
				'count' => $count,
				'metadata_name_value_pairs' => array(
					array('name' => 'parent_guid',
						'value' => $this->guid)
					)
				);

		$children = elgg_get_entities_from_metadata($child_options);
		return $children;
	}


	/**
	 * Get the only embedded child for an entity.
	 *
	 * note: if there are more than one children, then nothing will be
	 * returned.
	 */
	function getEmbeddedChild() {
		$embedded_children = $this->getChildren();
		if (!empty($embedded_children)) {
			if (count($embedded_children) == 1)
				$embedded_child = $embedded_children[0];
		}
		return $embedded_child;
	}

}


