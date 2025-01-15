<?php

declare(strict_types=1);

use Castor\Attribute\AsArgument;
use Castor\Attribute\AsTask;

use function Castor\fingerprint;
use function Castor\import;
use function Castor\io;
use function Castor\variable;
use function symfony\symfony_install;
use function ui\ui_install;

import(__DIR__ . '/src');

#[AsTask(description: 'Build the docker containers')]
function build(bool $force = false): void
{
    if (
        ! fingerprint(
            callback: static fn () => docker([
                'compose',
                '--progress', 'plain',
                '-f', 'compose.yaml', '-f', 'compose.override.yaml',
                'build',
                '--build-arg', sprintf('USER_ID=%s', variable('user.id')),
                '--build-arg', sprintf('GROUP_ID=%s', variable('user.group')),
            ])->run(),
            id: 'docker-build',
            fingerprint: fgp()->docker(),
            force: $force || ! docker()->hasImages(['test-php', 'test-front']),
        )
    ) {
        io()->note(
            'The Dockerfile or the docker-compose files have not changed since the last run, skipping the docker build command.',
        );
    }
}

#[AsTask(description: 'Build and start the docker containers')]
function start(bool $force = false): void
{
    build($force);

    docker(['compose', 'up', '-d', '--wait', '--remove-orphans'])->run();
}

#[AsTask(description: 'Stop the docker containers')]
function stop(): void
{
    docker(['compose', 'down'])->run();
}

#[AsTask(description: 'Restart the docker containers')]
function restart(bool $force = false): void
{
    stop();
    start($force);
}

#[AsTask(description: 'Install the project dependencies')]
function install(bool $force = false, bool $noStart = false): void
{
    if ($noStart === false && ! docker()->isRunningInDocker()) {
        start();
    }

    symfony_install($force);

    console(['lexik:jwt:generate-keypair', '--skip-if-exists'])->run();

    ui_install($force);
}

#[AsTask(description: 'Run the shell in the PHP container')]
function shell(?string $user = null, string $shell = 'fish', bool $front = false): void
{
    $defaultUser = $front ? 'node' : 'www-data';

    if ($front) {
        node(['bash'], user: $user ?? $defaultUser)->run();
    }

    php([$shell], user: $user ?? $defaultUser)->run();
}

/**
 * @param array<string> $commands
 */
#[AsTask(name: 'console', description: 'Run Symfony console commands')]
function symfony_console(
    #[AsArgument(description: 'The Symfony console commands to run')]
    array $commands = [''],
    string $user = 'www-data',
): void {
    console($commands, user: $user)->run();
}
