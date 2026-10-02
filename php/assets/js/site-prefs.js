(function () {
  'use strict';

  var FALLBACK_RATES = { AED: 1, USD: 0.273, EUR: 0.252, GBP: 0.216, SAR: 1.021, PKR: 76 };
  var RATES = {};
  var SYMS = {
    AED: 'AED ', USD: '$', EUR: '\u20AC', GBP: '\u00A3', JPY: '\u00A5', CNY: '\u00A5',
    INR: '\u20B9', RUB: '\u20BD', KRW: '\u20A9', CAD: 'C$', AUD: 'A$', SGD: 'S$',
    HKD: 'HK$', NZD: 'NZ$', CHF: 'CHF ', SEK: 'kr ', NOK: 'kr ', DKK: 'kr ',
    SAR: 'SAR ', KWD: 'KD ', QAR: 'QR ', BHD: 'BD ', OMR: 'OMR ', PKR: 'PKR '
  };
  var UNITS = { Sqft: 'sq ft', Sqm: 'sq m' };
  var SQM_FACTOR = 0.09290304;
  var PREF_NAME = 'expert_prefs';
  var CURRENCIES = [];
  var UNIT_OPTIONS = ['Sqft', 'Sqm'];

  function buildRates() {
    var src = (typeof window !== 'undefined' && window.EXPERT_RATES) ? window.EXPERT_RATES : null;
    RATES = {};
    for (var f in FALLBACK_RATES) RATES[f] = FALLBACK_RATES[f];
    if (src) {
      for (var k in src) {
        if (typeof src[k] === 'number' && src[k] > 0) RATES[k] = src[k];
      }
    }
    CURRENCIES = [];
    for (var r in RATES) CURRENCIES.push(r);
  }

  function hasRate(code) {
    return RATES[code] !== undefined;
  }

  function symOf(code) {
    return SYMS[code] !== undefined ? SYMS[code] : code + ' ';
  }

  function readPrefs() {
    var m = document.cookie.match(/(?:^|;\s*)expert_prefs=([^;]*)/);
    var cur = 'PKR';
    var unit = 'Sqft';
    if (m) {
      var parts = decodeURIComponent(m[1]).split('|');
      if (parts[0] && hasRate(parts[0])) cur = parts[0];
      if (parts[1] && UNIT_OPTIONS.indexOf(parts[1]) !== -1) unit = parts[1];
    }
    return { cur: cur, unit: unit };
  }

  function writePrefs(prefs) {
    var val = prefs.cur + '|' + prefs.unit;
    document.cookie = PREF_NAME + '=' + encodeURIComponent(val) +
      '; max-age=604800; path=/; SameSite=Lax';
  }

  function fmt(n) {
    return Math.round(n).toLocaleString('en-US');
  }

  function convertPrice(aed, cur) {
    return aed * (RATES[cur] !== undefined ? RATES[cur] : 1);
  }

  function roundRate(aed, cur) {
    return Math.round(convertPrice(aed, cur));
  }

  function convertArea(sqft, unit) {
    return unit === 'Sqm' ? sqft * SQM_FACTOR : sqft;
  }

  function apply() {
    var prefs = readPrefs();
    var cur = prefs.cur;
    var unit = prefs.unit;
    var sym = symOf(cur);
    var unitLabel = UNITS[unit];

    var nodes = document.querySelectorAll('[data-aed], [data-sqft]');
    for (var i = 0; i < nodes.length; i++) {
      var el = nodes[i];
      var aed = el.getAttribute('data-aed');
      var sqft = el.getAttribute('data-sqft');
      var hasAed = aed !== null && aed !== '';
      var hasSqft = sqft !== null && sqft !== '';

      if (hasAed && hasSqft) {
        var perSq = parseInt(aed, 10) / convertArea(parseInt(sqft, 10), unit);
        el.textContent = sym + fmt(perSq * RATES[cur]) + ' / ' + unitLabel;
        continue;
      }
      if (hasAed) {
        var price = roundRate(parseInt(aed, 10), cur);
        var qual = el.getAttribute('data-qual');
        el.textContent = sym + fmt(price) + (qual ? ' ' + qual : '');
        continue;
      }
      if (hasSqft) {
        var area = convertArea(parseInt(sqft, 10), unit);
        el.textContent = fmt(area) + ' ' + unitLabel;
      }
    }

    var selects = document.querySelectorAll('.js-footer-select');
    for (var s = 0; s < selects.length; s++) {
      hydrateSelect(selects[s], prefs);
    }

    var codes = document.querySelectorAll('.js-dev-currency-code');
    for (var c = 0; c < codes.length; c++) {
      codes[c].textContent = prefs.cur;
    }

    var ev;
    if (typeof window.CustomEvent === 'function') {
      ev = new CustomEvent('expert:prefs', { bubbles: true, detail: { cur: cur, unit: unit } });
    } else {
      ev = document.createEvent('CustomEvent');
      ev.initCustomEvent('expert:prefs', true, false, { cur: cur, unit: unit });
    }
    document.dispatchEvent(ev);
  }

  function hydrateSelect(select, prefs) {
    var role = select.getAttribute('data-role');
    if (role === 'currency') {
      var label = optionLabel(select, prefs.cur);
      updateSelected(select, label);
    } else if (role === 'unit') {
      var uLabel = optionLabel(select, prefs.unit);
      updateSelected(select, uLabel);
    }
  }

  function optionLabel(select, code) {
    var raw = select.getAttribute('data-options');
    if (!raw) return code;
    var opts = [];
    try { opts = JSON.parse(raw); } catch (e) { opts = []; }
    for (var i = 0; i < opts.length; i++) {
      if (opts[i].code === code) return opts[i].label;
    }
    return code;
  }

  function updateSelected(select, text) {
    var single = select.querySelector('.js-footer-selected');
    if (single) single.textContent = text;
  }

  function roleSelects(role) {
    return Array.prototype.slice.call(document.querySelectorAll('.js-footer-select[data-role="' + role + '"]'));
  }

  function menusAll() {
    return Array.prototype.slice.call(document.querySelectorAll('.react-select__menu'));
  }

  function closeMenus(except) {
    menusAll().forEach(function (m) {
      if (m !== except) {
        m.hidden = true;
        m.style.display = 'none';
      }
    });
  }

  function devPanels() {
    return Array.prototype.slice.call(document.querySelectorAll('.js-dev-to-open'));
  }

  function closePanels(exceptPanel) {
    devPanels().forEach(function (p) {
      if (p !== exceptPanel) p.hidden = true;
    });
    closeMenus();
  }

  function menuOf(control) {
    var m = control ? control.nextElementSibling : null;
    return m && m.classList.contains('react-select__menu') ? m : null;
  }

  function isMenuOpen(menu) {
    if (!menu) return false;
    if (menu.hidden) return false;
    if (menu.style.display === 'none') return false;
    return getComputedStyle(menu).display !== 'none';
  }

  function setMenuOpen(control, open) {
    var menu = menuOf(control);
    if (!menu) return;
    if (open) {
      closeMenus(menu);
      menu.hidden = false;
      menu.style.display = 'block';
      menu.querySelectorAll('.react-select__option').forEach(function (o) {
        if (!o.hasAttribute('tabindex')) o.setAttribute('tabindex', '-1');
        o.removeAttribute('data-focus');
      });
      var sel = menu.querySelector('.react-select__option--is-focused');
      if (sel) sel.setAttribute('data-focus', '1');
      var cRect = control.getBoundingClientRect();
      if (cRect.bottom + menu.offsetHeight > window.innerHeight - 8) {
        menu.classList.add('react-select__menu--top');
      } else {
        menu.classList.remove('react-select__menu--top');
      }
      control.classList.add('react-select__control--menu-is-open');
    } else {
      menu.hidden = true;
      menu.style.display = 'none';
      menu.classList.remove('react-select__menu--top');
      control.classList.remove('react-select__control--menu-is-open');
    }
  }

  function toggleSelect(control) {
    var owner = control.closest('.js-dev-to-open');
    closePanels(owner);
    setMenuOpen(control, !isMenuOpen(menuOf(control)));
  }

  function moveFocus(menu, delta) {
    var opts = Array.prototype.slice.call(menu.querySelectorAll('.react-select__option'));
    if (!opts.length) return;
    var idx = -1;
    for (var i = 0; i < opts.length; i++) {
      if (opts[i].hasAttribute('data-focus')) idx = i;
      opts[i].removeAttribute('data-focus');
    }
    var next = idx < 0 ? (delta > 0 ? 0 : opts.length - 1) : (idx + delta + opts.length) % opts.length;
    opts[next].setAttribute('data-focus', '1');
    if (opts[next].scrollIntoView) opts[next].scrollIntoView({ block: 'nearest' });
  }

  function highlighted(menu) {
    var foc = menu.querySelector('.react-select__option[data-focus="1"]');
    return foc
      ? foc
      : menu.querySelector('.react-select__option--is-focused') || menu.querySelector('.react-select__option');
  }

  function selectOption(opt) {
    var menu = opt.closest('.react-select__menu');
    var role = menu ? menu.getAttribute('data-role') : null;
    var code = opt.getAttribute('data-code');
    if (role && code) setPref(role, code);
    closePanels();
  }

  function setPref(role, code) {
    var prefs = readPrefs();
    if (role === 'currency') prefs.cur = code;
    if (role === 'unit') prefs.unit = code;
    writePrefs(prefs);
    var label = optionLabel(roleSelects(role)[0], code);
    roleSelects(role).forEach(function (sel) { updateSelected(sel, label); });
    apply();
  }

  function wireControl(control) {
    control.addEventListener('click', function (e) {
      if (e.target.closest('.react-select__option')) return;
      e.stopPropagation();
      toggleSelect(control);
    });
    control.addEventListener('keydown', function (e) {
      var menu = menuOf(control);
      if (e.key === 'Escape') {
        e.preventDefault();
        setMenuOpen(control, false);
        closePanels();
        return;
      }
      if (!menu || !isMenuOpen(menu)) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
          e.preventDefault();
          toggleSelect(control);
        }
        return;
      }
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        var foc = highlighted(menu);
        if (foc) selectOption(foc);
        else setMenuOpen(control, false);
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        moveFocus(menu, 1);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        moveFocus(menu, -1);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    buildRates();
    apply();

    document.querySelectorAll('.js-footer-select').forEach(wireControl);

    document.addEventListener('click', function (e) {
      var toggle = e.target.closest('.js-dev-toggle');
      if (toggle) {
        var dev = toggle.closest('.dev-to');
        var panel = dev ? dev.querySelector('.js-dev-to-open') : null;
        var open = panel ? !panel.hidden : false;
        closePanels();
        if (panel && !open) {
          panel.hidden = false;
          toggle.setAttribute('aria-expanded', 'true');
        } else if (panel) {
          toggle.setAttribute('aria-expanded', 'false');
        }
        return;
      }
      if (e.target.closest('.react-select__option')) {
        selectOption(e.target.closest('.react-select__option'));
        return;
      }
      closePanels();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closePanels();
    });
  });
})();