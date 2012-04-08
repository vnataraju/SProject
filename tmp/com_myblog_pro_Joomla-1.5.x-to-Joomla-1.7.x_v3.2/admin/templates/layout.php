<?php
////////////////////////////////////////////////////////////////////////////
// Layout tab
////////////////////////////////////////////////////////////////////////////
$opt    = null;

$opt    = new MYOptionSetup();
$opt->add_section('Main Page');
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'template',
					'value' 	=> $temp_lists,
					'selected'  => $config->get('template'),
					'size'      => 1,
					'title' 	=> 'Default My Blog Template',
					'desc'  	=> 'Select the default template for My Blog.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'overrideTemplate',
					'value' => $config->get('overrideTemplate'),
					'title' => 'Override Template',
					'desc'  => 'Enable template overriding. Your custom template must resided within /html/com_myblog/ folder of your currectly selected Joomla! template.'
				)
		);
$opt->add(
			array(
					'type' 		=> 'text',
					'name' 		=> 'numEntry',
					'value' 	=> $config->get('numEntry'),
					'size'      => 8,
					'maxlength' => 8,
					'title' 	=> 'No of entries per page',
					'desc'  	=> '&nbsp;'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'categoryDisplay',
					'value' => $config->get('categoryDisplay'),
					'title' => 'Show category',
					'desc'  => 'Display category which the entry is posted in.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'frontpageToolbar',
					'value' => $config->get('frontpageToolbar'),
					'title' => 'Show front page toolbar',
					'desc'  => 'Enable to show toolbar in My Blog\'s frontpage.'
				)
		);
$opt->add(
            array(
					'type' 		=> 'text',
					'name' 		=> 'dateFormat',
					'value' 	=> $config->get('dateFormat'),
					'size'      => 20,
					'maxlength' => 20,
					'title' 	=> 'Date Format',
					'desc'  	=> 'Works with the default template only. Set the date format that would appear in the blog entries. Format can be found at <a href="http://de.php.net/strftime" target="_blank">"PHP strftime"</a>'
                )
        );
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enableBackLink',
					'value' => $config->get('enableBackLink'),
					'title' => 'Back link',
					'desc'  => 'Enable back link when viewing the blog.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enablePdfLink',
					'value' => $config->get('enablePdfLink'),
					'title' => 'Enable PDF',
					'desc'  => 'Enable PDF link when viewing the blog.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enablePrintLink',
					'value' => $config->get('enablePrintLink'),
					'title' => 'Enable Print Link',
					'desc'  => 'Enable Print link when viewing the blog.'
				)
		);

$opt->add_section('Avatar Settings');
$none 		= '';
$gravatar   = '';
$cb         = '';
$smf        = '';
$fireboard  = '';
$juser		= '';
$jomsocial	= '';

if($config->get('avatar') == 'none'){
	$none   = 'selected="selected"';
}elseif($config->get('avatar') == 'gravatar'){
	$gravatar = 'selected="selected"';
}elseif($config->get('avatar') == 'cb'){
	$cb     = 'selected="selected"';
}elseif($config->get('avatar') == 'smf'){
	$smf    = 'selected="selected"';
}elseif($config->get('avatar') == 'fireboard'){
	$fireboard  = 'selected="selected"';
}elseif($config->get('avatar') == 'juser'){
	$juser	= 'selected="selected"';
}elseif( $config->get('avatar') == 'jomsocial' ){
	$jomsocial= ' selected="selected"';
}
$customval  = '';
$customval  = '<select name="avatar" id="avatar" multiple="multiple" size="4">'
			. '	<option value="none"' . $none . '>None</option>'
			. '	<option value="gravatar"' . $gravatar . '>Gravatar</option>'
			. '	<option value="cb"' . $cb . '>Community Builder</option>'
			. '	<option value="smf"' . $smf . '>SMF Forum</option>'
			. '	<option value="fireboard"' . $fireboard . '>Fireboard</option>'
			. '	<option value="juser"' . $juser . '>JUser</option>'
			. '	<option value="jomsocial"' . $jomsocial . '>JomSocial</option>'
			. '</select>';

