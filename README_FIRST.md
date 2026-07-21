# Что делать дальше

## 1. Создай на компьютере папку

`kabinet-v2`

## 2. Сделай внутри такую структуру

```text
kabinet-v2/
├── docs/
├── legacy/
│   └── public_html/
└── site/
```

## 3. Положи файлы

- содержимое папки `docs` из этого архива → в `kabinet-v2/docs`;
- распакуй старый сайт → в `kabinet-v2/legacy/public_html`;
- папку `site` оставь пустой.

В корень `kabinet-v2` положи:

- `CURSOR_START_PROMPT.md`;
- `README_FIRST.md`.

## 4. Сделай резервную копию без Git

Скопируй всю папку `kabinet-v2` и назови копию:

`kabinet-v2-backup-before-cursor`

## 5. Открой в Cursor

Открывай всю папку `kabinet-v2`, а не отдельную папку `site`.

## 6. Первый запрос

Открой `CURSOR_START_PROMPT.md`, скопируй промпт в Cursor Agent и отправь.

На первом этапе Cursor должен создать только:

`docs/implementation-plan.md`

Он не должен начинать переписывать сайт до утверждения плана.
