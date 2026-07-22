<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
/** @var array<int, array<string, mixed>> $siteNavTree */
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
      <div class="site-nav__chrome">
        <p class="site-nav__chrome-title">Меню</p>
        <button class="nav-close" type="button" id="nav-close" aria-label="Закрыть меню">&times;</button>
      </div>

      <div class="site-nav__scroll">
        <ul class="site-nav__list site-nav__list--desktop">
          <?php foreach ($siteNavTree as $item): ?>
            <?php
              $href = (string) $item['href'];
              $current = nav_is_current($href);
            ?>
            <li>
              <a href="<?= e(url($href)) ?>"<?= $current ? ' aria-current="page"' : '' ?>><?= e((string) $item['label']) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>

        <ul class="site-nav__list site-nav__list--mobile">
          <?php foreach ($siteNavTree as $item): ?>
            <?php
              $id = (string) ($item['id'] ?? '');
              $href = (string) $item['href'];
              $label = (string) $item['label'];
              $children = $item['children'] ?? null;
              $current = nav_is_current($href);
            ?>
            <?php if (is_array($children) && $children !== []): ?>
              <li class="site-nav__group">
                <button
                  type="button"
                  class="site-nav__accordion"
                  id="nav-acc-<?= e($id) ?>"
                  aria-expanded="false"
                  aria-controls="nav-panel-<?= e($id) ?>"
                >
                  <span><?= e($label) ?></span>
                  <span class="site-nav__chevron" aria-hidden="true"></span>
                </button>
                <div class="site-nav__panel" id="nav-panel-<?= e($id) ?>" hidden>
                  <ul>
                    <?php foreach ($children as $child): ?>
                      <?php
                        $childHref = (string) $child['href'];
                        $childCurrent = nav_is_current($childHref);
                      ?>
                      <li>
                        <a href="<?= e(url($childHref)) ?>"<?= $childCurrent ? ' aria-current="page"' : '' ?>><?= e((string) $child['label']) ?></a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </li>
            <?php else: ?>
              <li>
                <a class="site-nav__link" href="<?= e(url($href)) ?>"<?= $current ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>

        <div class="site-nav__actions">
          <a class="btn btn--accent site-nav__cta" href="<?= e(url('/kontakty/')) ?>">Получить консультацию</a>
          <div class="site-nav__contacts">
            <a href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram</a>
            <a href="<?= e($siteContacts['whatsapp']) ?>" target="_blank" rel="noopener noreferrer nofollow">WhatsApp</a>
            <a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a>
            <a href="mailto:<?= e($siteContacts['email']) ?>"><?= e($siteContacts['email']) ?></a>
          </div>
        </div>
      </div>
    </nav>

    <div class="site-header__phones" aria-label="Телефоны">
      <a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a>
      <a href="tel:<?= e($siteContacts['phone_secondary_tel']) ?>"><?= e($siteContacts['phone_secondary']) ?></a>
    </div>
  </div>
  <div class="nav-overlay" id="nav-overlay" hidden></div>
</header>
