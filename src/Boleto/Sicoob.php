<?php
namespace EuMatheusGomes\Febraban\Boleto;

use EuMatheusGomes\Barcode\InterleavedTwoOfFive;
use EuMatheusGomes\Febraban\Util\GetterSetter;
use EuMatheusGomes\Febraban\Util\Mod10;
use EuMatheusGomes\Febraban\Util\Mod11;

class Sicoob
{
    use GetterSetter;
    use Mod10;
    use Mod11;

    const DATA_BASE = '2000-07-03';
    const CONST_NOSSO_NUMERO = '3197';
    const CODIGO_BANCO = '756';

    private $formatoLinhaDigitavel = [
        1 => [['banco' => 3], ['moeda' => 1], ['carteira' => 1], ['agencia' => 4]],
        2 => [['modalidade' => 2], ['cedente' => 7], ['nossoNumero' => 1]],
        3 => [['nossoNumero' => [1, 7]], ['numeroParcela' => 3]],
        5 => [['fatorVencimento' => 4], ['valor' => 10]],
        4 => 'calcDvCodigoDeBarras',
    ];

    private $formatoCodigoDeBarras = [
        1 => [['banco' => 3]],
        2 => [['moeda' => 1]],
        4 => [['fatorVencimento' => 4]],
        5 => [['valor' => 10]],
        6 => [['carteira' => 1]],
        7 => [['agencia' => 4]],
        8 => [['modalidade' => 2]],
        9 => [['cedente' => 7]],
        10 => [['nossoNumero' => 8]],
        11 => [['numeroParcela' => 3]],
        3 => 'calcDvCodigoDeBarras'
    ];

    private $camposLinhaDigitavel = [];
    private $camposCodigoDeBarras = [];

    private $itf;

    protected $banco;
    protected $moeda;
    protected $carteira;
    protected $agencia;
    protected $modalidade;
    protected $cedente;
    protected $nossoNumero;
    protected $nossoNumeroDv;
    protected $vencimento;
    protected $fatorVencimento;
    protected $numeroParcela;
    protected $valor;
    protected $nomeCedente;
    protected $dataDocumento;
    protected $numeroDocumento;
    protected $especieDoc;
    protected $especie;
    protected $aceite;
    protected $dataProcessamento;
    protected $sacadoNome;
    protected $sacadoEndereco;
    protected $sacadoCidade;
    protected $sacadoUf;
    protected $sacadoCep;
    protected $sacadoCpfCnpj;
    protected $cedenteCpfCnpj;
    protected $demonstrativo;
    protected $instrucoes;

    public function __construct(InterleavedTwoOfFive $itf)
    {
        $this->itf = $itf;
    }

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
        $cedente = str_replace('-', '', $this->cedente);
        $this->nossoNumero = str_pad($numeroSequencial, 7, 0, STR_PAD_LEFT);

        $base  = $this->agencia;
        $base .= str_pad($cedente, 10, 0, STR_PAD_LEFT);
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

    public function setVencimento($vencimento = null)
    {
        $this->vencimento = $vencimento;

        if (is_null($this->vencimento)) {
            $this->fatorVencimento = '0000';
        } else {

            $vencimento = new \DateTime($vencimento);
            $diff = $vencimento->diff(new \DateTime(self::DATA_BASE));

            $this->fatorVencimento = $diff->days + 1000;
        }

        return $this;
    }

    public function setValor($valor)
    {
        $this->valor = preg_replace('/\.|,/', '', $valor);
        return $this;
    }

    public function getValor()
    {
        return $this->valor / 100;
    }

    private function calcDvCodigoDeBarras($campos)
    {
        return $this->mod11($campos, 2, 9);
    }

    private function calcularCampoLinhaDigitavel($ordem)
    {
        $formato = $this->formatoLinhaDigitavel[$ordem];
        $campo = '';

        if (is_array($formato)) {
            foreach ($formato as $parte) {
                foreach ($parte as $atributo => $tamanho) {

                    if (is_null($this->{$atributo})) {
                        throw new \InvalidArgumentException(
                            "Atributo \"{$atributo}\" não pode ser nulo."
                        );
                    }

                    if (!is_array($tamanho)) {
                        $tamanho = [0, $tamanho];
                    }

                    $params = array_merge([$this->{$atributo}], $tamanho);
                    $atributo = call_user_func_array('substr', $params);

                    if (strlen($atributo) < $tamanho[1]) {
                        $atributo = str_pad($atributo, $tamanho[1], 0, STR_PAD_LEFT);
                    }

                    $campo .= $atributo;
                }
            }

            if ($ordem != 5) {
                $campo .= $this->mod10($campo);
            }

            return $campo;
        } else {
            $camposLinhaDigitavel = implode('', $this->camposLinhaDigitavel);
            return $this->{$formato}($camposLinhaDigitavel);
        }
    }

    private function calcularCampoCodigoDeBarras($ordem)
    {
        $formato = $this->formatoCodigoDeBarras[$ordem];
        $campo = '';

        if (is_array($formato)) {
            foreach ($formato as $parte) {
                foreach ($parte as $atributo => $tamanho) {

                    if (is_null($this->{$atributo})) {
                        throw new \InvalidArgumentException(
                            "Atributo \"{$atributo}\" não pode ser nulo."
                        );
                    }

                    if (!is_array($tamanho)) {
                        $tamanho = [0, $tamanho];
                    }

                    $params = array_merge([$this->{$atributo}], $tamanho);
                    $atributo = call_user_func_array('substr', $params);

                    if (strlen($atributo) < $tamanho[1]) {
                        $atributo = str_pad($atributo, $tamanho[1], 0, STR_PAD_LEFT);
                    }

                    $campo .= $atributo;
                }
            }

            return $campo;
        } else {
            $camposCodigoDeBarras = implode('', $this->camposCodigoDeBarras);
            return $this->{$formato}($camposCodigoDeBarras);
        }
    }
}
