<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Gglshrtlnk_Settings_Tabs' ) ) {
	class Gglshrtlnk_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $gglshrtlnk_options, $gglshrtlnk_plugin_info;

			$tabs = array(
				'settings' 		=> array( 'label' => __( 'Settings', 'google-shortlink' ) ),
				'misc' 			=> array( 'label' => __( 'Misc', 'google-shortlink' ) ),
				'custom_code' 	=> array( 'label' => __( 'Custom Code', 'google-shortlink' ) )
			);

			parent::__construct( array(
				'plugin_basename' 	 => $plugin_basename,
				'plugins_info'		 => $gglshrtlnk_plugin_info,
				'prefix' 			 => 'gglshrtlnk',
				'default_options' 	 => gglshrtlnk_get_options_default(),
				'options' 			 => $gglshrtlnk_options,
				'doc_link'			 => 'https://bestwebsoft.com/documentation/shortlink/shortlink-user-guide/',
				'tabs' 				 => $tabs,
				'wp_slug'			 => 'google-shortlink'
			) );

			/**
			* @deprecated since 1.5.9
			* @todo remove after 20.09.2021
			*/
			add_action( get_parent_class( $this ) . '_display_custom_messages', array( $this, 'display_custom_messages' ) );
			/* end deprecated */
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			$message = $notice = $error = '';

			/* Takes all the changed settings on the plugin's admin page and saves them in array 'gglshrtlnk_options'. */
			/**
			* @deprecated since 1.5.9
			* @todo edit after 20.09.2021
			* Remove if else and leave only content else
			*/
			if ( 0 == $this->options['firebase_api_is_on'] ) {
				if ( '' != $_POST['gglshrtlnk_api-key'] && 39 == strlen( $_POST['gglshrtlnk_api-key'] ) ) {
					$this->options['api_key'] = stripslashes( sanitize_text_field( $_POST['gglshrtlnk_api-key'] ) );
					
					update_option( 'gglshrtlnk_options', $this->options );
					$message = __( 'Settings saved.', 'google-shortlink' );
				} else {
					$error = __( 'Incorrect API key entered', 'google-shortlink' );
				}
			} else {

                $this->options['api_key_for_firebase'] = stripslashes( sanitize_text_field( $_POST['gglshrtlnk_api_key_for_firebase'] ) );
                $this->options['client_id'] = stripslashes( sanitize_text_field( $_POST['gglshrtlnk_client_id'] ) );
                $this->options['client_secret'] = stripslashes( sanitize_text_field( $_POST['gglshrtlnk_client_secret'] ) );
                $this->options['redirect_uri'] = plugin_dir_url( dirname(__FILE__) ) . 'oauth2.php';
                $this->options['domain_link'] = esc_url( $_POST['gglshrtlnk_domain_link'] );
				
				update_option( 'gglshrtlnk_options', $this->options );
				$message = __( 'Settings saved.', 'google-shortlink' );
				
				if ( '' == $this->options['api_key_for_firebase'] || '' == $this->options['client_id']
	                || '' == $this->options['client_secret'] || '' == $this->options['domain_link'] ) {
					$error = __( 'Incorrect data entered.', 'google-shortlink' ) . '<br>' . __( 'You must fill all fields.', 'google-shortlink' );
				}
			}			
			/* end deprecated */
			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Shortlink Settings', 'google-shortlink' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label"><?php _e( 'Google API Console', 'google-shortlink' ); ?></div>			
			<?php 
			/**
			* @deprecated since 1.5.9
			* @todo edit after 20.09.2021
			* Remove if else and leave only content else
			*/
			if ( 0 == $this->options['firebase_api_is_on'] ) { ?>
				<table class="form-table">
					<tr>
						<th></th>
						<td>
				    		<input type="submit" name="submit_to_firebase" class="button button-secondary" value="<?php _e( 'Update to Firebase API', 'google-shortlink' ); ?>" />
				    		<p class="bws_info"><?php printf( __( 'Due to Google turning down support for goo.gl URL shortener api from %s.', 'google-shortlink' ), '30-03-2018' ); ?></p>
			    		</td>
					</tr>
			 		<!-- shortner api -->
					<tr valign="top">
						<th scope="row"><?php _e( 'API Key For Your goo.gl Account', 'google-shortlink' ); ?></th>
						<td>
							<input name="gglshrtlnk_api-key" id="gglshrtlnk_api-key" type="text" maxlength="250" value="<?php echo $this->options[ 'api_key' ]; ?>" />
							<p class="bws_info">
								<?php _e( 'To get API key you should go to', 'google-shortlink' ); ?>
								<a href="https://code.google.com/apis/console" target="_blank">Google API Console</a>.
								<?php _e( 'Create project there and insert public API key above.', 'google-shortlink' ); ?><br />
							</p>
						</td>
					</tr>
				</table>
				<!-- eng shortner api -->
				<!-- Firebase -->
			<?php } else { ?>				
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'API Key For Your Firebase Dynamic Links Account', 'google-shortlink' ); ?></th>
						<td>
							<input name="gglshrtlnk_api_key_for_firebase" id="gglshrtlnk_api_key_for_firebase" type="text" maxlength="500" value="<?php echo$this->options['api_key_for_firebase']; ?>" />
							<p class="bws_info">
								<?php _e( 'To get API key you should go to', 'google-shortlink' ); ?>
								<a href="https://code.google.com/apis/console" target="_blank">Google API Console</a>.
								<?php _e( 'Create project there and insert public API key above.', 'google-shortlink' ); ?><br />
							</p>
							<p class="bws_info">
								<?php _e( 'Don\'t know how to get API key? Follow this instruction - ', 'google-shortlink' ); ?>
								<a href="https://bestwebsoft.com/documentation/shortlink/firebase-dynamic-links-api/" target="_blank">Firebase Dynamic Links API</a>.
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Client ID', 'google-shortlink' ); ?></th>
						<td>
							<input name="gglshrtlnk_client_id" id="gglshrtlnk_client_id" type="text" maxlength="250" value="<?php echo $this->options['client_id']; ?>" />
						</td>
                	</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Client Secret', 'google-shortlink' ); ?></th>
						<td>
							<input name="gglshrtlnk_client_secret" id="gglshrtlnk_client_secret" type="text" maxlength="250" value="<?php echo $this->options['client_secret']; ?>" />
						</td>
               		</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Redirect URI', 'google-shortlink' ); ?></th>
						<td>
							<span id="gglshrtlnk_to_copy"><code><?php echo plugin_dir_url( dirname(__FILE__) ) . 'oauth2.php'; ?></code></span>
							<button id="gglshrtlnk_copy_to_clipboard"><span class="dashicons dashicons-admin-page"></span></button>
							<p class="bws_info">
								<?php _e( 'Copy this link and go to your', 'google-sortlink' ); ?> <a href="https://console.developers.google.com" target="_blank"><?php _e( 'API console', 'google-sortlink' ); ?></a>
							</p>
						</td>
                	</tr>
				</table>
				<!-- Domain Link for Google project -->
				<div class="bws_tab_sub_label"><?php _e( 'Firebase Console', 'google-shortlink' ); ?></div>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Domain Link for your google project', 'google-shortlink' ); ?></th>
						<td>
							<input name="gglshrtlnk_domain_link" id="gglshrtlnk_domain_link" type="text" maxlength="250" value="<?php echo $this->options['domain_link']; ?>" />
						</td>
					</tr>
				</table>
			<?php } 
			/* end deprecated */ ?>
			<div class="bws_tab_sub_label"><?php _e( 'General', 'google-shortlink' ); ?></div>
			<div class="results below-h2 gglshrtlnk_hide updated" id="gglshrtlnk_ajax-status"></div>
			<table class="form-table">
				<!-- End Firebase -->
				<tr valign="top">
					<th><?php _e( 'Scan Website', 'google-shortlink' ); ?></th>
					<td>
						<fieldset>
							<!-- scan web-site to find all external links -->
							<input type="button" name="gglshrtlnk_scan" class="button-secondary gglshrtlnk_btn_action" value="<?php _e( 'Scan Now', 'google-shortlink' ); ?>"/>
							<p class="bws_info"><?php _e( 'Your website will be scanned for new external links.', 'google-shortlink' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th><?php _e( 'External Links', 'google-shortlink' ); ?></th>
					<td>
						<fieldset class="btn-group">
							<!-- replace automatically -->
							<input type="button" name="gglshrtlnk_replace-all" id="gglshrtlnk_replace-all" value="<?php _e( 'Replace Now', 'google-shortlink' ); ?>" class="button-secondary gglshrtlnk_btn_action"/>
							<!-- restore all -->
							<input type="button" name="gglshrtlnk_restore-all" id="gglshrtlnk_restore-all" value="<?php _e( 'Restore Now', 'google-shortlink' ); ?>" class="button-secondary gglshrtlnk_btn_action"/>
							<!-- delete all -->
							<input type="button" name="gglshrtlnk_delete-all" id="gglshrtlnk_delete-all" value="<?php _e( 'Restore & Clean DB Now', 'google-shortlink' ); ?>" class="button-secondary gglshrtlnk_btn_action"/>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		/**
		* @deprecated since 1.5.9
		* @todo remove after 20.09.2021
		*/
		/**
		 * Display custom error\message\notice
		 * @access public
		 * @return void
		 */
		public function display_custom_messages() {
			if ( 0 == $this->options['firebase_api_is_on'] ) { ?>
				<div class="updated inline bws-notice"><p><strong><?php _e( 'The goo.gl API functionality has been deprecated. On 20.09.2021 it will be removed from our plugin. Hurry up to switch to Firebase API', 'google-shortlink' ); ?></strong></p></div>
			<?php }
		}
		/* end deprecated */ 
	}
}