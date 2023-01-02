<?php
/**
 * $Id: sitemap.php 2823802 2022-11-24 18:12:38Z auctollo $

 *  Google XML Sitemaps Generator for WordPress
 * ==============================================================================

 * This generator will create a sitemaps.org compliant sitemap of your WordPress site.

 * For additional details like installation instructions, please check the readme.txt and documentation.txt files.

 * Have fun!
 * Arne

 * Info for WordPress:
 * ==============================================================================
 * Plugin Name: Google XML Sitemaps
 * Plugin URI: https://auctollo.com/
 * Description: This plugin improves SEO using sitemaps for best indexation by search engines like Google, Bing, Yahoo and others.
 * Version: 4.1.8
 * Author: Auctollo
 * Author URI: https://acutollo.com/
 * Text Domain: sitemap
 * Domain Path: /lang


 * Copyright 2005 - 2018 ARNE BRACHHOLD  (email : himself - arnebrachhold - de)

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author Arne Brachhold
 * @package sitemap
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 * Please see license.txt for the full license.
 */

define( 'SM_SUPPORTFEED_URL', 'https://wordpress.org/support/plugin/google-sitemap-generator/feed/' );
define( 'SM_BETA_USER_INFO_URL', 'https://rmh2kgz0oi.execute-api.us-east-2.amazonaws.com/test/user/getBetaInfo' );
define( 'SM_BANNER_HIDE_DURATION_IN_DAYS', 7 );

add_action( 'admin_init', 'register_consent', 1 );
add_action( 'admin_head', 'ga_header' );
add_action( 'admin_footer', 'ga_footer' );


/**
 * Google analytics .
 */
