<?php
/*
Plugin Name: Shortlink by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/google-shortlink/
Description: Replace external WordPress website links with Google shortlinks and track click stats.
Author: BestWebSoft
Text Domain: google-shortlink
Domain Path: /languages
Version: 1.5.8
Author URI: https://bestwebsoft.com
License: GPLv2 or later
*/

/*  © Copyright 2020  BestWebSoft  ( https://support.bestwebsoft.com )

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

		$hook = add_menu_page( 'Shortlink', 'Shortlink', 'manage_options', 'google-shortlink', 'gglshrtlnk_page', 'none', '55.1' );

		$settings = add_submenu_page( 'google-shortlink', __( 'Shortlink Settings', 'google-shortlink' ), __( 'Settings', 'google-shortlink' ), 'manage_options', 'gglshrtlnk_options', 'gglshrtlnk_options_page' );

		add_submenu_page( 'google-shortlink', 'BWS Panel', 'BWS Panel', 'manage_options', 'gglshrtlnk-bws-panel', 'bws_add_menu_render' );

		add_action( 'load-' . $settings, 'gglshrtlnk_add_tabs' );
		add_action( 'load-' . $hook, 'gglshrtlnk_add_tabs' );
	}
}

/**
 * Internationalization
 */
if ( ! function_exists( 'gglshrtlnk_plugins_loaded' ) ) {
	function gglshrtlnk_plugins_loaded() {
		load_plugin_textdomain( 'google-shortlink', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists( 'gglshrtlnk_init' ) ) {
	function gglshrtlnk_init() {
		global $gglshrtlnk_plugin_info;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $gglshrtlnk_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglshrtlnk_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version  */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $gglshrtlnk_plugin_info, '4.5' );

		if ( ! is_admin() || ( isset( $_REQUEST['page'] ) && ( 'google-shortlink' == $_REQUEST['page'] || 'gglshrtlnk_options' == $_REQUEST['page'] ) ) ) {
		    register_gglshrtlnk_options();
		}
	}
}

if ( ! function_exists( 'gglshrtlnk_session_start' ) ) {
    function gglshrtlnk_session_start() {
        $session = session_id();
        if ( ! $session ) {
            session_start();
        }
    }
}

if ( ! function_exists( 'gglshrtlnk_admin_init' ) ) {
	function gglshrtlnk_admin_init() {
		global $bws_plugin_info, $gglshrtlnk_plugin_info;

		/* Add variable for bws_menu */
		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '115', 'version' => $gglshrtlnk_plugin_info["Version"] );
	}
}

/*function for register default settings*/
if ( ! function_exists( 'register_gglshrtlnk_options' ) ) {
	function register_gglshrtlnk_options() {
		global $gglshrtlnk_options, $gglshrtlnk_plugin_info;

		$db_version = '1.1';

		/* add options to database */
		if ( ! get_option( 'gglshrtlnk_options' ) ) {
			$default_options = gglshrtlnk_get_options_default();
			add_option( 'gglshrtlnk_options', $default_options );
		}

		/* get options from database to operate with them */
		$gglshrtlnk_options = get_option( 'gglshrtlnk_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $gglshrtlnk_options['plugin_option_version'] ) || $gglshrtlnk_options['plugin_option_version'] != $gglshrtlnk_plugin_info["Version"] ) {
			$default_options = gglshrtlnk_get_options_default();
			$default_options['display_settings_notice'] = 0;
			$gglshrtlnk_options = array_merge( $default_options, $gglshrtlnk_options );
			$gglshrtlnk_options['plugin_option_version'] = $gglshrtlnk_plugin_info["Version"];
			$update_option = true;
		}

		/* create or update db table */
		if ( ! isset( $gglshrtlnk_options['plugin_db_version'] ) || $gglshrtlnk_options['plugin_db_version'] != $db_version ) {
			gglshrtlnk_create_table();
			gglshrtlnk_update_db();
			$gglshrtlnk_options['plugin_db_version'] = $db_version;
			$update_option = true;
		}

		if ( isset( $update_option ) )
			update_option( 'gglshrtlnk_options', $gglshrtlnk_options );
	}
}

if ( ! function_exists( 'gglshrtlnk_get_options_default' ) ) {
	function gglshrtlnk_get_options_default() {
		global $gglshrtlnk_plugin_info;

		$default_options = array(
			'plugin_option_version' 	=> $gglshrtlnk_plugin_info["Version"],
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1,
			/* end general options */
			'api_key' 					=> '',
			'pagination' 				=> '10',			
			'firebase_api_is_on'		=> 0,
			'api_key_for_firebase' 		=> '',
			'domain_link' 				=> '',
			'client_id' 				=> '',
			'client_secret' 			=> '',
			'redirect_uri'				=> ''			
		);

		return $default_options;
	}
}

