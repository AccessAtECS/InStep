function InStep(){

	// Array of functions to run post-setup
	this.onLoadEvents = new Array();

	// Container for domains - fetched off server.
	this.domainMap = {};
	
	this.dateMatch = /^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/;
	
	// Function to call to add a post-setup function to InStep
	this.addLoadEvent = function(f){
		InStep.onLoadEvents.push(f);
	}
	
	this.init = function(){
		// Get domain map
		jQuery.getJSON(InStep.includeURL + "/presentation/scripts/domainMap.json", function(data){
			InStep.domainMap = data;	
		});	
		
		// Run post-setup scripts
		for(i in InStep.onLoadEvents){
			InStep.onLoadEvents[i]();
		}
	}
	
	this.ato = function(a){
		var o = {};
		for(var i=0;i<a.length;i++){
			o[a[i]]='';
		}
		return o;
	}

}

// Create the InStep object
var InStep = new InStep();

// Once the document is ready, run these functions
jQuery(document).ready(function() {
	// Get the list of domains
	InStep.init();

	if (typeof console == "undefined" && $.browser.msie) var console = { log: function() {}, dir: function(){} }; 

	if($.browser.msie) {
		$('.navbutton').css('border', '1px solid #5484b1');
		$('.navbutton').cornerz({ corners: 'tr tl bl br', radius: 10, background: "#8dbae6" });
		
		$('#wrapper').css('border', '1px solid #6585a5');
		
		$('#wrapper').cornerz({ corners: 'tr tl', radius: 10, background: "#8dbae6", borderWidth: 1 })
						.cornerz({ corners: 'bl br', radius: 10, background: "#f9f9f9", borderWidth: 1 });
		
		/*$('.levelBar').cornerz({ corners: 'tr tl bl br', radius: 15, background: "#E4E6E8"});
		$('.levelBar').children('.selected:first').cornerz({ corners: 'tl bl', radius: 15, background: "#E4E6E8"});
		$('.levelBar').children('.selected:last').cornerz({ corners: 'tr br', radius: 15, background: "#E4E6E8"});*/
	}
	
	$('.metadata table').each(function(){
		$(this).find('tr').each(function(i){
			if(i % 2 == 0) $(this).css('background-color', '#d8dbde');
		});
	});
	
	// Editing notes
	$('span.edit a').bind('click', function(){
		var note = $(this).parentsUntil('.note').parent().children('.noteBody');
		var noteID = note.siblings("input[name=id]").val();
		var body = note.html();
		
		note.html(
			$('<textarea>', { val: body, 'style': 'width:100%' }).bind('focusout', 
				function(){ 
					if($(this).val() != body) {
						InStep.commitNote( noteID, $(this).val(), $(this) );
					} else {
						$(this).replaceWith(body);
					}
				}
			)
		);
		return false;
	});
	
	InStep.commitNote = function(id, text, container){
		$.post(InStep.baseURL + 'review/note/update', { 'id': parseInt(id), 'note': text },  function(data) {
  			if(data.code == 200){
  				var note = $(container).val();
  				$(container).replaceWith(
  					$('<div>', { html: 'Note saved successfully', 'class': 'saveConfirmation' }).fadeIn(1000, function(){
						$(this).fadeOut(2000, function() { 
							$(this).replaceWith(note);
						 })
  					})  				
  						
  				);
  			} else {
  				alert(data.message);
  			}
		}, 'json');
	}
	
	// Date boxes
	$.datepicker.setDefaults( $.datepicker.regional[ "en-GB" ] );
	$( "#date, .datePicker" ).datepicker();
	$( "#date, .datePicker" ).datepicker("option", "dateFormat", "dd-mm-yy");
});