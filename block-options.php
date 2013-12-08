<?php

class HeadwayHTMLBuilderBlockOptions extends HeadwayBlockOptionsAPI {

	function modify_arguments($args) {
		
		$block = HeadwayBlocksData::get_block($args['block_id']);

		$this->open_js_callback .= self::initiateDraggables($block);

	}

	public $open_js_callback = '
		(function($) {
			$(document).ready(function() {

				var context = "div#panel";

				/* Initiate Draggable by focusing on the
				   first input to trigger its callback
				*/
				$(context).find("#sub-tab-heading-elements-tab-content #input-" + blockID + "-left").first().focus()
				$(context).focus();


				/* Now hide position element options */
				$("#input-left,#input-right,#input-top,#input-bottom").hide();

				/* Reload block options when new repeater is added 
				   to group to initiate draggable on it]
				*/
				$(context).delegate("div.repeater .add-group", "click", function() {
					
					setTimeout(function(){
	   					reloadBlockOptions(blockID);
	    			}, 1000);
					
						
				});
			
			});
		})(jQuery)
	';	

	function initiateDraggables($block) {

		$js = "";

		foreach (HeadwayHTMLBuilderBlock::$element_types as $element_type) {

			$elements = HeadwayBlockAPI::get_setting($block, $element_type . '-elements' , array());

			$i = -1;
			
			foreach ( $elements as $element ) {

				$i++;

				$js .= '
				var element = $(".' . $element_type .'-elements.draggable' . $i .'");
				var iframeElement = $i(".'. $element_type . '-elements.draggable'. $i .'");
				$(element).initiateDraggable("' . $element_type . '", "' . $i .'")';

			}
			
		
		}

		return;// $js;
	}
	
	public $tabs = array(
		'heading-elements-tab' => 'Heading Elements',
		'text-elements-tab' => 'Text Elements',
		'image-elements-tab' => 'Image Elements',
		'link-elements-tab' => 'Link Elements',
		'button-elements-tab' => 'Button Elements',
		'blockquote-elements-tab' => 'Blockquote Elements',
		'list-elements-tab' => 'List Elements',
		'address-elements-tab' => 'Address Elements',
		'svg-elements-tab' => 'SVG Elements'
	);

