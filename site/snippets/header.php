<?php
$navItems = $site->children()->listed();
$logo = $site->logo()->toFile();

// Logo size — tweak here. Sizes are rem-based and the root font size changes
// with the screen (16px phones → 18px from 640px → 20px from 1024px, see
// src/main.css), so the logo scales with it. The file is ~4:1 wide, so
// h-9 ≈ 144px wide on a phone and h-12 ≈ 240px wide on desktop (`nav:` = the
// desktop-nav breakpoint). Try h-8 … h-10 for mobile and h-10 … h-16 for desktop.
// max-w-full + object-contain keep it from ever overflowing the header.
$logoClass = 'h-13 nav:h-16 w-auto max-w-full object-contain object-left';
?>
<!DOCTYPE html>
<html lang="<?= $kirby->multilang() ? $kirby->language()->code() : 'it' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page->title() ?> — <?= $site->title() ?></title>
  <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
  <script defer src="https://umami.artomultiplo.dev/script.js" data-website-id="fe8e29ae-8dfa-4512-b912-ca9fb802e477"></script>
</head>
<body class="min-h-screen flex flex-col bg-white font-sans text-neutral-800 antialiased">

<header id="site-header" class="relative z-40 border-b border-neutral-200">
  <div class="max-w-6xl mx-auto px-4 h-16 nav:h-20 flex items-center justify-between gap-4">

    <a href="<?= $site->url() ?>" class="flex min-w-0 items-center font-semibold tracking-tight text-lg">
      <?php if ($logo): ?>
        <img src="<?= $logo->url() ?>" alt="<?= esc($site->title()) ?>" class="<?= $logoClass ?>">
      <?php else: ?>
        <?= $site->title() ?>
      <?php endif ?>
    </a>

    <div class="hidden nav:flex items-center gap-6">
      <nav class="flex items-center gap-8 text-sm">
        <?php foreach ($navItems as $item): ?>
          <?php $subItems = $item->children()->listed() ?>
          <?php if ($subItems->isNotEmpty()): ?>
            <?php /* CSS-only dropdown: shown on hover or keyboard focus (group-focus-within); `invisible` keeps hidden links out of the tab order until then. One level only. */ ?>
            <div class="relative group">
              <a href="<?= $item->url() ?>" class="inline-flex items-center gap-1 hover:opacity-60 transition-opacity <?= $item->isOpen() ? 'font-medium' : '' ?>">
                <?= $item->title() ?>
                <svg class="h-3 w-3 transition-transform group-hover:rotate-180 group-focus-within:rotate-180" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5"/></svg>
              </a>
              <div class="absolute left-0 top-full z-50 pt-3 invisible opacity-0 transition-opacity group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                <ul class="min-w-48 rounded-lg border border-neutral-200 bg-white py-2 shadow-lg">
                  <?php foreach ($subItems as $sub): ?>
                    <li>
                      <a href="<?= $sub->url() ?>" class="block whitespace-nowrap px-4 py-2 hover:bg-neutral-50 <?= $sub->isOpen() ? 'font-medium' : '' ?>">
                        <?= $sub->title() ?>
                      </a>
                    </li>
                  <?php endforeach ?>
                </ul>
              </div>
            </div>
          <?php else: ?>
            <a href="<?= $item->url() ?>" class="hover:opacity-60 transition-opacity <?= $item->isOpen() ? 'font-medium' : '' ?>">
              <?= $item->title() ?>
            </a>
          <?php endif ?>
        <?php endforeach ?>
        <?php snippet('cta-button') ?>
      </nav>
      <?php snippet('language-switcher') ?>
    </div>

    <?php /* On mobile the call-to-action is a floating button (see footer.php), so only the hamburger lives here. */ ?>
    <div class="flex shrink-0 items-center nav:hidden">
      <button id="menu-toggle" class="p-2" aria-label="Toggle menu">
        <span class="block w-5 h-px bg-current mb-1.5"></span>
        <span class="block w-5 h-px bg-current mb-1.5"></span>
        <span class="block w-5 h-px bg-current"></span>
      </button>
    </div>
  </div>

  <nav id="mobile-menu" class="nav:hidden grid grid-rows-[0fr] opacity-0 -translate-y-1 pointer-events-none transition-all duration-200">
    <div class="overflow-hidden">
      <div class="border-t border-neutral-200 px-4 py-4 flex flex-col gap-4 text-sm">
        <?php foreach ($navItems as $item): ?>
          <div class="flex flex-col gap-3">
            <a href="<?= $item->url() ?>" class="<?= $item->isOpen() ? 'font-medium' : '' ?>">
              <?= $item->title() ?>
            </a>
            <?php /* Mobile has no hover, so children are always listed (indented) under their parent. */ ?>
            <?php foreach ($item->children()->listed() as $sub): ?>
              <a href="<?= $sub->url() ?>" class="pl-4 text-neutral-500 <?= $sub->isOpen() ? 'font-medium text-neutral-800' : '' ?>">
                <?= $sub->title() ?>
              </a>
            <?php endforeach ?>
          </div>
        <?php endforeach ?>
        <?php snippet('language-switcher') ?>
      </div>
    </div>
  </nav>
</header>
