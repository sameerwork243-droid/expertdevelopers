<?php
// config.php — site-wide constants (port of src/lib/site.ts + src/lib/image.ts + src/lib/utils.ts)

define('APP_NAME', 'Expert Marketing & Developers');
define('APP_DOMAIN', 'expertmarketinganddevelopers.com');
define('APP_PHONE', '+92 312 5170194');
define('APP_PHONE_LINK', '+923125170194');
define('APP_WHATSAPP', '923125170194');
define('APP_EMAIL', 'buildersexpert42@gmail.com');
define('APP_MAP_QUERY', 'Main Chakri Road Near Punjab College, Rawalpindi, Pakistan');
define('APP_SOCIAL', ['facebook', 'instagram', 'tiktok']);

define('APP_BRAND_PRIMARY', '#EE7133');
define('APP_BRAND_NAVY', '#07234B');

define('APP_COPYRIGHT_YEAR', '2026');

// Bump on every CSS/JS deploy so browsers and the CDN fetch fresh assets.
define('APP_ASSET_VER', '2026092029');

define('SESSION_COOKIE', 'expert_session');
define('SESSION_TTL', 7 * 24 * 3600 * 1000);      // 7 days (ms — matches SESSION_TTL_MS)
define('REMEMBER_TTL', 30 * 24 * 3600 * 1000);    // 30 days (ms — matches REMEMBER_TTL_MS)

define('LISTING_PER_PAGE', 9);

define('CDN_BASE', 'https://d3h330vgpwpjr8.cloudfront.net');

