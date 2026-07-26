<?php
declare(strict_types=1);

/**
 * Общая конфигурация сайта v2.
 * Контакты и факты — только из legacy / docs, без выдумок.
 */

const SITE_NAME = 'Кабинет Бухучёта';
const SITE_LEGAL_NAME = 'ИП Сизонова Карина Вадимовна';
const SITE_DOMAIN = 'https://kabinetbuhucheta.ru';

/** Базовый путь, если сайт лежит не в корне (тестовая папка). Пусто = корень. */
const BASE_PATH = '';

/**
 * Боевой хост. На localhost / тестовом домене страницы получают noindex.
 */
function is_production_host(): bool
{
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host) ?? $host;
    return $host === 'kabinetbuhucheta.ru' || $host === 'www.kabinetbuhucheta.ru';
}

$siteContacts = [
    'phone_primary' => '+7 (918) 554-05-34',
    'phone_primary_tel' => '+79185540534',
    'phone_secondary' => '+7 (863) 294-05-34',
    'phone_secondary_tel' => '+78632940534',
    'email' => '79185540534@yandex.ru',
    'telegram' => 'https://t.me/sizonova_karina',
    'whatsapp' => 'https://wa.me/79185540534',
    'telegram_channel' => 'https://t.me/+4_zcN5nNj0E4NmJi',
    'vk' => 'https://vk.com/kabinbuh',
    'address' => 'Ростов-на-Дону, ул. Максима Горького, 151',
    'yandex_uslugi' => 'https://uslugi.yandex.ru/profile/KabinetBukhuchyotaIpSizonovaKarinaVadimovna-169764',
];

/**
 * Горизонтальная навигация: раздел = страница.
 * Контакты — panel действий (не отдельный пункт-страница в смысле mega).
 * «О Карине» и «Отзывы» в top-nav нет.
 *
 * @var array<int, array<string, mixed>>
 */
$siteNavTree = [
    [
        'id' => 'ip',
        'label' => 'ИП',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/',
        'lede' => 'Сопровождение ИП',
        'links' => [
            ['label' => 'К началу страницы', 'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/'],
        ],
        'previews' => [
            ['caption' => 'Кому подходит', 'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/', 'pos' => '50% 18%', 'rot' => -4],
            ['caption' => 'Что входит', 'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/', 'pos' => '50% 72%', 'rot' => 3],
        ],
    ],
    [
        'id' => 'ooo',
        'label' => 'ООО',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/',
        'lede' => 'Сопровождение ООО на УСН',
        'links' => [
            ['label' => 'К началу страницы', 'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/'],
        ],
        'previews' => [
            ['caption' => 'Кому подходит', 'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/', 'pos' => '30% 20%', 'rot' => -2],
            ['caption' => 'Стоимость', 'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/', 'pos' => '70% 65%', 'rot' => 5],
        ],
    ],
    [
        'id' => 'tax-consult',
        'label' => 'Консалтинг',
        'href' => '/uslugi/nalogovoe-konsultirovanie/',
        'lede' => 'Налоговое консультирование',
        'links' => [
            ['label' => 'К началу страницы', 'href' => '/uslugi/nalogovoe-konsultirovanie/'],
        ],
        'previews' => [
            ['caption' => 'Как работаем', 'href' => '/uslugi/nalogovoe-konsultirovanie/', 'pos' => '20% 40%', 'rot' => -5],
            ['caption' => 'Стоимость', 'href' => '/uslugi/nalogovoe-konsultirovanie/', 'pos' => '80% 30%', 'rot' => 2],
        ],
    ],
    [
        'id' => 'nalogi',
        'label' => 'Налоги',
        'href' => '/nalogi/',
        'lede' => 'Режимы и смежные темы',
        'links' => [
            ['label' => 'Обзор', 'href' => '/nalogi/'],
            ['label' => 'УСН с НДС', 'href' => '/nalogi/usn-s-nds/'],
            ['label' => 'АУСН', 'href' => '/nalogi/ausn/'],
            ['label' => 'Переход с ПСН', 'href' => '/nalogi/perehod-s-psn/'],
            ['label' => 'Выбор системы', 'href' => '/nalogi/vybor-sistemy-nalogooblozheniya/'],
        ],
        'previews' => [
            ['caption' => 'УСН с НДС', 'href' => '/nalogi/usn-s-nds/', 'pos' => '40% 25%', 'rot' => -3],
            ['caption' => 'АУСН', 'href' => '/nalogi/ausn/', 'pos' => '60% 70%', 'rot' => 4],
        ],
    ],
    [
        'id' => 'komu',
        'label' => 'Кому',
        'href' => '/komu-pomogaem/',
        'lede' => 'Отрасли и ситуации',
        'links' => [
            ['label' => 'Обзор', 'href' => '/komu-pomogaem/'],
            ['label' => 'Розница', 'href' => '/komu-pomogaem/roznichnaya-torgovlya/'],
            ['label' => 'Опт', 'href' => '/komu-pomogaem/optovaya-torgovlya/'],
            ['label' => 'Кафе', 'href' => '/komu-pomogaem/kafe-i-obshchepit/'],
            ['label' => 'Красота', 'href' => '/komu-pomogaem/industriya-krasoty/'],
            ['label' => 'Гостиницы', 'href' => '/komu-pomogaem/gostinitsy-i-razmeshchenie/'],
            ['label' => 'Услуги', 'href' => '/komu-pomogaem/biznes-v-sfere-uslug/'],
        ],
        'previews' => [
            ['caption' => 'Розница', 'href' => '/komu-pomogaem/roznichnaya-torgovlya/', 'pos' => '25% 35%', 'rot' => -4],
            ['caption' => 'Кафе', 'href' => '/komu-pomogaem/kafe-i-obshchepit/', 'pos' => '75% 55%', 'rot' => 3],
        ],
    ],
    [
        'id' => 'poleznoe',
        'label' => 'Полезное',
        'href' => '/poleznoe/',
        'lede' => 'Материалы и инструменты',
        'links' => [
            ['label' => 'Обзор', 'href' => '/poleznoe/'],
            ['label' => 'Статьи', 'href' => '/poleznoe/stati/'],
            ['label' => 'Самопроверка', 'href' => '/poleznoe/proverit-buhgaltera/'],
            ['label' => 'Квиз', 'href' => '/poleznoe/nalogovyi-kviz/'],
            ['label' => 'Вопросы', 'href' => '/poleznoe/chastye-voprosy/'],
        ],
        'previews' => [
            ['caption' => 'Самопроверка', 'href' => '/poleznoe/proverit-buhgaltera/', 'pos' => '45% 22%', 'rot' => -2],
            ['caption' => 'Квиз', 'href' => '/poleznoe/nalogovyi-kviz/', 'pos' => '55% 68%', 'rot' => 4],
        ],
    ],
    [
        'id' => 'contacts',
        'label' => 'Контакты',
        'type' => 'contacts',
        'lede' => 'Все способы связи — здесь',
    ],
];

