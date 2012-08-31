<?php

class CrudObject {
	function __construct($type) {
		$this->crud_type = $type;
		$this->variables = array();
		$this->module = $type;
		$this->children_type = false;
		$this->icon_var = false;
		$this->list_order = false;
		$this->list_order_direction = 'ASC';
		$this->list_tabs = false;
	}
	function getListTabContent() {
		$tab_var = $this->variables[$this->list_tabs];
		$first_option = $tab_var->options[0];
		$selected_tab = get_input('filter', $first_option);

		$container_guid = elgg_get_page_owner_guid();

		$options = array(
			'type' => 'object',
			'subtype' => $this->crud_type,
			'limit' => 10,
		#       'order_by' => 'e.last_action desc',
			'container_guid' => $container_guid,
			'full_view' => false,
        	);
		if ($this->list_tabs && $selected_tab != 'all') {
			$metadata_search = true;
			$options['metadata_name_value_pairs'] = array(
                                array('name' => $this->list_tabs,
                                        'value' => $selected_tab)
                                );
		}
		if ($this->list_order) {
			$metadata_search = true;
	                $options['order_by_metadata'] = array('name' => $this->list_order,
                                                      'direction' => $this->list_order_direction);
		}
		if ($metadata_search) {
			$content = elgg_list_entities_from_metadata($options);
		}
		else {
			$content = elgg_list_entities($options);
		}
		if (!$content) {
			$content = elgg_echo($this->module.':'.$this->crud_type.':none');
		}
		return $content;
	}
	function getListTabFilter() {
		if (!$this->list_tabs)
			return '';
		$tab_var = $this->variables[$this->list_tabs];
		$first_option = $tab_var->options[0];
		$selected_tab = get_input('filter', $first_option);
		$filter = elgg_view('crud/crud_sort_menu', array('selected' => $selected_tab, 'crud'=>$this));
		return $filter;
	}
}
