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

use WMQ\src\QUIZ;
use WMQ\src\Helpers;

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
		add_action('wp_enqueue_scripts', [ $this, 'wmq_remove_default_style' ]);
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
		$links[] = $new_link;
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

		if ( get_option('wmq_get_values') == '' ) {
			add_option('wmq_get_values');
		}
	}
	/**
	 * Bootstapping the normal values
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
		wp_enqueue_script('jvectormap-jquery', WMQ_DIR_URL . 'assets/js/jquery-jvectormap-1.1.1.min.js', [ 'jquery' ], null, true);
		wp_enqueue_script('jvectormap-world-mill', WMQ_DIR_URL . 'assets/js/jquery-jvectormap-world-mill.js', [ 'jquery' ], null, true);
		wp_enqueue_script('wmq-world-map', WMQ_DIR_URL . 'assets/js/wmq-world-map.js', [ 'jquery' ], null, true);
		wp_enqueue_script('wmq-quiz', WMQ_DIR_URL . 'assets/js/quiz.js', wp_rand(111, 999), null, true);
	}
	/**
	 * Enqueue the admin assets
	 *
	 * @param string $hook return the specific admin slug.
	 */
	public function wmq_admin_assets( $hook ) {
		if ( 'toplevel_page_wmq' == $hook ) {
			wp_enqueue_style('bootstrap-css', '//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', '', null);
			wp_enqueue_style('wmq-admin-css', WMQ_DIR_URL . 'assets/css/admin/admin.css', '', rand(111, 999), 'all');
			wp_enqueue_script('poppper-js', '//cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js', [ 'jquery' ], null, true);
			wp_enqueue_script('bootstrap-js', '//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js', [ 'jquery' ], null, true);
			wp_enqueue_script('swal-js', '//cdn.jsdelivr.net/npm/sweetalert2@11', [ 'jquery' ], null, true);
			wp_enqueue_script('wmq-admin-quiz', WMQ_DIR_URL . 'assets/js/admin/admin.js', [ 'jquery' ], rand(111, 999), true);
			$wmq_nonce = wp_create_nonce('wmq_quiz_nonce');
			$ajax_url = admin_url('admin-ajax.php');
			wp_localize_script('wmq-admin-quiz', 'wmq_quiz_option', [
				'ajax_url' => $ajax_url,
				'nonce' => $wmq_nonce,
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
			$values    = isset($_POST['task']) ? $sanitize_val->wmq_filter_values(wp_unslash($_POST['task'])) : '';
			$get_key   = [];
			$get_value = [];
			foreach ( $values as $value ) {
				$get_key[]   = $value['name'];
				$get_value[] = $value['value'];
			}
			$get_value_array = array_combine($get_key, $get_value);
			update_option('wmq_get_values', $get_value_array);
			die();
		} else {
			return false;
		}
	}

	/**
	 * Remove Default Style of Template
	 */
	public function wmq_remove_default_style() {

		if ( is_page_template('world-map-quiz') ) {
			$theme        = wp_get_theme();
			$parent_style = $theme->stylesheet . '-style';

			wp_dequeue_style($parent_style);
			wp_deregister_style($parent_style);
			wp_deregister_style($parent_style . '-css');
		}
	}

	/**
	 * Default Settings of the Plugin
	 */
	public function wmq_settings_content() {
		?>
		<div class="wrap">
			<h1><?php _esc_html_e('World Map Quiz Option', 'wmq'); ?></h1>
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
					<?php $this->get_options($this->get_optionvalues, $this->get_keys, $this->tabvalues); ?>
					<input type="submit" name="wmq_get_values" id="wmq-submit" class="btn btn-primary" value="Save">
				</div>
			</form>
			<div class="wmq-get-data"></div>
		</div>
		<?php
	}
	/**
	 * Get the inputs option values
	 *
	 * @param string $values set of input values.
	 *
	 * @param int    $number return the specific admin slug.
	 *
	 * @param string $wrapper to set the wrapper class.
	 */
	public function get_options( $values, $number, $wrapper ) {

		$fields = [
			[
				'label'       => __('Heading', 'wmq'),
				'type'        => 'text',
				'name'        => 'heading',
				'placeholder' => __('Heading Title', 'wmq'),
				'task'        => 'heading',
				'id'          => 'wmq_heading',
				'value'       => isset($values['heading']) ? esc_html($values['heading']) : 'Heading Title of the World Map',
			],
			[
				'label'       => __('Sub Heading', 'wmq'),
				'type'        => 'text',
				'name'        => 'subheading',
				'placeholder' => __('Sub Heading ', 'wmq'),
				'task'        => 'subheading',
				'id'          => 'wmq_subheading',
				'value'       => isset($values['subheading']) ? esc_html($values['subheading']) : 'Sub Heading of the World Map',
			],
			[
				'label'       => __('Quiz Time', 'wmq'),
				'type'        => 'number',
				'name'        => 'quiz_time',
				'placeholder' => '720',
				'task'        => 'quiz_time',
				'id'          => 'wmq_quiz_time',
				'value'       => isset($values['quiz_time']) ? esc_attr($values['quiz_time']) : '',
			],
			[
				'label'       => __('Header Nav Title', 'wmq'),
				'type'        => 'text',
				'name'        => 'header_nav_title',
				'placeholder' => 'World Map',
				'task'        => 'header_nav_title',
				'id'          => 'wmq_header_nav_title',
				'value'       => isset($values['header_nav_title']) ? esc_html($values['header_nav_title']) : '',
			],
			[
				'label'       => __('Header Span Title', 'wmq'),
				'type'        => 'text',
				'name'        => 'header_span_title',
				'placeholder' => 'Quiz',
				'task'        => 'header_span_title',
				'id'          => 'wmq_header_span_title',
				'value'       => isset($values['header_span_title']) ? esc_html($values['header_span_title']) : '',
			],
			[
				'label'       => __('Unlimited Timer Text', 'wmq'),
				'type'        => 'text',
				'name'        => 'wmq_timer_text',
				'placeholder' => 'Timer!!',
				'task'        => 'wmq_timer_text',
				'id'          => 'wmq_timer_text',
				'value'       => isset($values['wmq_timer_text']) ? esc_html($values['wmq_timer_text']) : '',
			],
			[
				'label'       => __('Give Up Title', 'wmq'),
				'type'        => 'text',
				'name'        => 'wmq_give_up_title',
				'placeholder' => 'Give Up?',
				'task'        => 'wmq_give_up_title',
				'id'          => 'wmq_give_up_title',
				'value'       => isset($values['wmq_give_up_title']) ? esc_html($values['wmq_give_up_title']) : '',
			],
			[
				'label'       => __('Try Again Title', 'wmq'),
				'type'        => 'text',
				'name'        => 'wmq_try_again_title',
				'placeholder' => 'Try Again',
				'task'        => 'wmq_try_again_title',
				'id'          => 'wmq_try_again_title',
				'value'       => isset($values['wmq_try_again_title']) ? esc_html($values['wmq_try_again_title']) : '',
			],
			[
				'label' => __('Header Span Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'header_span_color',
				'task'  => 'header_span_color',
				'id'    => 'wmq_header_span_color',
				'value' => isset($values['header_span_color']) ? esc_attr($values['header_span_color']) : '#95d1b1',
			],
			[
				'label'       => __('Map Width', 'wmq'),
				'type'        => 'number',
				'name'        => 'map_width',
				'placeholder' => '950',
				'task'        => 'map_width',
				'id'          => 'wmq_map_width',
				'value'       => isset($values['map_width']) ? esc_attr($values['map_width']) : 950,
			],
			[
				'label'       => __('Map Height', 'wmq'),
				'type'        => 'number',
				'name'        => 'map_height',
				'placeholder' => '450',
				'task'        => 'map_height',
				'id'          => 'wmq_map_height',
				'value'       => isset($values['map_height']) ? esc_attr($values['map_height']) : 450,
			],
			[
				'label' => __('World BG Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'world_bg_color',
				'task'  => 'world_bg_color',
				'id'    => 'world_bg_color',
				'value' => isset($values['world_bg_color']) ? esc_attr($values['world_bg_color']) : '#809fff',
			],
			[
				'label' => __('Nav Background Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'nav_background_color',
				'task'  => 'nav_background_color',
				'id'    => 'wmq_nav_background_color',
				'value' => isset($values['nav_background_color']) ? esc_attr($values['nav_background_color']) : '#1e4068',
			],
			[
				'label' => __('Country Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'country_color',
				'task'  => 'country_color',
				'id'    => 'country_color',
				'value' => isset($values['country_color']) ? esc_attr($values['country_color']) : '#ffffff',
			],
			[
				'label' => __('Score Country Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'score_country_color',
				'task'  => 'score_country_color',
				'id'    => 'score_country_color',
				'value' => isset($values['score_country_color']) ? esc_attr($values['score_country_color']) : '#ffff00',
			],
			[
				'label' => __('Hover Country Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'hover_country_color',
				'task'  => 'hover_country_color',
				'id'    => 'hover_country_color',
				'value' => isset($values['hover_country_color']) ? esc_attr($values['hover_country_color']) : '#dedede',
			],
			[
				'label' => __('All Answer Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'wmq_all_answer',
				'task'  => 'wmq_all_answer',
				'id'    => 'wmq_all_answer',
				'value' => isset($values['wmq_all_answer']) ? esc_attr($values['wmq_all_answer']) : '#f44336',
			],
			[
				'label' => __('Correct Answer Color', 'wmq'),
				'type'  => 'color',
				'name'  => 'wmq_correct_answer',
				'task'  => 'wmq_correct_answer',
				'id'    => 'wmq_correct_answer',
				'value' => isset($values['wmq_correct_answer']) ? esc_attr($values['wmq_correct_answer']) : '#0000ff',
			],
		];

		if ( count($fields) > $number ) {
			$helpers     = new Helpers();
			$first_array = array_chunk($fields, $number);
			if ( $first_array[0] ) {
				$content = '';
				printf('<div class="tab-pane fade show active" id="pills-%s" role="tabpanel" aria-labelledby="pills-%s-tab">', esc_attr($wrapper[0]), esc_attr($wrapper[0]));
				foreach ( $first_array[0] as $field ) {
					$content .= sprintf('%s', $helpers->input_switch($field));
				}
				printf('%s', '</div>');
			}
			if ( $first_array[1] ) {
				printf('<div class="tab-pane fade" id="pills-%s" role="tabpanel" aria-labelledby="pills-%s-tab">', esc_attr($wrapper[1]), esc_attr($wrapper[1]));
				foreach ( $first_array[1] as $field ) {
					$content .= sprintf('%s', $helpers->input_switch($field));
				}
				printf('%s', '</div>');
			}
			if ( $first_array[2] ) {
				printf('<div class="tab-pane fade" id="pills-%s" role="tabpanel" aria-labelledby="pills-%s-tab">', esc_attr($wrapper[2]), esc_attr($wrapper[2]));
				foreach ( $first_array[2] as $field ) {
					$content .= sprintf('%s', $helpers->input_switch($field));
				}
				printf('%s', '</div>');
			}
		} else {
			return false;
		}
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
				$quiz     = new QUIZ();
				$template = $quiz->quiz_init();
			}
		}
		return $template;
	}
}
new WorldMapQuiz();