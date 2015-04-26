<?php
/**
 * Facebook Open Graph Meta Tags
 * Copyright 2011 Aries-Belgium
 *
 * $Id$
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook('global_end', 'fbmeta_global');
$plugins->add_hook('showthread_end', 'fbmeta_thread');
$plugins->add_hook('member_profile_end', 'fbmeta_profile');

/**
 * Info function for MyBB plugin system
 */
function fbmeta_info()
{
	global $lang;
	
	$donate_button = 
'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RQNL345SN45DS" style="float:right;margin-top:-8px;padding:4px;" target="_blank"><img src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donate_SM.gif" /></a>';

	fbmeta__lang_load();

	return array(
		"name"			=> $lang->fbmeta,
		"description"	=> $donate_button.$lang->fbmeta_description,
		"website"		=> "http://mods.mybb.com/view/facebook-open-graph-meta-tags",
		"author"		=> "Aries-Belgium",
		"authorsite"	=> "http://community.mybb.com/user-3840.html",
		"version"		=> "1.1",
		'codename'		=> 'ougc_fbmeta',
		"compatibility" => "18*"
	);
}

/**
 * The install function for the plugin system
 */
function fbmeta_install()
{
	fbmeta_settings("install");
}

/**
 * The is_installed function for the plugin system
 */
function fbmeta_is_installed()
{
	global $db;
	
	$query = $db->simple_select("settinggroups", "gid", "name='fbmeta'");
	$settings_exists = $db->num_rows($query) == 1;
	
	return $settings_exists;
}

/**
 * The uninstall function for the plugin system
 */
function fbmeta_uninstall()
{
	fbmeta_settings("uninstall");
}

/**
 * The activate function for the plugin system
 */
function fbmeta_activate()
{
	fbmeta_settings("update");
}

/**
 * Settings
 */
