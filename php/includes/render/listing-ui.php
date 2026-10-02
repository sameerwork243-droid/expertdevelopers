<?php
// listing-ui.php — FilterDropdown / TypeSelect ports
// (src/components/listing-ui.tsx).

require_once __DIR__ . '/../functions.php';

function filter_dropdown(string $label, array $options, string $className = '', string $btnClass = 'custom-dropdown-toggle filter-dropdown-toggle dropdown-toggle'): string
{
    $html = '<div class="filter-dropdown dropdown' . ($className ? ' ' . $className : '') . '">';
    $html .= '<button class="' . esc($btnClass) . '" type="button" aria-expanded="false">';
    $html .= '<span><span>' . esc($label) . '</span></span>';
    $html .= '<svg class="arrow-down-icon" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13 5.5L8 10.5L3 5.5" stroke="#fff" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
    $html .= '</button>';
    $html .= '<div class="dropdown-menu">';
    foreach ($options as $o) {
        $html .= '<a class="dropdown-item" href="' . esc($o['href']) . '">' . esc($o['label']) . '</a>';
    }
    $html .= '</div></div>';
    return $html;
}

function type_select(array $options, string $label = 'Property Type'): string
{
    $html = '<div class="react-select-wrap filter-select building-type-select">';
    $html .= '<div class="react-select css-b62m3t-container">';
    $html .= '<div class="react-select__control css-14qho42-control">';
    $html .= '<div class="react-select__value-container react-select__value-container--has-value css-hlgwow">';
    $html .= '<div class="react-select__single-value css-1ubv46r-singleValue">' . esc($label) . '</div>';
    $html .= '</div>';
    $html .= '<div class="react-select__indicators css-1wy0on6">';
    $html .= '<span class="react-select__indicator-separator css-1uei4ir-indicatorSeparator"></span>';
    $html .= '<div class="dropdown-indicator react-select__indicator react-select__dropdown-indicator css-15ctyzv-indicatorContainer" aria-hidden="true">';
    $html .= '<svg class="arrow-down-icon" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13 5.5L8 10.5L3 5.5" stroke="#07234B" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
    $html .= '</div></div></div>';
    $html .= '<div class="react-select__menu" style="display:none">';
    $html .= '<div class="react-select__menu-list">';
    foreach ($options as $o) {
        $html .= '<a class="react-select__option" href="' . esc($o['href']) . '">' . esc($o['label']) . '</a>';
    }
    $html .= '</div></div></div></div>';
    return $html;
}