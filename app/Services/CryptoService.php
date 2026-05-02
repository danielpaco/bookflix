<?php

namespace App\Services;

class CryptoService
{
    public static function getBookKey($bookId)
    {
        return hash_hmac('sha256', $bookId, config('app.key'), true);
    }

    public static function getPageKey($bookId, $page)
    {
        return hash_hmac(
            'sha256',
            $page,
            self::getBookKey($bookId),
            true
        );
    }
}