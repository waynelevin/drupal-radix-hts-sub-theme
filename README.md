# Drupal Radix HTS sub-theme

**hts** is a Drupal 11 theme built on [Radix 6](https://www.drupal.org/project/radix)
and [Bootstrap 5.3](https://getbootstrap.com/). On top of the standard Radix
starter kit, it lets site builders restyle the whole site from the theme
settings page, with no Sass changes needed:

- **Bootswatch themes**: switch between all 26 free
  [Bootswatch](https://bootswatch.com/) designs with one dropdown.
- **Navbar colour and alignment**: give the top navbar the theme's primary,
  secondary, dark or light colour, and place the main menu left, centre or
  right.
- **Font**: choose from 20 Google Fonts, the device's system font, or the
  Bootswatch theme's own font.
- **Component styleguide**: a `/styleguide` page showing every Radix
  component, to check changes at a glance.
- **Bootstrap Icons** through Drupal's core Icon API.

| | |
|---|---|
| Machine name | `hts` |
| Base theme | Radix `^6.0` |
| Drupal | `^11` |
| Bootstrap | 5.3 (compiled from Sass) |
| Node.js | 18 or later (only needed to rebuild CSS/JS) |

## Contents

- [Installation](#installation)
- [Theme settings](#theme-settings)
- [Regions](#regions)
- [Component styleguide](#component-styleguide)
- [Icons](#icons)
- [JavaScript](#javascript)
- [Components and templates](#components-and-templates)
- [Development](#development)
- [File structure](#file-structure)

## Installation

### With Composer

```bash
composer config repositories.hts vcs https://github.com/waynelevin/drupal-radix-hts-sub-theme
composer require drupal/hts:dev-main
drush theme:enable hts
drush config:set system.theme default hts -y
```

Composer installs the theme to `web/themes/custom/hts` and pulls in Radix as
a dependency. The compiled CSS and JS (including all Bootswatch variants) are
committed in `build/`, so the theme works straight away without running npm.

### Manually

1. Install Radix: `composer require drupal/radix`
2. Copy this folder to `web/themes/custom/hts`.
3. Enable it and make it the default theme:
   ```bash
   drush theme:enable radix,hts
   drush config:set system.theme default hts -y
   ```

> **Bootstrap Icons** are not committed. To use them, run the
> [build](#development) once (see [Icons](#icons)).

## Theme settings

Go to **Appearance → hts → Settings** (`/admin/appearance/settings/hts`). The
**Bootswatch theme** section at the top of the page has four settings. Each
takes effect as soon as the form is saved.

### Bootswatch theme

Restyles the entire site with one of the free Bootswatch themes:

Brite, Cerulean, Cosmo, Cyborg, Darkly, Flatly, Journal, Litera, Lumen, Lux,
Materia, Minty, Morph, Pulse, Quartz, Sandstone, Simplex, Sketchy, Slate,
Solar, Spacelab, Superhero, United, Vapor, Yeti, Zephyr.

Choose **None (default Bootstrap)** for plain Bootstrap.

How it works:

- `npm run build` compiles one complete stylesheet per Bootswatch theme into
  `build/css/bootswatch/NAME.css`, from the official `bootswatch` npm package.
- Each variant includes all of the theme's own styles, and any Bootstrap
  variables set in `src/scss/base/_variables.scss` take priority over the
  Bootswatch ones.
- The selected variant replaces `build/css/main.style.css`, so the site still
  loads a single stylesheet.
- Most Bootswatch themes load their web font from Google Fonts.

### Navbar colour

The background of the top navbar, using the colours of the selected Bootswatch
theme:

| Option | Background | Text |
|---|---|---|
| Primary *(default)* | `bg-primary` | Light |
| Secondary | `bg-secondary` | Light |
| Dark | `bg-dark` | Light |
| Light grey | `bg-body-tertiary` | Dark |
| Light | `bg-light` | Dark |
| None (transparent) | none | Dark |

The text colour is set to match through Bootstrap's `data-bs-theme`, so links
and the menu toggle stay readable.

### Navbar menu alignment

Where the main menu (the **Navbar left** region) sits in the top navbar:

| Option | Wrapper class | Result |
|---|---|---|
| Left *(default)* | `me-auto` | Menu next to the logo; Navbar right content pushed to the far right |
| Centre | `mx-auto` | Menu centred in the space between the logo and Navbar right content |
| Right | `ms-auto` | Menu pushed to the right, just before any Navbar right content |

On small screens the menu collapses behind the toggle button as usual.

### Font

The font for body text, headings, buttons and the navbar:

- **Theme default** *(default)*: keeps the selected Bootswatch theme's font,
  or Bootstrap's system font stack.
- **System UI**: the device's native font. No web font is loaded.
- **Sans-serif**: Inter, Roboto, Open Sans, Lato, Montserrat, Poppins, Source
  Sans 3, Nunito, Raleway, Work Sans, DM Sans, IBM Plex Sans, Fira Sans,
  Manrope, Outfit.
- **Serif**: Merriweather, Lora, Playfair Display, PT Serif, Roboto Slab.

Web fonts load from Google Fonts in regular and bold, plus italics where the
font has them. Preview them at [fonts.google.com](https://fonts.google.com/).
The font is applied by setting Bootstrap's `--bs-body-font-family`, so it
also reaches components that use that variable.

### Settings reference

The settings are stored in `hts.settings` with Drupal's other theme settings,
and can be exported with configuration management:

| Key | Default | Values |
|---|---|---|
| `bootswatch` | `''` | A Bootswatch theme name, eg `darkly`, or empty for none |
| `navbar_color` | `primary` | `primary`, `secondary`, `dark`, `body-tertiary`, `light`, or empty for none |
| `navbar_alignment` | `left` | `left`, `center`, `right` |
| `font` | `''` | A font key from `includes/font.theme`, eg `poppins`, `system`, or empty for the theme default |

> **Tip:** reload the settings page before saving if it has been open for a
> while. Saving an out-of-date copy also re-saves Drupal's standard settings
> on the page (logo, favicon and so on) with the values it was loaded with.

## Regions

| Region | Machine name | Used for |
|---|---|---|
| Navbar branding | `navbar_branding` | Site logo and name (left of the navbar) |
| Navbar left | `navbar_left` | Main menu |
| Navbar right | `navbar_right` | Account menu, search, etc. |
| Header | `header` | Above the main content |
| Content | `content` | Main page content |
| Page bottom | `page_bottom` | Below the content |
| Footer | `footer` | Site footer |

## Component styleguide

The theme includes a small module, `modules/radix_styleguide`, that adds a
page at **`/styleguide`**. It shows every visual Radix component rendered with
sample content in this theme, so you can check how a Bootswatch theme, font or
Sass change looks everywhere at once.

Components shown: accordion, alert, badge, breadcrumb, button, button group,
card, carousel, close button, collapse, dropdown, figure, heading, image, list
group, modal, nav, navbar, offcanvas, pagination, progress, spinner, table and
toasts. Most sections show several colour, size and style variations. The
interactive components (modal, offcanvas, carousel, dropdown, collapse,
accordion) all work, and images are generated placeholders, so the page loads
nothing from other sites.

Not shown:

- **Page-structure components** (`html`, `page`, `node`, `region`, `field`,
  form elements, views). Drupal uses these to build every page.
- **Tooltips**. Radix only documents them and doesn't provide a component.
- **The disabled close button**. Radix 6.0.9's `close-button` template throws
  an error for `disabled: true`.

Enable it with:

```bash
drush en radix_styleguide -y
```

Viewing it requires the **View the Radix component styleguide** permission,
which administrators have automatically. It is a development aid, so leave it
disabled on live sites.

## Icons

The theme ships a [Bootstrap Icons](https://icons.getbootstrap.com) pack
wired into Drupal's core
[Icon API](https://www.drupal.org/docs/develop/drupal-apis/icon-api), declared
in `hts.icons.yml`.

The SVG files are not committed. The build copies them from the
`bootstrap-icons` npm package into `assets/icons/bootstrap-icons`, so run
`npm install && npm run build` once before using icons. If the theme was
enabled before the first build, run `drush cr` afterwards, since the Icon API
caches the list of icons.

In Twig:

```twig
{{ icon('bootstrap_icons', 'house-fill', { size: 48, color: '#0d6efd' }) }}
```

In a render array:

```php
$build['icon'] = [
  '#type' => 'icon',
  '#pack_id' => 'bootstrap_icons',
  '#icon_id' => 'house-fill',
  '#settings' => ['size' => 48],
];
```

## JavaScript

`build/js/main.script.js` is bundled from `src/js/` and includes:

- **Bootstrap's JavaScript**, so modals, dropdowns, collapse, carousel,
  offcanvas and so on work with Bootstrap's `data-bs-*` attributes.
- **Tooltips**: every element with `data-bs-toggle="tooltip"` gets a
  Bootstrap tooltip on page load.
- **Toasts**: every `.toast` on the page is shown on load and stays until
  closed.

Add your own site-wide JavaScript at the end of `src/js/main.script.js`.

## Components and templates

### Single-directory components

Radix provides about 60 Bootstrap-based
[single-directory components](https://www.drupal.org/docs/develop/theming-drupal/using-single-directory-components)
(`radix:card`, `radix:navbar`, `radix:modal`, …), which this theme uses
throughout. To customise one, copy it into `components/` and add
`replaces: 'radix:NAME'` to its `.component.yml`. The
[Radix CLI](https://github.com/doxigo/drupal-radix-cli) can do the copying.
Per-component `.scss` files are compiled to a `.css` file next to them by the
build.

Overridden in this theme:

- **`page-navigation`** replaces `radix:page-navigation` to add the
  [navbar colour](#navbar-colour) and
  [menu alignment](#navbar-menu-alignment) settings.

### Template overrides

The theme overrides Drupal's core templates to output Bootstrap markup, using
the Radix components:

| Area | Templates |
|---|---|
| Page | `html`, `page`, `page--front`, `region`, `container` |
| Blocks | `block`, branding, main content, menu and local-tasks blocks |
| Content | `node`, `media`, `comment`, `taxonomy-term`, `user`, `page-title` |
| Fields | `field`, `field--comment`, `image`, `time`, `filter-caption` |
| Forms | `form`, `form-element`, labels, `input` (text, checkbox, radio, submit), `select`, `textarea`, `radios`, `fieldset`, `details`, search block form |
| Menus and navigation | `menu`, `menu--main`, `menu--account`, local tasks and actions, `breadcrumb`, `pager`, `links`, dropbuttons |
| Views | `views-view`, unformatted, grid, table, mini pager |
| Other | `status-messages`, `progress-bar`, `item-list`, `table` |

PHP preprocess functions live in `includes/*.theme`, one file per area. They
are loaded automatically by `hts.theme`.

## Development

The theme's Sass and JavaScript are compiled with
[esbuild](https://esbuild.github.io). You only need this when you change
files in `src/`, `components/` or the Bootstrap variables.

### Setup

```bash
cd web/themes/custom/hts
nvm use                       # Node version from .nvmrc
npm install
cp .env.example .env.local    # then set DRUPAL_BASE_URL to your site URL
```

With **DDEV**, run npm inside the web container, so nothing needs to be
installed on your computer:

```bash
ddev exec -d /var/www/html/web/themes/custom/hts npm install
ddev exec -d /var/www/html/web/themes/custom/hts npm run build
```

### Scripts

| Command | What it does |
|---|---|
| `npm run build` | Production build: minified main CSS/JS, component CSS/JS, icons, **and all 26 Bootswatch stylesheets** |
| `npm run dev` | One-off development build, unminified with source maps |
| `npm run watch` | Rebuilds on save and reloads the browser with BrowserSync. Inside DDEV it detects the container and proxies to `localhost` instead of `DRUPAL_BASE_URL` |
| `npm run stylint` / `stylint-fix` | Lint (and fix) the Sass with Stylelint |
| `npm run biome:check` | Format and lint JavaScript with Biome |

`dev` and `watch` only rebuild the default stylesheet. Run `npm run build`
before committing, so the Bootswatch variants include your changes.

### Customising Bootstrap

Set Bootstrap variables in `src/scss/base/_variables.scss`. Copy the
variable from `node_modules/bootstrap/scss/_variables.scss` and remove its
`!default` flag:

```scss
$border-radius: .5rem;
$link-decoration: none;
```

These apply to the default stylesheet and override the Bootswatch theme's
values in every variant. Add other site-wide styles to the partials in
`src/scss/base/`.

### Notes

- The build prints Sass `@import` deprecation warnings and a few formatting
  warnings from the Radix starter kit files. They don't affect the output.
- The generated Bootswatch entry files in `src/scss/bootswatch/` are
  recreated on every build and are not committed.

## File structure

```
hts/
├── build/                 Compiled CSS/JS (committed)
│   ├── css/main.style.css
│   ├── css/bootswatch/    One stylesheet per Bootswatch theme
│   └── js/main.script.js
├── components/            Single-directory component overrides
├── config/                Settings schema and defaults
├── includes/              Preprocess functions and settings logic
│   ├── bootswatch.theme   Bootswatch stylesheet switching
│   ├── navbar.theme       Navbar colour and menu alignment
│   └── font.theme         Font setting
├── modules/
│   └── radix_styleguide/  The /styleguide page
├── src/
│   ├── js/                JavaScript sources
│   └── scss/              Sass sources (base/_variables.scss for Bootstrap variables)
├── templates/             Twig template overrides
├── build.mjs              Production build (incl. Bootswatch variants)
├── build-dev.mjs          Development build
├── watch.mjs              Watch + BrowserSync
├── hts.icons.yml          Bootstrap Icons pack for the Icon API
├── hts.info.yml           Theme definition and regions
├── hts.libraries.yml      The main "style" library
├── hts.theme              Loads includes/*.theme
└── theme-settings.php     The theme settings form
```
