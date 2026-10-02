export const PREF_CURRENCIES: readonly string[] = [
  "AED", "AFN", "ALL", "AMD", "ANG", "AOA", "ARS", "AUD", "AWG", "AZN",
  "BAM", "BBD", "BDT", "BGN", "BHD", "BIF", "BMD", "BND", "BOB", "BRL",
  "BSD", "BTN", "BWP", "BYN", "BZD", "CAD", "CDF", "CHF", "CLP", "CNY",
  "COP", "CRC", "CUP", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD", "EGP",
  "ERN", "ETB", "EUR", "FJD", "FKP", "GBP", "GEL", "GHS", "GIP", "GMD",
  "GNF", "GTQ", "GYD", "HKD", "HNL", "HTG", "HUF", "IDR", "ILS", "INR",
  "IQD", "IRR", "ISK", "JMD", "JOD", "JPY", "KES", "KGS", "KHR", "KMF",
  "KRW", "KWD", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD", "LSL", "LYD",
  "MAD", "MDL", "MGA", "MKD", "MMK", "MNT", "MOP", "MRU", "MUR", "MVR",
  "MWK", "MXN", "MYR", "MZN", "NAD", "NGN", "NIO", "NOK", "NPR", "NZD",
  "OMR", "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR", "RON",
  "RSD", "RUB", "RWF", "SAR", "SBD", "SCR", "SDG", "SEK", "SGD", "SHP",
  "SLE", "SOS", "SRD", "SSP", "STN", "SYP", "SZL", "THB", "TJS", "TMT",
  "TND", "TOP", "TRY", "TTD", "TWD", "TZS", "UAH", "UGX", "USD", "UYU",
  "UZS", "VES", "VND", "VUV", "WST", "XAF", "XCD", "XOF", "XPF", "YER",
  "ZAR", "ZMW", "ZWL",
];
export type PrefCurrency = string;

export const PREF_UNITS = ["Sqft", "Sqm"] as const;
export type PrefUnit = (typeof PREF_UNITS)[number];

