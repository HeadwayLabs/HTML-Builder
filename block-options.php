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
	    			}, 800);
					
						
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

	public $tab_notices = array(
		'blockquote-elements-tab' => '<h1>Blockquote <span>&lt;blockquote&gt;</span></h1><p>Use the Blockquote html element to quote another authors text. You can cite the author and also provide a link to the page it is from if it is on the web.</p>'
	);
	
	public $tabs = array(
		'heading-elements-tab' => 'Heading Elements',
		'text-elements-tab' => 'Text Elements',
		'image-elements-tab' => 'Image Elements',
		'link-elements-tab' => 'Link Elements',
		'button-elements-tab' => 'Button Elements',
		'blockquote-elements-tab' => 'Blockquote Elements',
		'video-elements-tab' => 'Video Elements',
		'audio-elements-tab' => 'Audio Elements',
		'address-elements-tab' => 'Address Elements',
		'svg-elements-tab' => 'SVG Elements',
		'list-elements-tab' => 'List Elements'
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
						'label' => 'Positioning <span>Position with drag or default</span>'
					),
					array(
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
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
						'label' => 'Dimensions <span>Width & Height of element</span>'
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
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
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
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
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
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
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
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
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
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
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
		'blockquote-elements-tab' => array(

			'blockquote-elements' => array(
				'type' => 'repeater',
				'name' => 'blockquote-elements',
				//'label' => 'Heading Elements',
				'inputs' => array(
					array(
						'name' => 'blockquote-heading',
						'type' => 'heading',
						'label' => 'Quote Text <span>What text to quote & cite</span>'
					),
					array(
						'type' => 'wysiwyg',
						'name' => 'blockquote',
						'label' => 'Blockquote Text',
						'default' => null
					),
					array(
						'type' => 'checkbox',
						'name' => 'show-cite',
						'label' => 'Cite Author',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-cite-heading',
									'#input-author-name',
									'#input-before-author',
									'#input-before-author',
									'#input-author-url'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-cite-heading',
									'#input-author-name',
									'#input-before-author',
									'#input-before-author',
									'#input-author-url'
								)
							)
						)
					),

					array(
						'name' => 'cite-heading',
						'type' => 'heading',
						'label' => 'Cite Author<span>Add author and link to source</span>'
					),
					array(
						'type' => 'text',
						'name' => 'author-name',
						'label' => 'Author'
					),
					array(
						'type' => 'text',
						'name' => 'before-author',
						'label' => 'Before Author',
						'default' => '-'
					),
					array(
						'type' => 'text',
						'name' => 'author-url',
						'label' => 'Cite URL',
						'tooltip' => 'URL to cited page where full article can be read'
					),
					array(
						'name' => 'target',
						'label' => 'New window?',
						'type' => 'checkbox',
						'tooltip' => 'Open the link in a new window?',
						'default' => false,
					),
					array(
						'name' => 'position-id-heading',
						'type' => 'heading',
						'label' => 'Positioning <span>Position with drag or default</span>'
					),
					array(
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'dimensions-heading',
						'type' => 'heading',
						'label' => 'Dimensions <span>Width & Height of element</span>'
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
		'address-elements-tab' => array(

			'address-elements' => array(
				'type' => 'repeater',
				'name' => 'address-elements',
				//'label' => 'Heading Elements',
				'inputs' => array(
					array(
						'name' => 'address-heading',
						'type' => 'heading',
						'label' => 'Address Info <span>Add address details</span>'
					),
					array(
						'type' => 'text',
						'name' => 'address',
						'label' => 'Name',
						'default' => null,
						'tooltip' => 'Person or Company/Organisations name.'
					),

					array(
						'type' => 'text',
						'name' => 'street',
						'label' => 'Street',
						'default' => null
					),

					array(
						'type' => 'text',
						'name' => 'complex',
						'label' => 'Complex',
						'default' => null
					),

					array(
						'type' => 'text',
						'name' => 'locality',
						'label' => 'Locality',
						'default' => null
					),

					array(
						'type' => 'text',
						'name' => 'postal-code',
						'label' => 'Post Code',
						'default' => null
					),

					array(
						'type' => 'text',
						'name' => 'country',
						'label' => 'Country',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'tel',
						'label' => 'Tel',
						'default' => null
					),
					array(
						'type' => 'text',
						'name' => 'url',
						'label' => 'URL',
						'default' => null
					),
					array(
						'name' => 'position-id-heading',
						'type' => 'heading',
						'label' => 'Positioning <span>Position with drag or default</span>'
					),
					array(
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'dimensions-heading',
						'type' => 'heading',
						'label' => 'Dimensions <span>Width & Height of element</span>'
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
		'video-elements-tab' => array(

			'video-elements' => array(
				'type' => 'repeater',
				'name' => 'video-elements',
				'label' => 'Upload Video',
				'inputs' => array(

					array(
						'name' => 'video-heading',
						'type' => 'heading',
						'label' => 'Upload Videos <span>Upload & Configure your videos</span>'
					),

					array(
						'type' => 'image',
						'name' => 'video',
						'label' => 'Upload mp4',
						'tooltip' => 'MPEG 4 files with H264 video codec and AAC audio codec: For Internet Explorer 9.0+, Chrome 3.0+, Safari 3.1+, Firefox 21, 24, Android Browser 3.0+, Safari (iOS) 3.1+, Firefox (Android) 17.0+, Internet Explorer (Windows Phone) 9.0+',
						'default' => null
					),

					array(
						'type' => 'image',
						'name' => 'webm',
						'tooltip' => 'For Firefox 4.0+, Chrome 6.0+ and Opera 10.60+, Android Browser 2.3+: WebM files with VP8 video codec and Vorbis audio codec',
						'label' => 'Upload webm',
						'default' => null
					),

					array(
						'type' => 'image',
						'name' => 'poster',
						'label' => 'Video Poster',
						'default' => null
					),

					array(
						'name' => 'position-id-heading',
						'type' => 'heading',
						'label' => 'Positioning <span>Position with drag or default</span>'
					),
					array(
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'dimensions-heading',
						'type' => 'heading',
						'label' => 'Dimensions <span>Width & Height of element</span>'
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
				'tooltip' => 'Upload the images that you would like to add to the image block.',
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),

		'audio-elements-tab' => array(

			'audio-elements' => array(
				'type' => 'repeater',
				'name' => 'audio-elements',
				'label' => 'Upload Audio',
				'inputs' => array(

					array(
						'name' => 'audio-heading',
						'type' => 'heading',
						'label' => 'Audio & Info <span>Upload & Configure Audio</span>'
					),

					array(
						'type' => 'image',
						'name' => 'audio',
						'label' => 'Upload Audio',
						'default' => null
					),

					array(
						'name' => 'position-id-heading',
						'type' => 'heading',
						'label' => 'Positioning <span>Position with drag or default</span>'
					),
					array(
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'dimensions-heading',
						'type' => 'heading',
						'label' => 'Dimensions <span>Width & Height of element</span>'
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
				'tooltip' => 'Upload the images that you would like to add to the image block.',
				'sortable' => true,
				'limit' => false,
				'callback' => '
					draggableCallback(block, args)
				'
			)

		),


		'list-elements-tab' => array(

			array(
				'type' => 'text',
				'name' => 'custom-list-id',
				'label' => 'Custom ID for List',
				'default' => null,
				'tooltip' => 'Set a custom ID so you can style this list'
			),

			'list-elements' => array(
				'type' => 'repeater',
				'name' => 'list-elements',
				'inputs' => array(
					array(
						'name' => 'list-heading',
						'type' => 'heading',
						'label' => 'Address Info <span>Add address details</span>'
					),
					array(
						'type' => 'text',
						'name' => 'list',
						'label' => 'Adddress line 1',
						'default' => null
					),
					array(
						'type' => 'checkbox',
						'name' => 'show-cite',
						'label' => 'Cite Author',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-cite-heading',
									'#input-author-name',
									'#input-before-author',
									'#input-before-author',
									'#input-author-url'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-cite-heading',
									'#input-author-name',
									'#input-before-author',
									'#input-before-author',
									'#input-author-url'
								)
							)
						)
					),

					array(
						'name' => 'cite-heading',
						'type' => 'heading',
						'label' => 'Cite Author<span>Add author and link to source</span>'
					),
					array(
						'type' => 'text',
						'name' => 'author-name',
						'label' => 'Author'
					),
					array(
						'type' => 'text',
						'name' => 'before-author',
						'label' => 'Before Author',
						'default' => '-'
					),
					array(
						'type' => 'text',
						'name' => 'author-url',
						'label' => 'Cite URL',
						'tooltip' => 'URL to cited page where full article can be read'
					),
					array(
						'name' => 'position-id-heading',
						'type' => 'heading',
						'label' => 'Positioning <span>Position with drag or default</span>'
					),
					array(
						'name' => 'enable-draggable',
						'label' => 'Enable draggable',
						'type' => 'checkbox',
						'default' => true,
						'callback' => 'reloadBlockOptions()'
					),
					array(
						'name' => 'dimensions-heading',
						'type' => 'heading',
						'label' => 'Dimensions <span>Width & Height of element</span>'
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

	);
	
}