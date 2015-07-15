<?php
namespace EuMatheusGomes\Febraban\Util;

trait Mod11
{
    public function mod11($number, $min, $max)
    {
        $number = strrev($number);
        $number = str_split($number);

        $multiplier = range($min, $max);
        $multiplier = implode('', $multiplier);
        $multiplier = str_repeat($multiplier, ceil(count($number) / strlen($multiplier)));
        $multiplier = substr($multiplier, 0, count($number));
        $multiplier = strrev($multiplier);
        $multiplier = str_split($multiplier);

        foreach ($number as $i => $num) {
            $number[$i] = $num * $multiplier[$i];
        }

        $sum = array_sum($number);
        $mod = $sum % 11;

        if ($mod > 1 && $mod < 9) {
            return 11 - $mod;
        }

        return 1;
    }
}
