<?php
/**
 * Extended class for crud objects
 */
class CrudVariable {
	function __construct($type, $options, $default) {
		$this->var_type = $type;
		$this->options = $options;
		$this->default = $default;
	}
}

class CrudObject {
	function __construct($type) {
		$this->crud_type = $type;
		$this->variables = array();
		$this->module = $type;
		$this->children_type = false;
		$this->icon_var = false;
	}

	function setVariable($name, $type, $options, $default) {
		$this->variables[$name] = new CrudVariable($type, $options, $default);
	}
}
