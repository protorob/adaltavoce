# Ad Alta Voce APS — website

The website of **Ad Alta Voce APS**, an association based in Montevago (AG), Sicily, that promotes reading, art, creativity and culture for children and families. It is hosted on the domain associazioneadaltavoce.it (first deploy still to be done at the time of writing).

The site is in **Italian only** and is aimed mainly at parents. It presents the association, its activities (creative campuses, Saturday workshops, family space) and how to get in touch — mainly through WhatsApp, which is the association's preferred channel besides email/PEC. There are no forms, no shop and no donations, and the site sets no cookies (see [Legal pages and analytics](#legal-pages-and-analytics)).

It is built with [Kirby CMS](https://getkirby.com) 5 (started from [Plainkit](https://github.com/getkirby/plainkit)) and [Tailwind CSS](https://tailwindcss.com) v4, bundled with Vite. Kirby is a flat-file CMS: there is no database, and all content lives in text files in `content/`.

## What's on the site

Pages are managed in the Kirby Panel (`/panel`) and appear in the main menu automatically when they are set to *listed*. At the time of writing:

- **Home** — a full-width hero (title, description, buttons, background image with a soft colour gradient), configured in Panel → Site → Header.
- **Chi siamo** — who the association is.
- **Le nostre attività** — the activities overview; each activity (*Campus Creativi ad Alta Voce*, *Sabato Creativi*, *Spazio Famiglia*) is a sub-page. Sub-pages show up as a dropdown in the main menu and, through the **Child pages** block, as a card grid (see [Custom blocks: Child pages](#custom-blocks-child-pages)).
- **Contatti** — contact details.
- **Legal** (not in the menu) — the Privacy Policy and Cookie Policy, linked from the footer.

Header and footer are driven by Panel → Site (logo, WhatsApp call-to-action button, company details, social links, legal pages) — see [Site panel defaults](#site-panel-defaults).

## Licensing

- **[Kirby CMS](https://getkirby.com)** — free to develop with locally; a license per domain is required for the live site. Buy it before going live and activate it in the Panel's System view.
- **[kirby.tools/content-translator](https://kirby.tools/content-translator)** — *not* installed; only offered by `setup-languages.sh` if multi-language is ever enabled (see [Multi-language support](#multi-language-support)). Free locally, paid once live.

## Requirements

- PHP 8.2+ with extensions: `mbstring`, `xml`, `gd`, `curl`, `zip`, `intl`
- [Composer](https://getcomposer.org)
- [Node.js](https://nodejs.org) 20.19+ or 22.12+ (includes npm) — required by Vite

## Installing PHP (Ubuntu / WSL2)

```bash
sudo apt update && sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-intl
```

Make `php` point to 8.3 if needed:

```bash
sudo update-alternatives --set php /usr/bin/php8.3
```

## Installing Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## Setup

```bash
git clone <repo-url>
cd adaltavoce
composer install
npm install
npm run build
```

`kirby/`, `vendor/`, `assets/css`, `assets/js` and `node_modules/` are not committed — `composer install` and `npm run build` restore them.

## Run locally

In two separate terminals:

```bash
# Terminal 1 — PHP dev server
composer start

# Terminal 2 — CSS/JS watch mode
npm run dev
```

Then open `http://localhost:8000` in your browser.

The Kirby Panel is available at `http://localhost:8000/panel` — you will be prompted to create an admin account on first visit. Accounts are stored in `site/accounts/`, which is gitignored: each environment (your machine, the live server) has its own, unless you copy them over on purpose — see [Creating the first Panel account](#creating-the-first-panel-account).

Port 8000 already taken (e.g. by another site)? Set `PORT` to use a different one: `PORT=8001 composer start`, then open `http://localhost:8001`. This relies on shell variable expansion, so on Windows run it from WSL.

> **Careful with `content/`.** Editing in the local Panel writes straight into `content/` (uncommitted, tracked by git). Never run `git checkout`, `git clean`, `git restore` or `git stash` on `content/` without checking `git status` first — you would throw away Panel edits that exist nowhere else. Commit content changes regularly.

## Site panel defaults

The header and footer are driven by `site/blueprints/site.yml`, under Panel → Site Settings:

- **Header tab** — an icon/label/URL call-to-action button (currently WhatsApp), with pickable background and text colors (swatches, defaulting to a dark neutral). Rendered by `site/snippets/cta-button.php`, called from `site/snippets/header.php` as the last item in the desktop nav and always visible next to the hamburger button on mobile (not tucked inside the collapsible menu). Renders nothing if the label or URL is empty. The same tab holds the homepage hero (see [Hero sections](#hero-sections)).
- **Company information tab** — a site logo upload (shown in the header in place of the text title, once set), company name/address/phone/email, and a repeatable social links structure (icon + label + URL).
  - **Legal pages** is a repeatable structure (one `page` picker per row) — add as many legal pages as needed, in whatever order they should appear in the footer. Each row just picks a page; the link text is that page's own title. It currently lists the Privacy Policy and the Cookie Policy (both under `content/legal/`, which is unlisted so it doesn't appear in the main menu).
- **Main menu** — driven by `$site->children()->listed()`. A top-level item with listed children gets a one-level dropdown on desktop (CSS-only, opens on hover or keyboard focus); the mobile menu always lists those children indented under their parent. Pages set to unlisted or draft don't appear.
- **Header tweaks** (two single-edit spots, no Panel field):
  - **Logo size** — `$logoClass` at the top of `site/snippets/header.php` (default `h-12 w-auto`). Sizes are rem-based and the root font is 125%, so `h-12` = 60px; the header bar itself is `h-20` (100px).
  - **Mobile-menu breakpoint** — `--breakpoint-nav` in `src/main.css`'s `@theme` block (default `40rem`, i.e. 800px). Below it the hamburger menu shows; at or above it the full nav shows. Raise it (e.g. `64rem` = 1280px) if the nav items wrap or crowd the logo before the menu collapses. `header.php` uses it as the `nav:` variant (`nav:flex`, `nav:hidden`) rather than `sm:`, which is why it's a single value. Re-run `npm run build` after changing it.
- **Base font size** — `src/main.css` sets `html { font-size: 125% }`, so every Tailwind size (text, spacing and the `max-w-*` containers) renders 25% larger than Tailwind's defaults.
- `site/snippets/footer.php` and `header.php` render all of the above and degrade cleanly when a field is empty (e.g. no logo → falls back to the text title; no social links → nothing renders in that row).
- Icons (CTA and social links) use [`tobimori/kirby-icon-field`](https://github.com/tobimori/kirby-icon-field) (installed via Composer, `type: icon` in the blueprint), reading SVGs from `assets/icons/` (tracked in git, unlike `assets/css`/`assets/js`). A starter set of common platforms ships in that folder (Facebook, Instagram, X, LinkedIn, YouTube, TikTok, WhatsApp, Pinterest) — drop in more `.svg` files there as needed and they show up in the field's picker automatically.
  - The plugin caches its `assets/icons/` folder scan by default, keyed by the field's config rather than the folder's actual contents — so a new `.svg` won't show up in the Panel until that cache is cleared (delete `site/cache/<host>/tobimori/`). `site/config/config.php` disables this cache (`'tobimori.icon-field' => ['cache' => false]`) so new icons always show up immediately — worth re-enabling (remove that config block) once the icon set has stabilized, since it adds a small perf cost on every Panel load of an icon field.

### URL fields use Kirby's `link` field

`ctaUrl`, `heroButtons.url`, and `social.url` are all `type: link` (not `type: url`), restricted to `options: [url, email, tel, anchor]`. Kirby's plain `url` field only validates `http(s)://`/`ftp://` values, so it rejects `mailto:`/`tel:` links outright — the `link` field gives editors a type-aware picker (URL / Email / Tel / Anchor) and validates each type correctly. The stored value already comes back scheme-prefixed (`mailto:...`, `tel:...`, `https://...`, `#...`), so templates use it directly as `href` with no extra resolution step — just `esc($field, 'attr')` since it lands in an HTML attribute.

The `page`/`file` options are deliberately excluded here: those store an unresolved `page://uuid`/`file://uuid` reference rather than a ready-to-use href, which would need a small resolver added to `cta-button.php`/`hero.php`/`footer.php` before it's usable. Add them (and the resolver) if a future need for internal-page or file CTAs comes up.

## Hero sections

The hero banner's fields (eyebrow, title, description, buttons, background) live in one shared fragment, `site/blueprints/fields/hero.yml`, pulled into a blueprint via a `type: group` field with `extends: fields/hero`. Group fields splice their child fields into the parent form inline — no visual wrapper, no content nesting under the group's own name — so the same field names (`eyebrow`, `heroTitle`, `heroButtons`, etc.) are reused wherever the group appears. `site/snippets/hero.php` takes whichever model is passed in as `snippet('hero', ['model' => ...])` and reads all fields off that model, so the identical fragment works for both the site and any page.

- **Homepage** — `site/blueprints/site.yml`'s Header tab includes the group unconditionally (`hero: extends: fields/hero`). `default.php` renders it via `snippet('hero', ['model' => $site])` whenever `$page->isHomePage()`.
- **Any other page** — `site/blueprints/pages/default.yml` has a Hero tab with a `heroToggle` toggle field, then the same group with `when: heroToggle: true`. Kirby's `group` field type propagates a `when:` set on the group to every field inside it automatically, so the whole hero only shows once the toggle is on. `default.php` renders it via `snippet('hero', ['model' => $page])` when the toggle is on, and skips the plain `<h1>` page-title heading in that case (the hero's own title stands in for it, avoiding two `<h1>`s on one page). Any new page blueprint added later should copy this Hero tab and toggle pattern.
- The background-image field queries `model.images` rather than a hardcoded `site.images`/`page.images` — `model` is a binding Kirby always provides pointing at whichever model a blueprint query runs against (`ModelWithContent::query()`), so the one fragment scopes correctly to the site's own files or a specific page's own files depending on where it's used.
- **Fields**: eyebrow, title, description, an overall hero text color, and a repeatable buttons structure (icon + label + link + per-button background color + text color). Button links use the `link` field (see "URL fields use Kirby's `link` field" above).
- **Layout toggle**: "Full width" vs "Contained" (`heroFullWidth`) — contained shows the hero as a rounded, inset card; full width bleeds it edge-to-edge with square corners, flush against the header.
- **Background**: a radio picks Image or Solid color; the relevant fields (image upload, "add color overlay" toggle + overlay color, or background color) appear conditionally via blueprint `when:` — each condition is a single exact-value match, since Kirby's `when` only supports "and" logic natively (no plugin needed here). The image overlay is rendered as a subtle bottom-to-top gradient (70% → 20% of the chosen color via `color-mix()`, tunable in `hero.php`) rather than a flat fill, so the image stays visible.
- Color fields use Kirby's [color field](https://getkirby.com/docs/reference/panel/fields/color) with a shared set of swatches (a YAML anchor `&heroSwatches` at the top of `fields/hero.yml`; `site.yml` has its own copy, `&ctaSwatches`, for the header button — YAML anchors can't cross files, so keep the two lists in sync). Update the hex values to the association's brand colors, which would also belong in `src/main.css`'s `@theme` block.
- Renders nothing if eyebrow/title/description/buttons are all empty — a page with the toggle on but no hero content shows nothing extra (and no `<h1>` at all, so fill in at least a title).
- Colors are applied via inline `style` attributes (not Tailwind classes) since they're arbitrary values chosen at runtime in the Panel, not known at Tailwind's build time. Each dynamic value is escaped once with `esc($value, 'attr')` (the attribute-embedding context) — escaping with `'css'` first and `'attr'` again double-encodes and corrupts the style string.

## Custom blocks: Child pages

The page body's `blocks` field has one custom block on top of Kirby's core ones: **Child pages**. Pick a page and it renders that page's *listed* subpages as a two-column grid of cards.

- **Files**: `site/blueprints/blocks/child-pages.yml` (the Panel form — just a page picker), `site/snippets/blocks/child-pages.php` (the markup), and `site/config/config.php`'s `blocks.fieldsets` list. Kirby only offers custom blocks once they're listed in `blocks.fieldsets`; setting it globally there means every `type: blocks` field gets them, with no per-blueprint `fieldsets:` list. That list also has to repeat Kirby's core blocks, since defining it replaces the default set.
- **Card title**: the child page's title.
- **Card description**: the child's **Page excerpt** (`pageExcerpt`, in the default blueprint's sidebar), shown under the title. Nothing renders if it's empty.
- **Card image** (3:2, `aspect-[3/2]`, i.e. 6/4), in this order of precedence:
  1. the child's **Miniatura Pagina** (`cardImage`);
  2. otherwise the child's hero background image — but only while its hero is on (`heroToggle`) and set to the "Image" background type, since those fields are hidden in the Panel otherwise;
  3. otherwise no image, and the card is text-only.
- The whole card is one link, so the image is clickable too, and it zooms slightly on hover (`group-hover:scale-105`). The image is cropped by Kirby to 600/900/1400px-wide thumbnails (`srcset`) and is `alt=""` on purpose — the title is the link text, so the image is decorative.
- The block uses `not-prose` because it sits inside `default.php`'s `.prose` wrapper, which would otherwise restyle the card links and headings.

## Default page content

`site/blueprints/pages/default.yml`'s `text` field is a `blocks` field (Kirby's visual block editor — text, heading, image, gallery, video, quote, list, table, line, markdown, code, plus the Child pages block), not a plain textarea/KirbyText field. `site/templates/default.php` renders it with `$page->text()->toBlocks()->toHtml()`, wrapped in a `.prose` container so `@tailwindcss/typography` styles whatever the blocks produce.

This is the field editors see on any page using the default blueprint — including the homepage's own body content below the hero (the hero itself is unrelated, driven by its own fields on `$site`, see "Hero sections" above).

## Multi-language support

The site is **Italian only** and Kirby's multi-language mode is **not enabled**. `<html lang>` in `header.php` is `it` unless multi-language is on, and `site/snippets/language-switcher.php` renders nothing until then (it is already wired into `header.php`'s desktop nav and mobile menu, so nothing needs changing if a second language is ever added).

If that day comes, `./setup-languages.sh` does the switch. It will:

1. Ask which languages to install (a preset list of common ones, or custom `code:Name:locale` entries) and which is the default.
2. If more than one language was selected, ask whether to also set up [kirby.tools/content-translator](https://kirby.tools/content-translator) for one-click page translation in the Panel (see [Licensing](#licensing)). If yes:
   - Runs `composer require johannschopplich/kirby-content-translator` (or prints the command if Composer isn't on `PATH` yet).
   - Asks which provider to use — DeepL, AI via Kirby Copilot (OpenAI), or skip and configure later.
   - Adds the provider config to `site/config/config.php`, reading the API key from an environment variable (`DEEPL_API_KEY` or `OPENAI_API_KEY`) rather than writing it into the file — `config.php` is committed to git, so the key itself must be set outside of it (shell env, host/server env config, etc).
   - Adds the `content-translator` button to `site/blueprints/pages/default.yml`'s `buttons:` list (skipped with a manual instruction if that file already defines `buttons:`).
   - Reminds you to activate a license in the Panel's System view before going live.
3. Add `'languages' => true` to `site/config/config.php` (plus the content-translator config from step 2, if set up) — if that file already exists, the script prints the keys for you to add yourself.
4. Create one `site/languages/{code}.php` file per selected language.
5. Migrate every un-suffixed `.txt` file under `content/` into per-language copies (e.g. `default.it.txt`) — discovered dynamically at run time (`find content -name '*.txt'`), not a fixed list, so it covers whatever pages exist at the time. The non-default language copies start as duplicates of the default and need translating via the Panel (or via content-translator, if installed).

**Only run it on a still-single-language site, and commit everything first** — it rewrites every content file. Before touching anything, it checks for three signs that the site is already multi-language — `site/config/config.php` already has `'languages' => true`, `site/languages/` already has language files, or `content/` already has a language-suffixed file — and aborts with no changes if any of them are true, since re-running it against an already-migrated site would silently orphan the existing per-language content files.

## Frontend build

The frontend uses [Tailwind CSS v4](https://tailwindcss.com) via the `@tailwindcss/vite` plugin. Source files live in `src/` and compile to `assets/` (gitignored, rebuilt on every deploy).

```bash
npm run dev     # watch mode, rebuilds on changes to src/, templates, snippets
npm run build   # production build → assets/css/ and assets/js/
```

- `src/main.css` — Tailwind entry point, `@theme` customizations (font, mobile-menu breakpoint), the 125% root font size, custom CSS
- `src/main.js` — entry point for JS behavior (mobile menu toggle)
- `site/snippets/header.php` / `site/snippets/footer.php` — shared page chrome, styled with Tailwind utility classes
- `site/templates/default.php` — the page template every page uses

Templates reference `assets/css/main.css` and `assets/js/main.js` directly, not the `src/` files — so run `npm run build` (or keep `npm run dev` running) after changing CSS classes or JS.

## Page transitions

Every navigation here is a normal full page load (Kirby renders server-side, there's no client-side router), so "page transitions" are done with CSS only — no JS, no new dependency:

- **Fallback fade-in** — `main { animation: page-fade-in .4s ease }` in `src/main.css` fades in only the `<main>` content on load. It's deliberately scoped to `<main>`, not `body`: animating the whole `<body>` would make the header/nav fade in and flash on every page change too. `#site-header` isn't part of this animation, so it renders immediately and never flashes.
- **Cross-document view transitions** — `@view-transition { navigation: auto; }` opts into the browser's native View Transitions API for same-origin navigations. Where supported, the browser cross-fades the whole old/new page automatically — a real fade-out-then-fade-in, not just the fade-in above. `#site-header` additionally gets `view-transition-name: site-header`, which tells the browser to treat it as a persistent element across the transition (matched by name between the outgoing and incoming page) instead of cross-fading it with everything else — since the header's markup is normally identical between pages, this reads as the header simply staying in place while only the content crossfades.
  - **Browser support**: Chromium browsers (Chrome/Edge) only, as of writing. Firefox and Safari don't recognize `@view-transition` yet and silently ignore it — those browsers just get the fallback fade-in above (nav still doesn't flash, but no fade-out).
- **Not done (yet)**: a JS page-transition library (e.g. [Swup](https://swup.js.org)) that intercepts internal link clicks, fetches the next page, and swaps only `<main>`'s content without a full reload — the header DOM node would never even reload. That would work identically in every browser (not just Chromium), but adds real complexity (handling back/forward navigation, re-running `main.js`'s mobile-menu logic after each swap, scroll restoration, updating `<title>`, etc.) that isn't justified yet. Revisit if cross-browser parity becomes a priority.

## Deploying to a live server

The site is hosted on a **DreamHost** VPS (United States). A deploy script pushes it via SSH/rsync.

### First-time setup (local)

```bash
cp deploy-example.sh deploy.sh
chmod +x deploy.sh
```

Open `deploy.sh` and fill in the server details:

```bash
SSH_USER="your-user"
SSH_HOST="your-server.com"
REMOTE_PATH="/home/your-user/associazioneadaltavoce.it"
SSH_PORT=22
PHP_BIN="/usr/local/php83/bin/php"   # path to PHP on the server
COMPOSER_BIN="~/composer"            # path to Composer on the server
```

`deploy.sh` is gitignored — your credentials will never be committed.

### First-time setup (server)

`vendor/` and `kirby/` are never uploaded — Composer runs on the server after each deploy so dependencies are always built for the server's PHP version. You need Composer installed on the server once:

```bash
ssh your-user@your-server.com
curl -sS https://getcomposer.org/installer | php
mv composer.phar ~/composer
```

On **DreamHost** the default CLI `php` may differ from the web PHP version configured for the domain. Find the available binaries:

```bash
ls /usr/local/php*/bin/php
```

Then set `PHP_BIN` in `deploy.sh` to match the PHP version configured for the domain in the DreamHost panel (e.g. `/usr/local/php83/bin/php`).

`site/cache`, `site/sessions` and `site/accounts` are excluded from every deploy on purpose (see below) — the *directories* are excluded too, not just their contents, so rsync never creates them on the server. Create them once, **before the first deploy**, or Kirby has nowhere to write the Panel's first admin account and you'll get stuck at the "create an account" screen with no visible error:

```bash
mkdir -p ~/associazioneadaltavoce.it/site/cache ~/associazioneadaltavoce.it/site/sessions ~/associazioneadaltavoce.it/site/accounts
```

### Running a deploy

```bash
./deploy.sh
```

This will:
1. Ask for confirmation first — the deploy overwrites the server's `content/` with your local copy, so anything edited in the live Panel since your last pull would be lost. Answer `y` to continue; anything else (including just pressing Enter) exits, and you should run `./pull.sh` first (see [Pulling content from the server](#pulling-content-from-the-server))
2. Run `npm run build` to compile CSS and JS
3. Upload all required files via rsync (only changed files are transferred)
4. Run `composer install` on the server to build `vendor/` and `kirby/`
5. Set correct write permissions on Kirby's data directories

### Creating the first Panel account

After the first deploy, visiting `/panel` shows **"The panel cannot be installed"** — Kirby refuses to run the account-creation installer on anything it detects as a public server, so a stranger can't beat you to creating the first admin account. `site/accounts` being empty on the server (correctly, per above) is exactly what triggers this. Two ways to get in:

- **Copy your local account over (simplest, same login everywhere):**
  ```bash
  rsync -avz -e "ssh -p ${SSH_PORT}" site/accounts/ ${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/site/accounts/
  ```
  (use the same `SSH_USER`/`SSH_HOST`/`REMOTE_PATH`/`SSH_PORT` values as in `deploy.sh`). This only works if you already created an account locally (`http://localhost:8000/panel` prompts for one on first visit). Log in with the same email/password afterward — you can change the password from inside the live Panel if you want it different from your local one. This is a one-time manual step, not something to add to `deploy.sh` — keep `site/accounts` excluded from the regular rsync, otherwise a password changed on the live Panel later would get silently overwritten by your local account on the next deploy.

- **Enable the installer temporarily instead (separate credentials per environment):** SSH in and edit the *server's* `site/config/config.php` directly (not the local repo — this should never be committed) to add:
  ```php
  'panel' => [
      'install' => true,
  ],
  ```
  Reload `/panel`, create the account, then remove that block again (or set it to `false`) — once an account exists, Kirby stops showing the installer regardless, so this just closes the door behind you. Because `config.php` is git-tracked, editing it on the server only (rather than locally + redeploying) means the next real deploy overwrites it back to the safe default automatically.

### What is excluded from the upload

- `.git`, `.gitignore`, `README.md`, `node_modules/`, `src/`
- `vendor/`, `kirby/` — installed on the server via Composer
- `deploy.sh`, `deploy-example.sh`, `pull.sh`, `pull-example.sh` — `pull.sh` holds the same server credentials as `deploy.sh`
- `site/accounts`, `site/sessions`, `site/cache`

Everything else in the project folder is uploaded, including `content/` — a deploy **overwrites the server's content with your local content**. If the association has edited pages in the live Panel since your last pull, run [`./pull.sh`](#pulling-content-from-the-server) *first*, or those edits are lost. `deploy.sh` asks you to confirm this before doing anything.

### Before the first deploy

- Make sure PHP 8.2+ is installed on the server with extensions: `mbstring`, `gd`, `curl`, `zip`, `intl`
- For Nginx servers, add a rewrite rule to route all requests through `index.php` (Apache/DreamHost is handled automatically via Kirby's `.htaccess`)
- Point the domain's web root at the project directory in the DreamHost panel
- Buy and activate the Kirby license for the domain (see [Licensing](#licensing))
- Check the [Legal pages and analytics](#legal-pages-and-analytics) checklist

### Pulling content from the server

Once the site is live, the association can edit content directly in the live Panel. `pull.sh` syncs `content/` (page text files and uploaded images) **down** from the server to your local copy, so it can be reviewed and committed. Set it up once:

```bash
cp pull-example.sh pull.sh
chmod +x pull.sh
```

Fill in the same `SSH_USER`/`SSH_HOST`/`REMOTE_PATH`/`SSH_PORT` values as `deploy.sh`. `pull.sh` is gitignored, same as `deploy.sh`, and is never uploaded by a deploy.

Then, from the project root:

```bash
./pull.sh --dry-run  # preview what would change, without touching local files
./pull.sh            # sync content/ down from the server
```

Recommended routine:

1. **Commit or stash any local content edits first** — the pull overwrites local files with the server's version of the same files (it never deletes local-only files, but it does replace ones that exist on both sides).
2. Run `./pull.sh --dry-run` and check the list.
3. Run `./pull.sh`, then review with `git status` / `git diff` and commit the result.

Only `content/` is pulled — never code, accounts, sessions or cache.

## Legal pages and analytics

`content/legal/privacy-policy/` and `content/legal/cookie-policy/` contain the association's real Privacy Policy and Cookie Policy, in Italian, written for this site's actual setup. They state, among other things, that:

- the site sets **no cookies** and loads **no third-party resources** (no external fonts, embedded videos or maps, social widgets — the WhatsApp/Instagram/Facebook links are plain outbound links), so there is **no cookie-consent banner**;
- there are **no forms**; people get in touch through WhatsApp, email, PEC or phone;
- server access logs are kept **7 days** on the hosting (DreamHost, USA);
- analytics is **[Umami](https://umami.is), self-hosted** on a Hetzner server in Germany with default settings (no cookies, no stored IP addresses, no profiling);
- the data controller is Ad Alta Voce APS (represented by its president and legal representative) and privacy requests go to the association's PEC address.

**Umami is not installed yet** — it will be added once the first deploy is confirmed to work. The tracker script will point at the association's own Umami instance and must stay on default (cookieless) settings.

**If you change any of the facts above, update both policy pages first** (Panel → Legal) — and if it involves cookies or a third-party service, a consent banner will probably be needed too. Typical triggers: Google Fonts, a YouTube or Google Maps embed, a contact or newsletter form, a donation button, or any analytics tool other than Umami.

## Project structure

```
content/        ← pages and uploaded files (tracked in git), incl. legal/ with the two policies
src/            ← Tailwind CSS + JS source (compiles to assets/)
assets/icons/   ← social icon SVGs for the icon field (tracked in git)
site/
  blueprints/   ← Panel field definitions: site.yml, pages/default.yml,
                  fields/hero.yml (shared hero fields), blocks/child-pages.yml
  config/       ← config.php (plugin settings, global block list)
  plugins/      ← third-party plugins (composer-managed ones are gitignored)
  templates/    ← PHP templates (default.php serves every page)
  snippets/     ← header, footer, hero, cta-button, language-switcher, blocks/child-pages
deploy-example.sh   ← copy to deploy.sh (gitignored) and fill in server details
pull-example.sh     ← copy to pull.sh (gitignored) to sync content/ from the server
setup-languages.sh  ← optional: enables multi-language mode (not used at the moment)
```

## Notes

- `vendor/` and `kirby/` are not committed — they are restored by `composer install`
- Never commit `site/accounts/`, `site/sessions/`, or `site/cache/`
- `deploy.sh` and `pull.sh` contain server credentials and are gitignored — never commit them
