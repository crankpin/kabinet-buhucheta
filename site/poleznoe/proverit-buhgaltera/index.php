<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/interactives.php';

$page = [
    'title' => 'Проверить бухгалтера — Кабинет Бухучёта',
    'description' => 'Чек-лист из 6 вопросов: оцените качество работы текущего бухгалтера.',
    'canonical' => SITE_DOMAIN . '/poleznoe/proverit-buhgaltera/',
    'body_class' => 'page-checklist',
];
$breadcrumbs = [
    ['label' => 'Полезное', 'href' => '/poleznoe/'],
    ['label' => 'Проверить бухгалтера'],
];

require dirname(__DIR__, 2) . '/includes/layout-start.php';
?>
<section class="stub">
  <div class="container stub__inner">
    <?php require dirname(__DIR__, 2) . '/includes/breadcrumbs.php'; ?>
    <p class="eyebrow">Интерактив</p>
    <h1 class="stub__title">Проверьте своего бухгалтера</h1>
    <p class="stub__lead">
      Отметьте пункты, которые выполняются у вас сейчас. Это не обещание принятия на сопровождение,
      а способ понять, где могут быть риски.
    </p>
    <?php render_checklist('full'); ?>
  </div>
</section>
<?php
require dirname(__DIR__, 2) . '/includes/layout-end.php';