function ga_header() {
	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		global $wp_version;
		$window_url = 'http://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
		$parts      = wp_parse_url( $window_url );
		$current_page = '';

		$plugin_version = GoogleSitemapGeneratorLoader::get_version();

		$default_value = 'not_defined';
		$consent_value = get_option( 'sm_user_consent', $default_value );

		if ( 'yes' === $consent_value || $default_value === $consent_value ) {
			echo "
			<script>
				(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','GTM-WT5FH38');
				
				</script>
				";
		}
		if ( $default_value !== $consent_value ) {
			echo "<script>
			setTimeout(()=>{
				document.querySelector(\"[id=\'modal-wrapper\']\").style.display = 'none'
			},200);
			</script>";
		}
		if ( isset( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
			if ( isset( $query['page'] ) ) {
				$current_page = $query['page'];
				if ( strpos( $current_page, 'google-sitemap-generator' ) !== false ) {
					if ( $default_value !== $consent_value ) {
						echo "<script>
						setTimeout(()=>{
							document.querySelector(\"[id=\'modal-wrapper\']\").style.visibility = 'hidden'
						},200);
						</script>";
					}
						echo "
						<script>
							setTimeout(()=>{
								document.querySelector(\"[id=\'modal-wrapper\']\").style.display = 'flex'
							document.getElementById('discard_content').classList.remove('discard_button_outside_settings')
							document.getElementById('discard_content').classList.add('discard_button')
							if( document.getElementById(\"user-consent-form\") ){
								const form = document.getElementById(\"user-consent-form\")
								var plugin_version = document.createElement(\"input\")
								plugin_version.name = \"plugin_version\"
								plugin_version.id = \"plugin_version\"
								plugin_version.value = \"<?php echo $wp_version;?>\"
								plugin_version.type = \"hidden\"
								form.appendChild(plugin_version)
								var wordpress_version = document.createElement(\"input\")
								wordpress_version.name = \"wordpress_version\"
								wordpress_version.id = \"wordpress_version\"
								wordpress_version.value = '$wp_version'
								wordpress_version.type = \"hidden\"
								form.appendChild(wordpress_version)
							}

							},200);
						</script>";
				} else {
					echo '<script>
					setTimeout(()=>{
						
					document.getElementById("discard_content").classList.add("discard_button_outside_settings")
					document.getElementById("discard_content").classList.remove("discard_button")
				},200);
					</script>';
				}
			} else {
				echo '<script>
				setTimeout(()=>{
					
				document.getElementById("discard_content").classList.add("discard_button_outside_settings")
				document.getElementById("discard_content").classList.remove("discard_button")
			},200);
				</script>';
			}
		} else {
			echo "<script>
			setTimeout(()=>{
				document.getElementById(\"discard_content\").classList.add(\"discard_button_outside_settings\")
				document.getElementById(\"discard_content\").classList.remove(\"discard_button\")
				if( document.getElementById(\"user-consent-form\") ){
					const form = document.getElementById(\"user-consent-form\")
					var plugin_version = document.createElement(\"input\")
					plugin_version.name = \"plugin_version\"
					plugin_version.id = \"plugin_version\"
					plugin_version.value = '$plugin_version'
					plugin_version.type = \"hidden\"
					form.appendChild(plugin_version)

					var wordpress_version = document.createElement(\"input\")
					wordpress_version.name = \"wp_version\"
					wordpress_version.id = \"wp_version\"
					wordpress_version.value = '$wp_version'
					wordpress_version.type = \"hidden\"
					form.appendChild(wordpress_version)
				}
			},200);
				</script>";
			return;
		}
	}
}

/**
 * Google analytics .
 */
function ga_footer() {
	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		echo "<script>
		document.querySelector(\"[name='user_consent']\")
		.addEventListener('click', function (event) {
			event.preventDefault();
			document.getElementById('action').value = \"yes\";
			document.querySelector(\"[name='user_consent']\").closest(\"form\").submit();
		});
		document.querySelector(\"[name=\'discard_consent\']\").addEventListener(\"click\", 
			function(event) {
			
			event.preventDefault();
			
			document.getElementById(\"action\").value = \"no\"; 
			setTimeout(()=>{
				document.querySelector(\"[name=\'discard_consent\']\").closest(\"form\").submit();
			},40)
		});
		</script>";
		$banner_discarded_count = get_option( 'sm_beta_banner_discarded_count' );
		if ( 1 === $banner_discarded_count || '1' === $banner_discarded_count ) {
			echo '<script>
			document.getElementById("discard_content").classList.add("reject_consent")
			document.getElementById("discard_content").classList.remove("discard_button")
			</script>';
		}
	}
}

/**
 * Check if the requirements of the sitemap plugin are met and loads the actual loader
 *
 * @package sitemap
 * @since 4.0
 */
function sm_setup() {

	$fail = false;

	// Check minimum PHP requirements, which is 5.2 at the moment.
	if ( version_compare( PHP_VERSION, '5.2', '<' ) ) {
		add_action( 'admin_notices', 'sm_add_php_version_error' );
		$fail = true;
	}

	// Check minimum WP requirements, which is 3.3 at the moment.
	if ( version_compare( $GLOBALS['wp_version'], '3.3', '<' ) ) {
		add_action( 'admin_notices', 'sm_add_wp_version_error' );
		$fail = true;
	}

	if ( ! $fail ) {
		require_once trailingslashit( dirname( __FILE__ ) ) . 'class-googlesitemapgeneratorloader.php';
	}

}

/**
 * Adds a notice to the admin interface that the WordPress version is too old for the plugin
 *
 * @package sitemap
 * @since 4.0
 */
function sm_add_wp_version_error() {
	/* translators: %s: search term */

	echo '<div id=\'sm-version-error\' class=\'error fade\'><p><strong>' . esc_html( __( 'Your WordPress version is too old for XML Sitemaps.', 'sitemap' ) ) . '</strong><br /> ' . esc_html( sprintf( __( 'Unfortunately this release of Google XML Sitemaps requires at least WordPress %4$s. You are using WordPress %2$s, which is out-dated and insecure. Please upgrade or go to <a href=\'%1$s\'>active plugins</a> and deactivate the Google XML Sitemaps plugin to hide this message. You can download an older version of this plugin from the <a href=\'%3$s\'>plugin website</a>.', 'sitemap' ), 'plugins.php?plugin_status=active', esc_html( $GLOBALS['wp_version'] ), 'http://www.arnebrachhold.de/redir/sitemap-home/', '3.3' ) ) . '</p></div>';
}

/**
 * Adds a notice to the admin interface that the WordPress version is too old for the plugin
 *
 * @package sitemap
 * @since 4.0
 */
function sm_add_php_version_error() {
	/* translators: %s: search term */

	echo '<div id=\'sm-version-error\' class=\'error fade\'><p><strong>' . esc_html( __( 'Your PHP version is too old for XML Sitemaps.', 'sitemap' ) ) . '</strong><br /> ' . esc_html( sprintf( __( 'Unfortunately this release of Google XML Sitemaps requires at least PHP %4$s. You are using PHP %2$s, which is out-dated and insecure. Please ask your web host to update your PHP installation or go to <a href=\'%1$s\'>active plugins</a> and deactivate the Google XML Sitemaps plugin to hide this message. You can download an older version of this plugin from the <a href=\'%3$s\'>plugin website</a>.', 'sitemap' ), 'plugins.php?plugin_status=active', PHP_VERSION, 'http://www.arnebrachhold.de/redir/sitemap-home/', '5.2' ) ) . '</p></div>';
}

/**
 * Returns the file used to load the sitemap plugin
 *
 * @package sitemap
 * @since 4.0
 * @return string The path and file of the sitemap plugin entry point
 */
function sm_get_init_file() {
	return __FILE__;
}

/**
 * Register beta user consent function.
 */
function register_consent() {
	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		if ( isset( $_POST['user_consent_yes'] ) ) {
			update_option( 'sm_user_consent', 'yes' );
		}
		if ( isset( $_POST['user_consent_no'] ) ) {
			update_option( 'sm_user_consent', 'no' );
		}
		if ( isset( $_POST['action'] ) ) {
			if ( 'yes' === $_POST['action'] ) {
				$plugin_version = GoogleSitemapGeneratorLoader::get_version();
				global $wp_version;
				$user      = wp_get_current_user();
				$user_id   = $user->ID;
				$mydomain  = $user->user_url ? $user->user_url : 'https://' . $_SERVER['HTTP_HOST'];
				$user_name = $user->user_nicename;
				$useremail = $user->user_email;
				global $wpdb;
				$result             = $wpdb->get_results( "select user_id,meta_value from wp_usermeta where meta_key='session_tokens' and user_id=" . $user_id ); // phpcs:ignore
				$user_login_details = unserialize( $result[0]->meta_value );
				$last_login         = '';
				foreach ( $user_login_details as $item ) {
					$last_login = $item['login'];
				}
				$data     = array(
					'domain'         => $mydomain,
					'userID'         => $user_id,
					'userEmail'      => $useremail,
					'userName'       => $user_name,
					'lastLogin'      => $last_login,
					'wp_version'     => $wp_version,
					'plugin_version' => $plugin_version,
				);
				$args     = array(
					'headers' => array(
						'Content-type : application/json',
					),
					'method'  => 'POST',
					'body'    => wp_json_encode( $data ),
				);
				$response = wp_remote_post( SM_BETA_USER_INFO_URL, $args );
				$body     = json_decode( $response['body'] );
				if ( 200 === $body->status ) {
					add_option( 'sm_show_beta_banner', 'false' );
					update_option( 'sm_beta_banner_discarded_count', (int) 2 );
				}
			}
			if ( 'no' === $_POST['action'] ) {
				if ( $_SERVER['QUERY_STRING'] ) {
					update_option( 'sm_show_beta_banner', 'false' );
					$count = get_option( 'sm_beta_banner_discarded_count' );
					if ( gettype( $count ) !== 'boolean' ) {
						update_option( 'sm_beta_banner_discarded_count', (int) $count + 1 );
					} else {
						add_option( 'sm_beta_banner_discarded_on', gmdate( 'Y/m/d' ) );
						update_option( 'sm_beta_banner_discarded_count', (int) 1 );
					}
				} else {
					add_option( 'sm_beta_notice_dismissed_from_wp_admin', 'true' );
				}
			}
		}
	}
}


// Don't do anything if this file was called directly.
if ( defined( 'ABSPATH' ) && defined( 'WPINC' ) && ! class_exists( 'GoogleSitemapGeneratorLoader', false ) ) {
	sm_setup();
	add_filter( 'wp_sitemaps_enabled', '__return_false' );

}

