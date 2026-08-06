(function () {
  "use strict";

  var header = document.getElementById("site-header");
  var nav = document.getElementById("site-nav");
  var toggle = document.getElementById("nav-toggle");
  var closeBtn = document.getElementById("nav-close");
  var overlay = document.getElementById("nav-overlay");

  /* New mega header owns mobile sheet via nav-mega.js */
  if (document.querySelector(".site-header--mega")) {
    nav = null;
    toggle = null;
    closeBtn = null;
    overlay = null;
  }
  var scrollY = 0;
  var lastFocused = null;

  function getFocusable(container) {
    if (!container) return [];
    return Array.prototype.slice.call(
      container.querySelectorAll(
        'a[href], button:not([disabled]), textarea, input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
      )
    ).filter(function (el) {
      return !el.hasAttribute("hidden") && el.getAttribute("aria-hidden") !== "true";
    });
  }

  function isMobileNav() {
    return typeof window.matchMedia === "function" && window.matchMedia("(max-width: 1100px)").matches;
  }

  function setNavHiddenState(open) {
    if (!nav) return;
    if (!isMobileNav()) {
      nav.setAttribute("aria-hidden", "false");
      if ("inert" in nav) nav.inert = false;
      return;
    }
    nav.setAttribute("aria-hidden", open ? "false" : "true");
    if ("inert" in nav) nav.inert = !open;
  }

  function setNav(open, options) {
    if (!nav || !toggle) return;
    var opts = options || {};
    var restoreFocus = opts.restoreFocus !== false;

    if (open) {
      scrollY = window.scrollY || window.pageYOffset || 0;
      lastFocused = document.activeElement;
      nav.classList.add("is-open");
      document.body.classList.add("nav-open");
      document.body.style.top = "-" + scrollY + "px";
      toggle.setAttribute("aria-expanded", "true");
      toggle.setAttribute("aria-label", "Закрыть меню");
      setNavHiddenState(true);
      if (overlay) overlay.removeAttribute("hidden");
      if (isMobileNav()) {
        var focusables = getFocusable(nav);
        if (focusables.length) focusables[0].focus();
      }
    } else {
      var wasOpen = nav.classList.contains("is-open");
      nav.classList.remove("is-open");
      document.body.classList.remove("nav-open");
      document.body.style.top = "";
      if (wasOpen) window.scrollTo(0, scrollY);
      toggle.setAttribute("aria-expanded", "false");
      toggle.setAttribute("aria-label", "Открыть меню");
      setNavHiddenState(false);
      if (overlay) overlay.setAttribute("hidden", "");
      if (restoreFocus && wasOpen) {
        if (lastFocused && typeof lastFocused.focus === "function") {
          lastFocused.focus();
        } else {
          toggle.focus();
        }
      }
    }
  }

  if (toggle) {
    toggle.addEventListener("click", function () {
      setNav(!nav.classList.contains("is-open"));
    });
  }
  if (closeBtn) closeBtn.addEventListener("click", function () { setNav(false); });
  if (overlay) overlay.addEventListener("click", function () { setNav(false); });

  if (nav) {
    nav.addEventListener("click", function (event) {
      var target = event.target;
      if (!(target instanceof HTMLElement)) return;

      var accordion = target.closest(".site-nav__accordion");
      if (accordion instanceof HTMLElement) {
        var panelId = accordion.getAttribute("aria-controls");
        var panel = panelId ? document.getElementById(panelId) : null;
        var expanded = accordion.getAttribute("aria-expanded") === "true";
        accordion.setAttribute("aria-expanded", expanded ? "false" : "true");
        if (panel) {
          if (expanded) panel.setAttribute("hidden", "");
          else panel.removeAttribute("hidden");
        }
        return;
      }

      var link = target.closest("a[href]");
      if (link && nav.classList.contains("is-open")) {
        setNav(false);
      }
    });

    nav.addEventListener("keydown", function (event) {
      if (!nav.classList.contains("is-open") || event.key !== "Tab") return;
      var focusables = getFocusable(nav);
      if (!focusables.length) return;
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }

  if (header) {
    window.addEventListener("scroll", function () {
      header.classList.toggle("is-scrolled", window.scrollY > 20);
    }, { passive: true });
  }

  setNavHiddenState(false);
  window.addEventListener("resize", function () {
    if (!isMobileNav() && nav && nav.classList.contains("is-open")) {
      setNav(false, { restoreFocus: false });
    } else {
      setNavHiddenState(nav && nav.classList.contains("is-open"));
    }
  });

  var fab = document.getElementById("contact-fab");
  var panel = document.getElementById("contact-panel");
  var panelClose = document.getElementById("contact-panel-close");
  var panelOverlay = document.getElementById("contact-panel-overlay");
  var panelLastTrigger = null;

  function setPanelBackgroundInert(inertOn) {
    Array.prototype.forEach.call(document.body.children, function (el) {
      if (el === panel || el === panelOverlay) return;
      if ("inert" in el) el.inert = inertOn;
    });
  }

  function setPanel(open, trigger) {
    if (!panel || !fab) return;
    fab.setAttribute("aria-expanded", open ? "true" : "false");
    if (open) {
      if (trigger) panelLastTrigger = trigger;
      panel.removeAttribute("hidden");
      panel.inert = false;
      setPanelBackgroundInert(true);
      if (panelOverlay) panelOverlay.removeAttribute("hidden");
      var focusables = getFocusable(panel);
      if (focusables.length) focusables[0].focus();
    } else {
      panel.setAttribute("hidden", "");
      panel.inert = true;
      setPanelBackgroundInert(false);
      if (panelOverlay) panelOverlay.setAttribute("hidden", "");
      if (panelLastTrigger && typeof panelLastTrigger.focus === "function") {
        panelLastTrigger.focus();
      }
      panelLastTrigger = null;
    }
  }

  if (fab) {
    fab.addEventListener("click", function () {
      if (fab.classList.contains("is-deferred")) return;
      var open = fab.getAttribute("aria-expanded") === "true";
      setPanel(!open, fab);
    });
  }
  if (panelClose) panelClose.addEventListener("click", function () { setPanel(false); });
  if (panelOverlay) panelOverlay.addEventListener("click", function () { setPanel(false); });
  if (panel) {
    panel.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        setPanel(false);
        return;
      }
      if (event.key !== "Tab") return;
      var focusables = getFocusable(panel);
      if (!focusables.length) return;
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }

  document.querySelectorAll("[data-contact-panel-open]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      setPanel(true, btn);
    });
  });

  /* Mobile: defer mandarin FAB while primary hero CTA is in view */
  (function initFabHeroDeferral() {
    if (!fab) return;
    var heroCta = document.querySelector(".hero__actions .btn--accent, .hero__actions .btn, .hero__actions [data-contact-panel-open]");
    if (!heroCta) return;

    var mobileMq =
      typeof window.matchMedia === "function"
        ? window.matchMedia("(max-width: 1100px)")
        : null;
    var heroInView = false;
    var observer = null;

    function applyFabDeferral() {
      var defer = !!(mobileMq && mobileMq.matches && heroInView);
      fab.classList.toggle("is-deferred", defer);
      fab.setAttribute("aria-hidden", defer ? "true" : "false");
      if (defer) {
        fab.setAttribute("tabindex", "-1");
        setPanel(false);
      } else {
        fab.removeAttribute("tabindex");
      }
    }

    function setHeroInView(next) {
      heroInView = !!next;
      applyFabDeferral();
    }

    function bindObserver() {
      if (!("IntersectionObserver" in window)) {
        setHeroInView(true);
        return;
      }
      if (observer) observer.disconnect();
      observer = new IntersectionObserver(
        function (entries) {
          var entry = entries[0];
          if (!entry) return;
          setHeroInView(entry.isIntersecting);
        },
        { root: null, threshold: 0, rootMargin: "0px" }
      );
      observer.observe(heroCta);
    }

    function syncMode() {
      if (mobileMq && mobileMq.matches) {
        bindObserver();
      } else {
        if (observer) {
          observer.disconnect();
          observer = null;
        }
        setHeroInView(false);
      }
      applyFabDeferral();
    }

    syncMode();
    if (mobileMq) {
      if (typeof mobileMq.addEventListener === "function") {
        mobileMq.addEventListener("change", syncMode);
      } else if (typeof mobileMq.addListener === "function") {
        mobileMq.addListener(syncMode);
      }
    }
  })();

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      if (nav && nav.classList.contains("is-open")) {
        setNav(false);
        return;
      }
      if (panel && !panel.hasAttribute("hidden")) {
        setPanel(false);
      }
    }
  });

  /* Homepage reviews strip: snap + drag + expand */
  (function initReviewsStrip() {
    var strip = document.querySelector("[data-reviews-strip]");
    if (!strip) return;
    var track = strip.querySelector("[data-reviews-track]");
    var prevBtn = document.querySelector("[data-reviews-prev]");
    var nextBtn = document.querySelector("[data-reviews-next]");
    var controls = document.querySelector(".reviews-strip__controls");
    var desktopMq =
      typeof window.matchMedia === "function"
        ? window.matchMedia("(min-width: 768px)")
        : null;
    var reduceMotion =
      typeof window.matchMedia === "function" &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function isDesktop() {
      return !desktopMq || desktopMq.matches;
    }

    function updateControls() {
      if (!controls || !prevBtn || !nextBtn) return;
      if (!isDesktop()) {
        controls.setAttribute("hidden", "");
        return;
      }
      controls.removeAttribute("hidden");
      var max = strip.scrollWidth - strip.clientWidth;
      prevBtn.disabled = strip.scrollLeft <= 4;
      nextBtn.disabled = strip.scrollLeft >= max - 4;
    }

    function scrollByDir(dir) {
      var amount = Math.max(240, Math.floor(strip.clientWidth * 0.8));
      strip.scrollBy({
        left: dir * amount,
        behavior: reduceMotion ? "auto" : "smooth",
      });
    }

    if (prevBtn) prevBtn.addEventListener("click", function () { scrollByDir(-1); });
    if (nextBtn) nextBtn.addEventListener("click", function () { scrollByDir(1); });
    strip.addEventListener("scroll", updateControls, { passive: true });
    window.addEventListener("resize", updateControls);

    /* Drag on fine pointer desktop */
    var dragging = false;
    var startX = 0;
    var startScroll = 0;
    var pointerId = null;

    function onPointerDown(event) {
      if (!isDesktop() || event.pointerType === "touch") return;
      if (event.target.closest("button, a")) return;
      dragging = true;
      pointerId = event.pointerId;
      startX = event.clientX;
      startScroll = strip.scrollLeft;
      strip.classList.add("is-dragging");
      try { strip.setPointerCapture(pointerId); } catch (err) { /* ignore */ }
    }

    function onPointerMove(event) {
      if (!dragging) return;
      var dx = event.clientX - startX;
      strip.scrollLeft = startScroll - dx;
    }

    function onPointerUp() {
      if (!dragging) return;
      dragging = false;
      strip.classList.remove("is-dragging");
      pointerId = null;
      updateControls();
    }

    strip.addEventListener("pointerdown", onPointerDown);
    strip.addEventListener("pointermove", onPointerMove);
    strip.addEventListener("pointerup", onPointerUp);
    strip.addEventListener("pointercancel", onPointerUp);

    strip.addEventListener("keydown", function (event) {
      if (!isDesktop()) return;
      if (event.key === "ArrowRight") {
        event.preventDefault();
        scrollByDir(1);
      } else if (event.key === "ArrowLeft") {
        event.preventDefault();
        scrollByDir(-1);
      }
    });

    strip.addEventListener("click", function (event) {
      var btn = event.target.closest("[data-review-expand]");
      if (!btn) return;
      var tile = btn.closest("[data-review-tile]");
      if (!tile) return;
      var preview = tile.querySelector("[data-review-preview]");
      var full = tile.querySelector("[data-review-full]");
      var expanded = tile.classList.toggle("is-expanded");
      btn.setAttribute("aria-expanded", expanded ? "true" : "false");
      btn.textContent = expanded ? "Свернуть" : "Прочитать полностью";
      if (preview) preview.hidden = expanded;
      if (full) full.hidden = !expanded;
      updateControls();
    });

    updateControls();
    if (desktopMq) {
      if (typeof desktopMq.addEventListener === "function") {
        desktopMq.addEventListener("change", updateControls);
      } else if (typeof desktopMq.addListener === "function") {
        desktopMq.addListener(updateControls);
      }
    }

    /* silence unused */
    void track;
  })();

  // Interactives on page (may be several)
  document.querySelectorAll("[data-checklist]").forEach(initChecklistRoot);
  document.querySelectorAll("[data-quiz]").forEach(initQuizRoot);

  function initChecklistRoot(root) {
    var items = root.querySelectorAll(".ix-check__item input[type='checkbox']");
    var result = root.querySelector("[data-checklist-result]");
    if (!items.length || !result) return;

    function render() {
      var checked = 0;
      items.forEach(function (input) {
        if (input.checked) checked += 1;
      });
      var total = items.length;
      if (checked === 0) {
        result.hidden = true;
        result.textContent = "";
        return;
      }
      result.hidden = false;
      var pct = Math.round((checked / total) * 100);
      if (pct >= 80) {
        result.textContent = "Вы отметили " + checked + " из " + total + " пунктов — по этим признакам сопровождение выглядит крепким. Это предварительный ориентир, а не полная диагностика: если хочется сверить детали, посмотрите услуги или напишите Карине.";
      } else if (pct >= 50) {
        result.textContent = "Вы отметили " + checked + " из " + total + " пунктов. Есть над чем поработать. Имеет смысл посмотреть услуги сопровождения.";
      } else {
        result.textContent = "Вы отметили только " + checked + " из " + total + " пунктов. Стоит внимательнее посмотреть на качество сопровождения — начните с услуг.";
      }
    }

    items.forEach(function (input) {
      input.addEventListener("change", render);
    });
    render();
  }

  function initQuizRoot(root) {
    var dataEl = root.querySelector(".quiz-data");
    if (!dataEl) return;
    var questions;
    try {
      questions = JSON.parse(dataEl.textContent || "[]");
    } catch (err) {
      return;
    }
    if (!questions.length) return;

    var content = root.querySelector("[data-quiz-content]");
    var result = root.querySelector("[data-quiz-result]");
    var progress = root.querySelector("[data-quiz-progress]");
    var progressText = root.querySelector("[data-quiz-progress-text]");
    if (!content || !result) return;

    var step = 0;
    var answers = [];

    function finish() {
      content.hidden = true;
      result.hidden = false;
      var title = result.querySelector("[data-quiz-result-title]");
      var text = result.querySelector("[data-quiz-result-text]");
      /* Каждый вопрос даёт равный голос: неуверенность в НДС/режиме,
         кадровая нагрузка, просрочки и желание оптимизации — все пять
         ответов учитываются, а не только последние два. */
      var signals = 0;
      if (answers[0] === "unsure") signals++;
      if (answers[1] === "unknown") signals++;
      if (answers[2] === "6-15" || answers[2] === "15+") signals++;
      if (answers[3] === "often" || answers[3] === "sometimes") signals++;
      if (answers[4] === "yes" || answers[4] === "consultation") signals++;
      if (signals >= 2) {
        if (title) title.textContent = "Имеет смысл разобрать сопровождение";
        if (text) {
          text.textContent = "По ответам видно, что полезно настроить учёт, сроки и налоги. Дальше — к услугам или короткому разговору с Кариной.";
        }
      } else {
        if (title) title.textContent = "База у вас уже есть";
        if (text) {
          text.textContent = "Похоже, дисциплина в порядке. Услуги помогут проверить, нет ли точек роста по налогам.";
        }
      }
    }

    function render() {
      if (step >= questions.length) {
        finish();
        return;
      }
      content.hidden = false;
      result.hidden = true;
      var q = questions[step];
      var pct = ((step + 1) / questions.length) * 100;
      if (progress) progress.style.setProperty("--progress", pct + "%");
      if (progressText) progressText.textContent = "Вопрос " + (step + 1) + " из " + questions.length;
      content.innerHTML =
        '<h3 class="ix-quiz__question">' + q.question + "</h3>" +
        '<div class="ix-quiz__options" role="group">' +
        q.options.map(function (opt, idx) {
          var selected = answers[step] === opt.value ? " is-selected" : "";
          return '<button type="button" class="ix-quiz__option' + selected + '" data-value="' + opt.value + '" data-index="' + idx + '">' + opt.text + "</button>";
        }).join("") +
        "</div>" +
        '<div class="ix-quiz__nav">' +
        (step > 0 ? '<button type="button" class="btn btn--ghost" data-quiz-prev>Назад</button>' : "<span></span>") +
        '<button type="button" class="btn btn--accent" data-quiz-next' + (answers[step] ? "" : " disabled") + ">" +
        (step === questions.length - 1 ? "Завершить" : "Далее") +
        "</button></div>";
    }

    content.addEventListener("click", function (event) {
      var target = event.target;
      if (!(target instanceof HTMLElement)) return;
      if (target.classList.contains("ix-quiz__option")) {
        content.querySelectorAll(".ix-quiz__option").forEach(function (el) {
          el.classList.remove("is-selected");
        });
        target.classList.add("is-selected");
        var next = content.querySelector("[data-quiz-next]");
        if (next) next.disabled = false;
      }
      if (target.hasAttribute("data-quiz-next")) {
        var selected = content.querySelector(".ix-quiz__option.is-selected");
        if (!selected) return;
        answers[step] = selected.getAttribute("data-value");
        step += 1;
        render();
      }
      if (target.hasAttribute("data-quiz-prev")) {
        step = Math.max(0, step - 1);
        render();
      }
    });

    render();
  }
})();


