<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
/** @var array<int, array{label:string,href:string}> $siteNav */
/** @var array<string, string> $siteContacts */
?>
<header class="site-header" id="site-header">
  <div class="site-header__inner">
    <a class="site-logo" href="<?= e(url('/')) ?>" aria-label="<?= e(SITE_NAME) ?> — на главную">
      <img
        src="<?= e(asset('images/logo.svg')) ?>"
        alt="Логотип <?= e(SITE_NAME) ?>"
        width="72"
        height="72"
        class="site-logo__img"
      >
      <span class="site-logo__text">
        <span class="site-logo__name"><?= e(SITE_NAME) ?></span>
        <span class="site-logo__sub"><?= e(SITE_LEGAL_NAME) ?></span>
      </span>
    </a>

    <button
      class="nav-toggle"
      type="button"
      id="nav-toggle"
      aria-label="Открыть меню"
      aria-expanded="false"
      aria-controls="site-nav"
    >
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </button>

    <nav class="site-nav" id="site-nav" aria-label="Основная навигация">
      <button class="nav-close" type="button" id="nav-close" aria-label="Закрыть меню">&times;</button>
      <ul class="site-nav__list">
        <?php foreach ($siteNav as $item): ?>
          <li><a href="<?= e(url($item['href'])) ?>"><?= e($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <a class="btn btn--accent site-nav__cta" href="<?= e(url('/kontakty/')) ?>">Получить консультацию</a>
      <div class="site-nav__contacts">
        <a href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram</a>
        <a href="<?= e($siteContacts['whatsapp']) ?>" target="_blank" rel="noopener noreferrer nofollow">WhatsApp</a>
        <a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a>
      </div>
    </nav>

    <div class="site-header__phones" aria-label="Телефоны">
      <a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a>
      <a href="tel:<?= e($siteContacts['phone_secondary_tel']) ?>"><?= e($siteContacts['phone_secondary']) ?></a>
    </div>
  </div>
  <div class="nav-overlay" id="nav-overlay" hidden></div>
</header>
