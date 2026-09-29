<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Number;

final class Money
{
    public static function format(int $minor): string
    {
        return (string) Number::currency($minor / 100, in: (string) config('store.currency.code'), locale: app()->getLocale());
    }
}
