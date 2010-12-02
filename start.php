<?php
/**
 * images
 *
 * @author Administrator
 */

function images_init() {
	global $CONFIG;

	elgg_extend_view('css','images/css');

	register_page_handler('images','images_page_handler');
	
	register_elgg_event_handler('create', 'object', 'images_image_hander');
	register_elgg_event_handler('update', 'object', 'images_image_hander');
	
	// Now override icons
	register_plugin_hook('entity:icon:url', 'object', 'images_icon_hook', 999);
	
}


function images_page_handler($page) {
	global $CONFIG;

	//pg/images/$prefix/$entity_guid/$size/$icontime.jpg";
	if($page[0]) {
		set_input('image_prefix', $page[0]);
	}
	if($page[1]) {
		set_input('guid', $page[1]);
	}
	if($page[2]) {
		set_input('size', $page[2]);
	}
	if($page[3]) {
		set_input('icontime', $page[3]);
	}
	
	include $CONFIG->pluginspath . 'images/pages/thumb.php';

	return TRUE;
}

function images_image_hander($event, $object_type, $object) {
	if(($event == 'create' || $event == 'update') && $object_type == 'object') {
		//validate hidden image input, and get name of file.
		$input_image = get_input('input_image', false);

		if($input_image) {
			// we have a file photo, so process it
			if (isset($_FILES[$input_image]) && $_FILES[$input_image]['error'] == 0) {
				$owner_guid = $object->getOwner();
				$object_guid = $object->getGUID();

				$prefix = images_get_prefix($object);
				
				//@todo make this configurable?
				$icon_sizes = array(
					//'topbar' => array('w'=>16, 'h'=>16, 'square'=>TRUE, 'upscale'=>TRUE),
					'tiny' => array('w'=>25, 'h'=>25, 'square'=>TRUE, 'upscale'=>TRUE),
					'small' => array('w'=>40, 'h'=>40, 'square'=>TRUE, 'upscale'=>TRUE),
					'medium' => array('w'=>100, 'h'=>100, 'square'=>TRUE, 'upscale'=>TRUE),
					'large' => array('w'=>200, 'h'=>200, 'square'=>FALSE, 'upscale'=>FALSE),
					'master' => array('w'=>600, 'h'=>600, 'square'=>FALSE, 'upscale'=>FALSE)
				);
				
				// get the images and save their file handlers into an array
				// so we can do clean up if one fails.
				$files = array();
				foreach ($icon_sizes as $name => $size_info) {
					$resized = get_resized_image_from_uploaded_file($input_image, $size_info['w'], $size_info['h'], $size_info['square'], $size_info['upscale']);
				
					if ($resized) {
						//@todo Make these actual entities.  See exts #348.
						$file = new ElggFile();
						$file->owner_guid = $owner_guid;
						$file->setFilename("{$prefix}/{$object_guid}{$name}.jpg");
						$file->open('write');
						$file->write($resized);
						$file->close();
						$files[] = $file;
					} else {
						// cleanup on fail
						foreach ($files as $file) {
							$file->delete();
						}
						$files = array();
						
						system_message(elgg_echo('image:icon:notfound'));
					}
				}
				
				if(count($files)) {
					$object->icontime = time();
				}
				
				//Should return what triggers return ?
				trigger_elgg_event('objectimageupdate', $object_type, $object);
			}
		}
	}
}

/**
 * This hooks into the getIcon API and provides nice user icons for users where possible.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function images_icon_hook($hook, $entity_type, $returnvalue, $params){
	global $CONFIG;
	
	if ((!$returnvalue) && ($hook == 'entity:icon:url')){
		$entity = $params['entity'];
		$entity_guid = $entity->getGUID();
		
		$type = $entity->type;
		$subtype = $entity->getSubtype();
		
		$viewtype = $params['viewtype'];
		$size = $params['size'];
		if(!$size) {
			$size = 'small';
		}

		if ($icontime = $entity->icontime) {
			$icontime = "{$icontime}";
		} else {
			$icontime = "default";
		}

		$prefix = images_get_prefix($entity);
		$filehandler = new ElggFile();
		$filehandler->owner_guid = $entity->getOwner();
		$filehandler->setFilename("$prefix/" . $entity_guid . $size . ".jpg");

		if ($filehandler->exists()) {
			$url = $CONFIG->url . "pg/images/$prefix/$entity_guid/$size/$icontime.jpg";
			return $url;
		}
	}
}

function images_get_prefix($object) {
	
	//$prefix = 'images';
	$prefix = $object->getSubtype();
	if(!$prefix) {
		$prefix = $object->type;
	}

	return $prefix;
}

register_elgg_event_handler('init', 'system', 'images_init');