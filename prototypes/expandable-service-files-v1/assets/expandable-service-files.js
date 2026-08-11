(function () {
  "use strict";

  var root = document.querySelector("[data-service-files]");
  if (!root) return;

  var items = Array.prototype.slice.call(root.querySelectorAll(".service-files__item"));
  var phaseTimer = 0;

  /* Premium “tight box” pacing: deliberate geometry, content in the tail. */
  var GEOMETRY_MS = 1240;
  var CONTENT_OUT_MS = 520;
  var HEIGHT_SETTLE_MS = 560;
  var SWITCH_GAP_MS = 1240; /* wait for column morph before underside */
  var VEIL_CLEAR_MS = 420;
  var RAIL_PX = 64;
  var GAP_PX = 14;

  var LABEL_OPEN = "Распаковать услугу";

  function isDesktop() {
    return window.matchMedia("(min-width: 901px)").matches;
  }

  function reduceMotion() {
    return (
      typeof window.matchMedia === "function" &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches
    );
  }

  function getItem(id) {
    return root.querySelector('.service-files__item[data-service-id="' + id + '"]');
  }

  function clearPhaseTimer() {
    if (phaseTimer) {
      window.clearTimeout(phaseTimer);
      phaseTimer = 0;
    }
  }

  function innerGridWidth() {
    var rect = root.getBoundingClientRect();
    var cs = window.getComputedStyle(root);
    var pl = parseFloat(cs.paddingLeft) || 0;
    var pr = parseFloat(cs.paddingRight) || 0;
    return Math.max(0, rect.width - pl - pr);
  }

  function colsClosed() {
    var inner = innerGridWidth();
    var usable = Math.max(0, Math.round(inner) - 2 * GAP_PX);
    var c1 = Math.floor(usable / 3);
    var c2 = Math.floor(usable / 3);
    var c3 = usable - c1 - c2;
    return c1 + "px " + c2 + "px " + c3 + "px";
  }

  function colsOpen(id) {
    var inner = innerGridWidth();
    var gaps = 2 * GAP_PX;
    var main = Math.max(220, Math.round(inner) - 2 * RAIL_PX - gaps);
    if (id === "01") return main + "px " + RAIL_PX + "px " + RAIL_PX + "px";
    if (id === "02") return RAIL_PX + "px " + main + "px " + RAIL_PX + "px";
    return RAIL_PX + "px " + RAIL_PX + "px " + main + "px";
  }

  function lockCurrentCols() {
    root.style.gridTemplateColumns = window.getComputedStyle(root).gridTemplateColumns;
    void root.offsetWidth;
  }

  function animateCols(target) {
    lockCurrentCols();
    root.style.gap = GAP_PX + "px";
    root.style.gridTemplateColumns = target;
  }

  function clearInlineGrid() {
    root.style.gridTemplateColumns = "";
    root.style.gap = "";
  }

  function setToggleState(item, expanded) {
    var toggle = item.querySelector("[data-service-toggle]");
    var closeBtn = item.querySelector("[data-service-close]");
    if (toggle) {
      toggle.setAttribute("aria-expanded", expanded ? "true" : "false");
      toggle.setAttribute("aria-label", "Распаковать услугу — открыть подробности");
      var label = toggle.querySelector(".service-files__toggle-label");
      if (label) label.textContent = LABEL_OPEN;
      if (expanded) toggle.setAttribute("tabindex", "-1");
      else toggle.removeAttribute("tabindex");
    }
    if (closeBtn) {
      if (expanded) {
        closeBtn.hidden = false;
        closeBtn.removeAttribute("tabindex");
      } else {
        closeBtn.hidden = true;
        closeBtn.setAttribute("tabindex", "-1");
      }
    }
  }

  function setPanelExpanded(item, expanded) {
    var panel = item.querySelector("[data-service-panel]");
    if (!panel) return;
    if (expanded) {
      panel.removeAttribute("inert");
      panel.setAttribute("aria-hidden", "false");
      panel.classList.add("is-expanded");
    } else {
      panel.classList.remove("is-expanded");
      panel.setAttribute("aria-hidden", "true");
      panel.setAttribute("inert", "");
    }
  }

  function syncChrome() {
    var openId = root.getAttribute("data-open") || "";
    var closingId = root.getAttribute("data-closing-id") || "";
    var desktop = isDesktop();
    var closing = root.getAttribute("data-phase") === "closing";

    items.forEach(function (item) {
      var id = item.getAttribute("data-service-id");
      var rail = item.querySelector("[data-service-tab]");
      /* Keep is-open on the card that is still wide while columns equalize —
         prevents price/seal from popping back (height jerk). */
      var isClosingCard = closing && closingId === id;
      var isOpen = openId === id || isClosingCard;
      var isRail =
        desktop &&
        ((!!openId && openId !== id) || (closing && !!closingId && id !== closingId));

      item.classList.toggle("is-open", isOpen);
      item.classList.toggle("is-rail", isRail);
      item.classList.remove("is-spine", "is-tabbed");
      setToggleState(item, isOpen && !isClosingCard);

      if (rail) rail.hidden = !isRail;
    });
  }

  function setFaceVeil(item, on) {
    if (!item) return;
    /* Plaque-level frost covers BOTH rail chrome and face title during morph */
    var plaque = item.querySelector(".service-files__plaque");
    var face = item.querySelector(".service-files__face");
    var rail = item.querySelector(".service-files__rail");
    if (plaque) plaque.classList.toggle("is-veiled", !!on);
    if (face) face.classList.remove("is-veiled");
    if (rail) rail.classList.toggle("is-veiled", !!on);
    // #region agent log
    fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H-rail-veil',location:'expandable-service-files.js:setFaceVeil',message:'plaque+rail veil toggle',data:{id:item.getAttribute('data-service-id'),on:!!on,plaqueVeiled:plaque?plaque.classList.contains('is-veiled'):null,railVeiled:rail?rail.classList.contains('is-veiled'):null,railOp:rail?getComputedStyle(rail).opacity:null,phase:root.getAttribute('data-phase'),open:root.getAttribute('data-open')},timestamp:Date.now()})}).catch(function(){});
    // #endregion
  }

  function setPanelVeil(item, on) {
    if (!item) return;
    var panel = item.querySelector("[data-service-panel]");
    if (panel) panel.classList.toggle("is-veiled", !!on);
    // #region agent log
    var cs = panel ? window.getComputedStyle(panel) : null;
    fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'audit1',hypothesisId:'H3',location:'expandable-service-files.js:setPanelVeil',message:'panel veil toggle',data:{id:item.getAttribute('data-service-id'),on:!!on,hasClass:panel?panel.classList.contains('is-veiled'):null,panelOpacity:cs?cs.opacity:null,panelRows:cs?cs.gridTemplateRows:null,phase:root.getAttribute('data-phase')},timestamp:Date.now()})}).catch(function(){});
    // #endregion
  }

  function clearAllVeils() {
    items.forEach(function (item) {
      setFaceVeil(item, false);
      setPanelVeil(item, false);
    });
  }

  function veilExpandingRails() {
    items.forEach(function (item) {
      if (item.classList.contains("is-rail")) setFaceVeil(item, true);
    });
  }

  function revealContent(id) {
    clearFaceHeightLocks();
    root.setAttribute("data-phase", "content");
    items.forEach(function (item) {
      setPanelExpanded(item, item.getAttribute("data-service-id") === id);
    });
    window.setTimeout(function () {
      clearAllVeils();
    }, reduceMotion() ? 0 : VEIL_CLEAR_MS);
  }

  function collapseContent() {
    /* Lock open face height before underside collapses — prevents row spike */
    items.forEach(function (item) {
      if (!item.classList.contains("is-open")) return;
      var face = item.querySelector(".service-files__face");
      if (!face) return;
      var h = Math.round(face.getBoundingClientRect().height);
      face.style.minHeight = h + "px";
      // #region agent log
      fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H-height',location:'expandable-service-files.js:collapseContent',message:'lock face minHeight before collapse',data:{id:item.getAttribute('data-service-id'),lockedH:h,phaseBefore:root.getAttribute('data-phase')},timestamp:Date.now()})}).catch(function(){});
      // #endregion
    });
    root.setAttribute("data-phase", "width");
    items.forEach(function (item) {
      setPanelExpanded(item, false);
    });
  }

  function clearFaceHeightLocks() {
    items.forEach(function (item) {
      var face = item.querySelector(".service-files__face");
      var plaque = item.querySelector(".service-files__plaque");
      if (face) face.style.minHeight = "";
      if (plaque) plaque.style.minHeight = "";
    });
  }

  function finishClose(focusToggle, prevId) {
    root.removeAttribute("data-closing-id");
    root.setAttribute("data-open", "");
    root.setAttribute("data-phase", "");
    clearAllVeils();
    clearFaceHeightLocks();
    items.forEach(function (item) {
      setPanelExpanded(item, false);
      item.classList.remove("is-open", "is-rail");
      setToggleState(item, false);
      var rail = item.querySelector("[data-service-tab]");
      if (rail) rail.hidden = true;
    });
    if (!isDesktop()) clearInlineGrid();

    if (focusToggle && prevId) {
      var prev = getItem(prevId);
      var btn = prev && prev.querySelector("[data-service-toggle]");
      if (btn) btn.focus();
    }
  }

  function beginClosingWidth(prevId, focusToggle) {
    root.setAttribute("data-closing-id", prevId);
    root.setAttribute("data-phase", "closing");
    root.setAttribute("data-open", "");
    items.forEach(function (item) {
      setPanelExpanded(item, false);
      setPanelVeil(item, false);
    });
    syncChrome();
    /* After underside gone, ease height down to closed size before/with columns */
    clearFaceHeightLocks();
    var closingFace = getItem(prevId) && getItem(prevId).querySelector(".service-files__face");
    if (closingFace) {
      closingFace.style.minHeight = "340px";
    }
    items.forEach(function (item) {
      if (!item.classList.contains("is-rail")) return;
      var plaque = item.querySelector(".service-files__plaque");
      if (plaque) plaque.style.minHeight = "340px";
    });
    /* Rails expand → closed faces: frost those faces. Closing title stays sharp. */
    veilExpandingRails();
    setFaceVeil(getItem(prevId), false);
    // #region agent log
    fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H5',location:'expandable-service-files.js:beginClosingWidth',message:'closing width + rail face veils',data:{prevId:prevId,closingStillOpen:getItem(prevId)&&getItem(prevId).classList.contains('is-open'),rails:items.filter(function(i){return i.classList.contains('is-rail');}).map(function(i){var pl=i.querySelector('.service-files__plaque');var rail=i.querySelector('.service-files__rail');return{id:i.getAttribute('data-service-id'),plaqueVeiled:pl&&pl.classList.contains('is-veiled'),railVeiled:rail&&rail.classList.contains('is-veiled'),railOp:rail?getComputedStyle(rail).opacity:null};}),closingPlaqueVeiled:getItem(prevId)&&getItem(prevId).querySelector('.service-files__plaque').classList.contains('is-veiled')},timestamp:Date.now()})}).catch(function(){});
    // #endregion
    animateCols(colsClosed());
    phaseTimer = window.setTimeout(function () {
      phaseTimer = 0;
      finishClose(focusToggle, prevId);
    }, GEOMETRY_MS);
  }

  function closeAll(focusToggle) {
    var prevId = root.getAttribute("data-open") || "";
    var phase = root.getAttribute("data-phase") || "";
    clearPhaseTimer();

    if (!prevId) {
      finishClose(false, "");
      return;
    }

    if (reduceMotion()) {
      animateCols(colsClosed());
      finishClose(focusToggle, prevId);
      return;
    }

    var openItem = getItem(prevId);
    if (openItem) {
      setToggleState(openItem, false);
      /* Underside fades via panel opacity — no face frost on title */
      setPanelVeil(openItem, false);
      setFaceVeil(openItem, false);
      // #region agent log
      fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H3',location:'expandable-service-files.js:closeAll',message:'close started selective veil',data:{prevId:prevId,phase:phase,faceVeiled:openItem.querySelector('.service-files__face').classList.contains('is-veiled'),panelVeiled:openItem.querySelector('[data-service-panel]').classList.contains('is-veiled'),faceH:Math.round(openItem.querySelector('.service-files__face').getBoundingClientRect().height)},timestamp:Date.now()})}).catch(function(){});
      // #endregion
    }

    if (!isDesktop()) {
      collapseContent();
      items.forEach(function (item) {
        item.classList.remove("is-open");
        setToggleState(item, false);
      });
      phaseTimer = window.setTimeout(function () {
        phaseTimer = 0;
        finishClose(focusToggle, prevId);
      }, phase === "content" ? HEIGHT_SETTLE_MS : 120);
      return;
    }

    collapseContent();

    /* If details never appeared (still width-phase), skip long height wait */
    var settle = phase === "content" ? HEIGHT_SETTLE_MS : 160;
    phaseTimer = window.setTimeout(function () {
      phaseTimer = 0;
      beginClosingWidth(prevId, focusToggle);
    }, settle);
  }

  function openService(id, opts) {
    opts = opts || {};
    if (!id) {
      closeAll(opts.focusToggle);
      return;
    }

    var prev = root.getAttribute("data-open") || "";
    var phase = root.getAttribute("data-phase") || "";
    clearPhaseTimer();
    root.removeAttribute("data-closing-id");

    if (reduceMotion()) {
      root.setAttribute("data-open", id);
      root.setAttribute("data-phase", "content");
      if (isDesktop()) {
        root.style.gap = GAP_PX + "px";
        root.style.gridTemplateColumns = colsOpen(id);
      }
      syncChrome();
      items.forEach(function (item) {
        setPanelExpanded(item, item.getAttribute("data-service-id") === id);
      });
      if (opts.focusToggle) {
        var btnRm = getItem(id).querySelector("[data-service-toggle]");
        if (btnRm) btnRm.focus();
      }
      return;
    }

    if (!isDesktop()) {
      root.setAttribute("data-open", id);
      root.setAttribute("data-phase", "content");
      syncChrome();
      items.forEach(function (item) {
        setPanelExpanded(item, item.getAttribute("data-service-id") === id);
      });
      if (opts.focusToggle) {
        var btnM = getItem(id).querySelector("[data-service-toggle]");
        if (btnM) btnM.focus();
      }
      return;
    }

    if (prev && prev !== id && (phase === "content" || phase === "width")) {
      /* Prev: fade underside only. Next (was rail): frost face for full width morph. */
      // #region agent log
      fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H1',location:'expandable-service-files.js:switch-start',message:'switch path entered',data:{prev:prev,next:id,phase:phase,contentOutMs:CONTENT_OUT_MS,switchGapMs:SWITCH_GAP_MS,geometryMs:GEOMETRY_MS,cssDur:getComputedStyle(root).getPropertyValue('--sf-dur').trim()},timestamp:Date.now()})}).catch(function(){});
      // #endregion
      setPanelVeil(getItem(prev), false);
      setFaceVeil(getItem(prev), false);
      collapseContent();
      // #region agent log
      (function(){var p=getItem(prev).querySelector('[data-service-panel]');var cs=p&&getComputedStyle(p);fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H3',location:'expandable-service-files.js:switch-after-collapse',message:'prev panel after collapseContent',data:{prev:prev,phase:root.getAttribute('data-phase'),panelVeiled:p&&p.classList.contains('is-veiled'),panelOpacity:cs&&cs.opacity,panelVis:cs&&cs.visibility,expanded:p&&p.classList.contains('is-expanded')},timestamp:Date.now()})}).catch(function(){});})();
      // #endregion
      phaseTimer = window.setTimeout(function () {
        clearFaceHeightLocks();
        root.setAttribute("data-open", id);
        root.setAttribute("data-phase", "width");
        syncChrome();
        animateCols(colsOpen(id));
        setFaceVeil(getItem(id), true);
        setPanelVeil(getItem(prev), false);
        // #region agent log
        fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H1',location:'expandable-service-files.js:switch-width',message:'switch width phase started',data:{next:id,cols:root.style.gridTemplateColumns,nextFaceVeiled:getItem(id).querySelector('.service-files__face').classList.contains('is-veiled'),nextFaceOp:getComputedStyle(getItem(id).querySelector('.service-files__face')).opacity,nextIsRail:getItem(id).classList.contains('is-rail'),prevIsRail:getItem(prev).classList.contains('is-rail'),switchGapThenReveal:SWITCH_GAP_MS},timestamp:Date.now()})}).catch(function(){});
        // #endregion
        phaseTimer = window.setTimeout(function () {
          phaseTimer = 0;
          // #region agent log
          fetch('http://127.0.0.1:7610/ingest/3377e22c-dcfd-4023-b3b8-7736187c100a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'a6d936'},body:JSON.stringify({sessionId:'a6d936',runId:'post-fix',hypothesisId:'H1',location:'expandable-service-files.js:switch-reveal',message:'revealContent called after switch gap',data:{id:id,msSinceWidthStart:SWITCH_GAP_MS,faceStillVeiled:getItem(id).querySelector('.service-files__face').classList.contains('is-veiled'),cols:root.style.gridTemplateColumns},timestamp:Date.now()})}).catch(function(){});
          // #endregion
          revealContent(id);
        }, SWITCH_GAP_MS);
      }, CONTENT_OUT_MS);
      return;
    }

    root.setAttribute("data-open", id);
    root.setAttribute("data-phase", "width");
    syncChrome();
    items.forEach(function (item) {
      setPanelExpanded(item, false);
    });
    animateCols(colsOpen(id));

    phaseTimer = window.setTimeout(function () {
      phaseTimer = 0;
      revealContent(id);
    }, prev === id && phase === "width" ? 0 : GEOMETRY_MS);

    if (opts.focusToggle) {
      var focusBtn = getItem(id).querySelector("[data-service-toggle]");
      if (focusBtn) focusBtn.focus();
    }
  }

  function toggleService(id) {
    var current = root.getAttribute("data-open") || "";
    if (current === id) closeAll(true);
    else openService(id);
  }

  root.addEventListener("click", function (event) {
    if (event.target.closest("a, .service-files__cta, .service-files__more")) {
      return;
    }

    var closeHit = event.target.closest("[data-service-close]");
    if (closeHit && root.contains(closeHit)) {
      event.preventDefault();
      event.stopPropagation();
      closeAll(true);
      return;
    }

    var toggle = event.target.closest("[data-service-toggle]");
    if (toggle && root.contains(toggle)) {
      event.preventDefault();
      event.stopPropagation();
      var fromToggle = toggle.closest(".service-files__item");
      if (fromToggle) toggleService(fromToggle.getAttribute("data-service-id"));
      return;
    }

    var railHit = event.target.closest(".service-files__item.is-rail");
    if (railHit && root.contains(railHit)) {
      openService(railHit.getAttribute("data-service-id"));
      return;
    }

    var closedSurface = event.target.closest(
      ".service-files__item:not(.is-open):not(.is-rail) .service-files__face"
    );
    if (closedSurface && root.contains(closedSurface)) {
      if (event.target.closest("[data-service-toggle]")) return;
      var item = closedSurface.closest(".service-files__item");
      if (item) openService(item.getAttribute("data-service-id"));
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key !== "Escape") return;
    if (!root.getAttribute("data-open") && root.getAttribute("data-phase") !== "closing") {
      return;
    }
    if (!root.contains(document.activeElement)) return;
    event.preventDefault();
    closeAll(true);
  });

  window.addEventListener("resize", function () {
    var openId = root.getAttribute("data-open") || "";
    var phase = root.getAttribute("data-phase") || "";
    if (!isDesktop()) {
      clearInlineGrid();
      if (openId) {
        root.setAttribute("data-phase", "content");
        syncChrome();
        items.forEach(function (item) {
          setPanelExpanded(item, item.getAttribute("data-service-id") === openId);
        });
      } else {
        syncChrome();
      }
      return;
    }

    if (phase === "closing") {
      root.style.gridTemplateColumns = colsClosed();
      root.style.gap = GAP_PX + "px";
      return;
    }

    if (openId) {
      root.style.gridTemplateColumns = colsOpen(openId);
      root.style.gap = GAP_PX + "px";
      syncChrome();
      return;
    }

    root.style.gridTemplateColumns = colsClosed();
    root.style.gap = GAP_PX + "px";
    syncChrome();
  });

  root.setAttribute("data-phase", "");
  if (isDesktop()) {
    root.style.gridTemplateColumns = colsClosed();
    root.style.gap = GAP_PX + "px";
  }
  closeAll(false);

  window.__serviceFilesLab = {
    open: openService,
    close: function () {
      closeAll(false);
    }
  };
})();