/** Плоский список разделов с URL (футер; без panel-only пунктов). */
$siteNav = [];
foreach ($siteNavTree as $item) {
    if (($item['type'] ?? '') === 'contacts') {
        $siteNav[] = ['label' => 'Контакты', 'href' => '/kontakty/'];
        continue;
    }
    if (!empty($item['href'])) {
        $siteNav[] = [
            'label' => (string) $item['label'],
            'href' => (string) $item['href'],
        ];
    }
}

/** Регионы: ссылки появятся после создания страниц /regiony/... */
$siteRegions = [
    ['label' => 'Москва'],
    ['label' => 'Московская область'],
    ['label' => 'Ростов-на-Дону'],
    ['label' => 'Ростовская область'],
    ['label' => 'ЛНР'],
    ['label' => 'ДНР'],
];

$siteServices = [
    [
        'title' => 'Бухгалтерское сопровождение ИП',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/',
        'teaser' => 'Учёт, отчётность и налоги для индивидуальных предпринимателей.',
    ],
    [
        'title' => 'Бухгалтерское сопровождение ООО на УСН',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/',
        'teaser' => 'Сопровождение обществ на упрощённой системе. ООО на ОСНО не принимаются.',
    ],
    [
        'title' => 'Налоговое консультирование',
        'href' => '/uslugi/nalogovoe-konsultirovanie/',
        'teaser' => 'Услуги дипломированного налогового консультанта.',
    ],
];

/**
 * Интерактивы на целевых страницах (не отдельный каталог квизов).
 *
 * Смысл:
 * - удержать посетителя на странице;
 * - в конце сценария вести к услугам / консультации.
 * Отдельные URL квизов в меню не выводим.
 *
 * @var array<int, array<string, mixed>>
 */
$siteInteractives = [
    [
        'id' => 'checklist',
        'title' => 'Проверить бухгалтера',
        'placement' => 'home',
        'cta' => '/uslugi/',
    ],
    [
        'id' => 'quiz',
        'title' => 'Налоговый квиз',
        'placement' => 'home',
        'cta' => '/uslugi/',
    ],
];

/**
 * URL с учётом BASE_PATH.
 */
function url(string $path = '/'): string
{
    if ($path === '') {
        $path = '/';
    }
    // Якорные ссылки вида /#reviews
    if (str_starts_with($path, '/#')) {
        return BASE_PATH . $path;
    }
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return BASE_PATH . $path;
}

/**
 * Путь к статическому файлу в /assets (с cache-bust по mtime).
 */
function asset(string $path): string
{
    $rel = ltrim($path, '/');
    $url = url('/assets/' . $rel);
    $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (is_file($file)) {
        $url .= '?v=' . (string) filemtime($file);
    }
    return $url;
}

/**
 * Экранирование для HTML-текста.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Текущий путь запроса без BASE_PATH.
 */
function current_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $uri = is_string($uri) && $uri !== '' ? $uri : '/';
    if (BASE_PATH !== '' && str_starts_with($uri, BASE_PATH)) {
        $uri = substr($uri, strlen(BASE_PATH)) ?: '/';
    }
    if ($uri !== '/' && !str_ends_with($uri, '/') && !preg_match('/\.[a-z0-9]+$/i', $uri)) {
        $uri .= '/';
    }
    return $uri;
}

/**
 * Проверка активного пункта меню.
 */
function nav_is_current(string $href): bool
{
    if (str_contains($href, '#')) {
        return false;
    }
    $path = current_path();
    $target = $href;
    if ($target !== '/' && !str_ends_with($target, '/')) {
        $target .= '/';
    }
    if ($target === '/') {
        return $path === '/';
    }
    return str_starts_with($path, $target);
}

/**
 * Значения страницы по умолчанию.
 *
 * @param array<string, mixed> $page
 * @return array<string, mixed>
 */
function page_defaults(array $page): array
{
    $defaults = [
        'title' => SITE_NAME . ' — ' . SITE_LEGAL_NAME,
        'description' => 'Бухгалтерское сопровождение ИП и ООО на УСН. Налоговое консультирование.',
        'canonical' => SITE_DOMAIN . '/',
        'og_image' => SITE_DOMAIN . '/assets/images/og-image.jpg',
        'body_class' => '',
        'noindex' => !is_production_host(),
    ];
    $merged = array_merge($defaults, $page);
    if (!is_production_host()) {
        $merged['noindex'] = true;
    }
    return $merged;
}
