<?php
declare(strict_types=1);

/** Страница «О Карине» снята с навигации — редирект на главную. */
header('Location: /', true, 301);
exit;
