<?php

class HeadwayHTMLBuilderBlock extends HeadwayBlockAPI {
	
	
	public $id = 'html-builder';
	
	public $name = 'HTML Builder';
		
	public $options_class = 'HeadwayHTMLBuilderBlockOptions';

	public $description = 'Create blocks using common html elements then position and style them.';

	/* Use this to pass the block from static function to static function */
	static public $block_id = null;

	public static $element_types = array(
		'heading',
		'text',
		'image',
		'link',
		'button',
		'svg'
	);

	public static function init_action($block_id, $block = false) {

		self::$block_id = $block_id;
		
	}

	public static function enqueue_action($block_id, $block) {

		wp_enqueue_script('html-builder', headway_url() . '/library/media/js/jquery.ui.js', array('jquery'));

	}

	public static function dynamic_js($block_id, $block) {

		$block_width = HeadwayBlocksData::get_block_width($block);
		
		return '
		(function ($) {
			
		})(jQuery);' . "\n";
	
	}

	public static function dynamic_css($block_id, $block) {

		$css = "";

		foreach (self::$element_types as $element_type) {

			$elements = HeadwayBlockAPI::get_setting($block, $element_type . '-elements' , array());

			$i = -1;
			
			foreach ( $elements as $element ) {

				$i++;

				$position = "";
				$inherit = "";

				$left = headway_fix_data_type(headway_get('left', $element, false));
				$right = headway_fix_data_type(headway_get('right', $element, false));

				if ($left >= 50 && $right <= 50) {
		        	$position = "right";
		        	$inherit = "left";
		        } else if ($left <= 50 && $right >= 50) {
		        	$position = "left";
		        	$inherit = "right";
		        }

		        $position_value = headway_fix_data_type(headway_get($position, $element, false));
		        $top_position = headway_fix_data_type(headway_get('top', $element, false));

				$css .= '
				#block-' . $block_id . ' {
					position: relative;
				}

				.ui-draggable { cursor: move; }

				.'.$element_type.'-elements.element'. $i .' { 
					' . $inherit . ': inherit;
					top: '. $top_position .'px;
					' . $position . ': '. $position_value .'%;
				}
				
			' . "\n";

			}
		
		}

		$css .= '
			/* TODO: must move to css file */
				.element {
					position: absolute;
					float: left;
				}';

