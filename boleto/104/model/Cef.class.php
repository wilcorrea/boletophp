<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cef
 *
 * @author Analista
 */
class Cef extends Banco {
  //put your code here
  
  public function Cef() {

    $this->blt_modalidade_cobranca = "02";
    $this->blt_numero_parcela = "001";
    $this->blt_carteira = "SR";
    $this->blt_moeda = "9";
    $this->blt_quantidade = "10";
    $this->blt_valor_unitario = "";
    $this->blt_aceite = "";
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

    $nosso_numero_formatado = str_pad($boleto->get_blt_value('blt_numero_documento'), 15, "0", STR_PAD_LEFT);

    $dadosboleto = array();
    $dadosboleto["nosso_numero1"] = substr($nosso_numero_formatado, 0, 3);
    $dadosboleto["nosso_numero_const1"] = "2";
    $dadosboleto["nosso_numero2"] = substr($nosso_numero_formatado, 4, 3);
    $dadosboleto["nosso_numero_const2"] = "4";
    $dadosboleto["nosso_numero3"] = substr($nosso_numero_formatado, 7, 9);

    $n = $this->formataNumero($dadosboleto["nosso_numero_const1"], 1, 0) . $this->formataNumero($dadosboleto["nosso_numero_const2"], 1, 0) . $this->formataNumero($dadosboleto["nosso_numero1"], 3, 0) . $this->formataNumero($dadosboleto["nosso_numero2"], 3, 0) . $this->formataNumero($dadosboleto["nosso_numero3"], 9, 0);
    $nosso_numero = $n . $this->digitoVerificadorNossoNumero($n);

    return $nosso_numero;
  }

  /**
   * 
   * @param Boleto $boleto
   * @param string $blt_nosso_numero
   * @return string
   */
  public function geraLinhaDigitavel(Boleto $boleto) {

    $nosso_numero_formatado = str_pad($boleto->get_blt_value('blt_numero_documento'), 15, "0", STR_PAD_LEFT);

    $dadosboleto = array();
    $dadosboleto["nosso_numero1"] = substr($nosso_numero_formatado, 0, 3);
    $dadosboleto["nosso_numero_const1"] = "2";
    $dadosboleto["nosso_numero2"] = substr($nosso_numero_formatado, 4, 3);
    $dadosboleto["nosso_numero_const2"] = "4";
    $dadosboleto["nosso_numero3"] = substr($nosso_numero_formatado, 7, 9);

    $valor = $this->formataNumero($boleto->blt_valor, 10, 0,"valor");
    //$agencia = $this->formataNumero($boleto->get_blt_value('blt_cedente_agencia'), 4, 0);
    //$conta = $this->formataNumero($boleto->get_blt_value('blt_cedente_conta'), 5, 0);
    //$conta_dv = $this->formataNumero($boleto->get_blt_value('blt_cedente_conta_dv'), 1, 0);
    $conta_cedente = $this->formataNumero($boleto->get_blt_value('blt_cedente_contrato'), 6, 0);
    $fator_vencimento = $this->geraFatorVencimento($boleto->get_blt_value('blt_data_vencimento'));
    $codigobanco = "104";
    $nummoeda = "9";

    $conta_cedente_dv = $this->digitoVerificadorCedente($conta_cedente);
    $campo_livre = $conta_cedente . $conta_cedente_dv . $this->formataNumero($dadosboleto["nosso_numero1"], 3, 0) . $this->formataNumero($dadosboleto["nosso_numero_const1"], 1, 0) . $this->formataNumero($dadosboleto["nosso_numero2"], 3, 0) . $this->formataNumero($dadosboleto["nosso_numero_const2"], 1, 0) . $this->formataNumero($dadosboleto["nosso_numero3"], 9, 0);
    $dv_campo_livre = $this->digitoVerificadorNossoNumero($campo_livre);
    $campo_livre_com_dv ="$campo_livre$dv_campo_livre";
    $dv = $this->digitoVerificadorBarra($codigobanco . $nummoeda . $fator_vencimento . $valor . $campo_livre_com_dv, 9, 0);

    $linha_digitavel = $codigobanco . $nummoeda . $dv . $fator_vencimento . $valor . $campo_livre_com_dv;

    return $linha_digitavel;
  }

  /**
   * 
   * @param $boleto
   */ 
  public function customizaAtributos(Boleto $boleto) {

    $agencia = $this->formataNumero($boleto->get_blt_value('blt_cedente_agencia'), 4, 0);
    $conta_cedente = $this->formataNumero($boleto->get_blt_value('blt_cedente_contrato'), 6, 0);
    $conta_dv = $this->formataNumero($boleto->get_blt_value('blt_cedente_conta_dv'), 1, 0);
    $conta_cedente_dv = $this->digitoVerificadorCedente($conta_cedente);

    $blt_cedente_agencia_codigo = $agencia . " / " . $conta_cedente . "-" . $conta_cedente_dv;

    $boleto->set_blt_value('blt_cedente_agencia_codigo', $blt_cedente_agencia_codigo);

    return $boleto;
  }

  /**
   * 
   * @param $numero
   * 
   * @return type
   */
  private function digitoVerificadorNossoNumero($numero) {

    $resto2 = $this->modulo11($numero, 9, 1);
    $digito = 11 - $resto2;
    if ($digito == 10 || $digito == 11) {
      $dv = 0;
    } else {
      $dv = $digito;
    }

	  return $dv;
  }

  /**
   * 
   * @param $numero
   */
  private function digitoVerificadorCedente($numero) {

    $resto2 = $this->modulo11($numero, 9, 1);
    $digito = 11 - $resto2;
    if ($digito == 10 || $digito == 11) {
      $digito = 0;
    }
    $dv = $digito;

    return $dv;
  }

  /**
   * 
   * @param $numero
   */
  private function digitoVerificadorBarra($numero) {

    $resto2 = $this->modulo11($numero, 9, 1);
    if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
      $dv = 1;
    } else {
      $dv = 11 - $resto2;
    }
	  return $dv;
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

    // Separacao dos numeros
    for ($i = strlen($num); $i > 0; $i--) {
      // pega cada numero isoladamente
      $numeros[$i] = substr($num,$i-1,1);
      // Efetua multiplicacao do numero pelo (falor 10)
      $temp = $numeros[$i] * $fator; 
      $temp0=0;
      foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
      $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
      // monta sequencia para soma dos digitos no (modulo 10)
      $numtotal10 += $parcial10[$i];
      if ($fator == 2) {
        $fator = 1;
      } else {
        $fator = 2; // intercala fator de multiplicacao (modulo 10)
      }
    }

    // várias linhas removidas, vide função original
    // Calculo do modulo 10
    $resto = $numtotal10 % 10;
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

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {

      // pega cada numero isoladamente
      $numeros[$i] = substr($num,$i-1,1);
      // Efetua multiplicacao do numero pelo falor
      $parcial[$i] = $numeros[$i] * $fator;
      // Soma dos digitos
      $soma += $parcial[$i];
      if ($fator == $base) {
        // restaura fator de multiplicacao para 2 
        $fator = 1;
      }
      $fator++;

    }

    /* Calculo do modulo 11 */
    if ($r == 0) {

      $soma *= 10;
      $digito = $soma % 11;
      if ($digito == 10) {
          $digito = 0;
      }

      return $digito;

    } else if ($r == 1) {

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

}
