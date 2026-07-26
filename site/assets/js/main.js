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
    return typeof window.matchMedia === "function" && window.matchMedia("(max-width: 960px)").matches;
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
  var multiClose = document.getElementById("multi-org-close");

  function showStep(step) {
    if (!multiForm) return;
    var steps = multiForm.querySelectorAll(".multi-step");
    steps.forEach(function (el) {
      var match = String(el.getAttribute("data-step")) === String(step);
      el.hidden = !match;
      el.classList.toggle("is-active", match);
    });
  }

  function closeMultiDialog() {
    if (!multiDialog) return;
    if (typeof multiDialog.close === "function" && multiDialog.open) {
      multiDialog.close();
    } else {
      multiDialog.removeAttribute("open");
    }
    if (multiOpen) {
      multiOpen.setAttribute("aria-expanded", "false");
      multiOpen.focus();
    }
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
      if (multiOpen) multiOpen.setAttribute("aria-expanded", "false");
      showStep(1);
      if (multiForm) multiForm.reset();
    });

    multiDialog.addEventListener("click", function (event) {
      if (event.target === multiDialog) {
        closeMultiDialog();
      }
    });
  }

  if (multiClose) {
    multiClose.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();
      closeMultiDialog();
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
      if (nav && nav.classList.contains("is-open")) {
        setNav(false);
        return;
      }
      setPanel(false);
      if (multiDialog && multiDialog.open) {
        closeMultiDialog();
      }
    }
  });

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
        result.textContent = "Отлично! Вы отметили " + checked + " из " + total + " пунктов. У вас хороший бухгалтер. Если хотите сверить детали — посмотрите услуги или напишите Карине.";
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
      var late = answers[3] === "often" || answers[3] === "sometimes";
      var wantHelp = answers[4] === "yes" || answers[4] === "consultation";
      if (late || wantHelp) {
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
