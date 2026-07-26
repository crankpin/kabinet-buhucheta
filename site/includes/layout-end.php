<?php
declare(strict_types=1);

/** @var array<int, array{id: string, label: string}> $spineItems */
$spineItems = $spineItems ?? [];
$hasSpine = $spineItems !== [];
?>
</main>
<?php
if ($hasSpine) {
    require __DIR__ . '/spine.php';
    echo '</div>';
}
require_once __DIR__ . '/footer.php';
