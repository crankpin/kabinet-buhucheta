<?php
declare(strict_types=1);

/**
 * Правый page-spine: локальное оглавление текущей страницы.
 *
 * @var array<int, array{id: string, label: string}> $spineItems
 */
$spineItems = $spineItems ?? [];
if ($spineItems === []) {
    return;
}
?>
<aside class="page-spine" aria-label="На этой странице">
  <p class="nav-kicker">На странице</p>
  <nav class="spine-sections">
    <?php foreach ($spineItems as $i => $sector): ?>
      <?php
        $sid = (string) ($sector['id'] ?? '');
        $slabel = (string) ($sector['label'] ?? '');
        if ($sid === '' || $slabel === '') {
            continue;
        }
        $isFirst = $i === 0;
      ?>
      <a
        class="spine-sector<?= $isFirst ? ' is-active' : '' ?>"
        href="#<?= e($sid) ?>"
        data-nav="<?= e($sid) ?>"
        <?= $isFirst ? ' aria-current="true"' : '' ?>
        style="--sector-progress: <?= $isFirst ? '0.08' : '0' ?>"
      >
        <span class="spine-sector__label"><?= e($slabel) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>
</aside>
