<?php
/**
 * Header call-to-action button.
 *
 * Two variants, both from the same Site Settings fields:
 *  - default: the pill in the desktop nav (header.php).
 *  - `floating => true`: a round icon button fixed to the bottom-right corner,
 *    shown only BELOW the desktop-nav breakpoint (`nav:hidden`), so on phones
 *    the header only carries the logo + hamburger. Rendered from footer.php
 *    (not inside <header>) so it isn't affected by the header's stacking context.
 */
$ctaLabel = $site->ctaLabel();
$ctaUrl = $site->ctaUrl();
$ctaIcon = $site->ctaIcon();
$floating = $floating ?? false;

// Colors are picked in the Panel at runtime, so they go in an inline style
// (Tailwind can't generate classes for values it can't see at build time),
// escaped once for the 'attr' context. Defaults match the original look.
$ctaColor     = $site->ctaColor()->or('#262626');
$ctaTextColor = $site->ctaTextColor()->or('#ffffff');
$ctaStyle     = 'background-color: ' . esc($ctaColor, 'attr') . '; color: ' . esc($ctaTextColor, 'attr') . ';';

if ($ctaLabel->isNotEmpty() && $ctaUrl->isNotEmpty()):
?>
<?php if ($floating): ?>
  <?php
  // With an icon: a 56px circle, the label kept for screen readers. Without one
  // the label itself is shown, as a pill. z-30 keeps it under the header (z-40),
  // so an open mobile menu is never covered by it. The bottom offset adds the
  // iPhone home-indicator safe area.
  $hasIcon = $ctaIcon->isNotEmpty();
  ?>
  <a id="cta-float" href="<?= esc($ctaUrl, 'attr') ?>"
     style="<?= $ctaStyle ?>"
     class="nav:hidden fixed right-4 bottom-[calc(1rem+env(safe-area-inset-bottom))] z-30 inline-flex items-center justify-center rounded-full shadow-lg hover:opacity-90 transition-opacity [&>svg]:fill-current <?= $hasIcon ? 'h-14 w-14 [&>svg]:h-7 [&>svg]:w-7' : 'h-12 px-5 text-sm font-medium' ?>">
    <?php if ($hasIcon): ?>
      <?= svg('/assets/icons/' . $ctaIcon) ?>
      <span class="sr-only"><?= esc($ctaLabel) ?></span>
    <?php else: ?>
      <?= esc($ctaLabel) ?>
    <?php endif ?>
  </a>
<?php else: ?>
  <a href="<?= esc($ctaUrl, 'attr') ?>"
     style="<?= $ctaStyle ?>"
     class="inline-flex items-center gap-2 font-medium rounded-full hover:opacity-90 transition-opacity [&>svg]:h-4 [&>svg]:w-4 [&>svg]:fill-current <?= $class ?? 'text-sm px-4 py-2' ?>">
    <?php if ($ctaIcon->isNotEmpty()): ?>
      <?= svg('/assets/icons/' . $ctaIcon) ?>
    <?php endif ?>
    <?= esc($ctaLabel) ?>
  </a>
<?php endif ?>
<?php endif ?>
