<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/interactives.php';

$page = [
    'title' => 'Кабинет Бухучёта — бухгалтерское сопровождение ИП и ООО на УСН',
    'description' => 'Бухгалтерское сопровождение ИП и ООО на УСН. Налоговое консультирование. УСН, НДС, АУСН, переход с ПСН.',
    'canonical' => SITE_DOMAIN . '/',
    'body_class' => 'page-home',
    'spine' => [
        ['id' => 'nachalo', 'label' => 'К началу'],
        ['id' => 'who-are-you', 'label' => 'Кто вы'],
        ['id' => 'services', 'label' => 'Услуги'],
        ['id' => 'tools', 'label' => 'Проверки'],
        ['id' => 'reviews', 'label' => 'Отзывы'],
        ['id' => 'kontakty', 'label' => 'Контакты'],
    ],
];

require __DIR__ . '/includes/layout-start.php';

$reviewsFile = __DIR__ . '/data/reviews.json';
$reviewsData = json_decode((string) file_get_contents($reviewsFile), true);
$homeReviews = [];
if (is_array($reviewsData) && !empty($reviewsData['reviews'])) {
    $homeReviews = array_slice($reviewsData['reviews'], 0, 3);
}
?>

<section class="hero" id="nachalo">
  <div class="container hero__grid">
    <div class="hero__content">
      <p class="eyebrow">ИП Сизонова Карина Вадимовна</p>
      <h1 class="hero__title">
        Бухгалтерское<br>
        сопровождение<br>
        <span>ИП и ООО на УСН</span>
      </h1>
      <p class="hero__lead">
        Дипломированный налоговый консультант. Веду учёт, отчётность и налоги —
        без серых схем, понятным языком.
      </p>
      <ul class="hero__tags" aria-label="Ключевые режимы">
        <li>УСН</li>
        <li>УСН с НДС</li>
        <li>АУСН</li>
        <li>Переход с ПСН</li>
      </ul>
      <div class="hero__actions">
        <a class="btn btn--accent" href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Получить консультацию</a>
        <a class="btn btn--ghost" href="#who-are-you">Кто вы?</a>
      </div>
    </div>

    <div class="hero__portrait">
      <figure class="hero__figure">
        <picture>
          <source srcset="<?= e(asset('images/karina-hero.avif')) ?>" type="image/avif">
          <source srcset="<?= e(asset('images/karina-hero.webp')) ?>" type="image/webp">
          <img
            src="<?= e(asset('images/karina-hero.webp')) ?>"
            alt="Карина Сизонова — бухгалтер и налоговый консультант"
            width="400"
            height="600"
            class="hero__photo"
            fetchpriority="high"
          >
        </picture>
        <figcaption class="hero__caption">Карина Сизонова</figcaption>
      </figure>
    </div>
  </div>
</section>

<section class="who" id="who-are-you" aria-labelledby="who-title">
  <div class="container">
    <div class="who__header">
      <p class="eyebrow">Развилка</p>
      <h2 id="who-title" class="who__title">КТО ВЫ?</h2>
      <p class="who__lead">Выберите формат — откроется нужная услуга или короткий сценарий.</p>
    </div>

    <div class="who__grid" role="group" aria-label="Выбор типа бизнеса">
      <a class="who-card" href="<?= e(url('/uslugi/buhgalterskoe-soprovozhdenie-ip/')) ?>">
        <span class="who-card__num" aria-hidden="true">01</span>
        <span class="who-card__label">Я — ИП</span>
        <span class="who-card__hint">К бухгалтерскому сопровождению ИП</span>
      </a>

      <a class="who-card" href="<?= e(url('/uslugi/buhgalterskoe-soprovozhdenie-ooo-usn/')) ?>">
        <span class="who-card__num" aria-hidden="true">02</span>
        <span class="who-card__label">У меня ООО</span>
        <span class="who-card__hint">К сопровождению ООО на УСН</span>
      </a>

      <button class="who-card who-card--button" type="button" id="multi-org-open" aria-expanded="false" aria-controls="multi-org-panel">
        <span class="who-card__num" aria-hidden="true">03</span>
        <span class="who-card__label">У меня несколько разных организаций</span>
        <span class="who-card__hint">Короткий сценарий</span>
      </button>
    </div>
  </div>
</section>

