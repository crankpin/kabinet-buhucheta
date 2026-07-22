<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/interactives.php';

$page = [
    'title' => 'Налоговый квиз — Кабинет Бухучёта',
    'description' => 'Мини-квиз из 5 вопросов о НДС, режиме и отчётности.',
    'canonical' => SITE_DOMAIN . '/poleznoe/nalogovyi-kviz/',
    'body_class' => 'page-quiz',
];
$breadcrumbs = [
    ['label' => 'Полезное', 'href' => '/poleznoe/'],
    ['label' => 'Налоговый квиз'],
];

require dirname(__DIR__, 2) . '/includes/layout-start.php';
?>
<section class="stub">
  <div class="container stub__inner">
    <?php require dirname(__DIR__, 2) . '/includes/breadcrumbs.php'; ?>
    <p class="eyebrow">Интерактив</p>
    <h1 class="stub__title">Налоговый квиз</h1>
    <p class="stub__lead">
      Пять вопросов помогут понять, стоит ли разбирать налоговую нагрузку подробнее.
      Результат не создаёт отдельный URL и не гарантирует принятие на сопровождение.
    </p>
    <?php render_quiz('full'); ?>
  </div>
</section>
<?php
require dirname(__DIR__, 2) . '/includes/layout-end.php';
