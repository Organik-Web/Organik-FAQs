jQuery(function ($) {

	// Accordian show/hide
	$('.orgnk-faqs.type-accordion').each( function() {

		const question = $(this).find('.question');
		const answer = '.answer';

		// Hide all answers
		$(question).siblings(answer).hide();

		$(question).on( 'click', function() {

			// If the clicked question's answer is hidden
			if ( $(this).siblings(answer).is(':hidden') === true ) {

				// This question and its answer
				$(this).parent().addClass('open');
				$(this).attr('aria-expanded', 'true');
				$(this).siblings(answer).slideDown(300).attr('aria-hidden', 'false');

				// Neighbouring questions and their answers
				const neighbours = $(this).parent().siblings();

				neighbours.removeClass('open');
				neighbours.find(question).attr('aria-expanded', 'false');
				neighbours.find(question).siblings(answer).slideUp(300).attr('aria-hidden', 'true');
			} 

			// If the clicked question's answer is visible
			else if ( $(this).siblings(answer).is(':hidden') === false ) {
				$(this).parent().removeClass('open');
				$(this).attr('aria-expanded', 'false');
				$(this).siblings(answer).slideUp(300).attr('aria-hidden', 'true');
			}
		});
	});
});
