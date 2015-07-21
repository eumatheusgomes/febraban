<?php
namespace EuMatheusGomes\Febraban\Util;

trait Mod11
{
    public function mod11($number, $multiplier, $default = 1)
    {
        $number = strrev($number);
        $number = str_split($number);

        if (count($multiplier) == 2) {
            $multiplier = range($multiplier[0], $multiplier[1]);
        }

        while (count($multiplier) < count($number)) {
            $multiplier[] = current($multiplier);
            next($multiplier);
        }

        $number = array_map(function ($n, $m) {
            return $n * $m;
        }, $number, $multiplier);

        $sum = array_sum($number);
        $mod = $sum % 11;

        if ($mod > 1 && $mod <= 9) {
            return 11 - $mod;
        }

        return $default;
    }
}
