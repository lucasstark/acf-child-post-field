<?php

/**
 * Plugin Name: Advanced Custom Fields:  Child Post Field
 * Plugin URI: https://github.com/lucasstark/acf-child-post-field/
 * Description: Adds the ability to manage related posts directly from a parent post.
 * Version: 0.0.0-alpha
 * Author: Lucas Stark
 * Author URI: http://lucasstark.com
 * Requires at least: 4.0
 * Tested up to: 4.1
 *
 * Text Domain: acf_child_post
 * Domain Path: /i18n/languages/
 *
 * @package ACF_Child_Post_Field
 * @category Core
 * @author Lucas Stark
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Child_Post_Field {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new ACF_Child_Post_Field();
		}
	}

	public function __construct() {
		add_action('acf/include_field_types', array($this, 'on_include_field_types'));		
	}
	
	public function on_include_field_types() {
		require_once 'acf-child-post-field-v5.php';
		ACF_Child_Post_Field_V5::register();
	}

}

ACF_Child_Post_Field::register();