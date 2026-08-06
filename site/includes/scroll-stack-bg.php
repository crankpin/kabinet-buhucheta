<?php
declare(strict_types=1);

/**
 * Decorative 3D document trains behind homepage content (desktop only).
 * train-in: incoming loose docs; train-out: processed ordered docs.
 * aria-hidden; no interactives.
 */

/** @var list<array{kind: string, title: string, fields: list<string>}> $stackFormsIn */
$stackFormsIn = [
    [
        'kind' => 'sheet',
        'title' => 'Налоговая декларация',
        'fields' => ['Код налогового органа', 'ИНН', 'КПП', 'Период'],
    ],
    [
        'kind' => 'invoice',
        'title' => 'Счёт-фактура',
        'fields' => ['Продавец', 'Покупатель', 'НДС', 'Всего'],
    ],
    [
        'kind' => 'act',
        'title' => 'Справка-расчёт',
        'fields' => ['Наименование', 'База', 'Ставка', 'Итого'],
    ],
    [
        'kind' => 'ledger',
        'title' => 'Расчёт по страховым взносам',
        'fields' => ['Сумма взносов', 'Тариф', 'ОКТМО', 'Подпись'],
    ],
    [
        'kind' => 'sheet',
        'title' => 'Декларация по УСН',
        'fields' => ['Объект', 'Доходы', 'Расходы', 'Налог'],
    ],
    [
        'kind' => 'invoice',
        'title' => 'Книга учёта доходов',
        'fields' => ['Дата', 'Содержание', 'Доход', 'Итого'],
    ],
    [
        'kind' => 'ledger',
        'title' => 'Платёжное поручение',
        'fields' => ['Плательщик', 'Получатель', 'КБК', 'Сумма'],
    ],
    [
        'kind' => 'act',
        'title' => 'Акт выполненных работ',
        'fields' => ['Заказчик', 'Период', 'Сумма', 'НДС'],
    ],
];

/** @var list<array{kind: string, title: string, fields: list<string>}> $stackFormsOut */
$stackFormsOut = [
    [
        'kind' => 'register',
        'title' => 'Реестр сданных отчётов',
        'fields' => ['№ п/п', 'Форма', 'Период', 'Статус'],
    ],
    [
        'kind' => 'folder',
        'title' => 'Папка клиента',
        'fields' => ['Клиент', 'Год', 'Раздел', 'Листов'],
    ],
    [
        'kind' => 'signed',
        'title' => 'Подписанная отчётность',
        'fields' => ['Номер', 'Дата', 'Подпись', 'Статус'],
    ],
    [
        'kind' => 'stamp',
        'title' => 'Акт сверки расчётов',
        'fields' => ['Сальдо', 'Начисления', 'Уплаты', 'Печать'],
    ],
    [
        'kind' => 'register',
        'title' => 'Уведомление об исчисленных суммах',
        'fields' => ['КБК', 'Сумма налога', 'Срок уплаты', 'ОКТМО'],
    ],
    [
        'kind' => 'folder',
        'title' => 'Архив квартала',
        'fields' => ['Квартал', 'Вид', 'Дата', 'Листов'],
    ],
    [
        'kind' => 'signed',
        'title' => 'Расчёт 6-НДФЛ',
        'fields' => ['Ставка', 'Исчислено', 'Удержано', 'Перечислено'],
    ],
    [
        'kind' => 'stamp',
        'title' => 'Квитанция о приёме',
        'fields' => ['ИдДок', 'Дата', 'КОД', 'Печать'],
    ],
];

/**
 * @param list<array{kind: string, title: string, fields: list<string>}> $forms
 * @param 'in'|'out' $trainId
 */
function scroll_stack_render_train(array $forms, string $trainId): void
{
    $badge = $trainId === 'out' ? 'ГОТОВО' : 'ВХОД';
    ?>
    <div class="scroll-stack-bg__train" data-train="<?= e($trainId) ?>">
<?php foreach ($forms as $i => $form): ?>
      <div
        class="scroll-stack-bg__card scroll-stack-bg__card--<?= e($form['kind']) ?><?= $trainId === 'in' && $i % 3 === 1 ? ' scroll-stack-bg__card--loose' : '' ?><?= $trainId === 'out' && $i % 4 === 0 ? ' scroll-stack-bg__card--tabbed' : '' ?>"
        data-stack-card
        data-stack-index="<?= (int) $i ?>"
      >
        <span class="scroll-stack-bg__badge"><?= e($badge) ?></span>
        <span class="scroll-stack-bg__title"><?= e($form['title']) ?></span>
<?php foreach ($form['fields'] as $field): ?>
        <span class="scroll-stack-bg__field">
          <span class="scroll-stack-bg__field-label"><?= e($field) ?></span>
          <span class="scroll-stack-bg__field-line"></span>
        </span>
<?php endforeach; ?>
        <span class="scroll-stack-bg__mark"></span>
      </div>
<?php endforeach; ?>
    </div>
    <?php
}
?>
<div class="scroll-stack-bg" id="scroll-stack-bg" aria-hidden="true" hidden>
  <div class="scroll-stack-bg__scene">
<?php
scroll_stack_render_train($stackFormsIn, 'in');
scroll_stack_render_train($stackFormsOut, 'out');
?>
  </div>
</div>
