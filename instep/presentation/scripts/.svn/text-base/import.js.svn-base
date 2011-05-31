// This script is injected into InFolio to allow users to add content to InStep.

$(function($) {

	// Boolean value denoting whether the asset is currently an InStep asset.
	var isAsset = false;
	var url = null;
	var baseURL = null;
	var assetID = null;
	var isAdminarea = false;
	
	var instep_label = "Include in InStep progression module?";
	var instep_confirmation = "Click update to commit your change";
	
	function init(){
		// Get the base URL
		u = window.location.toString().match(/(.*?)(?=[^\/]*$)/);
		url = u[1];
		
		// Check if we're looking at the admin page.
		if( /admin\/\?do=8/i.test(window.location.toString()) ){
			isAdminarea = true;
			injectAdmin();
			baseURL = url.replace(/admin\//i, '');
		} else {
			if($('.box-image').length > 0){
				if($('.box-image').children().get(0).nodeName.toLowerCase() == "img") return;
			}


			$('#footer').append($("<a>").attr({ href: "instep/"}).html("InStep"));
			
			baseURL = url;
			// Get the asset ID
			assetID = $('input[name=c]').val();	
					
			// Get status
			getStatus();
		}
	}
	
	function injectAdmin(){
		var instepHeader = $('<h2>', { id: 'instepHeader', html: 'InStep Controls' });
		
		$('#workspaceContainer').children('form:eq(0)').after(instepHeader);
		
		$('#SelectedImage').load(function(){
			adminInteraction();
		});
	}
	
	function adminInteraction(){
		try {
			assetID = getSelectedAssetId();
			getStatus();
		} catch(e){
			return false;
		}
	}
	
	function getStatus(){
		$.getJSON(baseURL + "instep/import", { id: assetID },  function(data){
			if(data.code == 200){
				isAsset = data.isAsset;
				inject();
			} else {
				if($('#InStepHolder').length != 0) $('#InStepHolder').remove();
			}
		});
	}

	// Send a request to the server stating that we're updating the status of this asset.
	function changeStatus(){
		var status = $('#instep').is(':checked') ? 1 : 0;
		console.log(status);
		$.getJSON(baseURL + "instep/import", { id: assetID, isAsset: status }, function(data){
			if(data.code == 200){
				$('#InStepUpdate').remove();
				alert(data.message);
			} else {
				alert(data.message);
			}
		
		});
	}

	// Show the user that they have updated the checkbox, and they must click update to actually finalise this change.
	function showChange(){
		if($('#InStepUpdate').length == 0){
			$('#InStepHolder').append(
				$('<div>').attr({'style': 'font-size:8pt; margin-left:28px; line-height:5px; color:red', 'id': 'InStepUpdate'}).html(instep_confirmation)
			);		
		}
	}

	// Inject HTML into the page
	function inject(){
		if($('#InStepHolder').length != 0) $('#InStepHolder').remove();
		
		var controls = $('<div>').attr({'style': 'margin-top: 40px', 'id': 'InStepHolder'}).append(
			$('<input>').attr({ 'id': 'instep', 'type': 'checkbox', 'name': 'instep', checked: isAsset }).bind('change', function(){ showChange() }),
			$('<label>').attr('style', 'margin:10px').text(instep_label).attr({ 'for': 'instep' }),
			$('<button>').text('Update').bind('click', function(){ changeStatus(); return false; })
		);
	
	
		if(isAdminarea){
			$('#instepHeader').after( controls );
			//controls.insertAfter('#addInstep');
		} else {
			$('.box-text div:first-child').append( controls );
		}
	}
	
	// Start injection script
	init();

});