# Palette lab v1

Изолированная лаборатория цветовой системы для «Кабинет Бухучёта».

**Не трогает `/site`.** Победителя не выбирает — только сравнение на одном макете.

## Открыть

Откройте `index.html` в браузере (пути к логотипу и фото Карины — относительные к `site/assets/`).

Либо из корня репозитория:

```bash
# из prototypes/palette-v1
php -S 127.0.0.1:8090
```

Затем: http://127.0.0.1:8090/

## Переключатели

| Control | Значения |
|---------|----------|
| Palette | `warm-satin` · `mineral-finance` · `editorial-contrast` |
| Motion | ON / OFF (ambient + scroll; `prefers-reduced-motion` гасит) |
| Mega | OPEN / CLOSED (push-down лист, не glass popup) |
| CTA | `ink/action` vs `warm/медь` (наследие мандарина) |

Состояние пишется в `localStorage` (`palette-v1`).

## Что на макете

Модель как в production-навигации:

- sticky header, домик в ряду, inline-search, push-down mega;
- stickers с `karina-hero.webp`;
- hero, услуги, trust, интерактив, **review-card** (как на сайте);
- мини-блок «наследие orbs»;
- dark pause, форма, swatches + contrast table.

Spine в lab нет.

## Три направления

1. **Warm Satin** — тёплый кабинет / бумага / сатин; ближе к будущему ship «бумага/медь», если победит.
2. **Mineral Finance** — прохладнее; ближе к текущему cool `#f4f7f9` site.
3. **Editorial Contrast** — сильнее ink, тёмные паузы, бронза редко.

Semantic tokens: `--color-page`, `--color-primary`, `--color-warm`, `--ambient-*` и т.д.  
Переключение только через `body[data-palette]`.

## Screenshots

Папка `screenshots/` — best effort. Имена по желанию:

- `warm-satin-home.png`
- `mineral-mega-open.png`
- `editorial-dark.png`
- `cta-warm-vs-action.png`

Скриншоты не являются hard-fail приёмки.

## Следующий шаг

**Победитель: Mineral Finance** — внедрён в `/site` (aliases + `docs/visual-principles.md`). Lab остаётся архивом сравнения.
