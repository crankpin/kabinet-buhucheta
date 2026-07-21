<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
/** @var array<string, string> $siteContacts */

$ctaTitle = $ctaTitle ?? 'Получить консультацию';
$ctaText = $ctaText ?? 'Опишите ситуацию — разберём режим, риски и порядок работы.';
?>
<section class="cta-band" aria-labelledby="cta-band-title">
  <div class="container cta-band__inner">
    <div>
      <h2 id="cta-band-title" class="cta-band__title"><?= e($ctaTitle) ?></h2>
      <p class="cta-band__text"><?= e($ctaText) ?></p>
    </div>
    <div class="cta-band__actions">
      <a class="btn btn--accent" href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram</a>
      <a class="btn btn--ghost" href="<?= e(url('/kontakty/')) ?>">Все способы связи</a>
    </div>
  </div>
</section>