/*function for create a new table in db*/
if ( ! function_exists( 'gglshrtlnk_create_table' ) ) {
	function gglshrtlnk_create_table() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$gglshrtlnk_sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "google_shortlink` (
			`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`long_url` VARCHAR(2048) NOT NULL,
			`short_url` VARCHAR(50) NOT NULL,
			`post_ids` LONGTEXT,
			PRIMARY KEY (`id`)
		)
		ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		dbDelta( $gglshrtlnk_sql );
	}
}

/*function for updating table if that's one has other options*/
if (! function_exists( 'gglshrtlnk_update_db' ) ) {
	function gglshrtlnk_update_db() {
		global $wpdb;

		/*columns and charset were changed at the same date. So we check only one column*/
		$column_type = $wpdb->get_var( "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = '" . $wpdb->prefix . "google_shortlink' AND COLUMN_NAME = 'post_ids';" );
		$column_type = strtolower( $column_type );

		if ( 'longtext' != $column_type ) {

			$wpdb->query( "ALTER TABLE `" . $wpdb->prefix . "google_shortlink`
				MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				MODIFY COLUMN `post_ids` LONGTEXT,
				MODIFY COLUMN `long_url` VARCHAR(2048) NOT NULL,
				ENGINE=InnoDB DEFAULT CHARSET=utf8;" );
		}
	}
}

/*function for adding styles and scripts*/
if( ! function_exists( 'gglshrtlnk_script_style' ) ) {
	function gglshrtlnk_script_style() {
		wp_enqueue_style( 'gglshrtlnk_icon', plugins_url( 'css/admin_page.css', __FILE__ ) );
		
		if ( isset( $_REQUEST['page'] ) && ( 'google-shortlink' == $_REQUEST['page'] || 'gglshrtlnk_options' == $_REQUEST['page'] ) ) {
			wp_enqueue_style( 'gglshrtlnk_styles', plugins_url( 'css/style.css', __FILE__ ) );

			wp_enqueue_script( 'gglshrtlnk_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
			wp_localize_script( 'gglshrtlnk_script', 'gglshrtlnk_vars', array(
				'gglshrtlnk_delete_fromdb_message' 	=> __( "Do you really want to delete all links from database?", 'google-shortlink' ),
				'gglshrtlnk_ajax_nonce' 			=> wp_create_nonce( 'gglshrtlnk_ajax_nonce_value' ),
				'gglshrtlnk_replace_all' 			=> __( "Replacing long links with short...", 'google-shortlink' ),
				'gglshrtlnk_restore_all' 			=> __( "Replace short links to long...", 'google-shortlink' ),
				'gglshrtlnk_delete_all_radio' 		=> __( "Restoring all links and deleting them from the database", 'google-shortlink' ),
				'gglshrtlnk_scan' 					=> __( "Scanning website....", 'google-shortlink' )
			) );
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
	}
}

/*callback for ajax function for total clicks*/
if ( ! function_exists( 'gglshrtlnk_ajax_total_clicks_callback' ) ) {
	function gglshrtlnk_ajax_total_clicks_callback() {
	    global $gglshrtlnk_options;
		check_ajax_referer( 'gglshrtlnk_ajax_nonce_value', 'gglshrtlnk_nonce' );

		if ( empty( $gglshrtlnk_options ) ) {
		    $gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
		}

		if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
			$gglshrtlnk_info_link = str_replace( 'goo.gl/', 'goo.gl/info/', $_POST['gglshrtlnk_short_to_count'] );
			$gglshrtlnk_count_var = gglshrtlnk_count( $_POST['gglshrtlnk_short_to_count'] );
			if ( is_wp_error( $gglshrtlnk_count_var ) ) {
				echo '<br />' . $gglshrtlnk_count_var->get_error_message();
				die();
			} else {
				echo $gglshrtlnk_count_var . '<br /><a target="_blank" href="' . $gglshrtlnk_info_link . '">( ' . __( 'more details', 'google-shortlink' ) . ' )</a>';
				die();
			}
        } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
			$gglshrtlnk_count_var = gglshrtlnk_count( $_POST['gglshrtlnk_short_to_count'] );
			if ( is_wp_error( $gglshrtlnk_count_var ) ) {
				echo '<br />' . $gglshrtlnk_count_var->get_error_message();
				die();
			} else {
				echo $gglshrtlnk_count_var;
				die();
			}
        }

	}
}

/* callback for ajax function for additional options */
if ( ! function_exists( 'gglshrtlnk_ajax_additional_opt_callback' ) ) {
	function gglshrtlnk_ajax_additional_opt_callback( $no_js = false ) {
		global $wpdb, $gglshrtlnk_links_number, $gglshrtlnk_options;

		if ( empty( $gglshrtlnk_options ) ) {
		    $gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
        }
		$failed_links = 0;
		$failed_links_list = array();
		$failed_links_message = '';
		$gglshrtlnk_rows_to_restore = '';

		if ( ! $no_js ) {
			check_ajax_referer( 'gglshrtlnk_ajax_nonce_value', 'gglshrtlnk_nonce' );
		}

		$result = array( 'message' => '', 'error' => '', 'error_message' => '' );

		$gglshrtlnk_links_number = 0;
		/* actions with all links part */
        if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	        $gglshrtlnk_rows_to_restore = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "google_shortlink` ", ARRAY_A );
        } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	        $gglshrtlnk_rows_to_restore = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "google_shortlink_for_firebase` ", ARRAY_A );
        }

		switch ( $_POST['gglshrtlnk_actions_with_links_radio']  ) {
			/*if need to restore all links and clear links table*/
			case 'delete-all-radio' :
				/*restore all links before deleting*/
				foreach ( $gglshrtlnk_rows_to_restore as $gglshrtlnk_row_to_action ) {
					if ( 'added_by_direct' != $gglshrtlnk_row_to_action['post_ids'] ) {
						gglshrtlnk_restore_one( $gglshrtlnk_row_to_action );
					}
				}
				/*clear db*/
            if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	            $wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "google_shortlink`" );
            } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	            $wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "google_shortlink_for_firebase`" );
            }


				$result['message'] = __( 'All long links from the database have been restored  and the database has been cleared.', 'google-shortlink' );
			break;
			/*if need only to restore all links	*/
			case 'restore-all':
				foreach ( $gglshrtlnk_rows_to_restore as $gglshrtlnk_row_to_action ) {
					if ( 'added_by_direct' != $gglshrtlnk_row_to_action['post_ids'] ) {
						gglshrtlnk_restore_one( $gglshrtlnk_row_to_action );
					}
				}
				$result['message'] = __( 'All long links from the database have been restored.', 'google-shortlink' ) . '<br />' .
					__( 'Total replaces:', 'google-shortlink' ) . " " . $gglshrtlnk_links_number;
			break;
			/*if need only to replace all links	*/
			case 'replace-all':
				foreach ( $gglshrtlnk_rows_to_restore as $gglshrtlnk_row_to_action ) {
					if ( 'added_by_direct' != $gglshrtlnk_row_to_action['post_ids'] ) {
						gglshrtlnk_replace_one( $gglshrtlnk_row_to_action );
					}
				}
				$result['message'] = __( 'All links from the database have been replaced.', 'google-shortlink' ) . '<br />' .
					__( 'Total replaces:', 'google-shortlink' ) . " " . $gglshrtlnk_links_number;
			break;
			/*if need to scan the site for new links*/
			case 'scan':
				$gglshrtlnk_get_all_posts = get_post_types( '', 'names' );
				unset( $gglshrtlnk_get_all_posts['revision'] );
				unset( $gglshrtlnk_get_all_posts['attachment'] );
				unset( $gglshrtlnk_get_all_posts['nav_menu_item'] );
				/* get post contents from db*/
				$gglshrtlnk_post_contents = $wpdb->get_results( "SELECT `post_content`, `ID`, `post_type` FROM `$wpdb->posts` WHERE `post_type` IN ( '" . implode( "', '", array_keys( $gglshrtlnk_get_all_posts ) ) . "' ) ORDER BY `ID`", ARRAY_A );
				foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_currentpost ) {
					/* find all links in posts and pages */

					preg_match_all( '~(http|https|ftp)://[^#]([^\'\"\s\r\n\t<>])+~', $gglshrtlnk_currentpost['post_content'], $gglshrtlnk_out );

					if ( empty( $gglshrtlnk_out[0] ) )
						continue;

					/*filter links from goo.gl or domain link and home_url */
					foreach ( $gglshrtlnk_out[0] as $gglshrtlnk_link ) {
					    if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {

						    if ( false === strpos( $gglshrtlnk_link, 'https://goo.gl' )
						         && false === strpos( $gglshrtlnk_link, home_url() ) ) {
							    /*check is link already in db */
							    $gglshrtlnk_link_from_db = $wpdb->get_results( $wpdb->prepare(
								    "SELECT `long_url`
								FROM `" . $wpdb->prefix . "google_shortlink`
								WHERE `long_url` = '%s'
								LIMIT 1;
								", $gglshrtlnk_link
							    ) );
							    /* add new link to db if it not exist */
							    if ( ! $gglshrtlnk_link_from_db ) {
								    $gglshrtlnk_short_url = gglshrtlnk_get( $gglshrtlnk_link );

								    if ( is_wp_error( $gglshrtlnk_short_url ) ) {
									    $result['error'] = '<b>' . __( 'Error:', 'google-shortlink' ) . '</b> ' . $gglshrtlnk_short_url->get_error_message();
									    continue;
								    } else {
									    /*find post ids for new link */
									    $gglshrtlnk_post_ids = array();
									    foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_is_in_post ) {
										    $gglshrtlnk_position = strpos( $gglshrtlnk_is_in_post['post_content'], $gglshrtlnk_link );
										    if ( false !== $gglshrtlnk_position ) {
											    $gglshrtlnk_post_ids[] = $gglshrtlnk_is_in_post['ID'];
										    }
									    }
									    /* add to database is url is not embedd object */
									    if ( ! empty( $gglshrtlnk_post_ids ) ) {
										    /*convert post ids into db format */
										    $gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );
										    $wpdb->insert(
											    $wpdb->prefix . "google_shortlink",
											    array(
												    'long_url'  => $gglshrtlnk_link,
												    'short_url' => $gglshrtlnk_short_url,
												    'post_ids'  => $gglshrtlnk_post_ids_converted
											    )
										    );
										    $gglshrtlnk_links_number ++;
									    }
								    }
							    } else {
								    /* update posts ids for link */
								    $gglshrtlnk_post_ids = array();

								    foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_is_in_post ) {
									    if ( array_key_exists( $gglshrtlnk_is_in_post['post_type'], $gglshrtlnk_get_all_posts ) ) {
										    if ( false !== strpos( $gglshrtlnk_is_in_post['post_content'], $gglshrtlnk_link ) ) {
											    $gglshrtlnk_post_ids[] = $gglshrtlnk_is_in_post['ID'];
										    }
									    }
								    }
								    /*convert post ids into db format */
								    $gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );
								    $wpdb->update(
									    $wpdb->prefix . "google_shortlink",
									    array( 'post_ids' => $gglshrtlnk_post_ids_converted ),
									    array( 'long_url' => $gglshrtlnk_link ),
									    array( '%s' ),
									    array( '%s' )
								    );
							    }
						    } elseif ( false === strpos( $gglshrtlnk_link, $_SERVER['HTTP_HOST'] ) ) {
							    $failed_links_list[ $failed_links ] = $gglshrtlnk_link;
							    $failed_links ++;

						    }
					    } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
						    if ( false === strpos( $gglshrtlnk_link,  $gglshrtlnk_options['domain_link'] )
						         && false === strpos( $gglshrtlnk_link, home_url() ) ) {
							    /*check is link already in db */
							    $gglshrtlnk_link_from_db = $wpdb->get_results( $wpdb->prepare(
								    "SELECT `long_url`
								FROM `" . $wpdb->prefix . "google_shortlink_for_firebase`
								WHERE `long_url` = '%s'
								LIMIT 1;
								", $gglshrtlnk_link
							    ) );
							    /* add new link to db if it not exist */
							    if ( ! $gglshrtlnk_link_from_db ) {
								    $gglshrtlnk_short_url = gglshrtlnk_get( $gglshrtlnk_link );

								    if ( is_wp_error( $gglshrtlnk_short_url ) ) {
									    $result['error'] = '<b>'. __( 'Error:', 'google-shortlink' ) . '</b> ' . $gglshrtlnk_short_url->get_error_message();
									    continue;
								    } else {
									    /*find post ids for new link */
									    $gglshrtlnk_post_ids = array();
									    foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_is_in_post ) {
										    $gglshrtlnk_position = strpos( $gglshrtlnk_is_in_post['post_content'], $gglshrtlnk_link );
										    if ( false !== $gglshrtlnk_position ) {
											    $gglshrtlnk_post_ids[] = $gglshrtlnk_is_in_post['ID'];
										    }
									    }
									    /* add to database is url is not embedd object */
									    if ( ! empty( $gglshrtlnk_post_ids ) ) {
										    /*convert post ids into db format */
										    $gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );
										    $wpdb->insert(
											    $wpdb->prefix . "google_shortlink_for_firebase",
											    array(
												    'long_url' => $gglshrtlnk_link,
												    'short_url' => $gglshrtlnk_short_url,
												    'post_ids' => $gglshrtlnk_post_ids_converted
											    )
										    );
										    $gglshrtlnk_links_number++;
									    }
								    }
							    } else {
								    /* update posts ids for link */
								    $gglshrtlnk_post_ids = array();

								    foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_is_in_post ) {
									    if ( array_key_exists( $gglshrtlnk_is_in_post['post_type'], $gglshrtlnk_get_all_posts ) ) {
										    if ( false !== strpos( $gglshrtlnk_is_in_post['post_content'], $gglshrtlnk_link ) ) {
											    $gglshrtlnk_post_ids[] = $gglshrtlnk_is_in_post['ID'];
										    }
									    }
								    }
								    /*convert post ids into db format */
								    $gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );
								    $wpdb->update(
									    $wpdb->prefix . "google_shortlink_for_firebase",
									    array( 'post_ids' => $gglshrtlnk_post_ids_converted ),
									    array( 'long_url' => $gglshrtlnk_link ),
									    array( '%s' ),
									    array( '%s' )
								    );
							    }
						    } elseif ( false === strpos( $gglshrtlnk_link, $_SERVER['HTTP_HOST'] ) ) {
							    $failed_links_list[$failed_links] = $gglshrtlnk_link;
							    $failed_links++;
						    }
                        }
					}
				}
				if ( $failed_links > 0 ) {
					$long_list = "";
					foreach ( $failed_links_list as $list ) {
						$long_list .= "<br>" . $list;
					}
					$failed_links_message = sprintf( _n( '%s link failed:', '%s links failed:', $failed_links, 'google-shortlink' ), $failed_links );
					$result['error_message'] = $failed_links_message;
					$failed_links_message .= $long_list;
					$result['error'] = $failed_links_message;
				}
				if ( empty( $result['error'] ) || $result['error'] == $failed_links_message ) {
					$result['message'] = __( 'Website has been scanned.', 'google-shortlink' );
					if ( 0 != $gglshrtlnk_links_number ) {
						$result['message'] .= " " . $gglshrtlnk_links_number . " " . __( 'links were added to database.', 'google-shortlink' );
					} else {
						$result['message'] .= " " . __( 'There are no new links.', 'google-shortlink' ) . "<br />" . __( 'The list of articles has been updated for each link.', 'google-shortlink' );
					}
				}
			break;
		}
		/* message creating */
		if ( ! $no_js ) {
			echo json_encode( $result );
			die();
		} else
			return $result;
	}
}

