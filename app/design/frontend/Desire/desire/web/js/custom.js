require(['jquery'],function($){
	$(document).ready(function($) {
		$("#social-login-popup .input-text").focus(function(){
			$(this).parent().addClass("move-to-top");
		}).blur(function(){
			if(!$(this).val()){
				$(this).parent().removeClass("move-to-top");
			}
		})
	});  
});