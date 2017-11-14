 require([ 'jquery', 'jquery/ui'], function($){

  jQuery(document).ready(function(){  
    // if(localStorage.getItem('otpMobNum')){
    //   localStorage.removeItem('otpMobNum');
    // }
    console.log(localStorage.getItem('otpMobNum'));
    var bserl = $('#baseurl').val();
    var est = $("#estimate").val();
    var mail = $("#custom_mail").val();
    var zip ="";
    var otp = "";
    var otp_mob = "";
    var top_log = "";
    var setdate = setInterval(function(){
     if(document.getElementById('checkout-loader') == null) {
      var top_log = jQuery('#log_act').val();
     if(top_log == 'no'){
        jQuery( "ul li:nth-child(2)" ).removeClass("_active");
        jQuery("ul li:nth-child(1)" ).addClass("_active");
        jQuery("#social-login-popup").removeClass("mfp-hide");
        jQuery("#checkout-loader").css("display","none");
     }else if(top_log == 'no'){
       jQuery("#checkoutSteps").css({"margin-top": 0, "pointer-events": "auto","opacity": 1});
     }
      jQuery('.edit_quantity').attr('href' , bserl+'checkout/cart');
      jQuery('.remove_product').attr('href' , bserl+'checkout/cart');

      jQuery(document).on('click','.continue',function(){
        otp_mob = jQuery('.selected-item .mob').text();
        if(otp_mob == "" || otp_mob == null){
          otp_mob = jQuery('input[name="telephone"]').val();
          localStorage.setItem('otpMobNum',otp_mob);
          jQuery('#custom-mobile').val(localStorage.getItem('otpMobNum'));
        }else{
          localStorage.setItem('otpMobNum',otp_mob);
          jQuery('#custom-mobile').val(localStorage.getItem('otpMobNum'));
        }
        
    });
      /* COD otp verification */
      function Vert(abc){
                       $.ajax({
                                showLoader: true,
                                url: bserl+"sociallogin/milton/milton",
                                data: {"mobile":parseInt(abc)},
                                type: "POST"
                            }).done(function (data) {
                               jQuery('.sent').show();
                               jQuery('.verifi').show();
                               jQuery('#veri').hide();
                               jQuery('.sent').delay(10000).fadeOut();
                            });
      }
    jQuery(document).on('click','#veri',function(){
      Vert(localStorage.getItem('otpMobNum'));        
    }); 
    jQuery(document).on('click','.resend',function(){
     // otp = jQuery('#custom-mobile').val();
                       $.ajax({
                                showLoader: true,
                                url: bserl+"sociallogin/milton/milton",
                                data: {"mobile":parseInt(localStorage.getItem('otpMobNum'))},
                                type: "POST"
                            }).done(function (data) {
                               jQuery('.resent').show();
                               jQuery('.resent').delay(4000).fadeOut();
                            });                         
    });  
    jQuery(document).on('click','#verify',function(){
          var my_otp = jQuery('#otp').val();
          //var base_url = jQuery('#base_url').val();
                        $.ajax({
                                showLoader: true,
                                url: bserl+"sociallogin/main/main",
                                data: {"mobile":parseInt(localStorage.getItem('otpMobNum')),"otp":my_otp},
                                type: "POST"
                            }).done(function (data) {
              if(data == "not matched"){
              jQuery('.not-match').show();
              jQuery('.not-match').delay(4000).fadeOut();
              }else if(data == "matched"){
              jQuery('.logged').show();
              jQuery('.logged').delay(4000).fadeOut();
              jQuery('.verifi').delay(6000).fadeOut();
              jQuery(".checkout").removeAttr('disabled');
              }
                            });
    });         
   jQuery(document).on('change','#cashondelivery',function(){
    $(".checkout").attr("disabled", "disabled");
    });
      /* COD otp verification */

      zip = $(".selected-item .my-zip").text();
      jQuery('.pin').html(zip);
       $.ajax({
        showLoader: true,
        url: bserl+"sociallogin/check/check",
        data: {"zipcode":parseInt(zip)},
        type: "POST"
    }).done(function (data) {
    if(data == 1){
    $(".continue").removeAttr("disabled")
    $('#not').fadeOut();
    }else if(data == 0){
    jQuery('#not').show();  
        $(".continue").attr("disabled", "disabled");
    }else if(data == "not"){
    jQuery('#not').show();  
        $(".continue").attr("disabled", "disabled");
    }
    });
      jQuery('#demo1').html(mail);
      updateDiv();
     }
    }, 1000);
function updateDiv(){
  clearInterval(setdate);
}
    
  var count="";
  var city = "";
  var country = "";
  var state = "";
  var jui = "";

     jQuery(document).on('click','.action-select-shipping-item',function(){
    var my_zip = $(".selected-item .my-zip").text();
    jQuery('.pin').html(my_zip);
                       $.ajax({
                                showLoader: true,
                                url: bserl+"sociallogin/check/check",
                                data: {"zipcode":parseInt(my_zip)},
                                type: "POST"
                            }).done(function (data) {
                if(data == 1){
                $(".continue").removeAttr("disabled")
                $('#not').fadeOut();
                }else if(data == 0){
                jQuery('#not').show();  
                    $(".continue").attr("disabled", "disabled");
                }else if(data == "not"){
                jQuery('#not').show();  
                    $(".continue").attr("disabled", "disabled");
                }
                            });
                         
  });          

    jQuery(document).on('change','input[name="postcode"]',function(){
    var tyu = $('input[name="postcode"]').val();
                       $.ajax({
                                showLoader: true,
                                url: bserl+"sociallogin/check/check",
                                data: {"zipcode":tyu},
                                type: "POST"
                            }).done(function (data) {
                if(data == 1){
                $(".continue").removeAttr("disabled")
                $('#not').fadeOut();
                }else if(data == 0){
                                jQuery('.custom-warn').show();
                                jQuery('.custom-warn').delay(4000).fadeOut();
                                jQuery('input[name="postcode"]').delay(4000).val("");
                }else if(data == "not"){
                                jQuery('.custom-warn').show();
                                setTimeout(showpanel1, 3000);
                               
                }
                            });
                         
    function showpanel1() { 
     jQuery('.custom-warn').fadeOut();
   jQuery('input[name="postcode"]').val("");
                                
 }                     
                        $.ajax({
                                showLoader: false,
                                url: "https://maps.googleapis.com/maps/api/geocode/json?address="+tyu+"&sensor=true",
                                data: "",
                                type: "POST"
                            }).done(function (data) {
                var arr = $.map(data, function(el) { return el });
                var arr1 = arr[0]['address_components'];
                 for(count = 0; count <= arr1.length -1; count++){
                   jui = arr1[count]['types'][0];
                   if(jui == 'locality' || jui == 'administrative_area_level_2'){ 
                   city = arr1[count]['long_name'];
                  }else if(jui == 'administrative_area_level_1'){
                    state = arr1[count]['long_name'];
                    short_state = arr1[count]['short_name'];
                  }else if(jui == 'country'){
                    country = arr1[count]['short_name'];
                  }
                 }
                jQuery("select[name='country_id'] option[value="+country+"]").prop('selected', 'selected');
                  jQuery("select[name='country_id']").trigger("change");
                jQuery("select[name='region_id'] option[value="+short_state+"]").prop('selected', 'selected');
                jQuery('input[name="region"]').val(state);
                jQuery('input[name="city"]').val(city);
                jQuery('input[name="city"]').keyup();
                            });
  
  }); 

});
  });

 