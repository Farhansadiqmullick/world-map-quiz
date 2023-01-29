<?php

namespace WMQ\src;

class Helpers {

	function getValues( $column ) {
		 $content = sprintf('<tr>%s</tr>', $this->country_table_head($column));
		return $content;
	}

	function country_names( $countries ) {
		$values = '';
		foreach ( $countries as $index => $country ) :

			$values .= sprintf('%s<td>
    <span style="display: none;" id="show%d">%s</span> <span type="text" id="guess%d" name="guess%d"> </span>
</td> <input type="hidden" id="answer%d" name="answer%d" value="%s" />%s', ( $index + 1 ) % 5 == 0 ? '<tr>' : '', ++$index, $country, $index, $index, $index, $index, $country, ( $index + 1 ) % 5 == 0 ? '<tr>' : '');

		endforeach;

		return $values;
	}

	function country_table_head( $column ) {
		$content = '';
		$value = sprintf('<td class="answer-col-head">%s</td>', 'Answer');
		for ( $i = 1; $i <= $column; $i++ ) {
			$content .= sprintf('%s', $value);
		}
		return $content;
	}

	function input_switch( $field ) {
		if ( is_array($field) && isset($field['type']) ) {
			switch ( $field['type'] ) {
				case 'number':
					$this->getContents($field['label'], $field['type'], $field['name'], $field['placeholder'], $field['task'], $field['id'], $field['value']);
					break;
				case 'color':
					$this->getContents($field['label'], $field['type'], $field['name'], null, $field['task'], $field['id'], $field['value']);
					break;
				default:
					$this->getContents($field['label'], $field['type'], $field['name'], $field['placeholder'], $field['task'], $field['id'], $field['value']);
			}
		}
	}

	function getContents( $label, $type, $name, $placeholder = null, $task, $id, $value ) {
		 $content = printf('<div class="d-block my-3 ">
        <label>%s: </label><input type="%s" name="%s" class="wmq-action" %s data-task="%s" id="%s" value="%s">
    </div>', $label, $type, $name, $placeholder ? 'placeholder="' . $placeholder . '"' : null, $task, $id, $value);
		return $content;
	}

	function wmq_filter_values( $value ) {
		if ( gettype($value) === 'string' ) {
			return sanitize_text_field( $value );

		} else if ( gettype($value) === 'number' ) {
			return filter_var($value, 'FILTER_VALIDATE_INT');
		} else if ( gettype( $value) === 'boolean' ) {
			return wp_validate_boolean($value);
		} else {
			return false;
		}
	}
}