	public $inputs = array(
		'heading-elements-tab' => array(

			'heading-elements' => array(
				'type' => 'repeater',
				'name' => 'heading-elements',
				//'label' => 'Heading Elements',
				'inputs' => array(
					array(
						'name' => 'heading-id-heading',
						'type' => 'heading',
						'label' => 'Heading & Markup <span>Set text and the html output</span>'
					),
					array(
						'type' => 'textarea',
						'name' => 'heading',
						'label' => 'Heading Text',
						'default' => null
					),
					array(
						'type' => 'select',
						'name' => 'heading-html-element',
						'label' => 'Markup',
						'options' => array(
							'h1' => 'H1',
							'h2' => 'H2',
							'h3' => 'H3',
							'h4' => 'H4',
							'h5' => 'H5'
						)
					),
					array(
						'name' => 'position-id-heading',
						'type' => 'heading',
						'label' => 'Positioning <span>Set text and the html output</span>'
					),
					array(
						'name' => 'position-type',
						'label' => 'Position using',
						'type' => 'select',
						'default' => 'draggable',
						'options' => array(
							'draggable' => 'Drag',
							'preset' => 'Preset'
						),
						'toggle' => array(
							'draggable' => array(
								'hide' => array(
									'#input-preset-position'
								)
							),
							'preset' => array(
								'show' => array(
									'#input-preset-position'
								)
							)
						),
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'preset-position',
						'label' => 'Position',
						'type' => 'select',
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'top_left' => 'Top Left',
							'top_center' => 'Top Center',
							'top_right' => 'Top Right',
							'center_left' => 'Center Left',
							'center_center' => 'Center Center',
							'center_right' => 'Center Right',
							'bottom_left' => 'Bottom Left',
							'bottom_center' => 'Bottom Center',
							'bottom_right' => 'Bottom Right'
						)
					),
					array(
						'name' => 'center-element',
						'label' => 'Center on x axis?',
						'type' => 'checkbox',
						'default' => false,
						'callback' => 'centerCallback(block, args)'
					),
					array(
						'name' => 'custom-id-heading',
						'type' => 'heading',
						'label' => 'Custom ID (#) <span>Set a custom ID & style in VE</span>'
					),
					array(
						'type' => 'text',
						'name' => 'custom-id',
						//'label' => '',
						'default' => null,
						'tooltip' => 'Set a custom ID so you can style this element differently from all others like it. This will also register this element in the design panel and element tree.'
					),
					array(
						'name' => 'dimensions-heading',
						'type' => 'heading',
						'label' => 'Dimensions <span>Width & height mainly for presets</span>'
					),
					array(
						'type' => 'text',
						'name' => 'width',
						'label' => 'Width',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'height',
						'label' => 'Height',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'left',
						'label' => 'Left',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'right',
						'label' => 'Right',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'top',
						'label' => 'Top',
						'default' => null
					)

				),
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),
		'text-elements-tab' => array(

			'text-elements' => array(
				'type' => 'repeater',
				'name' => 'text-elements',
				'label' => 'Text Elements',
				'inputs' => array(
					array(
						'type' => 'textarea',
						'name' => 'text',
						'label' => 'Text',
						'default' => null
					),

					array(
						'type' => 'select',
						'name' => 'html-element',
						'label' => 'Markup',
						'options' => array(
							'p' => 'Paragraph p',
							'div' => 'Division div',
							'span' => 'Span span',
						)
					),
					array(
						'type' => 'text',
						'name' => 'custom-id',
						'label' => 'Custom ID (#)',
						'default' => null,
						'tooltip' => 'Set a custom ID so you can style this element differently from all others like it. This will also register this element in the design panel and element tree.'
					),
					array(
						'name' => 'position-type',
						'label' => 'Position type',
						'type' => 'select',
						'default' => 'draggable',
						'options' => array(
							'draggable' => 'Draggable',
							'preset' => 'Preset'
						),
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'preset-position',
						'label' => 'Position',
						'type' => 'select',
						'tooltip' => 'You can position this element in relation to the block using the positions provided',
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'top_left' => 'Top Left',
							'top_center' => 'Top Center',
							'top_right' => 'Top Right',
							'center_left' => 'Center Left',
							'center_center' => 'Center Center',
							'center_right' => 'Center Right',
							'bottom_left' => 'Bottom Left',
							'bottom_center' => 'Bottom Center',
							'bottom_right' => 'Bottom Right'
						)
					),
					array(
						'name' => 'center-element',
						'label' => 'Center on x axis?',
						'type' => 'checkbox',
						'default' => false,
						'callback' => 'centerCallback(block, args)'
					),
					array(
						'type' => 'text',
						'name' => 'width',
						'label' => 'Width',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'height',
						'label' => 'Height',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'left',
						'label' => 'Left',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'right',
						'label' => 'Right',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'top',
						'label' => 'Top',
						'default' => null
					)

				),
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),
		'link-elements-tab' => array(
			'link-elements' => array(
				'type' => 'repeater',
				'name' => 'link-elements',
				'label' => 'Link Elements',
				'inputs' => array(
					array(
						'name' => 'link',
						'label' => 'Link Text',
						'type' => 'text',
						'tooltip' => 'Set the text to display in the link',
						'default' => false,
					),
					array(
						'type' => 'text',
						'name' => 'link-title',
						'label' => '"title"',
						'tooltip' => 'This will be used as the "title" attribute for the link.  The title attribute is beneficial for SEO (Search Engine Optimization) and will allow your visitors to move their mouse over the link and read about it.'
					),

					array(
						'name' => 'link-url',
						'label' => 'Link URL?',
						'type' => 'text',
						'tooltip' => 'Set the URL/href for the link'
					),

					array(
						'name' => 'link-target',
						'label' => 'New window?',
						'type' => 'checkbox',
						'tooltip' => 'Open the link in a new window?',
						'default' => false,
					),
					array(
						'type' => 'text',
						'name' => 'custom-id',
						'label' => 'Custom ID (#)',
						'default' => null,
						'tooltip' => 'Set a custom ID so you can style this element differently from all others like it. This will also register this element in the design panel and element tree.'
					),
					array(
						'name' => 'position-type',
						'label' => 'Position type',
						'type' => 'select',
						'default' => 'draggable',
						'options' => array(
							'draggable' => 'Draggable',
							'preset' => 'Preset'
						),
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'center-element',
						'label' => 'Center on x axis?',
						'type' => 'checkbox',
						'default' => false,
						'callback' => 'centerCallback(block, args)'
					),
					array(
						'name' => 'preset-position',
						'label' => 'Position',
						'type' => 'select',
						'tooltip' => 'You can position this element in relation to the block using the positions provided',
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'top_left' => 'Top Left',
							'top_center' => 'Top Center',
							'top_right' => 'Top Right',
							'center_left' => 'Center Left',
							'center_center' => 'Center Center',
							'center_right' => 'Center Right',
							'bottom_left' => 'Bottom Left',
							'bottom_center' => 'Bottom Center',
							'bottom_right' => 'Bottom Right'
						)
					),
					array(
						'type' => 'text',
						'name' => 'width',
						'label' => 'Width',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'height',
						'label' => 'Height',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'left',
						'label' => 'Left',
						'default' => null,
						'callback' => ''
					),
					array(
						'type' => 'text',
						'name' => 'right',
						'label' => 'Right',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'top',
						'label' => 'Top',
						'default' => null
					)

				),
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),
		'image-elements-tab' => array(

			'image-elements' => array(
				'type' => 'repeater',
				'name' => 'image-elements',
				'label' => 'Images',
				'inputs' => array(
					array(
						'type' => 'image',
						'name' => 'image',
						'label' => 'Image',
						'default' => null
					),

					array(
						'type' => 'text',
						'name' => 'image-alt',
						'label' => '"alt"',
						'tooltip' => 'This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.'
					),
					array(
						'type' => 'text',
						'name' => 'custom-id',
						'label' => 'Custom ID (#)',
						'default' => null,
						'tooltip' => 'Set a custom ID so you can style this element differently from all others like it. This will also register this element in the design panel and element tree.'
					),
					array(
						'name' => 'position-type',
						'label' => 'Position type',
						'type' => 'select',
						'default' => 'draggable',
						'options' => array(
							'draggable' => 'Draggable',
							'preset' => 'Preset'
						),
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'center-element',
						'label' => 'Center on x axis?',
						'type' => 'checkbox',
						'default' => false,
						'callback' => 'centerCallback(block, args)'
					),
					array(
						'name' => 'preset-position',
						'label' => 'Position',
						'type' => 'select',
						'tooltip' => 'You can position this element in relation to the block using the positions provided',
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'top_left' => 'Top Left',
							'top_center' => 'Top Center',
							'top_right' => 'Top Right',
							'center_left' => 'Center Left',
							'center_center' => 'Center Center',
							'center_right' => 'Center Right',
							'bottom_left' => 'Bottom Left',
							'bottom_center' => 'Bottom Center',
							'bottom_right' => 'Bottom Right'
						)
					),
					array(
						'type' => 'text',
						'name' => 'width',
						'label' => 'Width',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'height',
						'label' => 'Height',
						'default' => null
					),
					array(
						'name' => 'link-heading',
						'type' => 'heading',
						'label' => 'Link Image'
					),

					array(
						'name' => 'link-url',
						'label' => 'Link URL?',
						'type' => 'text',
						'tooltip' => 'Set the URL for the image to link to'
					),

					array(
						'name' => 'link-title',
						'label' => '"title"',
						'type' => 'text',
						'tooltip' => 'Set title text for the link'
					),

					array(
						'name' => 'link-target',
						'label' => 'New window?',
						'type' => 'checkbox',
						'tooltip' => 'If you would like to open the link in a new window check this option',
						'default' => false,
					),
					array(
						'type' => 'text',
						'name' => 'left',
						'label' => 'Left',
						'default' => null,
						'callback' => ''
					),
					array(
						'type' => 'text',
						'name' => 'right',
						'label' => 'Right',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'top',
						'label' => 'Top',
						'default' => null
					)

				),
				'tooltip' => 'Upload the images that you would like to add to the image block.',
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),

		'svg-elements-tab' => array(

			'svg-elements' => array(
				'type' => 'repeater',
				'name' => 'svg-elements',
				'label' => 'SVG Images',
				'inputs' => array(
					array(
						'type' => 'image',
						'name' => 'svg',
						'label' => 'SVG Image',
						'default' => null
					),

					array(
						'type' => 'text',
						'name' => 'svg-alt',
						'label' => '"alt"',
						'tooltip' => 'This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.'
					),

					array(
						'type' => 'text',
						'name' => 'svg-width',
						'label' => 'Width'
					),
					array(
						'type' => 'text',
						'name' => 'custom-id',
						'label' => 'Custom ID (#)',
						'default' => null,
						'tooltip' => 'Set a custom ID so you can style this element differently from all others like it. This will also register this element in the design panel and element tree.'
					),
					array(
						'name' => 'position-type',
						'label' => 'Position type',
						'type' => 'select',
						'default' => 'draggable',
						'options' => array(
							'draggable' => 'Draggable',
							'preset' => 'Preset'
						),
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'center-element',
						'label' => 'Center on x axis?',
						'type' => 'checkbox',
						'default' => false,
						'callback' => 'centerCallback(block, args)'
					),
					array(
						'name' => 'preset-position',
						'label' => 'Position',
						'type' => 'select',
						'tooltip' => 'You can position this element in relation to the block using the positions provided',
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'top_left' => 'Top Left',
							'top_center' => 'Top Center',
							'top_right' => 'Top Right',
							'center_left' => 'Center Left',
							'center_center' => 'Center Center',
							'center_right' => 'Center Right',
							'bottom_left' => 'Bottom Left',
							'bottom_center' => 'Bottom Center',
							'bottom_right' => 'Bottom Right'
						)
					),
					array(
						'type' => 'text',
						'name' => 'width',
						'label' => 'Width',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'height',
						'label' => 'Height',
						'default' => null
					),
					array(
						'name' => 'svg-link-heading',
						'type' => 'heading',
						'label' => 'Link SVG Image'
					),

					array(
						'name' => 'svg-link-url',
						'label' => 'Link URL?',
						'type' => 'text',
						'tooltip' => 'Set the URL for the image to link to'
					),

					array(
						'name' => 'svg-link-title',
						'label' => '"title"',
						'type' => 'text',
						'tooltip' => 'Set title text for the link'
					),
					array(
						'name' => 'svg-link-target',
						'label' => 'New window?',
						'type' => 'checkbox',
						'tooltip' => 'If you would like to open the link in a new window check this option',
						'default' => false,
					),
					array(
						'type' => 'text',
						'name' => 'left',
						'label' => 'Left',
						'default' => null,
						'callback' => ''
					),
					array(
						'type' => 'text',
						'name' => 'right',
						'label' => 'Right',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'top',
						'label' => 'Top',
						'default' => null
					)

				),
				'tooltip' => 'Upload the images that you would like to add to the image block.',
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),
		'button-elements-tab' => array(
			'button-elements' => array(
				'type' => 'repeater',
				'name' => 'button-elements',
				'label' => 'Button Elements',
				'inputs' => array(
					array(
						'name' => 'button',
						'label' => 'Button Text',
						'type' => 'text',
						'tooltip' => 'Set the text to display in the link',
						'default' => false,
					),
					array(
						'type' => 'text',
						'name' => 'title',
						'label' => '"title"',
						'tooltip' => 'This will be used as the "title" attribute for the button.'
					),
					array(
						'name' => 'url',
						'label' => 'Button URL?',
						'type' => 'text',
						'tooltip' => 'Set the URL/href for the button'
					),
					array(
						'name' => 'target',
						'label' => 'New window?',
						'type' => 'checkbox',
						'tooltip' => 'Open the button in a new window?',
						'default' => false,
					),
					array(
						'type' => 'text',
						'name' => 'custom-id',
						'label' => 'Custom ID (#)',
						'default' => null,
						'tooltip' => 'Set a custom ID so you can style this element differently from all others like it. This will also register this element in the design panel and element tree.'
					),
					array(
						'name' => 'position-type',
						'label' => 'Position type',
						'type' => 'select',
						'default' => 'draggable',
						'options' => array(
							'draggable' => 'Draggable',
							'preset' => 'Preset'
						),
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'center-element',
						'label' => 'Center on x axis?',
						'type' => 'checkbox',
						'default' => false,
						'callback' => 'centerCallback(block, args)'
					),
					array(
						'name' => 'preset-position',
						'label' => 'Position',
						'type' => 'select',
						'tooltip' => 'You can position this element in relation to the block using the positions provided',
						'default' => 'none',
						'options' => array(
							'' => 'None',
							'top_left' => 'Top Left',
							'top_center' => 'Top Center',
							'top_right' => 'Top Right',
							'center_left' => 'Center Left',
							'center_center' => 'Center Center',
							'center_right' => 'Center Right',
							'bottom_left' => 'Bottom Left',
							'bottom_center' => 'Bottom Center',
							'bottom_right' => 'Bottom Right'
						),
						'callback' => 'reloadBlockOptions();refreshBlockContent(block.id);'
					),
					array(
						'type' => 'text',
						'name' => 'width',
						'label' => 'Width',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'height',
						'label' => 'Height',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'left',
						'label' => 'Left',
						'default' => null,
						'callback' => ''
					),
					array(
						'type' => 'text',
						'name' => 'right',
						'label' => 'Right',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'top',
						'label' => 'Top',
						'default' => null
					)

				),
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),
	);
	
}