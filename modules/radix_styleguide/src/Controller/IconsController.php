<?php

declare(strict_types=1);

namespace Drupal\radix_styleguide\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Theme\Icon\Plugin\IconPackManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Shows every icon from every icon pack, with search and click-to-copy.
 */
final class IconsController extends ControllerBase {

  public function __construct(
    private readonly IconPackManagerInterface $iconPackManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static($container->get('plugin.manager.icon_pack'));
  }

  /**
   * Builds the icons page.
   */
  public function page(): array {
    $packs = $this->iconPackManager->getDefinitions() ?? [];
    $packs = array_filter($packs, fn(array $pack) => !empty($pack['icons']));
    $total = array_sum(array_map(fn(array $pack) => count($pack['icons']), $packs));

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['sg-icons', 'mb-5']],
      '#attached' => ['library' => ['radix_styleguide/icons']],
      '#cache' => ['tags' => ['icon_pack_plugins']],
    ];

    $build['intro'] = [
      '#markup' => Markup::create('<p class="lead">' . $this->t('Every icon available through Drupal\'s Icon API. Click an icon to copy its Twig code.') . '</p><p><a href="' . Url::fromRoute('radix_styleguide.page')->toString() . '">' . $this->t('← Back to the component styleguide') . '</a></p>'),
    ];

    if (!$packs) {
      $build['empty'] = [
        '#markup' => '<div class="alert alert-warning">' . $this->t('No icons were found. If the theme uses Bootstrap Icons, run <code>npm run build</code> in the theme folder, then <code>drush cr</code>.') . '</div>',
      ];
      return $build;
    }

    // Search box and pack links.
    $links = [];
    foreach ($packs as $id => $pack) {
      $links[] = '<li class="nav-item"><a class="nav-link py-1 px-2" href="#sg-pack-' . Html::escape($id) . '">' . Html::escape((string) ($pack['label'] ?? $id)) . ' <span class="badge text-bg-secondary">' . count($pack['icons']) . '</span></a></li>';
    }
    $build['toolbar'] = [
      '#markup' => Markup::create(
        '<div class="sticky-top bg-body py-3 mb-3 border-bottom">'
        . '<div class="d-flex flex-wrap align-items-center gap-3">'
        . '<input type="search" class="form-control sg-icons__search" style="max-width: 24rem" placeholder="' . Html::escape((string) $this->t('Search icons, eg arrow, house, check')) . '" aria-label="' . Html::escape((string) $this->t('Search icons')) . '">'
        . '<span class="sg-icons__count text-body-secondary small" aria-live="polite">' . $this->formatPlural($total, '1 icon', '@count icons') . '</span>'
        . '</div>'
        . '<ul class="nav nav-pills flex-wrap small mt-2">' . implode('', $links) . '</ul>'
        . '</div>'
      ),
    ];

    foreach ($packs as $pack_id => $pack) {
      $icons = $pack['icons'];
      ksort($icons);

      $section = [
        '#type' => 'container',
        '#attributes' => ['id' => 'sg-pack-' . $pack_id, 'class' => ['sg-icons__pack', 'mb-5']],
        'title' => [
          '#markup' => Markup::create(
            '<h2 class="h3 d-flex align-items-baseline gap-2">' . Html::escape((string) ($pack['label'] ?? $pack_id))
            . ' <code class="fs-6 fw-normal">' . Html::escape($pack_id) . '</code></h2>'
            . (!empty($pack['description']) ? '<p class="text-body-secondary">' . Html::escape((string) $pack['description']) . '</p>' : '')
          ),
        ],
        'grid' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['row', 'row-cols-3', 'row-cols-sm-4', 'row-cols-md-6', 'row-cols-lg-8', 'g-2']],
        ],
      ];

      foreach ($icons as $full_id => $icon) {
        $icon_id = (string) ($icon['icon_id'] ?? substr((string) $full_id, strlen($pack_id) + 1));
        $snippet = "{{ icon('$pack_id', '$icon_id') }}";
        $section['grid'][$icon_id] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['col', 'sg-icons__item'],
            'data-name' => strtolower($icon_id),
          ],
          'button' => [
            '#type' => 'html_tag',
            '#tag' => 'button',
            '#attributes' => [
              'type' => 'button',
              'class' => ['sg-icon'],
              'title' => $this->t('Copy @snippet', ['@snippet' => $snippet]),
              'data-snippet' => $snippet,
            ],
            'icon' => [
              '#type' => 'icon',
              '#pack_id' => $pack_id,
              '#icon_id' => $icon_id,
              '#settings' => ['size' => 32],
            ],
            'name' => [
              '#markup' => '<span class="sg-icon__name">' . Html::escape($icon_id) . '</span>',
            ],
          ],
        ];
      }
      $build[$pack_id] = $section;
    }

    return $build;
  }

}
