<?php
declare(strict_types=1);

/**
 * Каркас внутренней страницы (этап 1).
 * Полный контент — на следующих этапах. Без выдуманных фактов.
 *
 * @param array{
 *   title: string,
 *   description: string,
 *   canonical_path: string,
 *   h1: string,
 *   lead: string,
 *   breadcrumbs?: array<int, array{label:string,href?:string}>,
 *   body_class?: string,
 *   notes?: array<int, string>
 * } $pageData
 */
function render_stub_page(array $pageData): void
{
    require_once dirname(__DIR__) . '/includes/config.php';

    $page = [
        'title' => $pageData['title'],
        'description' => $pageData['description'],
        'canonical' => SITE_DOMAIN . $pageData['canonical_path'],
        'body_class' => $pageData['body_class'] ?? 'page-stub',
        'noindex' => true,
    ];

    $breadcrumbs = $pageData['breadcrumbs'] ?? [];

    require dirname(__DIR__) . '/includes/layout-start.php';
    ?>
    <section class="stub">
      <div class="container stub__inner">
        <?php require dirname(__DIR__) . '/includes/breadcrumbs.php'; ?>
        <p class="eyebrow">Раздел в разработке</p>
        <h1 class="stub__title"><?= e($pageData['h1']) ?></h1>
        <p class="stub__lead"><?= e($pageData['lead']) ?></p>
        <?php if (!empty($pageData['notes'])): ?>
          <ul class="stub__notes">
            <?php foreach ($pageData['notes'] as $note): ?>
              <li><?= e($note) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <div class="stub__actions">
          <a class="btn btn--accent" href="<?= e(url('/')) ?>">На главную</a>
          <a class="btn btn--ghost" href="<?= e(url('/kontakty/')) ?>">Контакты</a>
        </div>
      </div>
    </section>
    <?php
    require dirname(__DIR__) . '/includes/layout-end.php';
}
