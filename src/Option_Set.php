<?php
/**
 * Get the option_settings File
 *
 * @package option settings
 **/

namespace WMQ\src;

/**
 * Option_Set Class Component
 */
class Option_Set {

	/**
	 * Get the option_settings File Function
	 *
	 * @param string $values set of input values.
	 *
	 * @param int    $number return the specific admin slug.
	 *
	 * @param string $wrapper to set the wrapper class.
	 */
	function get_options( $values, $number, $wrapper ) {

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

}
