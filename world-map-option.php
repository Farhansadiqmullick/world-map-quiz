<?php
/*
Plugin Name: World Map Quiz
Plugin URI:
Description: World Map Quiz
Version: 1.0
Author: WPPOOL
License: GPLv2 or later
Text Domain: wmq
Domain Path: /languages/
*/

namespace WMQ;

if ( ! defined('ABSPATH') ) {
	exit;
}

use WMQ\src\Quiz;
use WMQ\src\Helpers;
use WMQ\src\Option_Set;

require_once dirname(__FILE__) . '/vendor/autoload.php';
/**
 * Main Class Component
 */
class WorldMapQuiz {
	/**
	 * Variables for table values

	 * @var string $tabvalues
	 */
	protected $tabvalues;
	/**
	 * Variables for getting option values

	 * @var string $get_optionvalues
	 */
	protected $get_optionvalues;
	/**
	 * Variables for getting the keys

	 * @var string $get_keys
	 */
	protected $get_keys;

	/**
	 * Constuctor of the class
	 */
	public function __construct() {
		add_action('admin_menu', [ $this, 'wmq_create_settings' ]);
		add_action('wp_enqueue_scripts', [ $this, 'wmq_frontend_assets' ]);
		add_action('admin_enqueue_scripts', [ $this, 'wmq_admin_assets' ]);
		add_action('wp_ajax_wmq_quiz', [ $this, 'wmq_getoption_value' ]);
		add_action('plugins_loaded', [ $this, 'wmq_bootstrap' ]);
		register_deactivation_hook(__FILE__, [ $this, 'wmq_deactivation' ]);
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'wmq_settings_link' ]);

		/**
		 * Function of the Page Templates
		 *
		 * @function return the page template design
		 */
		add_filter('theme_page_templates', [ $this, 'wmq_page_template_dropdown' ]);
		add_filter('template_include', [ $this, 'wmq_change_page_template' ], 99);
		$this->tabvalues        = [ 'content', 'color', 'others' ];
		$this->get_optionvalues = get_option('wmq_get_values');
		$this->get_keys         = 8;
	}

	/**
	 * Counts the number of items in the provided array.
	 *
	 * @param string $links return the admin slug.
	 */
	public function wmq_settings_link( $links ) {
		$new_link = sprintf("<a href='%s'>%s</a>", 'options-general.php?page=wmq', __('Options', 'wmq'));
		$links[]  = $new_link;
		return $links;
	}
	/**
	 * Bootstapping the values
	 */
	public function wmq_bootstrap() {
		global $options;
		if ( ! defined('WMQ_DIR_PATH') ) {
			define('WMQ_DIR_PATH', plugin_dir_path(__FILE__));
		}
		if ( ! defined('WMQ_DIR_URL') ) {
			define('WMQ_DIR_URL', plugin_dir_url(__FILE__));
		}
		load_plugin_textdomain('wmq', false, WMQ_DIR_PATH . '/languages');

		if ( get_option('wmq_get_values') === '' ) {
			add_option('wmq_get_values');
		}
	}
	/**
	 * Delete the values upon deactivation
	 */
	public function wmq_deactivation() {
		delete_option('wmq_get_values');
	}

	/**
	 * Enqueue the frontend assets
	 */
	public function wmq_frontend_assets() {

		wp_enqueue_style('bootstrap-css', '//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', '', null);
		wp_enqueue_style('jevctormap-css', WMQ_DIR_URL . 'assets/css/frontend/jvectormap.css', '', wp_rand(111, 999), 'all');
		wp_enqueue_style('wmq-frontend-css', WMQ_DIR_URL . 'assets/css/frontend/style.css', '', wp_rand(111, 999), 'all');
		wp_enqueue_style('wmq-admin-css', WMQ_DIR_URL . 'assets/css/admin/admin.css', '', wp_rand(111, 999), 'all');
		wp_enqueue_script('jvectormap-jquery', WMQ_DIR_URL . 'assets/js/jquery-jvectormap-1.1.1.min.js', [ 'jquery' ], null, false);
		wp_enqueue_script('jvectormap-world-mill', WMQ_DIR_URL . 'assets/js/jquery-jvectormap-world-mill.js', [ 'jquery' ], null, false);
		wp_enqueue_script('wmq-world-map', WMQ_DIR_URL . 'assets/js/wmq-world-map.js', [ 'jquery' ], wp_rand(111, 999), false);
		wp_enqueue_script('wmq-quiz', WMQ_DIR_URL . 'assets/js/quiz.js', [ 'jquery' ], wp_rand(111, 999), false);
	}
	/**
	 * Enqueue the admin assets
	 *
	 * @param string $hook return the specific admin slug.
	 */
	public function wmq_admin_assets( $hook ) {
		if ( 'toplevel_page_wmq' === $hook ) {
			wp_enqueue_style('bootstrap-css', '//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', '', null);
			wp_enqueue_style('wmq-admin-css', WMQ_DIR_URL . 'assets/css/admin/admin.css', '', wp_rand(111, 999), 'all');
			wp_enqueue_script('poppper-js', '//cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js', [ 'jquery' ], null, true);
			wp_enqueue_script('bootstrap-js', '//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js', [ 'jquery' ], null, true);
			wp_enqueue_script('swal-js', '//cdn.jsdelivr.net/npm/sweetalert2@11', [ 'jquery' ], null, true);
			wp_enqueue_script('wmq-admin-quiz', WMQ_DIR_URL . 'assets/js/admin/admin.js', [ 'jquery' ], wp_rand(111, 999), true);
			$wmq_nonce = wp_create_nonce('wmq_quiz_nonce');
			$ajax_url  = admin_url('admin-ajax.php');
			wp_localize_script('wmq-admin-quiz', 'wmq_quiz_option', [
				'ajax_url' => $ajax_url,
				'nonce'    => $wmq_nonce,
			]);
		}
	}

	/**
	 * Option Page Settings
	 */
	public function wmq_create_settings() {
		$page_title = __('World Map Quiz', 'wmq');
		$menu_title = __('World Map Quiz', 'wmq');
		$capability = 'manage_options';
		$slug       = 'wmq';
		$callback   = [ $this, 'wmq_settings_content' ];
		$icon       = WMQ_DIR_URL . 'images/world_map.png';

		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon);
	}

	/**
	 * Get Option Values
	 */
	public function wmq_getoption_value() {
		$wmq_nonce_verify = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if ( wp_verify_nonce($wmq_nonce_verify, 'wmq_quiz_nonce') ) {
			$sanitize_val = new Helpers();
			//phpcs:ignore
			$values    = isset($_POST['task']) ? wp_kses_allowed_html($_POST['task']) : '';
			$get_key   = [];
			$get_value = [];
			foreach ( $values as $value ) {
				$get_key[]   = sanitize_text_field($value['name']);
				$get_value[] = sanitize_text_field($value['value']);
			}
			$get_value_array = array_combine($get_key, $get_value);
			update_option('wmq_get_values', $get_value_array);
			die();
		} else {
			return false;
		}
	}

	/**
	 * Default Settings of the Plugin
	 */
	public function wmq_settings_content() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e('World Map Quiz Option', 'wmq'); ?></h1>
			<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
				<?php
				foreach ( $this->tabvalues as $key => $tab ) {
					printf('<li class="nav-item" role="presentation">
                            <button class="nav-link %s" id="pills-%s-tab" data-bs-toggle="pill" 
                            data-bs-target="#pills-%s" type="button" role="tab" aria-controls="pills-%s" 
                            aria-selected="true">%s</button>
                        </li>', esc_attr($key) === 0 ? 'active' : '', esc_attr($tab), esc_attr($tab), esc_attr($tab), esc_attr(ucwords($tab)));
				}
				?>
			</ul>
			<form action="options.php" id="wmq-quiz-form" method="post">
				<div class="tab-content" id="pills-tabContent">
					<?php
					$get_option_values = new Option_Set();
					$get_option_values->get_options($this->get_optionvalues, $this->get_keys, $this->tabvalues);
					?>
					<input type="submit" name="wmq_get_values" id="wmq-submit" class="btn btn-primary" value="Save">
				</div>
			</form>
			<div class="wmq-get-data"></div>
		</div>
		<?php

	}

	/**
	 * Admin Template dropdown Item
	 *
	 * @param string $templates return the specific admin slug.
	 */
	public function wmq_page_template_dropdown( $templates ) {
		$wmq_template                   = [];
		$wmq_template['world-map-quiz'] = __('World Map Template', 'wmq');
		$templates                      = array_merge($templates, $wmq_template);
		return $templates;
	}

	/**
	 * Admin Page Change Template
	 *
	 * @param string $template return all the template value.
	 */
	public function wmq_change_page_template( $template ) {
		if ( is_page() ) {
			global $post;
			$meta = get_post_meta($post->ID);
			if ( ! empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] !== $template ) {
				$quiz     = new Quiz();
				$template = $quiz->quiz_init();
			}
		}
		return $template;
	}
}
new WorldMapQuiz();