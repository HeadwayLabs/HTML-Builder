/* 
	Draggable Callback
	- sets draggable on all elements in repeater group
 */

var draggableCallback = function(block, args) {                

	setTimeout(function(){

		var blockID = block.id;

		var setWidth = function(element) {				        

		    var elWidth = element.width();
			element.css('width', elWidth);

		}

		setupGuides = function (element, el) {
  			/* Display center guides during drag */
	  		var blockWidth = element.parents('div#block-' + blockID + '.block').width();
			var elWidth = $(element).width();
			var xCenterPoint = (blockWidth / 2) - (elWidth / 2);

			var blockHeight = element.parents('div#block-' + blockID + '.block').height();
			var elHeight = $(element).height();
			var yCenterPoint = (blockHeight / 2) - (elHeight / 2);

			/* Shouw outline when close to edges */
			if (el.position['top'] <= 1 || el.position['left'] <= 1 || el.position['top'] >= blockHeight - elHeight - 1  || el.position['left'] >= blockWidth - elWidth - 1) {
				$(element).parents('.block').css('border', '1px dotted #D0011B')
			} else {
				$(element).parents('.block').css('border', 'none')
			}

			/* Show center guides */
			if (el.position['top'] >= yCenterPoint && el.position['top'] <= yCenterPoint + 2 && el.position['left'] >= xCenterPoint && el.position['left'] <= xCenterPoint + 2) {
				$(element).parents('.block').removeClass('x-line y-line x-y-line');
				$(element).parents('.block').addClass('x-y-line');
			} else if (el.position['left'] >= xCenterPoint && el.position['left'] <= xCenterPoint + 2) {
				$(element).parents('.block').removeClass('x-line y-line x-y-line');
				$(element).parents('.block').addClass('y-line');
			} else if (el.position['top'] >= yCenterPoint && el.position['top'] <= yCenterPoint + 2) {
				$(element).parents('.block').removeClass('x-line y-line x-y-line');
				$(element).parents('.block').addClass('x-line');
			} else {
				$(element).parents('.block').removeClass('x-line y-line x-y-line');
			}
  		}
		
		var envokeDraggable = function(index, element, elementGroups) {

			element.draggable({

			  	containment: $i('#block-'+blockID),

			  	start: function(event, el) {

			  		elementGroup = $(element).data('element-type');

			  		/* Trigger relevant tab to ensure values are saved correctly */
			  		var subTab = $('div#block-' + blockID + '-tab ul.sub-tabs li#sub-tab-' + elementGroup +'-tab').find('a').attr('href').replace('#', '');
			  		selectTab(subTab, $('div#block-' + blockID + '-tab'));
					
					setWidth(element);

			  	},

			  	drag: function(event, el) {

			  		setupGuides(element, el);

			  	},

			  	stop: function(event, el) {

			  		$(element).parents('.block').removeClass('x-line y-line x-y-line');
			  
			  		//console.log(args)
			  		//console.log(args.input)
			  		//console.log($(args.input).attr("id"))
			  		//console.log($(args.input).parents('.sub-tabs-content').attr('id'))
			  		//console.log($(args.input).attr("id"))

			  		//console.log(input)//the hidden input with all values
					//console.log(args.value)//object with array of all elements in current repeater group
					//console.log(block)// object with ALL block settings with all repeaters
					//console.log(args)//input args for this repeateable group/tab
					//console.log(block.settings)//all the blocks settings as an object ** we use this one to modify ALL elements in block
					//console.log(args.input.context.value)//gets the input value that was just changed
					//console.log(args.input.context.id)//gets the ID of the element just modified

					//draggableCallback(block, args, "text-elements", "top");

					//console.log($(input.context).attr("value", 100)
					//console.log($(input.context).attr("value"))
					//console.log($(input.context).val())

			  		// Get widths
					var elWidth = $(element).width();
					
					var blockWidth = element.parents('.block').width();

					/// Set element width 
					element.css('width', elWidth);

					leftPos = el.position['left'];

					rightPos = (blockWidth - leftPos) - elWidth;

			  		var positions = ['left', 'top', 'right'];

			  		$.each(positions, function(i, position) {

						if(position == 'right') {
							var newPosition = rightPos;
						} else if(position == "left") {
							var newPosition = leftPos;
						} else {
							var newPosition = el.position[position];
						}

						elementGroups[index][position] = newPosition;
						
						var thisInput = args.input.parents('.sub-tabs-content-container').find('#sub-tab-' + elementGroup + '-tab-content .repeater-group').eq(index).find('#input-' + blockID + '-' +position);

						thisInput.val(newPosition);

						setTimeout(function(){

							dataHandleInput(thisInput, newPosition);
							
		     			}, 1000);

		     			var thisCentered = args.input.parents('.sub-tabs-content-container').find('#sub-tab-' + elementGroup + '-tab-content .repeater-group').eq(index).find('#input-' + blockID + '-center-element');

		     			thisCentered.parents('.input-checkbox').find('.checkbox-checked').removeClass()

		     			
			  			
			  		})

				}
			  	
			    
			});
		}


		/*
			Go through each element and each group and envoke draggable
		*/
		$.each(block.settings, function(elementType, elementGroups) {

			//console.log(elementGroups)//repeater groups each as object, eg: text, images

			//gets each groups options and then applies draggable to them
			$.each(elementGroups, function(index, settings) {

				if (settings['disable-draggable'] == "false" || settings['disable-draggable'] == false) {
					var element = $i('.' + elementType + '.element' + index);
					envokeDraggable(index, element, elementGroups)
				};
				
			
			});

		});

	}, 1000);

}

