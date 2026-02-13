<?php

declare(strict_types=1);

namespace App\Enums;

enum TokenType
{
    case ADMIN_TOKEN;
    case USER_TOKEN;
    case NO_TOKEN;
}
