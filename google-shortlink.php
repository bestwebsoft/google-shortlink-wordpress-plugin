<?php
/*
Plugin Name: Google Shortlink
Plugin URI: http://bestwebsoft.com/plugin/
Description: This plugin allows you to shorten links of you site with Google Shortlink
Version: 1.4.1
Author: BestWebSoft 
Author URI: http://bestwebsoft.com
License: GPLv2 or later
*/

/*  © Copyright 2014  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* function for add menu and sub-menu */
if ( ! function_exists( 'gglshrtlnk_menu' ) ) {
	function gglshrtlnk_menu() {
		global $bstwbsftwppdtplgns_options, $wpmu, $bstwbsftwppdtplgns_added_menu;
		$bws_menu_info = get_plugin_data( plugin_dir_path( __FILE__ ) . "bws_menu/bws_menu.php" );
		$bws_menu_version = $bws_menu_info["Version"];
		$base = plugin_basename(__FILE__);

		if ( ! isset( $bstwbsftwppdtplgns_options ) ) {
			if ( 1 == $wpmu ) {
				if ( ! get_site_option( 'bstwbsftwppdtplgns_options' ) )
					add_site_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
			} else {
				if ( ! get_option( 'bstwbsftwppdtplgns_options' ) )
					add_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
			}
		}

		if ( isset( $bstwbsftwppdtplgns_options['bws_menu_version'] ) ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			unset( $bstwbsftwppdtplgns_options['bws_menu_version'] );
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] ) || $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] < $bws_menu_version ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_added_menu ) ) {
			$plugin_with_newer_menu = $base;
			foreach ( $bstwbsftwppdtplgns_options['bws_menu']['version'] as $key => $value ) {
				if ( $bws_menu_version < $value && is_plugin_active( $base ) ) {
					$plugin_with_newer_menu = $key;
				}
			}
			$plugin_with_newer_menu = explode( '/', $plugin_with_newer_menu );
			$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? basename( WP_CONTENT_DIR ) : 'wp-content';
			if ( file_exists( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' ) )
				require_once( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' );
			else
				require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
			$bstwbsftwppdtplgns_added_menu = true;			
		}

		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( 'images/px.png', __FILE__ ), 1001 ); 
		add_submenu_page( 'bws_plugins', __( 'Google Shortlink Settings', 'google-shortlink' ), __( 'Google Shortlink', 'google-shortlink' ), 'manage_options', "gglshrtlnk_options", 'gglshrtlnk_options_page');
		add_menu_page( 'Google Shortlink', 'Google Shortlink', 'manage_options', 'google-shortlink', 'gglshrtlnk_page', plugins_url( "images/px.png", __FILE__ ),'31');
	}
}

if ( ! function_exists( 'gglshrtlnk_init' ) ) {
	function gglshrtlnk_init() {
		if ( ! is_admin() || ( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] = 'google-shortlink' || $_REQUEST['page'] = 'gglshrtlnk_options' ) ) )
			register_gglshrtlnk_options();
	}
}

if ( ! function_exists( 'gglshrtlnk_admin_init' ) ) {
	function gglshrtlnk_admin_init() {
		global $bws_plugin_info, $gglshrtlnk_plugin_info;
		
		if ( ! $gglshrtlnk_plugin_info )
			$gglshrtlnk_plugin_info = get_plugin_data( __FILE__, false );

		/* Add variable for bws_menu */
		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '115', 'version' => $gglshrtlnk_plugin_info["Version"] );

		/* Function check if plugin is compatible with current WP version  */
		gglshrtlnk_version_check();					

		load_plugin_textdomain( 'google-shortlink', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'gglshrtlnk_version_check' ) ) {
	function gglshrtlnk_version_check() {
		global $wp_version, $gglshrtlnk_plugin_info;
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( __FILE__ );
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $gglshrtlnk_plugin_info['Name'] . " </strong> " . __( 'requires', 'google-shortlink' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'google-shortlink') . "<br /><br />" . __( 'Back to the WordPress', 'google-shortlink') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'google-shortlink') . "</a>." );
			}
		}
	}
}

/*function for register default settings*/
if ( ! function_exists( 'register_gglshrtlnk_options' ) ) {
	function register_gglshrtlnk_options() {
		global $gglshrtlnk_options, $wpmu, $gglshrtlnk_plugin_info, $wpdb, $gglshrtlnk_table_name;

		$gglshrtlnk_table_name = $wpdb->prefix . 'google_shortlink';

		if ( ! $gglshrtlnk_plugin_info ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglshrtlnk_plugin_info = get_plugin_data( __FILE__ );	
		}

		$gglshrtlnk_db_version = '1.0';

		$gglshrtlnk_default_options = array(
			'plugin_option_version' 	=> $gglshrtlnk_plugin_info["Version"],
			'plugin_db_version' 		=> '',
			'api_key' 					=> '',
			'pagination' 				=> '10'
		);
		/* add options to database */
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'gglshrtlnk_options' ) )
				add_site_option( 'gglshrtlnk_options', $gglshrtlnk_default_options );	
		} else {
			if ( ! get_option( 'gglshrtlnk_options' ) )
				add_option( 'gglshrtlnk_options', $gglshrtlnk_default_options );	
		}
		/* get options from database to operate with them */
		if ( 1 == $wpmu )
			$gglshrtlnk_options = get_site_option( 'gglshrtlnk_options' );
		else
			$gglshrtlnk_options = get_option( 'gglshrtlnk_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $gglshrtlnk_options['plugin_option_version'] ) || $gglshrtlnk_options['plugin_option_version'] != $gglshrtlnk_plugin_info["Version"] ) {
			$gglshrtlnk_options = array_merge( $gglshrtlnk_default_options, $gglshrtlnk_options );
			$gglshrtlnk_options['plugin_option_version'] = $gglshrtlnk_plugin_info["Version"];
			update_option( 'gglshrtlnk_options', $gglshrtlnk_options );	
		}	

		/* create or update db table */
		if ( ! isset( $gglshrtlnk_options['plugin_db_version'] ) || $gglshrtlnk_options['plugin_db_version'] != $gglshrtlnk_db_version ) {
			gglshrtlnk_create_table();
			$gglshrtlnk_options['plugin_db_version'] = $gglshrtlnk_db_version;
			update_option( 'gglshrtlnk_options', $gglshrtlnk_options );	
		}
	}
}

