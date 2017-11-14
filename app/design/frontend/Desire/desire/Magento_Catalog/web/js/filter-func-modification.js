require(['jquery'],function($){
	jQuery(document).ready(function () {
		if (jQuery('.filter-current')) {
			if (jQuery('.filter-current .item').length == 1) {
				if (jQuery('.filter-current .item a.action.remove').attr('title').search('Collections') > -1) {
					jQuery('.filter-current').addClass('algn-right');
					jQuery('#narrow-by-list').addClass('algn-left');
				}
			}
			jQuery('.filter-current .item .action.remove').click(function () {
				if (jQuery(this).attr('title').search('Brand') > -1) {
					localStorage.setItem('clearedFilter','bam');
				}else if (jQuery(this).attr('title').search('Collections') > -1){
					localStorage.setItem('clearedFilter','cl');
				}
			});
		}
		if (localStorage.getItem('clearedFilter')) {
			var locStg = localStorage.getItem('clearedFilter');
			if (locStg == 'cl') {
				setTimeout(function() {
					if (jQuery('.filter-options-content.collections').length > 0 ) {
						genClic('collections',locStg);
					}
				},1700);
				localStorage.removeItem('clearedFilter');
			}else if(locStg == 'bam'){
				setTimeout(function() {
					if (jQuery('.filter-options-content.brandmodel').length > 0 ) {
						genClic('brandmodel',locStg);
					}
				},1700);
				localStorage.removeItem('clearedFilter');
			}
		}
	});
});

function genClic(elmnt,lstg) {
	/*alert(lstg);*/
	jQuery('.filter-options-content.'+elmnt).siblings('.filter-options-title').trigger('click');
	jQuery('.filter-options-content.'+elmnt).siblings('.filter-options-title').trigger('click');
	jQuery('.filter-options-content.'+elmnt).siblings('.filter-options-title').trigger('click');
}