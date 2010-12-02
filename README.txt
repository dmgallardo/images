** ABOUT ** 
This plugin allow you to add images to your plugins in an easy way
 
images is released under the GNU Public License (GPL), which
is supplied in this distribution as LICENSE.


** LICENSE INFORMATION **

This software is governed under rights, privileges, and restrictions in 
addition to those provided by the GPL v2.  Please carefully read the
LICENSE.txt file for more information.


** INSTALLATION **

	* Unzip the file into the elgg/mods/ directory.

	* Go to your Elgg tools administration section, find the new tool, and 
	  enable it.
	  
	* Enjoy!

** HOW TO USE **
	  
	* If you want to add an image to your plugin, add into your form:
		
		elgg_view('input/image', array('internalname' => 'myimage'));
		
		An automaticly will be saved to your entity.
		
	* If you want to get that image, simple call, into your entity:
		echo elgg_view('images/icon', array('entity' => $vars['entity'], 'size' => 'large'));
	 
** TODO **
	  
	* Nothing TODO at the moment.
	
	
** CHANGES **

	* No CHANGES at the moment.