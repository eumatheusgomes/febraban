<?php
namespace EuMatheusGomes\Febraban\Boleto;

use EuMatheusGomes\Barcode\InterleavedTwoOfFive;
use EuMatheusGomes\Febraban\Util\GetterSetter;
use EuMatheusGomes\Febraban\Util\Mod10;
use EuMatheusGomes\Febraban\Util\Mod11;
use EuMatheusGomes\Febraban\Boleto\Sacado;

class Boleto
{
    use GetterSetter;
    use Mod10;
    use Mod11;

    const DATA_BASE = '2000-07-03';

    protected $camposLinhaDigitavel = [];
    protected $camposCodigoDeBarras = [];

    protected $itf;
    protected $sacado;
    protected $cedente;

    protected $banco;
    protected $moeda;
    protected $carteira;
    protected $agencia;
    protected $modalidade;
    protected $codigoCedente;
    protected $nossoNumero;
    protected $nossoNumeroDv;
    protected $vencimento;
    protected $fatorVencimento;
    protected $numeroParcela;
    protected $valor;
    protected $dataDocumento;
    protected $numeroDocumento;
    protected $especieDoc;
    protected $especie;
    protected $aceite;
    protected $dataProcessamento;
    protected $demonstrativo;
    protected $instrucoes;

    public function __construct(InterleavedTwoOfFive $itf)
    {
        $this->itf = $itf;
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

    protected function calcDvCodigoDeBarras($campos)
    {
        return $this->mod11($campos, 2, 9);
    }

    protected function calcularCampoLinhaDigitavel($ordem)
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

    protected function calcularCampoCodigoDeBarras($ordem)
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
