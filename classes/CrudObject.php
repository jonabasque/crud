<?php
/**
 * Extended class for crud objects
 */
class CrudObject {
	function __construct($type) {
		$this->crud_type = $type;
		$this->module = $type;
		$this->children_type = $type;
		$this->has_children = true;
	}
}