/* function for actions part on table of links tab */
if ( ! function_exists( 'gglshrtlnk_actions' ) ) {
	function gglshrtlnk_actions( $gglshrtlnk_action, $gglshrtlnk_id_to_action ) {
		global $wpdb, $gglshrtlnk_options;
		/*select row with short and long db */
		$gglshrtlnk_row_to_action = '';
        if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	        $gglshrtlnk_row_to_action = $wpdb->get_row( $wpdb->prepare(
		        "SELECT *
			FROM `" . $wpdb->prefix . "google_shortlink`
			WHERE `id` = %d
			", $gglshrtlnk_id_to_action
	        ), ARRAY_A );
        } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	        $gglshrtlnk_row_to_action = $wpdb->get_row( $wpdb->prepare(
		        "SELECT *
			FROM `" . $wpdb->prefix . "google_shortlink_for_firebase`
			WHERE `id` = %d
			", $gglshrtlnk_id_to_action
	        ), ARRAY_A );
        }
		/* delete selected links */
		if ( 'delete' == $gglshrtlnk_action ) {
			gglshrtlnk_delete_one( $gglshrtlnk_row_to_action );
		}
		/*check if link in some post */
		if( 'added_by_direct' != $gglshrtlnk_row_to_action['post_ids'] ) {
			/*replace selected long links */
			if ( 'replace' == $gglshrtlnk_action ) {
				gglshrtlnk_replace_one( $gglshrtlnk_row_to_action );
			}
			/*restore selected long links */
			if ( 'restore' == $gglshrtlnk_action ) {
				gglshrtlnk_restore_one( $gglshrtlnk_row_to_action );
			}
		}
	}
}

/* function for replacing one long link */
if ( ! function_exists( 'gglshrtlnk_replace_one' ) ) {
	function gglshrtlnk_replace_one( $gglshrtlnk_row_to_action ) {

		global $wpdb, $gglshrtlnk_links_number;
		try {
			$gglshrtlnk_post_ids = array_filter( unserialize( $gglshrtlnk_row_to_action['post_ids'] ) );

			if( empty( $gglshrtlnk_post_ids ) ) {
				return;
			}

			$gglshrtlnk_post_ids = implode( ",", $gglshrtlnk_post_ids );

			$gglshrtlnk_post_contents = $wpdb->get_results(
				"SELECT `post_content`, `ID`
				FROM `$wpdb->posts`
				WHERE `ID` IN ({$gglshrtlnk_post_ids});",
				ARRAY_A
			);
			foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_one ) {
				/*replace all url's symbols to prevent errors*/
				$pattern = preg_quote( $gglshrtlnk_row_to_action['long_url'] );
				$pattern = preg_replace( '~/$~', '', $pattern );

				/*replace all long links in the content*/
				$gglshrtlnk_one['post_content'] = preg_replace( "~{$pattern}/?(?![-?/\w&])~iu", $gglshrtlnk_row_to_action['short_url'], $gglshrtlnk_one['post_content'] );
				/*update wp_posts */
				$wpdb->update( $wpdb->posts, array( 'post_content' => $gglshrtlnk_one['post_content'] ), array( 'ID' => $gglshrtlnk_one['ID'] ), array( '%s' ), array( '%d' ) );
				/*increase count of replaced links */
				$gglshrtlnk_links_number++;
			}
		} catch( Exception $e ) {
			//
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
			", ARRAY_A
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
		global $wpdb, $gglshrtlnk_links_number, $gglshrtlnk_options;
		if ( 'added_by_direct' != $gglshrtlnk_row_to_action['post_ids'] ) {
			$gglshrtlnk_post_ids = unserialize( $gglshrtlnk_row_to_action['post_ids'] );
			$gglshrtlnk_post_ids = implode(" OR ID = ", $gglshrtlnk_post_ids );
			$gglshrtlnk_post_contents = $wpdb->get_results(
				"SELECT `post_content`, `ID`
				FROM `$wpdb->posts`
				WHERE `ID` = $gglshrtlnk_post_ids
				", ARRAY_A
			);
			foreach ( $gglshrtlnk_post_contents as $gglshrtlnk_one ) {
				$gglshrtlnk_one['post_content'] = str_replace( $gglshrtlnk_row_to_action['short_url'], $gglshrtlnk_row_to_action['long_url'] , $gglshrtlnk_one['post_content'] );
				/*update wp_posts */
				$wpdb->update( $wpdb->posts, array( 'post_content' => $gglshrtlnk_one['post_content'] ), array( 'ID' => $gglshrtlnk_one['ID'] ), array( '%s' ), array( '%d' ) );
			}
		}
		if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "google_shortlink` WHERE `id` = %d", $gglshrtlnk_row_to_action['id'] ) );
		} elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "google_shortlink_for_firebase` WHERE `id` = %d", $gglshrtlnk_row_to_action['id'] ) );
        }

		/*increase count of deleted links */
		$gglshrtlnk_links_number++;
	}
}

/* function for log in with firebase creds */
if ( ! function_exists( 'gglshrtlnk_oauth' ) ) {
	function gglshrtlnk_oauth() {
		global $gglshrtlnk_options;

		if ( empty( $gglshrtlnk_options ) ) {
			$gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
        }

		if ( isset( $_POST['action'] ) && 'gglshrtlnk_oauth' == $_POST['action'] ) {

			$session = session_id();
			if ( empty( $session ) ) {
				session_start();
			}

            $client = gglshrtlnk_create_client();

			$_SESSION['redirect_to'] = admin_url() . 'admin.php?page=google-shortlink';
			$_SESSION['client_id'] = $gglshrtlnk_options['client_id'];
			$_SESSION['client_secret'] = $gglshrtlnk_options['client_secret'];
			$_SESSION['redirect_uri'] = $gglshrtlnk_options['redirect_uri'];

			if ( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
				$client->setAccessToken( $_SESSION['access_token'] );
				$firebase = new Google_Service_FirebaseDynamicLinksAPI( $client );
			} else {
				$redirect_uri = $gglshrtlnk_options['redirect_uri'];

				header( 'Location: ' . filter_var( $redirect_uri, FILTER_SANITIZE_URL ) );
			}

			if ( isset( $_GET['code'] ) ) {
				$client->authenticate( $_GET['code'] );
				$_SESSION['access_token'] = $client->getAccessToken();
			}
		}
	}
}

