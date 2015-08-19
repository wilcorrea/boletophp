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
class Itau extends Banco {
  
  public function Itau() {

    $this->blt_modalidade_cobranca = "";
    $this->blt_numero_parcela = "";
    $this->blt_carteira = "109";
    $this->blt_moeda = "9";
    $this->blt_quantidade = "";
    $this->blt_valor_unitario = "";
    $this->blt_aceite = "";
    $this->blt_especie = "R$";
    $this->blt_especie_documento = "";

  }
  
  /**
   * 
   * @param Boleto $boleto
   * @return string
   */
  public function geraNossoNumero(Boleto $boleto) {

    $carteira = $this->blt_carteira;

    $numero_documento = $this->formataNumero($boleto->get_blt_value('blt_numero_documento'), 8, 0);
    $conta = $this->formataNumero($boleto->get_blt_value('blt_cedente_conta'), 5, 0);
    $agencia = $this->formataNumero($boleto->get_blt_value('blt_cedente_agencia'), 4, 0);
    
    $nosso_numero = $carteira . '/' . $numero_documento . '-' . $this->modulo10($agencia . $conta . $carteira . $numero_documento);

    return $nosso_numero;
  }

  /**
   * 
   * @param Boleto $boleto
   * @param type $blt_nosso_numero
   */
  public function geraLinhaDigitavel(Boleto $boleto) {
    
    $blt_banco = $boleto->blt_banco;

    $carteira = $this->blt_carteira;
    $blt_moeda = $this->blt_moeda;
    
    $fator_vencimento = $this->geraFatorVencimento($boleto->get_blt_value('blt_data_vencimento'));
    $valor = $this->formataNumero($boleto->blt_valor, 10, 0, "valor");
    $agencia = $this->formataNumero($boleto->get_blt_value('blt_cedente_agencia'), 4, 0);
    $conta = $this->formataNumero($boleto->get_blt_value('blt_cedente_conta'), 5, 0);
    $nosso_numero = $this->formataNumero($boleto->get_blt_value('blt_numero_documento'), 8, 0);
    
    $segmento_1 = $this->modulo10($agencia . $conta . $carteira . $nosso_numero);
    $segmento_2 = $this->modulo10($agencia . $conta);
    
    $codigo_barras = $blt_banco . $blt_moeda . $fator_vencimento . $valor . $carteira . $nosso_numero . $segmento_1 . $agencia . $conta . $segmento_2 . '000';
    
    $dv = $this->geraDigitoVerificarBarra($codigo_barras);
    
    $linha_digitavel = substr($codigo_barras, 0, 4) . $dv. substr($codigo_barras, 4, 43);

    return $linha_digitavel;
  }
  
  /**
   * 
   * @param Boleto $boleto
   * @return type
   */
  public function geraCedenteContrato(Boleto $boleto) {
    
    $conta = $this->formataNumero($boleto->get_blt_value('blt_cedente_conta'), 5, 0);
    $agencia = $this->formataNumero($boleto->get_blt_value('blt_cedente_agencia'), 4, 0);
    
    return $conta . "-" . $this->modulo10($agencia . $conta);
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
    // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
      $temp = $numeros[$i] * $fator;
      $temp0=0;
      foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v) {
        $temp0+=$v;
      }
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

    /**
     *   Autor:
     *           Pablo Costa <pablo@users.sourceforge.net>
     *
     *   Função:
     *    Calculo do Modulo 11 para geracao do digito verificador
     *    de boletos bancarios conforme documentos obtidos
     *    da Febraban - www.febraban.org.br
     *
     *   Entrada:
     *     $num: string numérica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Saída:
     *     Retorna o Digito verificador.
     *
     *   Observações:
     *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
     */

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
    
    // campo 1
    $banco    = substr($linha, 0, 3);
    $moeda    = substr($linha, 3, 1);
    $ccc      = substr($linha, 19, 3);
    $ddnnum   = substr($linha, 22, 2);
    $dv1      = $this->modulo10($banco.$moeda.$ccc.$ddnnum);
    // campo 2
    $resnnum  = substr($linha, 24, 6);
    $dac1     = substr($linha, 30, 1);//$this->modulo10($agencia.$conta.$carteira.$nnum);
    $dddag    = substr($linha, 31, 3);
    $dv2      = $this->modulo10($resnnum.$dac1.$dddag);
    // campo 3
    $resag    = substr($linha, 34, 1);
    $contadac = substr($linha, 35, 6); //substr($codigo,35,5).$this->modulo10(substr($codigo,35,5));
    $zeros    = substr($linha, 41, 3);
    $dv3      = $this->modulo10($resag.$contadac.$zeros);
    // campo 4
    $dv4      = substr($linha, 4, 1);
    // campo 5
    $fator    = substr($linha, 5, 4);
    $valor    = substr($linha, 9, 10);

    $campo1 = substr($banco.$moeda.$ccc.$ddnnum.$dv1,0,5) . '.' . substr($banco.$moeda.$ccc.$ddnnum.$dv1,5,5);
    $campo2 = substr($resnnum.$dac1.$dddag.$dv2,0,5) . '.' . substr($resnnum.$dac1.$dddag.$dv2,5,6);
    $campo3 = substr($resag.$contadac.$zeros.$dv3,0,5) . '.' . substr($resag.$contadac.$zeros.$dv3,5,6);
    $campo4 = $dv4;
    $campo5 = $fator.$valor;

    return $campo1 . " " . $campo2 . " " . $campo3 . " " . $campo4 . " " . $campo5;
  }

  /**
   * 
   * @param string $numero
   * @return string
   */
  private function geraDigitoVerificarBarra($numero) {
    
    $resto2 = $this->modulo11($numero, 9, 1);
    $digito = 11 - $resto2;
    if ($digito == 0 || $digito == 1 || $digito == 10 || $digito == 11) {
      $dv = 1;
    } else {
      $dv = $digito;
    }

    return $dv;
  }

}
