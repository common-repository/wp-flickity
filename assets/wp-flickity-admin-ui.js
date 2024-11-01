var FlickityUi = function(){
	return{
		imgsToViewport: function(){
			if(jQuery('#flickities-images').length !=0){
				var images_ids = jQuery('#flickities-images').val().split(',');
				if(images_ids[0]!=""){
					jQuery.ajax({
						url: 'admin-ajax.php',
						type: 'POST',
						data: {
							'action': 'flickity_ajax',
							'imagesids': images_ids
						},
					})
					.done(function(response) {
						//console.log(response);
						jQuery('#flickity-images-wrapper').html('');
						var h = "";
						for(var index in response){
							h = h + '<li img-id="'+response[index].id+'"><span class=" remove-flick-image dashicons dashicons-no"></span><img src="'+response[index].thumbnail[0]+'"></li>';
						}
						jQuery('#flickity-images-wrapper').append(h);
						jQuery('#flickity-images-wrapper').trigger('sortupdate');
						jQuery('#flickity-images-wrapper').sortable({
							handle: "img",
							update: function(){
								jQuery('#flickity-images-wrapper').trigger('sortupdate');
							}
						}).on('sortupdate', function(event) {
							var arrImages =[];
							jQuery('#flickity-images-wrapper').find('li > img').each(function(index, el) {
								arrImages.push(parseInt(jQuery(this).parent('li').attr('img-id')));
							});
							jQuery('#flickities-images').val(arrImages.join(','))
						});
					})
					.fail(function(err) {
						//console.log("error");
						//console.log(err);
					})
					.always(function() {
						//console.log("complete");
					});	
				}		
			}
		}
	}
}();

jQuery(document).ready(function($) {
	$(window).load(function() {
		FlickityUi.imgsToViewport();
	});
	$('#flickity-images-wrapper').on('click', '.remove-flick-image', function(event) {
		event.preventDefault();
		$(this).parent('li').detach();
		$('#flickity-images-wrapper').trigger('sortupdate');
	});
	$('.flickity-remove-link').on('click', function(event) {
		event.preventDefault();
		if(confirm('Are You Sure?')){
			window.location = $(this).attr('href');
		}else{
			//do nothing here. Nice!
		}
	});
	/**
	 * Help Button
	 */
	$('#wp-flickity-help-button').on('click', function(event) {
		event.preventDefault();
		$('.wp-flickity-help-wrapper').css('display','block').animate({
			opacity: 1},
			800, function() {
			/* stuff to do after animation is complete */
		});
	});
	/**
	 * Closer button of help window
	 */
	$('#wp-flickity-help-closer').on('click', function(event) {
		event.preventDefault();
		$('.wp-flickity-help-wrapper').animate({
			opacity: 0},
			500, function() {
			$(this).css('display','none');
		});
	});
	$('#wp-flickity-previewer').on('load', function(event) {
		var preview = $(this).contents();
		var h_preview = preview.find('html').height();
		$(this).height(h_preview);
	});
	$(window).load(function() {
		$('[wp-flickity-sync-on]').trigger('wpFlickitySync');
	});
	
	$('[wp-flickity-sync-on]').on('wpFlickitySync keyup change mouseup', function(e) {
		var v = $(this).val();
		var syncTo = $(this).attr('wp-flickity-sync-on');
		$(document).find(syncTo).val(v);
	});
	/**
	 * #flickity-querycomposer-button
	 */
	$('#flickity-querycomposer-button').on('click',function(){
		var syncTo = $(this).attr('sync-query-to');
		$('.querycomposer').toggleClass('show-frame');
		var qs = $(syncTo).val();
		var obj = $.parseJSON('{"' + qs.replace(/&/g, '","').replace(/=/g, '":"') + '"}');
		var strToRemove = '';
		if(obj.posts_per_page){
			$('[name="posts_per_page"]').val(obj.posts_per_page);
			strToRemove += 'posts_per_page='+obj.posts_per_page;
		}
		if(obj.post_type){
			var postTypesSelected = obj.post_type.toString().split(',');
			//console.log(postTypesSelected);
			$('[name="post_type"] > option').each(function(i,opt){
				//console.log( $(opt).attr('value') );
				if( postTypesSelected.indexOf( $(opt).attr('value') )>=0 ){
					$(opt).attr('selected','selected');
				}
			});
			strToRemove += '&post_type='+obj.post_type;
		}
		var strQueryS = $(syncTo).val().toString().replace(strToRemove,'');
		$('.querycomposer').trigger('updateQuery');
	});
	
	$('.querycomposer input, .querycomposer select').on('change',function(){
		$('.querycomposer').trigger('updateQuery');
	});
	$('.querycomposer').on('updateQuery',function(){
		var query = 'posts_per_page='+$('[name="posts_per_page"]').val();
		if($('[name="post_type"]').val()!=null && $('[name="post_type"]').val()!=""){
			query = query + '&post_type=' + $('[name="post_type"]').val().toString().replace('&',',');
		}
		if($('[name="advanced_query"]').val()!=""){
			query = query + '&' + $('[name="advanced_query"]').val();
		}
		$('.query-preview').text(query);
		$('[wp-flickity-sync-on]').trigger('wpFlickitySync');
	});
	$('#save-querycomposer').on('click',function(){
		$('.querycomposer').trigger('updateQuery');
		$('.querycomposer').removeClass('show-frame');
		var syncTo = $('#flickity-querycomposer-button').attr('sync-query-to');
		$(syncTo).val($('.query-preview').text());		
		$('[wp-flickity-sync-on]').trigger('wpFlickitySync');
	});
	
});

var file_frame;
jQuery('.upload-custom-img').live('click', function( event ){
	event.preventDefault();
	// If the media frame already exists, reopen it.
	if ( file_frame ) {
	  file_frame.open();
	  return;
	} 
	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
		title: jQuery( this ).data( 'uploader_title' ),
		button: {
			text: jQuery( this ).data( 'uploader_button_text' ),
		},
	   	library: {
            type: 'image'
        },
	  	multiple: true  // Set to true to allow multiple files to be selected
	});
	// When an image is selected, run a callback.
	file_frame.on( 'select', function() {
	  // We set multiple to false so only get one image from the uploader
	  	var selection = file_frame.state().get('selection');
	  	var images_ids = [];
	    selection.map( function( attachment ) {
	      attachment = attachment.toJSON();
	      images_ids.push(attachment.id);
	      // Do something with attachment.id and/or attachment.url here
	    });
	    if(jQuery('#flickities-images').val()!=""){
	    	var selectionTarget = jQuery('#flickities-images').val().toString().split(',');	
	    }else{
	    	var selectionTarget = [];
	    }
	    selectionTarget.push(images_ids);
	    jQuery('#flickities-images').val(selectionTarget.join(','));
	    FlickityUi.imgsToViewport();
	  // Restore the main post ID
	  //wp.media.model.settings.post.id = wp_media_post_id;
	});
	// Finally, open the modal
	file_frame.open();
});

// Restore the main ID when the add media button is pressed
jQuery('a.add_media').on('click', function() {
//wp.media.model.settings.post.id = wp_media_post_id;
});