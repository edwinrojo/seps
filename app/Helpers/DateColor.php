<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateColor
{
    public static function getColor(string $dateString): string
    {
        $date = Carbon::parse($dateString);
        $now = Carbon::now();

        if ($date->isPast()) {
            return 'danger';
        } else {
            return 'success';
        }
    }
}