/* Display settings page */
if ( ! function_exists( 'gglshrtlnk_options_page' ) ) {
	function gglshrtlnk_options_page() {
		global $wpdb, $gglshrtlnk_options;

		if ( isset( $_POST['submit_to_firebase_confirm'] ) && check_admin_referer( 'gglshrtlnk_opt-noonce-action', 'gglshrtlnk_opt-noonce-field' ) ) {		    
	        $gglshrtlnk_options[ 'firebase_api_is_on' ] = 1;
	        update_option( 'gglshrtlnk_options', $gglshrtlnk_options );

	        /* Creating table for firebase api to save links */
	        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	        $gglshrtlnk_sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "google_shortlink_for_firebase` (
		        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		        `long_url` VARCHAR(2048) NOT NULL,
		        `short_url` VARCHAR(50) NOT NULL,
		        `post_ids` LONGTEXT,
		        PRIMARY KEY (`id`)
		        )
		        ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	        dbDelta( $gglshrtlnk_sql );
		} ?>
		<div class="wrap">
			<h1><?php _e( 'Shortlink Settings', 'custom-admin-page' ); ?></h1>
			<?php if ( isset( $_POST['submit_to_firebase'] ) ) { ?>
				<form action="" method="post" class="bws_form">
				    <table class="form-table">
						<td>
							<input type="submit"  class="button button-primary" name="submit_to_firebase_confirm" value="<?php _e( 'Yes, update to Firebase', 'google-shortlink' ); ?>" />
							<input type="submit"  class="button" name="submit_to_firebase_deny" value="<?php _e( 'No, go back to the settings page', 'google-shortlink' ); ?>" />
						</td>
					</table>
					<?php wp_nonce_field( 'gglshrtlnk_opt-noonce-action', 'gglshrtlnk_opt-noonce-field' ); ?>
				</form>
			<?php } else {
				 if ( 1 == $gglshrtlnk_options['firebase_api_is_on'] && ! isset( $_SESSION['access_token'] ) ) { ?>
					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<div class="notice below-h2 notice-warning">
							<p>
					    		<?php _e( 'Please fill all settings and log in to your Google Account', 'google-shortlink' ); ?>
					    	</p>
					    	<p>
					    		<input type="submit" name="gglshrtlnk_oauth" class="button button-secondary" value="<?php _e( 'Log in to your Google Account', 'google-shortlink' ); ?>"/>
					    		<input type="hidden" name="action" value="gglshrtlnk_oauth">
					    	</p>
					    </div>						
					</form>
				<?php }
				if ( ! class_exists( 'Bws_Settings_Tabs' ) )
		            require_once( dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php' );
		        require_once( dirname( __FILE__ ) . '/includes/class-gglshrtlnk-settings.php' );
				$page = new Gglshrtlnk_Settings_Tabs( plugin_basename( __FILE__ ) );
				
				$page->display_content();
			} ?>
		</div>
	<?php }
}

/*function to prepage data for the links table */
if ( ! function_exists( "gglshrtlnk_table_data" ) ) {
	function gglshrtlnk_table_data( ) {
		global $wpdb, $gglshrtlnk_options;

		if ( empty( $gglshrtlnk_options ) ) {
		    $gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
        }

		if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
			/*if search query was send */
			if ( ! empty( $_POST['s'] ) ) {
				$gglshrtlnk_search = stripcslashes( sanitize_text_field( $_POST['s'] ) );
				/*if searching on short link */
				if ( false === strpos( $gglshrtlnk_search, 'https://goo.gl/' ) ) {
					$gglshrtlnk_data = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "google_shortlink` WHERE `long_url` LIKE '%" . $gglshrtlnk_search . "%' ", ARRAY_A );
					/*if searching of long link */
				} else {
					$gglshrtlnk_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "google_shortlink` WHERE `short_url` = %s", $gglshrtlnk_search ), ARRAY_A );
				}

				/*if pagination turn off */
			} elseif ( 'all' == $gglshrtlnk_options['pagination'] ) {
				$gglshrtlnk_data = $wpdb->get_results(
					"SELECT * FROM `" . $wpdb->prefix . "google_shortlink` ORDER BY `id` DESC ", ARRAY_A
				);
				/*if pagination turn on	*/
			} else {
				$gglshrtlnk_per_page = $gglshrtlnk_options['pagination'];
				$gglshrtlnk_begin    = 0;
				if ( isset( $_REQUEST['paged'] ) && 1 != $_REQUEST['paged'] ) {
					$gglshrtlnk_begin = $gglshrtlnk_per_page * absint( ( $_REQUEST['paged'] - 1 ) );
				}
				$gglshrtlnk_data = $wpdb->get_results(
					"SELECT * FROM `" . $wpdb->prefix . "google_shortlink`
					ORDER BY id DESC
					LIMIT $gglshrtlnk_per_page
					OFFSET $gglshrtlnk_begin
					", ARRAY_A
				);
			}
			/*common part */
			$i = 0;

			foreach ( $gglshrtlnk_data as $gglshrtlnk_row ) {
				if ( 'added_by_direct' != $gglshrtlnk_row['post_ids'] && @unserialize( $gglshrtlnk_row['post_ids'] ) ) {
					$gglshrtlnk_post_ids        = unserialize( $gglshrtlnk_row['post_ids'] );
					$gglshrtlnk_post_ids_string = implode( " OR ID = ", $gglshrtlnk_post_ids );
					/*get post title and guid from db */
					$gglshrtlnk_post_meta = $wpdb->get_results(
						"SELECT `ID`, `post_title`
					FROM `$wpdb->posts`
					WHERE `ID` = $gglshrtlnk_post_ids_string
					", ARRAY_A
					);
					$j                    = 0;
					$gglshrtlnk_home      = home_url( '/?p=' );
					foreach ( $gglshrtlnk_post_meta as $gglshrtlnk_one_meta ) {
						$post_url = $gglshrtlnk_home . $gglshrtlnk_one_meta['ID'];
						if ( '' == $gglshrtlnk_one_meta['post_title'] ) {
							$gglshrtlnk_one_meta['post_title'] = '(' . __( 'no title', 'google-shortlink' ) . ')';
						}
						$gglshrtlnk_post_ids[ $j ] = '<a target="_blank" href="' . $post_url . '">' . $gglshrtlnk_one_meta['post_title'] . '</a>';
						$j ++;
					}
					$gglshrtlnk_post_ids_content = implode( ', ', $gglshrtlnk_post_ids );
				} else {
					$gglshrtlnk_post_ids_content = __( 'None', 'google-shortlink' );
				}
				$gglshrtlnk_return[ $i ] = array(
					'id'           => $gglshrtlnk_row['id'],
					'long_url'     => '<a target="_blank" href="' . $gglshrtlnk_row['long_url'] . '">' . $gglshrtlnk_row['long_url'] . '</a>',
					'short_url'    => '<a target="_blank" href="' . $gglshrtlnk_row['short_url'] . '">' . $gglshrtlnk_row['short_url'] . '</a>',
					'total_clicks' => '<div class="hide-if-no-js">' . __( 'Wait for response', 'google-shortlink' ) . '</div>',
					'post_ids'     => $gglshrtlnk_post_ids_content
				);
				$i ++;
			}
			if ( isset( $gglshrtlnk_return ) ) {
				return $gglshrtlnk_return;
			} else {
				return false;
			}
		} else {
			/*if search query was send */
			if ( ! empty( $_POST['s'] ) ) {
				$gglshrtlnk_search = stripcslashes( sanitize_text_field( $_POST['s'] ) );
				/*if searching on short link */
				if ( false === strpos( $gglshrtlnk_options['domain_link'], $gglshrtlnk_search ) ) {
					$gglshrtlnk_data = $wpdb->get_results(  "SELECT * FROM `" . $wpdb->prefix . "google_shortlink_for_firebase` WHERE `long_url` LIKE '%" . $gglshrtlnk_search . "%' ", ARRAY_A);
					/*if searching of long link */
				} else {
					$gglshrtlnk_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "google_shortlink_for_firebase` WHERE `short_url` LIKE %s;", '%' . $gglshrtlnk_search . '%' ), ARRAY_A );
				}

				/*if pagination turn off */
			} elseif ( 'all' == $gglshrtlnk_options['pagination'] ) {
				$gglshrtlnk_data = $wpdb->get_results(
					"SELECT *
				FROM `" . $wpdb->prefix . "google_shortlink_for_firebase`
				ORDER BY `id` DESC
				", ARRAY_A
				);
				/*if pagination turn on	*/
			} else {
				$gglshrtlnk_per_page = $gglshrtlnk_options['pagination'];
				$gglshrtlnk_begin = 0;
				if ( isset( $_REQUEST['paged'] ) && 1 != $_REQUEST['paged'] ) {
					$gglshrtlnk_begin = $gglshrtlnk_per_page * absint( ( $_REQUEST['paged'] - 1 ) );
				}
				$gglshrtlnk_data = $wpdb->get_results(
					"SELECT *
				FROM `" . $wpdb->prefix . "google_shortlink_for_firebase`
				ORDER BY id DESC
				LIMIT $gglshrtlnk_per_page
				OFFSET $gglshrtlnk_begin
				", ARRAY_A
				);
			}
			/*common part */
			$i = 0;

			foreach ( $gglshrtlnk_data as $gglshrtlnk_row ) {
				if ( 'added_by_direct' != $gglshrtlnk_row['post_ids'] ) {
					$gglshrtlnk_post_ids = unserialize( $gglshrtlnk_row['post_ids'] );
					$gglshrtlnk_post_ids_string = implode( " OR ID = ", $gglshrtlnk_post_ids );
					/*get post title and guid from db */
					$gglshrtlnk_post_meta = $wpdb->get_results(
						"SELECT `ID`, `post_title`
					FROM `$wpdb->posts`
					WHERE `ID` = $gglshrtlnk_post_ids_string
					", ARRAY_A
					);
					$j = 0;
					$gglshrtlnk_home = home_url( '/?p=' );
					foreach ( $gglshrtlnk_post_meta as $gglshrtlnk_one_meta ) {
						$post_url = $gglshrtlnk_home . $gglshrtlnk_one_meta['ID'];
						if ( '' == $gglshrtlnk_one_meta['post_title'] ) {
							$gglshrtlnk_one_meta['post_title'] = '(' . __( 'no title' , 'google-shortlink' ) . ')';
						}
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
					'total_clicks' => '<div class="hide-if-no-js">' . __( 'Wait for response', 'google-shortlink' ) . '</div>',
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
}

/* creating class for display table of links */
if ( file_exists( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ) {
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	if ( ! class_exists( 'gglshrtlnk_list_table' ) ) {
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
				switch ( $column_name ) {
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
			function get_columns() {
				$columns = array(
					'cb'			=> '<input type="checkbox" />',
					'id' 			=> __( 'ID', 'google-shortlink' ),
					'long_url'  	=> __( 'Long Link', 'google-shortlink' ),
					'short_url' 	=> __( 'Short Link', 'google-shortlink' ),
					'total_clicks'	=> __( 'Total Clicks', 'google-shortlink' ),
					'post_ids'		=> __( 'Articles That Contain Links', 'google-shortlink' )
				);
				return $columns;
			}
			/* function for column cb */
			function column_cb( $item ) {
				if ( 'None' != $item['post_ids'] ) {
					return sprintf(
						'<input type="checkbox" name="link[]" value="%s" />', $item['id']
					);
				} else {
					return sprintf(
						'<input type="checkbox"  name="link[]" value="%s" />', $item['id']
					);
				}
			}
			/* function for actions */
			function column_long_url( $item ) {
				global $wpdb, $gglshrtlnk_options;
				$gglshrtlnk_is_added_by_direct = '';

				if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
					$gglshrtlnk_is_added_by_direct = $wpdb->get_var( $wpdb->prepare( "SELECT `post_ids` FROM `" . $wpdb->prefix . "google_shortlink` WHERE `id` = %s", $item['id'] ) );
				} else {
					$gglshrtlnk_is_added_by_direct = $wpdb->get_var( $wpdb->prepare( "SELECT `post_ids` FROM `" . $wpdb->prefix . "google_shortlink_for_firebase` WHERE `id` = %s", $item['id'] ) );
                }
				if ( 'added_by_direct' != $gglshrtlnk_is_added_by_direct ) {
					$actions = array(
						'replace' => '<a href="' . esc_url( wp_nonce_url( sprintf( '?page=%s&action=%s&link=%s', $_GET['page'], 'replace', $item['id'] ) , 'gglshrtlnk_tbl-noonce-replace' . $item['id'] ) ) . '">' .  __( 'Replace', 'google-shortlink' ) . '</a>',
						'restore' => '<a href="' . esc_url( wp_nonce_url( sprintf( '?page=%s&action=%s&link=%s', $_GET['page'], 'restore', $item['id'] ) , 'gglshrtlnk_tbl-noonce-restore' . $item['id'] ) ) . '">' . __( 'Restore', 'google-shortlink' ) . '</a>',
						'delete'  => '<a href="' . esc_url( wp_nonce_url( sprintf( '?page=%s&action=%s&link=%s', $_GET['page'], 'delete', $item['id'] ) , 'gglshrtlnk_tbl-noonce-delete' . $item['id'] ) ) . '">' . __( 'Delete', 'google-shortlink' ) . '</a>',
					);
				} else {
					$actions = array(
						'delete'  => '<a href="' . wp_nonce_url( sprintf( '?page=%s&action=%s&link=%s', $_GET['page'], 'delete', $item['id'] ) , 'gglshrtlnk_tbl-noonce-delete' . $item['id'] ) . '">' . __( 'Delete', 'google-shortlink' )  . '</a>',
					);
				}
				return sprintf( '%1$s %2$s', $item['long_url'], $this->row_actions( $actions ) );
			}
			/* function for bulk actions */
			function get_bulk_actions() {
				$actions = array(
					'replace'	=> __( 'Replace', 'google-shortlink' ),
					'restore'	=> __( 'Restore', 'google-shortlink' ),
					'delete'	=> __( 'Delete', 'google-shortlink' )
				);
				return $actions;
			}
			/* function for prepairing items */
			function prepare_items() {
				global $wpdb, $gglshrtlnk_options;
				$total_items = '';
				$columns	= $this->get_columns();
				$hidden		= array( 'id' );
				$sortable	= array();
				$this->_column_headers = array( $columns, $hidden, $sortable, 'long_url' );
				$this->items = gglshrtlnk_table_data();
				$action = $this->current_action();
				if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
					$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "google_shortlink" );
                } else {
					$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "google_shortlink_for_firebase" );
                }
				$table_items_quantity = count( (array) $this->items );

				/*if pagination turn on */
				if ( 'all' != $gglshrtlnk_options['pagination'] ) {
					$per_page = $gglshrtlnk_options['pagination'];
					if ( ! empty( $_POST['s'] ) ) {
						$this->set_pagination_args( array(
							'total_items' => $table_items_quantity,
							'per_page'    => 'all'
						) );
					} else {
						$this->set_pagination_args( array(
							'total_items' => $total_items,
							'per_page'    => $per_page
						) );
						$current_page = $this->get_pagenum();
					}
				}
			}
		} /*class end */
	}
}

