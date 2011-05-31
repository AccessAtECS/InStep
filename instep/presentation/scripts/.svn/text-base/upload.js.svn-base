(function($, InStep){

	// List of tags for this project. Populated on load, if required (when editing). Otherwise blank.
	
	InStep.tags = [];
	InStep.domains = { notice: 1, respond: 2, engage: 3, cando: 4, improve: 5, skilled: 6 };
	
	currentLevel = 0;

	function bindLinks(){
		$('.domains a').bind('click', function(){
			$('.domains a').removeClass('selected');
			$(this).addClass('selected');
			var index = $(this).index();
			$(this).siblings(":lt(" + index + ")").addClass('selected');
			
			var classes = $(this).attr('class').split(" ", 1);
			$('#domainLevel').val(InStep.domains[classes]);

			levelKey = $('.levelBar a.selected:last').index();
			updateDescription(index, levelKey);

		});
		
		$('.domains a').bind('mouseover', function(){
			$('.domains a:gt(' + $(this).index() + '):not(.selected)').removeClass('domainHover');
			$('.domains a:lt(' + $(this).index() + '):not(.selected)').addClass('domainHover');
		});
		
		$('#descHolder, .domains a').bind('mouseout', function(){
			$('.domains a').removeClass('domainHover');
		});
		
		
		// Bind changing the level bar.
		$('.levelBar a').bind('click', function(){
			myIndex = $(this).index();
			
			$(this).addClass('selected');
			$('.levelBar a:lt(' +  myIndex + ')').addClass('selected');
			$('.levelBar a:gt(' + myIndex + ')').removeClass('selected');
			
			var l = parseInt($(this).html());
			
			$('input#level').val(l);
			
			levelKey = $('.domains a.selected:last').index();
			updateDescription(levelKey, myIndex);
		});
	
		// Bind removing tags.
		$('span.tag a').live('click', function(){
			$(this).parent().fadeOut('slow', function(){
				name = $(this).data('tag');
				removeTag(name);
				$(this).remove();
				
			});
		});
		
		// Bind create a tag.
		$('#addNewTags a').bind('click', function(){	
			tag = $('#newTag').val();
			if(tag == "") return;
			
			if(tag in InStep.ato(InStep.tags)){
				alert('This tag has already been used!');		
			} else {
				// Add a tag
				addTag(tag);
			}
			// Clear the textbox.
			$('#newTag').val("");
		});
		
		// Bind date field.
		$('#review-date').bind('blur', function(){
			if(!InStep.dateMatch.test($(this).val()) && $('#review-date').val() != "") alert("Please enter a date with the format: dd-mm-yyyy");
		});
	}
	
	// Load data (tags) into the system and display
	function loadData(){
		// Get tags
		$.getJSON(window.location.href.replace(/#$/, '') + "/tags", function(data){
			if(data.code == 200){
				InStep.tags = data.tags;
				
				for(t in InStep.tags){
					addTag(InStep.tags[t]);
				}
			}
		});
		
		$( "#newTag" ).bind('keypress', function(e){
			if(e.keyCode==13){
				$('#addNewTags a').trigger('click');
			}
		});
	}

	// Remove a tag
	function removeTag(name){
		for(t in InStep.tags){
			if(InStep.tags[t] == name) {
				InStep.tags.splice(t, 1);
				return;
			}
		}
	}
	
	// Add a tag
	function addTag(name){		
		$('#tagContainer').append(
			$('<span>', { 'class': 'tag', 'style': 'display:none' }).data('tag', name).append(
				name + " ",
				$('<a>').append(
					$('<img>', {src: InStep.includeURL + "presentation/images/slash-button.png" })
				)
			).fadeIn()
		);
		InStep.tags.push(name);
	}


	function updateDescription(domainKey, levelKey){
		var domainKey = parseInt(domainKey) + 1;
		var newLevel = domainKey * levelKey;
		var hideDirection = (newLevel > currentLevel) ? 'left' : 'right';
		var showDirection = (newLevel > currentLevel) ? 'right' : 'left';
		
		$( "#levelDescriptionHolder" ).hide("drop", { direction: hideDirection, easing: 'easeOutExpo' }, 250);
		
		$("#domainName").text(InStep.domainMap[domainKey].name + " - level " + (parseInt(levelKey)+1));
		$("#levelName").text(InStep.domainMap[domainKey].levels[levelKey].name);


		$( "#levelDescriptionHolder" ).show("drop", { direction: showDirection, easing: 'easeInExpo' }, 250);
		currentLevel = newLevel;
	}	

	InStep.addLoadEvent(function(){
		bindLinks();
		loadData();
	});
	

})(jQuery, InStep);