export const WORLD_CURRENCIES: Record<string, string> = {
  AED: "UAE Dirham", AFN: "Afghan Afghani", ALL: "Albanian Lek", AMD: "Armenian Dram",
  ANG: "Netherlands Antillean Guilder", AOA: "Angolan Kwanza", ARS: "Argentine Peso",
  AUD: "Australian Dollar", AWG: "Aruban Florin", AZN: "Azerbaijani Manat",
  BAM: "Bosnia-Herzegovina Convertible Mark", BBD: "Barbadian Dollar", BDT: "Bangladeshi Taka",
  BGN: "Bulgarian Lev", BHD: "Bahraini Dinar", BIF: "Burundian Franc", BMD: "Bermudian Dollar",
  BND: "Brunei Dollar", BOB: "Bolivian Boliviano", BRL: "Brazilian Real", BSD: "Bahamian Dollar",
  BTN: "Bhutanese Ngultrum", BWP: "Botswana Pula", BYN: "Belarusian Ruble", BZD: "Belize Dollar",
  CAD: "Canadian Dollar", CDF: "Congolese Franc", CHF: "Swiss Franc", CLP: "Chilean Peso",
  CNY: "Chinese Yuan", COP: "Colombian Peso", CRC: "Costa Rican Colon", CUP: "Cuban Peso",
  CVE: "Cape Verdean Escudo", CZK: "Czech Koruna", DJF: "Djiboutian Franc", DKK: "Danish Krone",
  DOP: "Dominican Peso", DZD: "Algerian Dinar", EGP: "Egyptian Pound", ERN: "Eritrean Nakfa",
  ETB: "Ethiopian Birr", EUR: "Euro", FJD: "Fijian Dollar", FKP: "Falkland Islands Pound",
  GBP: "British Pound", GEL: "Georgian Lari", GHS: "Ghanaian Cedi", GIP: "Gibraltar Pound",
  GMD: "Gambian Dalasi", GNF: "Guinean Franc", GTQ: "Guatemalan Quetzal", GYD: "Guyanese Dollar",
  HKD: "Hong Kong Dollar", HNL: "Honduran Lempira", HTG: "Haitian Gourde", HUF: "Hungarian Forint",
  IDR: "Indonesian Rupiah", ILS: "Israeli New Shekel", INR: "Indian Rupee", IQD: "Iraqi Dinar",
  IRR: "Iranian Rial", ISK: "Icelandic Krona", JMD: "Jamaican Dollar", JOD: "Jordanian Dinar",
  JPY: "Japanese Yen", KES: "Kenyan Shilling", KGS: "Kyrgystani Som", KHR: "Cambodian Riel",
  KMF: "Comorian Franc", KRW: "South Korean Won", KWD: "Kuwaiti Dinar", KYD: "Cayman Islands Dollar",
  KZT: "Kazakhstani Tenge", LAK: "Laotian Kip", LBP: "Lebanese Pound", LKR: "Sri Lankan Rupee",
  LRD: "Liberian Dollar", LSL: "Lesotho Loti", LYD: "Libyan Dinar", MAD: "Moroccan Dirham",
  MDL: "Moldovan Leu", MGA: "Malagasy Ariary", MKD: "Macedonian Denar", MMK: "Myanmar Kyat",
  MNT: "Mongolian Tugrik", MOP: "Macanese Pataca", MRU: "Mauritanian Ouguiya", MUR: "Mauritian Rupee",
  MVR: "Maldivian Rufiyaa", MWK: "Malawian Kwacha", MXN: "Mexican Peso", MYR: "Malaysian Ringgit",
  MZN: "Mozambican Metical", NAD: "Namibian Dollar", NGN: "Nigerian Naira", NIO: "Nicaraguan Cordoba",
  NOK: "Norwegian Krone", NPR: "Nepalese Rupee", NZD: "New Zealand Dollar", OMR: "Omani Rial",
  PAB: "Panamanian Balboa", PEN: "Peruvian Sol", PGK: "Papua New Guinean Kina", PHP: "Philippine Peso",
  PKR: "Pakistani Rupee", PLN: "Polish Zloty", PYG: "Paraguayan Guarani", QAR: "Qatari Rial",
  RON: "Romanian Leu", RSD: "Serbian Dinar", RUB: "Russian Ruble", RWF: "Rwandan Franc",
  SAR: "Saudi Riyal", SBD: "Solomon Islands Dollar", SCR: "Seychellois Rupee", SDG: "Sudanese Pound",
  SEK: "Swedish Krona", SGD: "Singapore Dollar", SHP: "Saint Helena Pound", SLE: "Sierra Leonean Leone",
  SOS: "Somali Shilling", SRD: "Surinamese Dollar", SSP: "South Sudanese Pound",
  STN: "Sao Tome and Principe Dobra", SYP: "Syrian Pound", SZL: "Swazi Lilangeni", THB: "Thai Baht",
  TJS: "Tajikistani Somoni", TMT: "Turkmenistani Manat", TND: "Tunisian Dinar", TOP: "Tongan Paanga",
  TRY: "Turkish Lira", TTD: "Trinidad and Tobago Dollar", TWD: "New Taiwan Dollar",
  TZS: "Tanzanian Shilling", UAH: "Ukrainian Hryvnia", UGX: "Ugandan Shilling", USD: "US Dollar",
  UYU: "Uruguayan Peso", UZS: "Uzbekistani Som", VES: "Venezuelan Bolivar", VND: "Vietnamese Dong",
  VUV: "Vanuatu Vatu", WST: "Samoan Tala", XAF: "Central African CFA Franc",
  XCD: "East Caribbean Dollar", XOF: "West African CFA Franc", XPF: "CFP Franc", YER: "Yemeni Rial",
  ZAR: "South African Rand", ZMW: "Zambian Kwacha", ZWL: "Zimbabwean Dollar",
};

