<?php
/*
Plugin Name: Plugin Stats View
Plugin URI: https://wordpress.org/plugins/plugin-stats-view/
Version: 1.12
Description: The stats of plugin is displayed by shortcode.
Author: Katsushi Kawamori
Author URI: http://riverforest-wp.info/
Text Domain: plugin-stats-view
Domain Path: /languages
*/

/*  Copyright (c) 2016- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	define("PLUGINSTATSVIEW_PLUGIN_BASE_FILE", plugin_basename(__FILE__));
	define("PLUGINSTATSVIEW_PLUGIN_BASE_DIR", dirname(__FILE__));
	define("PLUGINSTATSVIEW_PLUGIN_URL", plugins_url($path='plugin-stats-view',$scheme=null));

	load_plugin_textdomain('plugin-stats-view');
//	load_plugin_textdomain('plugin-stats-view', false, basename( PLUGINSTATSVIEW_PLUGIN_BASE_DIR ) . '/languages' );

	require_once( PLUGINSTATSVIEW_PLUGIN_BASE_DIR . '/req/PluginStatsViewAdmin.php' );
	$pluginstatsviewadmin = new PluginStatsViewAdmin();
	add_action( 'admin_menu', array($pluginstatsviewadmin, 'plugin_menu'));
	add_action( 'admin_enqueue_scripts', array($pluginstatsviewadmin, 'load_custom_wp_admin_style') );
	add_filter( 'plugin_action_links', array($pluginstatsviewadmin, 'settings_link'), 10, 2 );
	unset($pluginstatsviewadmin);

	include_once PLUGINSTATSVIEW_PLUGIN_BASE_DIR.'/inc/PluginStatsView.php';
	$pluginstatsview = new PluginStatsView();
	add_shortcode( 'psview', array($pluginstatsview, 'main_func') );
	add_action( 'wp_footer', array($pluginstatsview, 'load_localize_scripts_styles') );
	unset($pluginstatsview);

?>