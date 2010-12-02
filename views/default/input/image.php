<?php

/*
 * This view put the image input.
 * 
 * */

echo elgg_view('input/hidden', array('internalname' => 'input_image', 'value' => $vars['internalname']));
echo elgg_view('input/file', $vars);

?>
<?php 
//Validate form enctype, part/multipart 
?>
<script type="text/javascript">
	var form= $('input[name=input_image]').parents('form');

	if($(form).attr('enctype') != 'multipart/form-data') {
		alert('<?php echo elgg_echo('image:missing:multipart_form_data') ?>');
	}
</script>