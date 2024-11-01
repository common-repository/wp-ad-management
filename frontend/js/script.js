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

(function($){

	$(document).ready(function(){

		if ( $('#a24pSuccessProRedirect').length ) {
			var getRedirectUrl = $('#a24p_payment_url').val();
			setTimeout(function() {
				window.location.replace(getRedirectUrl);
			}, 2000);
		}

		if ( $('#a24pSuccessProAgencyRedirect').length ) {
			var getAgencyRedirectUrl = $('#a24p_payment_agency_url').val();
			setTimeout(function() {
				window.location.replace(getAgencyRedirectUrl);
			}, 2000);
		}

		var a24pProItem = $('.a24pProItem');
		a24pProItem.each(function() {
			if ( $(this).data('animation') != null && $(this).data('animation') !== 'none' ) {
				$(this).addClass('a24pHidden').viewportChecker({
					// Class to add to the elements when they are visible
					classToAdd: 'animated ' + $(this).data('animation'),
					offset: 100,
					repeat: false,
					callbackFunction: function(elem, action){}
				});
			}
		});

	});

})(jQuery);