(function () {
  "use strict";

  var STORAGE_KEY = "palette-v1";
  var body = document.body;
  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function loadState() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}") || {};
    } catch (e) {
      return {};
    }
  }

  function saveState(partial) {
    var next = Object.assign(loadState(), partial);
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
    } catch (e) {
      /* ignore */
    }
  }

  var state = loadState();
  var palette = state.palette || "warm-satin";
  var motion = state.motion !== false;
  var megaOpen = !!state.megaOpen;
  var ctaMode = state.ctaMode || "action";

  if (reduced) motion = false;

  function applyPalette(id) {
    palette = id;
    body.setAttribute("data-palette", id);
    document.querySelectorAll("[data-palette-set]").forEach(function (btn) {
      var on = btn.getAttribute("data-palette-set") === id;
      btn.setAttribute("aria-pressed", on ? "true" : "false");
      btn.classList.toggle("is-active", on);
    });
    var theme = document.querySelector('meta[name="theme-color"]');
    if (theme) {
      var page = getComputedStyle(body).getPropertyValue("--color-page").trim();
      if (page) theme.setAttribute("content", page);
    }
    saveState({ palette: id });
  }

  function applyMotion(on) {
    motion = !!on && !reduced;
    body.classList.toggle("is-motion-off", !motion);
    document.querySelectorAll("[data-motion-set]").forEach(function (btn) {
      var val = btn.getAttribute("data-motion-set") === "on";
      btn.setAttribute("aria-pressed", val === motion ? "true" : "false");
      btn.classList.toggle("is-active", val === motion);
    });
    saveState({ motion: motion });
  }

  function applyMega(open) {
    megaOpen = !!open;
    var header = document.getElementById("site-header");
    if (header) header.classList.toggle("is-mega-open", megaOpen);
    document.querySelectorAll("[data-mega-set]").forEach(function (btn) {
      var val = btn.getAttribute("data-mega-set") === "open";
      btn.setAttribute("aria-pressed", val === megaOpen ? "true" : "false");
      btn.classList.toggle("is-active", val === megaOpen);
    });
    document.querySelectorAll("[data-mega-hover]").forEach(function (el) {
      el.setAttribute("aria-expanded", megaOpen ? "true" : "false");
    });
    saveState({ megaOpen: megaOpen });
  }

  function applyCta(mode) {
    ctaMode = mode === "warm" ? "warm" : "action";
    body.setAttribute("data-cta", ctaMode);
    document.querySelectorAll("[data-cta-set]").forEach(function (btn) {
      var on = btn.getAttribute("data-cta-set") === ctaMode;
      btn.setAttribute("aria-pressed", on ? "true" : "false");
      btn.classList.toggle("is-active", on);
    });
    saveState({ ctaMode: ctaMode });
  }

  document.querySelectorAll("[data-palette-set]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      applyPalette(btn.getAttribute("data-palette-set"));
    });
  });
  document.querySelectorAll("[data-motion-set]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      applyMotion(btn.getAttribute("data-motion-set") === "on");
    });
  });
  document.querySelectorAll("[data-mega-set]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      applyMega(btn.getAttribute("data-mega-set") === "open");
    });
  });
  document.querySelectorAll("[data-cta-set]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      applyCta(btn.getAttribute("data-cta-set"));
    });
  });

  var ambient = document.querySelector(".ambient");
  if (ambient && !reduced) {
    var ticking = false;
    window.addEventListener(
      "scroll",
      function () {
        if (!motion || ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
          var y = window.scrollY || 0;
          ambient.style.setProperty("--scroll-shift", String(Math.min(40, y * 0.04)));
          ticking = false;
        });
      },
      { passive: true }
    );
  }

  /* Demo interactive selected state */
  document.querySelectorAll(".ix-option").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var group = btn.closest(".ix-options");
      if (!group) return;
      group.querySelectorAll(".ix-option").forEach(function (b) {
        b.classList.remove("is-selected");
        b.setAttribute("aria-pressed", "false");
      });
      btn.classList.add("is-selected");
      btn.setAttribute("aria-pressed", "true");
    });
  });

  applyPalette(palette);
  applyMotion(motion);
  applyMega(megaOpen);
  applyCta(ctaMode);
})();
