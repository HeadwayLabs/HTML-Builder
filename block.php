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
		'svg',
		'blockquote',
		'address',
		'video',
		'audio',
		'list'
	);

	public static $position_properties = array(
		'top_left' => 'left: 0; top: 0;',
		'top_center' => 'left: 0; top: 0; right: 0;',
		'top_right' => 'top: 0; right: 0;',

		'center_center' => 'bottom: 0; left: 0; top: 0; right: 0;',
		'center_left' => 'bottom: 0; left: 0; top: 0;',
		'center_right' => 'bottom: 0; top: 0; right: 0;',
		
		'bottom_left' => 'bottom: 0; left: 0;',
		'bottom_center' => 'bottom: 0; left: 0; right: 0;',
		'bottom_right' => 'bottom: 0;right: 0;'
	);

	public static function init_action($block_id, $block = false) {

		self::$block_id = $block_id;
		
	}

	public static function enqueue_action($block_id, $block) {

		wp_enqueue_script('html-builder', plugins_url(false, __FILE__) . '/js/player.js', array('jquery'));

	}

	public static function dynamic_css($block_id, $block) {

		$css = "";

		foreach (self::$element_types as $element_type) {

			$elements = HeadwayBlockAPI::get_setting($block, $element_type . '-elements' , array());

			$i = -1;
			
			foreach ( $elements as $element ) {

				$i++;

				$enable_draggable = headway_fix_data_type(headway_get('enable-draggable', $element, false));
				$width = headway_fix_data_type(headway_get('width', $element, false)) ? 'width: '.headway_fix_data_type(headway_get('width', $element, false)).'' : null;
				$height = headway_fix_data_type(headway_get('height', $element, false)) ? 'height: '.headway_fix_data_type(headway_get('height', $element, false)).'' : null;

				if ($width or $height) {
					$css .= '
					#block-' . $block_id . ' .'.$element_type.'-elements.element'. $i .' {
					    ' . $height . ';
					    ' . $width . ';
					}';
				}

				if($enable_draggable) {

					$left = headway_fix_data_type(headway_get('left', $element, false));
					$right = headway_fix_data_type(headway_get('right', $element, false));

					if($left or $right) {

						$position = "";
						$inherit = "";

						$position = "left";
				       	$inherit = "right";

						// if ($left >= 50 && $right <= 50) {
			   //      		$position = "right";
			   //      		$inherit = "left";
				  //       } else if ($left <= 50 && $right >= 50) {
				  //       	$position = "left";
				  //       	$inherit = "right";
				  //       }

				        $position_value = headway_fix_data_type(headway_get($position, $element, false));
				        $top_position = headway_fix_data_type(headway_get('top', $element, false));

				        $position_metric = 'px';
				        

						$css .= '
						#block-' . $block_id . ' {
							position: relative;
						}

						#block-' . $block_id . ' .'.$element_type.'-elements.element'. $i .' { 
							' . $inherit . ': inherit;
							top: '. $top_position .'px;
							' . $position . ': '. $position_value . $position_metric . ';
						}
						
					' . "\n";

					}

				} else {
					$css .= '
						#block-' . $block_id . ' .'.$element_type.'-elements.element'. $i .' {
							position: static;
						}
					';
				}
			}
		
		}

		$css .= '

			.element {
				position: absolute;
				float: left;
			}

			.y-line {
				background: url(' . plugins_url(false, __FILE__) . '/admin/images/y-line.png) repeat-y 50% 0;
			}

			.x-line {
				background: url(' . plugins_url(false, __FILE__) . '/admin/images/x-line.png) repeat-x 0 50%;
			}

			.x-y-line {
				background: url(' . plugins_url(false, __FILE__) . '/admin/images/y-line.png) repeat-y 50% 0, url(' . plugins_url(false, __FILE__) . '/admin/images/x-line.png) repeat-x 0 50%;
			}
			.ui-draggable { cursor: move; }
			';


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

			if ($element_type == 'list') {

				$list_elements = parent::get_setting($block, $element_type . '-elements' , array());
				echo self::display_list_elements($list_elements, $element_type);

			} else {

				echo self::display_elements($elements, $element_type);

			}
		
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

	  			case 'blockquote':
	  				self::blockquote_element_output($element, $element_type, $i);
	  				break;

	  			case 'address':
	  				self::address_element_output($element, $element_type, $i);
	  				break;

	  			case 'video':
	  				self::video_element_output($element, $element_type, $i);
	  				break;

	  			case 'audio':
	  				self::audio_element_output($element, $element_type, $i);
	  				break;
	  			
	  			default:
	  				return;
	  				break;
	  		}	  		 
	  		
	  	}
		
	  	return $output;

	}

	function display_list_elements($elements, $element_type, $block) {

		$block_id = self::$block_id;
		$block = HeadwayBlocksData::get_block($block_id);

		$custom_id = parent::get_setting($block, 'custom-list-id', null);

		$custom_id = $custom_id ? 'id="'. $custom_id .'"' : null;
			

  		echo '<ul ' . $custom_id . ' class="' . $element_type . '-elements element element0" data-element-type="list-elements">';
	  	foreach ( $elements as $element ) {

	  		//move to UL $custom_id = headway_fix_data_type(headway_get('custom-id', $element)) ? 'id="'. headway_fix_data_type(headway_get('custom-id', $element)) .'"' : null;


			echo '<li>';

				echo headway_fix_data_type(headway_get($element_type, $element, false));

			echo '</li>';
	  		
	  	}

	  	echo '</ul>';
		
	}


	function heading_element_output($element, $element_type, $i) {

		$heading_element = headway_fix_data_type(headway_get('heading-html-element', $element));
		$custom_id = headway_fix_data_type(headway_get('custom-id', $element)) ? 'id="'. headway_fix_data_type(headway_get('custom-id', $element)) .'"' : null;

		echo '<' . $heading_element . ' ' . $custom_id . ' class="' . $element_type . '-elements element element' . $i . '" data-element-type="heading-elements">';

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

	function blockquote_element_output($element, $element_type, $i) {
			
		$output = array(

			'blockquote' => array(
  				'blockquote' => headway_fix_data_type(headway_get('blockquote', $element, false)),
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element, false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			),

  			'cite' => array(
  				'href' => headway_fix_data_type(headway_get('author-url', $element)),
  				'show-cite' => headway_fix_data_type(headway_get('show-cite', $element), false),
  				'before-author' => headway_fix_data_type(headway_get('before-author', $element, '')),
  				'author' => headway_fix_data_type(headway_get('author-name', $element, false)),
  				'target' => headway_fix_data_type(headway_get('target', $element, false)) ? ' target="_blank"' : null
  			)
  		);

  		echo '
  			<blockquote ' . $output['blockquote']['custom-id'] . ' class="'. $element_type . '-elements element element' . $i . '" data-element-type="blockquote-elements">';
  			
  			echo $output['blockquote']['blockquote'];

  			if ( $output['cite']['show-cite'] == false )
  				return;
			echo '<footer>';

					if ( $output['cite']['before-author'] )
						echo $output['cite']['before-author'];

					echo '<cite>';
						if ( $output['cite']['href'] )
						echo '<a href="' . $output['cite']['href'] . '" ' . $output['hyperlink']['target'] . '>';
							
							echo $output['cite']['author'];

						if ( $output['cite']['href'] )
						echo '</a>';

					if ( $output['cite']['author'] )		
					echo '
					</cite>

				</footer>';
		
		echo '</blockquote>';

	}

	function address_element_output($element, $element_type, $i) {
			
		$output = array(

			'address' => array(
  				'fn' => headway_fix_data_type(headway_get('address', $element, null)),
  				'street' => headway_fix_data_type(headway_get('street', $element, null)),
  				'complex' => headway_fix_data_type(headway_get('complex', $element, null)),
  				'locality' => headway_fix_data_type(headway_get('locality', $element, null)),
  				'postal-code' => headway_fix_data_type(headway_get('postal-code', $element, null)),
  				'tel' => headway_fix_data_type(headway_get('tel', $element, null)),
  				'country' => headway_fix_data_type(headway_get('country', $element, null)),
  				'url' => headway_fix_data_type(headway_get('url', $element, null)),
  				'href' => headway_fix_data_type(headway_get('url', $element, null)),
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element,false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			)
  		);

  		echo '<div ' . $output['address']['custom-id'] . ' class="vcard '. $element_type . '-elements element element' . $i . '" data-element-type="address-elements">';
			
			if($output['address']['fn'])
				echo '<p class="fn org">' . $output['address']['fn'] . '</p>';

		echo 
			'<ul class="adr">';

				$complex = $output['address']['complex'];
				if($complex)
					echo '<li class="complex">' . $complex .' </li>';

				$street = $output['address']['street'];
				if($street)
					echo '<li class="street-address">' . $street .' </li>';

				$locality = $output['address']['locality'];
				if($locality)
					echo '<li class="locality">' . $locality .' </li>';

				$post_code = $output['address']['postal-code'];
				if($post_code)
					echo '<li class="postal-code">' . $post_code .' </li>';

				$country = $output['address']['country'];
				if($country)
					echo '<li class="country-name">' . $country .' </li>';

		echo 	
			'</ul>';

				$tel = $output['address']['tel'];
				if($tel)
					echo '<p class="tel">' . $tel .' </p>';

				$url = $output['address']['url'];
				if($url)
					echo '<p class="url"><a href="' . $url .'">' . $url .'</a></p>';


  		echo 	'</div>';

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

	function video_element_output($element, $element_type, $i) {

		/* TODO: use for docs - http://blog.zencoder.com/2013/09/13/what-formats-do-i-need-for-html5-video/ */
			
		$output = array(
  			'video' => array(
  				'mp4' => headway_fix_data_type(headway_get('video', $element, false)),
  				'webm' => headway_fix_data_type(headway_get('webm', $element, false)),
  				'poster' => headway_fix_data_type(headway_get('poster', $element, false)) ? ' poster="' . headway_fix_data_type(headway_get('poster', $element, false)) . '"' : null,
  				'width' => (headway_fix_data_type(headway_get('width', $element, false))) ? 'width="' . headway_fix_data_type(headway_get('width', $element, false)) . '"' : null,
  				'height' => (headway_fix_data_type(headway_get('height', $element, false))) ? 'height="' . headway_fix_data_type(headway_get('height', $element, false)) . '"' : null,
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element, false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			)
  		);


		echo '<video ' . $output['video']['custom-id'] . '
				  ' . $output['video']['width'] . '
				  ' . $output['video']['height'] . '
				  ' . $output['video']['poster'] . '
				  class="'. $element_type . '-elements element element' . $i . '" 
				  data-element-type="video-elements" 
				  controls>';

				if ($output['video']['mp4'])
					echo '<source src="' . $output['video']['mp4'] . '" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>';

				if ($output['video']['webm'])
					echo '<source src="' . $output['video']['webm'] . '" type=\'video/webm; codecs="vp8, vorbis"\'>';

		echo 'Video tag not supported by your browser. Download this video <a href=" ' . $output['video']['mp4'] . ' ">here</a>.
				
			</video>
		';

	}

	function audio_element_output($element, $element_type, $i) {
			
		$output = array(
  			'audio' => array(
  				'src' => $element['audio'],
  				'width' => (headway_fix_data_type(headway_get('width', $element, false))) ? 'width="' . headway_fix_data_type(headway_get('width', $element, false)) . '"' : null,
  				'height' => (headway_fix_data_type(headway_get('height', $element, false))) ? 'height="' . headway_fix_data_type(headway_get('height', $element, false)) . '"' : null,
  				'custom-id' => headway_fix_data_type(headway_get('custom-id', $element, false)) ? ' id="' . headway_fix_data_type(headway_get('custom-id', $element, false)) . '"' : null,
  			)
  		);

		echo '
			<audio src=' . $output['audio']['src'] . '
			  ' . $output['audio']['width'] . '
			  ' . $output['audio']['height'] . '
			  class="'. $element_type . '-elements element element' . $i . '" 
			  data-element-type="audio-elements" 
			  controls>
			  <p>fallback text</p>
			</video>
		';

	}
}