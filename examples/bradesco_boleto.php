<?php

require '../vendor/autoload.php';

use EuMatheusGomes\Barcode\InterleavedTwoOfFive;
use EuMatheusGomes\Febraban\Boleto\Bradesco;
use EuMatheusGomes\Febraban\Boleto\Sacado;
use EuMatheusGomes\Febraban\Boleto\Cedente;

$itf = new InterleavedTwoOfFive();
$itf->setImages([
    'bar' => 'img/bar.png',
    'space' => 'img/space.png'
]);

$sacado = new Sacado();
$sacado->setNome('Consectetur Adipisicing Elit')
    ->setEndereco('Tempor Incididunt Ut Labore')
    ->setCidade('Nostrud Exercitation')
    ->setUf('AZ')
    ->setCep('99999-999')
    ->setCpfCnpj('999.999.999-99');

$cedente = new Cedente();
$cedente->setNome('Consectetur Adipisicing Elit')
    ->setCpfCnpj('999.999.999-99');

$bradesco = new Bradesco($itf);
$bradesco->setBanco(Bradesco::CODIGO_BANCO)
    ->setMoeda('9')
    ->setCarteira('09')
    ->setAgencia('999')
    ->setAgenciaDv('9')
    ->setConta('9999')
    ->setContaDv('9')
    ->setVencimento(date('Y-m-d', strtotime('+9 days')))
    ->setValor('999.99')
    ->setDataDocumento(date('Y-m-d'))
    ->setNumeroDocumento('99999999')
    ->setEspecieDoc('DM')
    ->setEspecie('R$')
    ->setAceite('N')
    ->setDataProcessamento(date('Y-m-d'))
    ->setNossoNumero('00000009999')
    ->setDemonstrativo([
        'Ut enim ad minim veniam',
        'Cillum dolore eu fugiat nulla pariatur',
        '',
        'Excepteur sint occaecat cupidatat non'
    ])
    ->setInstrucoes([
        'Sunt in culpa qui officia deserunt mollit',
        'Exercitation ullamco laboris nisi dolore eu fugiat nulla pariatur',
        '',
        'Quis nostrud ut aliquip ex ea commodo consequat velit esse'
    ])
    ->setSacado($sacado)
    ->setCedente($cedente);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Bradesco</title>
    <style type="text/css">
      table {
        font-family: sans-serif;
        width: 100%
      }
      td {
        line-height: 1;
        font-size: 14px;
        padding: 2px;
      }
      small {
        color: #666;
        display: block;
        font-size: 10px
      }

      .container { width: 700px }

      .brd-t { border-top: 1px solid #000 }
      .brd-r { border-right: 1px solid #000 }
      .brd-b { border-bottom: 1px solid #000 }
      .brd-l { border-left: 1px solid #000 }

      .brd-dsh-t { border-top: 1px dashed #000}
      .brd-dsh-b { border-bottom: 1px dashed #000}

      .right, .left, .center {
        display: block;
        font-size: 12px;
      }
      .right { text-align: right }
      .left { text-align: left }
      .center { text-align: center }

      .txt-bigger { font-size: 20px !important }
      .txt-big { font-size: 14px !important }
      .txt-small { font-size: 11px !important }
      .txt-right { text-align: right !important }

      .no-pd { padding: 0 }
      .pd-t { padding: 10px 0 0 }

      ul, li {
        font-size: 11px;
        font-weight: bold;
        margin: 0;
      }
      ul { padding: 0 0 0 14px }
      li { padding: 1px 0 }
    </style>
  </head>
  <body>
    <div class="container">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <strong class="txt-small">Instruções de Impressão</strong>
              <ul>
                <li>
                  Imprima em impressora jato de tinta (ink jet) ou laser em qualidade
                  normal ou alta (não use modo econômico).
                </li>
                <li>
                  Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens mínimas
                  à esquerda e à direita do formulário.
                </li>
                <li>
                  Corte na linha indicada. Não rasure, risque, fure ou dobre a região onde se
                  encontra o código de barras.
                </li>
                <li>
                  Caso tenha problemas ao imprimir, copie a seqüencia numérica abaixo e pague
                  no caixa eletrônico ou no internet banking.
                </li>
            </ul>
          </td>
        </tr>
        <tr><td>&nbsp;</td><tr>
        <tr>
          <td>
            <strong class="txt-small">
              Linha digitável: <?= $bradesco->linhaDigitavel(true) ?>
            </strong><br>
            <strong class="txt-small">
              Valor: R$ <?= number_format($bradesco->getValor(), 2, ',', '.') ?>
            </strong>
          </td>
        </tr>
        <tr><td>&nbsp;</td><tr>
        <tr>
          <td class="brd-dsh-t">
            <strong class="right txt-small">Recibo do pagador</strong>
          </td>
        </tr>
        <tr><td>&nbsp;</td><tr>
      </table>
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" class="no-pd">
            <table cellspacing="0" cellspacing="0">
              <tr>
                <td class="brd-b" width="150" valign="bottom">
                  <img src="img/bradesco.jpg" width="150">
                </td>
                <td class="brd-b" width="120" valign="bottom">
                  <strong class="center txt-bigger">| <?= $bradesco->getBanco() ?>-2 |</strong>
                </td>
                <td class="brd-b" valign="bottom">
                  <div class="right txt-big"><?= $bradesco->linhaDigitavel(true) ?></div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td class="no-pd">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="brd-t brd-b brd-r" valign="top">
                  <small>Beneficiário</small>
                  <strong class="left">
                    <?= $bradesco->getCedente()->getNome() ?>

                </strong>
                </td>
                <td class="brd-t brd-b brd-r" valign="top">
                  <small>Agência/Código do Beneficiário</small>
                  <strong class="right">
                    <?= str_pad($bradesco->getAgencia(), 4, 0, STR_PAD_LEFT) ?>-<?= $bradesco->getAgenciaDv() ?>/<?= str_pad($bradesco->getConta(), 7, 0, STR_PAD_LEFT) ?>-<?= $bradesco->getContaDv() ?>
                  </strong>
                </td>
                <td class="brd-t brd-b brd-r" valign="top">
                  <small>Espécie</small>
                  <strong class="center">
                    <?= $bradesco->getEspecie() ?>
                  </strong>
                </td>
              </tr>
            </table>
          </td>
          <td class="no-pd">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="brd-t brd-b brd-l" valign="top">
                  <small>Quantidade</small>
                  <strong class="left">&nbsp;</strong>
                </td>
                <td class="brd-t brd-b brd-l" valign="top">
                  <small>Nosso Número</small>
                  <strong class="right">
                    <?= $bradesco->getNossoNumero() ?>
                  </strong>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td class="no-pd">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="brd-b brd-r" valign="top">
                  <small>N. do Documento</small>
                  <strong class="right">
                    <?= str_pad($bradesco->getNumeroDocumento(), 7, 0, STR_PAD_LEFT) ?>
                  </strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>CPF/CNPJ</small>
                  <strong class="right">
                    <?= $bradesco->getCedente()->getCpfCnpj() ?>
                  </strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Vencimento</small>
                  <strong class="center">
                    <?= date('d/m/Y', strtotime($bradesco->getVencimento())) ?>
                  </strong>
                </td>
              </tr>
            </table>
          </td>
          <td class="brd-b brd-l" valign="top">
            <small>Valor documento</small>
            <strong class="right">
              R$ <?= number_format($bradesco->getValor(), 2, ',', '.') ?>
            </strong>
          </td>
        </tr>

        <tr>
          <td class="no-pd">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="brd-b brd-r" valign="top">
                  <small>(-) Desconto / Abatimento</small>
                  <strong class="right">&nbsp;</strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>(-) Outras deduções</small>
                  <strong class="right">&nbsp;</strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>(+) Mora / Multa</small>
                  <strong class="center">&nbsp;</strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>(+) Outros acréscimos</small>
                  <strong class="center">&nbsp;</strong>
                </td>
              </tr>
            </table>
          </td>
          <td class="brd-b brd-l" valign="top">
            <small>(=) Valor cobrado</small>
            <strong class="right">&nbsp;</strong>
          </td>
        </tr>

        <tr>
          <td colspan="2" class="brd-b" valign="top">
            <small>Pagador</small>
            <strong class="left"><?= $bradesco->getSacado()->getNome() ?></strong>
          </td>
        </tr>

        <tr>
          <td valign="top">
            <small>Demonstrativo</small>
            <strong class="left txt-small">
              <br><?= implode('<br>', $bradesco->getDemonstrativo()) ?>
            </strong>
          </td>
          <td valign="top">
            <small class="txt-right">Autenticação mecânica</small>
            <strong class="right">&nbsp;</strong>
          </td>
        </tr>

        <tr><td colspan="2">&nbsp;</td><tr>
        <tr><td colspan="2">&nbsp;</td><tr>
        <tr><td colspan="2">&nbsp;</td><tr>
        <tr>
            <td colspan="2" class="brd-dsh-b txt-right">
                <small>Corte na linha pontilhada</small>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td><tr>
        <tr><td colspan="2">&nbsp;</td><tr>
      </table>
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" style="padding:0">
            <table cellspacing="0" cellspacing="0">
              <tr>
                <td class="brd-b" width="150" valign="bottom">
                  <img src="img/bradesco.jpg" width="150">
                </td>
                <td class="brd-b" width="120" valign="bottom">
                  <strong class="center txt-bigger">| <?= $bradesco->getBanco() ?>-2 |</strong>
                </td>
                <td class="brd-b" valign="bottom">
                  <div class="right txt-big"><?= $bradesco->linhaDigitavel(true) ?></div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td class="brd-t brd-b brd-r" valign="top">
            <small>Local do Pagamento</small>
            <strong class="left">Pagável Preferencialmente na rede Bradesco ou no Bradesco Expresso</strong>
          </td>
          <td class="brd-t brd-b brd-l" valign="top">
            <small>Vencimento</small>
            <strong class="right"><?= date('d/m/Y', strtotime($bradesco->getVencimento())) ?></strong>
          </td>
        </tr>
        <tr>
          <td class="brd-t brd-b brd-r" valign="top">
            <small>Beneficiário</small>
            <strong class="left">
                <?= $bradesco->getCedente()->getNome() ?> -
                <?= $bradesco->getCedente()->getCpfCnpj() ?>
            </strong>
          </td>
          <td class="brd-t brd-b brd-l" valign="top">
            <small>Agência/Código do Beneficiário</small>
            <strong class="right">
              <?= str_pad($bradesco->getAgencia(), 4, 0, STR_PAD_LEFT) ?>-<?= $bradesco->getAgenciaDv() ?>/<?= str_pad($bradesco->getConta(), 7, 0, STR_PAD_LEFT) ?>-<?= $bradesco->getContaDv() ?>
            </strong>
          </td>
        </tr>
        <tr>
          <td class="no-pd">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="brd-b brd-r" valign="top">
                  <small>Data do doc.</small>
                  <strong class="right">
                    <?= date('d/m/Y', strtotime($bradesco->getDataDocumento())) ?>
                  </strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>N. do Documento</small>
                  <strong class="right">
                    <?= str_pad($bradesco->getNumeroDocumento(), 7, 0, STR_PAD_LEFT) ?>
                  </strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Espécie DOC.</small>
                  <strong class="center">
                    <?= $bradesco->getEspecieDoc() ?>
                  </strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Aceite</small>
                  <strong class="center">
                    <?= $bradesco->getAceite() ?>
                  </strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Data Processamento</small>
                  <strong class="right">
                    <?= date('d/m/Y', strtotime($bradesco->getDataProcessamento())) ?>
                  </strong>
                </td>
              </tr>
            </table>
          </td>
          <td class="brd-b brd-l" valign="top">
            <small>Carteira/Nosso Número</small>
            <strong class="right">
              <?= $bradesco->getCarteira() ?>/<?= $bradesco->getNossoNumero() ?>-<?= $bradesco->getNossoNumeroDv() ?>
            </strong>
          </td>
        </tr>
        <tr>
          <td class="no-pd">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="brd-b brd-r" valign="top">
                  <small>Uso do Banco</small>
                  <strong class="right">&nbsp;</strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Carteira</small>
                  <strong class="center"><?= $bradesco->getCarteira() ?></strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Espécie</small>
                  <strong class="center"><?= $bradesco->getEspecie() ?></strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Quantidade</small>
                  <strong class="center">&nbsp;</strong>
                </td>
                <td class="brd-b brd-r" valign="top">
                  <small>Valor</small>
                  <strong class="center">&nbsp;</strong>
                </td>
              </tr>
            </table>
          </td>
          <td class="brd-b brd-l" valign="top">
            <small>(=) Valor do Documento</small>
            <strong class="right">
              R$ <?= number_format($bradesco->getValor(), 2, ',', '.') ?>
            </strong>
          </td>
        </tr>
        <tr>
          <td rowspan="5" class="brd-b brd-r" valign="top">
            <small>Instruções</small>
            <strong class="left txt-small"><br><?= implode('<br>', $bradesco->getInstrucoes()) ?></strong>
          </td>
          <td class="brd-b brd-l" valign="top">
            <small>(-) Desconto</small>
            <strong class="right">&nbsp;</strong>
          </td>
        </tr>
        <tr>
          <td class="brd-b brd-l" valign="top">
            <small>(-) Outras deduções (abatimento)</small>
            <strong class="right">&nbsp;</strong>
          </td>
        </tr>
        <tr>
          <td class="brd-b brd-l" valign="top">
            <small>(+) Mora / Multas (Juros)</small>
            <strong class="right">&nbsp;</strong>
          </td>
        </tr>
        <tr>
          <td class="brd-b brd-l" valign="top">
            <small>(+) Outros Acrescimos</small>
            <strong class="right">&nbsp;</strong>
          </td>
        </tr>
        <tr>
          <td class="brd-b brd-l" valign="top">
            <small>(=) Valor cobrado</small>
            <strong class="right">&nbsp;</strong>
          </td>
        </tr>
        <tr>
          <td valign="top">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:0" valign="top" width="60">
                  <small>Pagador:</small>
                </td>
                <td style="padding:0" valign="top">
                  <strong class="left">
                    <?= $bradesco->getSacado()->getNome() ?><br>
                    <?= $bradesco->getSacado()->getEndereco() ?><br>
                    <?= $bradesco->getSacado()->getCidade() ?> -
                    <?= $bradesco->getSacado()->getUf() ?> -
                    <?= $bradesco->getSacado()->getCep() ?>
                  </strong>
                </td>
              </tr>
            </table>
          </td>
          <td valign="top">
            <small>CPF/CNPJ</small>
            <strong class="left"><?= $bradesco->getSacado()->getCpfCnpj() ?></strong>
          </td>
        </tr>
        <tr>
          <td class="brd-b" valign="top">
            <small>Sacador / Avalista:</small>
            <strong class="left">&nbsp;</strong>
          </td>
          <td class="brd-b" valign="top">
            <strong class="left">&nbsp;</strong>
            <small>Código da Baixa:</small>
          </td>
        </tr>
        <tr>
          <td class="brd-t pd-t" valign="bottom">
            <?= $bradesco->codigoDeBarras(true) ?>
          </td>
          <td class="brd-t" valign="top">
            <small>Autenticação mecânica</small>
            <strong class="left">&nbsp;</strong>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="brd-dsh-b txt-right">
            <strong class="right">Ficha de Compensação</strong>
          </td>
        </tr>
    </table>
  </body>
</html>
