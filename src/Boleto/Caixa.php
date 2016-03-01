<?php
namespace EuMatheusGomes\Febraban\Boleto;

use EuMatheusGomes\Febraban\Boleto\Boleto;

class Caixa extends Boleto
{
    const CODIGO_BANCO = '104';

    const CARTEIRA_COM_REGISTRO = '1';
    const CARTEIRA_SEM_REGISTRO = '2';

    const MODALIDADE_EMISSAO_CEDENTE = '4';

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

    public function campoLivre()
    {
        $campoLivre = [
            $this->formata($this->codigoCedente, 6),
            $this->mod11($this->codigoCedente, [2, 9]),
            substr($this->nossoNumero, 0, 3),
            $this->carteira,
            substr($this->nossoNumero, 3, 3),
            $this->modalidade,
            substr($this->nossoNumero, 6, 9)
        ];

        $campoLivre[] = $this->mod11(implode('', $campoLivre), [2, 9]);

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
        $carteiras = [1 => 'RG', 2 => 'SR'];
        return $carteiras[$this->carteira];
    }
}
