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

}


