<?php

/*
 * Plugin Name: The Void
 * Description: Removes all content in 1 click
 * Author: Alan Cesarini
 * Version: 1.0.1
 * Author URI: http://alancesarini.com
 * License: GPL2+
 */

class The_Void {

	private static $_this;

	private static $_version;

	private $obliterator;

	function __construct() {
	
		if( isset( self::$_this ) )
			wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.', get_class( $this ) ) );
		self::$_this = $this;

		self::$_version = '1.0.1';

		require( 'includes/class_obliterator.php' );

		$this->obliterator = new TV_Obliterator();

		add_action( 'wp_loaded', array( $this, 'register_assets' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		add_action( 'wp_ajax_remove_posts', array( $this, 'remove_posts' ) );

		add_action( 'wp_ajax_remove_terms', array( $this, 'remove_terms' ) );

	}
	
	static function this() {
	
		return self::$_this;
	
	}

	function add_menu_item() {

		$page = add_management_page( __( 'The Void', 'thevoid' ), __( 'The Void', 'thevoid' ), 'manage_options', 'the_void', array( $this, 'render_admin_page' ) );
	
	}

	function render_admin_page() {

	?>
		<div class="wrap">
			<div class="tv-container">
				<div class="tv-content">
					<h2>The Void</h2>
					<div class="tv-button">
						<a href="#" id="tv-removeall" class="button button-primary"><?php _e( 'Delete all content', 'thevoid' ); ?></a>
					</div>
					<div class="tv-result-container">
						<div class="tv-result tv-posts">Deleting posts, pages and custom post types... <img src="<?php echo plugin_dir_url( __FILE__ ); ?>assets/images/ajax-loader.gif" /></div>
						<div class="tv-result tv-terms">Deleting categories, tags and taxonomy terms... <img src="<?php echo plugin_dir_url( __FILE__ ); ?>assets/images/ajax-loader.gif" /></div>
					</div>
					<script>var home_url = '<?php echo home_url(); ?>';</script>
				</div>
			</div>
		</div>

	<?php
		
	}

	function register_assets() {

		wp_register_script( 'tv-admin-js', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), self::$_version );
		wp_register_style( 'tv-admin-style', plugins_url( 'assets/css/admin.css', __FILE__ ), false, self::$_version );

	}

	function enqueue_assets() {

		wp_enqueue_script( 'tv-admin-js' );
		wp_enqueue_style( 'tv-admin-style' );

	}

	function remove_posts() {

		$post_types = get_post_types();

		$array_result = array();
		foreach( $post_types as $type ) {
			$array_result[ $type ] = $this->obliterator->remove_posts( $type );
		}

		echo json_encode( $array_result );
		die();

	}	

	function remove_terms() {

		$taxonomies = get_taxonomies();

		$array_result = array();
		foreach( $taxonomies as $tax ) {
			$array_result[ $tax ] = $this->obliterator->remove_terms( $tax );
		}

		echo json_encode( $array_result );
		die();

	}

}

set_time_limit( 300 );

new The_Void();
