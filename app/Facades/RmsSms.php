<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(string|array $recipient, string $message, string|null $senderId = null)
 * @method static array sendWithRetry(string|array $recipient, string $message, int $retries = 3, string|null $senderId = null)
 * @method static array getBalance()
 * @method static array parseResponse(array $response)
 * 
 * @see \App\Services\RmsSmsService
 */
class RmsSms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rms-sms';
    }
}
