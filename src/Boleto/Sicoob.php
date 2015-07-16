<?php
namespace EuMatheusGomes\Febraban\Boleto;

use EuMatheusGomes\Febraban\Boleto\Boleto;

class Sicoob extends Boleto
{
    const CONST_NOSSO_NUMERO = '3197';
    const CODIGO_BANCO = '756';

    protected $formatoLinhaDigitavel = [
        1 => [['banco' => 3], ['moeda' => 1], ['carteira' => 1], ['agencia' => 4]],
        2 => [['modalidade' => 2], ['codigoCedente' => 7], ['nossoNumero' => 1]],
        3 => [['nossoNumero' => [1, 7]], ['numeroParcela' => 3]],
        5 => [['fatorVencimento' => 4], ['valor' => 10]],
        4 => 'calcDvCodigoDeBarras',
    ];

    protected $formatoCodigoDeBarras = [
        1 => [['banco' => 3]],
        2 => [['moeda' => 1]],
        4 => [['fatorVencimento' => 4]],
        5 => [['valor' => 10]],
        6 => [['carteira' => 1]],
        7 => [['agencia' => 4]],
        8 => [['modalidade' => 2]],
        9 => [['codigoCedente' => 7]],
        10 => [['nossoNumero' => 8]],
        11 => [['numeroParcela' => 3]],
        3 => 'calcDvCodigoDeBarras'
    ];

    public function linhaDigitavel($formatada = false)
    {
        foreach ($this->formatoLinhaDigitavel as $i => $v) {
            $this->camposLinhaDigitavel[$i] = $this->calcularCampoLinhaDigitavel($i);
        }

        ksort($this->camposLinhaDigitavel);
        if ($formatada) {

            $linhaDigitavel  = substr_replace($this->camposLinhaDigitavel[1], '.', 5, 0) . ' ';
            $linhaDigitavel .= substr_replace($this->camposLinhaDigitavel[2], '.', 5, 0) . ' ';
            $linhaDigitavel .= substr_replace($this->camposLinhaDigitavel[3], '.', 5, 0) . ' ';
            $linhaDigitavel .= $this->camposLinhaDigitavel[4] . ' ';
            $linhaDigitavel .= $this->camposLinhaDigitavel[5];

            return $linhaDigitavel;
        }

        return implode('', $this->camposLinhaDigitavel);
    }

    public function codigoDeBarras($renderizar = false)
    {
        foreach ($this->formatoCodigoDeBarras as $i => $v) {
            $this->camposCodigoDeBarras[$i] = $this->calcularCampoCodigoDeBarras($i);
        }

        ksort($this->camposCodigoDeBarras);
        $codigoDeBarras = implode('', $this->camposCodigoDeBarras);

        if ($renderizar) {
            return $this->itf->render($codigoDeBarras);
        }

        return $codigoDeBarras;
    }


    public function setNossoNumero($numeroSequencial = 1)
    {
        $codigoCedente = str_replace('-', '', $this->codigoCedente);
        $this->nossoNumero = str_pad($numeroSequencial, 7, 0, STR_PAD_LEFT);

        $base  = $this->agencia;
        $base .= str_pad($codigoCedente, 10, 0, STR_PAD_LEFT);
        $base .= $this->nossoNumero;

        $multiplicador = str_repeat(self::CONST_NOSSO_NUMERO, 6);
        $multiplicador = substr($multiplicador, 0, 21);

        $arrBase = str_split($base);
        $arrMultiplicador = str_split($multiplicador);

        $arrNossoNumero = array_map(function ($f1, $f2) {
            return $f1 * $f2;
        }, $arrBase, $arrMultiplicador);

        $soma = array_sum($arrNossoNumero);
        $mod = $soma % 11;

        if ($mod > 1 && $mod < 10) {
            $this->nossoNumeroDv = 11 - $mod;
        }

        $this->nossoNumeroDv = 0;

        return $this;
    }
}
