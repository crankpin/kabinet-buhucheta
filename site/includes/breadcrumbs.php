<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/** @var array<int, array{label:string,href?:string}> $breadcrumbs */
$breadcrumbs = $breadcrumbs ?? [];
if ($breadcrumbs === []) {
    return;
}
?>
<nav class="breadcrumbs" aria-label="Хлебные крошки">
  <ol class="breadcrumbs__list">
    <li><a href="<?= e(url('/')) ?>">Главная</a></li>
    <?php foreach ($breadcrumbs as $crumb): ?>
      <li>
        <?php if (!empty($crumb['href'])): ?>
          <a href="<?= e(url((string) $crumb['href'])) ?>"><?= e($crumb['label']) ?></a>
        <?php else: ?>
          <span aria-current="page"><?= e($crumb['label']) ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</nav>
