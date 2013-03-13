<?php
/*
Plugin Name: My Custom CSS
Plugin URI: http://wordpress.org/extend/plugins/my-custom-css/
Description: With this plugin you can put custom css code without edit your theme and/or your plugins (really useful in case of any theme/plugin update).
It contain also a syntax color and tab support for write a good css code.
You can see in action (source code) here: http://vegamami.altervista.org/ :)
Author: Salvatore Noschese - DarkWolf
Version: 1.3
Author URI: http://www.darkwolf.it/
Text Domain: mccss
*/


// Prevent Direct Access with homepage redirect
if (!defined('DB_NAME') && $_SERVER["QUERY_STRING"] !== 'css')
{
	header('Location: http://'.$_SERVER['SERVER_NAME'].'/');
}

// Fix first upgrade from old release or manual my_style.css deletion!
$mycustomcss = mycustomcss();
if (!empty($mycustomcss) && (!file_exists(css_path())))
{
	makecss();
}
	
function css_path()
{
	$css_path = plugin_dir_path(__FILE__)."my_style.css";
	return $css_path;
}

function css_url()
{
	$css_url = plugin_dir_url(__FILE__)."my_style.css";
	return $css_url;
}

function icon_link()
{
	$icon_link = plugin_dir_url(__FILE__)."css-icon.png";
	return $icon_link;
}

function mycustomcss()
{
	// Strip tag for security reason!
	$mycustomcss = strip_tags(get_option('my_custom_css'));
	return $mycustomcss;
}

function makecss()
{
	// make my_style.css and write write css code!
	$makecss = file_put_contents(css_path(), "/********* Do not edit this file *********/\n/*\nMy Custom CSS - Maked by Salvatore Noschese\na.k.a. DarkWolf - http://www://darkwolf.it/\n*/\n\n".mycustomcss());
	return $makecss;
}


function add_my_custom_css()
{
	$mycustomcss = mycustomcss();
	if (!empty($mycustomcss))
	{
		// Write in source!
		echo "\n<!-- My Custom CSS Start -->\n<style type=\"text/css\">\n/* Plugin Author: Salvatore Noschese */\n@import url('".css_url()."?".filemtime(css_path())."')\n/* Also known as: DarkWolf - http://www.darkwolf.it/ */\n</style>\n<!-- My Custom CSS End -->\n";
	}
}


// Start Add link on plugins page
function my_custom_css_links($links)
{ 
	# Settings:
	$settings_link = '<a href="admin.php?page=my_custom_css" title="My Custom CSS">'.__('Settings','mccss').'</a>';
	array_unshift($links, $settings_link);
	# Support:
	$support_forum = '<a href="http://www.darkwolf.it/" title="DarkWolf">'.__('Support','mccss').'</a>';
	array_unshift($links, $support_forum);
	# return:
	return $links;
}
function add_plugin_meta_links($links, $file)
{
	if ($file == plugin_basename(__FILE__))
	{
		$links[] = '<a href="http://www.darkwolf.it/donate-wp" title="Buy Me a Beer ;)">Donate :)</a>';
	}
	return $links;
}
// End Add link on plugins page


// Change the CSS for this plugin on admin plugins page
function my_custom_css_plugin_style()
{
	global $pagenow;
	if ($pagenow == "plugins.php")
	{
		echo "<style type=\"text/css\">\n#my-custom-css td.plugin-title strong {\n\tbackground: url(\"".icon_link()."\") no-repeat 98px 1px;\n}\n#my-custom-css div.row-actions-visible {\n\tpadding-top: 15px;\n}\n#my-custom-css div.plugin-version-author-uri {\n\tbackground-color: #EAEAEA;\n\tborder-radius: 5px;\n\tbox-shadow: 0 8px 6px -6px gray; \n\tfont-weight: bold; \n\tpadding: 7px; \n\tmargin-bottom: 12px;\n}\n</style>\n";
	}
}


