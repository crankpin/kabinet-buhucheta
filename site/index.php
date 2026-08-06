<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

$page = [
    'title' => 'Кабинет Бухучёта — бухгалтерское сопровождение ИП и ООО на УСН',
    'description' => 'Бухгалтерское сопровождение ИП и ООО на УСН. Налоговое консультирование. УСН, НДС, АУСН, переход с ПСН.',
    'canonical' => SITE_DOMAIN . '/',
    'body_class' => 'page-home',
    'scroll_stack' => true,
    'spine' => [
        ['id' => 'nachalo', 'label' => 'Начало'],
        ['id' => 'services', 'label' => 'Услуги'],
        ['id' => 'situations', 'label' => 'Ситуации'],
        ['id' => 'about', 'label' => 'Карина'],
        ['id' => 'scope', 'label' => 'Работа'],
        ['id' => 'process', 'label' => 'Старт'],
        ['id' => 'reviews', 'label' => 'Отзывы'],
        ['id' => 'consultation', 'label' => 'Контакты'],
    ],
];

require __DIR__ . '/includes/layout-start.php';

$reviewsFile = __DIR__ . '/data/reviews.json';
$reviewsData = json_decode((string) file_get_contents($reviewsFile), true);
$homeReviews = [];
if (is_array($reviewsData) && !empty($reviewsData['reviews']) && is_array($reviewsData['reviews'])) {
    $homeReviews = $reviewsData['reviews'];
}

$reviewPreviewLimit = 180;

/**
 * UTF-8 length without requiring mbstring.
 */
function home_str_len(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value);
    }
    if (preg_match_all('/./us', $value, $matches)) {
        return count($matches[0]);
    }
    return strlen($value);
}

/**
 * UTF-8 substring without requiring mbstring.
 */
function home_str_cut(string $value, int $limit): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $limit);
    }
    if (preg_match('/^.{0,' . $limit . '}/us', $value, $matches)) {
        return $matches[0];
    }
    return substr($value, 0, $limit);
}

/**
 * Cut to a character limit without breaking a word in half.
 */
function home_str_cut_words(string $value, int $limit): string
{
    $cut = home_str_cut($value, $limit);
    if ($cut === $value) {
        return $value;
    }
    $safe = preg_replace('/\s+\S*$/u', '', $cut);
    if (is_string($safe) && trim($safe) !== '') {
        return rtrim($safe);
    }
    return rtrim($cut);
}

/**
 * Format an ISO (Y-m-d) date as a plain-language Russian date, no intl/mbstring.
 */
function home_format_date_ru(string $value): string
{
    $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря',
    ];
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
        return $value;
    }
    $month = (int) $m[2];
    if (!isset($months[$month])) {
        return $value;
    }
    return sprintf('%d %s %d года', (int) $m[3], $months[$month], (int) $m[1]);
}

$serviceSegments = [
    [
        'num' => '01',
        'title' => 'Бухгалтер для ИП на УСН и АУСН',
        'text' => 'Регулярный учёт, отчётность, первичные документы, ЭДО, сотрудники и зарплата. Работаем с УСН без НДС и с НДС, а также с АУСН, когда режим применим.',
        'price' => 'От 15 000 ₽ в месяц',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/',
        'cta' => 'К услуге',
    ],
    [
        'num' => '02',
        'title' => 'Бухгалтер для ООО на УСН',
        'text' => 'Бухгалтерский и налоговый учёт, первичка, ЭДО, сотрудники, зарплата и отчётность. Сопровождаем ООО на УСН без НДС и с НДС; ООО на ОСНО не принимаем.',
        'price' => 'От 18 000 ₽ в месяц',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/',
        'cta' => 'К услуге',
    ],
    [
        'num' => '03',
        'title' => 'Налоговый консультант для ИП и организаций',
        'text' => 'Разбор налоговых последствий по НДС, УСН, АУСН, выбору режима и переходу с ПСН. Консультации возможны и по вопросам организаций на ОСНО после предварительной оценки задачи.',
        'price' => 'От 10 000 ₽',
        'href' => '/uslugi/nalogovoe-konsultirovanie/',
        'cta' => 'К услуге',
    ],
];

