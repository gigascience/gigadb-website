import { trapFocus } from './trap-focus.js';

export function initMobileNavigation() {
  const $mobileNav = $("#mobileNavigation");
  const $toggleButton = $(".navbar-toggle");
  const $closeButton = $(".mobile-navigation__close");
  const $body = $("body");

  function closeNav() {
    $mobileNav.removeClass("is-visible");
    $body.css("overflow", "");
  }

  $toggleButton.on("click", function () {
    $mobileNav.addClass("is-visible");
    $body.css("overflow", "hidden");
    trapFocus($mobileNav);
  });

  $closeButton.on("click", function () {
    closeNav();
  });

  $(document).on("keydown", function (event) {
    if (event.key === "Escape" && $mobileNav.hasClass("is-visible")) {
      closeNav();
    }
  });

  $mobileNav.on("click", "button, a", function (event) {
    if (event.target === this) {
      closeNav();
    }
  });
}
