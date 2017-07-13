<?php
/**
 * Plugin Stats View
 * 
 * @package    Plugin Stats View
 * @subpackage PluginStatsView Main Functions
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

class PluginStatsView {

	public $psv_count;
	public $psv_atts;

	/* ==================================================
	* @param	string	$atts
	* @return	string	$content
	* @since	1.0
	*/
	function main_func( $atts, $content = NULL ) {

		extract(shortcode_atts(array(
			'slug'	=> '',
			'width' => '',
			'size' => '',
			'float' => '',
			'icon' => '',
			'border' => '',
			'view' => '',
			'link' => ''
		), $atts));

		if ( !empty($slug) ) {
			$call_apis = array();
			if( get_transient( 'psview_datas_'.$slug.'_'.get_locale() ) ) {
				// Get cache
				$call_apis = get_transient( 'psview_datas_'.$slug.'_'.get_locale() );
			} else {
				// Call API
				require_once ABSPATH.'wp-admin/includes/plugin-install.php';
				$call_api = plugins_api( 'plugin_information', array( 'slug' => $slug,
																	'fields' => array(
																					'short_description' => TRUE,
																					'description' => TRUE,
																					'active_installs' => TRUE,
																					'icons' => TRUE
																					)
																	 )
										);
			    if ( is_wp_error( $call_api ) ) {
					// skip
			    } else {
					$call_apis = array (
										'name' => $call_api->name,
										'icons' => $call_api->icons,
										'short_description' => $call_api->short_description,
										'description' => $call_api->description,
										'author' => $call_api->author,
										'version' => $call_api->version,
										'homepage' => $call_api->homepage,
										'name' => $call_api->name,
										'added' => $call_api->added,
										'requires' => $call_api->requires,
										'tested' => $call_api->tested,
										'last_updated' => $call_api->last_updated,
										'rating' => round( 5 * ($call_api->rating/100) ,1 ),
										'downloaded' => $call_api->downloaded,
										'download_link' => $call_api->download_link,
										'active_installs' => $call_api->active_installs
									);

					// Set cache
					set_transient( 'psview_datas_'.$slug.'_'.get_locale(), $call_apis, 86400 );
				}

			}

		    if ( !empty($call_apis) ) {
				if ( $width <> NULL ) { $width = 'width: '.$width.';'; }
				if ( $size <> NULL ) { $size = 'font-size: '.$size.';'; }
				if ( $float == 'left' || $float == 'right' ) {
					$float = 'float: '.$float.';';
					$content_break = NULL;
				} else {
					$float = 'float: none;';
					$content_break = '<p style="clear:both"></p>'."\n";
				}
				if ( $icon == NULL ) {
					$height = 'height: 80px;';
					$icon = 'width: 80px; height: 80px;';
				} else {
					$height = 'height: '.$icon.';';
					$icon = 'width: '.$icon.'; height:'.$icon.';';
				}
				if ( $view == NULL ) {
					$view = 'normal';
					$height = NULL;
				}
				if ( $border == NULL ) {
					$border = 'border: #CCC 2px solid;';
				} else {
					$border = 'border: '.$border.';';
				}
				if ( $link <> NULL ) {
					$homepage = $link;
				} else {
					$homepage = $call_apis['homepage'];
				}

				$author_link = str_replace('<a href', '<a style="text-decoration: none;" href', $call_apis['author']);
				$lastupdated = human_time_diff( strtotime($call_apis['last_updated']), current_time('timestamp', 1) ).__(' ago', 'plugin-stats-view');
				$ratings_text = sprintf(__('%1$s out of %2$d stars', 'plugin-stats-view'), $call_apis['rating'], 5);

				$content .= $content_break;
				$content .= '<div style="'.$width.' '.$height.' padding:10px; '.$border.' '.$size.' '.$float.'">'."\n";

				$content .= '<div><img src="'.array_pop($call_apis['icons']).'" style="'.$icon.' float: left; padding: 5px;" />'."\n";
				$content .= '<div style="overflow: hidden;">'."\n";
				$content .= '<div style="font-weight: bold;"><a href="'.$homepage.'" style="text-decoration: none;">'.$call_apis['name'].'</a></div>'."\n";
				$content .= '<div style="text-align: left; float: left;"><psviewrate-'.$slug.'></psviewrate-'.$slug.'></div>'."\n";
				$content .= '<div style="clear: both;"></div>'."\n";
				$content .= '<div>';
				if ( $call_apis['active_installs'] > 10 && $call_apis['active_installs'] < 1000000 ) {
					$content .= number_format($call_apis['active_installs']).__('+ ', 'plugin-stats-view').__('active installs', 'plugin-stats-view');
				} else if ( $call_apis['active_installs'] >= 1000000 ) {
					$content .= sprintf(__('%1$d+ million', 'plugin-stats-view'), floor($call_apis['active_installs']/1000000)).__('active installs', 'plugin-stats-view');
				} else {
					$content .= number_format($call_apis['downloaded']).__('Download', 'plugin-stats-view');
				}
				$content .= '</div></div></div>'."\n";

				$content .= '<div style="clear: both;"></div>'."\n";
				if ( $view <> 'simple') {
					$content .= '<div>'.$call_apis['short_description'].'</div>'."\n";
					$content .= '<div><span style="font-weight: bold;">'.__('Author', 'plugin-stats-view').': </span>'.$author_link.'</div>'."\n";
					$content .= '<div style="font-weight: bold;">'.__('Description').'</div>'."\n";
					$content .= '<div style="padding: 20px;">'.$call_apis['description'].'</div>'."\n";
					$content .= '<div><span style="font-weight: bold;">'.__('Last Updated', 'plugin-stats-view').': </span>'.$lastupdated.'</div>'."\n";
					$content .= '<div><span style="font-weight: bold;">'.__('Requires', 'plugin-stats-view').': </span>'.$call_apis['requires'].'</div>'."\n";
					$content .= '<div><span style="font-weight: bold;">'.__('Compatible up to', 'plugin-stats-view').': </span>'.$call_apis['tested'].'</div>'."\n";
					$content .= '<div><span style="font-weight: bold;">'.__('Release', 'plugin-stats-view').': </span>'.$call_apis['added'].'</div>'."\n";
					$content .= '<div><span style="font-weight: bold;">'.__('Download', 'plugin-stats-view').': </span>'.__('Version', 'plugin-stats-view').$call_apis['version'].' <a href="'.$call_apis['download_link'].'" class="dashicons dashicons-download" style="text-decoration: none; display:inline-block; vertical-align:middle;"></a></div>'."\n";
				}
				$content .= '<div style="clear: both;"></div>'."\n";

				$content .= '</div>'."\n";
				$content .= $content_break;

				++$this->psv_count;
				$raty_arrays = array(
								'slug'.$this->psv_count			=> $slug,
								'ratings_text'.$this->psv_count	=> $ratings_text,
								'rating'.$this->psv_count		=> $call_apis['rating']
								);
				$this->psv_atts[$this->psv_count] = $raty_arrays;

				return do_shortcode($content);
		    }
		} else {
			return "";
		}

	}


	/* ==================================================
	* Load Localize Script and Style
	* @param	none
	* @since	1.11
	*/
	function load_localize_scripts_styles() {

		if ( !empty($this->psv_count) ) {

			wp_enqueue_style( 'jquery-raty-style',  PLUGINSTATSVIEW_PLUGIN_URL.'/raty/jquery.raty.css' );
			wp_enqueue_style( 'ratings',  PLUGINSTATSVIEW_PLUGIN_URL.'/css/ratings.css' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-raty', PLUGINSTATSVIEW_PLUGIN_URL.'/raty/jquery.raty.js', null, '2.7.0' );
			wp_enqueue_script( 'pluginstatsview-jquery', PLUGINSTATSVIEW_PLUGIN_URL.'/js/jquery.pluginstatsview.js', array('jquery') );

			$raty_arrays = array();
			foreach($this->psv_atts as $key => $value) {
				$raty_arrays = array_merge($raty_arrays, $value);
			}
			$raty_images = array( 'raty_images' => PLUGINSTATSVIEW_PLUGIN_URL.'/raty/images' );
			$raty_arrays = array_merge($raty_arrays, $raty_images);
			$maxcount = array( 'maxcount' => $this->psv_count );
			$raty_arrays = array_merge($raty_arrays, $maxcount);
			wp_localize_script( 'pluginstatsview-jquery', 'psv_obj', $raty_arrays );
		}

	}

	/* ==================================================
	 * @param	none
	 * @return	int		$del_cash_count(int)
	 * @since	1.02
	 */
	function delete_all_cash(){

		global $wpdb;
		$search_transients = 'psview_datas_';
		$del_transients = $wpdb->get_results("
						SELECT	option_name
						FROM	$wpdb->options
						WHERE	option_name LIKE '%%$search_transients%%'
						");

		$del_cash_count = 0;
		foreach ( $del_transients as $del_transient ) {
			$transient = str_replace ( '_transient_', '', $del_transient->option_name );
			$value_del_cash = get_transient( $transient );
			if ( $value_del_cash <> FALSE ) {
				delete_transient( $transient );
				++$del_cash_count;
			}
		}

		return $del_cash_count;

	}

}

?>