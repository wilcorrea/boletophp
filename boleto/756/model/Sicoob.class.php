<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sicoob
 *
 * @author Analista
 */
class Sicoob extends Banco {
  //put your code here
  
  public function Sicoob() {

    $this->blt_modalidade_cobranca = "02";
    $this->blt_numero_parcela = "001";
    $this->blt_carteira = "1";
    $this->blt_moeda = "9";
    $this->blt_quantidade = "10";
    $this->blt_valor_unitario = "10";
    $this->blt_aceite = "N";
    $this->blt_especie = "R$";
    $this->blt_especie_documento = "DM";

  }
  
  /**
   * 
   * @param Boleto $boleto
   * 
   * @return type
   */
  public function geraNossoNumero(Boleto $boleto) {
    
    $blt_cedente_agencia = str_pad($boleto->get_blt_value('blt_cedente_agencia'), 4, "0", STR_PAD_LEFT);
    $blt_cedente_contrato = str_pad($boleto->get_blt_value('blt_cedente_contrato'), 10, "0", STR_PAD_LEFT);
    $blt_numero_documento = str_pad($boleto->get_blt_value('blt_numero_documento'), 7, "0", STR_PAD_LEFT);

    $linha = $blt_cedente_agencia . $blt_cedente_contrato . $blt_numero_documento;
    $constant = "319731973197319731973";

    $total = 0;
    for($i = 0; $i <= 20; $i++){
      $total += ($linha{$i} * $constant{$i});
    }
    
    $resto = $total % 11;

    if ($resto == 0 or $resto == 1) {
      $dv = 0;
    } else {
      $dv = 11 - $resto;
    }

    $nosso_numero = $blt_numero_documento . "-" . $dv;

    return $nosso_numero;
  }
  
  /**
   * 
   * @param Boleto $boleto
   * @param string $blt_nosso_numero
   * @return string
   */
  public function geraLinhaDigitavel(Boleto $boleto) {

    $blt_banco = $boleto->blt_banco;
    $blt_nosso_numero = $this->geraNossoNumero($boleto);

    $blt_modalidade_cobranca = $this->blt_modalidade_cobranca;
    $blt_numero_parcela = $this->blt_numero_parcela;
    $blt_carteira = $this->blt_carteira;
    $blt_moeda = $this->blt_moeda;
    
    $valor = $this->formataNumero($boleto->blt_valor, 10, 0, "valor");
    
    $blt_cedente_agencia = $this->formataNumero($boleto->get_blt_value('blt_cedente_agencia'), 4, 0);
    $fator_vencimento = $this->geraFatorVencimento($boleto->get_blt_value('blt_data_vencimento'));

    $blt_cedente_contrato = $this->formataNumero($boleto->get_blt_value('blt_cedente_contrato'), 7, 0);

    $campo_livre = $this->montaCampoLivre($blt_modalidade_cobranca, $blt_cedente_contrato, $blt_nosso_numero, $blt_numero_parcela);

    $digito_verificador = $this->modulo11($blt_banco . $blt_moeda . $fator_vencimento . $valor . $blt_carteira . $blt_cedente_agencia . $campo_livre);
    $blt_linha_digitavel = $blt_banco . $blt_moeda . $digito_verificador . $fator_vencimento . $valor . $blt_carteira . $blt_cedente_agencia . $campo_livre;

    return $blt_linha_digitavel;
  }

  /**
   * 
   * @param Boleto $boleto
   */
  public function geraCedenteContrato(Boleto $boleto) {
    return $this->formataNumero($boleto->get_blt_value('blt_cedente_contrato'), 7, 0);
  }

  /**
   * 
   * @param type $num
   * @return int
   */
  public function modulo10($num) {
    $numtotal10 = 0;
    $fator = 2;

    for ($i = strlen($num); $i > 0; $i--) {
      $numeros[$i] = String::substring($num, $i - 1, 1);
      $parcial10[$i] = $numeros[$i] * $fator;
      $numtotal10 .= $parcial10[$i];
      if ($fator == 2) {
        $fator = 1;
      } else {
        $fator = 2;
      }
    }

    $soma = 0;
    for ($i = strlen($numtotal10); $i > 0; $i--) {
      $numeros[$i] = String::substring($numtotal10, $i - 1, 1);
      $soma += $numeros[$i];
    }
    $resto = $soma % 10;
    $digito = 10 - $resto;
    if ($resto == 0) {
      $digito = 0;
    }

    return $digito;
  }
  