/*function to display table of links */
if ( ! function_exists( 'gglshrtlnk_table' ) ) {
	function gglshrtlnk_table() {
		$myListTable = new gglshrtlnk_list_table();
		$myListTable->prepare_items();
		$myListTable->search_box( 'Search', 'search_id' );
		$myListTable->display();
		wp_nonce_field( 'gglshrtlnk_tbl-noonce-action', 'gglshrtlnk_tbl-noonce-field' );
	}
}

/* function for plugin page */
if ( ! function_exists( 'gglshrtlnk_page' ) ) {
	function gglshrtlnk_page() {
		global $wpdb, $gglshrtlnk_links_number, $gglshrtlnk_options; ?>
		<div class="wrap">
			<h1>Shortlink</h1>
			<h2 class="nav-tab-wrapper">
			<?php if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) { ?>
				<a class="nav-tab <?php if ( ! isset( $_GET['tab'] ) ) echo 'nav-tab-active'; ?>" href="<?php echo admin_url( 'admin.php?page=google-shortlink', '' ); ?>"><?php _e( 'Table of Links for Shortener API', 'google-shortlink' ); ?></a>
			<?php } else { ?>
				<a class="nav-tab <?php if ( ! isset( $_GET['tab'] ) ) echo 'nav-tab-active'; ?>" href="<?php echo admin_url( 'admin.php?page=google-shortlink', '' ); ?>"><?php _e( 'Table of links for Firebase Dynamic Links', 'google-shortlink' ); ?></a>
			<?php } ?>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && 'direct' == $_GET['tab'] ) echo 'nav-tab-active'; ?>" href="<?php echo admin_url( 'admin.php?page=google-shortlink&tab=direct', '' ); ?>"><?php _e( 'Direct Input', 'google-shortlink' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && 'all' == $_GET['tab'] ) echo 'nav-tab-active'; ?>" href="<?php echo admin_url( 'admin.php?page=google-shortlink&tab=all', '' ); ?>"><?php _e( 'Additional Options', 'google-shortlink' ); ?></a>
			</h2>
			<?php if ( ! isset( $_GET['tab'] ) ) { ?>
				<noscript><div class="error below-h2"><p><?php _e( 'Please enable JavaScript to count total clicks.', 'google-shortlink' ); ?></p></div></noscript>
				<?php /*do action if isset  */
				if ( isset( $_GET['action'] ) && isset( $_GET['link'] ) ) {
					if ( check_admin_referer( 'gglshrtlnk_tbl-noonce-' . $_GET['action'] . $_GET['link'] ) ) {
						gglshrtlnk_actions( $_GET['action'], $_GET['link'] );
					}
				}
				/*bulk actions part */
				if ( ( ( isset( $_POST['action'] ) && -1 != $_POST['action'] ) || ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] ) ) && isset( $_POST['link'] ) && check_admin_referer( 'gglshrtlnk_tbl-noonce-action', 'gglshrtlnk_tbl-noonce-field' ) ) {
					foreach ( $_POST['link'] as $gglshrtlnk_id_to_action ) {
						if ( -1 != $_POST['action'] ) {
							gglshrtlnk_actions( $_POST['action'], $gglshrtlnk_id_to_action );
						} elseif ( -1 != $_POST['action2'] ) {
							gglshrtlnk_actions( $_POST['action2'], $gglshrtlnk_id_to_action );
						}
					}
				} ?>
				<!-- TABLE OF LINKS TAB -->
				<!-- show message if action was done -->
				<?php if ( isset( $_GET['action'] ) ) { ?>
					<div class="updated below-h2">
						<p>
							<?php switch ( $_GET['action'] ) {
								case 'replace':
									if ( check_admin_referer( 'gglshrtlnk_tbl-noonce-replace' . $_GET['link'] ) ) {
										_e( 'One long link was replaced with a short link.', 'google-shortlink' );
									}
									break;
								case 'restore':
									if ( check_admin_referer( 'gglshrtlnk_tbl-noonce-restore' . $_GET['link'] ) ) {
										_e( 'One short link was restored to a long link.', 'google-shortlink' );
									}
									break;
								case 'delete':
									if ( check_admin_referer( 'gglshrtlnk_tbl-noonce-delete' . $_GET['link'] ) ) {
										_e( 'One short link was deleted from database.', 'google-shortlink' );
									}
									break;
							} ?>
						</p>
					</div>
				<?php }
				if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
					if ( '' == $gglshrtlnk_options['api_key'] ) { ?>
                        <div class="error below-h2">
                            <p>
								<?php echo "<b/>" . __( 'Warning:', 'google-shortlink' ) . "</b> " . __( "You haven't entered API key yet. Go to plugin's", 'google-shortlink' ) . ' <a href="' . admin_url( 'admin.php?page=gglshrtlnk_options', '' ) . '">' . __( 'settings page', 'google-shortlink' ) . '</a> ' . __( 'and enter your key.', 'google-shortlink' ); ?>
                            </p>
                        </div>
					<?php }
				} else {
					if ( '' == $gglshrtlnk_options['api_key_for_firebase'] ) { ?>
                        <div class="error below-h2 notice-error">
                            <p>
								<?php echo "<b/>" . __( 'Warning:', 'google-shortlink' ) . "</b> " . __( "You haven't entered API key yet. Go to plugin's", 'google-shortlink' ) . ' <a href="' . admin_url( 'admin.php?page=gglshrtlnk_options', '' ) . '">' . __( 'settings page', 'google-shortlink' ) . '</a> ' . __( 'and enter your key.', 'google-shortlink' ); ?>
                            </p>
                        </div>
					<?php }
				}
				/* if user input his api key notice about statistic update */
				if ( ! empty( $gglshrtlnk_options['api_key_for_firebase'] ) ) { ?>
				<br>
                    <div class="notice below-h2 notice-warning is-dismissible">
                        <p>
							<?php echo "<b/>" . __('Notice:', 'google-shortlink') . "</b> " . __('Click statistic updates every 3 days', 'google-shortlink') ?>
                        </p>
                    </div>
					<?php
				}
				if ( ( ( isset( $_POST['action'] ) && -1 != $_POST['action'] ) || ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] ) ) && check_admin_referer( 'gglshrtlnk_tbl-noonce-action', 'gglshrtlnk_tbl-noonce-field' ) ) { ?>
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
				$gglshrtlnk_total_items = '';
				if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
					$gglshrtlnk_total_items = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "google_shortlink" );
                } else {
					$gglshrtlnk_total_items = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "google_shortlink_for_firebase" );
                }

				/*show this if database is empty */
				if ( ! $gglshrtlnk_total_items ) { ?>
					<div class="updated below-h2">
						<p><?php _e( 'There are no links in the database. Go to the Additional options tab, and scan your website.', 'google-shortlink' ); ?></p>
					</div>
				<?php }	?>
				<form method="post" name="gglshrtlnk_table-of-links" id="gglshrtlnk_table-of-links" action="<?php echo admin_url( 'admin.php?page=google-shortlink', '' ); ?>" class="gglshrtlnk_auto-replace">
					<?php gglshrtlnk_table(); ?>
				</form>
			<?php } else {
			switch ( $_GET['tab'] ) {
				case 'direct':
					/*
					* direct input part
					*/
					$gglshrtlnk_number_of_input_links = 3;

					/*set number of direct link fields if direct input form was send */
					if ( isset( $_POST['gglshrtlnk_submit-direct-input'] ) && check_admin_referer( 'gglshrtlnk_dir-noonce-action', 'gglshrtlnk_dir-noonce-field' ) )
						$gglshrtlnk_number_of_input_links = intval( $_POST['gglshrtlnk_number_of_input_links'] );

					/* get short links if long links are exist in direct input */
					for ( $i=1; $i < $gglshrtlnk_number_of_input_links + 1; $i++ ) {
						$gglshrtlnk_input = "gglshrtlnk_url-input-" . $i;
						$gglshrtlnk_output = "gglshrtlnk_url-output-" . $i;

						if ( ! isset( $_POST['gglshrtlnk_reset-direct-input'] ) && ! empty( $_POST[ $gglshrtlnk_input ] ) && check_admin_referer( 'gglshrtlnk_dir-noonce-action', 'gglshrtlnk_dir-noonce-field' ) ) {
							$gglshrtlnk_input_links[ $gglshrtlnk_input ] = stripslashes( sanitize_text_field( $_POST[ $gglshrtlnk_input ] ) );
							/*check first is a short link alreary exist in db */
							$gglshrtlnk_short_url_from_db = '';
                            if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	                            $gglshrtlnk_short_url_from_db = $wpdb->get_var(
		                            $wpdb->prepare(
			                            "SELECT `short_url`
									FROM `" . $wpdb->prefix . "google_shortlink`
									WHERE `long_url` = %s
									", $gglshrtlnk_input_links[ $gglshrtlnk_input ]
		                            )
	                            );
                            } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	                            $gglshrtlnk_short_url_from_db = $wpdb->get_var(
		                            $wpdb->prepare(
			                            "SELECT `short_url`
									FROM `" . $wpdb->prefix . "google_shortlink_for_firebase`
									WHERE `long_url` = %s
									", $gglshrtlnk_input_links[ $gglshrtlnk_input ]
		                            )
	                            );
                            }


							if ( ! $gglshrtlnk_short_url_from_db ) {
								/*get a short url*/
								$gglshrtlnk_short_url[ $gglshrtlnk_output ] = gglshrtlnk_get( $gglshrtlnk_input_links[ $gglshrtlnk_input ] );
								if ( is_wp_error( $gglshrtlnk_short_url[ $gglshrtlnk_output ] ) ) {
									$gglshrtlnk_error = $gglshrtlnk_short_url[ $gglshrtlnk_output ];
									$gglshrtlnk_short_url[ $gglshrtlnk_output ] = ''; ?>
									<div class="below-h2 error">
										<p><?php echo __( 'Error:', 'google-shortlink' ) . ' ' . $gglshrtlnk_error->get_error_message() . ' - ' . esc_html( $_POST[ $gglshrtlnk_input ] ); ?></p>
									</div>
									<?php continue;
								}
								/* add long and short url to db */
								$gglshrtlnk_post_contents = $wpdb->get_results( "SELECT `post_content`, `ID`, `post_type` FROM `$wpdb->posts`", ARRAY_A );
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
								/* convert post ids into db format */
								if ( !empty( $gglshrtlnk_post_ids ) ) {
									$gglshrtlnk_post_ids_converted = serialize( $gglshrtlnk_post_ids );
								} else {
									$gglshrtlnk_post_ids_converted = 'added_by_direct';
								}
								if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
									$wpdb->insert(
										$wpdb->prefix . "google_shortlink",
										array(
											'long_url'	=> $gglshrtlnk_input_links[ $gglshrtlnk_input ],
											'short_url'	=> $gglshrtlnk_short_url[ $gglshrtlnk_output ],
											'post_ids'	=> $gglshrtlnk_post_ids_converted
										)
									);
                                } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
									$wpdb->insert(
										$wpdb->prefix . "google_shortlink_for_firebase",
										array(
											'long_url'	=> $gglshrtlnk_input_links[ $gglshrtlnk_input ],
											'short_url'	=> $gglshrtlnk_short_url[ $gglshrtlnk_output ],
											'post_ids'	=> $gglshrtlnk_post_ids_converted
										)
									);
                                }
							} else {
								/*get a short url from database */
								$gglshrtlnk_short_url[ $gglshrtlnk_output ] = $gglshrtlnk_short_url_from_db;
							}
						} else {
							$gglshrtlnk_input_links[ $gglshrtlnk_input ] = '';
							$gglshrtlnk_short_url[ $gglshrtlnk_output ] = '';
						}
					}
					if ( isset( $gglshrtlnk_key_invalid ) ) { ?>
						<div class="below-h2 error">
							<p><?php echo __( "Invalid API key. Go to plugin's", 'google-shortlink' ) . ' <a href="' . admin_url( 'admin.php?page=gglshrtlnk_options', '' ) . '">' . __( 'settings page', 'google-shortlink' ) . '</a> ' . __( 'and enter correct key.', 'google-shortlink' ); ?></p>
						</div>
					<?php } ?>
					<div class="below-h2 updated gglshrtlnk_hide" id="gglshrtlnk_no_more_fields">
						<p><?php _e( "There are empty fields on the page. Fill them out before adding another one.", 'google-shortlink' ); ?></p>
					</div>
					<!-- Direct input form -->
					<form method="post" name="gglshrtlnk_direct-input-form" action="" class="gglshrtlnk_direct-input">
						<table class="form-table">
							<tr valign="top">
								<th scope="row">
									<?php _e( 'Get Short Links By Direct Input:', 'google-shortlink' ) ?>
								</th>
								<td>
									<table id="gglshrtlnk_direct-input-table" cellspacing="0">
										<tbody>
											<tr>
												<td class="gglshrtlnk_long-link-column"><?php _e( 'Type long links here:', 'google-shortlink' ); ?></td>
												<td class="gglshrtlnk_short-link-column"><?php _e( 'Short links will appear below:', 'google-shortlink' ); ?></td>
											</tr>
											<!-- Creating table for direct input -->
											<?php for ( $i = 1; $i < $gglshrtlnk_number_of_input_links + 1; $i++ ) {
												$gglshrtlnk_input = "gglshrtlnk_url-input-" . $i;
												$gglshrtlnk_output = "gglshrtlnk_url-output-" . $i; ?>
												<tr valign="top">
													<td class="gglshrtlnk_long-link-column"><input type="url" name="<?php echo $gglshrtlnk_input; ?>"  value="<?php echo $gglshrtlnk_input_links[ $gglshrtlnk_input ]; ?>" /></td>
													<td class="gglshrtlnk_short-link-column"><input type="url" name="<?php echo $gglshrtlnk_output; ?>" readonly value="<?php echo $gglshrtlnk_short_url[ $gglshrtlnk_output ]; ?>" /></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</td>
							</tr>
						</table>
						<p>
							<input type="hidden" name="gglshrtlnk_number_of_input_links" id="gglshrtlnk_number_of_input_links" value="<?php echo $gglshrtlnk_number_of_input_links; ?>" />
							<input type="submit" name="gglshrtlnk_submit-direct-input" class="button-primary" value="<?php _e( 'Get Short Links', 'google-shortlink' ); ?>" />
							<input type="submit" name="gglshrtlnk_reset-direct-input" class="button-primary" id="reset-direct" value="<?php _e( 'Reset Form', 'google-shortlink' ); ?>" />
							<input type="button" value="<?php _e( 'Add Field', 'google-shortlink' ); ?>" class="button-primary hide-if-no-js" id="gglshrtlnk_add-field-button" />
							<?php wp_nonce_field( 'gglshrtlnk_dir-noonce-action', 'gglshrtlnk_dir-noonce-field' ); ?>
						</p>
					</form>
				<?php break;
				case 'all':
					/*
					* Actions with links part
					*/
					if ( isset( $_POST['gglshrtlnk_actions-with-links-was-send'] ) && check_admin_referer( 'gglshrtlnk_act-noonce-action', 'gglshrtlnk_act-noonce-field' ) ) {
						$result = gglshrtlnk_ajax_additional_opt_callback( true );
						if ( ! empty( $result['message'] ) ) { ?>
                            <div class="updated fade below-h2"><p><?php echo $result['message']; ?></p></div>
						<?php }
						if ( ! empty( $result['error'] ) ) { ?>
                            <div class="error fade below-h2"><p><?php echo $result['error']; ?></p></div>
						<?php }
					}
					$gglshrtlnk_total_items = '';
					/*check if db is empty */
					if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
						$gglshrtlnk_total_items = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "google_shortlink" );
					} elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
						$gglshrtlnk_total_items = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "google_shortlink_for_firebase" );
					} ?>
                    <div class="results below-h2 gglshrtlnk_hide updated" id="gglshrtlnk_ajax-status"></div>
                    <!-- ACTIONS WITH LINKS FORM -->
                    <form method="post" name="gglshrtlnk_actions-with-links" id="gglshrtlnk_actions-with-links"
                          action="" class="gglshrtlnk_auto-replace">
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th><?php _e( 'Actions with Links:', 'google-shortlink' ) ?></th>
                                <td>
                                    <fieldset>
                                        <!-- scan web-site to find all external links -->
                                        <label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="scan" id="gglshrtlnk_scan" checked /> <?php _e( 'Scan website for new external links', 'google-shortlink' ); ?> </label><br />
										<!-- replace automatically -->
										<label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="replace-all" id="gglshrtlnk_replace-all"<?php if ( 0 == $gglshrtlnk_total_items ) echo 'disabled="disabled"'; ?>/> <?php _e( 'Automatically replace all external links', 'google-shortlink' ); ?> </label><br />
										<!-- restore all -->
										<label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="restore-all" id="gglshrtlnk_restore-all"<?php if ( 0 == $gglshrtlnk_total_items ) echo 'disabled="disabled"'; ?>/> <?php _e( 'Automatically restore all external links', 'google-shortlink' ); ?> </label><br />
										<!-- delete all -->
										<label> <input type="radio" name="gglshrtlnk_actions_with_links_radio" value="delete-all-radio" id="gglshrtlnk_delete-all-radio"<?php if ( 0 == $gglshrtlnk_total_items ) echo 'disabled="disabled"'; ?>/> <?php _e( 'Restore all links and clear the database', 'google-shortlink' ); ?></label><br />
									</fieldset></td>
								</tr>
							</tbody>
						</table>
						<p>
							<input class="button-primary" value="<?php _e( 'Apply', 'google-shortlink' ); ?>" type="submit" name="gglshrtlnk_apply_button3" id="gglshrtlnk_apply_button3" />
							<input type="hidden" name="gglshrtlnk_actions-with-links-was-send" value="send" />
							<?php wp_nonce_field( 'gglshrtlnk_act-noonce-action', 'gglshrtlnk_act-noonce-field' ); ?>
						</p>
					</form><!-- actions with links -->
				<?php break;
				}
			} ?>
		</div>
	<?php }
}

