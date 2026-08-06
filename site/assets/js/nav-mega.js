(function () {
  "use strict";

  var header = document.getElementById("site-header");
  if (!header || !header.classList.contains("site-header--mega")) return;

  var canHover = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
  var megaId = null;
  var leaveTimer = null;
  var poster = document.getElementById("site-nav-mobile");
  var toggle = document.getElementById("nav-toggle");
  var closeBtn = document.getElementById("nav-close");
  var overlay = document.getElementById("nav-overlay");
  var mobileOpen = false;
  var savedY = 0;
  var closeTimer = null;
  var closeGen = 0;
  var mobileOpenTrigger = null;
  var desktopNavMq =
    typeof window.matchMedia === "function"
      ? window.matchMedia("(min-width: 1101px)")
      : null;
  var reducedMotion =
    typeof window.matchMedia === "function" &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function getFocusable(container) {
    if (!container) return [];
    return Array.prototype.slice
      .call(
        container.querySelectorAll(
          'a[href], button:not([disabled]), textarea, input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )
      )
      .filter(function (el) {
        return !el.hasAttribute("hidden") && el.getAttribute("aria-hidden") !== "true";
      });
  }

  function setBackgroundInert(inertOn) {
    Array.prototype.forEach.call(document.body.children, function (el) {
      if (el === poster || el === overlay) return;
      if ("inert" in el) el.inert = inertOn;
    });
  }

  function setExpanded(id) {
    header.querySelectorAll("[data-mega-hover]").forEach(function (el) {
      el.setAttribute(
        "aria-expanded",
        el.getAttribute("data-mega-hover") === id ? "true" : "false"
      );
    });
  }

  function showPanel(id) {
    header.querySelectorAll(".mega__panel").forEach(function (panel) {
      var on = panel.getAttribute("data-mega-panel") === id;
      if (on) panel.removeAttribute("hidden");
      else panel.setAttribute("hidden", "");
    });
  }

  function openMega(id) {
    clearTimeout(leaveTimer);
    megaId = id;
    header.classList.add("is-mega-open");
    showPanel(id);
    setExpanded(id);
  }

  function closeMega() {
    clearTimeout(leaveTimer);
    megaId = null;
    header.classList.remove("is-mega-open");
    setExpanded(null);
    header.querySelectorAll(".mega__panel").forEach(function (panel) {
      panel.setAttribute("hidden", "");
    });
  }

  function scheduleClose() {
    clearTimeout(leaveTimer);
    leaveTimer = setTimeout(closeMega, 160);
  }

  if (canHover) {
    header.querySelectorAll("[data-mega-hover]").forEach(function (el) {
      el.addEventListener("mouseenter", function () {
        openMega(el.getAttribute("data-mega-hover"));
      });
      el.addEventListener("focus", function () {
        openMega(el.getAttribute("data-mega-hover"));
      });
    });
    header.addEventListener("mouseleave", scheduleClose);
    header.addEventListener("mouseenter", function () {
      clearTimeout(leaveTimer);
    });
    header.addEventListener("focusout", function (e) {
      if (!header.contains(e.relatedTarget)) scheduleClose();
    });
  }

  header.querySelectorAll('[data-mega-hover="contacts"]').forEach(function (el) {
    el.addEventListener("click", function (e) {
      e.preventDefault();
      if (canHover) {
        openMega("contacts");
        return;
      }
      if (megaId === "contacts") closeMega();
      else openMega("contacts");
    });
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      if (mobileOpen) closeMobile();
      else if (headerSearchOut && !headerSearchOut.hasAttribute("hidden")) {
        closeHeaderSearch();
        if (headerSearch) headerSearch.blur();
      } else if (megaId) closeMega();
    }
  });

  /* Keyword search in header */
  var searchWrap = header.querySelector(".site-header__search");
  var headerSearch = document.getElementById("header-search");
  var headerSearchOut = document.getElementById("header-search-out");
  var searchIndex = null;
  var searchPromise = null;
  var indexUrl =
    (searchWrap && searchWrap.getAttribute("data-search-index")) ||
    "/assets/data/navigation-index.json";

  function loadSearchIndex() {
    if (searchIndex) return Promise.resolve(searchIndex);
    if (searchPromise) return searchPromise;
    searchPromise = fetch(indexUrl)
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        searchIndex = data;
        return data;
      })
      .catch(function () {
        searchIndex = { version: 1, minChars: 2, items: [] };
        return searchIndex;
      });
    return searchPromise;
  }

  function matchSearchItems(query, data) {
    var q = (query || "").trim().toLowerCase();
    var min = (data && data.minChars) || 2;
    if (q.length < min) return [];
    var items = (data && data.items) || [];
    return items
      .filter(function (item) {
        if ((item.title || "").toLowerCase().indexOf(q) !== -1) return true;
        var keys = item.keywords || [];
        for (var i = 0; i < keys.length; i++) {
          if (String(keys[i]).toLowerCase().indexOf(q) !== -1) return true;
        }
        return false;
      })
      .sort(function (a, b) {
        return (b.priority || 0) - (a.priority || 0);
      })
      .slice(0, 12);
  }

  function closeHeaderSearch() {
    if (!headerSearchOut) return;
    headerSearchOut.innerHTML = "";
    headerSearchOut.setAttribute("hidden", "");
  }

  function renderSearchHits(query) {
    if (!headerSearchOut) return;
    headerSearchOut.innerHTML = "";
    loadSearchIndex().then(function (data) {
      var q = (query || "").trim();
      var min = data.minChars || 2;
      if (q.length < min) {
        headerSearchOut.setAttribute("hidden", "");
        return;
      }
      var hits = matchSearchItems(query, data);
      if (!hits.length) {
        var empty = document.createElement("p");
        empty.className = "search-hint";
        empty.textContent = "Нет совпадений по подготовленным ключам.";
        headerSearchOut.appendChild(empty);
        headerSearchOut.removeAttribute("hidden");
        return;
      }
      hits.forEach(function (item) {
        var b = document.createElement("button");
        b.type = "button";
        b.className = "search-hit";
        b.textContent = item.title + (item.description ? " — " + item.description : "");
        b.addEventListener("click", function () {
          closeMega();
          closeHeaderSearch();
          if (item.url) window.location.href = item.url;
        });
        headerSearchOut.appendChild(b);
      });
      headerSearchOut.removeAttribute("hidden");
    });
  }

  if (headerSearch && headerSearchOut) {
    headerSearch.addEventListener("focus", function () {
      closeMega();
      loadSearchIndex().then(function () {
        renderSearchHits(headerSearch.value);
      });
    });
    headerSearch.addEventListener("input", function () {
      closeMega();
      renderSearchHits(headerSearch.value);
    });
  }

  document.addEventListener("pointerdown", function (e) {
    if (!searchWrap || searchWrap.contains(e.target)) return;
    closeHeaderSearch();
  });

  function resetPosterAccordions() {
    if (!poster) return;
    poster.querySelectorAll(".poster-acc__item.is-open").forEach(function (item) {
      item.classList.remove("is-open");
      var t = item.querySelector(".poster-acc__toggle");
      var p = item.querySelector(".poster-acc__panel");
      if (t) t.setAttribute("aria-expanded", "false");
      if (p) p.setAttribute("hidden", "");
    });
  }

  function finishCloseMobile(gen) {
    if (gen !== closeGen) return;
    document.body.classList.remove("nav-open");
    document.body.style.top = "";
    if (overlay) {
      overlay.classList.remove("is-visible");
      overlay.hidden = true;
    }
    resetPosterAccordions();
    window.scrollTo(0, savedY);
  }

  function openMobile() {
    if (!poster) return;
    closeMega();
    closeHeaderSearch();
    closeGen += 1;
    clearTimeout(closeTimer);
    savedY = window.scrollY || 0;
    mobileOpenTrigger = document.activeElement;
    mobileOpen = true;
    document.body.classList.add("nav-open");
    document.body.style.top = "-" + savedY + "px";
    if (toggle) toggle.setAttribute("aria-expanded", "true");
    if (overlay) {
      overlay.hidden = false;
      overlay.classList.remove("is-visible");
      void overlay.offsetWidth;
      overlay.classList.add("is-visible");
    }
    void poster.offsetWidth;
    poster.classList.add("is-open");
    poster.setAttribute("aria-hidden", "false");
    /* `inert` (unlike the CSS `visibility` transition) applies synchronously,
       so the panel is genuinely focusable right away. */
    poster.inert = false;
    setBackgroundInert(true);
    if (closeBtn) {
      closeBtn.focus();
    } else {
      var focusables = getFocusable(poster);
      if (focusables.length) focusables[0].focus();
    }
  }

  function closeMobile() {
    if (!poster) return;
    if (!mobileOpen && !poster.classList.contains("is-open")) return;
    mobileOpen = false;
    var gen = ++closeGen;
    clearTimeout(closeTimer);
    poster.classList.remove("is-open");
    poster.setAttribute("aria-hidden", "true");
    poster.inert = true;
    if (toggle) toggle.setAttribute("aria-expanded", "false");
    if (overlay) overlay.classList.remove("is-visible");
    setBackgroundInert(false);

    var trigger = mobileOpenTrigger;
    mobileOpenTrigger = null;
    if (trigger && typeof trigger.focus === "function") {
      trigger.focus();
    } else if (toggle) {
      toggle.focus();
    }

    if (reducedMotion) {
      finishCloseMobile(gen);
      return;
    }

    var done = false;
    function complete() {
      if (done || gen !== closeGen) return;
      done = true;
      poster.removeEventListener("transitionend", onEnd);
      clearTimeout(closeTimer);
      finishCloseMobile(gen);
    }
    function onEnd(e) {
      if (e.target !== poster) return;
      if (e.propertyName !== "opacity" && e.propertyName !== "transform") return;
      complete();
    }
    poster.addEventListener("transitionend", onEnd);
    closeTimer = setTimeout(complete, 420);
  }

  if (toggle) {
    toggle.addEventListener("click", function () {
      if (mobileOpen || (poster && poster.classList.contains("is-open"))) closeMobile();
      else openMobile();
    });
  }
  if (closeBtn) closeBtn.addEventListener("click", closeMobile);
  if (overlay) overlay.addEventListener("click", closeMobile);

  if (poster) {
    poster.addEventListener("keydown", function (event) {
      if (event.key !== "Tab" || !poster.classList.contains("is-open")) return;
      var focusables = getFocusable(poster);
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

  if (desktopNavMq) {
    var onDesktopNavChange = function (e) {
      if (e.matches && (mobileOpen || (poster && poster.classList.contains("is-open")))) {
        closeMobile();
      }
    };
    if (typeof desktopNavMq.addEventListener === "function") {
      desktopNavMq.addEventListener("change", onDesktopNavChange);
    } else if (typeof desktopNavMq.addListener === "function") {
      desktopNavMq.addListener(onDesktopNavChange);
    }
  }

  poster &&
    poster.querySelectorAll(".poster-acc__toggle").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var item = btn.closest(".poster-acc__item");
        if (!item) return;
        var open = !item.classList.contains("is-open");
        poster.querySelectorAll(".poster-acc__item.is-open").forEach(function (other) {
          if (other === item) return;
          other.classList.remove("is-open");
          var ot = other.querySelector(".poster-acc__toggle");
          var op = other.querySelector(".poster-acc__panel");
          if (ot) ot.setAttribute("aria-expanded", "false");
          if (op) op.setAttribute("hidden", "");
        });
        item.classList.toggle("is-open", open);
        btn.setAttribute("aria-expanded", open ? "true" : "false");
        var panel = item.querySelector(".poster-acc__panel");
        if (panel) {
          if (open) panel.removeAttribute("hidden");
          else panel.setAttribute("hidden", "");
        }
      });
    });

  poster &&
    poster.querySelectorAll("[data-acc-open]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var item = btn.closest(".poster-acc__item");
        var t = item && item.querySelector(".poster-acc__toggle");
        if (t) t.click();
      });
    });

  poster &&
    poster.querySelectorAll("a[href]").forEach(function (a) {
      a.addEventListener("click", closeMobile);
    });

  /* Page spine scroll progress — Raw Materials nav-bar concept
     (https://www.therawmaterials.com/approach): each sector's height grows
     continuously through its own section's ENTIRE scroll, peaking exactly
     when that section's middle lines up with the screen's middle — not a
     binary "active section snaps to tall" switch. Every sector gets its own
     `--sector-progress` every frame, so the growth reads as a wave passing
     through the list rather than a single pill jumping between sizes.

     Two fixes are kept from the earlier throttled version:
     1. rAF-throttled: the raw `scroll` event fires far more often than the
        display paints; without throttling, every event did a full DOM read
        (getBoundingClientRect for every section) plus a style write.
     2. Hysteresis on the discrete `is-active` (color/border) pick only —
        the continuous height itself needs no hysteresis since it's already
        smooth, but which single pill is "current" for a11y/color still
        benefits from not flickering right on a section boundary. */
  var spineActiveId = null;
  var spineTicking = false;

  function computePageSpine() {
    var sectors = document.querySelectorAll(".page-spine .spine-sector[data-nav]");
    if (!sectors.length) return;

    var mid = window.innerHeight * 0.42;
    var spread = window.innerHeight * 0.6;
    var candidates = [];
    var progressMap = {};

    sectors.forEach(function (sector) {
      var id = sector.getAttribute("data-nav");
      var section = document.getElementById(id);
      if (!section) return;
      var rect = section.getBoundingClientRect();
      var center = rect.top + rect.height / 2;
      var dist = Math.abs(center - mid);
      progressMap[id] = Math.max(0, 1 - dist / spread);
      if (rect.bottom > 80 && rect.top < window.innerHeight - 40) {
        candidates.push({ id: id, dist: dist });
      }
    });

    candidates.sort(function (a, b) {
      return a.dist - b.dist;
    });
    var top = candidates[0];
    var bestId = top ? top.id : null;

    var HYSTERESIS = window.innerHeight * 0.06;
    if (spineActiveId && top) {
      var current = candidates.filter(function (c) {
        return c.id === spineActiveId;
      })[0];
      if (current && current.dist - top.dist < HYSTERESIS) {
        bestId = spineActiveId;
      }
    }

    if (!bestId && sectors[0]) bestId = sectors[0].getAttribute("data-nav");
    spineActiveId = bestId;

    sectors.forEach(function (sector) {
      var id = sector.getAttribute("data-nav");
      var active = id === bestId;
      sector.classList.toggle("is-active", active);
      if (active) sector.setAttribute("aria-current", "true");
      else sector.removeAttribute("aria-current");
      var progress = progressMap[id] || 0;
      sector.style.setProperty("--sector-progress", String(Math.round(progress * 1000) / 1000));
    });
  }

  function requestPageSpineUpdate() {
    if (spineTicking) return;
    spineTicking = true;
    window.requestAnimationFrame(function () {
      spineTicking = false;
      computePageSpine();
    });
  }

  if (document.querySelector(".page-spine .spine-sector[data-nav]")) {
    computePageSpine();
    window.addEventListener("scroll", requestPageSpineUpdate, { passive: true });
    window.addEventListener("resize", requestPageSpineUpdate);
  }
})();
