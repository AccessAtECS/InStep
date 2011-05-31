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
			window.location = InStep.baseURL + "compare/view/" + $(this).data('compareObject').objectID;
		});		

	});
	

})(jQuery, InStep);