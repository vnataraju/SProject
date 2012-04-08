<?php

$opt->add_section('General Settings');
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'mainBlogTitle',
					'size'  => 45,
					'maxlength' => 500,
					'value' => stripslashes($config->get('mainBlogTitle')),
					'title' => 'Primary blog title',
					'desc'  => 'Set the primary blog title for My Blog.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name'  => 'mainBlogDesc',
					'value' => stripslashes($config->get('mainBlogDesc')),
					'rows'  => 4,
					'cols'  => 30,
					'title' => 'Primary blog description',
					'desc'  => 'Set some descripton for the primary blog.'
				)
		);

$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'managedSections[]',
					'value' 	=> $sect_lists,
					'selected'  => $sel_sect_lists,
					'size'      => $sect_rows,
					'title' 	=> 'Manage sections using My Blog',
					'desc'  	=> 'Select one or more sections to be managed using MyBlog.By selecting sections to be managed using MyBlog,content in the selected sections can be managed,published,editted,and viewed using MyBlog.'
				)
		);

$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'postSection',
					'value' 	=> $save_sect_lists,
					'selected'  => $config->get('postSection'),
					'size'      => 1,
					'title' 	=> 'Save entries in default section',
					'desc'  	=> 'MyBlog will store all entry in 1 single category, named "MyBlog" within your selected section. If it doesn\'t exist, the category will be created automatically'
				)
		);
		
$opt->add_section('Atom Publishing');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'allowAtom',
					'value' => $config->get('allowAtom'),
					'title' => 'Enable Atom Publishing API',
					'desc'  => 'Enable entries to be updated from desktop blogging client.'
				)
		);		
		
$opt->add_section('RSS Feeds');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useRSSFeed',
					'value' => $config->get('useRSSFeed'),
					'title' => 'Enable Blog RSS Feeds',
					'desc'  => 'Enable RSS feeds for each user\'s blog.Must be enabled for MyBlog tags to be picked up as Technorati Tags.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'rssFeedLimit',
					'size'  => 5,
					'maxlength' => 5,
					'value' => (int) $config->get('rssFeedLimit'),
					'title' => 'Limit number of entries to appear in the feed',
					'desc'  => 'Set the limit of entries to appear in the RSS Feed'
				)
		);
$opt->add_section('Feedburner');
$opt->add(
			array(
					'type'	=> 'checkbox',
					'name'	=> 'useFeedBurner',
					'value'	=> $config->get('useFeedBurner'),
					'title'	=> 'Use Feedburner',
					'desc'	=> 'Enable or use feedburner by providing the feed url below instead.'
					)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'useFeedBurnerURL',
					'size'  => 45,
					'maxlength' => 500,
					'value' => stripslashes($config->get('useFeedBurnerURL')),
					'title' => 'Feedburner URL',
					'desc'  => 'Set the feedburner URL'
				)
		);
$opt->add(
			array(
					'type'	=> 'checkbox',
					'name'	=> 'userUseFeedBurner',
					'value'	=> $config->get('userUseFeedBurner'),
					'title'	=> 'Allow blogger\'s to use Feedburner',
					'desc'	=> 'Allow\'s site bloggers to use feedburner instead of My Blog\'s default RSS Feeds.'
					)
		);
?>