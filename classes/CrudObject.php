<?php

/**
 * Entity class for crud objects.
 */
class CrudObject extends ElggObject  {
	function getCrudTemplate() {
		return crud_get_handler($this->getSubType());
	}
}


