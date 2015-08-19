<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cedente
 *
 * @author Analista
 */
class Cedente {
  
  private $cdn_items = array();
 
  /**
   * 
   * @return Cedente
   */
  public function Cedente($cdn_cedente_agencia, $cdn_cedente_conta, $cdn_cedente_contrato) {
  
    if (!$cdn_cedente_agencia || !$cdn_cedente_conta) {
      Console::add("Dados incorretos no cadastro do cedente => cdn_cedente_contrato: " . $cdn_cedente_contrato . ", cdn_cedente_agencia: " . $cdn_cedente_agencia . ", cdn_cedente_conta: " . $cdn_cedente_conta . "");
    }

    $this->cdn_items['cdn_cedente_contrato'] = array("id"=>'cdn_cedente_contrato', "value"=>$cdn_cedente_contrato, "type"=>"string");
    $this->cdn_items['cdn_cedente_agencia'] = array("id"=>'cdn_cedente_agencia', "value"=>$cdn_cedente_agencia, "type"=>"string");
    $this->cdn_items['cdn_cedente_conta'] = array("id"=>'cdn_cedente_conta', "value"=>$cdn_cedente_conta, "type"=>"string");

    $this->cdn_items['cdn_cedente_conta_dv'] = array("id"=>'cdn_cedente_conta_dv', "value"=>"", "type"=>"string");
    $this->cdn_items['cdn_cedente_nome'] = array("id"=>'cdn_cedente_nome', "value"=>"", "type"=>"string");
    $this->cdn_items['cdn_cedente_cnpj'] = array("id"=>'cdn_cedente_cnpj', "value"=>"", "type"=>"string");
    $this->cdn_items['cdn_cedente_endereco'] = array("id"=>'cdn_cedente_endereco', "value"=>"", "type"=>"string");
    $this->cdn_items['cdn_cedente_bairro'] = array("id"=>'cdn_cedente_bairro', "value"=>"", "type"=>"string");
    $this->cdn_items['cdn_cedente_cidade'] = array("id"=>'cdn_cedente_cidade', "value"=>"", "type"=>"string");
    $this->cdn_items['cdn_cedente_uf'] = array("id"=>'cdn_cedente_uf', "value"=>"", "type"=>"string");
    $this->cdn_items['cdn_cedente_cep'] = array("id"=>'cdn_cedente_cep', "value"=>"", "type"=>"string");
    
    return $this;
  }
  
  /**
   * 
   * @param string $id
   * @param string $value
   * 
   * @return boolean
   */
  public function set_cdn_value($id, $value) {
    $set = false;
    if (isset($this->cdn_items[$id])) {
      $set = $this->cdn_items[$id]['value'] = $value;
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
  public function get_cdn_value($id) {
    $get = false;
    if (isset($this->cdn_items[$id])) {
      $get = $this->cdn_items[$id]['value'];
    } else {
      Console::add("{lib_message_001}: " . $id, Console::$STATUS_ERROR);
    }
    return $get;
  }
  
}