if ( ! function_exists( 'gglshrtlnk_create_client' ) ) {
    function gglshrtlnk_create_client() {
        global $gglshrtlnk_options;

	    $config = array(
		    'client_id' => $gglshrtlnk_options['client_id'],
		    'client_secret' => $gglshrtlnk_options['client_secret'],
		    'redirect_uri' => $gglshrtlnk_options['redirect_uri'],
	    );

	    require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
	    $client = new Google_Client();
	    $client->setAuthConfig( $config );
	    $client->addScope( Google_Service_FirebaseDynamicLinksAPI::FIREBASE );
	    $client->setAccessType( 'offline' );
	    $client->setApprovalPrompt( "force" );
	    return $client;
    }
}

/*function for getting short links from direct input */
if ( ! function_exists( 'gglshrtlnk_get' ) ) {
	function gglshrtlnk_get( $long_url ) {
	    global $gglshrtlnk_options;

	    if ( empty( $gglshrtlnk_options ) ) {
	        $gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
        }

        if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) { // if using old Shortener API
	        /* api key for application */
	        $gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
	        $gglshrtlnk_api_key = $gglshrtlnk_options['api_key'];
	        /* encoding data to json */
	        $gglshrtlnk_post_data = array( 'longUrl' => $long_url );
	        $gglshrtlnk_json_data = json_encode( $gglshrtlnk_post_data );
	        /* set options for wp_remote_post */
	        $gglshrtlnk_args = array(
		        'headers'   => array( 'Content-type' => 'application/json' ),
		        'body'      => $gglshrtlnk_json_data,
		        'sslverify' => true,
	        );
	        $gglshrtlnk_url  = 'https://www.googleapis.com/urlshortener/v1/url?key=' . $gglshrtlnk_api_key;
	        /*get response*/
	        $gglshrtlnk_response = wp_remote_post( $gglshrtlnk_url, $gglshrtlnk_args );
	        /*return an error if we have one*/
	        if ( is_wp_error( $gglshrtlnk_response ) ) {
		        return $gglshrtlnk_response;
	        } else {
		        /*decoding json response */
		        $gglshrtlnk_json = json_decode( $gglshrtlnk_response['body'] );
		        if ( isset( $gglshrtlnk_json->id ) ) {
			        $gglshrtlnk_short_url = $gglshrtlnk_json->id;

			        return $gglshrtlnk_short_url;
		        } elseif ( isset( $gglshrtlnk_json->error ) ) {
			        $code = $gglshrtlnk_json->error->errors[0]->reason;

			        return new WP_Error( $code, gglshrtlnk_get_error_message( $code ) );
		        }
	        }

	        return new WP_Error( 'unknown_error', gglshrtlnk_get_error_message() );
        } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) { // if using Firebase Dynamic Links
	        /* checking if last character is / */
	        if ( '/' != substr($long_url, -1) ) {
		        $long_url .= "/";
	        }
	        /* api key for application */
	        $gglshrtlnk_api_key = $gglshrtlnk_options['api_key_for_firebase'];
	        $gglshrtlnk_domain_link = ( null == parse_url( $gglshrtlnk_options['domain_link'], PHP_URL_HOST ) ) ? $gglshrtlnk_options['domain_link'] : parse_url( $gglshrtlnk_options['domain_link'], PHP_URL_HOST ); // taking only host from provided domain link
	        /* encoding data to json */
	        $gglshrtlnk_post_data = array(
		        'dynamicLinkInfo' => array(
			        'dynamicLinkDomain' => $gglshrtlnk_domain_link,
			        'link' => $long_url ,
		        ),
		        'suffix' => array(
			        'option' => 'SHORT',
		        )
	        );
	        $gglshrtlnk_json_data = json_encode( $gglshrtlnk_post_data );
	        /* set options for wp_remote_post */
	        $gglshrtlnk_args = array(
		        'headers' => array( 'Content-type' => 'application/json' ),
		        'body' => $gglshrtlnk_json_data,
	        );
	        $gglshrtlnk_url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $gglshrtlnk_api_key;
	        /*get response*/
	        $gglshrtlnk_response = wp_remote_post( $gglshrtlnk_url, $gglshrtlnk_args );

	        /*return an error if we have one*/
	        if ( is_wp_error( $gglshrtlnk_response ) ) {
		        return $gglshrtlnk_response;
	        } else {
		        /*decoding json response */
		        $gglshrtlnk_json = json_decode( $gglshrtlnk_response['body'] );

		        if ( isset( $gglshrtlnk_json->error ) ) {
			        $code = $gglshrtlnk_json->error->code;
			        return new WP_Error( $code, gglshrtlnk_get_error_message( $code ) );
		        } elseif (isset($gglshrtlnk_json->shortLink)) {
			        $gglshrtlnk_short_url = $gglshrtlnk_json->shortLink;
			        return $gglshrtlnk_short_url;
		        }
	        }
	        return new WP_Error( 'unknown_error', gglshrtlnk_get_error_message() );
        }
	}
}
/*function for getting total clicks on short link */
if ( ! function_exists( 'gglshrtlnk_count' ) ) {
	function gglshrtlnk_count( $gglshrtlnk_short_url ) {
	    global $gglshrtlnk_options;

	    if ( empty( $gglshrtlnk_options ) ) {
	        $gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
        }
        if ( 0 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	        /* api key for application */
	        $gglshrtlnk_options = get_option( 'gglshrtlnk_options' );
	        $gglshrtlnk_api_key = $gglshrtlnk_options['api_key'];
	        /* set options for wp_remote_post */
	        $gglshrtlnk_args     = array(
		        'headers' => array( 'Content-type' => 'application/json' ),
		        'timeout' => 60,
	        );
	        $gglshrtlnk_url      = 'https://www.googleapis.com/urlshortener/v1/url?shortUrl=' . $gglshrtlnk_short_url . '&projection=ANALYTICS_CLICKS&fields=analytics,status&key=' . $gglshrtlnk_api_key;
	        $gglshrtlnk_response = wp_remote_get( $gglshrtlnk_url, $gglshrtlnk_args );
	        /*return an error if we have one*/
	        if ( is_wp_error( $gglshrtlnk_response ) ) {
		        return $gglshrtlnk_response;
	        } else {
		        /*decoding json response */
		        $gglshrtlnk_json = json_decode( $gglshrtlnk_response['body'] );
		        if ( isset( $gglshrtlnk_json->analytics->allTime->shortUrlClicks ) ) {
			        return $gglshrtlnk_json->analytics->allTime->shortUrlClicks;
		        } elseif ( isset( $gglshrtlnk_json->error ) ) {
			        $code = $gglshrtlnk_json->error->errors[0]->reason;

			        return new WP_Error( $code, gglshrtlnk_get_error_message( $code ) );
		        }
	        }

	        return new WP_Error( 'unknown_error', gglshrtlnk_get_error_message() );
        } elseif ( 1 == $gglshrtlnk_options['firebase_api_is_on'] ) {
	        $elem = $code = $access_token = '';
	        $days = 30;
			$session = session_id();
	        if ( empty( $session ) ) {
				session_start();
			}

	        /* getting access token from SESSION */
	        if ( isset( $_SESSION['access_token']['access_token'] ) ) {
		        $access_token = $_SESSION['access_token']['access_token'];
	        }
	        /* encoding url to send to endpoint */
	        $gglshrtlnk_short_url = urlencode( $gglshrtlnk_short_url );
			$gglshrtlnk_url = 'https://firebasedynamiclinks.googleapis.com/v1/' . $gglshrtlnk_short_url . '/linkStats?durationDays=' . $days;

	        /* retriev statistic for short link */
	        $gglshrtlnk_args = array(
			    'headers' => array(
			    	'Content-type' => 'application/json',
			        'Authorization' => 'Bearer ' . $access_token
			    )
			);
			
	        /*get response*/
	        $gglshrtlnk_response = wp_remote_get( $gglshrtlnk_url, $gglshrtlnk_args );

	        if ( is_wp_error( $gglshrtlnk_response ) ) {
		        return $gglshrtlnk_response;
	        } else {
	        	/*decoding json response */
		        $gglshrtlnk_json = json_decode( $gglshrtlnk_response['body'] );

		        if ( isset( $gglshrtlnk_json->error ) ) {
			        $code = $gglshrtlnk_json->error->code;
			        return new WP_Error( $code, gglshrtlnk_get_error_message( $code ) );
		        } elseif ( isset( $gglshrtlnk_json->linkEventStats ) ) {
			        $elem = $gglshrtlnk_json->linkEventStats;
			        /* getting CLICK statistic for one link */
			        foreach ($elem as $arr => $el) {
				        if ( NULL == $elem ){
					        $code = '';
					        return new WP_Error( $code, gglshrtlnk_get_error_message( $code ) );
				        }
				        if ( 'CLICK' == $el['event']  ) {
					        return $el['count'];
				        }
			        }
		        }
	        }
	        return new WP_Error( 'unknown_error', gglshrtlnk_get_error_message() );
        }
	}
}