function fbmeta_settings()
{
	global $db, $mybb, $lang;
	
	fbmeta__lang_load();
	
	$settings_group = array(
		"name" => "fbmeta",
		"title" => $lang->fbmeta,
		"description" => $lang->fbmeta_settings,
		"disporder" => 100,
		"isdefault" => "no"
	);
	
	$disporder = 0;
	$settings = array();
	
	$settings['fbmeta_global_admins'] = array(
		"name" => "fbmeta_global_admins",
		"optionscode" => "text",
		"value" => "",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_global_appid'] = array(
		"name" => "fbmeta_global_appid",
		"optionscode" => "text",
		"value" => "",
		"disporder" => $disporder++,
	);
	
	
	$settings['fbmeta_global_site_name'] = array(
		"name" => "fbmeta_global_site_name",
		"optionscode" => "text",
		"value" => $mybb->settings['bbname'],
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_global_site_url'] = array(
		"name" => "fbmeta_global_site_url",
		"optionscode" => "text",
		"value" => $mybb->settings['bburl'],
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_global_site_logo'] = array(
		"name" => "fbmeta_index_site_logo",
		"optionscode" => "radio\n"
				."none=".$lang->fbmeta_none."\n"
				."default=".$lang->fbmeta_logo_default."\n"
				."fblogo=".$lang->fbmeta_logo_fblogo."",
		"value" => "default",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_global_site_description'] = array(
		"name" => "fbmeta_global_site_description",
		"optionscode" => "textarea",
		"value" => "",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_thread_enable'] = array(
		"name" => "fbmeta_thread_enable",
		"optionscode" => "onoff",
		"value" => "1",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_thread_message_cutoff'] = array(
		"name" => "fbmeta_thread_message_cutoff",
		"optionscode" => "text",
		"value" => "50",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_thread_image'] = array(
		"name" => "fbmeta_thread_image",
		"optionscode" => "radio\n"
			."none=".$lang->fbmeta_none."\n"
			."global=".$lang->fbmeta_image_global."\n"
			."avatar=".$lang->fbmeta_thread_image_avatar."\n"
			."firstimage=".$lang->fbmeta_thread_image_firstimage."\n"
			."allimages=".$lang->fbmeta_thread_image_allimages,
		"value" => "global",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_profile_enable'] = array(
		"name" => "fbmeta_profile_enable",
		"optionscode" => "onoff",
		"value" => "1",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_profile_description'] = array(
		"name" => "fbmeta_profile_description",
		"optionscode" => "php\n"
			."\".fbmeta_profile_fields_select(\$setting['name'],\$setting['value']).\"",
		"value" => "global",
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_profile_static_description'] = array(
		"name" => "fbmeta_profile_static_description",
		"optionscode" => "textarea",
		"value" => sprintf($lang->fbmeta_proud_member, $mybb->settings['bbname']),
		"disporder" => $disporder++,
	);
	
	$settings['fbmeta_profile_image'] = array(
		"name" => "fbmeta_profile_image",
		"optionscode" => "radio\n"
			."none=".$lang->fbmeta_none."\n"
			."global=".$lang->fbmeta_image_global."\n"
			."avatar=".$lang->fbmeta_profile_image_avatar,
		"value" => "global",
		"disporder" => $disporder++,
	);
	
	

	foreach(array_keys($settings) as $key)
	{
		$title = "setting_".$key;
		$description = "setting_desc_".$key;
		
		$settings[$key]['title'] = $lang->{$title};
		$settings[$key]['description'] = $lang->{$description};
	}
	
	$op = "all";
	$args = func_get_args();
	if(isset($args[0]))
	{
		if(in_array($args[0],array('install','update','get','all','uninstall')))
		{
			$op = $args[0];
		}
		else
		{
			$op = "get";
			$args[1] = $args[0];
		}
	}
	
	switch($op)
	{
		case "install":
			// create the settings group
			$db->insert_query("settinggroups", $settings_group);
			$gid = $db->insert_id();
			
			// insert the settings
			foreach($settings as $setting)
			{
				$setting['gid'] = $gid;
				$setting['title'] = $db->escape_string($setting['title']);
				$setting['description'] = $db->escape_string($setting['description']);
				$setting['optionscode'] = $db->escape_string($setting['optionscode']);
				$setting['value'] = $db->escape_string($setting['value']);
				$db->insert_query("settings",$setting);
			}
			
			// rebuild the settings
			rebuild_settings();
			break;
		case "update":
			$query = $db->simple_select("settinggroups", "gid", "name='{$settings_group['name']}'");
			$gid = intval($db->fetch_field($query, "gid"));
			
			foreach($settings as $setting)
			{
				$query = $db->simple_select("settings", "sid", "name='{$setting['name']}'");
				$sid = intval($db->fetch_field($query, "sid"));
				
				if($sid > 0)
				{
					unset($setting['value']);
					$setting['title'] = $db->escape_string($setting['title']);
					$setting['description'] = $db->escape_string($setting['description']);
					$setting['optionscode'] = $db->escape_string($setting['optionscode']);
					$db->update_query("settings", $setting, "sid={$sid}");
				}
				else
				{
					$setting['gid'] = $gid;
					$setting['title'] = $db->escape_string($setting['title']);
					$setting['description'] = $db->escape_string($setting['description']);
					$setting['optionscode'] = $db->escape_string($setting['optionscode']);
					$setting['value'] = $db->escape_string($setting['value']);
					$db->insert_query("settings",$setting);
				}
			}
			
			rebuild_settings();
			break;
		case "uninstall":
			$query = $db->simple_select("settinggroups", "gid", "name='{$settings_group['name']}'");
			
			while($group = $db->fetch_array($query))
			{
				$gid = intval($group['gid']);
				
				// remove the settings
				$db->delete_query("settings","gid='{$gid}'");
				
				// remove the settings group
				$db->delete_query("settinggroups","gid='{$gid}'");
			}
			
			// rebuild the settings
			rebuild_settings();
			break;
		case "get":
			$setting = $args[1];
			if(isset($settings[$setting]))
			{
				return isset($mybb->settings[$setting]) ? $mybb->settings[$setting] : $settings[$setting]['value'];
			}
			return false;
			break;
		case "all":
		default:
			return $settings;
			break;
	}
}

/**
 * Generate the select box for the profile description setting that will
 * be used in the settings.
 */
