jQuery(document).ready(function($) {
	initialise_multipliers();

// 	$('ol.filters-duplicator').siblings('fieldset').children('button.constructor').eq(0).click(function() {
// 		initialise_multipliers();
// 	});

	function initialise_multipliers() {
		$('ol.filters-duplicator li').each(function() {
			var value = $('input[type="text"]', this).val();
			if($('div.content input[type="checkbox"]').size() == 0) {
				$('div.content', this).append('<label>Execute for each value in this parameter <input type="checkbox" value="' + value + '"/></label>');
			}
		});
		
		$.each(Symphony.Context.get('ds_filters'), function(index, value) {
			var field = $('ol.filters-duplicator input[type="checkbox"][value="' + value + '"]');
			field.attr('name', 'multipliers[' + index + ']');
			
			if(Symphony.Context.get('ds_multipliers')[index]) {
				field.attr('checked', 'checked');
			}
		});
	}
});