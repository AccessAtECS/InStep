(function($, InStep){

	var usedObjects = [];

	var learnerID = 0;
	
	function createDataStorage(){
		$('.compareItem').data('compareObject', {
			objectID: 0,
			title: '',
			description: '',
			date: ''
		});
		
		// This needs to come from the database once the connector is done...
		$('.smallCompare').each(function(i,v){
			$(this).data('compareObject', {
				objectID: $(this).children('input[name=id]').val(),
				title: $(this).children('span.title').html(),
				description: $(this).children('span.description').html(),
				date: new Date($(this).children('span.description').html())
			});
		});
		
		$('.smallCompare').bind('dblclick', function(){
			var learnerID = window.location.toString().match(/\?learner=(\d+)&/);
			window.location = InStep.baseURL + "review/" + $(this).data('compareObject').objectID + "/" + learnerID[1];
		});
	}

	function bindDraggables(){
		$(".smallCompare").draggable({ 
			//revert: "invalid",
			appendTo: 'body', 
			zIndex:2700,
			helper: 'clone',
			revert:'invalid',
            start: function(event, ui) {
                $(this).hide();
            },
            stop: function(event, ui) {
                $(this).show();
                $(this).addClass('draggableHoverActive');
            }
		 }).disableSelection();
		
		$(".compareItem").droppable({
			hoverClass: "draggableHoverActive",
			drop: function(event, ui){
				$(this).addClass('populatedCompare');

				if(canCopy($(ui.draggable), $(this)) == false) return false;

				$(this).data('compareObject', $(ui.draggable).data('compareObject') );
				usedObjects.push($(ui.draggable).data('compareObject').objectID);
				copySearchItem($(ui.draggable), $(this));
				$(ui.helper).remove();
				
			}
		});	
		
	}
	
	function copySearchItem(source, destination){
		destination.children().remove();
		
		newObject = source.clone();
		
		newObject.find('img.thumb').attr('align', 'right').insertAfter(newObject.find('span.description'));
		
		newObject.removeClass('smallCompare').addClass('comparisonItem').appendTo(destination);
		
		newObject.fadeIn();
		
		if($("#compare1").data('compareObject').objectID != 0 && $("#compare2").data('compareObject').objectID != 0){
			$('#invokeCompareItems').removeAttr('disabled').bind('click', function(){
				// Get learner
				var learnerID = window.location.toString().match(/\?learner=(\d+)&/);
				window.location = InStep.baseURL + "compare/" + $("#compare1").data('compareObject').objectID + "-" + $("#compare2").data('compareObject').objectID + "/" + learnerID[1];
			})
		}
		$(source).remove();

	}

	function canCopy(source, destination){
		// Check to see if we can actually copy this object (we shouldnt be able to if its already been used elsewhere)
		srcID = $(source).data('compareObject').objectID;
		destID = $(destination).data('compareObject').objectID;
		if(srcID != destID || srcID in InStep.ato(usedObjects)) {
			return true;
		} else {
			return false;
		}
	}


	function bindSearchBoxes(){
		
		$.datepicker.setDefaults( $.datepicker.regional[ "en-GB" ] );
		$( "#date" ).datepicker();
		$( "#date" ).datepicker("option", "dateFormat", "dd-mm-yy");
	}

	InStep.addLoadEvent(function(){
		createDataStorage();
		bindDraggables();
		bindSearchBoxes();
	});
	

})(jQuery, InStep);