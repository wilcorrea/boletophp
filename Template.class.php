<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Template
 *
 * @author Analista
 */
class Template {

  /**
   * 
   * @param type $numero
   * @param type $loop
   * @param type $insert
   * @param type $tipo
   * 
   * @return string
   */
  public function formataNumero($numero, $loop, $insert, $tipo = "geral") {

    if ($tipo == "geral") {
      $n = String::replace($numero, ",", "");
      $numero = String::replace($n, ".", "");
      while (strlen($numero) < $loop) {
        $numero = $insert . $numero;
      }
    }

    if ($tipo == "valor") {
      /*
        retira as virgulas
        formata o numero
        preenche com zeros
       */
      $numero = String::replace($numero, ",", "");
      while (strlen($numero) < $loop) {
        $numero = $insert . $numero;
      }
    }

    if ($tipo == "convenio") {
      while (strlen($numero) < $loop) {
        $numero = $numero . $insert;
      }
    }

    return $numero;
  }
  
  /**
   * 
   * @param $boleto
   */
  public function customizaAtributos(Boleto $boleto) {
    return $boleto;
  }

  /**
   * 
   * @param type $numero
   * @return type
   */
  public function geraCodigoBanco($numero) {
    $parte1 = substr($numero, 0, 3);
    $parte2 = $this->modulo11($parte1);
    return $parte1 . "-" . $parte2;
  }

  /**
   * 
   * @param type $blt_linha_digitavel
   * @return string
   */
  public function geraCodigoBarra($valor, $lib_page) {

    $linhas = array();

    $fino = 1;
    $largo = 3;
    $altura = 50;
    
    $barcodes[0] = "00110" ;
    $barcodes[1] = "10001";
    $barcodes[2] = "01001";
    $barcodes[3] = "11000";
    $barcodes[4] = "00101";
    $barcodes[5] = "10100";
    $barcodes[6] = "01100";
    $barcodes[7] = "00011";
    $barcodes[8] = "10010";
    $barcodes[9] = "01010";
    for ($f1 = 9; $f1 >= 0; $f1--) {
      for ($f2 = 9; $f2 >= 0; $f2--) {
        $f = ($f1 * 10) + $f2;
        $texto = "";
        for ($i = 1; $i < 6; $i++) {
          $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
        }
        $barcodes[$f] = $texto;
      }
    }

    $linhas[] = '<img src="{lib_page}/images/p.png" width="' . $fino . '" height="' . $altura . '" border="0">';
    $linhas[] = '<img src="{lib_page}/images/b.png" width="' . $fino . '" height="' . $altura . '" border="0">';
    $linhas[] = '<img src="{lib_page}/images/p.png" width="' . $fino . '" height="' . $altura . '" border="0">';
    $linhas[] = '<img src="{lib_page}/images/b.png" width="' . $fino . '" height="' . $altura . '" border="0">';

    $texto = $valor;
    if ((strlen($texto) % 2) <> 0) {
      $texto = "0" . $texto;
    }

    while (strlen($texto) > 0) {

      $i = round($this->esquerda($texto, 2));
      $texto = $this->direita($texto, strlen($texto) - 2);
      $f = $barcodes[$i];
      for ($i = 1; $i < 11; $i += 2) {
        if (substr($f, ($i - 1), 1) == "0") {
          $f1 = $fino ;
        } else {
          $f1 = $largo ;
        }
        $linhas[] = '<img src="{lib_page}/images/p.png" width="' . $f1 . '" height="' . $altura . '" border="0">';

        if (substr($f,$i,1) == "0") {
          $f2 = $fino ;
        }else{
          $f2 = $largo ;
        }

        $linhas[] = '<img src="{lib_page}/images/b.png" width="' . $f2 . '" height="' . $altura . '" border="0">';
      }

    }

    $linhas[] = '<img src="{lib_page}/images/p.png" width="' . $largo . '" height="' . $altura . '" border="0">';
    $linhas[] = '<img src="{lib_page}/images/b.png" width="' . $fino . '" height="' . $altura . '" border="0">';
    $linhas[] = '<img src="{lib_page}/images/p.png" width="1" height="' . $altura . '" border="0">';

    //print "[" . count($linhas) . "]";

    $barra = implode("", $linhas);

    return String::replace($barra, "{lib_page}", $lib_page);
  }

  /**
   * 
   * @param $entra
   * @param $comp
   */ 
  private function esquerda($entra, $comp) {
  	return substr($entra, 0, $comp);
  }

  /**
   * 
   * @param $entra
   * @param $comp
   */ 
  private function direita($entra, $comp) {
  	return substr($entra, strlen($entra) - $comp, $comp);
  }

  /**
   * 
   * @param type $data
   * @return type
   */
  public function geraFatorVencimento($data) {

    $fator = "0000";
    $datas = explode("/",$data);

    if (count($datas) === 3) {

    	$ano = $datas[2];
    	$mes = $datas[1];
    	$dia = $datas[0];
      
      $fator = (abs(($this->dateToDays("1997", "10", "07")) - ($this->dateToDays($ano, $mes, $dia))));
    }

    return $fator;
  }

  /**
   * 
   * @param type $year
   * @param type $month
   * @param type $day
   * @return type
   */
  private function dateToDays($year, $month, $day) {

    $century = substr($year, 0, 2);
    $year = substr($year, 2, 2);
    if ($month > 2) {
      $month -= 3;
    } else {
      $month += 9;
      if ($year) {
        $year--;
      } else {
        $year = 99;
        $century--;
      }
    }
    return ( floor(( 146097 * $century) / 4) +
      floor(( 1461 * $year) / 4) +
      floor(( 153 * $month + 2) / 5) +
      $day + 1721119);
  }

}
