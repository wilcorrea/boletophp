<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sacado
 *
 * @author Analista
 */
class Sacado {
  
  private $scd_items = array();

  /**
   * 
   * @return \Sacado
   */
  public function Sacado() {
    
    $this->scd_items['scd_sacado_nome'] = array("id" => 'cd_sacado_nome', "value" => "", "type" => "string");
    $this->scd_items['scd_sacado_endereco'] = array("id" => 'cd_sacado_endereco', "value" => "", "type" => "string");
    $this->scd_items['scd_sacado_bairro'] = array("id" => 'cd_sacado_bairro', "value" => "", "type" => "string");
    $this->scd_items['scd_sacado_cidade'] = array("id" => 'cd_sacado_cidade', "value" => "", "type" => "string");
    $this->scd_items['scd_sacado_uf'] = array("id" => 'cd_sacado_uf', "value" => "", "type" => "string");
    $this->scd_items['scd_sacado_cep'] = array("id" => 'cd_sacado_cep', "value" => "", "type" => "string");

    return $this;
  }
  /**
   * 
   * @param string $id
   * @param string $value
   * 
   * @return boolean
   */
  public function set_scd_value($id, $value) {
    $set = false;
    if (isset($this->scd_items[$id])) {
      $set = $this->scd_items[$id]['value'] = $value;
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
  public function get_scd_value($id) {
    $get = false;
    if (isset($this->scd_items[$id])) {
      $get = $this->scd_items[$id]['value'];
    } else {
      Console::add("{lib_message_001}: " . $id, Console::$STATUS_ERROR);
    }
    return $get;
  }
  
}
