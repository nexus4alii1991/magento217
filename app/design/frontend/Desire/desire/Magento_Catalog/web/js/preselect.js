jQuery(document).ready(function () {
	timer = setInterval(function() {
		if (jQuery('select.swatch-select.finish').length > 0 ) {
			var finish = jQuery('select.swatch-select.finish>option:eq(1)');
		}else if (jQuery('select.super-attribute-select>option:eq(1)').length > 0 ){
			var finish = jQuery('select.super-attribute-select>option:eq(1)');
		}else {
			var finish = jQuery('select.super-attribute-select>option:eq(1)');
		}
		if(finish.length > 0 || jQuery('.swatch-attribute.color').length > 0) {
			if (finish.length > 0 && jQuery('.swatch-attribute.color').length == 0) {
				finishPreset(finish);
			}else if (finish.length == 0 && jQuery('.swatch-attribute.color').length > 0) {
				colorPreset();
			}else if (finish.length > 0 && jQuery('.swatch-attribute.color').length > 0){
				finishPreset(finish);
				colorPreset();
			}
			clearInterval(timer);
		}else{
		}
	},500);
});
function finishPreset(fnsh) {
	fnsh.attr('selected', true);
	jQuery('.swatch-select.finish').trigger('change');
}
function colorPreset() {
	jQuery('.swatch-attribute.color .swatch-attribute-selected-option').addClass('selected').attr('style',jQuery('.swatch-attribute-options > .swatch-option').attr('style'));
	setTimeout(function () {jQuery('.swatch-attribute-options > .swatch-option').trigger('click');},1000);
}