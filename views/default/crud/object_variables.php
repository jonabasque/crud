<?php
	$entity = $vars['entity'];
	$object_subtype = $entity->getSubtype();
	$crud_object = crud_get_handler($object_subtype);

	$variables = elgg_get_config($object_subtype);
	foreach ($variables as $name => $type) {
		if (in_array($name, array('title', 'description', 'access_id')))
			continue;
	?>
	<div>
		<?php
			if ($name != 'tags') {
		?>
		<label><b><?php echo elgg_echo("$crud_object->module:$object_subtype:$name") ?>: </b></label>
		<?php
			}
			if ($type != 'longtext') {
			}
		?>
		<?php echo elgg_view("output/$type", array(
				'name' => $name,
				'value' => $entity->$name,
			));
		?>
	</div>
	<?php
	}

