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

$siteNav = [
    ['label' => 'Услуги', 'href' => '/uslugi/'],
    ['label' => 'Налоги и режимы', 'href' => '/nalogi/'],
    ['label' => 'Кому помогаем', 'href' => '/komu-pomogaem/'],
    ['label' => 'Полезное', 'href' => '/poleznoe/'],
    ['label' => 'О Карине', 'href' => '/o-karine/'],
    ['label' => 'Контакты', 'href' => '/kontakty/'],
];

$siteRegions = [
    ['label' => 'Москва', 'href' => '/regiony/moskva/'],
    ['label' => 'Московская область', 'href' => '/regiony/moskovskaya-oblast/'],
    ['label' => 'Ростов-на-Дону', 'href' => '/regiony/rostov-na-donu/'],
    ['label' => 'Ростовская область', 'href' => '/regiony/rostovskaya-oblast/'],
    ['label' => 'ЛНР', 'href' => '/regiony/lnr/'],
    ['label' => 'ДНР', 'href' => '/regiony/dnr/'],
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
 * URL с учётом BASE_PATH.
 */
function url(string $path = '/'): string
{
    if ($path === '') {
        $path = '/';
    }
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return BASE_PATH . $path;
}

/**
 * Путь к статическому файлу в /assets.
 */
function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

/**
 * Экранирование для HTML-текста.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Значения страницы по умолчанию.
 *
 * @param array<string, mixed> $page
 * @return array<string, mixed>
 */
function page_defaults(array $page): array
{
    return array_merge([
        'title' => SITE_NAME . ' — ' . SITE_LEGAL_NAME,
        'description' => 'Бухгалтерское сопровождение ИП и ООО на УСН. Налоговое консультирование.',
        'canonical' => SITE_DOMAIN . '/',
        'og_image' => SITE_DOMAIN . '/assets/images/og-image.jpg',
        'body_class' => '',
        'noindex' => false,
    ], $page);
}