/*function for create a new table in db*/
if ( ! function_exists( 'gglshrtlnk_create_table' ) ) {
	function gglshrtlnk_create_table() {
		global $wpdb, $gglshrtlnk_table_name;

		if ( ! $gglshrtlnk_table_name )
			$gglshrtlnk_table_name = $wpdb->prefix . 'google_shortlink';

		$is_table_exist = $wpdb->get_var(
			$wpdb->prepare(
				"SHOW TABLES LIKE %s", $gglshrtlnk_table_name
			)
		);	
		if ( $is_table_exist != $gglshrtlnk_table_name ) {
			$gglshrtlnk_sql = "CREATE TABLE `" . $gglshrtlnk_table_name . "` (
				`id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,				
				`long_url` VARCHAR(255) NOT NULL,
				`short_url` VARCHAR(50) NOT NULL,
				`post_ids` VARCHAR (500),
				PRIMARY KEY  (`id`)
			);";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $gglshrtlnk_sql );
		}
	}
}

/*function for adding styles and scripts*/
if( ! function_exists( 'gglshrtlnk_script_style' ) ) {
	function gglshrtlnk_script_style() {
		global $wp_version;
		if ( 3.8 > $wp_version ) {
			wp_enqueue_style( 'gglshrtlnk_styles', plugins_url( 'css/style_wp_before_3.8.css', __FILE__ ) );			
		} else {
			wp_enqueue_style( 'gglshrtlnk_styles', plugins_url( 'css/style.css', __FILE__ ) );
		}
		if ( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] = 'google-shortlink' || $_REQUEST['page'] = 'gglshrtlnk_options' ) ) {
			wp_enqueue_script( 'gglshrtlnk_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );?>
			<script type="text/javascript">
				var gglshrtlnk_delete_fromdb_message = '<?php _e( "Do you really want to delete all links in database?", "google-shortlink" ); ?>';
				var gglshrtlnk_plugin_page = '<?php echo admin_url("admin.php?page=google-shortlink",""); ?>'
			</script>
		<?php }			
	}
}

/* ajax function for total clicks*/
if ( ! function_exists( 'gglshrtlnk_total_clicks_javascript' ) ) {	
	function gglshrtlnk_total_clicks_javascript() { ?>
		<script type="text/javascript" >
			jQuery(document).ready(function($) {
				$( '.total_clicks' ).each( function( current_item ) {
					var gglshrtlnk_data = {
						action: 'total_clicks',
						gglshrtlnk_is_javascript_on: 1,
						gglshrtlnk_short_to_count: $(this).prev().text()
					};
					$.post( ajaxurl, gglshrtlnk_data, function( gglshrtlnk_response ) {
						$('.total_clicks').eq( current_item ).html( gglshrtlnk_response );
					});
				});
			});  			
		</script>
<?php }
}

/*callback for ajax function for total clicks*/
if ( ! function_exists( 'gglshrtlnk_ajax_total_clicks_callback' ) ) {	
	function gglshrtlnk_ajax_total_clicks_callback() {
		$gglshrtlnk_info_link = str_replace( 'goo.gl/', 'goo.gl/info/', $_POST['gglshrtlnk_short_to_count'] );
		$gglshrtlnk_count_var = gglshrtlnk_count( $_POST['gglshrtlnk_short_to_count'] );
		switch ( $gglshrtlnk_count_var ) {
			case 'unknown_error':
				echo __( 'An unknown error occurred.', 'google-shortlink' );
				die();
			break;
			case 'invalid':
				echo __( 'Bad request error.', 'google-shortlink' );
				die();
			break;
			case 'keyInvalid':
				echo __( 'Invalid API key error.', 'google-shortlink' );
				die();
			break;
			case 'accessNotConfigured':
				echo __( 'Access not configured error.', 'google-shortlink' );
				die();
			break;			
			case 'keyExpired':
				echo __( 'Expired API key error.', 'google-shortlink' );
				die();
			break;											
			case 'curl_error':
				echo __( 'Curl error. Please try again.', 'google-shortlink' );
				die();
			break;
			default:
				echo $gglshrtlnk_count_var . '<br /><a target="_blank" href="' . $gglshrtlnk_info_link . '">(' . __( 'more details', 'google-shortlink' ) . ')</a>';
				die();
			break;
		}
	}
}

/* ajax function for additional options*/
if ( ! function_exists( 'gglshrtlnk_additional_opt_javascript' ) ) {	
	function gglshrtlnk_additional_opt_javascript() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			$( '#gglshrtlnk_actions-with-links' ).submit( function() {
				return false;
			});
			$( '#gglshrtlnk_apply_button3' ).click( function() {
				var gglshrtlnk_radio_state = $( 'input[name=gglshrtlnk_actions_with_links_radio]:checked' ).val();
				var gglshrtlnk_data = {
					action: 'additional_opt',
					gglshrtlnk_is_javascript_on: 1,
					gglshrtlnk_actions_with_links_radio: gglshrtlnk_radio_state,
					gglshrtlnk_bulk_select1: $( '#gglshrtlnk_bulk_select1' ).val(),
					gglshrtlnk_bulk_select2: $( '#gglshrtlnk_bulk_select2' ).val()
				};
				$( '#gglshrtlnk_ajax-status' ).removeClass( 'gglshrtlnk_hide' ).removeClass( 'error' ).addClass( 'updated' );
				switch ( gglshrtlnk_radio_state ) {
					case 'replace-all':
						$( '#gglshrtlnk_ajax-status' ).html('<p><?php _e( "Replacing long links with short...","google-shortlink");?></p>');
					break
					case 'restore-all':
						$( '#gglshrtlnk_ajax-status' ).html('<p><?php _e( "Restoring short links to long...","google-shortlink");?></p>');
					break
					case 'delete-all-radio':
						$( '#gglshrtlnk_ajax-status' ).html('<p><?php _e( "Restoring all links and deleting them from db...","google-shortlink");?></p>');
					break
					case 'scan':
						$( '#gglshrtlnk_ajax-status' ).html('<p><?php _e( "Scanning web-site....","google-shortlink");?></p>');
					break					
				}
				$.post( ajaxurl, gglshrtlnk_data, function( gglshrtlnk_response ) {
					$( '#gglshrtlnk_ajax-status' ).html('<p>' + gglshrtlnk_response + '</p>' );
				});
			});
		});
		</script>
<?php }
}

/* callback for ajax function for additional options */
if ( ! function_exists( 'gglshrtlnk_ajax_additional_opt_callback' ) ) {	
	function gglshrtlnk_ajax_additional_opt_callback() {
		global $wpdb, $gglshrtlnk_table_name, $gglshrtlnk_links_number, $gglshrtlnk_options, $gglshrtlnk_curl_errors_count;
		$gglshrtlnk_links_number = $gglshrtlnk_curl_errors_count = 0;
		if ( ! $gglshrtlnk_table_name )
			$gglshrtlnk_table_name = $wpdb->prefix . 'google_shortlink';
		/*
		* actions with all links part
		*/
		$gglshrtlnk_rows_to_restore = $wpdb->get_results (
			"SELECT *
			FROM `$gglshrtlnk_table_name` 
			", "ARRAY_A"
		);
		switch ( $_POST['gglshrtlnk_actions_with_links_radio']  ) {
			/*if need to restore all links and clear links table*/
			case 'delete-all-radio' :
				/*restore all links before deleting*/
				foreach ( $gglshrtlnk_rows_to_restore as $gglshrtlnk_row_to_action ) {
					if ( $gglshrtlnk_row_to_action['post_ids'] != 'added_by_direct' ) {
						gglshrtlnk_restore_one( $gglshrtlnk_row_to_action );
					}
				}
				/*clear db*/
				$wpdb->query(
					"TRUNCATE 
					TABLE `$gglshrtlnk_table_name`
					"
				);?>
				<!-- disabling radibuttons afted deleting -->
				<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {
						$( '#gglshrtlnk_replace-all' ).attr( 'disabled', 'disabled' );
						$( '#gglshrtlnk_restore-all' ).attr( 'disabled', 'disabled' );
						$( '#gglshrtlnk_delete-all-radio' ).removeAttr( 'checked' ).attr( 'disabled', 'disabled' );
						$( '#gglshrtlnk_scan' ).attr( 'checked', 'checked' );
					});	
				</script>
			<?php break;	
			/*if need only to restore all links	*/
			case 'restore-all':
				foreach ( $gglshrtlnk_rows_to_restore as $gglshrtlnk_row_to_action ) {
					if ( $gglshrtlnk_row_to_action['post_ids'] != 'added_by_direct' ) {
						gglshrtlnk_restore_one( $gglshrtlnk_row_to_action );
					}
				}
			break;	
			/*if need only to replace all links	*/
			case 'replace-all':
				foreach ( $gglshrtlnk_rows_to_restore as $gglshrtlnk_row_to_action ) {
					if ( $gglshrtlnk_row_to_action['post_ids'] != 'added_by_direct' ) {
						gglshrtlnk_replace_one( $gglshrtlnk_row_to_action );
					}
				}
			break;		
			/*if need to scan the site for new links*/
			case 'scan':
				$gglshrtlnk_get_all_posts = get_post_types( '', 'names' );
				unset( $gglshrtlnk_get_all_posts['revision'] );
				unset( $gglshrtlnk_get_all_posts['attachment'] );
				unset( $gglshrtlnk_get_all_posts['nav_menu_item'] );
				$gglshrtlnk_get_posts = "'" . implode( "', '", array_keys( $gglshrtlnk_get_all_posts ) ) . "'";

				/* get post contents from db*/
				$gglshrtlnk_post_contents = $wpdb->get_results( "SELECT `post_content`, `ID`, `post_type` FROM `$wpdb->posts` WHERE `post_type` IN (" . $gglshrtlnk_get_posts . ") ORDER BY `ID`", ARRAY_A );
								
				foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_currentpost ) {
					/* find all links in posts and pages */
					preg_match_all( '#(http://|https://|ftp://)[0-9a-zA-Z./?\-_=%\#a-z&\#38;]+#m', $gglshrtlnk_currentpost['post_content'], $gglshrtlnk_out );
					/*filter links from goo.gl and home_url */
					foreach ( $gglshrtlnk_out[0] as $gglshrtlnk_link ) {
						if ( strpos( $gglshrtlnk_link, 'http://goo.gl' ) === false && strpos( $gglshrtlnk_link ,home_url() ) === false ) {
							/*check is link already in db */
							$gglshrtlnk_sql = $wpdb->prepare(
								"SELECT `long_url`
								FROM `$gglshrtlnk_table_name`
								WHERE `long_url` = %s;
								", $gglshrtlnk_link
							);
							$gglshrtlnk_link_from_db = $wpdb->get_results( $gglshrtlnk_sql );
							/*add new link to db if it not exist */
							if ( ! $gglshrtlnk_link_from_db ) {
								$gglshrtlnk_short_url = gglshrtlnk_get( $gglshrtlnk_link );
								switch ( $gglshrtlnk_short_url ) {
									case 'curl_error':
										$gglshrtlnk_curl_errors_count++;
									break;
									/* if entered invalid api-key */
									case 'keyInvalid': ?>
										<!--  if entered invalid api-key -->
										<script type="text/javascript">
											jQuery(document).ready(function($) {
												$( '#gglshrtlnk_ajax-status' ).addClass( 'error' ).removeClass( 'updated' );
											});										
										</script>
										<?php	echo '<b>'. __( 'Error:', 'google-shortlink' ) . '</b> ' . __( "Invalid API key. Go to plugin's", 'google-shortlink') . ' <a href="' . admin_url('admin.php?page=gglshrtlnk_options','') . '">' . __( 'settings page', 'google-shortlink') . '</a> ' . __('and enter correct key.', 'google-shortlink' );
										/*stop script */
										die();
									break;
									case 'keyExpired': ?>
										<!--  if entered invalid api-key -->
										<script type="text/javascript">
											jQuery(document).ready(function($) {
												$( '#gglshrtlnk_ajax-status' ).addClass( 'error' ).removeClass( 'updated' );
											});										
										</script>
										<?php echo	'<b>'. __( 'Error:', 'google-shortlink' ) . '</b> ' . __( 'Expired API key. Your key has either expired or has newly been created.', 'google-shortlink' ) . "<br/>";
											echo	__( 'Create a new key in the first case or just wait for a few minutes in the second case.', 'google-shortlink' );
										/*stop script */
										die();
									break;									
									/*skip bad request link */
									case 'invalid':								
									break;
									/*
									case 'accessNotConfigured': ?>
										<!--  if entered invalid api-key -->
										<script type="text/javascript">
											jQuery(document).ready(function($) {
												$( '#gglshrtlnk_ajax-status' ).addClass( 'error' ).removeClass( 'updated' );
											});										
										</script>
										<?php echo	'<b>'. __( 'Error:', 'google-shortlink' ) . '</b> ' . __( 'Access not configured.', 'google-shortlink' ) . "<br/>";
											echo __( 'It occurs when "URL shortener API" is turned off at Google console or when Referers field is not empty', 'google-shortlink' );
										/*stop script */
										die();
									break;
									/*skip unknown error */
									case 'unknown_error':	
									break;
									/* correct short link */
									default:
										/*find post ids for new link */
										$gglshrtlnk_post_ids = array();
										foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_is_in_post ) {
											if ( strpos( $gglshrtlnk_is_in_post['post_content'], $gglshrtlnk_link ) ) {
												$gglshrtlnk_post_ids[] = $gglshrtlnk_is_in_post['ID'];
											}
										}
										/* add to database is url is not embedd object */
										if ( ! empty( $gglshrtlnk_post_ids ) ) {
										/*convert post ids into db format */
											$gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );
											$wpdb->insert(
												$gglshrtlnk_table_name,
												array(
													'long_url' => $gglshrtlnk_link,
													'short_url' => $gglshrtlnk_short_url,
													'post_ids' => $gglshrtlnk_post_ids_converted
												)
											);
											$gglshrtlnk_links_number++;
										}
									break;
								}
							} else {
								/* update posts ids for link */
								$gglshrtlnk_post_ids = array();
								$gglshrtlnk_get_all_posts = get_post_types( '', 'names' );
								unset( $gglshrtlnk_get_all_posts['revision'] );
								unset( $gglshrtlnk_get_all_posts['attachment'] );
								unset( $gglshrtlnk_get_all_posts['nav_menu_item'] );

								foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_is_in_post ) {
									if ( array_key_exists( $gglshrtlnk_is_in_post['post_type'], $gglshrtlnk_get_all_posts ) ) {
										if ( strpos( $gglshrtlnk_is_in_post['post_content'], $gglshrtlnk_link ) ) {
											$gglshrtlnk_post_ids[] = $gglshrtlnk_is_in_post['ID'];
										}
									}
								}
								/*convert post ids into db format */
								$gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );										
								$wpdb->update( 
									$gglshrtlnk_table_name, 
									array( 'post_ids' => $gglshrtlnk_post_ids_converted ), 
									array( 'long_url' => $gglshrtlnk_link ), 
									array( '%s' ), 
									array( '%s' ) 
								);
							}
						}
					}
				}?>
				<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {
						$( '#gglshrtlnk_replace-all' ).removeAttr( 'disabled' );
						$( '#gglshrtlnk_restore-all' ).removeAttr( 'disabled' );
						$( '#gglshrtlnk_delete-all-radio' ).removeAttr( 'disabled' );
					});	
				</script>
			<?php break;
		}	
		/* message creating */
		if ( isset( $_POST['gglshrtlnk_is_javascript_on'] ) ) {
			# code...
			if ( isset( $_POST['gglshrtlnk_actions_with_links_radio'] ) && $_POST['gglshrtlnk_actions_with_links_radio'] != 'none' ) {
				if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'delete-all-radio' ) {
					_e( 'All links from database have been restored to long links and the database has been cleared.', 'google-shortlink' );
				}
				if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'restore-all' ) {
					_e( 'All links from database have been restored to long links.', 'google-shortlink' );
					echo '<br />' . __( 'Total replaces:', 'google-shortlink' ) . " " . $gglshrtlnk_links_number;
				}
				if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'replace-all' ) {
					_e( 'All links from database have been replaced with short links.', 'google-shortlink' );
					echo '<br />' . __( 'Total replaces:', 'google-shortlink' ) . " " . $gglshrtlnk_links_number;					
				}
				if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'scan' ) {
					_e( 'Web-site was scanned for new links,', 'google-shortlink' );
					if ( 0 != $gglshrtlnk_links_number ) {
						echo " " . $gglshrtlnk_links_number . " ";
						_e( 'links were added to db.', 'google-shortlink' );
					} else {
						echo " " . __( 'no new links found.', 'google-shortlink' ) . "<br />" . __( 'The list of articles, where the link is located, has been updated for each link.', 'google-shortlink' );
					}
					if ( 0 != $gglshrtlnk_curl_errors_count ) {
						echo "<br/>" . __( 'Curl errors:', 'google-shortlink' ) . " " . $gglshrtlnk_curl_errors_count . "." . __( 'Scan the web site again to find skipped links if they exist.', 'google-shortlink' );
					}
				}
			}
		die(); /* this is required to return a proper result */
		}
	}
}

/*function for actions part on table of links tab */
if ( ! function_exists( 'gglshrtlnk_actions' ) ) {
	function gglshrtlnk_actions( $gglshrtlnk_action, $gglshrtlnk_id_to_action ) {
		global $wpdb, $gglshrtlnk_table_name;
		$gglshrtlnk_sql = $wpdb->prepare(
			"SELECT *
			FROM `$gglshrtlnk_table_name`
			WHERE `id` = %d
			", $gglshrtlnk_id_to_action
		);	
		/*select row with short and long db */
		$gglshrtlnk_row_to_action = $wpdb->get_row( $gglshrtlnk_sql , 'ARRAY_A');
		/* delete selected links */
		if ( $gglshrtlnk_action == 'delete' ) {
			gglshrtlnk_delete_one( $gglshrtlnk_row_to_action );
		}
		/*check if link in some post */
		if( $gglshrtlnk_row_to_action['post_ids'] != 'added_by_direct' ) {				
			/*replace selected long links */
			if ( $gglshrtlnk_action == 'replace' ) {
				gglshrtlnk_replace_one( $gglshrtlnk_row_to_action );
			}
			/*restore selected long links */
			if ( $gglshrtlnk_action == 'restore' ) {
				gglshrtlnk_restore_one( $gglshrtlnk_row_to_action );							
			}
		}	
	}						
}

/* function for replacing one long link */
if ( ! function_exists( 'gglshrtlnk_replace_one' ) ) {
	function gglshrtlnk_replace_one( $gglshrtlnk_row_to_action ) {
		global $wpdb, $gglshrtlnk_links_number;
		$gglshrtlnk_post_ids = unserialize( $gglshrtlnk_row_to_action['post_ids'] );
		$gglshrtlnk_post_ids = implode(" OR ID = ", $gglshrtlnk_post_ids );
		$gglshrtlnk_post_contents = $wpdb->get_results( 
			"SELECT `post_content`, `ID`
			FROM `$wpdb->posts`
			WHERE `ID` = $gglshrtlnk_post_ids 
			", 'ARRAY_A'
		);
		foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_one ) {
		 	$gglshrtlnk_one['post_content'] = str_replace( $gglshrtlnk_row_to_action['long_url'], $gglshrtlnk_row_to_action['short_url'], $gglshrtlnk_one['post_content'] );
			/*update wp_posts */
			$wpdb->update( $wpdb->posts, array( 'post_content' => $gglshrtlnk_one['post_content'] ), array( 'ID' => $gglshrtlnk_one['ID'] ), array( '%s' ), array( '%d' ) );
			/*increase count of replaced links */
			$gglshrtlnk_links_number++;
		}
	}
}

/* function for restoring one long link */
if ( ! function_exists( 'gglshrtlnk_restore_one' ) ) {
	function gglshrtlnk_restore_one( $gglshrtlnk_row_to_action ) {
		global $wpdb, $gglshrtlnk_links_number;
		$gglshrtlnk_post_ids = unserialize( $gglshrtlnk_row_to_action['post_ids'] );
		$gglshrtlnk_post_ids = implode(" OR ID = ", $gglshrtlnk_post_ids );
		$gglshrtlnk_post_contents = $wpdb->get_results( 
			"SELECT `post_content`, `ID`
			FROM `$wpdb->posts`
			WHERE `ID` = $gglshrtlnk_post_ids 
			", 'ARRAY_A'
		);
		foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_one ) {
			$gglshrtlnk_one['post_content'] = str_replace( $gglshrtlnk_row_to_action['short_url'], $gglshrtlnk_row_to_action['long_url'] , $gglshrtlnk_one['post_content'] );
			/*update wp_posts */
			$wpdb->update( $wpdb->posts, array( 'post_content' => $gglshrtlnk_one['post_content'] ), array( 'ID' => $gglshrtlnk_one['ID'] ), array( '%s' ), array( '%d' ) );
			/*increase count of replaced links */
			$gglshrtlnk_links_number++;
		}
	}
}

/* function for restoring and deletind one long link */
if ( ! function_exists( 'gglshrtlnk_delete_one' ) ) {
	function gglshrtlnk_delete_one( $gglshrtlnk_row_to_action ) {
		global $wpdb, $gglshrtlnk_links_number, $gglshrtlnk_table_name;
		if ( $gglshrtlnk_row_to_action['post_ids'] != 'added_by_direct' ) {
			$gglshrtlnk_post_ids = unserialize( $gglshrtlnk_row_to_action['post_ids'] );
			$gglshrtlnk_post_ids = implode(" OR ID = ", $gglshrtlnk_post_ids );
			$gglshrtlnk_post_contents = $wpdb->get_results( 
				"SELECT `post_content`, `ID`
				FROM `$wpdb->posts`
				WHERE `ID` = $gglshrtlnk_post_ids 
				", 'ARRAY_A'
			);
			foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_one ) {
				$gglshrtlnk_one['post_content'] = str_replace( $gglshrtlnk_row_to_action['short_url'], $gglshrtlnk_row_to_action['long_url'] , $gglshrtlnk_one['post_content'] );
				/*update wp_posts */
				$wpdb->update( $wpdb->posts, array( 'post_content' => $gglshrtlnk_one['post_content'] ), array( 'ID' => $gglshrtlnk_one['ID'] ), array( '%s' ), array( '%d' ) );
			}
		}
		$gglshrtlnk_sql = $wpdb->prepare(
			"DELETE
			FROM `$gglshrtlnk_table_name`
			WHERE `id` = %d
			", $gglshrtlnk_row_to_action[ 'id' ]
		);
		$wpdb->query( $gglshrtlnk_sql );
		/*increase count of deleted links */
		$gglshrtlnk_links_number++;	
	}
}

/*function for plugin settings page */
if ( ! function_exists( 'gglshrtlnk_options_page' ) ) {
	function gglshrtlnk_options_page() {
		global $wpdb, $gglshrtlnk_table_name, $gglshrtlnk_options;

		if ( ! current_user_can( 'manage_options' ) ) {
	    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		if ( isset( $_POST['gglshrtlnk_options-form-was-send'] ) && check_admin_referer( 'gglshrtlnk_opt-noonce-action', 'gglshrtlnk_opt-noonce-field' ) ) {
			if ( $_POST['gglshrtlnk_api-key'] != '' && strlen( $_POST['gglshrtlnk_api-key'] ) == 39 ) {
				$gglshrtlnk_new_api = $_POST['gglshrtlnk_api-key'];
				$gglshrtlnk_new_pagination = $_POST['gglshrtlnk_links-per-page'];
				$gglshrtlnk_options['api_key'] = $_POST['gglshrtlnk_api-key'];
				$gglshrtlnk_options['pagination'] = $_POST['gglshrtlnk_links-per-page'];
				update_option( 'gglshrtlnk_options', $gglshrtlnk_options );
				$gglshrtlnk_message_value = __( 'Settings were changed', 'google-shortlink' );
				$gglshrtlnk_message_class = 'updated';
			} else {
				$gglshrtlnk_message_value = __( 'Incorrect API key entered', 'google-shortlink' );
				$gglshrtlnk_message_class = 'error';
			}	
		}
		?>
		<!-- page begin -->
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php _e( 'Google Shortlink Settings', 'google-shortlink' ) ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="admin.php?page=gglshrtlnk_options"><?php _e( 'Settings', 'google-shortlink' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/plugin/google-shortlink/#faq" target="_blank"><?php _e( 'FAQ', 'google-shortlink' ); ?></a>
			</h2>
			<?php if ( isset( $_POST['gglshrtlnk_options-form-was-send'] ) ) :?>		
				<div class="<?php echo $gglshrtlnk_message_class; ?> fade below-h2" >
					<p>
						<?php echo $gglshrtlnk_message_value; ?>
					</p>
				</div>
	    	<?php endif; ?>
	    	<form name="gglshrtlnk_options-form" method="post" action="">
				<h3><?php _e( 'How to get API key', 'google-shortlink' ); ?></h3>
				<p>
					<?php _e( 'To get API key you must go to', 'google-shortlink' ); ?>
					<a href="https://code.google.com/apis/console" target="_blank">Google Api Console</a>.
					<?php echo __( 'Create project there and insert public API key below.', 'google-shortlink' ) . "<br />";?>
					<a href="<?php echo admin_url('admin.php?page=google-shortlink&tab=faq','')?>"><?php _e( 'More details', 'google-shortlink' ); ?></a>.
				</p>
				<h3><?php _e( 'Settings', 'google-shortlink' ); ?></h3>
				<table class='form-table'>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'API key for your goo.gl account', 'google-shortlink' ); ?>
						</th>
						<td>
							<input name="gglshrtlnk_api-key" id="gglshrtlnk_api-key" type="text" value="<?php echo $gglshrtlnk_options[ 'api_key' ]; ?>" />
						</td>
					</tr>
					<tr valign="top" >
						<th scope="row" >
							<?php _e( 'Show links in table per page', 'google-shortlink' ); ?>
						</th>
						<td>
							<select name="gglshrtlnk_links-per-page" >
								<option value="5" <?php if ( $gglshrtlnk_options[ 'pagination' ] == '5' ) {echo 'selected="selected"';} ?>>5</option>
								<option value="10" <?php if ( $gglshrtlnk_options[ 'pagination' ] == '10' ) {echo 'selected="selected"';} ?>>10</option>
								<option value="20" <?php if ( $gglshrtlnk_options[ 'pagination' ] == '20' ) {echo 'selected="selected"';} ?>>20</option>
								<option value="50" <?php if ( $gglshrtlnk_options[ 'pagination' ] == '50' ) {echo 'selected="selected"';} ?>>50</option>
								<option value="all" <?php if ( $gglshrtlnk_options[ 'pagination' ] == 'all' ) {echo 'selected="selected"';} ?>><?php _e( 'All', 'google-shortlink' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th colspan="2" >
							<input type="submit" name="gglshrtlnk_options-form-was-send" class="button-primary" value="<?php _e( 'Save changes', 'google-shortlink' ); ?>" />
							<?php wp_nonce_field( 'gglshrtlnk_opt-noonce-action', 'gglshrtlnk_opt-noonce-field' ); ?>	
						</th>
					</tr>
				</table>
			</form>
		</div>
	<?php }
}

/*function to prepage data for the links table */
if ( ! function_exists( "gglshrtlnk_table_data" ) ) {
	function gglshrtlnk_table_data(){
		global $wpdb, $gglshrtlnk_table_name, $gglshrtlnk_options;
		/*if search query was send */
		if ( isset( $_POST['s'] ) && $_POST['s'] != '' ) {
			$gglshrtlnk_search = stripcslashes( $_POST['s'] );
			/*if searching on short link */
			if ( strpos( $gglshrtlnk_search, 'http://goo.gl/') === false ) {
				$gglshrtlnk_sql = $wpdb->prepare(
					"SELECT *
					FROM `$gglshrtlnk_table_name`
					WHERE `long_url` = %s;
					", $gglshrtlnk_search
				);
				$gglshrtlnk_data = $wpdb->get_results( $gglshrtlnk_sql, 'ARRAY_A' );
			/*if searching of long link */
			} else {
				$gglshrtlnk_sql = $wpdb->prepare(
					"SELECT *
					FROM `$gglshrtlnk_table_name`
					WHERE `short_url` = %s;
					", $gglshrtlnk_search
				);
				$gglshrtlnk_data = $wpdb->get_results( $gglshrtlnk_sql, 'ARRAY_A' );
			}	

		/*if pagination turn off */		 							
		} elseif ( $gglshrtlnk_options['pagination'] == 'all' ) {
			$gglshrtlnk_data = $wpdb->get_results(
				"SELECT *
				FROM `$gglshrtlnk_table_name`
				ORDER BY `id` DESC
				", 'ARRAY_A'
			);
		/*if pagination turn on	*/
		} else {
			$gglshrtlnk_per_page = $gglshrtlnk_options['pagination'];
			$gglshrtlnk_begin = 0;
			if ( isset( $_REQUEST['paged'] ) && $_REQUEST['paged'] != 1 ) {
				$gglshrtlnk_begin = $gglshrtlnk_per_page * absint( ( $_REQUEST['paged'] - 1 ) );
			}
			$gglshrtlnk_data = $wpdb->get_results(
				"SELECT *
				FROM `$gglshrtlnk_table_name`
				ORDER BY id DESC
				LIMIT $gglshrtlnk_per_page
				OFFSET $gglshrtlnk_begin 
				", 'ARRAY_A'
			);			
		}
		/*common part */	
		$i = 0;
		foreach ( $gglshrtlnk_data as $gglshrtlnk_row ) {
			if ( $gglshrtlnk_row['post_ids'] != 'added_by_direct') {

				$gglshrtlnk_post_ids = unserialize( $gglshrtlnk_row['post_ids'] );

				$gglshrtlnk_post_ids_string = implode( " OR ID = ", $gglshrtlnk_post_ids );
				/*get post title and guid from db */
				$gglshrtlnk_post_meta = $wpdb->get_results( 
					"SELECT `ID`, `post_title`
					FROM `$wpdb->posts`
					WHERE `ID` = $gglshrtlnk_post_ids_string 
					", 'ARRAY_A'
				);
				$j=0;
				$gglshrtlnk_home = home_url( '/?p=' );
				foreach ( $gglshrtlnk_post_meta as $gglshrtlnk_one_meta ) {
					$post_url = $gglshrtlnk_home . $gglshrtlnk_one_meta['ID'];
					$gglshrtlnk_post_ids[ $j ] = '<a target="_blank" href="' . $post_url .'">' . $gglshrtlnk_one_meta['post_title'] . '</a>';
					$j++;
				}
				$gglshrtlnk_post_ids_content = implode( ', ', $gglshrtlnk_post_ids  );
			} else {
				$gglshrtlnk_post_ids_content = __( 'None' , 'google-shortlink' );
			}
			$gglshrtlnk_return[ $i ] = array(
				'id'           => $gglshrtlnk_row['id'],
				'long_url'     => '<a target="_blank" href="' . $gglshrtlnk_row['long_url'] . '">' . $gglshrtlnk_row['long_url'] . '</a>' ,
				'short_url'    => '<a target="_blank" href="' . $gglshrtlnk_row['short_url'] . '">' . $gglshrtlnk_row['short_url'] . '</a>',
				'total_clicks' => __( 'Wait for response', 'google-shortlink' ),
				'post_ids'     => $gglshrtlnk_post_ids_content
			);
			$i++;		
		}
		if ( isset( $gglshrtlnk_return ) ) {
			return $gglshrtlnk_return;	
		} else {
			return false;
		}
	}
}

/* creating class for display table of links */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class gglshrtlnk_list_table extends WP_List_Table {
	/*conctructor */
	function __construct() {
		global $status, $page;
	    parent::__construct( array(
	        'singular'  => __( 'link', 'google-shortlink' ),     /*singular name of the listed records */
	        'plural'    => __( 'links', 'google-shortlink' ),   /*plural name of the listed records */
	        'ajax'      => true       /*does this table support ajax? */
		) );
	}
	function column_default( $item, $column_name ) {
		switch( $column_name ) { 
			case 'id':
			case 'long_url':
			case 'short_url':
			case 'total_clicks':
			case 'post_ids':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ; /*Show the whole array for troubleshooting purposes */
		}
	}
	/*function for columns */
	function get_columns(){
		$columns = array(
			'cb'		=> '<input type="checkbox" />',
			'id' 		=> __( 'ID', 'google-shortlink' ),
			'long_url'  => __( 'Long link', 'google-shortlink' ),
			'short_url' => __( 'Short link', 'google-shortlink' ),
			'total_clicks' => __( 'Total clicks', 'google-shortlink' ),
			'post_ids'  => __( 'Articles that contain links', 'google-shortlink' )
		);
		return $columns;
	}
	/*function for column cb */
	function column_cb( $item ) {
		if( $item['post_ids'] != 'None' ) {
			return sprintf(
				'<input type="checkbox" name="link[]" value="%s" />', $item['id']
			);
		} else {
			return sprintf(
				'<input type="checkbox"  name="link[]" value="%s" />', $item['id']
			);			
		}			
	}
	/*function for actions */
	function column_long_url( $item ) {
		global $wpdb, $gglshrtlnk_table_name;
		$gglshrtlnk_id = $item['id'];
		$gglshrtlnk_sql = $wpdb->prepare(
		"SELECT post_ids
		 FROM $gglshrtlnk_table_name
		 WHERE id = %s
		", $gglshrtlnk_id
		);
		$gglshrtlnk_is_added_by_direct = $wpdb->get_var( $gglshrtlnk_sql );
		if( $gglshrtlnk_is_added_by_direct != 'added_by_direct' ) {
			$actions = array(
				'replace' => sprintf( '<a href="?page=%s&action=%s&link=%s">%s</a>',$_GET['page'],'replace',$item['id'], __( 'Replace', 'google-shortlink' ) ),
				'restore' => sprintf( '<a href="?page=%s&action=%s&link=%s">%s</a>',$_GET['page'],'restore',$item['id'], __( 'Restore', 'google-shortlink' ) ),
				'delete'  => sprintf( '<a href="?page=%s&action=%s&link=%s">%s</a>',$_GET['page'],'delete',$item['id'], __( 'Delete', 'google-shortlink' ) ),
			);
		} else {
			$actions = array(
				'delete' => sprintf( '<a href="?page=%s&action=%s&link=%s">%s</a>',$_GET['page'],'delete',$item['id'], __( 'Delete', 'google-shortlink' ) ),
			);			
		}

		return sprintf( '%1$s %2$s', $item['long_url'], $this->row_actions( $actions ) );
	}
	/*function for bulk actions */
	function get_bulk_actions() {
		$actions = array(
			'replace' => __( 'Replace', 'google-shortlink' ),
			'restore' => __( 'Restore', 'google-shortlink' ),
			'delete' => __( 'Delete from db', 'google-shortlink' )
		);
		return $actions;
	}
	/*function for prepairing items */
	function prepare_items() {
		global $wpdb, $gglshrtlnk_options, $gglshrtlnk_table_name;
		/*if no pagination */
		if ( $gglshrtlnk_options['pagination'] == 'all' ){
			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items = gglshrtlnk_table_data();
			$action = $this->current_action();
			$total_items = $wpdb->get_var(
				"SELECT COUNT(*)
				FROM $gglshrtlnk_table_name
				"
			);
		/*if pagination turn on */
		} else {
			$per_page = $gglshrtlnk_options['pagination'];
  			$current_page = $this->get_pagenum();			
			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = array();
			$total_items = $wpdb->get_var(
				"SELECT COUNT(*)
				FROM $gglshrtlnk_table_name
				"
			);
			$this->found_data = gglshrtlnk_table_data();
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page
			) );			
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items = $this->found_data;
			$action = $this->current_action();					
		}
	}
} /*class end */

/*function to display table of links */
if ( ! function_exists( 'gglshrtlnk_table' ) ) {
	function gglshrtlnk_table(){
		$myListTable = new gglshrtlnk_list_table();
		$myListTable->prepare_items();
		$myListTable->search_box( 'search', 'search_id' ); 
		$myListTable->display(); 
	}
}

/* function for plugin page */
if ( ! function_exists( 'gglshrtlnk_page' ) ) {
	function gglshrtlnk_page() {
		/*globals */
		global $wpdb, $gglshrtlnk_table_name, $gglshrtlnk_links_number, $gglshrtlnk_options;
		if ( ! current_user_can( 'manage_options' ) ) {
	    	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		if ( ! isset( $_GET['tab'] ) ) {
			/*do action if isset  */
			if( isset( $_GET['action'] ) && isset( $_GET['link'] ) ) {
				gglshrtlnk_actions( $_GET['action'], $_GET['link'] );?>
			<?php } 
				/*bulk actions part */
			if ( ( ( isset( $_POST['action'] ) && $_POST['action'] != -1 ) || ( isset( $_POST['action2'] ) && $_POST['action2'] != -1 ) ) && isset( $_POST['link'] ) ) {
				foreach ( $_POST['link'] as $gglshrtlnk_id_to_action ) {
					if ( $_POST['action'] != -1 ) {
						gglshrtlnk_actions( $_POST['action'], $gglshrtlnk_id_to_action );
					} elseif ( $_POST['action2'] != -1 ) {	
						gglshrtlnk_actions( $_POST['action2'], $gglshrtlnk_id_to_action );
					}	
				}
			}?>
			<!-- TABLE OF LINKS TAB -->
			<div class="wrap">
				<div class="icon32 icon32-bws" id="icon-options-general"></div>
				<h2><?php _e( 'Google Shortlink', 'google-shortlink' ) ?></h2>
				<!-- show message if action was done -->
				<?php if ( isset( $_GET['action'] )){ ?>
					<div class="updated below-h2">
						<p>
							<?php switch ( $_GET['action'] ) {
								case 'replace':
									_e( 'One long link was replaced with a short link.', 'google-shortlink' );
									break;
								case 'restore':
									_e( 'One short link was restored to a long link.', 'google-shortlink' );
									break;
								case 'delete':
									_e( 'One short link was deleted from database.', 'google-shortlink' );
									break;															
							} ?>
						</p>
					</div>		
				<?php }
				if ( $gglshrtlnk_options['api_key'] == '' ) {?>
					<div class="error below-h2">
						<p>
							<?php echo "<b/>" . __( 'Warning:', 'google-shortlink' ) ."</b> ". __( "You don't enter api key yet. Go to plugin's", 'google-shortlink' ) . ' <a href="' . admin_url( 'admin.php?page=gglshrtlnk_options', '' ) . '">' . __( 'settings page', 'google-shortlink') . '</a> ' . __( 'and enter your key.', 'google-shortlink' );?>
						</p>
					</div>						
				<?php } 
				if ( ( isset( $_POST['action'] ) && $_POST['action'] != -1 ) || ( isset( $_POST['action2'] ) && $_POST['action2'] != -1 ) ) { ?>
					<div class="updated below-h2">
						<p>
							<?php switch ( $_POST['action'] ) {
								case 'replace':
									printf( __( 'Total %d links have been replaced', 'google-shortlink' ), $gglshrtlnk_links_number );
									break;
								case 'restore':
									printf( __( 'Total %d links have been restored', 'google-shortlink' ), $gglshrtlnk_links_number );
									break;
								case 'delete':
									printf( __( 'Total %d links have been deleted from database', 'google-shortlink' ), $gglshrtlnk_links_number );
									break;													
							} 
							switch ( $_POST['action2'] ) {
								case 'replace':
									printf( __( 'Total %d links have been replaced', 'google-shortlink' ), $gglshrtlnk_links_number );
									break;
								case 'restore':
									printf( __( 'Total %d links have been restored', 'google-shortlink' ), $gglshrtlnk_links_number );
									break;
								case 'delete':
									printf( __( 'Total %d links have been deleted from database', 'google-shortlink' ), $gglshrtlnk_links_number );
									break;													
							} ?>								
						</p>
					</div>
				<?php } 
				$gglshrtlnk_total_items = $wpdb->get_var(
					"SELECT COUNT(*)
					FROM $gglshrtlnk_table_name
					"
				);
				/*show this if database is empty */
				if ( ! $gglshrtlnk_total_items ) { ?>
					<div class="updated below-h2">
						<p>
							<?php _e( 'There are no links in database. Go to the Additional options tab, and scan your web site', 'google-shortlink' ); ?>
						</p>
					</div>
				<?php }	?>
				<h2 class="nav-tab-wrapper">
					<a class="nav-tab nav-tab-active" href="<?php echo admin_url('admin.php?page=google-shortlink','')?>"><?php _e( 'Table of links', 'google-shortlink' ); ?></a>
					<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=direct','')?>"><?php _e( 'Direct input', 'google-shortlink' ); ?></a>
					<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=all','')?>"><?php _e( 'Additional options', 'google-shortlink' ); ?></a>
					<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=faq','')?>"><?php _e( 'FAQ', 'google-shortlink' ); ?></a>
				</h2>
				<form method="post" name="gglshrtlnk_table-of-links" id="gglshrtlnk_table-of-links" action="<?php echo admin_url('admin.php?page=google-shortlink',''); ?>" class="gglshrtlnk_auto-replace">
					<?php gglshrtlnk_table(); ?>
				</form>
			</div><!-- TABLE OF LINKS END -->
		<?php } else {
			switch ( $_GET['tab'] ) {
				case 'direct':
					/*
					* direct input part
					*/
					/*set number of direct link fields if direct input form was send */
					if ( isset( $_POST[ 'gglshrtlnk_direct_was_send' ] ) && check_admin_referer( 'gglshrtlnk_dir-noonce-action', 'gglshrtlnk_dir-noonce-field' ) ) {
						$gglshrtlnk_number_of_input_links = $_POST[ 'gglshrtlnk_number_of_input_links' ];
					} else {
						$gglshrtlnk_number_of_input_links = 3;
					}
				    /* get short links if long links are exist in direct input */
					for ( $i=1; $i < $gglshrtlnk_number_of_input_links + 1; $i++ ) {
						$gglshrtlnk_input = "gglshrtlnk_url-input-" . $i;
						$gglshrtlnk_output = "gglshrtlnk_url-output-" . $i;
							if ( isset( $_POST[ $gglshrtlnk_input ] ) && $_POST[ $gglshrtlnk_input ] != '' ) {
							    $gglshrtlnk_input_links[ $gglshrtlnk_input ] = $_POST[ $gglshrtlnk_input ];
							    /*check first is a short kink alreary exist in db */
							    $gglshrtlnk_short_url_from_db = $wpdb->get_var(
							    	$wpdb->prepare(
										"SELECT `short_url`
										FROM `$gglshrtlnk_table_name`
										WHERE `long_url` = %s
										", $gglshrtlnk_input_links[ $gglshrtlnk_input ]
									)
							    );			    
							    if ( ! $gglshrtlnk_short_url_from_db ) {
							    	/*get a short url from goo.gl */
							    	$gglshrtlnk_short_url[ $gglshrtlnk_output ] = gglshrtlnk_get( $gglshrtlnk_input_links[ $gglshrtlnk_input ] );
							    	switch ( $gglshrtlnk_short_url[ $gglshrtlnk_output ] ) {
							    		/*if invalid api key */
							    		case 'keyInvalid':
									    	$gglshrtlnk_short_url[ $gglshrtlnk_output ] = '';
									    	$gglshrtlnk_key_invalid = 1;	
							    		break;
							    		case 'invalid':
									    	$gglshrtlnk_short_url[ $gglshrtlnk_output ] = __( 'Bad request', 'google-shortlink' );
									    	$gglshrtlnk_bad_request = 1;
									    break;
							    		case 'accessNotConfigured':
									    	$gglshrtlnk_short_url[ $gglshrtlnk_output ] = __( 'Acces not configured.', 'google-shortlink' );
									    	$gglshrtlnk_unknown_error = 1;
									    break;
									    case 'unknown_error':
									    	$gglshrtlnk_short_url[ $gglshrtlnk_output ] = __( 'Unknown error. Try again later.', 'google-shortlink' );
									    	$gglshrtlnk_unknown_error = 1;
									    break;	
							    		default:
									    	/*add long and short url to db */
												$gglshrtlnk_post_contents = $wpdb->get_results( 
													"SELECT `post_content`, `ID`, `post_type` 
													FROM `$wpdb->posts` 
													", 'ARRAY_A'
												);									    					    
												$gglshrtlnk_post_ids = array();
												$gglshrtlnk_get_all_posts = get_post_types( '', 'names' );
												unset( $gglshrtlnk_get_all_posts['revision'] );
												unset( $gglshrtlnk_get_all_posts['attachment'] );
												unset( $gglshrtlnk_get_all_posts['nav_menu_item'] );
												foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_is_in_post ) {
													if ( array_key_exists( $gglshrtlnk_is_in_post['post_type'], $gglshrtlnk_get_all_posts ) ) {
														if ( strpos( $gglshrtlnk_is_in_post['post_content'], $gglshrtlnk_input_links[ $gglshrtlnk_input ] ) ) {
															$gglshrtlnk_post_ids[] = $gglshrtlnk_is_in_post['ID'];
														}
													}
												}
												/*convert post ids into db format */
												if ( !empty( $gglshrtlnk_post_ids ) ) {
													$gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );										
												} else {
													$gglshrtlnk_post_ids_converted = 'added_by_direct';
												}
												$wpdb->insert(
											    	$gglshrtlnk_table_name,
											    	array(
											    		'long_url' => $gglshrtlnk_input_links[ $gglshrtlnk_input ],
											    		'short_url' => $gglshrtlnk_short_url[ $gglshrtlnk_output ],
											    		'post_ids' => $gglshrtlnk_post_ids_converted
											    	)
										    	);	
							    		break;
							    	}
								} else {
									/*get a short url from database */
							    	$gglshrtlnk_short_url[ $gglshrtlnk_output ] = $gglshrtlnk_short_url_from_db;						
								}
							} else {
								$gglshrtlnk_input_links[ $gglshrtlnk_input ] = '';
								$gglshrtlnk_short_url[ $gglshrtlnk_output ] = '';
							} 		
							} ?>
					<div class="wrap">
					<div class="icon32 icon32-bws" id="icon-options-general"></div>
						<h2><?php _e( 'Google Shortlink', 'google-shortlink' ) ?></h2>
						<?php if ( isset( $gglshrtlnk_key_invalid ) ) {?>
							<div class="below-h2 error">
								<p>
									<?php echo __( "Invalid API key. Go to plugin's", 'google-shortlink') . ' <a href="' . admin_url('admin.php?page=gglshrtlnk_options','') . '">' . __( 'settings page', 'google-shortlink') . '</a> ' . __('and enter correct key.', 'google-shortlink' ); ?>
								</p>
							</div>
						<?php } ?>
							<div class="below-h2 updated gglshrtlnk_hide" id="gglshrtlnk_no_more_fields">
								<p>
									<?php _e( "There are empty fields on the page. Fill them out before adding another one.", 'google-shortlink'); ?>
								</p>
							</div>							
						<h2 class="nav-tab-wrapper">
							<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink','')?>"><?php _e( 'Table of links', 'google-shortlink' ); ?></a>
							<a class="nav-tab nav-tab-active" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=direct','')?>"><?php _e( 'Direct input', 'google-shortlink' ); ?></a>
							<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=all','')?>"><?php _e( 'Additional options', 'google-shortlink' ); ?></a>
							<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=faq','')?>"><?php _e( 'FAQ', 'google-shortlink' ); ?></a>
						</h2>
						<!-- Direct input form -->
						<form method="post" name="gglshrtlnk_direct-input-form" action="" class="gglshrtlnk_direct-input">
							<table class="gglshrtlnk_table">
								<tbody>
									<tr valign="top">
										<th scope="row" class="gglshrtlnk_main-th">
											<?php _e( 'Get short links by direct input:', 'google-shortlink' ) ?>
										</th>
										<td> 
											<table id="gglshrtlnk_direct-input-table" cellspacing="0">
												<tbody>
													<tr>
														<td class="gglshrtlnk_long-link-column"><?php _e( 'Type long links here:','google-shortlink' ) ?></td>
														<td class="gglshrtlnk_short-link-column"><?php _e( 'Short links will appear below:', 'google-shortlink' ) ?></td>
													</tr>
													<!-- Creating table for direct input -->
													<?php for ( $i = 1; $i < $gglshrtlnk_number_of_input_links + 1; $i++ ) { 
														$gglshrtlnk_input = "gglshrtlnk_url-input-" . $i;
														$gglshrtlnk_output = "gglshrtlnk_url-output-" . $i;?> 
														<tr valign="top">
															<td class="gglshrtlnk_long-link-column"><input type="url" name="<?php echo $gglshrtlnk_input; ?>"  value="<?php echo $gglshrtlnk_input_links[ $gglshrtlnk_input ]; ?>" /></td>
															<td class="gglshrtlnk_short-link-column"><input type="url" name="<?php echo $gglshrtlnk_output; ?>" readonly value="<?php echo $gglshrtlnk_short_url[ $gglshrtlnk_output ]; ?>" /></td>
														</tr>
													<?php } ?>										
												</tbody>
											</table>
										</td>
									</tr>
									<tr valign="top" >
										<td colspan="3">
											<input type="hidden" name="gglshrtlnk_number_of_input_links" id="gglshrtlnk_number_of_input_links" value="<?php echo $gglshrtlnk_number_of_input_links; ?>">
											<input type="hidden" name="gglshrtlnk_direct_was_send" value="send">
											<input type="submit" name="gglshrtlnk_submit-direct-input" class="button-primary alignleft>" value="<?php esc_attr_e( 'Get short links', 'google-shortlink' ) ?>" />
											<input type="button" name="gglshrtlnk_reset-direct-input" class="button-primary alignleft>" id="reset-direct"value="<?php esc_attr_e( 'Reset form', 'google-shortlink' ) ?>" />
											<input type="button" value="<?php esc_attr_e( 'Add field', 'google-shortlink' ) ?>" class="button-primary" id="gglshrtlnk_add-field-button"> </input>
											<?php wp_nonce_field( 'gglshrtlnk_dir-noonce-action', 'gglshrtlnk_dir-noonce-field' ); ?>	
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>
				<?php break;
				case 'all':
					/*
					* Actions with links part
					*/
					/*check if db is empty */
					$gglshrtlnk_total_items = $wpdb->get_var(
						"SELECT COUNT(*)
						FROM $gglshrtlnk_table_name
						"
					);
					if ( isset( $_POST[ 'gglshrtlnk_actions-with-links-was-send' ] ) && check_admin_referer( 'gglshrtlnk_act-noonce-action', 'gglshrtlnk_act-noonce-field' ) ) {
						gglshrtlnk_ajax_additional_opt_callback();
					} ?>
					<div class="wrap">
						<div class="icon32 icon32-bws" id="icon-options-general"></div>
						<h2><?php _e( 'Google Shortlink', 'google-shortlink' ) ?></h2>
						<div class="results below-h2 gglshrtlnk_hide updated" id="gglshrtlnk_ajax-status"></div>
						<?php if ( isset( $_POST[ 'gglshrtlnk_actions-with-links-was-send' ] ) && check_admin_referer( 'gglshrtlnk_act-noonce-action', 'gglshrtlnk_act-noonce-field' ) ) {?>		
							<div class="updated fade below-h2" >
								<p>
									<?php if ( isset( $_POST['gglshrtlnk_actions_with_links_radio'] ) && $_POST['gglshrtlnk_actions_with_links_radio'] != 'none' ) {
											if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'delete-all-radio' ) {
												_e( 'All links from database have been restored to long links and the database has been cleared', 'google-shortlink' );
											}
											if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'restore-all' ) {
												_e( 'All links from database have been restored to long links', 'google-shortlink' );
											}
											if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'replace-all' ) {
												_e( 'All links from database have been replaced with short links', 'google-shortlink' );
											}
											if ( $_POST['gglshrtlnk_actions_with_links_radio'] == 'scan' ) {
												_e( 'Web-site was scanned for new links,', 'google-shortlink' );
												if ( 0 != $gglshrtlnk_links_number ) {
													echo " " . $gglshrtlnk_links_number . " ";
													_e( 'links were added to db.', 'google-shortlink' );
												} else {
													echo " " . __( 'no new links found.', 'google-shortlink' );
												}
											}
										}?>
								</p>
							</div>
						<?php } ?>							
						<h2 class="nav-tab-wrapper">
							<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink','')?>"><?php _e( 'Table of links', 'google-shortlink' ); ?></a>
							<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=direct','')?>"><?php _e( 'Direct input', 'google-shortlink' ); ?></a>
							<a class="nav-tab nav-tab-active" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=all','')?>"><?php _e( 'Additional options', 'google-shortlink' ); ?></a>
							<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=faq','')?>"><?php _e( 'FAQ', 'google-shortlink' ); ?></a>
						</h2>
						<!-- ACTIONS WITH LINKS FORM -->
						<form method="post" name="gglshrtlnk_actions-with-links" id="gglshrtlnk_actions-with-links" action="" class="gglshrtlnk_auto-replace">	
							<table class="gglshrtlnk_table">
								<tbody>
									<tr valign="top">
										<th scope ="row" class="gglshrtlnk_main-th">
											<?php _e( 'Actions with all links:', 'google-shortlink' ) ?>
										</th>
										<td>
											<table id="gglshrtlnk_all-actions">
												<tbody>
													<td scope ="row" class="gglshrtlnk_actions-all-div">
														<!-- scan web-site to find all external links -->
															<label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="scan" id="gglshrtlnk_scan" checked /> <?php _e( 'Scan web-site for new external links', 'google-shortlink' ); ?> </label><br />
														<!-- replace automatically -->
															<label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="replace-all" id="gglshrtlnk_replace-all"<?php if ( $gglshrtlnk_total_items == 0 ) { echo 'disabled="disabled"'; } ?>/> <?php _e( 'Replace automatically all external links', 'google-shortlink' ); ?> </label><br />
														<!-- restore all -->
															<label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="restore-all" id="gglshrtlnk_restore-all"<?php if ( $gglshrtlnk_total_items == 0 ) { echo 'disabled="disabled"'; } ?>/> <?php _e( 'Restore automatically all external links', 'google-shortlink' ); ?> </label><br />
														<!-- delete all -->
															<label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="delete-all-radio" id="gglshrtlnk_delete-all-radio"<?php if ( $gglshrtlnk_total_items == 0 ) { echo 'disabled="disabled"'; } ?>/> <?php _e( 'Restore all links and clear database', 'google-shortlink' ); ?></label><br />
													</td >
												</tbody>
											</table>
										</td>						
									</tr>
									<tr>
										<td colspan="2">
											<input class="button-primary" value="<?php _e( 'Apply', 'google-shortlink' ); ?>" type="submit" name="gglshrtlnk_apply_button3" id="gglshrtlnk_apply_button3" />
											<input type="hidden" name="gglshrtlnk_actions-with-links-was-send" value="send">
											<?php wp_nonce_field( 'gglshrtlnk_act-noonce-action', 'gglshrtlnk_act-noonce-field' ); ?>
										</td>
									</tr>
								</tbody>
							</table>
						</form><!-- actions with links -->							
					</div>
			<?php break;
			case 'faq':?>
			<div class="wrap">
				<div class="icon32 icon32-bws" id="icon-options-general"></div>
				<h2><?php _e( 'Google Shortlink', 'google-shortlink' ) ?></h2>
				<h2 class="nav-tab-wrapper">
					<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink','')?>"><?php _e( 'Table of links', 'google-shortlink' ); ?></a>
					<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=direct','')?>"><?php _e( 'Direct input', 'google-shortlink' ); ?></a>
					<a class="nav-tab" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=all','')?>"><?php _e( 'Additional options', 'google-shortlink' ); ?></a>
					<a class="nav-tab nav-tab-active" href="<?php echo admin_url('admin.php?page=google-shortlink&tab=faq','')?>"><?php _e( 'FAQ', 'google-shortlink' ); ?></a>
				</h2>
				<h3><?php _e( 'How to get API key', 'google-shortlink' ); ?></h3>
				<p>
					<?php _e( 'To get API key you must go to', 'google-shortlink' ); ?>
					<a href="https://code.google.com/apis/console" target="_blank">Google Api Console</a>.
					<?php echo __( 'Then go to "Projects" tab and press "Create project" button.', 'google-shortlink' ) .'<br/>';?>
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_1.png', __FILE__ ); ?>" /><br/>
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_2.png', __FILE__ ); ?>" /><br/>
					<?php echo __( 'After that you will be redirected to project page. Go to "API and auth" tab. Make sure "URL Shortener API" turn on.' , 'google-shortlink' ) . '<br/>';?>
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_3.png', __FILE__ ); ?>" /><br/>
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_4.png', __FILE__ ); ?>" /><br/>
					<?php echo __( 'Then go to "Credentials" tab. At "Public API access" section of the page click "Create new key" button.', 'google-shortlink' ) . '<br/>';?>
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_5.png', __FILE__ ); ?>" /><br/>
					<?php echo __( 'In a popup window, that will appear choose "Browser key".','google-shortlink ') .' <b>' . __( 'Do not fill', 'google-shortlink') .'</b> ' . __( '"referers" field in the next popup window and click "Create" button.', 'google-shortlink' ) . '<br />'; ?>
					<?php echo __( 'It is important not to fill "referers" field. It may cause "Acces not configured" error, it is highly recomended to leave this field empty for correct work.', 'google-shortlink' ) . '<br />';?>	
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_6.png', __FILE__ ); ?>" /><br/>
					<?php echo __( 'After all you will see created API key at the page, just copy and paste it to the field below and enjoy the plugin.', 'google-shortlink' ) . '<br/>'; ?>
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_7.png', __FILE__ ); ?>" /><br/>
					<?php _e( 'If the interface on your Google Api Console differs from the given screenshots, you may probably have not switched to the new interface.', 'google-shortlink' ); ?><br/>
					<img class="gglsrtlnk_img" src="<?php echo plugins_url( 'images/faq_8.png', __FILE__ ); ?>" /><br/>
				</p>
				<h3><?php _e( 'I have an error!', 'google-shortlink' ); ?></h3>
				<h4><?php _e( 'Access not configured', 'google-shortlink' ); ?></h4>
				<img class="gglsrtlnk_img gglsrtlnk_img_error" src="<?php echo plugins_url( 'images/error_1.png', __FILE__ ); ?>" /><br/>
				<p>
					<?php echo __( "This error occurs in two cases:", 'google-shortlink' ) . '<br/>' ?>
					<ol>
						<li>
							<p>
								<?php echo __( '"URL Shortener api" is turned off at your project API options at google api console.', 'google-shortlink' ) . '<br />';
									echo __( 'To fix this just go to api options of your project and set "URL Shortener api" on. After that in some cases you will have to ceate a new API key, or recreate the current one.', 'google-shortlink' ); ?>
							</p>
						</li>
						<li>
							<p>
								<?php echo __( 'The "referers" field at public API key options is not empty.', 'google-shortlink' ) . "<br />";
								echo __( 'To fix this you need to clear "referers" field and recreate API key.', 'google-shortlink' ); ?>
							</p>
						</li>						
					</ol>
				</p>						
				<h4><?php _e( 'Invalid API key', 'google-shortlink' ); ?></h4>
				<img class="gglsrtlnk_img gglsrtlnk_img_error" src="<?php echo plugins_url( 'images/error_2.png', __FILE__ ); ?>" /><br/>			
				<p>
					<?php _e( "This error occurs if you entered incorrect API key on plugin's settings page. Go to google api console, copy public API key there and paste it to the field on plugin's settings page.", 'google-shortlink' ) ?>
				</p>
				<h4><?php _e( 'Expired API key', 'google-shortlink' ); ?></h4>
				<img class="gglsrtlnk_img gglsrtlnk_img_error" src="<?php echo plugins_url( 'images/error_3.png', __FILE__ ); ?>" /><br/>			
				<p>
					<?php _e( "This error occurs if your API key is outdate or it is a newly created one. Go to google api console, create a new public API key there and paste it to the field on the plugin's options page in the first case, or just wait a few minutes in the second case..", 'google-shortlink' ) ?>
				</p>
															
			</div>			
			<?php break;					
			}
		} ?>	
<?php }
}

/*function for getting short links from direct input */
if ( ! function_exists( 'gglshrtlnk_get' ) ) {
	function gglshrtlnk_get( $long_url ) {
		/* api key for application */
		$gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
		$gglshrtlnk_api_key = $gglshrtlnk_options[ 'api_key' ];
		/* encoding data to json */
		$gglshrtlnk_post_data = array( 'longUrl' => $long_url );
		$gglshrtlnk_json_data = json_encode( $gglshrtlnk_post_data );
		/* create curl object */
		$gglshrtlnk_curl_obj = curl_init();
		 curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $gglshrtlnk_api_key );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_HEADER, 0 );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_HTTPHEADER, array( 'Content-type:application/json' ) );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_POST, 1 );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_POSTFIELDS, $gglshrtlnk_json_data );
		/*execute curl */
		$gglshrtlnk_response = curl_exec( $gglshrtlnk_curl_obj );
		if ( $gglshrtlnk_response === false ) {
			$gglshrtlnk_return = 'curl_error';
			curl_close( $gglshrtlnk_curl_obj );
			return $gglshrtlnk_return;
		} else {
			/*decoding json response */
			$gglshrtlnk_json = json_decode( $gglshrtlnk_response );
			curl_close( $gglshrtlnk_curl_obj );
			if ( isset( $gglshrtlnk_json->id ) ) {
				$gglshrtlnk_short_url = $gglshrtlnk_json->id;
				return $gglshrtlnk_short_url;
			} elseif ( isset( $gglshrtlnk_json->error ) ) {
				switch ( $gglshrtlnk_json->error->errors[0]->reason ) {
					case 'keyInvalid':
						return 'keyInvalid';
					break;
					case 'invalid':
						return 'invalid';
					break;	
					case 'accessNotConfigured':
						return 'accessNotConfigured';
					break;
					case 'keyExpired':
						return 'keyExpired';
					break;					
					default:
						return 'unknown_error';
					break;
				}
			}
		}
	}
}

/*function for getting total clicks on short link */
if ( ! function_exists( 'gglshrtlnk_count' ) ) {
	function gglshrtlnk_count( $gglshrtlnk_short_url ) {
		/* api key for application */
		$gglshrtlnk_options =  get_option( 'gglshrtlnk_options' );
		$gglshrtlnk_api_key = $gglshrtlnk_options['api_key'];
		/* create curl object */
		$gglshrtlnk_curl_obj = curl_init();
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?shortUrl='. $gglshrtlnk_short_url .'&projection=ANALYTICS_CLICKS&fields=analytics,status&key=' . $gglshrtlnk_api_key );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_HEADER, 0 );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_HTTPHEADER, array( 'Content-type:application/json' ) );
		curl_setopt( $gglshrtlnk_curl_obj, CURLOPT_POST, 0 );
		/*execute curl */
		$gglshrtlnk_response = curl_exec( $gglshrtlnk_curl_obj );
		if ( $gglshrtlnk_response === false ) {
			$gglshrtlnk_return = 'curl_error';
			return $gglshrtlnk_return;
		} else {
			/*decoding json response */
			$gglshrtlnk_json = json_decode( $gglshrtlnk_response );
			curl_close( $gglshrtlnk_curl_obj );
			if ( isset( $gglshrtlnk_json->status )  && $gglshrtlnk_json->status == 'OK' ) {
				$gglshrtlnk_clicks = $gglshrtlnk_json->analytics->allTime->shortUrlClicks;
				return $gglshrtlnk_clicks;
			} else {
				if ( isset( $gglshrtlnk_json->status ) ) {
					$gglshrtlnk_return = __( 'Link status: ', 'google-shortlink' ) . $gglshrtlnk_json->status;
					return $gglshrtlnk_return;
				} elseif ( isset( $gglshrtlnk_json->error ) ) {
					switch ( $gglshrtlnk_json->error->errors[0]->reason ) {
						case 'keyInvalid':
							return 'keyInvalid';
						break;
						case 'keyExpired':
							return 'keyExpired';
						break;						
						case 'invalid':
							return 'invalid';
						break;	
						case 'accessNotConfigured':
							return 'accessNotConfigured';
						break;				
						default:
							return 'unknown_error';
						break;
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'gglshrtlnk_action_links' ) ) {
	function gglshrtlnk_action_links( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
		if ( $file == $this_plugin ){
			$settings_link = '<a href="admin.php?page=gglshrtlnk_options">' . __( 'Settings', 'google-shortlink' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
}

if ( ! function_exists( 'gglshrtlnk_links' ) ) {
	function gglshrtlnk_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$links[]	=	'<a href="admin.php?page=gglshrtlnk_options">' . __( 'Settings', 'google-shortlink' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/google-shortlink/faq/" target="_blank">' . __( 'FAQ', 'google-shortlink' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google-shortlink' ) . '</a>';
		}
		return $links;
	}
}

/* function for delete options */
if ( ! function_exists( 'gglshrtlnk_delete_options' ) ) {
	function gglshrtlnk_delete_options() {
		global $wpdb, $gglshrtlnk_table_name;
		$wpdb->query( "DROP TABLE `" . $gglshrtlnk_table_name . "`;" );
		delete_option( 'gglshrtlnk_options' );
		delete_site_option( "gglshrtlnk_options" );
	}
}

/*hook for activation plugin */
register_activation_hook( __FILE__, 'gglshrtlnk_create_table' );

/*hook for add menu */
add_action( 'admin_menu', 'gglshrtlnk_menu' ); 

add_action( 'init', 'gglshrtlnk_init' );
add_action( 'admin_init', 'gglshrtlnk_admin_init' );

/*hook for scripts and styles */
add_action( 'admin_enqueue_scripts', 'gglshrtlnk_script_style' );

add_action( 'admin_footer', 'gglshrtlnk_additional_opt_javascript' );
add_action( 'admin_footer', 'gglshrtlnk_total_clicks_javascript' );
/*hooks for ajax on additional options tab */
add_action( 'wp_ajax_additional_opt', 'gglshrtlnk_ajax_additional_opt_callback' );
/*hooks for ajax to get total clicks */
add_action( 'wp_ajax_total_clicks', 'gglshrtlnk_ajax_total_clicks_callback' );
/*hook for plugin links */
add_filter( 'plugin_action_links', 'gglshrtlnk_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglshrtlnk_links', 10, 2 );
/*hook for uninstalling plugin */
register_uninstall_hook( __FILE__, 'gglshrtlnk_delete_options' );
?>