// settings (src/lib/site.ts SETTINGS)
// Every currency of the world (ISO 4217). Prices are stored in AED and converted
// at live rates served by expert_rates() (functions.php), cached once per hour.
$SETTINGS_WORLD = [
    'AED' => 'UAE Dirham', 'AFN' => 'Afghan Afghani', 'ALL' => 'Albanian Lek',
    'AMD' => 'Armenian Dram', 'ANG' => 'Netherlands Antillean Guilder', 'AOA' => 'Angolan Kwanza',
    'ARS' => 'Argentine Peso', 'AUD' => 'Australian Dollar', 'AWG' => 'Aruban Florin',
    'AZN' => 'Azerbaijani Manat', 'BAM' => 'Bosnia-Herzegovina Convertible Mark', 'BBD' => 'Barbadian Dollar',
    'BDT' => 'Bangladeshi Taka', 'BGN' => 'Bulgarian Lev', 'BHD' => 'Bahraini Dinar',
    'BIF' => 'Burundian Franc', 'BMD' => 'Bermudian Dollar', 'BND' => 'Brunei Dollar',
    'BOB' => 'Bolivian Boliviano', 'BRL' => 'Brazilian Real', 'BSD' => 'Bahamian Dollar',
    'BTN' => 'Bhutanese Ngultrum', 'BWP' => 'Botswana Pula', 'BYN' => 'Belarusian Ruble',
    'BZD' => 'Belize Dollar', 'CAD' => 'Canadian Dollar', 'CDF' => 'Congolese Franc',
    'CHF' => 'Swiss Franc', 'CLP' => 'Chilean Peso', 'CNY' => 'Chinese Yuan',
    'COP' => 'Colombian Peso', 'CRC' => 'Costa Rican Colon', 'CUP' => 'Cuban Peso',
    'CVE' => 'Cape Verdean Escudo', 'CZK' => 'Czech Koruna', 'DJF' => 'Djiboutian Franc',
    'DKK' => 'Danish Krone', 'DOP' => 'Dominican Peso', 'DZD' => 'Algerian Dinar',
    'EGP' => 'Egyptian Pound', 'ERN' => 'Eritrean Nakfa', 'ETB' => 'Ethiopian Birr',
    'EUR' => 'Euro', 'FJD' => 'Fijian Dollar', 'FKP' => 'Falkland Islands Pound',
    'GBP' => 'British Pound', 'GEL' => 'Georgian Lari', 'GHS' => 'Ghanaian Cedi',
    'GIP' => 'Gibraltar Pound', 'GMD' => 'Gambian Dalasi', 'GNF' => 'Guinean Franc',
    'GTQ' => 'Guatemalan Quetzal', 'GYD' => 'Guyanese Dollar', 'HKD' => 'Hong Kong Dollar',
    'HNL' => 'Honduran Lempira', 'HTG' => 'Haitian Gourde', 'HUF' => 'Hungarian Forint',
    'IDR' => 'Indonesian Rupiah', 'ILS' => 'Israeli New Shekel', 'INR' => 'Indian Rupee',
    'IQD' => 'Iraqi Dinar', 'IRR' => 'Iranian Rial', 'ISK' => 'Icelandic Krona',
    'JMD' => 'Jamaican Dollar', 'JOD' => 'Jordanian Dinar', 'JPY' => 'Japanese Yen',
    'KES' => 'Kenyan Shilling', 'KGS' => 'Kyrgystani Som', 'KHR' => 'Cambodian Riel',
    'KMF' => 'Comorian Franc', 'KRW' => 'South Korean Won', 'KWD' => 'Kuwaiti Dinar',
    'KYD' => 'Cayman Islands Dollar', 'KZT' => 'Kazakhstani Tenge', 'LAK' => 'Laotian Kip',
    'LBP' => 'Lebanese Pound', 'LKR' => 'Sri Lankan Rupee', 'LRD' => 'Liberian Dollar',
    'LSL' => 'Lesotho Loti', 'LYD' => 'Libyan Dinar', 'MAD' => 'Moroccan Dirham',
    'MDL' => 'Moldovan Leu', 'MGA' => 'Malagasy Ariary', 'MKD' => 'Macedonian Denar',
    'MMK' => 'Myanmar Kyat', 'MNT' => 'Mongolian Tugrik', 'MOP' => 'Macanese Pataca',
    'MRU' => 'Mauritanian Ouguiya', 'MUR' => 'Mauritian Rupee', 'MVR' => 'Maldivian Rufiyaa',
    'MWK' => 'Malawian Kwacha', 'MXN' => 'Mexican Peso', 'MYR' => 'Malaysian Ringgit',
    'MZN' => 'Mozambican Metical', 'NAD' => 'Namibian Dollar', 'NGN' => 'Nigerian Naira',
    'NIO' => 'Nicaraguan Cordoba', 'NOK' => 'Norwegian Krone', 'NPR' => 'Nepalese Rupee',
    'NZD' => 'New Zealand Dollar', 'OMR' => 'Omani Rial', 'PAB' => 'Panamanian Balboa',
    'PEN' => 'Peruvian Sol', 'PGK' => 'Papua New Guinean Kina', 'PHP' => 'Philippine Peso',
    'PKR' => 'Pakistani Rupee', 'PLN' => 'Polish Zloty', 'PYG' => 'Paraguayan Guarani',
    'QAR' => 'Qatari Rial', 'RON' => 'Romanian Leu', 'RSD' => 'Serbian Dinar',
    'RUB' => 'Russian Ruble', 'RWF' => 'Rwandan Franc', 'SAR' => 'Saudi Riyal',
    'SBD' => 'Solomon Islands Dollar', 'SCR' => 'Seychellois Rupee', 'SDG' => 'Sudanese Pound',
    'SEK' => 'Swedish Krona', 'SGD' => 'Singapore Dollar', 'SHP' => 'Saint Helena Pound',
    'SLE' => 'Sierra Leonean Leone', 'SOS' => 'Somali Shilling', 'SRD' => 'Surinamese Dollar',
    'SSP' => 'South Sudanese Pound', 'STN' => 'Sao Tome and Principe Dobra', 'SYP' => 'Syrian Pound',
    'SZL' => 'Swazi Lilangeni', 'THB' => 'Thai Baht', 'TJS' => 'Tajikistani Somoni',
    'TMT' => 'Turkmenistani Manat', 'TND' => 'Tunisian Dinar', 'TOP' => 'Tongan Paanga',
    'TRY' => 'Turkish Lira', 'TTD' => 'Trinidad and Tobago Dollar', 'TWD' => 'New Taiwan Dollar',
    'TZS' => 'Tanzanian Shilling', 'UAH' => 'Ukrainian Hryvnia', 'UGX' => 'Ugandan Shilling',
    'USD' => 'US Dollar', 'UYU' => 'Uruguayan Peso', 'UZS' => 'Uzbekistani Som',
    'VES' => 'Venezuelan Bolivar', 'VND' => 'Vietnamese Dong', 'VUV' => 'Vanuatu Vatu',
    'WST' => 'Samoan Tala', 'XAF' => 'Central African CFA Franc', 'XCD' => 'East Caribbean Dollar',
    'XOF' => 'West African CFA Franc', 'XPF' => 'CFP Franc', 'YER' => 'Yemeni Rial',
    'ZAR' => 'South African Rand', 'ZMW' => 'Zambian Kwacha', 'ZWL' => 'Zimbabwean Dollar',
];
$SETTINGS_CURRENCIES = array_keys($SETTINGS_WORLD);
$SETTINGS_UNITS = ['Sqft', 'Sqm'];

