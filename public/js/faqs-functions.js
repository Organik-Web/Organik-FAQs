jQuery(function ($) {

	// Accordian show/hide
	$('.orgnk-faqs.type-accordion').each(function () {

		var question = $(this).find('.question'),
			answer = '.answer';

		// Hide all answers
		$(question).siblings(answer).hide();

		$(question).on('click', function () {

			// If the clicked question's answer is hidden
			if ($(this).siblings(answer).is(':hidden') === true) {

				// This question and its answer
				$(this).addClass('open').attr('aria-expanded', 'true');
				$(this).siblings(answer).slideDown(300).attr('aria-hidden', 'false');

				// Neighbouring questions and their answers
				$(this).parent().siblings().find(question).removeClass('open').attr('aria-expanded', 'false');
				$(this).parent().siblings().find(question).siblings(answer).slideUp(300).attr('aria-hidden', 'true');
			} 

			// If the clicked question's answer is visible
			else if ($(this).siblings(answer).is(':hidden') === false) {
				$(this).removeClass('open').attr('aria-expanded', 'false');
				$(this).siblings(answer).slideUp(300).attr('aria-hidden', 'true');
			}
		});
	});
});
