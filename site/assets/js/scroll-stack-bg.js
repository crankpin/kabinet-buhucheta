/**
 * Homepage decorative 3D document trains (desktop only).
 *
 * Model: ONE shared 3D path per train. Every card runs the same path
 * with the SAME duration; stagger only shifts phase.
 *
 * Trains stay in outer side rails — never under the readable content column:
 *
 * train-in  (incoming):  RIGHT rail, top → bottom   (scroll 0–48%)
 * train-out (outgoing):  LEFT of spine-sections, bottom → top  (scroll 52–100%)
 *
 * Sequential: one train finishes before the other starts (short handoff gap).
 * Trajectories are not mirrors: different side, opposite vertical direction,
 * opposite tilt sign.
 */
(function () {
  var root = document.getElementById("scroll-stack-bg");
  if (!root) return;

  var desktopMq = window.matchMedia("(min-width: 1025px)");
  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  if (typeof window.gsap === "undefined" || typeof window.ScrollTrigger === "undefined") {
    root.hidden = true;
    return;
  }

  var gsap = window.gsap;
  var ScrollTrigger = window.ScrollTrigger;
  gsap.registerPlugin(ScrollTrigger);

  var trainIn = root.querySelector('[data-train="in"]');
  var trainOut = root.querySelector('[data-train="out"]');
  var tl = null;
  var st = null;

  var OP_FAR = 0.35;
  var OP_NEAR = 0.58;
  var MIN_GUTTER = 72;
  var TRAIN_BASE_W = 130;

  function canRun() {
    return desktopMq.matches && !reduceMotion.matches;
  }

  function visibleCards(train) {
    return gsap.utils.toArray(train.querySelectorAll("[data-stack-card]")).filter(function (el) {
      return window.getComputedStyle(el).display !== "none";
    });
  }

  function winW() {
    return window.innerWidth || document.documentElement.clientWidth || 1200;
  }

  function winH() {
    return window.innerHeight || document.documentElement.clientHeight || 800;
  }

  function killScene() {
    if (st) {
      st.kill();
      st = null;
    }
    if (tl) {
      tl.kill();
      tl = null;
    }
    root.hidden = true;
    root.style.opacity = "0";
  }

  function lerp(a, b, t) {
    return a + (b - a) * t;
  }

  function clamp(v, lo, hi) {
    return Math.max(lo, Math.min(hi, v));
  }

  /**
   * Left rail: tucked against spine-sections (up to ~10% under spine).
   * Right rail: against reading column (up to ~10% under content).
   * Transforms are offsets from viewport center (scene is grid-centered).
   */
  function measureGutters() {
    var ww = winW();
    var wh = winH();
    var cx = ww / 2;
    var sheet = TRAIN_BASE_W;

    var contentLeft = ww;
    var contentRight = 0;
    var nodes = document.querySelectorAll(
      ".page-content .container, .hero__content, .page-content .section-head"
    );
    Array.prototype.forEach.call(nodes, function (el) {
      var r = el.getBoundingClientRect();
      if (r.width < 600) return;
      contentLeft = Math.min(contentLeft, r.left);
      contentRight = Math.max(contentRight, r.right);
    });
    if (contentRight <= contentLeft) {
      var fallback =
        document.querySelector(".page-content .container") ||
        document.querySelector(".page-content");
      var fr = fallback.getBoundingClientRect();
      contentLeft = fr.left;
      contentRight = fr.right;
    }

    var spineSections = document.querySelector(".spine-sections");
    var spineEl = document.querySelector(".page-spine");
    var spineLeft = 0;
    if (spineSections && getComputedStyle(spineSections).display !== "none") {
      spineLeft = spineSections.getBoundingClientRect().left;
    } else if (spineEl && getComputedStyle(spineEl).display !== "none") {
      spineLeft = spineEl.getBoundingClientRect().left;
    }

    /* Outgoing: hug spine from the left; ≤10% of sheet under spine.
       Center sits just left of spine so sheets aren't lost past the viewport edge. */
    var leftCenter = spineLeft - sheet * 0.38;
    var leftInner = Math.max(0, leftCenter - sheet * 0.55);
    var leftOuter = leftCenter + sheet * 0.55;
    var leftWidth = leftOuter - leftInner;

    /* Incoming: hug content from the right; ≤10% of sheet under content.
       Lane is a tight band against the column (not mid-gutter). */
    var rightCenter = contentRight + sheet * 0.38;
    var rightInner = rightCenter - sheet * 0.55;
    var rightOuter = Math.min(ww, rightCenter + sheet * 0.55);
    var rightWidth = rightOuter - rightInner;

    var pad = 4;

    return {
      ww: ww,
      wh: wh,
      cx: cx,
      leftWidth: leftWidth,
      rightWidth: rightWidth,
      leftX: leftCenter - cx,
      rightX: rightCenter - cx,
      leftMinX: leftInner + pad - cx,
      leftMaxX: leftOuter - pad - cx,
      rightMinX: rightInner + pad - cx,
      rightMaxX: rightOuter - pad - cx,
      leftHalf: Math.max(0, leftWidth / 2 - pad),
      rightHalf: Math.max(0, rightWidth / 2 - pad),
      contentLeft: contentLeft,
      contentRight: contentRight,
      usable: leftWidth >= MIN_GUTTER && rightWidth >= MIN_GUTTER,
    };
  }

  function scaleForGutter(gutterWidth) {
    /* Fit sheet into rail; prefer near-1 when rail allows readable type. */
    return clamp(gutterWidth / TRAIN_BASE_W, 0.7, 1.08);
  }

  function samplePath(u, path, lateral) {
    var lx = lateral ? lateral.x : 0;
    var ly = lateral ? lateral.y : 0;
    var lr = lateral ? lateral.rot : 0;

    if (u <= 0.5) {
      var t = u / 0.5;
      return {
        x: lerp(path.x0, path.xWork, t) + lx,
        y: lerp(path.y0, path.yWork, t) + ly,
        z: lerp(path.z0, path.zWork, t),
        scale: lerp(path.s0, path.sWork, t),
        rotationZ: lerp(path.r0, path.rWork, t) + lr,
        opacity: lerp(OP_FAR, OP_NEAR, t),
      };
    }

    var t2 = (u - 0.5) / 0.5;
    return {
      x: lerp(path.xWork, path.x1, t2) + lx,
      y: lerp(path.yWork, path.y1, t2) + ly,
      z: lerp(path.zWork, path.z1, t2),
      scale: lerp(path.sWork, path.s1, t2),
      rotationZ: lerp(path.rWork, path.r1, t2) + lr,
      opacity: lerp(OP_NEAR, OP_FAR, t2),
    };
  }

  function poseTrain(train, cards, opts) {
    var n = cards.length;
    var windowDur = opts.t1 - opts.t0;
    var stagger = opts.stagger;
    var cardDur = windowDur - (n - 1) * stagger;
    if (cardDur < 0.2) {
      cardDur = 0.2;
      stagger = Math.max(0.016, (windowDur - cardDur) / Math.max(1, n - 1));
    }

    var mid = (n - 1) / 2;
    var path = opts.path;
    var xMin = opts.xMin;
    var xMax = opts.xMax;

    gsap.set(train, {
      x: 0,
      y: 0,
      z: 0,
      rotateX: opts.tiltX,
      rotateY: opts.tiltY,
      rotateZ: 0,
      force3D: true,
      opacity: 0,
    });

    gsap.set(cards, {
      force3D: true,
      transformOrigin: "50% 50%",
      rotationX: 0,
      rotationY: 0,
    });

    tl.fromTo(
      train,
      { opacity: 0 },
      { opacity: 1, duration: opts.fadeIn, ease: "none" },
      opts.t0
    );
    tl.to(
      train,
      { opacity: 0, duration: opts.fadeOut, ease: "none" },
      opts.t1 - opts.fadeOut
    );

    cards.forEach(function (card, i) {
      var lateral = {
        x: clamp((i - mid) * opts.laneX, -opts.laneClamp, opts.laneClamp),
        y: (i - mid) * opts.laneY,
        rot: (i - mid) * opts.laneRot,
      };

      var p0 = samplePath(0, path, lateral);
      var pMid = samplePath(0.5, path, lateral);
      var p1 = samplePath(1, path, lateral);

      function clampX(p) {
        var half = ((TRAIN_BASE_W * p.scale) / 2) * 1.2;
        var lo = xMin + half;
        var hi = xMax - half;
        if (lo > hi) {
          return {
            x: (xMin + xMax) / 2,
            y: p.y,
            z: p.z,
            scale: Math.min(p.scale, Math.max(0.45, (xMax - xMin) / (TRAIN_BASE_W * 1.2))),
            rotationZ: p.rotationZ,
            opacity: p.opacity,
          };
        }
        return {
          x: clamp(p.x, lo, hi),
          y: p.y,
          z: p.z,
          scale: p.scale,
          rotationZ: p.rotationZ,
          opacity: p.opacity,
        };
      }

      p0 = clampX(p0);
      pMid = clampX(pMid);
      p1 = clampX(p1);

      var cardStart = opts.t0 + i * stagger;
      var withinWrapperFade = cardStart <= opts.t0 + opts.fadeIn;

      gsap.set(card, {
        x: p0.x,
        y: p0.y,
        z: p0.z,
        scale: p0.scale,
        rotationZ: p0.rotationZ,
        opacity: withinWrapperFade ? p0.opacity : 0,
      });

      tl.to(
        card,
        {
          keyframes: [
            {
              x: pMid.x,
              y: pMid.y,
              z: pMid.z,
              scale: pMid.scale,
              rotationZ: pMid.rotationZ,
              opacity: pMid.opacity,
              duration: cardDur * 0.5,
              ease: "none",
            },
            {
              x: p1.x,
              y: p1.y,
              z: p1.z,
              scale: p1.scale,
              rotationZ: p1.rotationZ,
              opacity: p1.opacity,
              duration: cardDur * 0.5,
              ease: "none",
            },
          ],
        },
        cardStart
      );
    });
  }

  function build() {
    killScene();

    if (!canRun()) return;
    if (!trainIn || !trainOut) return;

    var cardsIn = visibleCards(trainIn);
    var cardsOut = visibleCards(trainOut);
    if (!cardsIn.length || !cardsOut.length) return;

    var gutters = measureGutters();
    if (!gutters.usable) return;

    root.hidden = false;
    root.style.opacity = "1";

    var wh = gutters.wh;
    var triggerEl = document.querySelector(".page-content") || document.body;
    var footerEl = document.querySelector(".site-footer");

    var inScale = scaleForGutter(gutters.rightWidth);
    var outScale = scaleForGutter(gutters.leftWidth);
    var inFar = inScale * 0.82;
    var outFar = outScale * 0.82;

    tl = gsap.timeline({
      defaults: { ease: "none" },
      scrollTrigger: {
        trigger: triggerEl,
        start: "top top",
        endTrigger: footerEl || triggerEl,
        end: footerEl ? "top bottom" : "bottom bottom",
        scrub: 1,
        invalidateOnRefresh: true,
      },
    });
    st = tl.scrollTrigger;

    var inLane = Math.min(14, gutters.rightHalf * 0.25);
    var outLane = Math.min(14, gutters.leftHalf * 0.25);

    /* Incoming FIRST: right rail, top → bottom (0 → 48%). */
    poseTrain(trainIn, cardsIn, {
      tiltX: -8,
      tiltY: -14,
      fadeIn: 0.04,
      fadeOut: 0.05,
      t0: 0,
      t1: 0.48,
      stagger: 0.028,
      laneX: inLane * 0.4,
      laneY: 14,
      laneRot: 0.7,
      laneClamp: Math.max(6, gutters.rightHalf * 0.3),
      xMin: gutters.rightMinX,
      xMax: gutters.rightMaxX,
      path: {
        x0: gutters.rightX - gutters.rightHalf * 0.06,
        y0: -0.55 * wh,
        z0: -120,
        s0: inFar,
        r0: 2.5,
        xWork: gutters.rightX - gutters.rightHalf * 0.04,
        yWork: 0.02 * wh,
        zWork: 0,
        sWork: inScale,
        rWork: 0.8,
        x1: gutters.rightX - gutters.rightHalf * 0.12,
        y1: 0.55 * wh,
        z1: -90,
        s1: inFar,
        r1: -0.6,
      },
    });

    /* Outgoing SECOND: left of spine-sections, bottom → top (52 → 100%). */
    poseTrain(trainOut, cardsOut, {
      tiltX: 7,
      tiltY: 12,
      fadeIn: 0.04,
      fadeOut: 0.05,
      t0: 0.52,
      t1: 1,
      stagger: 0.03,
      laneX: outLane * 0.35,
      laneY: -12,
      laneRot: -0.55,
      laneClamp: Math.max(6, gutters.leftHalf * 0.3),
      xMin: gutters.leftMinX,
      xMax: gutters.leftMaxX,
      path: {
        x0: gutters.leftX + gutters.leftHalf * 0.08,
        y0: 0.55 * wh,
        z0: -110,
        s0: outFar,
        r0: -2,
        xWork: gutters.leftX + gutters.leftHalf * 0.06,
        yWork: -0.02 * wh,
        zWork: 0,
        sWork: outScale,
        rWork: -0.8,
        x1: gutters.leftX + gutters.leftHalf * 0.12,
        y1: -0.55 * wh,
        z1: -80,
        s1: outFar,
        r1: 1.2,
      },
    });

    tl.duration(1);
  }

  build();

  var resizeTimer = 0;
  window.addEventListener("resize", function () {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(function () {
      build();
      if (canRun()) ScrollTrigger.refresh();
    }, 120);
  });

  if (typeof desktopMq.addEventListener === "function") {
    desktopMq.addEventListener("change", function () {
      build();
      if (canRun()) ScrollTrigger.refresh();
    });
  }

  if (typeof reduceMotion.addEventListener === "function") {
    reduceMotion.addEventListener("change", function () {
      build();
      if (canRun()) ScrollTrigger.refresh();
    });
  }

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(function () {
      if (canRun()) ScrollTrigger.refresh();
    });
  }

  window.addEventListener("load", function () {
    if (canRun()) {
      ScrollTrigger.refresh();
    }
  });
})();
