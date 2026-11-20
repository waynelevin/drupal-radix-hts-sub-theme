<?php

/**
 * @file
 * Theme settings form for the hts theme.
 */

use Drupal\Core\Form\FormStateInterface;

require_once __DIR__ . '/includes/bootswatch.theme';
require_once __DIR__ . '/includes/navbar.theme';
require_once __DIR__ . '/includes/font.theme';

/**
 * Implements hook_form_FORM_ID_alter() for system_theme_settings.
 */
function hts_form_system_theme_settings_alter(array &$form, FormStateInterface $form_state): void {
  $options = hts_bootswatch_options();

  $form['hts_bootswatch'] = [
    '#type' => 'details',
    '#title' => t('Bootswatch theme'),
    '#open' => TRUE,
    '#weight' => -10,
  ];
  $form['hts_bootswatch']['bootswatch'] = [
    '#type' => 'select',
    '#title' => t('Colour and style theme'),
    '#options' => ['' => t('None (default Bootstrap)')] + $options,
    '#default_value' => (string) theme_get_setting('bootswatch', 'hts'),
    '#description' => t('Restyle the site with one of the free themes from <a href=":url" target="_blank" rel="noopener">Bootswatch</a>. Preview them there, or on the <a href=":styleguide">component styleguide</a> after saving. Most Bootswatch themes also load their web font from Google Fonts.', [
      ':url' => 'https://bootswatch.com/',
      ':styleguide' => '/styleguide',
    ]),
  ];
  if (!$options) {
    $form['hts_bootswatch']['bootswatch']['#disabled'] = TRUE;
    $form['hts_bootswatch']['bootswatch']['#description'] = t('No Bootswatch builds were found. Run <code>npm run build</code> in the theme folder to create them.');
  }

  $form['hts_bootswatch']['navbar_color'] = [
    '#type' => 'select',
    '#title' => t('Navbar colour'),
    '#options' => array_map(fn(array $option) => t($option[0]), hts_navbar_color_options()),
    '#default_value' => theme_get_setting('navbar_color', 'hts') ?? 'primary',
    '#description' => t('Background of the top navbar, using the colours of the selected Bootswatch theme.'),
  ];

  $form['hts_bootswatch']['font'] = [
    '#type' => 'select',
    '#title' => t('Font'),
    '#options' => array_map(fn(array $option) => t($option[0]), hts_font_options()),
    '#default_value' => (string) theme_get_setting('font', 'hts'),
    '#description' => t('Used for body text and headings. "Theme default" keeps the font of the selected Bootswatch theme. Web fonts are loaded from Google Fonts; preview them at <a href=":url" target="_blank" rel="noopener">fonts.google.com</a>.', [
      ':url' => 'https://fonts.google.com/',
    ]),
  ];

  $form['#submit'][] = 'hts_bootswatch_settings_submit';
}

/**
 * Form submit handler: rebuilds library definitions so the change applies.
 */
function hts_bootswatch_settings_submit(array &$form, FormStateInterface $form_state): void {
  \Drupal::service('library.discovery')->clear();
}
