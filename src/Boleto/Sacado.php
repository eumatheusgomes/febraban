<?php
namespace EuMatheusGomes\Febraban\Boleto;

use EuMatheusGomes\Febraban\Util\GetterSetter;

class Sacado
{
    use GetterSetter;

    protected $nome;
    protected $endereco;
    protected $cidade;
    protected $uf;
    protected $cep;
    protected $cpfCnpj;
}
