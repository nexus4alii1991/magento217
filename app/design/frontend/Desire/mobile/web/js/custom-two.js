require([ 'jquery', 'jquery/ui'], function($){
 	jQuery(document).on('click','.plus',function(){
 		var dev = $(this).prev('input').attr('id');
 		var numer = $(this).prev('input').val();
		var update = parseInt(numer) + 1;
		$(this).prev('input').val(update);
		if($('.'+dev+'_upcart').length == 0) {
		jQuery(this).closest('.control').append('<button class="update-cart-item '+dev+'_upcart"><span>Update</span></button>');
	    }
	});

    jQuery(document).on('click','.minus',function(){
    	var dev = $(this).next('input').attr('id');
 		var numer = $(this).next('input').val();
 		if(parseInt(numer) > 1){
		var update = parseInt(numer) - 1;
		$(this).next('input').val(update);
		if($('.'+dev+'_upcart').length == 0) {
		jQuery(this).closest('.control').append('<button class="update-cart-item '+dev+'_upcart"><span>Update</span></button>');
	    }
	    }
	});
	jQuery(document).on('click','.update-cart-item',function(){
    	jQuery('.update').trigger('click');
	});
	jQuery(document).on('click','.cust_delete',function(){
		var item_id = jQuery(this).attr('data-cart-item');
		var form_key = jQuery('input[name="form_key"]').val();
        var site_url = jQuery('#site_url').val();
		$.ajax({
                showLoader: false,
                url:  site_url+"checkout/sidebar/removeItem/",
                data: {"item_id":parseInt(item_id),"form_key":form_key},
                type: "POST"
            }).done(function (data) {
             console.log(data);
             });
	});
})