function fbmeta_profile_fields_select($name, $selected)
{
	global $db, $lang;
	
	fbmeta__lang_load();
	
	$select = '<select name="upsetting['.$name.']">';
	$select .= '<option value="global" '.($selected=='global' ? 'selected="selected"' : '').'>'.$lang->fbmeta_profile_description_global.'</option>';
	$select .= '<option value="static" '.($selected=='static' ? 'selected="selected"' : '').'>'.$lang->fbmeta_profile_description_static.'</option>';
	
	$query = $db->simple_select("profilefields", "fid, name, type", "", array('order_by' => 'disporder'));
	while($pfield = $db->fetch_array($query))
	{
		$name = sprintf($lang->fbmeta_profile_description_custom_field, $pfield['name']);
		$select .= '<option value="fid'.$pfield['fid'].'" '.($selected=='fid'.$pfield['fid'] ? 'selected="selected"' : '').'>'.$name.'</option>';
	}
	
	$select .= '</select>';
	
	return $select;
}

/**
 * Generate the og: meta tags
 *
 */
function fbmeta_get_tags($params=array())
{
	global $mybb, $plugins, $fburl;
	
	$tags = array();
	
	// check if an appid or admins are provided in the
	// params
	$admins = isset($params['admins']) ? $params['admins'] : "";
	$appid = isset($params['appdid']) ? $params['appid'] : "";
	if(empty($admins) && empty($appid))
	{
		// if not, try to load the default settings
		$admins = fbmeta_settings('fbmeta_global_admins');
		$appid = fbmeta_settings('fbmeta_global_appid');
		
		// if that is still no good, just exit the function
		if(empty($admins) && empty($appid))
		{
			return;
		}
	}
	
	if(!empty($admins))
	{
		$tags['fb:admins'] = $admins;
	}
	
	if(!empty($appid))
	{
		$tags['fb:appid'] = $appid;
	}
	
	$site_name = fbmeta_settings("fbmeta_global_site_name");
	$tags['og:site_name'] = !empty($site_name) ? $site_name : $mybb->settings['bbname'];
	
	switch(true)
	{
		case isset($params['tid']) || isset($params['thread']):
			$thread = isset($params['thread']) ? $params['thread'] : get_thread($params['tid']);
			if(!is_array($thread))
			{
				return;
			}
			
			$post = get_post($thread['firstpost']);
			
			$thread['url'] = get_thread_link($thread['tid']);
			
			$parser_options = array(
				"allow_html" => 1,
				"allow_mycode" => 1,
				"allow_smilies" => 0,
				"allow_imgcode" => 1,
				"allow_videocode" => 1,
				"filter_badwords" => 1
			);
			
			require_once MYBB_ROOT."inc/class_parser.php";
			$parser = new postParser;
			
			
			$thread['subject'] = $parser->parse_badwords($thread['subject']);
			$post['message'] = $parser->parse_message($post['message'], $parser_options);
			$uploadpath = preg_replace("/^\./", "", $mybb->settings['uploadspath']) . "/";
			$attachcache = fbmeta_get_attachcache();
			$attachments = isset($attachcache[$post['pid']]) ? $attachcache[$post['pid']] : array();
			
			// fix the thumbnail path for attachments
			$aids = array_keys($attachments);
			foreach($aids as $aid)
			{
				$ext = get_extension($attachments[$aid]['filename']);
				if($ext == "jpeg" || $ext == "gif" || $ext == "bmp" || $ext == "png" || $ext == "jpg")
				{
					$attachments[$aid]['thumbnail'] = $mybb->settings['bburl'] . $uploadpath . $attachments[$aid]['thumbnail'];
				}
				else
				{
					// we don't care about non-images
					unset($attachments[$aid]);
				}
			}
			
			// parse inline attachments first and replace it with the thumbnail
			if(preg_match_all("/\[attachment\=([0-9]+)\]/si", $post['message'], $matches))
			{
				foreach($matches[1] as $aid)
				{
					$aid = intval($aid);
					if(isset($attachments[$aid]))
					{
						$post['message'] = str_replace("[attachment={$aid}]", "<img src=\"{$attachments[$aid]['thumbnail']}\" />", $post['message']);
					}
				}
			}
			
			$cutoff = fbmeta_settings("fbmeta_thread_message_cutoff");
			if($cutoff == -1)
			{
				$description = fbmeta_settings("fbmeta_global_site_description");
			}
			elseif($cutoff > 0)
			{
				$message = trim(strip_tags($post['message']));
				$description = substr($message, 0, $cutoff);
				if(strlen($message) > $cutoff && strlen($description) > 0)
				{
					$description .= "...";
				}
			}
			else
			{
				$description = "";
			}
			
			$images = array();
			$image_setting = fbmeta_settings("fbmeta_thread_image");
			switch($image_setting)
			{
				case 'avatar':
					$user = get_user($thread['uid']);
					if(!empty($user['avatar']))
					{
						$images[] = fbmeta_get_user_avatar($user['avatar']);
					}
					break;
				case 'firstimage':
					if(preg_match("/\<img.*?src\=\"(.*?)\"/si", $post['message'], $match))
					{
						$images[] = $match[1];
					}
					
					if(count($images) == 0 && count($attachments) > 0)
					{
						$first_attachment = array_shift($attachments);
						$images[] = $first_attachment['thumbnail'];
					}
					break;
				case 'allimages':
					$images[] = fbmeta_get_global_logo();
					
					$user = get_user($thread['uid']);
					if(!empty($user['avatar']))
					{
						$images[] = fbmeta_get_user_avatar($user['avatar']);
					}
					
					if(preg_match_all("/\<img.*?src\=\"(.*?)\"/si", $post['message'], $matches))
					{
						foreach($matches[1] as $url)
						{
							$images[] = $url;
						}
					}
					
					foreach($attachments as $attachment)
					{
						$images[] = $attachment['thumbnail'];
					}
					break;
			}
			
			// fallback to global logo
			if(count($images) == 0 && $image_setting != "none")
			{
				$images[] = fbmeta_get_global_logo();
			}
			
			$tags['og:title'] = $thread['subject'];
			if(!empty($description)) $tags['og:description'] = $description;
			$tags['og:type'] = "article";
			$tags['og:url'] = $mybb->settings['bburl']."/".$thread['url'];
			if(count($images) > 0)
			{
				$tags['og:image'] = $images;
			}
		
			break;
		case isset($params['uid']) || isset($params['user']):
			$user = isset($params['user']) ? $params['user'] : get_user($params['uid']);
			
			$tags['og:title'] = $user['username'];
			
			$description = "";
			$description_type = fbmeta_settings("fbmeta_profile_description");
			if(substr($description_type, 0, 3) == 'fid')
			{
				$description = $user[$description_type];
			}
			elseif($description_type == 'global')
			{
				$description = fbmeta_settings("fbmeta_global_site_description");
			}
			
			// truncate the description
			$_description = substr($description, 0, 100);
			if(strlen($description) > 100)
			{
				$_description .= "...";
			}
			$description = $_description;
			
			// fallback to the default if the description is still empty
			if(empty($description))
			{
				$description = fbmeta_settings("fbmeta_profile_static_description");
			}
			
			$tags['og:description'] = $description;
			$tags['og:type'] = "profile";
			$tags['og:url'] = $mybb->settings['bburl']."/".get_profile_link($user['uid']);
			
			$image_type = fbmeta_settings("fbmeta_profile_image");
			if($image_type == 'avatar')
			{
				$image = fbmeta_get_user_avatar($user['avatar']);
			}
			
			// fallback to default
			if(empty($image))
			{
				$image = fbmeta_get_global_logo();
			}
			
			// unless the image type is none, of course.
			if($image_type != 'none')
			{
				$tags['og:image'] = $image;
			}
			break;
		default:
			$tags['og:title'] = fbmeta_settings("fbmeta_global_site_name");
			$tags['og:description'] = fbmeta_settings("fbmeta_global_site_description");
			$tags['og:type'] = "website";
			$tags['og:url'] = $mybb->settings['bburl']."/index.php";
			$tags['og:image'] = fbmeta_get_global_logo();
			break;
	}
	
	$plugins->run_hooks('fbmeta_tags', $tags);
	
	if(isset($params['as_array']) && $params['as_array'] == true)
	{
		return $tags;
	}
	
	$tags_html = "";
	foreach($tags as $tag => $value)
	{
		if(!is_array($value))
		{
			$value = array($value);
		}
		
		foreach($value as $v)
		{
			$v = str_replace('"', "'", $v);
			$tags_html .= "<meta property=\"{$tag}\" content=\"{$v}\" />\n";
		}
	}
	
	$fburl = rawurlencode($tags['og:url']);
	
	return $tags_html;
}

