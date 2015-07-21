<?php
namespace EuMatheusGomes\Febraban\Boleto;

use EuMatheusGomes\Febraban\Boleto\Boleto;

class Sicoob extends Boleto
{
    const CONST_NOSSO_NUMERO = '3197';
    const CODIGO_BANCO = '756';

    public function codigoDeBarras($renderizar = false)
    {
        $codigoDeBarras = [
            1  => $this->formata($this->banco, 3),
            2  => $this->formata($this->moeda, 1),
            4  => $this->formata($this->fatorVencimento, 4),
            5  => $this->formata($this->valor, 10),
            6  => $this->formata($this->carteira, 1),
            7  => $this->formata($this->agencia, 4),
            8  => $this->formata($this->modalidade, 2),
            9  => $this->formata($this->codigoCedente, 7),
            10 => $this->formata($this->nossoNumero, 8),
            11 => $this->formata($this->numeroParcela, 3),
        ];

        $codigoDeBarras[3]  = $this->mod11(implode('', $codigoDeBarras), [2, 9]);

        ksort($codigoDeBarras);
        $codigoDeBarras = implode('', $codigoDeBarras);

        if ($renderizar) {
            return $this->itf->render($codigoDeBarras);
        }

        return $codigoDeBarras;
    }

    public function linhaDigitavel($formatada = false)
    {
        $codigoDeBarras = $this->codigoDeBarras();

        $linhaDigitavel[1]  = $this->banco;
        $linhaDigitavel[1] .= $this->moeda;
        $linhaDigitavel[1] .= $this->carteira;
        $linhaDigitavel[1] .= $this->agencia;
        $linhaDigitavel[1] .= $this->mod10($linhaDigitavel[1]);

        $linhaDigitavel[2]  = $this->modalidade;
        $linhaDigitavel[2] .= $this->formata($this->codigoCedente, 7);
        $linhaDigitavel[2] .= substr($this->nossoNumero, 0, 1);
        $linhaDigitavel[2] .= $this->mod10($linhaDigitavel[2]);

        $linhaDigitavel[3]  = substr($this->nossoNumero, 1);
        $linhaDigitavel[3] .= $this->formata($this->numeroParcela, 3);
        $linhaDigitavel[3] .= $this->mod10($linhaDigitavel[3]);

        $linhaDigitavel[4]  = substr($codigoDeBarras, 4, 1);

        $linhaDigitavel[5]  = $this->fatorVencimento;
        $linhaDigitavel[5] .= $this->formata($this->valor, 10);

        $separador = '';
        if ($formatada) {
            $separador = ' ';

            for ($i = 1; $i <= 3; $i++) {
                $linhaDigitavel[$i] = substr_replace($linhaDigitavel[$i], '.', 5, 0);
            }
        }

        return implode($separador, $linhaDigitavel);
    }

    public function setNossoNumero($numeroSequencial = 1)
    {
        $this->nossoNumero = str_pad($numeroSequencial, 7, 0, STR_PAD_LEFT);
        $this->nossoNumeroDv = $this->calculaNossoNumeroDv();

        $this->nossoNumero .= $this->nossoNumeroDv;

        return $this;
    }

    private function calculaNossoNumeroDv()
    {
        foreach (['agencia', 'codigoCedente'] as $atributo) {
            if (is_null($this->{$atributo})) {
                throw new \InvalidArgumentException(
                    "Atributo \"{$atributo}\" não pode ser nulo."
                );
            }
        }

        $codigoCedente = str_replace('-', '', $this->codigoCedente);

        $base  = $this->agencia;
        $base .= str_pad($codigoCedente, 10, 0, STR_PAD_LEFT);
        $base .= $this->nossoNumero;

        return $this->mod11(strrev($base), str_split(self::CONST_NOSSO_NUMERO), 0);
    }
}
