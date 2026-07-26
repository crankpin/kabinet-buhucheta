<?php
declare(strict_types=1);

require_once __DIR__ . '/head.php';
require_once __DIR__ . '/header.php';

/** @var array<string, mixed> $page */
$spineItems = [];
if (!empty($page['spine']) && is_array($page['spine'])) {
    $spineItems = $page['spine'];
}
$hasSpine = $spineItems !== [];
?>
<?php if ($hasSpine): ?>
<div class="page-shell page-shell--spine">
<?php endif; ?>
<main id="main-content" class="page-content">
