<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$page = page_defaults($page ?? []);
$bodyClass = trim('page ' . (string) ($page['body_class'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#1e3a4c">
<?php if (!empty($page['noindex'])): ?>
  <meta name="robots" content="noindex, nofollow">
<?php else: ?>
  <meta name="robots" content="index, follow">
<?php endif; ?>

  <title><?= e((string) $page['title']) ?></title>
  <meta name="description" content="<?= e((string) $page['description']) ?>">
  <link rel="canonical" href="<?= e((string) $page['canonical']) ?>">

  <meta property="og:title" content="<?= e((string) $page['title']) ?>">
  <meta property="og:description" content="<?= e((string) $page['description']) ?>">
  <meta property="og:url" content="<?= e((string) $page['canonical']) ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="ru_RU">
  <meta property="og:image" content="<?= e((string) $page['og_image']) ?>">

  <link rel="icon" type="image/x-icon" href="<?= e(asset('images/favicon.ico')) ?>">
  <link rel="icon" type="image/svg+xml" href="<?= e(asset('images/favicon.svg')) ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= e(asset('images/favicon-32x32.png')) ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= e(asset('images/favicon-16x16.png')) ?>">

  <link rel="preload" href="<?= e(asset('fonts/Unbounded-Regular.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?= e(asset('fonts/Unbounded-Bold.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?= e(asset('css/main.css')) ?>" as="style">
  <link rel="stylesheet" href="<?= e(asset('css/main.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>">
  <a href="#main-content" class="skip-link">Перейти к основному содержимому</a>
