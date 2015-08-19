<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Banco
 *
 * @author Analista
 */
abstract class Banco extends Template {
  
  public $blt_modalidade_cobranca;
  public $blt_numero_parcela;
  public $blt_carteira;
  public $blt_moeda;
  public $blt_quantidade;
  public $blt_valor_unitario;
  public $blt_aceite;
  public $blt_especie;
  public $blt_especie_documento;
  

  /**
   * 
   * @param Boleto $boleto
   * @return string
   */
  public abstract function geraNossoNumero(Boleto $boleto);

  /**
   * 
   * @param Boleto $boleto
   * @return string
   */
  public abstract function geraLinhaDigitavel(Boleto $boleto);

  /**
   * 
   * @param Boleto $boleto
   * @return string
   */
  public abstract function geraCedenteContrato(Boleto $boleto);

  /**
   * 
   * @param string $value
   * @return string
   */
  public abstract function modulo10($value);

  /**
   * 
   * @param string $value
   * @param int $base
   * @param int $r
   * 
   * @return string
   */
  public abstract function modulo11($value, $base = 9, $r = 0);

  /**
   * 
   * @param string $value
   * @return string
   */
  public abstract function montaLinhaDigitavel($value);

}
