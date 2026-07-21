<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/includes/stub.php';
render_stub_page([
    'title' => 'Бухгалтерское сопровождение ООО на УСН — Кабинет Бухучёта',
    'description' => 'Бухгалтерское сопровождение ООО на УСН. ООО на ОСНО не принимаются.',
    'canonical_path' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/',
    'h1' => 'Бухгалтерское сопровождение ООО на УСН',
    'lead' => 'Посадочная страница услуги. Ключевое ограничение: ООО на ОСНО не принимаются.',
    'breadcrumbs' => [
        ['label' => 'Услуги', 'href' => '/uslugi/'],
        ['label' => 'ООО на УСН'],
    ],
    'notes' => ['ООО на ОСНО не принимаются.'],
]);