		return $css;
		
	}

	/* To make the layout responsive
	 * Works out a percentage value equivalent of the px value 
	 * using common responsive formula: target_width / container_width * 100
	 */	
	static function widthAsPercentage($target = '', $block) {
		$block_width = HeadwayBlocksData::get_block_width($block);
		
		if ($block_width > 0 )
			return ($target / $block_width)*100;

		return false;
	}
	
	
	function content($block) {

		foreach (self::$element_types as $element_type) {

			$elements = parent::get_setting($block, $element_type . '-elements' , array());
			
			$has_elements = false;

			foreach ( $elements as $element ) {

				if ( $element[$element_type] ) {
					$has_elements = true;
					break;
				}

			}

			echo self::display_elements($elements, $element_type);
		
		}
	  		
	}

	function setup_elements($block_id) {

		$block_id = self::$block_id;
		$block = HeadwayBlocksData::get_block($block_id);

		foreach (self::$element_types as $element_type) {

			/* Register defaults/parents */
			$this->register_block_element(array(
				'id' => $element_type . '-elements',
				'name' => ucwords($element_type) . ' Elements',
				'selector' => '.' . $element_type . '-elements'
			));

			$elements = parent::get_setting($block, $element_type . '-elements' , array());
			
			foreach ( $elements as $element ) {

				if ( !$element[$element_type] )
					break;

				$custom_id = $element['custom-id'];

				if ( !$custom_id )
					continue;

				$this->register_block_element(array(
					'id' => $element_type . '-' . $custom_id,
					'name' => ucwords($element_type) . ' (#' . $custom_id . ')',
					'selector' => '#' . $custom_id,
					'parent' => $element_type . '-elements'
				));

			}

		
		}
		
	}

	function display_elements($elements, $element_type) {

		//TDOD: make draggable only if draggable positioning enabled
		$output = '';
  		$i = -1;
	  	foreach ( $elements as $element ) {

	  		if ( !$element[$element_type] )
	  			echo '<div style="margin: 5px;" class="alert alert-yellow"><p>You have not added any html elements yet. Please choose add some elements and then you can position them where you want.</p></div>';

	  		$i++;

	  		switch ($element_type) {
	  			case 'heading':
	  				self::heading_element_output($element, $element_type, $i);
	  				break;

	  			case 'text':
	  				self::text_element_output($element, $element_type, $i);
	  				break;

	  			case 'image':
	  				self::image_element_output($element, $element_type, $i);
	  				break;

	  			case 'link':
	  				self::link_element_output($element, $element_type, $i);
	  				break;

	  			case 'button':
	  				self::button_element_output($element, $element_type, $i);
	  				break;

	  			case 'svg':
	  				self::svg_element_output($element, $element_type, $i);
	  				break;
	  			
	  			default:
	  				return;
	  				break;
	  		}	  		 
	  		
	  	}
		
	  	return $output;

	}

	function heading_element_output($element, $element_type, $i) {

		$heading_element = headway_fix_data_type(headway_get('heading-html-element', $element));
		$custom_id = headway_fix_data_type(headway_get('custom-id', $element)) ? 'id="'. headway_fix_data_type(headway_get('custom-id', $element)) .'"' : null;

		echo '<' . $heading_element . ' ' . $custom_id . ' class="' . $element_type . '-elements element element' . $i . ' " data-element-type="heading-elements">';

			echo headway_fix_data_type(headway_get($element_type, $element, false));

		echo '</' . $heading_element . '>';

	}

	function text_element_output($element, $element_type, $i) {

		$custom_id = headway_fix_data_type(headway_get('custom-id', $element)) ? 'id="'. headway_fix_data_type(headway_get('custom-id', $element)) .'"' : null;
		$text_element = headway_fix_data_type(headway_get('html-element', $element, 'p'));


		echo '<' . $text_element . ' ' . $custom_id . ' class="' . $element_type . '-elements element element' . $i . '" data-element-type="text-elements">';

			echo headway_fix_data_type(headway_get($element_type, $element, false));

		echo '</' . $text_element . '>';

	}

	function image_element_output($element, $element_type, $i) {
			
		$output = array(
  			'image' => array(
  				'src' => $element['image'],
  				'alt' => headway_fix_data_type(headway_get('image-alt', $element, false)) ? ' alt="' . headway_fix_data_type(headway_get('image-alt', $element, false)) . '"' : null,
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element, false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			),

  			'hyperlink' => array(
  				'href' => headway_fix_data_type(headway_get('link-url', $element)),
  				'title' => headway_fix_data_type(headway_get('link-title', $element, false)) ? ' title="' . headway_fix_data_type(headway_get('link-title', $element, false)) . '"' : null,
  				'target' => headway_fix_data_type(headway_get('link-target', $element, false)) ? ' target="_blank"' : null
  			)
  		);

  		echo '<figure ' . $output['image']['custom-id'] . ' class="'. $element_type . '-elements element element' . $i . '" data-element-type="image-elements">';

  			/* Open hyperlink if user added one for image */
  			if ( $output['hyperlink']['href'] )
  				echo '<a href="' . $output['hyperlink']['href'] . '"' . $output['hyperlink']['target'] . $output['hyperlink']['title'] . '>';

		  			/* Don't forget to display the ACTUAL IMAGE */
		  			echo '<img src="' . $output['image']['src'] . '"' . $output['image']['alt'] . ' class="img-' . $i . '" />';

  			/* Closing tag for hyperlink */
  			if ( $output['hyperlink']['href'] )
  				echo '</a>';

  		echo '</figure>';

	}

	function link_element_output($element, $element_type, $i) {

		$output = array(
  			'hyperlink' => array(
  				'href' => headway_fix_data_type(headway_get('link-url', $element)),
  				'text' => headway_fix_data_type(headway_get('link', $element)),
  				'title' => headway_fix_data_type(headway_get('link-title', $element, false)) ? ' title="' . headway_fix_data_type(headway_get('link-title', $element, false)) . '"' : null,
  				'target' => headway_fix_data_type(headway_get('link-target', $element, false)) ? ' target="_blank"' : null,
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element, false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			)
  		);

  		echo '<a ' . $output['hyperlink']['custom-id'] . ' class="'. $element_type . '-elements element element' . $i . '" data-element-type="link-elements" href="' . $output['hyperlink']['href'] . '"' . $output['hyperlink']['target'] . $output['hyperlink']['title'] . '>' . $output['hyperlink']['text'] . '</a>';

	}

	function button_element_output($element, $element_type, $i) {

		$output = array(
  			'button' => array(
  				'href' => headway_fix_data_type(headway_get('url', $element)),
  				'text' => headway_fix_data_type(headway_get('button', $element)),
  				'title' => headway_fix_data_type(headway_get('title', $element, false)) ? ' title="' . headway_fix_data_type(headway_get('title', $element, false)) . '"' : null,
  				'target' => headway_fix_data_type(headway_get('target', $element, false)) ? ' target="_blank"' : null,
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element, false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			)
  		);

  		echo '<a ' . $output['button']['custom-id'] . ' class="'. $element_type . '-elements button element element' . $i . '" data-element-type="button-elements" href="' . $output['button']['href'] . '"' . $output['button']['target'] . $output['button']['title'] . '>' . $output['button']['text'] . '</a>';

	}

	function svg_element_output($element, $element_type, $i) {
			
		$output = array(
  			'svg' => array(
  				'src' => $element['svg'],
  				'alt' => headway_fix_data_type(headway_get('svg-image-alt', $element, false)) ? ' alt="' . headway_fix_data_type(headway_get('svg-image-alt', $element, false)) . '"' : null,
  				'width' => (headway_fix_data_type(headway_get('svg-width', $element, false))) ? 'width="' . headway_fix_data_type(headway_get('svg-width', $element, false)) . '"' : null,
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element, false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			),

  			'hyperlink' => array(
  				'href' => headway_fix_data_type(headway_get('link-url', $element)),
  				'title' => headway_fix_data_type(headway_get('link-title', $element, false)) ? ' title="' . headway_fix_data_type(headway_get('link-title', $element, false)) . '"' : null,
  				'target' => headway_fix_data_type(headway_get('link-target', $element, false)) ? ' target="_blank"' : null
  			)
  		);

  		echo '<div ' . $output['svg']['custom-id'] . ' class="'. $element_type . '-elements element element' . $i . '" data-element-type="svg-elements">';

  			/* Open hyperlink if user added one for image */
  			if ( $output['hyperlink']['href'] )
  				echo '<a href="' . $output['hyperlink']['href'] . '"' . $output['hyperlink']['target'] . $output['hyperlink']['title'] . '>';

		  			/* Don't forget to display the ACTUAL IMAGE */
		  			echo '<img src="' . $output['svg']['src'] . '"' . $output['svg']['alt'] . ' ' . $output['svg']['width'] . ' class="svg-' . $i . '" />';

  			/* Closing tag for hyperlink */
  			if ( $output['hyperlink']['href'] )
  				echo '</a>';

  		echo '</div>';

	}
	
}