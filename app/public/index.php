<?php

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

if (($_SERVER['APP_ENV'] ?? 'dev') === 'dev') {
    //usleep(random_int(0, 1000000));
}

return static fn(array $context): Kernel => new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