/**
 *  Get the global logo
 */
function fbmeta_get_global_logo()
{
	global $theme, $mybb;
	
	$image = fbmeta_settings("fbmeta_global_site_logo");
	switch($image)
	{
		case "custom":
			$logo = "fblogo.png";
		case "default":
		default:
			if(isset($theme['logo']))
			{
				return $theme['logo'];
			}
			
			$logo = "logo.gif";
			break;
	}
	
	if(file_exists(MYBB_ROOT."/". $theme['imgurl']."/".$logo))
	{
		return $mybb->settings['bburl']."/". $theme['imgurl']."/".$logo;
	}
	elseif(file_exists(MYBB_ROOT."/images/".$logo))
	{
		return $mybb->settings['bburl']."/images/".$logo;
	}
}

/**
 * Get the full url to an avatar
 */
function fbmeta_get_user_avatar($avatar)
{
	global $mybb;
	
	switch(true)
	{
		case substr($avatar, 0, 14) == $mybb->settings['avatardir']:
			$image = $mybb->settings['bburl']."/".$avatar;
			break;
		case substr($avatar, 0, 17) == $mybb->settings['avataruploadpath']:
			$image = preg_replace("/^\./", $mybb->settings['bburl'], $avatar);
			break;
		case substr($avatar, 0, 7) == "http://":
		case substr($avatar, 0, 8) == "https://":
			$image = $avatar;
			break;
		default:
			$image = "";
			break;
	}
	
	return $image;
}