export const FALLBACK_RATES: Record<string, number> = {
  AED: 1, AFN: 19.1, ALL: 25.5, AMD: 105.5, ANG: 0.49, AOA: 231, ARS: 250, AUD: 0.417,
  AWG: 0.489, AZN: 0.462, BAM: 0.491, BBD: 0.545, BDT: 32.3, BGN: 0.491, BHD: 0.1027, BIF: 779,
  BMD: 0.2724, BND: 0.366, BOB: 1.88, BRL: 1.49, BSD: 0.2724, BTN: 22.7, BWP: 3.71, BYN: 0.866,
  BZD: 0.549, CAD: 0.371, CDF: 775, CHF: 0.241, CLP: 253, CNY: 1.945, COP: 1082, CRC: 141.6,
  CUP: 27.24, CVE: 27.7, CZK: 6.32, DJF: 48.4, DKK: 1.87, DOP: 16.2, DZD: 36.6, EGP: 13.15,
  ERN: 4.086, ETB: 29.8, EUR: 0.2517, FJD: 0.616, FKP: 0.214, GBP: 0.2142, GEL: 0.72, GHS: 3.42,
  GIP: 0.214, GMD: 18.3, GNF: 2340, GTQ: 2.11, GYD: 56.9, HKD: 2.126, HNL: 6.72, HTG: 36.1,
  HUF: 96.4, IDR: 4350, ILS: 1.002, INR: 22.68, IQD: 356.5, IRR: 11450, ISK: 37.7, JMD: 42.4,
  JOD: 0.193, JPY: 40.5, KES: 35.1, KGS: 23.5, KHR: 1105, KMF: 123.9, KRW: 363, KWD: 0.0833,
  KYD: 0.227, KZT: 128, LAK: 5905, LBP: 24400, LKR: 79.2, LRD: 52.6, LSL: 4.93, LYD: 1.31,
  MAD: 2.7, MDL: 4.85, MGA: 1225, MKD: 15.6, MMK: 571.9, MNT: 939, MOP: 2.19, MRU: 10.8,
  MUR: 12.6, MVR: 4.21, MWK: 472, MXN: 4.68, MYR: 1.202, MZN: 17.4, NAD: 4.93, NGN: 415,
  NIO: 10.02, NOK: 2.91, NPR: 36.3, NZD: 0.446, OMR: 0.1048, PAB: 0.2724, PEN: 1.017, PGK: 1.04,
  PHP: 15.4, PKR: 76, PLN: 1.082, PYG: 2088, QAR: 0.991, RON: 1.253, RSD: 29.8, RUB: 25.2,
  RWF: 3560, SAR: 1.0215, SBD: 2.27, SCR: 3.63, SDG: 163.4, SEK: 2.85, SGD: 0.365, SHP: 0.214,
  SLE: 6.17, SOS: 155.6, SRD: 8.95, SSP: 217.9, STN: 6.17, SYP: 357, SZL: 4.93, THB: 9.34,
  TJS: 2.96, TMT: 0.953, TND: 0.854, TOP: 0.645, TRY: 9.4, TTD: 1.85, TWD: 8.73, TZS: 644,
  UAH: 10.6, UGX: 1008, USD: 0.2724, UYU: 11.4, UZS: 3417, VES: 10.34, VND: 6745, VUV: 32.4,
  WST: 0.754, XAF: 165.1, XCD: 0.736, XOF: 165.1, XPF: 30.04, YER: 68.1, ZAR: 4.93, ZMW: 6.72,
  ZWL: 87.8,
};

/** Live base rates (1 AED = X); starts from FALLBACK_RATES, refreshed by loadLiveRates(). */
export const RATES: Record<string, number> = { ...FALLBACK_RATES };

export const SYMBOLS: Record<string, string> = {
  AED: "AED ", USD: "$", EUR: "€", GBP: "£", JPY: "¥", CNY: "¥", INR: "₹", RUB: "₽",
  KRW: "₩", CAD: "C$", AUD: "A$", SGD: "S$", HKD: "HK$", NZD: "NZ$", CHF: "CHF ",
  SEK: "kr ", NOK: "kr ", DKK: "kr ", SAR: "SAR ", KWD: "KD ", QAR: "QR ", BHD: "BD ", OMR: "OMR ",
};
export const UNIT_SUFFIX: Record<PrefUnit, string> = { Sqft: "sq ft", Sqm: "sq m" };

export interface SitePrefs {
  cur: PrefCurrency;
  unit: PrefUnit;
}

export const DEFAULT_PREFS: SitePrefs = { cur: "AED", unit: "Sqft" };

const FEATURED_CURRENCIES: readonly string[] = [
  "AED", "USD", "EUR", "GBP", "INR", "PKR", "BDT", "SAR", "QAR", "KWD", "BHD", "OMR",
  "AUD", "CAD", "SGD", "CNY", "JPY", "HKD", "RUB", "TRY", "MYR", "THB", "PHP", "IDR",
  "NZD", "EGP", "LKR",
];

/** Popular currencies first, then the rest alphabetically. */
export const FOOTER_CURRENCIES: { label: string; code: string }[] = (() => {
  const present = new Set(Object.keys(WORLD_CURRENCIES));
  const order: string[] = [];
  for (const code of FEATURED_CURRENCIES) {
    if (present.has(code)) order.push(code);
  }
  for (const code of present) {
    if (!FEATURED_CURRENCIES.includes(code)) order.push(code);
  }
  order.sort((a, b) => {
    const fa = FEATURED_CURRENCIES.includes(a) ? 0 : 1;
    const fb = FEATURED_CURRENCIES.includes(b) ? 0 : 1;
    if (fa !== fb) return fa - fb;
    return a < b ? -1 : a > b ? 1 : 0;
  });
  return order.map((code) => ({ label: `${code} - ${WORLD_CURRENCIES[code]}`, code }));
})();

