<?php
$opt    = null;

$opt    = new MYOptionSetup();
$opt->add_section('Notification');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'allowNotification',
					'value' => $config->get('allowNotification'),
					'title' => 'E-mail notifications',
					'desc'  => 'Send email to the specified address whenever a new blog entry is posted. If you disable the auto-publish feature, you might want to enable this.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'adminEmail',
					'value' => $config->get('adminEmail'),
					'size'  => 30,
					'maxlength' => 255,
					'title' => 'Notification Email',
					'desc'  => 'Sent to specify multiple e-mail address, use \'comma\'. E.g: user@email.com,user2@email.com'
				)
		);


$opt->add_section('Tags');

$site	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_myblog&task=category';
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'allowDefaultTags',
					'value' => $config->get('allowDefaultTags'),
					'title' => 'Default Tags',
					'desc'  => 'Allows the tags to be automatically tagged into the entry. To set the default tags, please proceed to the tags management page by <a href="' . $site . '">clicking here</a>'
				)
		);
$opt->add_section('JomSocial Integrations');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'jomsocialActivity',
					'value' => $config->get('jomsocialActivity'),
					'title' => 'Integration with Jom Social Activities',
					'desc'  => 'Enable this if you would like to have the activities integrated with JomSocial.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'jomsocialActivityTextLimit',
					'value' => $config->get('jomsocialActivityTextLimit'),
					'title' => 'Limit characters for Jomsocial Activities',
					'desc'  => 'Set this value to 0, if you want show all content'

				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'jomsocialPoints',
					'value' => $config->get('jomsocialPoints'),
					'title' => 'Integration with Jom Social User points',
					'desc'  => 'Enable this if you would like to have the user points integrated with JomSocial.'
				)
		);

//===================COMMENT SYSTEM + EXTRA PARAMS============================================
$opt->add_section('Comments');
$commentsettings = '';
$selectedsys = $config->get('useComment');
$commentsettings = '<select name="useComment" id="useComment" onchange=\'jQuery(".commentparam").hide();
						 jQuery("#" + this.value + "_param").show(); \'>
						<option value="">None</option>
						<option value="jomcomment" '.(($selectedsys == '1' || $selectedsys == 'jomcomment') ? 'selected' : '' ).'>JomComment (currently only available for Joomla <= 1.5)</option>
						<option value="intensedebate" '.(($selectedsys == 'intensedebate') ? 'selected' : '' ).'>IntenseDebate</option>
						<option value="disqus" '.(($selectedsys == 'disqus') ? 'selected' : '' ).'>Disqus</option>
                                                <option value="facebook" '.(($selectedsys == 'facebook') ? 'selected' : '' ).'>Facebook</option>
                     </select>';

$jomcomment_param = '<div class="commentparam mytable cfgdesc" id="jomcomment_param" style="display:'.(($selectedsys == 'jomcomment') ? '' : 'none' ).';">
						<dl>
							<dt>
							<div style="float:left; margin-right:10px;">
							<input type="checkbox" id="enableJCDashboard" value="1" name="enableJCDashboard" '.($config->get('enableJCDashboard') ? 'checked' : '').'>
							</div>
							<div>
							<label for="enableJCDashboard" class="cfgdesc" style="float:left;">
							Add Jom Comment locking in dashboard
							</label>
							<div class="cfgdesc" style="float:left;">
							Adds Jom Comment locking features in the dashboard and allow users to customize their blog to enable / disable comments
							</div>
							</dt>
							</div>
							<dd></dd>
						</dl>
					</div>';
$intensedebate_param = '<div class="commentparam mytable cfgdesc" id="intensedebate_param" style="display:'.(($selectedsys == 'intensedebate') ? '' : 'none' ).';">
						<dl>
							<dt>
							<label class="cfgdesc" style="float:left;">IntenseDebate Account</label>
							<div>
							<input onclick="event.preventDefault();" size="60" type="text" id="accountIntenseDebate" value="'.$config->get('accountIntenseDebate').'" name="accountIntenseDebate" /><br />
							<div class="cfgdesc">Fill your IntenseDebate site account</div>
							</div>
							</dt>
							<dd></dd>
						</dl>
					</div>';
$disqus_param = '<div class="commentparam mytable cfgdesc" id="disqus_param" style="display:'.(($selectedsys == 'disqus') ? '' : 'none' ).';">
						<dl>
							<dt>
							<label class="cfgdesc" style="float:left;">Disqus Shortname</label>
							<div>
							<input onclick="event.preventDefault();" size="60" type="text" id="disqusShortname" value="'.$config->get('disqusShortname').'" name="disqusShortname" /><br />
							<div class="cfgdesc">This is the name of your comment forum after registering with Disqus e.g. <b>myblogcomment</b>.disqus.com</div>
							</div>
							</dt>
							<dd></dd>
						</dl>
					</div>';
