<?php

declare(strict_types=1);

namespace App\Shared\Api;

class ApiGroups
{
    public const string GET_ONE = 'GET_ONE';

    public const string GET_PAGINATED = 'GET_PAGINATED';

    public const string GET_LIST = 'GET_LIST';

    public const string POST = 'POST';

    public const string PUT = 'PUT';

    public const string PATCH = 'PATCH';
    public const string DELETE = 'DELETE';


    public const array ALL = [
        self::GET_ONE,
        self::GET_PAGINATED,
        self::GET_LIST,
        self::POST,
        self::PUT,
        self::DELETE,
        self::PATCH,
    ];
}