// Popular currencies pinned to the top of the dropdown; the rest follow alphabetically.
$SETTINGS_FEATURED = ['AED', 'USD', 'EUR', 'GBP', 'INR', 'PKR', 'BDT', 'SAR', 'QAR', 'KWD', 'BHD', 'OMR', 'AUD', 'CAD', 'SGD', 'CNY', 'JPY', 'HKD', 'RUB', 'TRY', 'MYR', 'THB', 'PHP', 'IDR', 'NZD', 'EGP', 'LKR'];

// Last-resort base rates (1 AED = X) used only when the live-rate API is unreachable.
$SETTINGS_FALLBACK_RATES = [
    'AED' => 1, 'AFN' => 19.1, 'ALL' => 25.5, 'AMD' => 105.5, 'ANG' => 0.49, 'AOA' => 231,
    'ARS' => 250, 'AUD' => 0.417, 'AWG' => 0.489, 'AZN' => 0.462, 'BAM' => 0.491, 'BBD' => 0.545,
    'BDT' => 32.3, 'BGN' => 0.491, 'BHD' => 0.1027, 'BIF' => 779, 'BMD' => 0.2724, 'BND' => 0.366,
    'BOB' => 1.88, 'BRL' => 1.49, 'BSD' => 0.2724, 'BTN' => 22.7, 'BWP' => 3.71, 'BYN' => 0.866,
    'BZD' => 0.549, 'CAD' => 0.371, 'CDF' => 775, 'CHF' => 0.241, 'CLP' => 253, 'CNY' => 1.945,
    'COP' => 1082, 'CRC' => 141.6, 'CUP' => 27.24, 'CVE' => 27.7, 'CZK' => 6.32, 'DJF' => 48.4,
    'DKK' => 1.87, 'DOP' => 16.2, 'DZD' => 36.6, 'EGP' => 13.15, 'ERN' => 4.086, 'ETB' => 29.8,
    'EUR' => 0.2517, 'FJD' => 0.616, 'FKP' => 0.214, 'GBP' => 0.2142, 'GEL' => 0.72, 'GHS' => 3.42,
    'GIP' => 0.214, 'GMD' => 18.3, 'GNF' => 2340, 'GTQ' => 2.11, 'GYD' => 56.9, 'HKD' => 2.126,
    'HNL' => 6.72, 'HTG' => 36.1, 'HUF' => 96.4, 'IDR' => 4350, 'ILS' => 1.002, 'INR' => 22.68,
    'IQD' => 356.5, 'IRR' => 11450, 'ISK' => 37.7, 'JMD' => 42.4, 'JOD' => 0.193, 'JPY' => 40.5,
    'KES' => 35.1, 'KGS' => 23.5, 'KHR' => 1105, 'KMF' => 123.9, 'KRW' => 363, 'KWD' => 0.0833,
    'KYD' => 0.227, 'KZT' => 128, 'LAK' => 5905, 'LBP' => 24400, 'LKR' => 79.2, 'LRD' => 52.6,
    'LSL' => 4.93, 'LYD' => 1.31, 'MAD' => 2.70, 'MDL' => 4.85, 'MGA' => 1225, 'MKD' => 15.6,
    'MMK' => 571.9, 'MNT' => 939, 'MOP' => 2.19, 'MRU' => 10.8, 'MUR' => 12.6, 'MVR' => 4.21,
    'MWK' => 472, 'MXN' => 4.68, 'MYR' => 1.202, 'MZN' => 17.4, 'NAD' => 4.93, 'NGN' => 415,
    'NIO' => 10.02, 'NOK' => 2.91, 'NPR' => 36.3, 'NZD' => 0.446, 'OMR' => 0.1048, 'PAB' => 0.2724,
    'PEN' => 1.017, 'PGK' => 1.04, 'PHP' => 15.4, 'PKR' => 76, 'PLN' => 1.082, 'PYG' => 2088,
    'QAR' => 0.991, 'RON' => 1.253, 'RSD' => 29.8, 'RUB' => 25.2, 'RWF' => 3560, 'SAR' => 1.0215,
    'SBD' => 2.27, 'SCR' => 3.63, 'SDG' => 163.4, 'SEK' => 2.85, 'SGD' => 0.365, 'SHP' => 0.214,
    'SLE' => 6.17, 'SOS' => 155.6, 'SRD' => 8.95, 'SSP' => 217.9, 'STN' => 6.17, 'SYP' => 357,
    'SZL' => 4.93, 'THB' => 9.34, 'TJS' => 2.96, 'TMT' => 0.953, 'TND' => 0.854, 'TOP' => 0.645,
    'TRY' => 9.4, 'TTD' => 1.85, 'TWD' => 8.73, 'TZS' => 644, 'UAH' => 10.6, 'UGX' => 1008,
    'USD' => 0.2724, 'UYU' => 11.4, 'UZS' => 3417, 'VES' => 10.34, 'VND' => 6745, 'VUV' => 32.4,
    'WST' => 0.754, 'XAF' => 165.1, 'XCD' => 0.736, 'XOF' => 165.1, 'XPF' => 30.04, 'YER' => 68.1,
    'ZAR' => 4.93, 'ZMW' => 6.72, 'ZWL' => 87.8,
];

