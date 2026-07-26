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
  var reducedMotion =
    typeof window.matchMedia === "function" &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

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
  }

  function closeMobile() {
    if (!poster) return;
    if (!mobileOpen && !poster.classList.contains("is-open")) return;
    mobileOpen = false;
    var gen = ++closeGen;
    clearTimeout(closeTimer);
    poster.classList.remove("is-open");
    poster.setAttribute("aria-hidden", "true");
    if (toggle) toggle.setAttribute("aria-expanded", "false");
    if (overlay) overlay.classList.remove("is-visible");

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

  /* Page spine scroll progress */
  function updatePageSpine() {
    var sectors = document.querySelectorAll(".page-spine .spine-sector[data-nav]");
    if (!sectors.length) return;

    var mid = window.innerHeight * 0.42;
    var readLine = window.innerHeight * 0.28;
    var bestId = null;
    var bestDist = Infinity;
    var progressMap = {};

    sectors.forEach(function (sector) {
      var id = sector.getAttribute("data-nav");
      var section = document.getElementById(id);
      if (!section) return;
      var rect = section.getBoundingClientRect();
      var center = rect.top + rect.height / 2;
      var dist = Math.abs(center - mid);
      if (rect.bottom > 80 && rect.top < window.innerHeight - 40 && dist < bestDist) {
        bestDist = dist;
        bestId = id;
      }
      var scrolled =
        rect.height > 0
          ? Math.max(0, Math.min(1, (readLine - rect.top) / rect.height))
          : 0;
      progressMap[id] = scrolled;
    });

    if (!bestId && sectors[0]) bestId = sectors[0].getAttribute("data-nav");

    sectors.forEach(function (sector) {
      var id = sector.getAttribute("data-nav");
      var active = id === bestId;
      var progress = 0;
      if (active) progress = Math.max(0.12, progressMap[id] || 0);
      sector.classList.toggle("is-active", active);
      if (active) sector.setAttribute("aria-current", "true");
      else sector.removeAttribute("aria-current");
      sector.style.setProperty("--sector-progress", String(Math.round(progress * 1000) / 1000));
    });
  }

  if (document.querySelector(".page-spine .spine-sector[data-nav]")) {
    updatePageSpine();
    window.addEventListener("scroll", updatePageSpine, { passive: true });
    window.addEventListener("resize", updatePageSpine);
  }
})();
