<?php

/**
 * Elgg file thumbnail
 *
 * @package ElggFile
 */

// Get engine
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

// Get file GUID
$guid = (int) get_input('guid',0);

// Get file thumbnail size
$size = get_input('size','small');

// Get file entity
if ($entity = get_entity($guid)) {
		
	$prefix = images_get_prefix($entity);
	// Get thumbnail
	$thumbfile = "{$prefix}/{$guid}{$size}.jpg";

	// Grab the file
	if ($thumbfile && !empty($thumbfile)) {
		$readfile = new ElggFile();
		$readfile->owner_guid = $entity->owner_guid;
		$readfile->setFilename($thumbfile);
		$contents = $readfile->grabFile();
		
		// caching images for 10 days
		header("Content-type: image/jpeg");
		header('Expires: ' . date('r',time() + 864000));
		header("Pragma: public", true);
		header("Cache-Control: public", true);
		header("Content-Length: " . strlen($contents));
		
		echo $contents;
		exit;
			
	}
}
