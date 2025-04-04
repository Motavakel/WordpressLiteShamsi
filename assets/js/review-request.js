(function ($) {
  $(document).ready(function () {
    $("#molsc-dismiss-review").on("click", function (e) {
      e.preventDefault();

      $.post(molsc_ajax.ajaxurl, { action: "molsc_dismiss_review" })
        .done(function () {
          $("#molsc-review-notice").fadeOut();
        })
        .fail(function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
        });
    });
    $("#molsc-give-feedback").on("click", function () {
      $.post(molsc_ajax.ajaxurl, {
        action: "molsc_dismiss_review",
        molsc_feedback_given: "yes",
      })
        .done(function () {
          $("#molsc-review-notice").fadeOut();
        })
        .fail(function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
        });
    });
  });
})(jQuery);
