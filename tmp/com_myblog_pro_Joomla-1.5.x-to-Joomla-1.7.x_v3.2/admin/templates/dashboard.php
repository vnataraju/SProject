<?php
////////////////////////////////////////////////////////////////////////////
// Dashboard tab
////////////////////////////////////////////////////////////////////////////
$opt    = null;
$opt    = new MYOptionSetup();
$opt->add_section('Configurations');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useGzipEditor',
					'value' => $config->get('useGzipEditor'),
					'title' => 'Enable Gzip version of TINYMCE editor',
					'desc'  => 'Enable Gzip version for TINYMCE editor. If you have troubles displaying editor, disable this option.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'defaultPublishStatus',
					'value' => $config->get('defaultPublishStatus'),
					'title' => 'Auto-publish entries',
					'desc'  => 'Automatically publish new entries once saved.'
				)
		);
/*
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useLoginForm',
					'value' => $config->get('useLoginForm'),
					'title' => 'Show Login Form if not logged in',
					'desc'  => 'If enabled ,a login form will show when a user that is not logged in tries to access the MyBlog Dashboard. This is an alternative for sites that do not have a login form enabled.'
				)
		);
*/
// $opt->add(
// 			array(
// 					'type' 	=> 'checkbox',
// 					'name' 	=> 'languageCompat',
// 					'value' => $config->get('languageCompat'),
// 					'title' => 'Language compatibilty mode',
// 					'desc'  => 'Enable this if you are having troubles with encoding issues in the Dashboard by disabling some AJAX functionality.'
// 				)
// 		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useMCEeditor',
					'value' => $config->get('useMCEeditor'),
					'title' => 'Use Visual (WYSIWYG) Editor',
					'desc'  => 'Enable this if you would like to have visual editor enabled when writing an entry.'
				)
		);

$opt->add_section('Tags');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enableUserCreateTags',
					'value' => $config->get('enableUserCreateTags'),
					'title' => 'Allow bloggers to create tags through dashboard',
					'desc'  => 'Allow tag creations by bloggers from the dashboard.'
				)
		);
/*$opt->add_section('Google Gears');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'userUseGoGears',
					'value' => $config->get('userUseGoGears'),
					'title' => 'Allow blogger to use Google Gears',
					'desc'  => 'Enables Google gear option in bloggers preference page.'
				)
		);*/
?>
