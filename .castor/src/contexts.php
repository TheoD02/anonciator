<?php

declare(strict_types=1);

use Castor\Attribute\AsContext;
use Castor\Context;

use function Castor\capture;

define('ROOT_DIR', dirname(__DIR__, 2));
define('USER_ID', capture(['id', '-u']));
define('GROUP_ID', capture(['id', '-g']));

#[AsContext(default: true)]
function root_context(): Context
{
    return new Context(
        data: [
            'user.id' => USER_ID,
            'user.group' => GROUP_ID,
        ],
        workingDirectory: ROOT_DIR
    );
}

#[AsContext(name: 'symfony')]
function symfony_context(): Context
{
    return root_context()
        ->withName('symfony')
        ->withWorkingDirectory(ROOT_DIR . '/app')
        ->withData([
            'registry' => 'registry.theo-corp.fr',
            'image' => 'theod02/demo-app-symfony',
        ])
    ;
}

#[AsContext(name: 'frontend')]
function frontend_context(): Context
{
    return root_context()
        ->withName('frontend')
        ->withWorkingDirectory(ROOT_DIR . '/front')
        ->withData([
            'registry' => 'registry.theo-corp.fr',
            'image' => 'theod02/demo-app-frontend',
        ])
    ;
}
