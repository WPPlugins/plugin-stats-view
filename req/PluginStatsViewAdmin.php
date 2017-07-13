<?php
/**
 * Plugin Stats View
 * 
 * @package    Plugin Stats View
 * @subpackage PluginStatsViewAdmin Management screen
    Copyright (c) 2016- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

class PluginStatsViewAdmin {

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.01
	 */
	function settings_link($links, $file) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = PLUGINSTATSVIEW_PLUGIN_BASE_FILE;
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('options-general.php?page=PluginStatsView').'">'.__( 'Settings').'</a>';
		}
		return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.01
	 */
	function plugin_menu() {
		add_options_page( 'PluginStatsView Options', 'Plugin Stats View', 'manage_options', 'PluginStatsView', array($this, 'plugin_options') );
	}

	/* ==================================================
	 * Add Css and Script
	 * @since	1.01
	 */
	function load_custom_wp_admin_style() {
		if ($this->is_my_plugin_screen()) {
			wp_enqueue_style( 'jquery-responsiveTabs', PLUGINSTATSVIEW_PLUGIN_URL.'/css/responsive-tabs.css' );
			wp_enqueue_style( 'jquery-responsiveTabs-style', PLUGINSTATSVIEW_PLUGIN_URL.'/css/style.css' );
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'jquery-responsiveTabs', PLUGINSTATSVIEW_PLUGIN_URL.'/js/jquery.responsiveTabs.min.js' );
			wp_enqueue_script( 'pluginstatsview-js', PLUGINSTATSVIEW_PLUGIN_URL.'/js/jquery.pluginstatsview.admin.js', array('jquery') );
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	1.01
	 */
	function is_my_plugin_screen() {
		$screen = get_current_screen();
		if (is_object($screen) && $screen->id == 'settings_page_PluginStatsView') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* ==================================================
	 * Settings page
	 * @since	1.01
	 */
	function plugin_options() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if( !empty($_POST) ) {
			$post_nonce_field = 'pluginstatsview_tabs';
			if ( isset($_POST[$post_nonce_field]) && $_POST[$post_nonce_field] ) {
				if ( check_admin_referer( 'psv_settings', $post_nonce_field ) ) {
					$this->options_updated();
				}
			}
		}

		$scriptname = admin_url('options-general.php?page=PluginStatsView');

		?>
		<div class="wrap">
			<h2>PluginStatsView</h2>
			<div id="pluginstatsview-tabs">
				<ul>
				<li><a href="#pluginstatsview-tabs-1"><?php _e('How to use', 'plugin-stats-view'); ?></a></li>
				<li><a href="#pluginstatsview-tabs-2"><?php _e('Donate to this plugin &#187;'); ?></a></li>
				<!--
				<li><a href="#pluginstatsview-tabs-3">FAQ</a></li>
				 -->
				</ul>
				<div id="pluginstatsview-tabs-1">
					<div class="wrap">
						<h2><?php _e('How to use', 'plugin-stats-view'); ?></h2>
						<div style="padding:10px;"><?php _e('Please add new Page. Please write a short code in the text field of the Page. Please go in Text mode this task.', 'plugin-stats-view'); ?></div>

						<div style="padding:10px;">
						<h3><?php _e('Example of short code',  'plugin-stats-view'); ?></h3>

						<div style="padding: 10px 0px;"><code>[psview slug="plugin-stats-view"]</code></div>
						<div style="padding: 10px 0px;"><code>[psview slug="plugin-stats-view" width="300px" size="small" float="none" icon="60px"]</code></div>

						<h3><?php _e('Description of each attribute.' ,'plugin-stats-view'); ?></h3>

						<li style="margin:10px;"><code>slug</code> <?php _e('Specifies the plugin slug.', 'plugin-stats-view'); ?></li>
						<li style="margin:10px;"><code>width</code> <?php echo sprintf(__('Same as CSS of %1$s.', 'plugin-stats-view'), 'width').' '.__('Default'); ?>: <code>width=NULL</code></li>
						<li style="margin:10px;"><code>size</code> <?php echo sprintf(__('Same as CSS of %1$s.', 'plugin-stats-view'), 'size').' '.__('Default'); ?>: <code>size=NULL</code></li>
						<li style="margin:10px;"><code>float</code> <?php echo sprintf(__('Same as CSS of %1$s.', 'plugin-stats-view'), 'float').' '.__('Default'); ?>: <code>float="none"</code></li>
						<li style="margin:10px;"><code>icon</code> <?php echo __('Icon size.', 'plugin-stats-view').' '.__('Default'); ?>: <code>icon="80px"</code></li>
						<li style="margin:10px;"><code>border</code> <?php echo sprintf(__('Same as CSS of %1$s.', 'plugin-stats-view'), 'border').' '.__('Default'); ?>: <code>border="#CCC 2px solid"</code></li>
						<li style="margin:10px;"><code>view</code> <?php echo __('View style. Select normal(Standard display) or simple(Convenient display).', 'plugin-stats-view').' '.__('Default'); ?>: <code>view="normal"</code></li>
						<li style="margin:10px;"><code>link</code> <?php echo __('You can specify the link destination of the plug-in name. If not specified, this is the plugin homepage link.', 'plugin-stats-view').' '.__('Default'); ?>: <code>link=NULL</code></li>

						<h3><?php _e('It will create a cache in one-day intervals for speedup. Please delete the cache if you want to display the most recent data.', 'plugin-stats-view'); ?></h3>

						<form style="padding:10px;" method="post" action="<?php echo $scriptname; ?>" />
						<?php wp_nonce_field('psv_settings', 'pluginstatsview_tabs'); ?>
							<input type="hidden" name="psview_clear_cash" value="1" />
							<?php submit_button( __('Remove Cache', 'plugin-stats-view'), 'large', 'Submit', FALSE ); ?>
						</form>

						</div>

					</div>
				</div>

				<div id="pluginstatsview-tabs-2">
				<div class="wrap">
					<?php
					$plugin_datas = get_file_data( PLUGINSTATSVIEW_PLUGIN_BASE_DIR.'/pluginstatsview.php', array('version' => 'Version') );
					$plugin_version = __('Version:').' '.$plugin_datas['version'];
					?>
					<h4 style="margin: 5px; padding: 5px;">
					<?php echo $plugin_version; ?> |
					<a style="text-decoration: none;" href="https://wordpress.org/support/plugin/plugin-stats-view" target="_blank"><?php _e('Support Forums') ?></a> |
					<a style="text-decoration: none;" href="https://wordpress.org/support/view/plugin-reviews/plugin-stats-view" target="_blank"><?php _e('Reviews', 'plugin-stats-view') ?></a>
					</h4>
					<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
					<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', 'plugin-stats-view'); ?></h3>
					<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
			<a style="margin: 5px; padding: 5px;" href='https://pledgie.com/campaigns/28307' target="_blank"><img alt='Click here to lend your support to: Various Plugins for WordPress and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/28307.png?skin_name=chrome' border='0' ></a>
					</div>
				</div>
				</div>

			</div>
		</div>
		<?php
	}

	/* ==================================================
	 * Update wp_options table.
	 * @param	none
	 * @since	1.02
	 */
	function options_updated(){

		include_once PLUGINSTATSVIEW_PLUGIN_BASE_DIR.'/inc/PluginStatsView.php';
		$pluginstatsview = new PluginStatsView();
		if ( !empty($_POST['psview_clear_cash']) ) {
			$del_cash_count = $pluginstatsview->delete_all_cash();
			if ( $del_cash_count > 0 ) {
				echo '<div class="notice notice-success is-dismissible"><ul><li>'.__('Removed the cache.', 'plugin-stats-view').'</li></ul></div>';
			} else {
				echo '<div class="notice notice-error is-dismissible"><ul><li>'.__('No Cache', 'plugin-stats-view').'</li></ul></div>';
			}
		}
		unset($pluginstatsview);

	}


}

?>