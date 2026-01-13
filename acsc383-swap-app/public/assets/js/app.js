// public/assets/js/app.js
/* global bootstrap */
(function () {
  "use strict";

  $(function () {
    // Auto-hide flash alerts
    const $alerts = $(".alert[data-autohide='1']");
    if ($alerts.length) setTimeout(() => $alerts.fadeOut(220), 3200);

    // Tooltips
    const tipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tipEls.forEach(el => new bootstrap.Tooltip(el));

    // Tiny UX: smooth anchor jumps
    $("a[href^='#']").on("click", function (e) {
      const target = $(this.getAttribute("href"));
      if (target.length) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: target.offset().top - 70 }, 240);
      }
    });
  });
})();
