/**
 * @file
 * Icon styleguide: live search and click-to-copy.
 */
((Drupal, once) => {
  Drupal.behaviors.radixStyleguideIcons = {
    attach(context) {
      once('sg-icons', '.sg-icons', context).forEach((root) => {
        const search = root.querySelector('.sg-icons__search');
        const count = root.querySelector('.sg-icons__count');
        const tiles = [...root.querySelectorAll('.sg-icons__item')];
        const sections = [...root.querySelectorAll('.sg-icons__pack')];

        const filter = () => {
          const terms = search.value.toLowerCase().trim().split(/\s+/).filter(Boolean);
          let shown = 0;
          tiles.forEach((tile) => {
            const name = tile.dataset.name;
            const match = terms.every((term) => name.includes(term));
            tile.hidden = !match;
            if (match) {
              shown += 1;
            }
          });
          sections.forEach((section) => {
            section.hidden = !section.querySelector('.sg-icons__item:not([hidden])');
          });
          count.textContent = Drupal.formatPlural(shown, '1 icon', '@count icons');
        };
        search.addEventListener('input', filter);

        root.addEventListener('click', (event) => {
          const button = event.target.closest('.sg-icon');
          if (!button) {
            return;
          }
          const snippet = button.dataset.snippet;
          const done = () => {
            const name = button.querySelector('.sg-icon__name');
            const original = name.textContent;
            button.classList.add('is-copied');
            name.textContent = Drupal.t('Copied!');
            setTimeout(() => {
              button.classList.remove('is-copied');
              name.textContent = original;
            }, 1200);
          };
          if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(snippet).then(done);
          }
          else {
            window.prompt(Drupal.t('Copy this code:'), snippet);
          }
        });
      });
    },
  };
})(Drupal, once);