if ( ! function_exists( 'gglshrtlnk_refresh_token' ) ) {
	function gglshrtlnk_refresh_token() {

		$client = gglshrtlnk_create_client();
        $client->setAccessToken( $_SESSION['access_token'] );
		if ( $client->isAccessTokenExpired() && isset( $_SESSION['access_token']['refresh_token'] ) ) {
			$client->refreshToken( $_SESSION['access_token']['refresh_token'] );
			$_SESSION['access_token'] = $client->getAccessToken();
		}
	}
}

/* functions get's an error message for WP_Error class */
if ( ! function_exists( 'gglshrtlnk_get_error_message' ) ) {
	function gglshrtlnk_get_error_message( $code = null ) {
		$message = '';
		switch ( $code ) {
			case '':
				$message = __( 'No statistic for this link', 'google-shortlink' );
				break;
			case '401':
				$message = __( 'Request had invalid authentication credentials', 'google-shortlink' );
				break;
			case '400':
				$message = __( 'We could not match link with whitelisted URL patterns in your Google project', 'google-shortlink' );
				break;
			case 'invalid':
				$message = __( 'Bad request error.', 'google-shortlink' );
				break;
			case 'keyInvalid':
				$message = __( 'Invalid API key error.', 'google-shortlink' );
				break;
			case 'accessNotConfigured':
				$message = __( 'Error with access not configuration.', 'google-shortlink' );
				break;
			case 'keyExpired':
				$message = __( 'Expired API key error.', 'google-shortlink' );
				break;
			case 'curl__error':
				$message = __( 'Curl error. Please try again.', 'google-shortlink' );
				break;
			case 'not_support_curl':
				$message = __( 'This hosting does not support СURL.', 'google-shortlink' );
				break;
			default:
				$message = __( 'Unknown error occurred.', 'google-shortlink' );
				break;
		}
		return $message;
	}
}

