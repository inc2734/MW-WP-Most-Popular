<?php
/*
	Plugin Name: MW WP Most Popular
	Plugin URI: http://2inc.org
	Description: It is the change version of "WP Most Popular". http://mattgeri.com/projects/wordpress/wp-most-popular
	Version: 0.2.1
	Author: Takashi Kitajima
	Author URI: http://2inc.org
	Modified: August 16, 2012
	
	Original Plugin Name: WP Most Popular
	Original Original Plugin URI: http://mattgeri.com/projects/wordpress/wp-most-popular
	Description: Flexible plugin to show most popular posts based on views
	Original Version: 0.1
	Original Author: Matt Geri
	Original Author URI: http://mattgeri.com
	License: GPL2
	
	Copyright 2011 Matt Geri (email: mattgeri@gmail.com)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class MW_WMP_system{
	const NAME = 'MW_WMP_system';
		
	/**
	 * Constructor
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( self::NAME, 'activation' ) );
		register_uninstall_hook( __FILE__, array( self::NAME, 'uninstall' ) );
		
		include_once( plugin_dir_path( __FILE__ ) . 'system/helpers.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/track.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/widget.php' );
		
		// Use ajax for tracking popular posts
		add_action( 'wp_head', array( $this, 'javascript' ) );
		add_action( 'wp_ajax_mw_wmp_update', array( $this, 'actions' ) );
		// Comment out to stop logging stats for admin and logged in users
		add_action( 'wp_ajax_nopriv_mw_wmp_update', array( $this, 'actions' ) );
		
		// Widget
		add_action( 'widgets_init', array( $this, 'widget' ) );
	}
	
	public function actions() {
		// Check for token
		if ( ! wp_verify_nonce( $_POST['token'], 'MW_WMP_token' ) ) die();
		
		$track = new MW_WMP_track( intval( $_POST['id'] ) );
	}
	
	public function javascript() {
		global $wp_query;
		wp_reset_query();
		wp_print_scripts( 'jquery' );
		$token = wp_create_nonce( 'MW_WMP_token' );
		if ( ! is_front_page() && ( is_singular() ) ) {
			?>
			<!-- MW WP Most Popular -->
			<script type="text/javascript">
			// <![CDATA[
			jQuery( function( $ ) {
				$.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", {
					action: "mw_wmp_update",
					id: ' <?php echo $wp_query->post->ID; ?>',
					token: "<?php echo $token; ?>"
				});
			} );
			// ]]>
			</script><!-- /MW WP Most Popular -->
			<?php
		}
	}
	
	public function widget() {
		register_widget( 'MW_WMP_Widget' );
	}
	
	public static function activation() {
		include_once( plugin_dir_path( __FILE__ ) . 'system/setup.php' );
		MW_WMP_setup::install();
	}

	public static function uninstall() {
		include_once( plugin_dir_path( __FILE__ ) . 'system/setup.php' );
		MW_WMP_setup::uninstall();
	}
}
$MW_WMP_system = new MW_WMP_system();
