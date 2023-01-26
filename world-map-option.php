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

if (!defined('ABSPATH')) {
    exit;
}

use WMQ\src\QUIZ;
use WMQ\src\Helpers;

require_once dirname(__FILE__) . '/vendor/autoload.php';
class WorldMapQuiz
{
    protected $tabvalues;
    protected $getOptionvalues;
    protected $getKeys;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'wmq_create_settings'));
        add_action('wp_enqueue_scripts', array($this, 'wmq_frontend_assets'));
        add_action('wp_enqueue_scripts', array($this, 'wmq_remove_default_style'));
        add_action('admin_enqueue_scripts', array($this, 'wmq_admin_assets'));
        add_action('wp_ajax_wmq_quiz', array($this, 'wmq_get_options_value'));
        add_action('plugins_loaded', array($this, 'wmq_bootstrap'));
        register_deactivation_hook(__FILE__, array($this, 'wmq_deactivation'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'wmq_settngs_link'));

        //page templates
        add_filter('theme_page_templates', array($this, 'wmq_page_template_to_dropdown'));
        add_filter('template_include', array($this, 'wmq_change_page_template'), 99);
        $this->tabvalues = ['content', 'color', 'others'];
        $this->getOptionvalues = get_option('wmq_get_values');
        $this->getKeys = 8;
    }

    public function wmq_settngs_link($links)
    {
        $newLink = sprintf("<a href='%s'>%s</a>", 'options-general.php?page=wmq', __('Options', 'wmq'));
        $links[] = $newLink;
        return $links;
    }

    function wmq_bootstrap()
    {
        global $options;
        if (!defined('WMQ_DIR_PATH')) {
            define('WMQ_DIR_PATH', plugin_dir_path(__FILE__));
        }
        if (!defined('WMQ_DIR_URL')) {
            define('WMQ_DIR_URL', plugin_dir_url(__FILE__));
        }
        load_plugin_textdomain('wmq', false,  WMQ_DIR_PATH . "/languages");

        if (get_option('wmq_get_values') == '') {
            add_option('wmq_get_values');
        }
    }

    function wmq_deactivation()
    {
        delete_option('wmq_get_values');
    }


    function wmq_frontend_assets()
    {
        wp_enqueue_style('bootstrap-css', '//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', '', null);
        wp_enqueue_style('jevctormap-css', WMQ_DIR_URL . 'assets/css/frontend/jvectormap.css', '', rand(111, 999), 'all');
        wp_enqueue_style('wmq-frontend-css', WMQ_DIR_URL . 'assets/css/frontend/style.css', '', rand(111, 999), 'all');
        wp_enqueue_style('wmq-admin-css', WMQ_DIR_URL . 'assets/css/admin/admin.css', '', rand(111, 999), 'all');
        wp_enqueue_script('jvectormap-jquery', WMQ_DIR_URL . 'assets/js/jquery-jvectormap-1.1.1.min.js', ['jquery'], null, true);
        wp_enqueue_script('jvectormap-world-mill', WMQ_DIR_URL . 'assets/js/jquery-jvectormap-world-mill.js', ['jquery'], null, true);
        wp_enqueue_script('wmq-world-map', WMQ_DIR_URL . 'assets/js/wmq-world-map.js', ['jquery'], null, true);
        wp_enqueue_script('wmq-quiz', WMQ_DIR_URL . 'assets/js/quiz.js', rand(111, 999), null, true);
    }
    function wmq_admin_assets($hook)
    {
        if ('toplevel_page_wmq' == $hook) {
            wp_enqueue_style('bootstrap-css', '//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', '', null);
            wp_enqueue_style('wmq-admin-css', WMQ_DIR_URL . 'assets/css/admin/admin.css', '', rand(111, 999), 'all');
            wp_enqueue_script('poppper-js', '//cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js', ['jquery'], null, true);
            wp_enqueue_script('bootstrap-js', '//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js', ['jquery'], null, true);
            wp_enqueue_script('swal-js', '//cdn.jsdelivr.net/npm/sweetalert2@11', ['jquery'], null, true);
            wp_enqueue_script('wmq-admin-quiz', WMQ_DIR_URL . 'assets/js/admin/admin.js', ['jquery'], rand(111, 999), true);
            $wmq_nonce = wp_create_nonce('wmq_quiz_nonce');
            $ajax_url = admin_url('admin-ajax.php');
            wp_localize_script('wmq-admin-quiz', 'wmq_quiz_option', array(
                'ajax_url' => $ajax_url,
                'nonce' => $wmq_nonce
            ));
        }
    }

    function wmq_create_settings()
    {
        $page_title = __('World Map Quiz', 'wmq');
        $menu_title = __('World Map Quiz', 'wmq');
        $capability = 'manage_options';
        $slug = 'wmq';
        $callback = array($this, 'wmq_settings_content');
        $icon = WMQ_DIR_URL . 'images/world_map.png';

        add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon);
    }


    function wmq_get_options_value()
    {
        $wmq_nonce_verify = isset($_POST['nonce']) ? $_POST['nonce'] : '';
        if (wp_verify_nonce($wmq_nonce_verify, 'wmq_quiz_nonce')) {
            $values = isset($_POST['task']) ? $_POST['task'] : '';
            $getKey = [];
            $getValue = [];
            foreach ($values as $value) {
                $getKey[] = $value['name'];
                $getValue[] = $value['value'];
            }
            $getvalueArray = array_combine($getKey, $getValue);
            update_option('wmq_get_values', $getvalueArray);
            die();
        } else {
            return false;
        }
    }

    function wmq_remove_default_style()
    {

        if (is_page_template('world-map-quiz')) {
            var_dump(is_page_template( 'world-map-quiz' ));
            die();
            $theme = wp_get_theme();
            $parent_style = $theme->stylesheet . '-style';

            wp_dequeue_style($parent_style);
            wp_deregister_style($parent_style);
            wp_deregister_style($parent_style . '-css');
        }
    }

    function wmq_settings_content()
    {
?>
        <div class="wrap">
            <h1><?php _e('World Map Quiz Option', 'wmq'); ?></h1>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <?php foreach ($this->tabvalues as $key => $tab) {
                    printf('<li class="nav-item" role="presentation">
                            <button class="nav-link %s" id="pills-%s-tab" data-bs-toggle="pill" data-bs-target="#pills-%s" type="button" role="tab" aria-controls="pills-%s" aria-selected="true">%s</button>
                        </li>', $key == 0 ? 'active' : '', $tab, $tab, $tab, ucwords($tab));
                }
                ?>
            </ul>
            <form action="options.php" id="wmq-quiz-form" method="post">
                <div class="tab-content" id="pills-tabContent">
                    <?php $this->getOptions($this->getOptionvalues, $this->getKeys, $this->tabvalues); ?>
                    <input type="submit" name="wmq_get_values" id="wmq-submit" class="btn btn-primary" value="Save">
                </div>
            </form>
            <div class="wmq-get-data"></div>
        </div>
<?php
    }

    function getOptions($values, $number, $wrapper)
    {


        $fields = array(
            array(
                'label'       => __('Heading', 'wmq'),
                'type'        => 'text',
                'name'     => 'heading',
                'placeholder' => __('Heading Title', 'wmq'),
                'task'     => 'heading',
                'id'          => 'wmq_heading',
                'value' => isset($values['heading']) ? esc_html($values['heading']) : 'Heading Title of the World Map',
            ),
            array(
                'label'       => __('Sub Heading', 'wmq'),
                'type'        => 'text',
                'name'     => 'subheading',
                'placeholder' => __('Sub Heading ', 'wmq'),
                'task'     => 'subheading',
                'id'          => 'wmq_subheading',
                'value' => isset($values['subheading']) ? esc_html($values['subheading']) : 'Sub Heading of the World Map',
            ),
            array(
                'label'       => __('Quiz Time', 'wmq'),
                'type'        => 'number',
                'name'     => 'quiz_time',
                'placeholder' => '720',
                'task'     => 'quiz_time',
                'id'          => 'wmq_quiz_time',
                'value' => isset($values['quiz_time']) ? esc_attr($values['quiz_time']) : '',
            ),
            array(
                'label'       => __('Header Nav Title', 'wmq'),
                'type'        => 'text',
                'name'     => 'header_nav_title',
                'placeholder' => 'World Map',
                'task'     => 'header_nav_title',
                'id'          => 'wmq_header_nav_title',
                'value' => isset($values['header_nav_title']) ? $values['header_nav_title'] : '',
            ),
            array(
                'label'       => __('Header Span Title', 'wmq'),
                'type'        => 'text',
                'name'     => 'header_span_title',
                'placeholder' => 'Quiz',
                'task'     => 'header_span_title',
                'id'          => 'wmq_header_span_title',
                'value' => isset($values['header_span_title']) ? $values['header_span_title'] : '',
            ), array(
                'label'       => __('Unlimited Timer Text', 'wmq'),
                'type'        => 'text',
                'name'     => 'wmq_timer_text',
                'placeholder' => 'Timer!!',
                'task'     => 'wmq_timer_text',
                'id'          => 'wmq_timer_text',
                'value' => isset($values['wmq_timer_text']) ? $values['wmq_timer_text'] : "",
            ), array(
                'label'       => __('Give Up Title', 'wmq'),
                'type'        => 'text',
                'name'     => 'wmq_give_up_title',
                'placeholder' => 'Give Up?',
                'task'     => 'wmq_give_up_title',
                'id'          => 'wmq_give_up_title',
                'value' => isset($values['wmq_give_up_title']) ? $values['wmq_give_up_title'] : '',
            ), array(
                'label'       => __('Try Again Title', 'wmq'),
                'type'        => 'text',
                'name'     => 'wmq_try_again_title',
                'placeholder' => 'Try Again',
                'task'     => 'wmq_try_again_title',
                'id'          => 'wmq_try_again_title',
                'value' => isset($values['wmq_try_again_title']) ? $values['wmq_try_again_title'] : '',
            ),
            array(
                'label'       => __('Header Span Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'header_span_color',
                'task'     => 'header_span_color',
                'id'          => 'wmq_header_span_color',
                'value' => isset($values['header_span_color']) ? $values['header_span_color'] : '#95d1b1',
            ),
            array(
                'label'       => __('Map Width', 'wmq'),
                'type'        => 'number',
                'name'     => 'map_width',
                'placeholder'     => '950',
                'task'     => 'map_width',
                'id'          => 'wmq_map_width',
                'value' => isset($values['map_width']) ? $values['map_width'] : 950,
            ), array(
                'label'       => __('Map Height', 'wmq'),
                'type'        => 'number',
                'name'     => 'map_height',
                'placeholder'     => '450',
                'task'     => 'map_height',
                'id'          => 'wmq_map_height',
                'value' => isset($values['map_height']) ? $values['map_height'] : 450,
            ), array(
                'label'       => __('World BG Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'world_bg_color',
                'task'     => 'world_bg_color',
                'id'          => 'world_bg_color',
                'value' => isset($values['world_bg_color']) ? $values['world_bg_color'] : '#809fff',
            ), array(
                'label'       => __('Nav Background Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'nav_background_color',
                'task'     => 'nav_background_color',
                'id'          => 'wmq_nav_background_color',
                'value' => isset($values['nav_background_color']) ? $values['nav_background_color'] : '#1e4068',
            ), array(
                'label'       => __('Country Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'country_color',
                'task'     => 'country_color',
                'id'          => 'country_color',
                'value' => isset($values['country_color']) ? $values['country_color'] : '#ffffff',
            ), array(
                'label'       => __('Score Country Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'score_country_color',
                'task'     => 'score_country_color',
                'id'          => 'score_country_color',
                'value' => isset($values['score_country_color']) ? $values['score_country_color'] : '#ffff00',
            ), array(
                'label'       => __('Hover Country Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'hover_country_color',
                'task'     => 'hover_country_color',
                'id'          => 'hover_country_color',
                'value' => isset($values['hover_country_color']) ? $values['hover_country_color'] : '#dedede',
            ),
            array(
                'label'       => __('All Answer Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'wmq_all_answer',
                'task'     => 'wmq_all_answer',
                'id'          => 'wmq_all_answer',
                'value' => isset($values['wmq_all_answer']) ? $values['wmq_all_answer'] : '#f44336',
            ),
            array(
                'label'       => __('Correct Answer Color', 'wmq'),
                'type'        => 'color',
                'name'     => 'wmq_correct_answer',
                'task'     => 'wmq_correct_answer',
                'id'          => 'wmq_correct_answer',
                'value' => isset($values['wmq_correct_answer']) ? $values['wmq_correct_answer'] : '#0000ff',
            ),
        );

        if (count($fields) > $number) {
            $helpers = new Helpers();
            $first_array = array_chunk($fields, $number);
            if ($first_array[0]) {
                $content = '';
                printf('<div class="tab-pane fade show active" id="pills-%s" role="tabpanel" aria-labelledby="pills-%s-tab">', $wrapper[0], $wrapper[0]);
                foreach ($first_array[0] as $field) {
                    $content .= sprintf('%s', $helpers->input_switch($field));
                }
                printf('%s', '</div>');
            }
            if ($first_array[1]) {
                printf('<div class="tab-pane fade" id="pills-%s" role="tabpanel" aria-labelledby="pills-%s-tab">', $wrapper[1], $wrapper[1]);
                foreach ($first_array[1] as $field) {
                    $content .= sprintf('%s', $helpers->input_switch($field));
                }
                printf('%s', '</div>');
            }
            if ($first_array[2]) {
                printf('<div class="tab-pane fade" id="pills-%s" role="tabpanel" aria-labelledby="pills-%s-tab">', $wrapper[2], $wrapper[2]);
                foreach ($first_array[2] as $field) {
                    $content .= sprintf('%s', $helpers->input_switch($field));
                }
                printf('%s', '</div>');
            }
        } else {
            return false;
        }
    }


    function wmq_page_template_to_dropdown($templates)
    {
        $wmq_template = [];
        $wmq_template['world-map-quiz'] = __('World Map Template', 'wmp');
        $templates = array_merge($templates, $wmq_template);
        return $templates;
    }

    function wmq_change_page_template($template)
    {
        if (is_page()) {
            global $post;
            $meta = get_post_meta($post->ID);
            if (!empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] != $template) {
                $quiz = new QUIZ;
                $template = $quiz->quiz_init();
            }
        }
        return $template;
    }
}

new WorldMapQuiz();
