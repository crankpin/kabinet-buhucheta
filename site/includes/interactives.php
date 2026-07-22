<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Чек-лист «Проверить бухгалтера».
 * Живёт на целевой странице: удерживает посетителя и ведёт к услугам.
 *
 * @param 'full'|'inline' $mode
 */
function render_checklist(string $mode = 'full'): void
{
    global $siteContacts;
    $file = dirname(__DIR__) . '/data/checklist.json';
    $data = json_decode((string) file_get_contents($file), true);
    if (!is_array($data) || empty($data['items'])) {
        return;
    }
    /** @var array<int, array{text:string,hint?:string}> $items */
    $items = $data['items'];
    $uid = 'checklist-' . bin2hex(random_bytes(3));
    ?>
    <div class="ix-block" data-checklist data-mode="full" id="<?= e($uid) ?>">
      <div class="ix-check">
        <?php foreach ($items as $i => $item): ?>
          <label class="ix-check__item">
            <input type="checkbox" value="<?= (int) ($i + 1) ?>">
            <span>
              <strong><?= e($item['text']) ?></strong>
              <?php if (!empty($item['hint'])): ?>
                <br><small><?= e((string) $item['hint']) ?></small>
              <?php endif; ?>
            </span>
          </label>
        <?php endforeach; ?>
      </div>
      <div class="ix-check__result" data-checklist-result hidden></div>
      <div class="ix-teaser__actions">
        <a class="btn btn--accent" href="<?= e(url('/uslugi/')) ?>">Смотреть услуги</a>
        <a class="btn btn--ghost" href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Написать Карине</a>
      </div>
    </div>
    <?php
}

/**
 * Налоговый квиз на целевой странице.
 *
 * @param 'full'|'inline' $mode
 */
function render_quiz(string $mode = 'full'): void
{
    global $siteContacts;
    $file = dirname(__DIR__) . '/data/quiz.json';
    $data = json_decode((string) file_get_contents($file), true);
    if (!is_array($data) || empty($data['questions'])) {
        return;
    }
    $uid = 'quiz-' . bin2hex(random_bytes(3));
    $json = json_encode($data['questions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    ?>
    <div class="ix-block" data-quiz data-mode="full" id="<?= e($uid) ?>">
      <script type="application/json" class="quiz-data"><?= $json ?></script>
      <div class="ix-quiz__progress" data-quiz-progress style="--progress:0%"></div>
      <span class="ix-quiz__progress-text" data-quiz-progress-text></span>
      <div data-quiz-content></div>
      <div class="ix-quiz__result" data-quiz-result hidden>
        <h3 data-quiz-result-title></h3>
        <p data-quiz-result-text></p>
        <div class="ix-teaser__actions">
          <a class="btn btn--accent" href="<?= e(url('/uslugi/')) ?>">Смотреть услуги</a>
          <a class="btn btn--ghost" href="<?= e($siteContacts['telegram']) ?>" target="_blank" rel="noopener noreferrer nofollow">Написать Карине</a>
        </div>
      </div>
    </div>
    <?php
}
