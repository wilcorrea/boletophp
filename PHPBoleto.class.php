<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$root = File::getParent(__FILE__);
define('PHPBOLETO_ROOT', $root);

$module = File::getName(PHPBOLETO_ROOT);
define('PHPBOLETO_MODULE', $module);

$path = File::getParent(PHPBOLETO_ROOT);
define('PHPBOLETO_PATH', $path);

$separator = File::getSeparator();
define('PHPBOLETO_SEPARATOR', $separator);

System::import('class', PHPBOLETO_MODULE, 'Template', PHPBOLETO_PATH, true, '');
System::import('class', PHPBOLETO_MODULE, 'Banco', PHPBOLETO_PATH, true, '');
System::import('class', PHPBOLETO_MODULE, 'Boleto', PHPBOLETO_PATH, true, '');
System::import('class', PHPBOLETO_MODULE, 'Cedente', PHPBOLETO_PATH, true, '');
System::import('class', PHPBOLETO_MODULE, 'Sacado', PHPBOLETO_PATH, true, '');
 
/**
 * Description of Boleto
 *
 * @author Analista
 */
class PHPBoleto {

  private static $banks = null;
  private static $styles = array();
  private static $printed = 0;

  /**
   * 
   * @param type $dir
   */
  private static function load($dir) {
    $s = PHPBOLETO_SEPARATOR;
    $filename = $dir . $s . "config" . $s . "settings.json";
    $settings = File::openFile($filename);
    self::$banks = Json::decode($settings);
  }

  /**
   * 
   * @param type $boletos
   */
  public static function write($boletos, $print = false, $debug = false) {
    
    System::import('class', PHPBOLETO_MODULE, 'Boleto', PHPBOLETO_PATH, true, '');

    if (!is_array($boletos)) {
      $boletos = array($boletos);
    }

    if (!self::$banks) {
      self::load(PHPBOLETO_ROOT);
    }

    if (self::$banks) {

      foreach ($boletos as $boleto) {

        $b = $boleto->blt_banco;
        if (isset(self::$banks->$b)) {

          $bank = self::$banks->$b;

          if ($boleto->blt_valor > 0) {

            $template = self::getTemplate($bank);
            self::$styles[$b] = self::getStyle($bank);

            $replaces = self::getReplaces($bank, $boleto);

            $html = System::replaceMarkup($template, $replaces);

            self::show($html);
          } else {
            Console::add("{lib_message_003}", Console::$STATUS_ERROR);
          }
        } else {
          Console::add("{lib_message_002}", Console::$STATUS_ERROR);
        }
      }
      
      self::defineStyles(implode("", self::$styles));
    }

  }
  
  /**
   * 
   * @return string
   */
  public static function getTranslates() {
    
    $translates = array();

    $translates["lib_message_001"] = array("key" => "lib_message_001", "value" => "Atributo não encontrado", "type" => "string");
    $translates["lib_message_002"] = array("key" => "lib_message_002", "value" => "Banco inválido", "type" => "string");
    $translates["lib_message_003"] = array("key" => "lib_message_003", "value" => "Valor incorreto", "type" => "string");

    return $translates;
  }

  /**
   * 
   * @param type $bank
   * @return type
   */
  public static function getStyle($bank) {
    $s = PHPBOLETO_SEPARATOR;
    $file = $bank->css;
    $filename = PHPBOLETO_ROOT . $s . "boleto" . $s . $bank->id . $s . "style" . $s . $file;

    $css = File::openFile($filename);

    return $css;
  }

  /**
   * 
   * @param type $bank
   * @return type
   */
  private static function getTemplate($bank) {

    $s = PHPBOLETO_SEPARATOR;
    $file = $bank->template;
    $filename = PHPBOLETO_ROOT . $s . "boleto" . $s . $bank->id . $s . "template" . $s . $file;

    $template = File::openFile($filename);

    return $template;
  }

  /**
   * 
   * @param Object $object
   * @param Boleto $boleto
   * 
   * @return array
   */
  private static function getReplaces($object, $boleto) {

    self::import($object);

    $class = $object->model;

    $banco = new $class();

    $locate = self::getLocate();
    $lib_page = PAGE_APP . $locate;

    $boleto = $banco->customizaAtributos($boleto);

    $blt_banco_codigo = $banco->geraCodigoBanco($boleto->blt_banco);
    
    $blt_nosso_numero = $banco->geraNossoNumero($boleto);
    $blt_cedente_contrato = $banco->geraCedenteContrato($boleto);
    $linha_digitavel = $banco->geraLinhaDigitavel($boleto);

    $blt_linha_digitavel = $banco->montaLinhaDigitavel($linha_digitavel);
    $blt_codigo_barras = $banco->geraCodigoBarra($linha_digitavel, $lib_page);
    
    $boleto->set_blt_value('blt_banco_codigo', $blt_banco_codigo);
    $boleto->set_blt_value('blt_cedente_contrato', $blt_cedente_contrato);
    $boleto->set_blt_value('blt_nosso_numero', $blt_nosso_numero);
    $boleto->set_blt_value('blt_codigo_barras', $blt_codigo_barras);
    $boleto->set_blt_value('blt_linha_digitavel', $blt_linha_digitavel);

    $replaces = array();

    $replaces[] = array("key" => 'blt_banco', "value" => $boleto->blt_banco, "type" => "int");
    $replaces[] = array("key" => 'blt_valor', "value" => $boleto->blt_valor, "type" => "string");
    $replaces[] = array("key" => 'blt_carteira', "value" => $boleto->blt_carteira, "type" => "string");
    $replaces[] = array("key" => 'blt_quantidade', "value" => $boleto->blt_quantidade, "type" => "string");

    $replaces[] = array("key" => 'lib_page', "value" => $lib_page, "type" => "string");

    $items = $boleto->get_blt_items();
    foreach ($items as $id => $item) {
      $replaces[] = array("key" => $id, "value" => $item['value'], "type" => $item['type']);
    }

    return $replaces;
  }

  /**
   * 
   * @param type $html
   */
  private static function show($html) {
    if (self::$printed === 0) {
      print '<style>.phpboleto-break-page{ page-break-after:always; }</style>';
    } else {
      print '<div class="phpboleto-break-page">&nbsp;</div>';
    }
    self::$printed++;
    print $html;
  }

  /**
   * 
   * @param type $style
   */
  private static function defineStyles($style) {
    print '<style>';
    print $style;
    print '</style>';
  }
  
  /**
   * 
   * @return type
   */
  private static function getLocate() {
    return String::replace(PHPBOLETO_ROOT, PATH_APP, "");
  }

  /**
   * 
   * @param type $bank
   */
  private static function import($bank) {
    $s = PHPBOLETO_SEPARATOR;
    $path = PHPBOLETO_ROOT . $s . "boleto" . $s . $bank->id;
    System::import('class', 'model', $bank->model, $path, true, '');
  }

}
