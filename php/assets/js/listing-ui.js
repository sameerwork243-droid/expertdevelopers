// listing-ui.js — client behaviour ported from listing-ui.tsx, read-more.tsx,
// card-gallery.tsx and the MortgageCalculator state (React components).
(function () {
  "use strict";

  /* ---------- FilterDropdown (open/close + outside click) ---------- */

  function closeAllDropdowns() {
    document.querySelectorAll(".filter-dropdown .dropdown-menu.show").forEach(function (menu) {
      menu.classList.remove("show");
      var root = menu.closest(".filter-dropdown");
      var btn = root ? root.querySelector(".filter-dropdown-toggle") : null;
      if (btn) btn.setAttribute("aria-expanded", "false");
    });
  }

  document.addEventListener("click", function (e) {
    var toggle = e.target && e.target.closest ? e.target.closest(".filter-dropdown-toggle") : null;
    if (toggle) {
      var root = toggle.closest(".filter-dropdown");
      var menu = root ? root.querySelector(".dropdown-menu") : null;
      if (menu) {
        var wasOpen = menu.classList.contains("show");
        closeAllDropdowns();
        if (!wasOpen) {
          menu.classList.add("show");
          toggle.setAttribute("aria-expanded", "true");
        }
        return;
      }
    }
    closeAllDropdowns();
  });

  /* ---------- TypeSelect (react-select open/close) ---------- */

  function closeAllSelects() {
    document.querySelectorAll(".react-select-wrap .react-select--is-open").forEach(function (box) {
      box.classList.remove("react-select--is-open");
      var ctl = box.querySelector(".react-select__control");
      if (ctl) ctl.classList.remove("react-select__control--menu-is-open");
      var menu = box.querySelector(".react-select__menu");
      if (menu) menu.style.display = "none";
    });
  }

  document.addEventListener("click", function (e) {
    var control = e.target && e.target.closest ? e.target.closest(".react-select__control") : null;
    if (control) {
      var root = control.closest(".react-select-wrap");
      var box = root ? root.querySelector(".react-select") : null;
      var menu = root ? root.querySelector(".react-select__menu") : null;
      if (box && menu) {
        var wasOpen = box.classList.contains("react-select--is-open");
        closeAllSelects();
        if (!wasOpen) {
          box.classList.add("react-select--is-open");
          control.classList.add("react-select__control--menu-is-open");
          menu.style.display = "block";
        }
        return;
      }
    }
    closeAllSelects();
  });

  /* ---------- ReadMore (clamp measurement + toggle) ---------- */

  document.querySelectorAll(".read-more-wrap").forEach(function (wrap) {
    var el = wrap.querySelector(".read-more");
    var btn = wrap.querySelector(".read-more-toggle");
    if (!el || !btn) return;
    var lines = parseInt(el.style.webkitLineClamp || "4", 10) || 4;

    function measure() {
      if (el.classList.contains("read-more-expanded")) return;
      btn.style.display = el.scrollHeight > el.clientHeight + 1 ? "" : "none";
    }
    measure();
    if (typeof ResizeObserver !== "undefined") {
      var ro = new ResizeObserver(measure);
      ro.observe(el);
    }
    btn.addEventListener("click", function () {
      var expanded = el.classList.contains("read-more-expanded");
      if (expanded) {
        el.classList.remove("read-more-expanded");
        el.classList.add("read-more-clamped");
        el.style.display = "-webkit-box";
        el.style.webkitBoxOrient = "vertical";
        el.style.webkitLineClamp = String(lines);
        el.style.overflow = "hidden";
        btn.textContent = "Read More";
        btn.setAttribute("aria-expanded", "false");
        measure();
      } else {
        el.classList.add("read-more-expanded");
        el.classList.remove("read-more-clamped");
        el.style.display = "";
        el.style.webkitBoxOrient = "";
        el.style.webkitLineClamp = "";
        el.style.overflow = "";
        btn.textContent = "Read Less";
        btn.setAttribute("aria-expanded", "true");
      }
    });
  });

  /* ---------- CardGallery (mini swiper paging) ---------- */

  document.querySelectorAll(".property-card .swiper").forEach(function (sw) {
    var slides = Array.prototype.slice.call(sw.querySelectorAll(".swiper-slide"));
    if (!slides.length) return;
    var prev = sw.querySelector(".custom-prev");
    var next = sw.querySelector(".custom-next");
    var countEl = sw.querySelector(".count");
    var n = countEl ? parseInt(countEl.textContent || "0", 10) : slides.length;
    if (!n || isNaN(n)) n = slides.length;
    n = Math.max(1, n);
    var idx = 0;

    function show() {
      slides.forEach(function (s, j) {
        s.style.display = j === idx % n ? "" : "none";
      });
    }
    if (prev) prev.addEventListener("click", function () { idx = (idx - 1 + n) % n; show(); });
    if (next) next.addEventListener("click", function () { idx = (idx + 1) % n; show(); });
  });

  /* ---------- SortDropdown (listing order) ---------- */

  function sortValue(card, key) {
    if (key === "price") {
      var pa = card.getAttribute("data-sort-price");
      if (pa !== null) return parseInt(pa, 10) || 0;
      var pe = card.querySelector("[data-save-price]");
      if (pe) return parseInt(pe.getAttribute("data-save-price"), 10) || 0;
      var pEl = card.querySelector(".content-section .price");
      var pv = pEl ? parseInt((pEl.textContent || "").replace(/[^\d]/g, ""), 10) : NaN;
      return isNaN(pv) ? 0 : pv;
    }
    if (key === "bed") {
      var ba = card.getAttribute("data-sort-bed");
      if (ba !== null) return parseInt(ba, 10) || 0;
      var be = card.querySelector(".bedrooms span, .beds span");
      var bv = be ? parseInt((be.textContent || "").replace(/[^\d]/g, ""), 10) : NaN;
      return isNaN(bv) ? 0 : bv;
    }
    if (key === "size") {
      var sa = card.getAttribute("data-sort-size");
      if (sa !== null) return parseInt(sa, 10) || 0;
      var se = card.querySelector(".size span");
      var sv = se ? parseInt((se.textContent || "").replace(/[^\d]/g, ""), 10) : NaN;
      return isNaN(sv) ? 0 : sv;
    }
    return 0;
  }

  Array.prototype.forEach.call(document.querySelectorAll(".sort-dropdown"), function (sortRoot) {
    var sortBtn = sortRoot.querySelector(".sort-section");
    var sortMenu = sortRoot.querySelector(".sort-menu");
    var sortLabel = sortRoot.querySelector(".text.button-text span");
    var targetSel = sortRoot.getAttribute("data-sort-target");
    var listContainer = targetSel
      ? document.querySelector(targetSel)
      : document.querySelector("#property-page-1") || document.querySelector(".property-list-section");
    var originalOrder = null;

    function closeSort() {
      if (sortMenu) sortMenu.classList.remove("show");
      if (sortBtn) { sortBtn.classList.remove("show"); sortBtn.setAttribute("aria-expanded", "false"); }
    }

    if (sortBtn && sortMenu) sortBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      var open = sortMenu.classList.contains("show");
      closeSort();
      if (!open) {
        sortMenu.classList.add("show");
        sortBtn.classList.add("show");
        sortBtn.setAttribute("aria-expanded", "true");
      }
    });

    function applySort(sortBy) {
      if (!listContainer) return;
      var cards = Array.prototype.slice.call(listContainer.querySelectorAll(".property-card-wrapper, .offplan-card-wrap"));
      if (!cards.length) return;
      if (!originalOrder) originalOrder = cards.slice();
      var key = null, dir = 0;
      if (sortBy === "price-desc" || sortBy === "price-asc") { key = "price"; dir = sortBy === "price-desc" ? -1 : 1; }
      else if (sortBy === "bed-desc" || sortBy === "bed-asc") { key = "bed"; dir = sortBy === "bed-desc" ? -1 : 1; }
      else if (sortBy === "size-desc" || sortBy === "size-asc") { key = "size"; dir = sortBy === "size-desc" ? -1 : 1; }
      if (key) {
        var normal = cards.filter(function (c) { return !c.classList.contains("more-box"); });
        var boxes = cards.filter(function (c) { return c.classList.contains("more-box"); });
        normal.sort(function (a, b) {
          var va = sortValue(a, key), vb = sortValue(b, key);
          if (va === vb) return 0;
          return dir < 0 ? vb - va : va - vb;
        });
        cards = normal.concat(boxes);
      } else {
        cards = originalOrder.slice();
      }
      for (var i = 0; i < cards.length; i++) listContainer.appendChild(cards[i]);
    }

    if (sortMenu) sortMenu.addEventListener("click", function (e) {
      var opt = e.target && e.target.closest ? e.target.closest(".sort-option") : null;
      if (!opt) return;
      applySort(opt.getAttribute("data-sort") || "most-recent");
      if (sortLabel) sortLabel.textContent = opt.textContent;
      closeSort();
    });
  });

  document.addEventListener("click", function (e) {
    Array.prototype.forEach.call(document.querySelectorAll(".sort-dropdown"), function (root) {
      if (root.contains(e.target)) return;
      var m = root.querySelector(".sort-menu");
      var b = root.querySelector(".sort-section");
      if (m) m.classList.remove("show");
      if (b) { b.classList.remove("show"); b.setAttribute("aria-expanded", "false"); }
    });
  });

  /* ---------- Map / Grid toggle (listing results) ---------- */

  var resultsRoot = document.querySelector("#property-page-1") || document.querySelector(".property-list-section");
  var mapButtons = Array.prototype.slice.call(document.querySelectorAll(".map-button"));
  var mapAreaValue = (function () {
    var inp = document.querySelector("#search-input-field");
    var v = inp ? (inp.value || "").trim() : "";
    return v || "Pakistan";
  })();
  var MAP_POPUP_SLUGS = {};

  function setCardListView(on) {
    if (!resultsRoot) return;
    Array.prototype.slice.call(resultsRoot.querySelectorAll(".property-card")).forEach(function (card) {
      card.classList.toggle("list-view", on);
    });
  }

  function setActiveMapButtons(which) {
    mapButtons.forEach(function (b) {
      var isGrid = b.classList.contains("list-grid");
      var active = (which === "grid" && isGrid) || (which === "map" && !isGrid);
      b.classList.toggle("active", active);
    });
  }

  var mapPanel = null;
  var returnBar = null;
  function ensureReturnBar() {
    if (returnBar || !mapPanel) return returnBar;
    returnBar = document.createElement("div");
    returnBar.className = "map-return-bar";
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "button map-button";
    btn.textContent = "Back to Property List";
    btn.addEventListener("click", showList);
    returnBar.appendChild(btn);
    mapPanel.parentNode.insertBefore(returnBar, mapPanel.nextSibling);
    return returnBar;
  }
  function showMap() {
    if (!resultsRoot) return;
    if (mapPanel) {
      mapPanel.style.display = "";
    } else {
      mapPanel = document.createElement("div");
      mapPanel.className = "listing-map-panel";
      mapPanel.innerHTML = '<iframe src="https://maps.google.com/maps?q=' + encodeURIComponent(mapAreaValue)
        + '&z=11&output=embed" style="border:0;width:100%;height:100%;min-height:420px" loading="lazy" title="Property locations map"></iframe>';
      resultsRoot.parentNode.insertBefore(mapPanel, resultsRoot);
    }
    ensureReturnBar();
    if (returnBar) returnBar.style.display = "";
    resultsRoot.classList.add("map-open");
    resultsRoot.classList.add("list-view");
    setCardListView(true);
    setActiveMapButtons("map");
  }
  function hideMap() {
    if (mapPanel) mapPanel.style.display = "none";
    if (returnBar) returnBar.style.display = "none";
    if (resultsRoot) resultsRoot.classList.remove("map-open");
    setActiveMapButtons(null);
  }
  function showGrid() {
    hideMap();
    if (!resultsRoot) return;
    resultsRoot.classList.remove("list-view");
    setCardListView(false);
    setActiveMapButtons("grid");
  }
  function showList() {
    hideMap();
    if (!resultsRoot) return;
    resultsRoot.classList.add("list-view");
    setCardListView(true);
    setActiveMapButtons(null);
  }

  mapButtons.forEach(function (b) {
    var isGrid = b.classList.contains("list-grid");
    b.addEventListener("click", function () {
      if (isGrid) {
        if (b.classList.contains("active") && resultsRoot && !resultsRoot.classList.contains("list-view")) {
          showList();
        } else {
          showGrid();
        }
      } else {
        if (b.classList.contains("active") && resultsRoot && resultsRoot.classList.contains("map-open")) {
          showList();
        } else {
          showMap();
        }
      }
    });
  });

  /* ---------- [mortgage calculator removed] ---------- */
})();