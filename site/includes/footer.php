<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
/** @var array<int, array{label:string,href:string}> $siteNav */
/** @var array<int, array{label:string,href?:string}> $siteRegions */
/** @var array<int, array{title:string,href:string,teaser:string}> $siteServices */
/** @var array<string, string> $siteContacts */
?>
<footer class="site-footer" id="contacts">
  <div class="container site-footer__grid">
    <div class="site-footer__col">
      <h2 class="site-footer__title">Контакты</h2>
      <ul class="site-footer__list">
        <li><a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a></li>
        <li><a href="tel:<?= e($siteContacts['phone_secondary_tel']) ?>"><?= e($siteContacts['phone_secondary']) ?></a></li>
        <li><a href="mailto:<?= e($siteContacts['email']) ?>"><?= e($siteContacts['email']) ?></a></li>
        <li><?= e($siteContacts['address']) ?></li>
      </ul>
    </div>

    <div class="site-footer__col">
      <h2 class="site-footer__title">Услуги</h2>
      <ul class="site-footer__list">
        <?php foreach ($siteServices as $service): ?>
          <li><a href="<?= e(url($service['href'])) ?>"><?= e($service['title']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="site-footer__col">
      <h2 class="site-footer__title">Разделы</h2>
      <ul class="site-footer__list">
        <?php foreach ($siteNav as $item): ?>
          <li><a href="<?= e(url($item['href'])) ?>"><?= e($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="site-footer__col">
      <h2 class="site-footer__title">География</h2>
      <ul class="site-footer__list">
        <?php foreach ($siteRegions as $region): ?>
          <?php
            $regionHref = isset($region['href']) ? trim((string) $region['href']) : '';
          ?>
          <li>
            <?php if ($regionHref !== ''): ?>
              <a href="<?= e(url($regionHref)) ?>"><?= e($region['label']) ?></a>
            <?php else: ?>
              <?= e($region['label']) ?>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div class="container site-footer__bottom">
    <p>© <?= date('Y') ?> <?= e(SITE_LEGAL_NAME) ?>. <?= e(SITE_NAME) ?>.</p>
    <p>
      <a href="<?= e(url('/politika-konfidentsialnosti/')) ?>">Политика конфиденциальности</a>
      ·
      <a href="<?= e($siteContacts['telegram_channel']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram-канал</a>
      ·
      <a href="<?= e($siteContacts['vk']) ?>" target="_blank" rel="noopener noreferrer nofollow">ВКонтакте</a>
    </p>
  </div>
</footer>

<button class="contact-fab" id="contact-fab" type="button" aria-label="Связаться" aria-expanded="false" aria-controls="contact-panel">
  <img src="<?= e(asset('images/mandarin.png')) ?>" alt="" width="56" height="56" decoding="async">
</button>

<div class="contact-panel" id="contact-panel" role="dialog" aria-modal="true" aria-labelledby="contact-panel-title" hidden inert>
  <div class="contact-panel__header">
    <h2 id="contact-panel-title">Связаться</h2>
    <button type="button" class="contact-panel__close" id="contact-panel-close" aria-label="Закрыть">&times;</button>
  </div>
  <div class="contact-panel__list">
    <a href="<?= e($siteContacts['whatsapp']) ?>" target="_blank" rel="noopener noreferrer nofollow">
      <img src="<?= e(asset('images/icons/whatsapp.svg')) ?>" alt="" width="24" height="24">
      <span>WhatsApp</span>
    </a>
    <a href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">
      <img src="<?= e(asset('images/icons/telegram.svg')) ?>" alt="" width="24" height="24">
      <span>Telegram</span>
    </a>
    <a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>">
      <img src="<?= e(asset('images/icons/phone.svg')) ?>" alt="" width="24" height="24">
      <span><?= e($siteContacts['phone_primary']) ?></span>
    </a>
  </div>
</div>
<div class="contact-panel-overlay" id="contact-panel-overlay" hidden></div>

<script src="<?= e(asset('js/main.js')) ?>" defer></script>
<script src="<?= e(asset('js/nav-mega.js')) ?>" defer></script>
<?php if (!empty($page['scroll_stack'])): ?>
<script>
/* Decorative desktop-only scroll effect: fetch GSAP + its assets only when
   the viewport is actually desktop-sized and motion is not reduced, so
   phones never pay for CSS/JS they will never render. */
(function () {
  var desktopMq = window.matchMedia('(min-width: 1025px)');
  var reduceMq = window.matchMedia('(prefers-reduced-motion: reduce)');
  var loaded = false;

  function canRun() {
    return desktopMq.matches && !reduceMq.matches;
  }

  function loadScript(src) {
    return new Promise(function (resolve) {
      var s = document.createElement('script');
      s.src = src;
      s.onload = function () { resolve(true); };
      s.onerror = function () { resolve(false); };
      document.body.appendChild(s);
    });
  }

  function loadScrollStack() {
    if (loaded || !canRun()) return;
    loaded = true;

    try {
      var link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = <?= json_encode(asset('css/scroll-stack-bg.css'), JSON_UNESCAPED_SLASHES) ?>;
      document.head.appendChild(link);

      loadScript(<?= json_encode(asset('js/vendor/gsap.min.js'), JSON_UNESCAPED_SLASHES) ?>)
        .then(function (ok) {
          return ok ? loadScript(<?= json_encode(asset('js/vendor/ScrollTrigger.min.js'), JSON_UNESCAPED_SLASHES) ?>) : false;
        })
        .then(function (ok) {
          return ok ? loadScript(<?= json_encode(asset('js/scroll-stack-bg.js'), JSON_UNESCAPED_SLASHES) ?>) : false;
        })
        .catch(function () { /* decorative effect only — never break the page */ });
    } catch (err) { /* decorative effect only — never break the page */ }
  }

  loadScrollStack();

  if (typeof desktopMq.addEventListener === 'function') {
    desktopMq.addEventListener('change', loadScrollStack);
  } else if (typeof desktopMq.addListener === 'function') {
    desktopMq.addListener(loadScrollStack);
  }
  if (typeof reduceMq.addEventListener === 'function') {
    reduceMq.addEventListener('change', loadScrollStack);
  } else if (typeof reduceMq.addListener === 'function') {
    reduceMq.addListener(loadScrollStack);
  }
})();
</script>
<?php endif; ?>
</body>
</html>