function mccss_admin()
{
	$plugin_page = add_menu_page(__('My Custom CSS Panel','mccss'),__('My Custom CSS','mccss'), 'manage_options', 'my_custom_css', 'mccss_options', icon_link(), 61);
	add_action('admin_init', 'register_settings_mccss');
	add_action('admin_head-'. $plugin_page, 'mccss_syntax');
	// Disable "WP Editor" in this page if is active: http://wordpress.org/extend/plugins/wp-editor/
	If (is_plugin_active("wp-editor/wpeditor.php") && $_SERVER['QUERY_STRING'] == 'page=my_custom_css')
	{
		function remove_wpeditor_header_info()
		{
		// Wp Editor Style
		wp_deregister_style('wpeditor');
		wp_deregister_style('fancybox');
		wp_deregister_style('codemirror');
		wp_deregister_style('codemirror_dialog');
		wp_deregister_style('codemirror_themes');
		// Wp Editor Script
		wp_deregister_script('wpeditor');
		wp_deregister_script('wp-editor-posts-jquery');
		wp_deregister_script('fancybox');
		wp_deregister_script('codemirror');
		wp_deregister_script('codemirror_php');
		wp_deregister_script('codemirror_javascript');
		wp_deregister_script('codemirror_css');
		wp_deregister_script('codemirror_xml');
		wp_deregister_script('codemirror_clike');
		wp_deregister_script('codemirror_dialog');
		wp_deregister_script('codemirror_search');
		wp_deregister_script('codemirror_searchcursor');
		wp_deregister_script('codemirror_mustache');
		}
	add_action('admin_init', 'remove_wpeditor_header_info', 20);
	}
}


function mccss_syntax()
{
$plugdir = plugin_dir_url(__FILE__);
?>
<!-- Syntax Support Start -->
<link type="text/css" rel="stylesheet" href="<?php echo $plugdir; ?>syntax/codemirror.css" />
<script language="javascript" src="<?php echo $plugdir; ?>syntax/codemirror.js"></script>
<script language="javascript" src="<?php echo $plugdir; ?>syntax/css.js"></script>
<!-- Syntax Support End -->
<?php
}


// Register Settings
function register_settings_mccss()
{
	register_setting('mccss_settings','my_custom_css');
}


function mccss_options()
{
?>
<div class="wrap">
	<h2><?php _e('My Custom CSS Options','mccss'); ?></h2>
	<form method="post" action="options.php">
	<?php settings_fields( 'mccss_settings' ); ?>
	<p><?php _e('Custom CSS Code:','mccss'); ?> <input type="submit" class="button-secondary" value="<?php _e('Save','mccss'); ?>" style="font-size: 20px; height: 35px; position: fixed; top: 35px; right: 15px; z-index: 5;" /></p>
	<textarea name="my_custom_css" id="my_custom_css" dir="ltr" style="width:100%;height:350px;"><?php echo get_option('my_custom_css'); ?></textarea>
	<script language="javascript">var editor = CodeMirror.fromTextArea(document.getElementById("my_custom_css"), { lineNumbers: true });</script>
	<p>
	<input type="submit" class="button-primary" value="<?php _e('Save','mccss'); ?>" />
	<input type="button" class="button-secondary" value="Top^" onclick="self.scrollTo(0, 0); return false;" style="float: right;" />
	</p>
	</form>
</div>
<?php
	// Save also in *.css file!
	$mycustomcss = mycustomcss();
	if (empty($mycustomcss))
	{
		unlink(css_path());
	}
	else
	{
		makecss();
	}
}


add_action('admin_menu', 'mccss_admin');
add_action('wp_head', 'add_my_custom_css', 999);
add_action('admin_print_styles', 'my_custom_css_plugin_style');
add_filter('plugin_row_meta', 'add_plugin_meta_links', 10, 2);
add_filter('plugin_action_links_'.plugin_basename(__FILE__).'', 'my_custom_css_links');
load_plugin_textdomain('mccss', false, dirname(plugin_basename(__FILE__)) . '/lang/');


?>