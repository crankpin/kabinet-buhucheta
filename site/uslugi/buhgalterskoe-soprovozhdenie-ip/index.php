<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/includes/stub.php';
render_stub_page([
    'title' => 'Бухгалтерское сопровождение ИП — Кабинет Бухучёта',
    'description' => 'Бухгалтерское сопровождение индивидуальных предпринимателей: учёт, отчётность, налоги.',
    'canonical_path' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/',
    'h1' => 'Бухгалтерское сопровождение ИП',
    'lead' => 'Посадочная страница услуги. Полный состав блоков — на следующем этапе.',
    'breadcrumbs' => [
        ['label' => 'Услуги', 'href' => '/uslugi/'],
        ['label' => 'Сопровождение ИП'],
    ],
]);