$facebook_param = '<div class="commentparam mytable cfgdesc" id="facebook_param" style="display:'.(($selectedsys == 'facebook') ? '' : 'none' ).';">
						<dl>
							<dt>
							<label class="cfgdesc" style="float:left;">Facebook App ID</label>
							<div>
							<input onclick="event.preventDefault();" size="60" type="text" id="fbAppID" value="'.$config->get('fbAppID').'" name="fbAppID" /><br />
							</div>
                            <label class="cfgdesc" style="float:left;">Facebook Secret ID</label>
                                                        <div>
							<input onclick="event.preventDefault();" size="60" type="text" id="fbSecretID" value="'.$config->get('fbSecretID').'" name="fbSecretID" /><br />
							</div>
                                                        <label class="cfgdesc" style="float:left;">Moderator Facebook UserID</label>
                                                        <div>
							<input onclick="event.preventDefault();" size="60" type="text" id="fbUserID" value="'.$config->get('fbUserID').'" name="fbUserID" /><br />
							<div class="cfgdesc">This is for moderator in FB Comment, you can put multiple moderators by inputting comma-separated Facebook UserIDs </div>

                                                        </div>
							</dt>
							<dd></dd>
						</dl>
					</div>';

$opt->add(
			array(
					'type' 	=> 'custom',
					'name' 	=> 'useComment',
					'value' => $commentsettings.$jomcomment_param.$intensedebate_param.$disqus_param.$facebook_param,
					'title' => 'Choose your Commenting System',
					'desc'  => ''
				)
		);

//========================================================================

/*$opt->add_section('Comments');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useComment',
					'value' => $config->get('useComment'),
					'title' => 'Integration with Jom Comment',
					'desc'  => 'Enable this if you would like to have Jom Comment integrations.'
				)
		);

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enableJCDashboard',
					'value' => $config->get('enableJCDashboard'),
					'title' => 'Add Jom Comment locking in dashboard',
					'desc'  => 'Adds Jom Comment locking features in the dashboard and allow users to customize their blog to enable / disable comments.'
				)
		);
*/

/*====== Social Media Integration ===========*/
$opt->add_section('Social Media Integration');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enableTweet',
					'value' => $config->get('enableTweet'),
					'title' => 'Enable Twitter Option',
					'desc'  => 'This option will be shown in dashboard when you create new entry'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'twitterShare',
					'value' => $config->get('twitterShare'),
					'title' => 'Enable Sharing to Twitter',
					'desc'  => 'This option will be shown in view blog entry'
				)
		);

$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'tweetConsumerKey',
					'value' => $config->get('tweetConsumerKey'),
					'title' => 'Consumer Key',
					'desc'  => ''
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'tweetConsumerSecret',
					'value' => $config->get('tweetConsumerSecret'),
					'title' => 'Consumer Secret',
					'desc'  => ''
				)
		);


/*$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'tweetUserToken',
					'value' => $config->get('tweetUserToken'),
					'title' => 'User Token',
					'desc'  => ''
				)
		);

$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'tweetUserSecret',
					'value' => $config->get('tweetUserSecret'),
					'title' => 'User Secret',
					'desc'  => ''
				)
		);*/

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enablePostFacebook',
					'value' => $config->get('enablePostFacebook'),
					'title' => 'Enable Facebook Option',
					'desc'  => 'This option will be shown in dashboard during you create new entry'
				)
		);

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'fbShare',
					'value' => $config->get('fbShare'),
					'title' => 'Enable Sharing to Facebook',
					'desc'  => 'This option will be shown in view blog entry'
				)
		);

$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'fbAppID2',
					'value' => $config->get('fbAppID2'),
					'title' => 'Facebook App ID',
					'desc'  => ''
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'fbSecretID2',
					'value' => $config->get('fbSecretID2'),
					'title' => 'Facebook App Secret ID',
					'desc'  => ''
				)
		);

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'googleShare',
					'value' => $config->get('googleShare'),
					'title' => 'Enable Sharing to Google Plus',
					'desc'  => 'This option will be shown in view blog entry'
				)
		);



$opt->add_section('Technorati Pings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'pingTechnorati',
					'value' => $config->get('pingTechnorati'),
					'title' => 'Ping Technorati',
					'desc'  => 'Automatically ping Technorati with each new blog post.Requires xmlrpc extension enabled in php.ini.(Blog RSS Feeds must be set to \'Yes\' for MyBlog tags to be picked up by Technorati)'
				)
		);
?>