<?php
declare(strict_types=1);

http_response_code(404);

require_once __DIR__ . '/includes/config.php';

$page = [
    'title' => 'Страница не найдена — Кабинет Бухучёта',
    'description' => 'Запрошенная страница не найдена.',
    'canonical' => SITE_DOMAIN . '/404',
    'body_class' => 'page-404',
    'noindex' => true,
];

require __DIR__ . '/includes/layout-start.php';
?>
<section class="stub">
  <div class="container stub__inner">
    <p class="eyebrow">404</p>
    <h1 class="stub__title">Страница не найдена</h1>
    <p class="stub__lead">Проверьте адрес или перейдите к нужному разделу.</p>
    <div class="stub__actions">
      <a class="btn btn--accent" href="<?= e(url('/')) ?>">На главную</a>
      <a class="btn btn--ghost" href="<?= e(url('/uslugi/')) ?>">Услуги</a>
      <a class="btn btn--ghost" href="<?= e(url('/kontakty/')) ?>">Контакты</a>
    </div>
  </div>
</section>
<?php
require __DIR__ . '/includes/layout-end.php';
