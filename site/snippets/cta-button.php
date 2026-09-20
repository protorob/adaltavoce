<?php
$ctaLabel = $site->ctaLabel();
$ctaUrl = $site->ctaUrl();
$ctaIcon = $site->ctaIcon();

// Colors are picked in the Panel at runtime, so they go in an inline style
// (Tailwind can't generate classes for values it can't see at build time),
// escaped once for the 'attr' context. Defaults match the original look.
$ctaColor     = $site->ctaColor()->or('#262626');
$ctaTextColor = $site->ctaTextColor()->or('#ffffff');

if ($ctaLabel->isNotEmpty() && $ctaUrl->isNotEmpty()):
?>
  <a href="<?= esc($ctaUrl, 'attr') ?>"
     style="background-color: <?= esc($ctaColor, 'attr') ?>; color: <?= esc($ctaTextColor, 'attr') ?>;"
     class="inline-flex items-center gap-2 font-medium rounded-full hover:opacity-90 transition-opacity [&>svg]:h-4 [&>svg]:w-4 [&>svg]:fill-current <?= $class ?? 'text-sm px-4 py-2' ?>">
    <?php if ($ctaIcon->isNotEmpty()): ?>
      <?= svg('/assets/icons/' . $ctaIcon) ?>
    <?php endif ?>
    <?= esc($ctaLabel) ?>
  </a>
<?php endif ?>
