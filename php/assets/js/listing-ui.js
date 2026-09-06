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

  var sortRoot = document.querySelector(".sort-dropdown");
  if (sortRoot) {
    var sortBtn = sortRoot.querySelector(".sort-section");
    var sortMenu = sortRoot.querySelector(".sort-menu");
    var sortLabel = sortRoot.querySelector(".text.button-text span");
    var listContainer = document.querySelector("#property-page-1") || document.querySelector(".property-list-section.list-view");
    var originalOrder = null;

    function closeSort() {
      if (sortMenu) sortMenu.classList.remove("show");
      if (sortBtn) sortBtn.setAttribute("aria-expanded", "false");
    }

    if (sortBtn && sortMenu) sortBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      var open = sortMenu.classList.contains("show");
      closeSort();
      if (!open) {
        sortMenu.classList.add("show");
        sortBtn.setAttribute("aria-expanded", "true");
      }
    });

    function applySort(sortBy) {
      if (!listContainer) return;
      var cards = Array.prototype.slice.call(listContainer.querySelectorAll(".property-card-wrapper"));
      if (!cards.length) return;
      if (!originalOrder) originalOrder = cards.slice();
      if (sortBy === "price-desc" || sortBy === "price-asc") {
        cards.sort(function (a, b) {
          var paEl = a.querySelector("[data-save-price]");
          var pbEl = b.querySelector("[data-save-price]");
          var pa = paEl ? (parseInt(paEl.getAttribute("data-save-price"), 10) || 0) : 0;
          var pb = pbEl ? (parseInt(pbEl.getAttribute("data-save-price"), 10) || 0) : 0;
          if (pa === pb) return 0;
          return sortBy === "price-desc" ? pb - pa : pa - pb;
        });
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

    document.addEventListener("click", function (e) {
      if (!sortRoot.contains(e.target)) closeSort();
    });
  }

  /* ---------- Map / Grid toggle (listing results) ---------- */

  var resultsRoot = document.querySelector("#property-page-1") || document.querySelector(".property-list-section");
  var mapButtons = Array.prototype.slice.call(document.querySelectorAll(".map-button"));
  var mapAreaValue = (function () {
    var inp = document.querySelector("#search-input-field");
    var v = inp ? (inp.value || "").trim() : "";
    return v || "Dubai";
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

  /* ---------- MortgageCalculator ---------- */

  document.querySelectorAll("#mortgage-calculator").forEach(function (calc) {
    var inputs = calc.querySelectorAll(".input-item");
    if (inputs.length < 4) return;
    var priceEl = inputs[0], downEl = inputs[1], rateEl = inputs[2], yearsEl = inputs[3];
    var monthlyEl = calc.querySelector(".pric-bx .results") || calc.querySelector(".left-side .results");
    var loanEl = calc.querySelector(".nn-bt .one-bk .con");
    var durationEl = loanEl ? calc.querySelectorAll(".nn-bt .one-bk .con")[1] : null;
    var fixTxt = calc.querySelector(".fix-txt");
    var label = calc.querySelector(".input-section .label");
    var currency = fixTxt ? fixTxt.textContent.trim() : (label ? (label.textContent.match(/\(([^)]+)\)/) || [null, "AED"])[1].trim() : "AED");

    function compute() {
      var p = parseFloat((priceEl.value || "").replace(/,/g, "")) || 0;
      var down = parseFloat(downEl.value) || 0;
      var rate = parseFloat(rateEl.value) || 0;
      var years = parseFloat(yearsEl.value) || 0;
      var principal = p * (1 - down / 100);
      var r = rate / 12 / 100;
      var n = years * 12;
      var monthly = p > 0 && r > 0 && n > 0 ? (principal * r) / (1 - Math.pow(1 + r, -n)) : 0;
      var fmt = function (v) { return v ? Math.round(v).toLocaleString("en-US") : "0"; };
      if (monthlyEl) monthlyEl.textContent = currency + " " + fmt(monthly) + " /month";
      if (loanEl) loanEl.textContent = currency + " " + fmt(principal);
      if (durationEl) durationEl.textContent = years + " Years";
    }
    Array.prototype.forEach.call(inputs, function (inp) {
      inp.addEventListener("input", compute);
      inp.addEventListener("change", compute);
    });
  });
})();