$SETTINGS_SYMBOLS = [
    'AED' => 'AED ', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥', 'CNY' => '¥',
    'INR' => '₹', 'RUB' => '₽', 'KRW' => '₩', 'CAD' => 'C$', 'AUD' => 'A$', 'SGD' => 'S$',
    'HKD' => 'HK$', 'NZD' => 'NZ$', 'CHF' => 'CHF ', 'SEK' => 'kr ', 'NOK' => 'kr ', 'DKK' => 'kr ',
    'SAR' => 'SAR ', 'KWD' => 'KD ', 'QAR' => 'QR ', 'BHD' => 'BD ', 'OMR' => 'OMR ',
];

// local CDN asset overrides (src/lib/image.ts LOCAL_OVERRIDES)
$LOCAL_OVERRIDES = [
    '2_15234debdc' => [
        'base' => '/images/banners/careers-banner.webp',
        'sizes' => ['376x' => '/images/banners/careers-banner-376.webp', '744x' => '/images/banners/careers-banner-744.webp'],
    ],
    'Group_image_016b5fb1e3' => [
        'base' => '/images/banners/about-video.webp',
        'sizes' => ['336x' => '/images/banners/about-video-336.webp', '696x' => '/images/banners/about-video-696.webp'],
    ],
];

// nav links (src/lib/site.ts NAV_LINKS)
$NAV_LINKS = [
    ['label' => 'Buy', 'href' => '/buy', 'children' => [
        ['label' => 'Apartments for sale', 'href' => '/buy/apartments-for-sale'],
        ['label' => 'Villas for sale', 'href' => '/buy/villas-for-sale'],
        ['label' => 'Townhouses for sale', 'href' => '/buy/townhouses-for-sale'],
        ['label' => 'Penthouse for sale', 'href' => '/buy/penthouses-for-sale'],
        ['label' => 'All properties for sale', 'href' => '/buy'],
    ]],
    ['label' => 'Rent', 'href' => '/let', 'children' => [
        ['label' => 'Apartments for rent', 'href' => '/let/apartments-for-rent'],
        ['label' => 'Villas for rent', 'href' => '/let/villas-for-rent'],
        ['label' => 'All properties to rent', 'href' => '/let'],
    ]],
    ['label' => 'New Projects', 'href' => '/new-projects', 'children' => [
        ['label' => 'All new projects', 'href' => '/new-projects'],
        ['label' => 'Under construction', 'href' => '/new-projects/type-under-construction'],
        ['label' => 'Ready to move in', 'href' => '/new-projects/type-ready'],
        ['label' => 'Across all developers', 'href' => '/developers'],
    ]],
    ['label' => 'Area Guides', 'href' => '/area-guides'],
    ['label' => 'Blog', 'href' => '/blog'],
    ['label' => 'Team', 'href' => '/team'],
    ['label' => 'Company', 'href' => '/about', 'children' => [
        ['label' => 'About us', 'href' => '/about'],
        ['label' => 'Our team', 'href' => '/team'],
        ['label' => 'Careers', 'href' => '/careers'],
        ['label' => 'Services', 'href' => '/services'],
        ['label' => 'Roadshow & events', 'href' => '/roadshow'],
        ['label' => 'Contact', 'href' => '/contact'],
    ]],
];