$situations = [
    [
        'title' => 'Переходите от другого бухгалтера',
        'text' => 'Нужно принять базу, понять состояние учёта, увидеть незакрытые периоды и организовать передачу документов.',
    ],
    [
        'title' => 'Появился НДС или меняется режим',
        'text' => 'Нужно заранее разобрать последствия УСН с НДС, применения АУСН или перехода с ПСН и только затем принимать решение.',
    ],
    [
        'title' => 'Учёт перестал помещаться между остальными делами',
        'text' => 'Операций, документов, счетов или торговых точек стало больше, и бизнесу нужен регулярный порядок вместо учёта от случая к случаю.',
    ],
    [
        'title' => 'Появились сотрудники и кадровые задачи',
        'text' => 'Нужно вовремя считать зарплату, оформлять кадровые документы и контролировать связанные с сотрудниками сроки.',
    ],
    [
        'title' => 'У вас несколько организаций',
        'text' => 'Нужно выстроить единый понятный порядок передачи документов и сопровождения нескольких ИП или ООО.',
    ],
    [
        'title' => 'Нужен аргументированный налоговый ответ',
        'text' => 'Перед сделкой, изменением режима или другим важным решением нужен разбор исходных данных, документов и налоговых последствий.',
    ],
];

$scopeIn = [
    'Налоговый учёт и расчёт налогов',
    'Подготовка и сдача отчётности',
    'Контроль сроков',
    'Ведение первичных документов',
    'ЭДО, счета и акты',
    'Клиент-банк',
    'Расчёт зарплаты',
    'Кадровые документы',
    'Сверки с ФНС',
    'Текущие консультации по сопровождаемой организации',
];

$scopeOut = [
    'Подготовка ответов на требования ФНС',
    'Восстановление старых периодов',
    'Исправление ошибок до начала сопровождения',
    'Большой кадровый объём',
    'Регистрационные изменения',
    'Настройка 1С, ЭДО и онлайн-кассы',
    'Подготовка отдельных юридических документов',
    'Выездная работа',
];

$scopeNot = [
    'Воинский учёт',
    'Охрана труда',
];

$processSteps = [
    [
        'title' => 'Знакомство',
        'text' => 'Короткий разговор: подходит ли ситуация специализации и какие данные нужны. Это не бесплатная налоговая консультация. Работаем дистанционно; офис — в Ростове-на-Дону.',
    ],
    [
        'title' => 'Опросник и база',
        'text' => 'Вы заполняете опросник и, если бизнес уже работает, передаёте базу и доступные документы.',
    ],
    [
        'title' => 'Оценка',
        'text' => 'Обычно за два–три дня — стоимость и состав работы. Восстановление незакрытых периодов оценивается отдельно.',
    ],
    [
        'title' => 'Договор',
        'text' => 'Согласовываем каналы, границы сторон и начинаем сопровождение.',
    ],
];
?>

<section class="hero home-hero" id="nachalo">
  <div class="container hero__grid hero__grid--lead">
    <div class="hero__content">
      <p class="eyebrow">ИП Сизонова Карина Вадимовна</p>
      <h1 class="hero__title">
        Бухгалтерское<br>
        сопровождение<br>
        <span>ИП и ООО на УСН</span>
      </h1>
      <p class="hero__lead">
        Карина Сизонова ведёт учёт и отчётность ИП на УСН и АУСН, ООО на УСН
        и отдельно разбирает налоговые вопросы. Законно, понятно и с ответственностью,
        закреплённой в договоре.
      </p>
      <ul class="hero__tags" aria-label="Ключевые режимы">
        <li>УСН</li>
        <li>УСН с НДС</li>
        <li>АУСН</li>
        <li>Переход с ПСН</li>
      </ul>
      <div class="hero__actions">
        <button class="btn btn--accent" type="button" data-contact-panel-open>Начать знакомство</button>
        <a class="btn btn--ghost" href="#services">Посмотреть услуги</a>
      </div>
    </div>
  </div>
</section>

