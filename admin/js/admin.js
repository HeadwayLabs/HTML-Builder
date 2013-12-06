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
		
		var envokeDraggable = function(index, element, elementGroups) {

			element.draggable({

			  	containment: $i('#block-'+blockID),

			  	grid: [10,10],

			  	snap: true,

			  	start: function() {

			  		elementGroup = $(element).data('element-type');

			  		/* Trigger relevant tab to ensure values are saved correctly */
			  		var subTab = $('div#block-' + blockID + '-tab ul.sub-tabs li#sub-tab-' + elementGroup +'-tab').find('a').attr('href').replace('#', '');
			  		selectTab(subTab, $('div#block-' + blockID + '-tab'));
					
					setWidth(element);

			  	},

			  	stop: function(event, el) {
			  
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
					leftPosPercentage = (leftPos / (blockWidth - elWidth)) * 100;

					rightPos = (blockWidth - leftPos) - elWidth;
					rightPosPercentage = (rightPos / (blockWidth - elWidth)) * 100;

					if (leftPosPercentage >= 50 && rightPosPercentage <= 50) {
			        	element.css('right', rightPosPercentage+'%');
						element.css('left', 'inherit');
			        } else if (leftPosPercentage <= 50 && rightPosPercentage >= 50) {
			        	element.css('left', leftPosPercentage+'%');
						element.css('right', 'inherit');
			        }

			  		var positions = ['left', 'top', 'right'];

			  		$.each(positions, function(i, position) {

						if(position == 'right') {
							var newPosition = rightPosPercentage;
						} else if(position == "left") {
							var newPosition = leftPosPercentage;
						} else {
							var newPosition = el.position[position];
						}

						elementGroups[index][position] = newPosition;
						
						var thisInput = args.input.parents('.sub-tabs-content-container').find('#sub-tab-' + elementGroup + '-tab-content .repeater-group').eq(index).find('#input-' + blockID + '-' +position);

						thisInput.val(newPosition);

						setTimeout(function(){

							dataHandleInput(thisInput, newPosition);
							
		     			}, 700);
			  			
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

				var element = $i('.' + elementType + '.element' + index);
				envokeDraggable(index, element, elementGroups)
			
			});

		});

	}, 1000);

}