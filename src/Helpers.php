<?php

/**
 * Helpers Class for The Plugin
 *
 * @package Helpers Class
 */
namespace WMQ\src;

/**
 * Helpers Class Component
 */
class Helpers {

	/**
	 * Get values for the table
	 *
	 * @param string $column for column values.
	 */
	function get_values( $column ) {
		$content = sprintf('<tr>%s</tr>', $this->country_table_head($column));
		return $content;
	}

	/**
	 * Country Names in Table
	 *
	 * @param string $countries for all the country value.
	 */
	function country_names( $countries ) {
		$values = '';
		foreach ( $countries as $index => $country ) :

			$values .= sprintf('%s<td>
    <span style="display: none;" id="show%d">%s</span> <span type="text" id="guess%d" name="guess%d"> </span>
</td> <input type="hidden" id="answer%d" name="answer%d" value="%s" />%s', ( $index + 1 ) % 5 == 0 ? '<tr>' : '', ++$index, $country, $index, $index, $index, $index, $country, ( $index + 1 ) % 5 == 0 ? '<tr>' : '');

		endforeach;

		return $values;
	}

	/**
	 * Heading Content of the Table
	 *
	 * @param string $column for the head.
	 */
	function country_table_head( $column ) {
		$content = '';
		$value   = sprintf('<td class="answer-col-head">%s</td>', 'Answer');
		for ( $i = 1; $i <= $column; $i++ ) {
			$content .= sprintf('%s', $value);
		}
		return $content;
	}

	/**
	 * Input Values Switch for the Table
	 *
	 * @param string $field type values.
	 */
	function input_switch( $field ) {
		if ( is_array($field) && isset($field['type']) ) {
			switch ( $field['type'] ) {
				case 'number':
					$this->get_contents($field['label'], $field['type'], $field['name'], $field['placeholder'], $field['task'], $field['id'], $field['value']);
					break;
				case 'color':
					$this->get_contents($field['label'], $field['type'], $field['name'], null, $field['task'], $field['id'], $field['value']);
					break;
				default:
					$this->get_contents($field['label'], $field['type'], $field['name'], $field['placeholder'], $field['task'], $field['id'], $field['value']);
			}
		}
	}

	/**
	 * Get all the DOM content for the variables
	 *
	 * @param string $label type values.
	 * @param string $type type values.
	 * @param string $name type values.
	 * @param string $placeholder type values.
	 * @param string $task type values.
	 * @param int    $id type values.
	 * @param string $value type values.
	 */
	function get_contents( $label, $type, $name, $placeholder = null, $task, $id, $value ) {
		$content = printf('<div class="d-block my-3 ">
        <label>%s: </label><input type="%s" name="%s" class="wmq-action" %s data-task="%s" id="%s" value="%s">
    </div>', esc_attr($label), esc_attr($type), esc_html($name), $placeholder ? 'placeholder="' . esc_attr($placeholder) . '"' : null, esc_attr($task), esc_attr($id), esc_attr($value));
		return $content;
	}

	/**
	 * Filter Values According to Table
	 *
	 * @param string $value type.
	 */
	function wmq_filter_values( $value ) {
		if ( gettype($value) === 'string' ) {
			return sanitize_text_field( $value );

		} elseif ( gettype($value) === 'number' ) {
			return filter_var($value, 'FILTER_VALIDATE_INT');
		} elseif ( gettype( $value) === 'boolean' ) {
			return wp_validate_boolean($value);
		} else {
			return false;
		}
	}
}