  /**
   * 
   * @param type $num
   * @param type $base
   * @param type $r
   * @return int
   */
  public function modulo11($num, $base = 9, $r = 0) {

    $soma = 0;
    $fator = 2;
    for ($i = strlen($num); $i > 0; $i--) {
      $numeros[$i] = String::substring($num, $i - 1, 1);
      $parcial[$i] = $numeros[$i] * $fator;
      $soma += $parcial[$i];
      if ($fator == $base) {
        $fator = 1;
      }
      $fator++;
    }
    if ($r == 0) {
      $soma *= 10;
      $digito = $soma % 11;

      //corrigido
      if ($digito == 10) {
        $digito = "X";
      }

      if (strlen($num) == "43") {
        //então estamos checando a linha digitável
        if ($digito == "0" or $digito == "X" or $digito > 9) {
          $digito = 1;
        }
      }
      return $digito;
    } elseif ($r == 1) {
      $resto = $soma % 11;
      return $resto;
    }

  }
  
  /**
   * 
   * @param type $linha
   * @return type
   */
  public function montaLinhaDigitavel($linha) {
    // Posição 	Conteúdo
    // 1 a 3    Número do banco
    // 4        Código da Moeda - 9 para Real
    // 5        Digito verificador do Código de Barras
    // 6 a 19   Valor (12 inteiros e 2 decimais)
    // 20 a 44  Campo Livre definido por cada banco
    // 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
    // do campo livre e DV (modulo10) deste campo
    $p11 = String::substring($linha, 0, 4);
    $p12 = String::substring($linha, 19, 5);
    $p13 = $this->modulo10($p11 . $p12);
    $p14 = $p11 . $p12 . $p13;
    $p15 = String::substring($p14, 0, 5);
    $p16 = String::substring($p14, 5);
    $campo1 = $p15 . "." . $p16;

    // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
    // e livre e DV (modulo10) deste campo
    $p21 = String::substring($linha, 24, 10);
    $p22 = $this->modulo10($p21);
    $p23 = $p21 . $p22;
    $p24 = String::substring($p23, 0, 5);
    $p25 = String::substring($p23, 5);
    $campo2 = $p24 . "." . $p25;

    // 3. Campo composto pelas posicoes 16 a 25 do campo livre
    // e livre e DV (modulo10) deste campo
    $p31 = String::substring($linha, 34, 10);
    $p32 = $this->modulo10($p31);
    $p33 = $p31 . $p32;
    $p34 = String::substring($p33, 0, 5);
    $p35 = String::substring($p33, 5);
    $campo3 = $p34 . "." . $p35;

    // 4. Campo - digito verificador do codigo de barras
    $campo4 = String::substring($linha, 4, 1);

    // 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
    // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
    // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
    $campo5 = String::substring($linha, 5, 14);

    return $campo1 . " " . $campo2 . " " . $campo3 . " " . $campo4 . " " . $campo5;
  }

  /**
   * 
   * @param type $blt_modalidade_cobranca
   * @param type $blt_cedente_contrato
   * @param type $blt_nosso_numero
   * @param type $blt_numero_parcela
   * 
   * @return string
   */
  private function montaCampoLivre($blt_modalidade_cobranca, $blt_cedente_contrato, $blt_nosso_numero, $blt_numero_parcela) {
    
    $blt_nosso_numero = $this->formataNumero(String::replace($blt_nosso_numero, "-", ""), 8, 0);
    
    $campo_livre = $blt_modalidade_cobranca . $blt_cedente_contrato . $blt_nosso_numero . $blt_numero_parcela;
    
    return $campo_livre;
  }

}
