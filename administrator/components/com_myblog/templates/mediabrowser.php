<?php
$opt    = null;
$opt    = new MYOptionSetup();
$opt->add_section('Media browser');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useImageBrowser',
					'value' => $config->get('useImageBrowser'),
					'title' => 'Enable image browser',
					'desc'  => 'Select No to disable image browser. Will subsequently disable image uploads.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'imgFolderRoot',
					'size'  => 45,
					'maxlength' => 500,
					'value' => $config->get('imgFolderRoot'),
					'title' => 'Set image root directory',
					'desc'  => 'Set root directory of image browser (<b>relative to joomla directory</b>).Users will be able to browse all images in this directory and its subdirectories unless \'restrict user to own directory\' is set to \'Yes\'.<br/><br/>Note:User uploaded images will reside in their own subdirectory within the image root directory.(image_root_directory/user_id)'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'imgFolderRestrict',
					'value' => $config->get('imgFolderRestrict'),
					'title' => 'Restrict user to own directory',
					'desc'  => 'Restricts user to their own directory (image_root_directory/user_id/)'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'uploadSizeLimit',
					'value' => $config->get('uploadSizeLimit'),
					'size'  => 8,
					'maxlength' => 8,
					'title' => 'Image file size limit',
					'desc'  => 'Set image file upload size limit (in KBytes). Limit must be less than the POST limit set in php.ini'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enableImageResize',
					'value' => $config->get('enableImageResize'),
					'title' => 'Allow Automatic Image Upload Resizing (Remember to set the width below)',
					'desc'  => 'Enabling this option will automatically resize uploaded images.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'maxWidth',
					'value' => $config->get('maxWidth'),
					'size'  => 8,
					'maxlength' => 8,
					'title' => 'Automatic resize uploaded images larger than width (In pixels)',
					'desc'  => 'Automatically resize images which has larger width than specified. (In pixels)'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'allowedUploadFileType',
					'value' => $config->get('allowedUploadFileType'),
					'size'  => 45,
					'maxlength' => 500,
					'title' => 'File extension allowed for uploads',
					'desc'  => 'Set the file extension that you would allow to be uploaded by users. Only file extension listed here can be uploaded.'
				)
		);
$opt->add_section('Video embedding');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enableAzrulVideoBot',
					'value' => $config->get('enableAzrulVideoBot'),
					'title' => 'Enable Video Embedding in dashboard',
					'desc'  => 'Enable video embedding. Make sure to enable the Video plugin under <a href="index.php?option=com_myblog&task=contentmambots">Content Plugins</a>'
				)
		);
?>