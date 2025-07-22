<?php


namespace App\Exceptions;

use Spatie\Permission\Exceptions\UnauthorizedException;

class UserInActiveException extends UnauthorizedException
{
}
