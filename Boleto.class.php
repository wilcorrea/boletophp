<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Boleto
 *
 * @author Analista
 */
class Boleto {
  
  public $blt_banco = "";
  public $blt_valor = 0.00;

  public $blt_modalidade_cobranca = "";
  public $blt_numero_parcela = "";
  public $blt_carteira = "";
  public $blt_moeda = "";
  public $blt_quantidade = "";
  public $blt_valor_unitario = "";
  public $blt_aceite = "";
  public $blt_especie = "";
  public $blt_especie_documento = "";
     
  private $blt_items = array();
  
  /**
   * 
   * @param Cedente $cedente
   * 
   * @param Sacado $sacado
   * 
   * @param string $blt_banco
   * @param double $blt_valor
   * @param string $blt_numero_documento
   * 
   * @param string $blt_parcela
   * @param string $blt_descricao
   * 
   * @param date $blt_data_vencimento
   * @param date $blt_data_documento
   * @param date $blt_data_processamento
   * 
   * @param double $blt_juros_valor
   * @param double $blt_multa_valor
   * @param double $blt_desconto_valor
   * @param double $blt_abatimento_valor
   * 
   * return Boleto
   */
  public function Boleto(Cedente $cedente, Sacado $sacado, $blt_banco, $blt_valor, $blt_numero_documento, $blt_parcela = "1/1", $blt_descricao = "", $blt_data_vencimento = CURDATE, $blt_data_documento = CURDATE, $blt_data_processamento = CURDATE, $blt_juros_valor = 0, $blt_multa_valor = 0, $blt_desconto_valor = 0, $blt_abatimento_valor = 0) {

    $this->blt_banco = $blt_banco;
    $this->blt_valor = String::replace(Number::format($blt_valor), ".", "");

    $blt_cedente_contrato = "";
    $blt_cedente_agencia = "";
    $blt_cedente_conta = "";
    $blt_cedente_conta_dv = "";
    $blt_cedente_nome = "";
    $blt_cedente_cnpj = "";
    $blt_cedente_endereco = "";
    $blt_cedente_bairro = "";
    $blt_cedente_cidade = "";
    $blt_cedente_uf = "";
    $blt_cedente_cep = "";

    $blt_sacado_nome = "";
    $blt_sacado_endereco = "";
    $blt_sacado_bairro = "";
    $blt_sacado_cidade = "";
    $blt_sacado_uf = "";
    $blt_sacado_cep = "";

    if (!is_null($cedente)) {
      
      $blt_cedente_contrato = $cedente->get_cdn_value('cdn_cedente_contrato');
      $blt_cedente_agencia = $cedente->get_cdn_value('cdn_cedente_agencia');
      $blt_cedente_conta = $cedente->get_cdn_value('cdn_cedente_conta');
      $blt_cedente_conta_dv = $cedente->get_cdn_value('cdn_cedente_conta_dv');
      $blt_cedente_nome = $cedente->get_cdn_value('cdn_cedente_nome');
      $blt_cedente_cnpj = $cedente->get_cdn_value('cdn_cedente_cnpj');
      $blt_cedente_endereco = $cedente->get_cdn_value('cdn_cedente_endereco');
      $blt_cedente_bairro = $cedente->get_cdn_value('cdn_cedente_bairro');
      $blt_cedente_cidade = $cedente->get_cdn_value('cdn_cedente_cidade');
      $blt_cedente_uf = $cedente->get_cdn_value('cdn_cedente_uf');
      $blt_cedente_cep = $cedente->get_cdn_value('cdn_cedente_cep');

    } else {
      
      Console::add("Cedente incorreto");

    }

    if (!is_null($sacado)) {
      
      $blt_sacado_nome = $sacado->get_scd_value('scd_sacado_nome');
      $blt_sacado_endereco = $sacado->get_scd_value('scd_sacado_endereco');
      $blt_sacado_bairro = $sacado->get_scd_value('scd_sacado_bairro');
      $blt_sacado_cidade = $sacado->get_scd_value('scd_sacado_cidade');
      $blt_sacado_uf = $sacado->get_scd_value('scd_sacado_uf');
      $blt_sacado_cep = $sacado->get_scd_value('scd_sacado_cep');

    } else {
      
      Console::add("Sacado incorreto");

    }

    $this->blt_items['blt_cedente_contrato'] = array("id"=>'blt_cedente_contrato', "value"=>$blt_cedente_contrato, "type"=>"string");
    $this->blt_items['blt_cedente_agencia'] = array("id"=>'blt_cedente_agencia', "value"=>$blt_cedente_agencia, "type"=>"string");
    $this->blt_items['blt_cedente_conta'] = array("id"=>'blt_cedente_conta', "value"=>$blt_cedente_conta, "type"=>"string");
    $this->blt_items['blt_cedente_conta_dv'] = array("id"=>'blt_cedente_conta_dv', "value"=>$blt_cedente_conta_dv, "type"=>"string");
    $this->blt_items['blt_cedente_agencia_codigo'] = array("id"=>'blt_cedente_agencia_codigo', "value"=>"", "type"=>"string");

    $this->blt_items['blt_cedente_nome'] = array("id"=>'blt_cedente_nome', "value"=>$blt_cedente_nome, "type"=>"string");
    $this->blt_items['blt_cedente_cnpj'] = array("id"=>'blt_cedente_cnpj', "value"=>$blt_cedente_cnpj, "type"=>"cnpj");
    $this->blt_items['blt_cedente_endereco'] = array("id"=>'blt_cedente_endereco', "value"=>$blt_cedente_endereco, "type"=>"string");
    $this->blt_items['blt_cedente_bairro'] = array("id"=>'blt_cedente_bairro', "value"=>$blt_cedente_bairro, "type"=>"string");
    $this->blt_items['blt_cedente_cidade'] = array("id"=>'blt_cedente_cidade', "value"=>$blt_cedente_cidade, "type"=>"string");
    $this->blt_items['blt_cedente_uf'] = array("id"=>'blt_cedente_uf', "value"=>$blt_cedente_uf, "type"=>"string");
    $this->blt_items['blt_cedente_cep'] = array("id"=>'blt_cendete_cep', "value"=>$blt_cedente_cep, "type"=>"cep");

    $this->blt_items['blt_sacado_nome'] = array("id"=>'blt_sacado_nome', "value"=>$blt_sacado_nome, "type"=>"string");
    $this->blt_items['blt_sacado_endereco'] = array("id"=>'blt_sacado_endereco', "value"=>$blt_sacado_endereco, "type"=>"string");
    $this->blt_items['blt_sacado_bairro'] = array("id"=>'blt_sacado_bairro', "value"=>$blt_sacado_bairro, "type"=>"string");
    $this->blt_items['blt_sacado_cidade'] = array("id"=>'blt_sacado_cidade', "value"=>$blt_sacado_cidade, "type"=>"string");
    $this->blt_items['blt_sacado_uf'] = array("id"=>'blt_sacado_uf', "value"=>$blt_sacado_uf, "type"=>"string");
    $this->blt_items['blt_sacado_cep'] = array("id"=>'blt_sacado_cep', "value"=>$blt_sacado_cep, "type"=>"cep");

    $this->blt_items['blt_banco_codigo'] = array("id"=>'blt_banco_codigo', "value"=>"", "type"=>"string");
    $this->blt_items['blt_nosso_numero'] = array("id"=>'blt_nosso_numero', "value"=>"", "type"=>"string");
    $this->blt_items['blt_linha_digitavel'] = array("id"=>'blt_linha_digitavel', "value"=>"", "type"=>"string");
    $this->blt_items['blt_codigo_barras'] = array("id"=>'blt_codigo_barras', "value"=>"", "type"=>"string");
    $this->blt_items['blt_dias_vencimento'] = array("id"=>'blt_dias_vencimento', "value"=>10, "type"=>"int");

    $this->blt_items['blt_numero_documento'] = array("id"=>'blt_numero_documento', "value"=>$blt_numero_documento, "type"=>"string");

    $this->blt_items['blt_parcela'] = array("id"=>'blt_parcela', "value"=>$blt_parcela, "type"=>"string");
    $this->blt_items['blt_descricao'] = array("id"=>'blt_descricao', "value"=>$blt_descricao, "type"=>"string");
    $this->blt_items['blt_juros_valor'] = array("id"=>'blt_juros_valor', "value"=>$blt_juros_valor, "type"=>"percent");
    $this->blt_items['blt_multa_valor'] = array("id"=>'blt_multa_valor', "value"=>$blt_multa_valor, "type"=>"percent");
    $this->blt_items['blt_desconto_valor'] = array("id"=>'blt_desconto_valor', "value"=>$blt_desconto_valor, "type"=>"money");
    $this->blt_items['blt_abatimento_valor'] = array("id"=>'blt_abatimento_valor', "value"=>$blt_abatimento_valor, "type"=>"money");

    $this->blt_items['blt_data_documento'] = array("id"=>'blt_data_documento', "value"=>$blt_data_documento, "type"=>"date");
    $this->blt_items['blt_data_processamento'] = array("id"=>'blt_data_processamento', "value"=>$blt_data_processamento, "type"=>"date");
    $this->blt_items['blt_data_vencimento'] = array("id"=>'blt_data_vencimento', "value"=>$blt_data_vencimento, "type"=>"date");

    return $this;
  }
  
  /**
   * 
   * @param string $id
   * @param string $value
   * 
   * @return boolean
   */
  public function set_blt_value($id, $value) {
    $set = false;
    if (isset($this->blt_items[$id])) {
      $set = $this->blt_items[$id]['value'] = $value;
    } else {
      Console::add("{lib_message_001}: " . $id, Console::$STATUS_ERROR);
    }
    return $set;
  }
  
  /**
   * 
   * @param string $id
   * 
   * @return mixed
   */
  public function get_blt_value($id) {
    $get = false;
    if (isset($this->blt_items[$id])) {
      $get = $this->blt_items[$id]['value'];
    } else {
      Console::add("{lib_message_001}: " . $id, Console::$STATUS_ERROR);
    }
    return $get;
  }
  
  /**
   * 
   * @return type
   */
  public function get_blt_items() {
    return $this->blt_items;
  }

}
