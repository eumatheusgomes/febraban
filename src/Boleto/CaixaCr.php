<?php
namespace EuMatheusGomes\Febraban\Boleto;

use EuMatheusGomes\Febraban\Boleto\Boleto;

class CaixaCr extends Boleto
{
    const CODIGO_BANCO = '104';
    const DATA_BASE = '1997-10-07';
    const CARTEIRA_COBRANCA_RAPIDA = '9';

    public function codigoDeBarras($renderizar = false)
    {
        $codigoDeBarras = [
            1 => $this->formata($this->banco, 3),
            2 => $this->formata($this->moeda, 1),
            4 => $this->formata($this->fatorVencimento, 4),
            5 => $this->formata($this->valor, 10),
            6 => $this->campoLivre()
        ];

        $codigoDeBarras[3]  = $this->mod11(implode('', $codigoDeBarras), [2, 9]);

        ksort($codigoDeBarras);
        $codigoDeBarras = implode('', $codigoDeBarras);

        if ($renderizar) {
            return $this->itf->render($codigoDeBarras);
        }

        return $codigoDeBarras;
    }

    public function setVencimento($vencimento = null)
    {
        $this->vencimento = $vencimento;

        if (is_null($this->vencimento)) {
            $this->fatorVencimento = '0000';
        } else {

            $vencimento = new \DateTime($vencimento);
            $diff = $vencimento->diff(new \DateTime(self::DATA_BASE));

            $this->fatorVencimento = $diff->days;
        }

        return $this;
    }

    public function campoLivre()
    {
        $campoLivre = [
            $this->carteira,
            $this->nossoNumero,
            $this->codigoCedente,
        ];

        return implode('', $campoLivre);
    }

    public function linhaDigitavel($formatada = false)
    {
        $codigoDeBarras = $this->codigoDeBarras();

        $linhaDigitavel[1]  = substr($codigoDeBarras, 0, 4) . substr($codigoDeBarras, 19, 5);
        $linhaDigitavel[1] .= $this->mod10($linhaDigitavel[1]);

        $linhaDigitavel[2]  = substr($codigoDeBarras, 24, 10);
        $linhaDigitavel[2] .= $this->mod10($linhaDigitavel[2]);

        $linhaDigitavel[3]  = substr($codigoDeBarras, 34, 10);
        $linhaDigitavel[3] .= $this->mod10($linhaDigitavel[3]);

        $linhaDigitavel[4]  = substr($codigoDeBarras, 4, 1);

        $linhaDigitavel[5]  = substr($codigoDeBarras, 5, 4);
        $linhaDigitavel[5] .= substr($codigoDeBarras, 9, 10);

        $separador = '';
        if ($formatada) {
            $separador = ' ';

            for ($i = 1; $i <= 3; $i++) {
                $linhaDigitavel[$i] = substr_replace($linhaDigitavel[$i], '.', 5, 0);
            }
        }

        return implode($separador, $linhaDigitavel);
    }

    public function getCarteira()
    {
        $carteiras = [9 => 'CR'];
        return $carteiras[$this->carteira];
    }
}
