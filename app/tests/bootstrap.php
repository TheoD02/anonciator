<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../config/bootstrap.php')) {
    require __DIR__ . '/../config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    new Dotenv()->bootEnv(__DIR__ . '/../.env');
}

umask(0o000);

if (is_dir(__DIR__ . '/../var/cache/test')) {
    $filesystem = new Filesystem();
    $filesystem->remove(__DIR__ . '/../var/cache/test');
}