if ( ! function_exists( 'gglshrtlnk_action_links' ) ) {
	function gglshrtlnk_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=gglshrtlnk_options">' . __( 'Settings', 'google-shortlink' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists( 'gglshrtlnk_links' ) ) {
	function gglshrtlnk_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() ) {
				$links[] = '<a href="admin.php?page=gglshrtlnk_options">' . __( 'Settings', 'google-shortlink' ) . '</a>';
			}
			$links[] = '<a href="http://wordpress.org/plugins/google-shortlink/faq/" target="_blank">' . __( 'FAQ', 'google-shortlink' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'google-shortlink' ) . '</a>';
		}

		return $links;
	}
}

/* add admin notices */
if ( ! function_exists ( 'gglshrtlnk_admin_notices' ) ) {
	function gglshrtlnk_admin_notices() {
		global $hook_suffix, $gglshrtlnk_plugin_info;
		if ( 'plugins.php' == $hook_suffix && ! is_network_admin() ) {
			bws_plugin_banner_to_settings( $gglshrtlnk_plugin_info, 'gglshrtlnk_options', 'google-shortlink', 'admin.php?page=gglshrtlnk_options', 'admin.php?page=google-shortlink&tab=all' );
		}
		if ( isset( $_GET['page'] ) && ( 'gglshrtlnk_options' == $_GET['page'] || 'google-shortlink' == $_GET['page'] ) ) {
			bws_plugin_suggest_feature_banner( $gglshrtlnk_plugin_info, 'gglshrtlnk_options', 'google-shortlink' );
		}
	}
}

/* add help tab  */
if ( ! function_exists( 'gglshrtlnk_add_tabs' ) ) {
	function gglshrtlnk_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id' 			=> 'gglshrtlnk',
			'section' 		=> '200538839'
		);
		bws_help_tab( $screen, $args );
	}
}

/* function for delete options */
if ( ! function_exists( 'gglshrtlnk_delete_options' ) ) {
	function gglshrtlnk_delete_options() {
		global $wpdb;

		/* Delete options and db table */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'gglshrtlnk_options' );
				$wpdb->query( "DROP TABLE `" . $wpdb->prefix . "google_shortlink`;" );
				$wpdb->query( "DROP TABLE `" . $wpdb->prefix . "google_shortlink_for_firebase`;" );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'gglshrtlnk_options' );
			$wpdb->query( "DROP TABLE `" . $wpdb->prefix . "google_shortlink`;" );
			$wpdb->query( "DROP TABLE `" . $wpdb->prefix . "google_shortlink_for_firebase`;" );
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

/*hook for activation plugin */
register_activation_hook( __FILE__, 'gglshrtlnk_create_table' );

/*hook for add menu */
add_action( 'admin_menu', 'gglshrtlnk_menu' );

add_action( 'init', 'gglshrtlnk_init' );
add_action( 'init', 'gglshrtlnk_session_start' );
add_action( 'admin_init', 'gglshrtlnk_admin_init' );
add_action( 'plugins_loaded', 'gglshrtlnk_plugins_loaded' );
/* action for updating to firebase */

/* function for log in to firebase dynamic links */
add_action( 'admin_post_gglshrtlnk_oauth', 'gglshrtlnk_oauth' );
/*hook for scripts and styles */
add_action( 'admin_enqueue_scripts', 'gglshrtlnk_script_style' );
/*hooks for ajax on additional options tab */
add_action( 'wp_ajax_additional_opt', 'gglshrtlnk_ajax_additional_opt_callback' );
/*hooks for ajax to get total clicks */
add_action( 'wp_ajax_total_clicks', 'gglshrtlnk_ajax_total_clicks_callback' );
/*hook for plugin links */
add_filter( 'plugin_action_links', 'gglshrtlnk_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglshrtlnk_links', 10, 2 );
/* add admin notices */
add_action( 'admin_notices', 'gglshrtlnk_admin_notices' );
/*hook for uninstalling plugin */
register_uninstall_hook( __FILE__, 'gglshrtlnk_delete_options' );
