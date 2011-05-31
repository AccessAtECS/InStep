(function($, InStep){

	InStep.addLoadEvent(function(){

		$('#comparison-add').click(function(){	
			var send = {};
			
			send.notes = $('#review-notes').val();
			send.learnerID = $('#learnerID').val();
			
			$('.reviewForm').each(function(i, v){
				id = $(this).attr('id');
				realId = id.match(/-(\d+)$/);
				send['review' + (i + 1)] = parseInt(realId[1]);
			});
			
		
			$.post(window.location.href.replace(/#$/, '') + "/add", send, function(data){
				if(data.code == 200){	
					window.location = InStep.baseURL + "compare/view/" + data.comparison_id;
				} else {
					alert(data.message);
				}
			}, 'json');
		
		});
		
		InStep.players = {};
		
		
		$('.displayBar div[class!=progressPopup]').hover(function(){

			if($(this).hasClass('progressPopup') || $(this).hasClass('prog')) return false;
			
			var end = $(this).parent().find('.end');
			var position = end.position();
			
			
			var popup = $('<div>', { 'class': 'progressPopup' });
			
			if( $('.progressPopup').length > 0 ) {
				popup = $('.progressPopup');
				
				if(popup.parentsUntil('.displayBar') != $(this).parentsUntil('.displayBar')){
					popup.insertAfter(end);
				}
				
			} else {
				popup.insertAfter(end);
			}
			
			
			var inputs = $(this).parentsUntil('.barHolder').parent().find('input[type=hidden]');
			
			var progressBar = $('<div>', { 'class': 'pbar', 'style': 'width:170px;' }).append($('<div>', { 'class': 'progress' }));
			var heading = $('<h4>', { text: inputs.filter('[name=category]').val() });
			var info = $('<p>', { text: "Level " +  inputs.filter('[name=levelNum]').val()  + " of 3 - " + inputs.filter("[name=level]").val() });
			var description = $('<p>', { 'class': 'smallinfo', text: inputs.filter('[name=description]').val() });
			
			popup.html(heading)
				.append(progressBar, info, description);
			
			
			
			var popupLeft = position.left - (popup.outerWidth() / 2);
			var popupTop = position.top + end.outerHeight() + 5;
			
			var popupPos = popup.position();
			
			if(popupPos.left == popupLeft && popupPos.top == popupTop) return false;
			
			popup.css({ 'left': popupLeft, 'top': popupTop });
			
			popup.stop(true, true).fadeIn('fast', function(){
				var percentage = inputs.filter('[name=progress]').val() + "%";
				popup.find('.progress').animate({
					width: percentage
				}, 600);
			});
			
		}, function(){
			$('.progressPopup').stop(true, true).fadeOut('fast', function(){
				$(this).remove();
			});		
		});
		
		/*$('.progressPopup, .displayBar').live('mouseout', function(e){
			$('.progressPopup').fadeOut('fast', function(){
				$(this).remove();
			});
		});*/
		
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
