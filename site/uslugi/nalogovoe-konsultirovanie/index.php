<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/includes/stub.php';
render_stub_page([
    'title' => 'Налоговое консультирование — Кабинет Бухучёта',
    'description' => 'Услуги дипломированного налогового консультанта для ИП и ООО.',
    'canonical_path' => '/uslugi/nalogovoe-konsultirovanie/',
    'h1' => 'Налоговое консультирование',
    'lead' => 'Услуги дипломированного налогового консультанта. Полный состав блоков — на следующем этапе.',
    'breadcrumbs' => [
        ['label' => 'Услуги', 'href' => '/uslugi/'],
        ['label' => 'Налоговое консультирование'],
    ],
]);
