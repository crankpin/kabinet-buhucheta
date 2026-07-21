<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/config.php';

$page = [
    'title' => 'Контакты — Кабинет Бухучёта',
    'description' => 'Связаться с Кабинетом Бухучёта: телефон, Telegram, WhatsApp, email.',
    'canonical' => SITE_DOMAIN . '/kontakty/',
    'body_class' => 'page-contacts',
];

$breadcrumbs = [['label' => 'Контакты']];

require dirname(__DIR__) . '/includes/layout-start.php';
?>
<section class="stub">
  <div class="container stub__inner">
    <?php require dirname(__DIR__) . '/includes/breadcrumbs.php'; ?>
    <h1 class="stub__title">Контакты</h1>
    <p class="stub__lead">Формат работы — удалённо. Офис: <?= e($siteContacts['address']) ?>.</p>
    <ul class="stub__notes">
      <li><a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a></li>
      <li><a href="tel:<?= e($siteContacts['phone_secondary_tel']) ?>"><?= e($siteContacts['phone_secondary']) ?></a></li>
      <li><a href="mailto:<?= e($siteContacts['email']) ?>"><?= e($siteContacts['email']) ?></a></li>
      <li><a href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Telegram</a></li>
      <li><a href="<?= e($siteContacts['whatsapp']) ?>" target="_blank" rel="noopener noreferrer nofollow">WhatsApp</a></li>
    </ul>
    <p class="stub__lead">Приоритетная география: Москва, Московская область, Ростов-на-Дону, Ростовская область, ЛНР, ДНР.</p>
  </div>
</section>
<?php
require dirname(__DIR__) . '/includes/layout-end.php';
