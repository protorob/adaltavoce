<?php
/** @var \Kirby\Cms\Block $block */
$parent   = $block->page()->toPage();
$children = $parent?->children()->listed();

if (!$children || $children->isEmpty()) {
  return;
}
?>
<?php /* not-prose: the block sits inside default.php's .prose wrapper, which would restyle these links/headings */ ?>
<div class="not-prose my-10 grid gap-6 sm:grid-cols-2">
  <?php foreach ($children as $child): ?>
    <a href="<?= $child->url() ?>" class="block rounded-xl border border-neutral-200 p-6 hover:border-neutral-400 transition-colors">
      <h2 class="text-lg font-semibold"><?= $child->title()->esc() ?></h2>
    </a>
  <?php endforeach ?>
</div>
