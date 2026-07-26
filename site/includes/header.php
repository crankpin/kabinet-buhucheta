<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
/** @var array<int, array<string, mixed>> $siteNavTree */
/** @var array<string, string> $siteContacts */

$karinaPreview = asset('images/karina-hero.webp');
?>
<header class="site-header site-header--mega" id="site-header">
  <div class="site-header__bar">
    <div class="site-header__inner">
      <a class="site-logo" href="<?= e(url('/')) ?>" aria-label="<?= e(SITE_NAME) ?> — на главную">
        <img
          src="<?= e(asset('images/logo.svg')) ?>"
          alt=""
          width="44"
          height="44"
          class="site-logo__img"
        >
        <span class="site-logo__text">
          <span class="site-logo__name"><?= e(SITE_NAME) ?></span>
        </span>
      </a>

      <button
        class="nav-toggle"
        type="button"
        id="nav-toggle"
        aria-label="Открыть меню"
        aria-expanded="false"
        aria-controls="site-nav-mobile"
      >
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </button>

      <nav class="site-header__nav" aria-label="Разделы сайта">
        <a class="site-header__home" href="<?= e(url('/')) ?>" aria-label="На главную">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 10.5L12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
          </svg>
        </a>
        <?php foreach ($siteNavTree as $item): ?>
          <?php
            $id = (string) ($item['id'] ?? '');
            $label = (string) ($item['label'] ?? '');
            $type = (string) ($item['type'] ?? 'page');
            $href = (string) ($item['href'] ?? '');
            $current = $href !== '' && nav_is_current($href);
          ?>
          <?php if ($type === 'contacts'): ?>
            <button
              type="button"
              class="site-header__item site-header__item--action"
              data-mega-hover="contacts"
              aria-expanded="false"
              aria-controls="mega-panel"
            ><?= e($label) ?></button>
          <?php else: ?>
            <a
              class="site-header__item"
              href="<?= e(url($href)) ?>"
              data-mega-hover="<?= e($id) ?>"
              aria-expanded="false"
              aria-controls="mega-panel"
              <?= $current ? ' aria-current="page"' : '' ?>
            ><?= e($label) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>

      <div
        class="site-header__search"
        data-search-index="<?= e(asset('data/navigation-index.json')) ?>"
      >
        <label class="sr-only" for="header-search">Поиск по темам</label>
        <input
          class="site-header__search-field"
          id="header-search"
          type="search"
          placeholder="Найти…"
          autocomplete="off"
        >
        <div class="site-header__search-out" id="header-search-out" hidden></div>
      </div>

      <div class="site-header__actions">
        <a class="site-header__cta" href="<?= e(url('/kontakty/')) ?>">Консультация</a>
      </div>
    </div>
  </div>

  <div class="mega" id="mega-panel">
    <div class="mega__clip">
      <div class="mega__inner">
        <?php foreach ($siteNavTree as $item): ?>
          <?php
            $id = (string) ($item['id'] ?? '');
            $type = (string) ($item['type'] ?? 'page');
            if ($type === 'contacts') {
                continue;
            }
            $label = (string) ($item['label'] ?? '');
            $lede = (string) ($item['lede'] ?? '');
            $links = $item['links'] ?? [];
            $previews = $item['previews'] ?? [];
            $left = is_array($previews) && isset($previews[0]) && is_array($previews[0]) ? $previews[0] : null;
            $right = is_array($previews) && isset($previews[1]) && is_array($previews[1]) ? $previews[1] : null;
          ?>
          <div class="mega__panel" data-mega-panel="<?= e($id) ?>" hidden>
            <p class="mega__title"><?= e($label) ?></p>
            <?php if ($lede !== ''): ?>
              <p class="mega__lede"><?= e($lede) ?></p>
            <?php endif; ?>
            <div class="mega__stage">
              <div class="mega__stickers mega__stickers--left">
                <?php if ($left): ?>
                  <?php
                    $rot = isset($left['rot']) ? (float) $left['rot'] : -4.0;
                    $pos = (string) ($left['pos'] ?? '50% 50%');
                  ?>
                  <a
                    class="mega__sticker mega__sticker--left"
                    href="<?= e(url((string) ($left['href'] ?? '#'))) ?>"
                    style="--sticker-rot: <?= e((string) $rot) ?>deg"
                  >
                    <span
                      class="mega__sticker-img"
                      style="background-image:url(<?= e($karinaPreview) ?>);background-position:<?= e($pos) ?>"
                    ></span>
                    <span class="mega__sticker-cap"><?= e((string) ($left['caption'] ?? '')) ?></span>
                  </a>
                <?php else: ?>
                  <div class="mega__sticker-slot" aria-hidden="true"></div>
                <?php endif; ?>
              </div>
              <div class="mega__links">
                <?php if (is_array($links)): ?>
                  <?php foreach ($links as $link): ?>
                    <a href="<?= e(url((string) $link['href'])) ?>"><?= e((string) $link['label']) ?></a>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <div class="mega__stickers mega__stickers--right">
                <?php if ($right): ?>
                  <?php
                    $rot = isset($right['rot']) ? (float) $right['rot'] : 3.0;
                    $pos = (string) ($right['pos'] ?? '50% 50%');
                  ?>
                  <a
                    class="mega__sticker mega__sticker--right"
                    href="<?= e(url((string) ($right['href'] ?? '#'))) ?>"
                    style="--sticker-rot: <?= e((string) $rot) ?>deg"
                  >
                    <span
                      class="mega__sticker-img"
                      style="background-image:url(<?= e($karinaPreview) ?>);background-position:<?= e($pos) ?>"
                    ></span>
                    <span class="mega__sticker-cap"><?= e((string) ($right['caption'] ?? '')) ?></span>
                  </a>
                <?php else: ?>
                  <div class="mega__sticker-slot" aria-hidden="true"></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="mega__panel" data-mega-panel="contacts" hidden>
          <p class="mega__title">Контакты</p>
          <p class="mega__lede">Все способы связи — здесь</p>
          <div class="mega-contacts">
            <a class="mega-contacts__item is-primary" href="<?= e(url('/kontakty/')) ?>">Получить консультацию</a>
            <a class="mega-contacts__item" href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram</a>
            <a class="mega-contacts__item" href="<?= e($siteContacts['whatsapp']) ?>" target="_blank" rel="noopener noreferrer nofollow">WhatsApp</a>
            <a class="mega-contacts__item" href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a>
            <a class="mega-contacts__item" href="mailto:<?= e($siteContacts['email']) ?>"><?= e($siteContacts['email']) ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

  <div class="nav-overlay" id="nav-overlay" hidden></div>

  <div class="poster site-nav-mobile" id="site-nav-mobile" role="dialog" aria-modal="true" aria-label="Меню" aria-hidden="true">
    <div class="poster__chrome">
      <div class="poster__head">
        <div class="title-wrap">
          <div class="kicker"><?= e(SITE_NAME) ?></div>
          <div class="title">МЕНЮ</div>
        </div>
        <div class="poster__tools">
          <button type="button" class="poster__close" id="nav-close" aria-label="Закрыть меню">
            <span aria-hidden="true">×</span> ЗАКРЫТЬ
          </button>
        </div>
      </div>
    </div>
    <div class="poster__body poster__body--scroll">
      <ul class="poster-acc">
        <?php foreach ($siteNavTree as $item): ?>
          <?php
            $id = (string) ($item['id'] ?? '');
            $label = (string) ($item['label'] ?? '');
            $type = (string) ($item['type'] ?? 'page');
            $href = (string) ($item['href'] ?? '');
            $links = $item['links'] ?? [];
          ?>
          <li class="poster-acc__item" data-acc="<?= e($id) ?>">
            <div class="poster-acc__row">
              <?php if ($type === 'contacts'): ?>
                <button type="button" class="poster-acc__page poster-acc__page--btn" data-acc-open="<?= e($id) ?>"><?= e($label) ?></button>
              <?php else: ?>
                <a class="poster-acc__page" href="<?= e(url($href)) ?>"><?= e($label) ?></a>
              <?php endif; ?>
              <button type="button" class="poster-acc__toggle" aria-expanded="false" aria-label="Подменю: <?= e($label) ?>">
                <span class="poster-acc__plus" aria-hidden="true"></span>
              </button>
            </div>
            <div class="poster-acc__panel" hidden>
              <div class="poster-acc__links">
                <?php if ($type === 'contacts'): ?>
                  <a href="<?= e(url('/kontakty/')) ?>">Получить консультацию</a>
                  <a href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram</a>
                  <a href="<?= e($siteContacts['whatsapp']) ?>" target="_blank" rel="noopener noreferrer nofollow">WhatsApp</a>
                  <a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a>
                <?php elseif (is_array($links)): ?>
                  <?php foreach ($links as $link): ?>
                    <a href="<?= e(url((string) $link['href'])) ?>"><?= e((string) $link['label']) ?></a>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="poster__foot">
      <a class="cta" href="<?= e(url('/kontakty/')) ?>">Получить консультацию</a>
      <div class="channels">
        <a href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram</a>
        <a href="<?= e($siteContacts['whatsapp']) ?>" target="_blank" rel="noopener noreferrer nofollow">WhatsApp</a>
      </div>
    </div>
  </div>
