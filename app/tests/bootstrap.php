<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

require dirname(__DIR__) . '/vendor/autoload.php';

new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');
new Dotenv()->bootEnv(dirname(__DIR__) . '/.env.test');

if ($_SERVER['APP_DEBUG']) {
    umask(0o000);
}

new Filesystem()->remove(__DIR__ . '/../var/cache/test');
