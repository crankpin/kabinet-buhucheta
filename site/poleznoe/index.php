<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/stub.php';
render_stub_page([
    'title' => 'Полезное — Кабинет Бухучёта',
    'description' => 'Статьи, проверка бухгалтера, налоговый квиз, частые вопросы.',
    'canonical_path' => '/poleznoe/',
    'h1' => 'Полезное',
    'lead' => 'Хаб для статей и интерактивов. Чек-лист и квиз переносятся на следующем этапе.',
    'breadcrumbs' => [['label' => 'Полезное']],
]);