<dialog class="multi-dialog" id="multi-org-panel" aria-labelledby="multi-dialog-title">
  <form method="dialog" class="multi-dialog__form" id="multi-org-form">
    <div class="multi-dialog__head">
      <h2 id="multi-dialog-title">Несколько организаций</h2>
      <button type="button" class="multi-dialog__close" id="multi-org-close" aria-label="Закрыть">&times;</button>
    </div>

    <div class="multi-step is-active" data-step="1">
      <p class="multi-step__q">Какая у вас структура?</p>
      <div class="multi-options">
        <label><input type="radio" name="structure" value="ip-ooo" required> ИП и ООО</label>
        <label><input type="radio" name="structure" value="several-ooo"> Несколько ООО</label>
        <label><input type="radio" name="structure" value="several-ip"> Несколько ИП / направлений</label>
        <label><input type="radio" name="structure" value="other"> Другая структура</label>
      </div>
      <button type="button" class="btn btn--accent multi-next" data-next="2">Дальше</button>
    </div>

    <div class="multi-step" data-step="2" hidden>
      <p class="multi-step__q">Какие налоговые режимы используются?</p>
      <div class="multi-options multi-options--checks">
        <label><input type="checkbox" name="regimes" value="usn"> УСН</label>
        <label><input type="checkbox" name="regimes" value="usn-nds"> УСН с НДС</label>
        <label><input type="checkbox" name="regimes" value="ausn"> АУСН</label>
        <label><input type="checkbox" name="regimes" value="osno"> ОСНО</label>
        <label><input type="checkbox" name="regimes" value="other"> Другое / не уверен</label>
      </div>
      <div class="multi-nav">
        <button type="button" class="btn btn--ghost multi-back" data-back="1">Назад</button>
        <button type="button" class="btn btn--accent multi-next" data-next="3">Дальше</button>
      </div>
    </div>

    <div class="multi-step" data-step="3" hidden>
      <p class="multi-step__q">Сколько организаций требуется сопровождать?</p>
      <div class="multi-options">
        <label><input type="radio" name="count" value="2" required> 2</label>
        <label><input type="radio" name="count" value="3"> 3</label>
        <label><input type="radio" name="count" value="4plus"> 4 и больше</label>
      </div>
      <div class="multi-nav">
        <button type="button" class="btn btn--ghost multi-back" data-back="2">Назад</button>
        <button type="button" class="btn btn--accent multi-next" data-next="4">Дальше</button>
      </div>
    </div>

    <div class="multi-step" data-step="4" hidden>
      <p class="multi-step__result">
        Для нескольких организаций типовой тариф не подходит.
        Сначала разберём структуру бизнеса и предложим единый порядок сопровождения.
      </p>
      <div class="multi-nav">
        <button type="button" class="btn btn--ghost multi-back" data-back="3">Назад</button>
        <a class="btn btn--accent" href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Обсудить единый порядок сопровождения</a>
      </div>
    </div>
  </form>
</dialog>

<section class="services-teaser" id="services" aria-labelledby="services-teaser-title">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Услуги</p>
      <h2 id="services-teaser-title">Три направления работы</h2>
    </div>
    <div class="services-teaser__grid">
      <?php foreach ($siteServices as $i => $service): ?>
        <a class="service-teaser" href="<?= e(url($service['href'])) ?>">
          <span class="service-teaser__num"><?= e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
          <h3 class="service-teaser__title"><?= e($service['title']) ?></h3>
          <p class="service-teaser__text"><?= e($service['teaser']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="ix-home" id="tools" aria-label="Полезные проверки">
  <div class="container">
    <details class="ix-disclosure">
      <summary class="ix-disclosure__summary">
        <span class="ix-disclosure__eyebrow">2 минуты</span>
        <span class="ix-disclosure__title">Проверьте своего бухгалтера</span>
        <span class="ix-disclosure__hint">6 пунктов — понять, всё ли в порядке с учётом</span>
      </summary>
      <div class="ix-disclosure__body">
        <?php render_checklist(); ?>
      </div>
    </details>

    <details class="ix-disclosure">
      <summary class="ix-disclosure__summary">
        <span class="ix-disclosure__eyebrow">5 вопросов</span>
        <span class="ix-disclosure__title">Налоговый квиз</span>
        <span class="ix-disclosure__hint">Есть ли смысл разбирать налоговую нагрузку</span>
      </summary>
      <div class="ix-disclosure__body">
        <?php render_quiz(); ?>
      </div>
    </details>
  </div>
</section>

<section class="reviews" id="reviews" aria-labelledby="reviews-title">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Нас рекомендуют</p>
      <h2 id="reviews-title">Отзывы</h2>
    </div>
    <?php if ($homeReviews !== []): ?>
    <div class="reviews-grid">
      <?php foreach ($homeReviews as $review): ?>
        <article class="review-card">
          <p class="review-card__author"><?= e((string) ($review['author'] ?? 'Клиент')) ?></p>
          <p class="review-card__text"><?= e((string) ($review['text'] ?? '')) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <p style="margin-top:1rem">
      <a href="<?= e($siteContacts['yandex_uslugi']) ?>" target="_blank" rel="noopener noreferrer nofollow">Больше отзывов на Яндекс Услугах</a>
    </p>
  </div>
</section>

<?php
$ctaTitle = 'Разберём вашу ситуацию';
$ctaText = 'Напишите в Telegram или оставьте запрос на странице контактов.';
require __DIR__ . '/includes/cta.php';
require __DIR__ . '/includes/layout-end.php';