<section class="home-services" id="services" aria-labelledby="services-title">
  <div class="container">
    <header class="section-head">
      <p class="eyebrow">Услуги</p>
      <h2 id="services-title">Чем можем помочь</h2>
      <p class="section-lead">Стартовая цена на сайте. Окончательная стоимость — после опросника и данных о бизнесе.</p>
    </header>

    <ul class="service-monolith" aria-label="Услуги">
      <?php foreach ($serviceSegments as $segment): ?>
        <li class="service-seg-item">
          <a class="service-seg" href="<?= e(url($segment['href'])) ?>">
            <span class="service-seg__num" aria-hidden="true"><?= e($segment['num']) ?></span>
            <h3 class="service-seg__title"><?= e($segment['title']) ?></h3>
            <p class="service-seg__text"><?= e($segment['text']) ?></p>
            <p class="service-seg__price"><?= e($segment['price']) ?></p>
            <span class="service-seg__go"><?= e($segment['cta']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <p class="home-services__multi">
      Несколько организаций?
      <button type="button" class="text-action" data-contact-panel-open>Обсудим единый порядок сопровождения</button>
    </p>
  </div>
</section>

<section class="home-situations" id="situations" aria-labelledby="situations-title">
  <div class="container">
    <header class="section-head section-head--narrow">
      <p class="eyebrow">Ситуации</p>
      <h2 id="situations-title">Когда к нам приходят</h2>
      <p class="section-lead">Узнайте себя в типичном запросе — без каталога всех отраслей сразу.</p>
    </header>

    <div class="situations-mount" role="list">
      <?php foreach ($situations as $i => $item): ?>
        <article class="situation situation--<?= e((string) (($i % 3) + 1)) ?>" role="listitem">
          <span class="situation__num" aria-hidden="true"><?= e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
          <h3 class="situation__title"><?= e($item['title']) ?></h3>
          <p class="situation__text"><?= e($item['text']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>

    <p class="home-more">
      <a class="text-link" href="<?= e(url('/komu-pomogaem/')) ?>">С какими видами бизнеса работаем</a>
    </p>
  </div>
</section>

<section class="home-about" id="about" aria-labelledby="about-title">
  <div class="container photo-stage">
    <div class="photo-stage__text">
      <header class="section-head">
        <p class="eyebrow">О специалисте</p>
        <h2 id="about-title">Карина и кабинет</h2>
      </header>
      <p class="section-lead">
        Карина Сизонова работает в бухгалтерии с 2013 года. Дипломированный налоговый консультант,
        состоит в Федеральной палате налоговых консультантов. Лично участвует в работе и общении,
        до начала фиксирует состав и границы сторон. Основной формат — дистанционный;
        офис находится в Ростове-на-Дону.
      </p>
      <ul class="about-facts">
        <li>Диплом по квалификации «Налоговый консультант» — 7 октября 2025 года</li>
        <li>Аттестат ФПНК № 3722864 — действует до 18 октября 2027 года</li>
        <li>Запись можно проверить в <a href="https://fp-nk.ru/person/3722864" target="_blank" rel="noopener noreferrer nofollow">официальном реестре</a></li>
      </ul>
      <a class="text-link" href="#about">Подробнее о Карине</a>
    </div>

    <figure class="photo-arch">
      <div class="photo-arch__frame">
        <picture>
          <source srcset="<?= e(asset('images/karina-hero.avif')) ?>" type="image/avif">
          <source srcset="<?= e(asset('images/karina-hero.webp')) ?>" type="image/webp">
          <img
            src="<?= e(asset('images/karina-hero.webp')) ?>"
            alt="Карина Сизонова — бухгалтер и налоговый консультант"
            width="400"
            height="600"
            class="photo-arch__img"
            loading="lazy"
            decoding="async"
          >
        </picture>
      </div>
      <figcaption class="photo-arch__cap">Карина Сизонова</figcaption>
    </figure>
  </div>
</section>

<section class="home-scope" id="scope" aria-labelledby="scope-title">
  <div class="container">
    <header class="section-head">
      <p class="eyebrow">Объём работы</p>
      <h2 id="scope-title">Что берём на себя</h2>
      <p class="section-lead">Открытый регистр: согласованная работа, то, что оценивается отдельно, и то, что мы не выполняем.</p>
    </header>

    <div class="register">
      <div class="register__col">
        <p class="register__marker">В согласованном составе</p>
        <ul class="register__list">
          <?php foreach ($scopeIn as $item): ?>
            <li><span class="register__dot" aria-hidden="true"></span><?= e($item) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="register__col register__col--soft">
        <p class="register__marker">Оценивается отдельно</p>
        <ul class="register__list">
          <?php foreach ($scopeOut as $item): ?>
            <li><span class="register__dot register__dot--warm" aria-hidden="true"></span><?= e($item) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="register__col register__col--not">
        <p class="register__marker">Не выполняем</p>
        <ul class="register__list">
          <?php foreach ($scopeNot as $item): ?>
            <li><span class="register__dot register__dot--muted" aria-hidden="true"></span><?= e($item) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <p class="register__footnote">
      ООО на ОСНО на бухгалтерское сопровождение не принимаем. Налоговая консультация по вопросу организации на ОСНО возможна после предварительной оценки задачи; такая консультация не означает принятия на сопровождение.
    </p>
  </div>
</section>

<section class="home-process" id="process" aria-labelledby="process-title">
  <div class="container">
    <header class="section-head">
      <p class="eyebrow">Старт</p>
      <h2 id="process-title">Как начинается работа</h2>
      <p class="section-lead">Четыре шага без обещания «всё под ключ» заранее.</p>
    </header>

    <ol class="thread">
      <?php foreach ($processSteps as $i => $step): ?>
        <li class="thread__step">
          <span class="thread__num" aria-hidden="true"><?= e((string) ($i + 1)) ?></span>
          <div class="thread__content">
            <h3><?= e($step['title']) ?></h3>
            <p><?= e($step['text']) ?></p>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="home-reviews" id="reviews" aria-labelledby="reviews-title">
  <div class="container">
    <header class="section-head section-head--reviews">
      <div>
        <p class="eyebrow">Нас рекомендуют</p>
        <h2 id="reviews-title">Отзывы</h2>
      </div>
      <div class="reviews-strip__controls" hidden>
        <button type="button" class="reviews-strip__btn" data-reviews-prev aria-label="Предыдущие отзывы">‹</button>
        <button type="button" class="reviews-strip__btn" data-reviews-next aria-label="Следующие отзывы">›</button>
      </div>
    </header>

    <?php if ($homeReviews !== []): ?>
    <div
      class="reviews-strip"
      data-reviews-strip
      tabindex="0"
      role="region"
      aria-roledescription="карусель"
      aria-label="Отзывы клиентов"
    >
      <div class="reviews-strip__track" data-reviews-track>
        <?php foreach ($homeReviews as $review):
            $author = (string) ($review['author'] ?? 'Клиент');
            if (($review['authorNote'] ?? '') !== '') {
                $author = (string) $review['authorNote'];
            }
            $text = (string) ($review['text'] ?? '');
            $needsExpand = home_str_len($text) > $reviewPreviewLimit;
            $preview = $needsExpand
                ? rtrim(home_str_cut_words($text, $reviewPreviewLimit)) . '…'
                : $text;
            $rating = max(0, min(5, (int) ($review['rating'] ?? 0)));
            $reviewDate = home_format_date_ru((string) ($review['date'] ?? ''));
            $reviewSource = (string) ($review['source'] ?? '');
            ?>
          <article class="review-tile" data-review-tile<?= $needsExpand ? ' data-expandable' : '' ?>>
            <div class="review-tile__head">
              <p class="review-tile__author"><?= e($author) ?></p>
              <?php if ($rating > 0): ?>
                <p class="review-tile__rating">
                  <span aria-hidden="true"><?= e(str_repeat('★', $rating) . str_repeat('☆', 5 - $rating)) ?></span>
                  <span class="sr-only">Оценка: <?= e((string) $rating) ?> из 5</span>
                </p>
              <?php endif; ?>
            </div>
            <p class="review-tile__text" data-review-preview><?= e($preview) ?></p>
            <?php if ($needsExpand): ?>
              <p class="review-tile__full" data-review-full hidden><?= e($text) ?></p>
              <button type="button" class="review-tile__more" data-review-expand aria-expanded="false">Прочитать полностью</button>
            <?php endif; ?>
            <?php if ($reviewDate !== '' || $reviewSource !== ''): ?>
              <p class="review-tile__meta">
                <?php if ($reviewDate !== ''): ?><span class="review-tile__date"><?= e($reviewDate) ?></span><?php endif; ?>
                <?php if ($reviewDate !== '' && $reviewSource !== ''): ?><span aria-hidden="true">·</span><?php endif; ?>
                <?php if ($reviewSource !== ''): ?><span class="review-tile__source"><?= e($reviewSource) ?></span><?php endif; ?>
              </p>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <p class="home-more">
      <a class="text-link" href="<?= e($siteContacts['yandex_uslugi']) ?>" target="_blank" rel="noopener noreferrer nofollow">Больше отзывов на Яндекс Услугах</a>
    </p>
  </div>
</section>

<section class="home-cta" id="consultation" aria-labelledby="consultation-title">
  <div class="cta-field">
    <div class="container cta-field__inner">
      <p class="eyebrow eyebrow--on-dark">Контакты</p>
      <h2 id="consultation-title">Давайте начнём со знакомства</h2>
      <p class="cta-field__text">
        Напишите — разберём, подходит ли запрос специализации и какие данные нужны для оценки.
        Содержательный налоговый ответ начинается после согласования работы.
      </p>
      <button class="btn btn--on-dark" type="button" data-contact-panel-open>Начать знакомство</button>
      <p class="cta-field__secondary">
        <a href="tel:<?= e($siteContacts['phone_primary_tel']) ?>"><?= e($siteContacts['phone_primary']) ?></a>
        <span aria-hidden="true">·</span>
        <a href="<?= e(url('/kontakty/')) ?>">Контакты Кабинета Бухучёта</a>
      </p>
    </div>
    <svg class="cta-field__arc" viewBox="0 0 400 60" aria-hidden="true" focusable="false">
      <path d="M8 48 C 80 8, 160 52, 240 24 S 360 8, 392 36" fill="none" stroke="currentColor" stroke-width="1"/>
    </svg>
  </div>
</section>

<?php
require __DIR__ . '/includes/layout-end.php';