$opt->add(
			array(
					'type' 	=> 'custom',
					'name' 	=> 'avatar',
					'value' => $customval,
					'title' => 'Use Avatar',
					'desc'  => 'Select which avatar to display.If you do not want to use avatar,select &quot;none&quot;,otherwise you can select <a href="http://www.gravatar.com" target="_blank">Gravatar</a>.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'avatarWidth',
					'value' => $config->get('avatarWidth'),
					'size'  => 4,
					'maxlength' => 3,
					'title' => 'Avatar Width',
					'desc'  => 'Width of the avatar to display'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'avatarHeight',
					'value' => $config->get('avatarHeight'),
					'size'  => 4,
					'maxlength' => 3,
					'title' => 'Avatar Height',
					'desc'  => 'Height of the avatar to display'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'smfPath',
					'value' => $config->get('smfPath'),
					'size'  => 45,
					'maxlength' => 500,
					'title' => 'Path to SMF forum (if required)',
					'desc'  => 'Path to your SMF forum.(Example:C:/xampplite/htdocs/smf )'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'linkAvatar',
					'value' => $config->get('linkAvatar'),
					'title' => 'Link Avatar to profile',
					'desc'  => 'Links avatar image to respective profile.Only works if Community Builder or SMF forum profile avatar is enabled'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useFullName',
					'value' => $config->get('useFullName'),
					'title' => 'Use full name',
					'desc'  => 'Choose to use full name or username of blogger for posts.'
				)
		);

$opt->add_section('Read More Settings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useIntrotext',
					'value' => $config->get('useIntrotext'),
					'title' => 'Display Introtext',
					'desc'  => 'Enable if you want to display introtext instead of fulltext in the blog entries view.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'autoReadmorePCount',
					'value' => $config->get('autoReadmorePCount'),
					'size'  => 5,
					'maxlength' => 5,
					'title' => 'Default number of paragraph for default introtext',
					'desc'  => 'If no {readmore} is not used in content, select how many paragraph to be displayed as introtext.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'readMoreLink',
					'value' => $config->get('readMoreLink'),
					'title' => 'Read More Link display',
					'desc'  => 'Customize the Read More link in the My Blog Frontpage. Only applies to certain templates that do not have a preset Read More link.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'disableReadMoreTag',
					'value' => $config->get('disableReadMoreTag'),
					'title' => 'Disable {readmore} tag and read more button',
					'desc'  => 'Disable {readmore} tag and button in editor. Force all introtext to be first X paragraph as defined above.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'necessaryReadmore',
					'value' => $config->get('necessaryReadmore'),
					'title' => 'Show Read More link only when necessary',
					'desc'  => 'Show Read More link only if there is fulltext in addition to introtext.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'anchorReadmore',
					'value' => $config->get('anchorReadmore'),
					'title' => 'Readmore Anchor',
					'desc'  => 'If enabled, clicking on read more link will automatically focus the view to the rest of the entry.'
				)
		);
$opt->add_section('Other');		
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'mambotFrontpage',
					'value' => $config->get('mambotFrontpage'),
					'title' => 'Use plugins on My Blog frontpage',
					'desc'  => 'Select \'Yes\' to integrate plugins on My Blog frontpage, \'No\' to integrate only when user clicks on blog entry.'
				)
		);
// $opt->add_section('Bookmarking Settings');
// $opt->add(
// 			array(
// 					'type' 	=> 'checkbox',
// 					'name' 	=> 'showBookmarking',
// 					'value' => $config->get('showBookmarking'),
// 					'title' => 'Show Social bookmarking links',
// 					'desc'  => 'Show Social Bookmarking links when viewing blogs'
// 				)
// 		);
?>