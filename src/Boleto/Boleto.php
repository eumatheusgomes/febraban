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

    public function formata($atributo, $tamanho)
    {
        if (is_null($atributo)) {
            throw new \InvalidArgumentException("O atributo \"{$atributo}\" não pode ser nulo.");
        }

        if (strlen($atributo) < $tamanho) {
            $atributo = str_pad($atributo, $tamanho, 0, STR_PAD_LEFT);
        }

        return substr($atributo, 0, $tamanho);
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
}
