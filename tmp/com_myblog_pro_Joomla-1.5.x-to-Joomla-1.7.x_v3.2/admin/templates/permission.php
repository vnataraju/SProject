<?php

$opt    = null;
$opt    = new MYOptionSetup();

$opt->add_section("Reading Permissions");

$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'viewIntro',
					'value' 	=> array(
						'1' => 'All',
						'2' => 'Members Only'
						),
					'selected'  => $config->get('viewIntro'),
					'size'      => 1,
					'title' 	=> 'Permissions to view blog introtext',
					'desc'  	=> 'Select the permissions that you would want to allow for viewing of blogs'
				)
		);

$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'viewEntry',
					'value' 	=> array(
					                        '1' => 'All',
					                        '2' => 'Members Only'
										),
					'selected'  => $config->get('viewEntry'),
					'size'      => 1,
					'title' 	=> 'Permissions to view blog entries',
					'desc'  	=> 'Select the permissions that you would want to allow for viewing of blogs'
				)
		);

$opt->add_section("General Permissions");

$postGroup		= explode(",",$config->get('postGroup'));
array_walk($postGroup,"trim");

$adminPostGroup		= explode(",",$config->get('adminPostGroup'));
array_walk($adminPostGroup,"trim");

$registered = '';
$author     = '';
$editor     = '';
$publisher  = '';

if(in_array('Registered',$postGroup)){
	$registered = ' selected="selected"';
}

if(in_array('Author',$postGroup)){
	$author     = ' selected="selected"';
}

if(in_array('Editor',$postGroup)){
	$editor     = ' selected="selected"';
}

if(in_array('Publisher',$postGroup)){
	$publisher  = ' selected="selected"';
}

$manager    = '';
$admin      = '';
$supadmin   = '';
$addPermission = '';
if(in_array('Manager',$adminPostGroup)){
	$manager    = ' selected="selected"';
}

if(in_array('Administrator',$adminPostGroup)){
	$admin      = ' selected="selected"';
}

if(JVERSION >= 1.6){
	if(in_array('Super Users',$adminPostGroup)){
        $supadmin   = ' selected="selected"';
    }
}else{
	if(in_array('Super Administrator',$adminPostGroup)){
		$supadmin   = ' selected="selected"';
	}
}

$customval  = '<select name="postGroup[]" multiple="multiple" size="4">'
			. '	<option value="Registered"' . $registered . '>- Registered</option>'
			. '	<option value="Author"' . $author . '>- Author</option>'
			. '	<option value="Editor"' . $editor . '>- Editor</option>'
			. '	<option value="Publisher"' . $publisher . '>- Publisher</option>'
			. '</select>'
			. '&nbsp;&nbsp;&nbsp;&nbsp;'
			. '<select name="adminPostGroup[]" multiple="multiple" size="4">'
			. '	<option value="Manager"' . $manager . '>- Manager</option>'
			. ' <option value="Administrator"' . $admin . '>- Administrator</option>'
			. '	<option value="'.((JVERSION >= 1.6) ? 'Super Users' : 'Super Administrator').'"' . $supadmin . '>- '.((JVERSION >= 1.6) ? 'Super Users' : 'Super Administrator') .'</option>'
                        . $addPermission
                        . '</select>'
			. '<br />Additional user groups (separated by comma):<br />'
			. '<input type="text" name="extraPostGroups" value="' . $config->get('extraPostGroups') . '" size="40"/>';
$opt->add(
			array(
					'type' 	=> 'custom',
					'value' => $customval,
					'name'	=> 'postingFrom',
					'title' => 'Allow posting from',
					'desc'  => 'Allow specific groups to post.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'value' => $config->get('allowedPosters'),
					'name'  => 'allowedPosters',
					'rows'  => 4,
					'cols'  => 30,
					'title' => 'Allow posting from',
					'desc'  => 'Enter <b>userids or username</b> of users you want to allow to post (seperated by commas). For example: 61,62,63. <br /><b>NOTE: If you want ONLY these users to be able to post, deselect all options in allow posting from settings above</b>'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'value' => $config->get('disallowedPosters'),
					'name'  => 'disallowedPosters',
					'rows'  => 4,
					'cols'  => 30,
					'title' => 'Disallow blog posting from the specified users below',
					'desc'  => 'Enter <b>userids or username</b> of users you want to disallow to post (seperated by commas). For example: 61,admin,63. <br />'
				)
		);
$publishControlGroup    	= explode(",",$config->get('publishControlGroup'));
array_walk($publishControlGroup,"trim");

$adminPublishControlGroup   = explode(",",$config->get('adminPublishControlGroup'));
array_walk($adminPublishControlGroup,"trim");

$categoryGroup              = explode(",",$config->get('publishControlGroup'));
array_walk($categoryGroup,"trim");

$registered = '';
$author     = '';
$editor     = '';
$publisher  = '';

if(in_array('Registered',$publishControlGroup)){
	$registered = ' selected="selected"';
}

if(in_array('Author',$publishControlGroup)){
	$author     = ' selected="selected"';
}

if(in_array('Editor',$publishControlGroup)){
	$editor     = ' selected="selected"';
}

if(in_array('Publisher',$publishControlGroup)){
	$publisher  = ' selected="selected"';
}

$manager    = '';
$admin      = '';
$supadmin   = '';

if(in_array('Manager',$adminPublishControlGroup)){
	$manager    = ' selected="selected"';
}

if(in_array('Administrator',$adminPublishControlGroup)){
	$admin      = ' selected="selected"';
}

if(JVERSION >= 1.6){
	if(in_array('Super Users',$adminPublishControlGroup)){
        $supadmin   = ' selected="selected"';
    }
}else{
	if(in_array('Super Administrator',$adminPublishControlGroup)){
		$supadmin   = ' selected="selected"';
	}
}


$customval = '';
$customval  = '<select name="publishControlGroup[]" multiple="multiple" size="4">'
			. '	<option value="Registered"' . $registered . '>- Registered</option>'
			. '	<option value="Author"' . $author . '>- Author</option>'
			. '	<option value="Editor"' . $editor . '>- Editor</option>'
			. '	<option value="Publisher"' . $publisher . '>- Publisher</option>'
			. '</select>'
			. '&nbsp;&nbsp;&nbsp;&nbsp;'
			. '<select name="adminPublishControlGroup[]" multiple="multiple" size="4">'
			. '	<option value="Manager"' . $manager . '>- Manager</option>'
			. ' <option value="Administrator"' . $admin . '>- Administrator</option>'
			. '	<option value="'.((JVERSION >= 1.6) ? 'Super Users' : 'Super Administrator').'"' . $supadmin . '>- '.((JVERSION >= 1.6) ? 'Super Users' : 'Super Administrator').'</option>'
			. '</select>'
			. '<br />Extra user groups (seperated by comma):<br />'
			.'<input type="text" name="extraPublishGroups" value="' . $config->get('extraPublishGroups') . '" size="40"/>';

$opt->add(
			array(
					'type' 	=> 'custom',
					'value' => $customval,
					'name'	=> '',
					'title' => 'Allow Publish/Unpublish by',
					'desc'  => 'Allow specific groups to post.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name'  => 'allowedPublishers',
					'value' => $config->get('allowedPublishers'),
					'rows'  => 4,
					'cols'  => 30,
					'title' => 'Allow specific users to publish',
					'desc'  => 'Enter <b>userids or username</b> of users you want to allow to publish (seperated by commas). For example: 61,62,63. <br /><b>NOTE: If you want ONLY these users to be able to publish, deselect all options in allow publish/unpublish by settings above</b>'
				)
		);
?>