export const FOOTER_UNITS: { label: string; code: PrefUnit }[] = [
  { label: "SQ FT", code: "Sqft" },
  { label: "SQ M", code: "Sqm" },
];

export function onClient(): boolean {
  return typeof document !== "undefined" && typeof window !== "undefined";
}

export function hasRate(code: string): boolean {
  return RATES[code] !== undefined;
}

export function symOf(code: string): string {
  return SYMBOLS[code] ?? (code === "AED" ? "AED " : `${code} `);
}

export function readPrefs(): SitePrefs {
  const start: SitePrefs = { ...DEFAULT_PREFS };
  if (!onClient()) return start;
  const m = document.cookie.match(/(?:^|;\s*)zoya_prefs=([^;]*)/);
  if (!m) return start;
  const parts = decodeURIComponent(m[1]).split("|");
  if (parts[0] && hasRate(parts[0])) start.cur = parts[0];
  if (parts[1] && (PREF_UNITS as readonly string[]).includes(parts[1])) start.unit = parts[1] as PrefUnit;
  return start;
}

export function writePrefs(p: SitePrefs): void {
  if (!onClient()) return;
  document.cookie = `zoya_prefs=${encodeURIComponent(`${p.cur}|${p.unit}`)}; max-age=604800; path=/; SameSite=Lax`;
}

/** Refresh RATES from the free open.er-api.com endpoint, then re-apply any DOM conversions. */
export async function loadLiveRates(): Promise<void> {
  if (!onClient()) return;
  try {
    const res = await fetch("https://open.er-api.com/v6/latest/AED");
    const j: { result?: string; rates?: Record<string, number> } = await res.json();
    if (j && j.result === "success" && j.rates) {
      for (const k of Object.keys(j.rates)) {
        const v = typeof j.rates[k] === "number" ? j.rates[k] : parseFloat(String(j.rates[k]));
        if (Number.isFinite(v) && v > 0) RATES[k] = v;
      }
      applyDom();
    }
  } catch {
    // keep fallback rates
  }
}

export function formatPrice(aed: number, prefs: SitePrefs, qual = ""): string {
  const sym = symOf(prefs.cur);
  const val = Math.round(aed * (RATES[prefs.cur] ?? 1)).toLocaleString("en-US");
  return `${sym}${val}${qual ? ` ${qual}` : ""}`;
}

export function formatArea(sqft: number, prefs: SitePrefs): string {
  const val = (prefs.unit === "Sqm" ? sqft * 0.09290304 : sqft).toLocaleString("en-US", { maximumFractionDigits: 0 });
  return `${val} ${UNIT_SUFFIX[prefs.unit]}`;
}

export function formatPerSqFt(aed: number, sqft: number, prefs: SitePrefs): string {
  const sym = symOf(prefs.cur);
  const area = prefs.unit === "Sqm" ? sqft * 0.09290304 : sqft;
  const val = Math.round((aed / area) * (RATES[prefs.cur] ?? 1)).toLocaleString("en-US");
  return `${sym}${val} / ${UNIT_SUFFIX[prefs.unit]}`;
}

export function applyDom(): void {
  if (!onClient()) return;
  const prefs = readPrefs();
  document.querySelectorAll("[data-aed], [data-sqft]").forEach((n) => {
    const el = n as HTMLElement;
    const aed = el.getAttribute("data-aed");
    const sqft = el.getAttribute("data-sqft");
    const hasAed = aed != null && aed !== "";
    const hasSqft = sqft != null && sqft !== "";
    if (hasAed && hasSqft) {
      el.textContent = formatPerSqFt(parseInt(aed, 10), parseInt(sqft, 10), prefs);
      return;
    }
    if (hasAed) {
      el.textContent = formatPrice(parseInt(aed, 10), prefs, el.getAttribute("data-qual") || "");
      return;
    }
    if (hasSqft) {
      el.textContent = formatArea(parseInt(sqft, 10), prefs);
    }
  });
}

type Listener = (p: SitePrefs) => void;
const listeners = new Set<Listener>();

export function subscribePrefs(fn: Listener): () => void {
  listeners.add(fn);
  return () => {
    listeners.delete(fn);
  };
}

export function emitPrefs(p: SitePrefs): void {
  listeners.forEach((fn) => fn(p));
}