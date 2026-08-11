<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

$page = [
    'title' => 'Кабинет Бухучёта — бухгалтерское сопровождение ИП и ООО на УСН',
    'description' => 'Бухгалтерское сопровождение ИП и ООО на УСН. Налоговое консультирование. УСН, НДС, АУСН, переход с ПСН.',
    'canonical' => SITE_DOMAIN . '/',
    'body_class' => 'page-home',
    'scroll_stack' => true,
    'service_files' => true,
    'spine' => [
        ['id' => 'nachalo', 'label' => 'Сначала главное!'],
        ['id' => 'services', 'label' => 'Чем занимаемся?'],
        ['id' => 'situations', 'label' => 'Когда это нужно?'],
        ['id' => 'about', 'label' => 'Кто отвечает?'],
        ['id' => 'scope', 'label' => 'Что входит в работу?'],
        ['id' => 'process', 'label' => 'Как всё начинается?'],
        ['id' => 'reviews', 'label' => 'Что говорят клиенты?'],
        ['id' => 'consultation', 'label' => 'Куда писать?'],
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
        'modifier' => 'ip',
        'title' => 'Бухгалтер для ИП на УСН и АУСН',
        'rail_title' => 'ИП на УСН и АУСН',
        'blurb' => 'ИП на УСН без НДС и с НДС, а также на АУСН — когда режим применим.',
        'lead' => 'Карина берёт на себя согласованный объём регулярной бухгалтерской работы, а владелец понимает, что происходит и где требуется его решение.',
        'subhead' => 'Внутри сопровождения',
        'list' => [
            'Налоговый учёт, расчёт налогов и контроль сроков',
            'Подготовка и сдача отчётности',
            'Первичные документы, ЭДО, счета и акты',
            'Сверки с ФНС и согласованная работа с клиент-банком',
            'Сотрудники, зарплата и кадровые документы',
        ],
        'note_label' => 'Важно',
        'note' => [
            'Ответы на требования ФНС, восстановление старых периодов и исправление прежних ошибок оцениваются отдельно.',
        ],
        'price' => 'От 15 000 ₽ в месяц',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ip/',
        'more_label' => 'Без сокращений: всё о сопровождении ИП →',
    ],
    [
        'num' => '02',
        'modifier' => 'ooo',
        'title' => 'Бухгалтер для ООО на УСН',
        'rail_title' => 'ООО на УСН',
        'blurb' => 'ООО на УСН без НДС и с НДС — от состояния базы до отчётности.',
        'lead' => 'Карина ведёт бухгалтерский и налоговый учёт, поддерживает состояние базы и заранее показывает руководителю, каких данных или решений не хватает.',
        'subhead' => 'Внутри сопровождения',
        'list' => [
            'Бухгалтерский и налоговый учёт',
            'Налоги, бухгалтерская и налоговая отчётность',
            'Первичные документы, ЭДО, счета и акты',
            'Клиент-банк, сверки с ФНС и контроль базы',
            'Сотрудники, зарплата и кадровые документы',
        ],
        'note_label' => 'Как устроена работа',
        'note' => [
            'Сопровождаем ООО на УСН. Восстановление учёта, исправление прежних ошибок и ответы на требования ФНС оцениваем отдельно — после знакомства с вашей ситуацией.',
            'С ООО на ОСНО работаем в формате отдельных консультаций, но не берём на постоянное бухгалтерское сопровождение.',
        ],
        'price' => 'От 18 000 ₽ в месяц',
        'href' => '/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/',
        'more_label' => 'Без сокращений: всё о сопровождении ООО →',
    ],
    [
        'num' => '03',
        'modifier' => 'tax',
        'title' => 'Налоговый консультант для ИП и организаций',
        'rail_title' => 'Налоговая консультация',
        'blurb' => 'Разбор налоговой ситуации до решения — для ИП и организаций, включая вопросы по ОСНО.',
        'lead' => 'Карина изучает факты, документы и действующие правила, чтобы дать понятный вывод до того, как клиент примет налоговое решение.',
        'subhead' => 'Что можно получить',
        'list' => [
            'Предварительное изучение документов',
            'Разбор применимых налоговых правил',
            'Расчёт или сравнение нескольких вариантов',
            'Устное объяснение или письменный вывод',
            'Понятную последовательность дальнейших действий',
        ],
        'note_label' => 'Важно',
        'note' => [
            'Консультация объясняет последствия и варианты. Подготовка ответа ФНС, исправление декларации или базы — отдельная работа.',
        ],
        'price' => 'От 10 000 ₽',
        'href' => '/uslugi/nalogovoe-konsultirovanie/',
        'more_label' => 'Без сокращений: всё о налоговой консультации →',
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
        <button class="btn btn--accent" type="button" data-contact-panel-open>Давайте знакомиться</button>
        <a class="btn btn--ghost" href="#services">Посмотреть услуги</a>
      </div>
    </div>
  </div>
</section>

<section class="home-services" id="services" aria-labelledby="services-title">
  <div class="container">
    <header class="section-head">
      <p class="eyebrow">Услуги</p>
      <h2 id="services-title">Чем мы здесь вообще занимаемся?</h2>
      <p class="section-lead">Ведём бухгалтерию ИП и ООО на УСН и отдельно разбираем налоговые вопросы. Цена на сайте — стартовая; точная — после опросника и данных о бизнесе.</p>
    </header>

    <ul class="service-files" data-service-files data-open="" data-phase="" aria-label="Услуги">
      <?php foreach ($serviceSegments as $segment): ?>
        <?php
          $panelId = 'service-files-panel-' . $segment['num'];
          $modifier = (string) ($segment['modifier'] ?? '');
        ?>
        <li class="service-files__item<?= $modifier !== '' ? ' service-files__item--' . e($modifier) : '' ?>" data-service-id="<?= e($segment['num']) ?>">
          <div class="service-files__plaque">
            <div class="service-files__rail" data-service-tab hidden>
              <span class="service-files__rail-num"><?= e($segment['num']) ?></span>
              <span class="service-files__rail-title"><?= e($segment['rail_title']) ?></span>
              <span class="service-files__rail-hint">Распаковать</span>
            </div>

            <div class="service-files__face">
              <button
                type="button"
                class="service-files__close"
                data-service-close
                aria-label="Короче — свернуть подробности"
                hidden
              >
                <span class="service-files__close-label">Короче!</span>
                <span class="service-files__close-icon" aria-hidden="true"></span>
              </button>

              <div class="service-files__closed">
                <p class="service-files__num"><?= e($segment['num']) ?></p>
                <h3 class="service-files__title"><?= e($segment['title']) ?></h3>
                <p class="service-files__blurb"><?= e($segment['blurb']) ?></p>
                <p class="service-files__price"><?= e($segment['price']) ?></p>
                <button
                  type="button"
                  class="service-files__toggle"
                  data-service-toggle
                  aria-expanded="false"
                  aria-controls="<?= e($panelId) ?>"
                  aria-label="Распаковать услугу — открыть подробности"
                >
                  <span class="service-files__toggle-label">Распаковать услугу</span>
                  <span class="service-files__toggle-seal" aria-hidden="true"></span>
                </button>
              </div>

              <div
                class="service-files__panel"
                id="<?= e($panelId) ?>"
                data-service-panel
                aria-hidden="true"
                inert
              >
                <div class="service-files__panel-inner">
                  <div class="service-files__main">
                    <p class="service-files__lead"><?= e($segment['lead']) ?></p>
                    <h4 class="service-files__subhead"><?= e($segment['subhead']) ?></h4>
                    <ul class="service-files__list">
                      <?php foreach ($segment['list'] as $listItem): ?>
                        <li><?= e($listItem) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                  <aside class="service-files__aside">
                    <div class="service-files__note">
                      <p class="service-files__note-label"><?= e($segment['note_label']) ?></p>
                      <?php foreach ($segment['note'] as $notePara): ?>
                        <p><?= e($notePara) ?></p>
                      <?php endforeach; ?>
                    </div>
                    <p class="service-files__price service-files__price--open"><?= e($segment['price']) ?></p>
                    <div class="service-files__actions">
                      <button type="button" class="btn btn--accent service-files__cta" data-contact-panel-open>Обсудить мою ситуацию</button>
                      <a class="service-files__more" href="<?= e(url($segment['href'])) ?>"><?= e($segment['more_label']) ?></a>
                    </div>
                  </aside>
                </div>
              </div>
            </div>
          </div>
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
      <h2 id="situations-title">А оно вам надо?</h2>
      <p class="section-lead">По какому поводу к нам приходят люди — возможно, в одном из этих сюжетов вы узнаете свой.</p>
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
    <div class="photo-stage__lead">
      <div class="photo-stage__text">
        <header class="section-head">
          <p class="eyebrow">О специалисте</p>
          <h2 id="about-title">Кто здесь за всё отвечает?</h2>
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
      </div>

      <div class="photo-portrait__bay">
        <p class="about-brand-mark"><?= e(SITE_NAME) ?></p>
        <img
          class="about-welcome-chair"
          src="<?= e(asset('images/welcome-chair-owl.webp')) ?>"
          alt="Кресло с подушкой-совой — место для гостя"
          width="720"
          height="883"
          decoding="async"
          loading="lazy"
        >
      </div>
    </div>

    <figure class="photo-portrait">
      <picture>
        <source srcset="<?= e(asset('images/karina-hero.webp')) ?>" type="image/webp">
        <img
          src="<?= e(asset('images/karina-hero.webp')) ?>"
          alt="Карина Сизонова — бухгалтер и налоговый консультант"
          width="429"
          height="1306"
          class="photo-portrait__img"
          loading="lazy"
          decoding="async"
        >
      </picture>
    </figure>
  </div>
</section>

<section class="home-scope" id="scope" aria-labelledby="scope-title">
  <div class="container">
    <header class="section-head">
      <p class="eyebrow">Объём работы</p>
      <h2 id="scope-title">Ладно, а делать-то что будем?</h2>
      <p class="section-lead">Без бухгалтерского тумана: что входит в согласованный состав, что оценивается отдельно и чем мы не занимаемся.</p>
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
      <h2 id="process-title">Допустим, мы друг другу подходим. Что дальше?</h2>
      <p class="section-lead">Знакомство, опросник, оценка и договор. Четыре понятных шага — без обещания «всё под ключ» до знакомства с вашим бизнесом.</p>
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
        <h2 id="reviews-title">Похвалить себя можно. А зачем?</h2>
        <p class="section-lead">Пусть лучше за нас говорят те, кто уже работает с Кариной.</p>
      </div>
    </header>

    <?php if ($homeReviews !== []): ?>
    <div class="reviews-strip-shell">
      <div class="reviews-strip__controls" hidden>
        <button type="button" class="reviews-strip__btn" data-reviews-prev aria-label="Предыдущие отзывы">
          <span class="reviews-strip__arrow" aria-hidden="true"></span>
        </button>
        <button type="button" class="reviews-strip__btn reviews-strip__btn--next" data-reviews-next aria-label="Следующие отзывы">
          <span class="reviews-strip__arrow" aria-hidden="true"></span>
        </button>
      </div>
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
      <h2 id="consultation-title">Ну что, познакомимся?</h2>
      <p class="cta-field__text">
        Можно просто написать «Здравствуйте». Дальше мы сами зададим нужные вопросы — без неловкой паузы.
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
