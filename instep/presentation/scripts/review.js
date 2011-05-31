(function($, InStep){

	InStep.addLoadEvent(function(){
	
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
		
		$('#review-add').click(function(){	
		
			if(!InStep.dateMatch.test($('#review-date').val()) && $('#review-date').val() != "") {
				alert("Please enter a date with the format: dd-mm-yyyy");
				return false;
			}

			var send = "";
			send = $('.reviewForm').serialize();
			if(InStep.tags.length > 0) send += "&" + $.param({tags: InStep.ato(InStep.tags)});
			
			var loc = window.location.href.match(/(.*?\d+).*$/);
			loc = loc[1];
			
			$.post(loc + "/edit", send, function(data){
				if(data.code == 200){
					window.location = loc + "?u=1";
				} else {
					alert(data.message);
				}
			}, 'json');
		
		});
		
		var cache = {}, lastXhr;
		$( "#newTag" ).autocomplete({
			minLength: 3,
			source: function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
					response( cache[ term ] );
					return;
				}

				lastXhr = $.getJSON( InStep.baseURL + "review/tags", { text: $('#newTag').val() }, function( data, status, xhr ) {
					cache[ term ] = data.tags;
					if ( xhr === lastXhr ) {
						response( data.tags );
					}
				});
			}
		});
		
		InStep.players = {};
		
		// Show description on hover over
		$('.domains a').hover(function(){
			if($(this).hasClass('progressPopup') || $(this).hasClass('prog')) return false;
			
			var position = $(this).position();
			
			var popup = $('<div>', { 'class': 'progressPopup' });
			
			if( $('.progressPopup').length > 0 ) {
				popup = $('.progressPopup');
				
			} else {
				popup.insertAfter($(this));
			}
						
			popup.html($('<p>', { text: InStep.domainMap[$(this).index()+1].description }));			

			var popupLeft = position.left - (popup.width() / 2) + (parseInt($(this).css("margin-left").replace("px", "")) * 2);
			var popupTop = position.top + $(this).outerHeight() + 10;
			
			var popupPos = popup.position();
			
			if(popupPos.left == popupLeft && popupPos.top == popupTop) return false;
			
			popup.css({ 'left': popupLeft, 'top': popupTop });
			
			popup.stop(true, true).fadeIn();

		}, function(){
			$('.progressPopup').stop(true, true).fadeOut('fast', function(){
				$(this).remove();
			});			
		});
		
		$('.levelBar a').hover(
			function(){
				if($(this).hasClass('progressPopup') || $(this).hasClass('prog')) return false;
				
				var position = $(this).position();
				
				var popup = $('<div>', { 'class': 'progressPopup' });
				
				// Check to see if the popup already exists. If it doesn't, insert it.
				if( $('.progressPopup').length > 0 ) {
					popup = $('.progressPopup');
					
				} else {
					popup.insertAfter($(this));
				}
				
				// Grab the index
				var lk = $('.domains a.selected:last').index();

				popup.html($('<p>', { text: InStep.domainMap[lk+1].levels[$(this).index()].name, 'style': 'text-align:center' }));
				
				var popupLeft = position.left - (popup.width() / 2) + (parseInt($(this).css("margin-left").replace("px", "")) * 2) + 60;
				var popupTop = position.top + $(this).outerHeight() + 5;
				
				var popupPos = popup.position();
				
				if(popupPos.left == popupLeft && popupPos.top == popupTop) return false;
				
				popup.css({ 'left': popupLeft, 'top': popupTop });
				
				popup.stop(true, true).fadeIn();
			}, function(){
				$('.progressPopup').stop(true, true).fadeOut('fast', function(){
					$(this).remove();
				});			
		});
			
		
	});
	

})(jQuery, InStep);

			
function playerReady(thePlayer) {
	InStep.players[thePlayer.id] = window.document[thePlayer.id];
	addListeners(thePlayer.id);
}

function addListeners(id) {
	if (InStep.players[id]) { 
		InStep.players[id].addControllerListener("VOLUME", "volumeListener");
	} else {
		setTimeout(function(){ addListeners(id) },100);
	}
}


function volumeListener(obj) { 
	eval(obj.id + "_volume = obj.percentage;");
}
