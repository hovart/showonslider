$(document).ready(function() {
	var is_new_tab = false;
	$("#product-tab-content-ModuleShowonslider").on('loaded', function(){
			$('#show_on_slider').iphoneStyle({ 
				checkedLabel:'Show',
				uncheckedLabel: 'Hide'

				 });
			
	})
	
	if($('#featured-products_block_center').length>0){
		$('#bxslider').bxSlider({auto:true});
	}
	
});