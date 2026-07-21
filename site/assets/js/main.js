(function () {
  "use strict";

  var header = document.getElementById("site-header");
  var nav = document.getElementById("site-nav");
  var toggle = document.getElementById("nav-toggle");
  var closeBtn = document.getElementById("nav-close");
  var overlay = document.getElementById("nav-overlay");

  function setNav(open) {
    if (!nav || !toggle) return;
    nav.classList.toggle("is-open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    if (overlay) {
      if (open) overlay.removeAttribute("hidden");
      else overlay.setAttribute("hidden", "");
    }
    document.body.style.overflow = open ? "hidden" : "";
  }

  if (toggle) {
    toggle.addEventListener("click", function () {
      setNav(!nav.classList.contains("is-open"));
    });
  }
  if (closeBtn) closeBtn.addEventListener("click", function () { setNav(false); });
  if (overlay) overlay.addEventListener("click", function () { setNav(false); });

  if (header) {
    window.addEventListener("scroll", function () {
      header.classList.toggle("is-scrolled", window.scrollY > 20);
    }, { passive: true });
  }

  var fab = document.getElementById("contact-fab");
  var panel = document.getElementById("contact-panel");
  var panelClose = document.getElementById("contact-panel-close");
  var panelOverlay = document.getElementById("contact-panel-overlay");

  function setPanel(open) {
    if (!panel || !fab) return;
    fab.setAttribute("aria-expanded", open ? "true" : "false");
    if (open) {
      panel.removeAttribute("hidden");
      if (panelOverlay) panelOverlay.removeAttribute("hidden");
    } else {
      panel.setAttribute("hidden", "");
      if (panelOverlay) panelOverlay.setAttribute("hidden", "");
    }
  }

  if (fab) {
    fab.addEventListener("click", function () {
      var open = fab.getAttribute("aria-expanded") === "true";
      setPanel(!open);
    });
  }
  if (panelClose) panelClose.addEventListener("click", function () { setPanel(false); });
  if (panelOverlay) panelOverlay.addEventListener("click", function () { setPanel(false); });

  var multiOpen = document.getElementById("multi-org-open");
  var multiDialog = document.getElementById("multi-org-panel");
  var multiForm = document.getElementById("multi-org-form");

  function showStep(step) {
    if (!multiForm) return;
    var steps = multiForm.querySelectorAll(".multi-step");
    steps.forEach(function (el) {
      var match = String(el.getAttribute("data-step")) === String(step);
      el.hidden = !match;
      el.classList.toggle("is-active", match);
    });
  }

  if (multiOpen && multiDialog) {
    multiOpen.addEventListener("click", function () {
      multiOpen.setAttribute("aria-expanded", "true");
      if (typeof multiDialog.showModal === "function") {
        multiDialog.showModal();
      } else {
        multiDialog.setAttribute("open", "");
      }
      showStep(1);
    });

    multiDialog.addEventListener("close", function () {
      multiOpen.setAttribute("aria-expanded", "false");
      showStep(1);
      if (multiForm) multiForm.reset();
    });
  }

  if (multiForm) {
    multiForm.addEventListener("click", function (event) {
      var target = event.target;
      if (!(target instanceof HTMLElement)) return;

      if (target.classList.contains("multi-next")) {
        var next = target.getAttribute("data-next");
        var currentStep = target.closest(".multi-step");
        if (!currentStep || !next) return;

        var requiredRadios = currentStep.querySelectorAll('input[type="radio"][required]');
        if (requiredRadios.length) {
          var name = requiredRadios[0].getAttribute("name");
          var checked = currentStep.querySelector('input[name="' + name + '"]:checked');
          if (!checked) {
            requiredRadios[0].focus();
            return;
          }
        }
        showStep(next);
      }

      if (target.classList.contains("multi-back")) {
        var back = target.getAttribute("data-back");
        if (back) showStep(back);
      }
    });
  }

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      setNav(false);
      setPanel(false);
    }
  });
})();
