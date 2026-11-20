<?php

declare(strict_types=1);

namespace Drupal\radix_styleguide\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;

/**
 * Renders every visual Radix component with sample content.
 */
final class StyleguideController extends ControllerBase {

  /**
   * Bootstrap theme colours.
   */
  private const COLORS = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

  /**
   * Builds the styleguide page.
   */
  public function page(): array {
    if (!\Drupal::service('theme_handler')->themeExists('radix')) {
      return ['#markup' => $this->t('The Radix theme must be installed to use the styleguide.')];
    }

    $sections = [
      'accordion' => ['Accordion', $this->accordion()],
      'alert' => ['Alert', $this->alerts()],
      'badge' => ['Badge', $this->badges()],
      'breadcrumb' => ['Breadcrumb', $this->breadcrumb()],
      'button' => ['Button', $this->buttons()],
      'button-group' => ['Button group', $this->buttonGroup()],
      'card' => ['Card', $this->cards()],
      'carousel' => ['Carousel', $this->carousel()],
      'close-button' => ['Close button', $this->closeButton()],
      'collapse' => ['Collapse', $this->collapse()],
      'dropdown' => ['Dropdown', $this->dropdown()],
      'figure' => ['Figure', $this->figure()],
      'heading' => ['Heading', $this->headings()],
      'image' => ['Image', $this->images()],
      'list-group' => ['List group', $this->listGroups()],
      'modal' => ['Modal', $this->modal()],
      'nav' => ['Nav', $this->navs()],
      'navbar' => ['Navbar', $this->navbar()],
      'offcanvas' => ['Offcanvas', $this->offcanvas()],
      'pagination' => ['Pagination', $this->pagination()],
      'progress' => ['Progress', $this->progress()],
      'spinner' => ['Spinner', $this->spinners()],
      'table' => ['Table', $this->table()],
      'toasts' => ['Toasts', $this->toasts()],
    ];

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['radix-styleguide', 'mb-5']],
      '#cache' => ['max-age' => 0],
    ];
    $build['intro'] = [
      '#markup' => Markup::create('<p class="lead">' . $this->t('Every visual component from the Radix base theme, rendered with sample content in the active theme. Page-structure components (html, page, node, region, field, form elements, views) are used by Drupal itself and are not shown here.') . '</p><p><a class="btn btn-outline-primary btn-sm" href="' . Url::fromRoute('radix_styleguide.icons')->toString() . '">' . $this->t('Browse all icons →') . '</a></p>'),
    ];

    $toc = [];
    foreach ($sections as $id => [$title]) {
      $toc[] = '<li class="nav-item"><a class="nav-link py-1 px-2" href="#sg-' . $id . '">' . Html::escape($title) . '</a></li>';
    }
    $build['toc'] = [
      '#markup' => Markup::create('<nav aria-label="Components" class="mb-4"><ul class="nav nav-pills flex-wrap small">' . implode('', $toc) . '</ul></nav>'),
    ];

    foreach ($sections as $id => [$title, $demos]) {
      $build[$id] = [
        '#type' => 'container',
        '#attributes' => ['id' => 'sg-' . $id, 'class' => ['mb-5', 'pt-3', 'border-top']],
        'title' => [
          '#markup' => Markup::create('<h2 class="h3 d-flex align-items-baseline gap-2">' . Html::escape($title) . ' <code class="fs-6 fw-normal">radix:' . $id . '</code></h2>'),
        ],
        'demos' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['p-3', 'border', 'rounded', 'bg-body-tertiary', 'd-flex', 'flex-column', 'gap-3']],
        ] + $demos,
      ];
    }
    return $build;
  }

  /**
   * Returns a component render array.
   */
  private function component(string $name, array $props = [], array $slots = []): array {
    return [
      '#type' => 'component',
      '#component' => 'radix:' . $name,
      '#props' => $props,
      '#slots' => $slots,
    ];
  }

  /**
   * Wraps render arrays in a flex row.
   */
  private function row(array $items, string $extra = ''): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => array_filter(['d-flex', 'flex-wrap', 'align-items-center', 'gap-2', $extra])],
    ] + $items;
  }

  /**
   * Returns a placeholder image as an inline SVG data URI.
   */
  private function placeholder(string $label, int $width = 800, int $height = 400, string $bg = '#6c757d'): string {
    $svg = sprintf(
      '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%2$d" viewBox="0 0 %1$d %2$d"><rect width="100%%" height="100%%" fill="%3$s"/><text x="50%%" y="50%%" fill="#fff" font-family="sans-serif" font-size="%4$d" text-anchor="middle" dominant-baseline="middle">%5$s</text></svg>',
      $width, $height, $bg, max(14, (int) ($height / 10)), Html::escape($label)
    );
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
  }

  /**
   * Builds a menu-style item, as Drupal menus pass them to Radix.
   */
  private function menuItem(string $title, string $path = '/styleguide', bool $active = FALSE, array $below = []): array {
    return [
      'title' => $title,
      'url' => Url::fromUserInput($path),
      'attributes' => new Attribute(),
      'in_active_trail' => $active,
      'is_expanded' => (bool) $below,
      'below' => $below,
    ];
  }

  private function accordion(): array {
    return [
      $this->component('accordion', [
        'id' => 'sg-accordion',
        'open_item_id' => 1,
        'items' => [
          ['title' => 'Accordion item #1', 'content' => 'This item is open by default. Content can be any text or markup.'],
          ['title' => 'Accordion item #2', 'content' => 'Click a heading to expand it and collapse the others.'],
          ['title' => 'Accordion item #3', 'content' => 'Set "flush" to remove the borders and background.'],
        ],
      ]),
    ];
  }

  private function alerts(): array {
    $build = [];
    foreach (self::COLORS as $color) {
      $build[$color] = $this->component('alert', ['type' => $color], ['content' => "A simple $color alert."]);
    }
    $build['heading'] = $this->component('alert', [
      'type' => 'success',
      'heading' => 'Well done!',
      'dismissible' => TRUE,
    ], ['content' => 'An alert with a heading and a close button.']);
    return $build;
  }

  private function badges(): array {
    $items = [];
    foreach (self::COLORS as $color) {
      $items[$color] = $this->component('badge', ['color' => $color], ['content' => ucfirst($color)]);
    }
    return [$this->row($items)];
  }

  private function breadcrumb(): array {
    return [
      $this->component('breadcrumb', [
        'breadcrumb' => [
          ['text' => 'Home', 'url' => '/'],
          ['text' => 'Library', 'url' => '/styleguide'],
          ['text' => 'Data', 'url' => ''],
        ],
      ]),
    ];
  }

  private function buttons(): array {
    $solid = $outline = [];
    foreach (array_merge(self::COLORS, ['link']) as $color) {
      $solid[$color] = $this->component('button', ['color' => $color], ['content' => ucfirst($color)]);
      if ($color !== 'link') {
        $outline[$color] = $this->component('button', ['color' => $color, 'outline' => TRUE], ['content' => ucfirst($color)]);
      }
    }
    return [
      'solid' => $this->row($solid),
      'outline' => $this->row($outline),
      'sizes' => $this->row([
        $this->component('button', ['color' => 'primary', 'size' => 'btn-lg'], ['content' => 'Large']),
        $this->component('button', ['color' => 'primary'], ['content' => 'Default']),
        $this->component('button', ['color' => 'primary', 'size' => 'btn-sm'], ['content' => 'Small']),
        $this->component('button', ['color' => 'secondary', 'disabled' => TRUE], ['content' => 'Disabled']),
        $this->component('button', ['color' => 'primary', 'button_html_tag' => 'a', 'url' => '#sg-button'], ['content' => 'Link as button']),
      ]),
    ];
  }

  private function buttonGroup(): array {
    $group = fn(string $color) => [
      ['content' => 'Left', 'color' => $color],
      ['content' => 'Middle', 'color' => $color],
      ['content' => 'Right', 'color' => $color],
    ];
    return [
      $this->row([
        $this->component('button-group', ['aria_label' => 'Basic example', 'items' => [$group('primary')]]),
        $this->component('button-group', ['aria_label' => 'Small', 'size' => 'sm', 'items' => [$group('secondary')]]),
        $this->component('button-group', ['aria_label' => 'Vertical', 'vertical' => TRUE, 'items' => [$group('dark')]]),
      ], 'gap-4'),
    ];
  }

  private function cards(): array {
    return [
      [
        '#type' => 'container',
        '#attributes' => ['class' => ['row', 'row-cols-1', 'row-cols-md-3', 'g-3']],
        'image' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['col']],
          'card' => $this->component('card', [
            'card_image_src' => $this->placeholder('Card image', 600, 300, '#0d6efd'),
            'card_image_alt' => 'Placeholder',
            'card_image_cap' => 'top',
            'card_title' => 'Card with image',
            'card_body' => 'Some quick example text to build on the card title.',
            'card_link_text' => 'Card link',
            'card_link_url' => '#sg-card',
            'card_border' => TRUE,
          ]),
        ],
        'header' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['col']],
          'card' => $this->component('card', [
            'card_header' => 'Featured',
            'card_title' => 'Header and footer',
            'card_subtitle' => 'Card subtitle',
            'card_body' => 'A card with a header, subtitle and footer.',
            'card_footer' => '2 days ago',
            'card_border' => TRUE,
          ]),
        ],
        'overlay' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['col']],
          'card' => $this->component('card', [
            'card_image_src' => $this->placeholder('Overlay', 600, 300, '#198754'),
            'card_image_alt' => 'Placeholder',
            'card_image_overlays' => TRUE,
            'card_title' => 'Image overlay',
            'card_body' => 'Text placed on top of the card image.',
            'card_border' => TRUE,
          ]),
        ],
      ],
    ];
  }

  private function carousel(): array {
    $items = [];
    foreach (['#0d6efd' => 'First slide', '#6f42c1' => 'Second slide', '#d63384' => 'Third slide'] as $bg => $label) {
      $items[] = [
        'image_src' => $this->placeholder($label, 1200, 400, $bg),
        'image_alt' => $label,
        'caption_title' => $label,
        'caption_content' => 'Sample caption for the ' . strtolower($label) . '.',
      ];
    }
    return [
      $this->component('carousel', ['id' => 'sg-carousel', 'carousel_theme' => 'light', 'items' => $items]),
    ];
  }

  private function closeButton(): array {
    // The disabled variant is omitted: Radix 6.0.9's close-button template
    // prints its "disabled" array directly, which throws a render error.
    return [
      $this->row([
        $this->component('close-button', ['close_button_attributes' => new Attribute()]),
      ]),
    ];
  }

  private function collapse(): array {
    return [
      $this->component('collapse', [
        'id' => 'sg-collapse',
        'button_html_tag' => 'button',
        'title' => 'Toggle content',
        'content' => 'This content is hidden until the button is clicked.',
      ]),
    ];
  }

  private function dropdown(): array {
    $items = [];
    foreach (['Action', 'Another action', 'Something else here'] as $label) {
      $items[] = [
        'link' => ['#type' => 'link', '#title' => $label, '#url' => Url::fromUserInput('/styleguide')],
        'in_active_trail' => FALSE,
      ];
    }
    return [
      $this->row([
        $this->component('dropdown', ['items' => $items, 'dropdown_button_content' => 'Dropdown button', 'dropdown_size' => '']),
        $this->component('dropdown', ['items' => $items, 'dropdown_button_content' => 'Secondary', 'dropdown_color' => 'secondary', 'dropdown_size' => '']),
        $this->component('dropdown', ['items' => $items, 'dropdown_button_content' => 'Split button', 'dropdown_color' => 'success', 'split_button' => TRUE, 'dropdown_size' => '']),
      ]),
    ];
  }

  private function figure(): array {
    return [
      $this->component('figure', [
        'image' => Markup::create('<img src="' . $this->placeholder('Figure', 400, 250) . '" class="figure-img img-fluid rounded" alt="Placeholder">'),
        'caption' => 'A caption for the image above.',
      ]),
    ];
  }

  private function headings(): array {
    $build = [];
    foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
      $build[$tag] = $this->component('heading', ['heading_html_tag' => $tag, 'content' => strtoupper($tag) . ' heading']);
    }
    $build['display'] = $this->component('heading', ['heading_html_tag' => 'div', 'display' => 'display-5', 'content' => 'Display heading']);
    return $build;
  }

  private function images(): array {
    return [
      $this->row([
        $this->component('image', ['src' => $this->placeholder('Rounded', 200, 200, '#0dcaf0'), 'alt' => 'Rounded', 'rounded' => TRUE]),
        $this->component('image', ['src' => $this->placeholder('Thumbnail', 200, 200, '#fd7e14'), 'alt' => 'Thumbnail', 'thumbnails' => TRUE]),
        $this->component('image', ['src' => $this->placeholder('Responsive', 200, 200, '#20c997'), 'alt' => 'Responsive', 'responsive' => TRUE]),
      ], 'gap-3'),
    ];
  }

  private function listGroups(): array {
    $items = array_map(fn($v) => ['value' => $v], ['An item', 'A second item', 'A third item']);
    return [
      $this->row([
        $this->component('list-group', ['list_group_items' => $items]),
        $this->component('list-group', ['list_group_items' => $items, 'numbered' => TRUE]),
        $this->component('list-group', ['list_group_items' => $items, 'list_group_item_variants' => 'primary']),
      ], 'align-items-start gap-4'),
      $this->component('list-group', ['list_group_items' => $items, 'horizontal' => TRUE]),
    ];
  }

  private function modal(): array {
    $trigger = (new Attribute())
      ->setAttribute('type', 'button')
      ->setAttribute('data-bs-toggle', 'modal')
      ->setAttribute('data-bs-target', '#sg-modal');
    return [
      $this->row([
        $this->component('button', ['color' => 'primary', 'button_attributes' => $trigger], ['content' => 'Launch demo modal']),
      ]),
      $this->component('modal', [
        'id' => 'sg-modal',
        'header' => 'Modal title',
        'body' => 'Woo-hoo, you are reading this text in a modal!',
        'footer' => 'Modal footer',
        'vertically_centered' => TRUE,
      ]),
    ];
  }

  private function navs(): array {
    $items = [
      $this->menuItem('Active', '/styleguide', TRUE),
      $this->menuItem('Link'),
      $this->menuItem('Another link'),
    ];
    return [
      'default' => $this->component('nav', ['items' => $items]),
      'tabs' => $this->component('nav', ['items' => $items, 'style' => 'tabs']),
      'pills' => $this->component('nav', ['items' => $items, 'style' => 'pills']),
      'centered' => $this->component('nav', ['items' => $items, 'style' => 'pills', 'alignment' => 'center']),
    ];
  }

  private function navbar(): array {
    $build = [];
    foreach (['light' => 'bg-body', 'dark' => 'bg-dark'] as $theme => $bg) {
      $build[$theme] = $this->component('navbar', [
        'navbar_theme' => $theme,
        'navbar_id' => 'sg-navbar-' . $theme,
        'navbar_utility_classes' => [$bg, 'rounded', 'border'],
        'navbar_container_type' => 'fluid',
      ], [
        'branding' => ['#markup' => Markup::create('<a class="navbar-brand" href="#sg-navbar">Navbar</a>')],
        'left' => $this->component('nav', [
          'items' => [
            $this->menuItem('Home', '/styleguide', TRUE),
            $this->menuItem('Features'),
            $this->menuItem('Pricing'),
          ],
          'nav_utility_classes' => ['navbar-nav', 'me-auto'],
        ]),
      ]);
    }
    return $build;
  }

  private function offcanvas(): array {
    return [
      $this->row([
        $this->component('offcanvas', [
          'offcanvas_id' => 'sg-offcanvas',
          'offcanvas_title' => 'Offcanvas',
          'offcanvas_content' => 'Content for the offcanvas panel goes here.',
          'offcanvas_button_content' => 'Open offcanvas',
          'placement' => 'start',
        ]),
        $this->component('offcanvas', [
          'offcanvas_id' => 'sg-offcanvas-end',
          'offcanvas_title' => 'From the right',
          'offcanvas_content' => 'This panel slides in from the end.',
          'offcanvas_button_content' => 'Open from right',
          'placement' => 'end',
        ]),
      ]),
    ];
  }

  private function pagination(): array {
    $pages = [];
    foreach (range(1, 5) as $n) {
      $pages[$n] = ['href' => '#sg-pagination', 'attributes' => new Attribute()];
    }
    $link = ['href' => '#sg-pagination', 'attributes' => new Attribute()];
    $items = ['first' => $link, 'previous' => $link, 'pages' => $pages, 'next' => $link, 'last' => $link];
    return [
      $this->component('pagination', ['items' => $items, 'current' => 2]),
      $this->component('pagination', ['items' => $items, 'current' => 3, 'size' => 'sm', 'alignment' => 'center', 'show_first' => FALSE, 'show_last' => FALSE]),
    ];
  }

  private function progress(): array {
    return [
      $this->component('progress', ['valuenow' => 25, 'striped' => FALSE, 'animated' => FALSE]),
      $this->component('progress', ['valuenow' => 50, 'color' => 'success', 'label' => '50%', 'striped' => FALSE, 'animated' => FALSE]),
      $this->component('progress', ['valuenow' => 75, 'color' => 'info', 'striped' => TRUE, 'animated' => FALSE]),
      $this->component('progress', ['valuenow' => 100, 'color' => 'warning', 'striped' => TRUE, 'animated' => TRUE]),
    ];
  }

  private function spinners(): array {
    $border = $grow = [];
    foreach (self::COLORS as $color) {
      $border[$color] = $this->component('spinner', ['type' => 'border', 'color' => $color]);
      $grow[$color] = $this->component('spinner', ['type' => 'grow', 'color' => $color]);
    }
    $border['sm'] = $this->component('spinner', ['type' => 'border', 'size' => 'sm']);
    return [$this->row($border), $this->row($grow)];
  }

  private function table(): array {
    $cell = fn(string $content, string $tag = 'td') => ['tag' => $tag, 'attributes' => new Attribute(), 'content' => $content];
    $rows = [];
    foreach ([['1', 'Mark', 'Otto', '@mdo'], ['2', 'Jacob', 'Thornton', '@fat'], ['3', 'Larry', 'Bird', '@twitter']] as $data) {
      $rows[] = ['attributes' => new Attribute(), 'cells' => array_map($cell, $data)];
    }
    return [
      $this->component('table', [
        'caption' => 'List of users',
        'header' => array_map(fn($h) => $cell($h, 'th'), ['#', 'First', 'Last', 'Handle']),
        'rows' => $rows,
        // Follow the active theme's "Striped tables" setting.
        'striped' => (bool) theme_get_setting('table_striped'),
      ]),
    ];
  }

  private function toasts(): array {
    return [
      $this->component('toasts', [
        'with_container' => TRUE,
        'container_classes' => 'position-static',
        'toasts' => [
          [
            'attributes' => ['class' => ['show']],
            'header' => ['title' => 'Bootstrap', 'subtitle' => '11 mins ago'],
            'with_close' => TRUE,
            'body' => 'Hello, world! This is a toast message.',
          ],
          [
            'attributes' => ['class' => ['show']],
            'header' => ['title' => 'With actions', 'subtitle' => 'just now'],
            'body' => 'Toasts can include extra buttons.',
            'additional_buttons' => [
              ['text' => 'Take action', 'color' => 'primary'],
              ['text' => 'Dismiss', 'color' => 'secondary', 'outline' => TRUE],
            ],
          ],
        ],
      ]),
    ];
  }

}