/**
 * Get the attachment cache or load it to the cache
 */
function fbmeta_get_attachcache()
{
	global $attachcache;
	
	if(!is_array($attachcache))
	{
		$attachcache = array();
		if($thread['attachmentcount'] > 0)
		{
			// Get the attachments for this post.
			$query = $db->simple_select("attachments", "*", "pid=".$mybb->input['pid']);
			while($attachment = $db->fetch_array($query))
			{
				$attachcache[$attachment['pid']][$attachment['aid']] = $attachment;
			}
		}
	}
	
	return $attachcache;
}

/**
 * Implementation of the index_start hook
 *
 * Adds the meta tags to the index page
 */
function fbmeta_global($force_global=false)
{
	global $mybb, $headerinclude;

	// if we are not showing the thread or the profile, show the global
	if(
		$force_global || 
		(THIS_SCRIPT != "showthread.php" && (THIS_SCRIPT != "member.php" && $mybb->input['action'] != "profile"))
	)
	{
		$meta_tags = fbmeta_get_tags();
		$headerinclude = $meta_tags . $headerinclude;
	}
}

/**
 * Implementation of the showthread_start hook
 * 
 * Adds the meta tags to the showthread page
 */
function fbmeta_thread()
{
	global $thread, $headerinclude;
	
	if(fbmeta_settings("fbmeta_thread_enable") == 1)
	{
		$meta_tags = fbmeta_get_tags(array('thread'=>$thread));
		$headerinclude = $meta_tags . $headerinclude;
	}
	else
	{
		fbmeta_global(true);
	}
}

/**
 * Implementation of the member_profile_end hook
 * 
 * Adds the meta tags to the profile page
 */
function fbmeta_profile()
{
	global $memprofile, $headerinclude;
	
	if(fbmeta_settings("fbmeta_profile_enable") == 1)
	{
		$meta_tags = fbmeta_get_tags(array('user'=>$memprofile));
		$headerinclude = $meta_tags . $headerinclude;
	}
	else
	{
		fbmeta_global(true);
	}
}


/**
 * Helper function to load language files for the plugin
 */
function fbmeta__lang_load()
{
	global $lang;

	isset($lang->fbmeta) or $lang->load('fbmeta');
}