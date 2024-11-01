function selectBillingModel()
{
	(function($){

		var radioModel = $('input[name="ad_model"]:checked');
		var radioValues = $(".a24pProInputsValues");
		var radioValuesCPC = $(".a24pProInputsValuesCPC");
		var radioValuesCPM = $(".a24pProInputsValuesCPM");
		var radioValuesCPD = $(".a24pProInputsValuesCPD");

		$('input[name="ad_limit_cpc"]').prop('checked', false);
		$('input[name="ad_limit_cpm"]').prop('checked', false);
		$('input[name="ad_limit_cpd"]').prop('checked', false);

		$('input[name="ad_model"]').click(function() {
			$('.a24pInputInnerModel').removeClass('a24pSelected');
		});

		radioValues.slideUp();

		if ( radioModel.val() == 'cpc' ) {
			radioValuesCPC.slideDown();
			radioModel.parent(1).addClass('a24pSelected');
		} else if ( radioModel.val() == 'cpm' ) {
			radioValuesCPM.slideDown();
			radioModel.parent(1).addClass('a24pSelected');
		} else if ( radioModel.val() == 'cpd' ) {
			radioValuesCPD.slideDown();
			radioModel.parent(1).addClass('a24pSelected');
		}

	})(jQuery);
}