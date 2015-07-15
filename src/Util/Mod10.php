<?php
namespace EuMatheusGomes\Febraban\Util;

trait Mod10
{
    public function mod10($number)
    {
        $reverseNumber = strrev($number);
        $numbersArray = str_split($reverseNumber);

        foreach ($numbersArray as $i => $num) {
            if ($i % 2 == 0) {
                $num *= 2;

                if ($num > 9) {
                    $num = str_split((string) $num);
                    $num = $num[0] + $num[1];
                }

                $numbersArray[$i] = $num;
            }
        }

        $sum = array_sum($numbersArray);
        $mod = $sum % 10;

        if ($mod == 0) {
            return 0;
        }

        return 10 - $mod;
    }
}
