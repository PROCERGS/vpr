<?php

namespace PROCERGS\VPR\CoreBundle\Helper;

class Utils {

  public static function formatPercentageNumber($value, $decimals = 2) {
    return number_format($value, $decimals);
  }

  public static function formatCurrencyNumber($value) {
    return number_format($value, 2, ",", ".");
  }

  public static function humanizeCurrencyNumber($value) {
    // is this a number?
    if(!is_numeric($value)) return false;
    
    // filtering
    if($value>1000000000000) return intval($value/1000000000000).' Tri';
    else if($value>=1000000000) return intval($value/1000000000).' Bi';
    else if($value>=1000000) return intval($value/1000000).' Mi';
    else if($value>=1000) return intval($value/1000).' mil';
    return number_format($value);
  }

  public static function formatFirstLetterMonth($monthNumber) {
    $months = array('J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D');
    return $months[$monthNumber-1];
  }

  public static function colorByQuantity($quantity, $maxQuantity) {
    $percent = ($maxQuantity != 0) ? (($quantity/$maxQuantity) * 100) : 0;
    if($percent < 5) return "#f6b7a5";
    else if($percent >= 5.00 && $percent < 10.00) return "#f2a186";
    else if($percent >= 10.00 && $percent < 25.00) return "#eb7148";
    else if($percent >= 25.00 && $percent < 50.00) return "#e75927";
    else if($percent >= 50.00 && $percent < 80.00) return "#d8490f";
    else if($percent >= 80.00) return "#bb3e0e";
  }

  public static function sizeByAmount($amount, $maxAmount) {
    $percent = ($maxAmount != 0) ? (($amount/$maxAmount) * 100) : 0;
    if($percent < 5) return 1;
    else if($percent >= 5.00 && $percent < 10.00) return 2;
    else if($percent >= 10.00 && $percent < 25.00) return 3;
    else if($percent >= 25.00 && $percent < 50.00) return 4;
    else if($percent >= 50.00 && $percent < 80.00) return 5;
    else if($percent >= 80.00) return 6;
  }

  public static function formatAnoMes($ano, $mes) {
    return $ano.str_pad($mes, 2, "0", STR_PAD_LEFT);
  }

  public static function formatMes($mes) {
    return str_pad($mes, 2, "0", STR_PAD_LEFT);
  }

  public static function humanizeYearMonth($params) {
    if(!empty($params['mes']) && !empty($params['ano'])){
      return self::getAbbrMonthName($params['mes']).'/'.$params['ano'];
    } elseif (!empty($params['mes']) && empty($params['ano'])){
      return self::getAbbrMonthName($params['mes']);
    } elseif (empty($params['mes']) && !empty($params['ano'])){
      return $params['ano'];
    } else {
      return "";
    }
  }

  public static function arraySortByColumn(&$array, $column, $sortType = 'asc') {
    $sortArray = array();

    foreach($array as $item) {
      foreach($item as $key=>$value) {
        if(!isset($sortArray[$key])) $sortArray[$key] = array();
        $sortArray[$key][] = $value;
      }
    }
    $direction = ($sortType == 'asc') ? SORT_ASC : SORT_DESC;

    if(!empty($sortArray)) array_multisort($sortArray[$column], $direction, $array);
  }

  public static function getLastMonthName() {
    return self::getMonthName((date('m')-1 == 0) ? 12 : date('m')-1);
  }

  public static function getAbbrMonthName($month) {
    return substr(self::getMonthName($month), 0, 3);
  }

  public static function getMonthName($month) {
    switch ($month) {
      case 1:  $monthName = 'Janeiro';     break;
      case 2:  $monthName = 'Fevereiro';   break;
      case 3:  $monthName = 'Março';       break;
      case 4:  $monthName = 'Abril';       break;
      case 5:  $monthName = 'Maio';        break;
      case 6:  $monthName = 'Junho';       break;
      case 7:  $monthName = 'Julho';       break;
      case 8:  $monthName = 'Agosto';      break;
      case 9:  $monthName = 'Setembro';    break;
      case 10: $monthName = 'Outubro';     break;
      case 11: $monthName = 'Novembro';    break;
      case 12: $monthName = 'Dezembro';    break; 
    } 
    return $monthName;
  }
  
  private static function toCssInline($htmlFile)
  {
      $dom = new \DOMDocument();
      $b = file_get_contents($htmlFile);
      $a  = $dom->loadHTML($b);
      $css = str_replace(array("\r","\n", "\t"), array(' ',' ', ' '), $dom->getElementsByTagName('style')->item(0)->nodeValue);
      $c = preg_match_all('/\.([a-zA-Z0-9]{0,})[\\ ]{0,}\{([a-zA-Z0-9:\.\"\\\ ;#-]+)\}/', $css, $mat);
      foreach (array('p', 'body', 'table', 'tr', 'td', 'span', 'a') as $tag) {
          foreach ($dom->getElementsByTagName($tag) as $node) {
              if ($node->hasAttributes()) {
                  $attr = $node->attributes->getNamedItem('class');
                  if ($attr) {
                      $style = $node->attributes->getNamedItem('style');
                      if ($style === null) {
                          $style = '';
                      }
                      foreach (explode(' ', $attr->value) as $class) {
                          foreach ($mat[1] as $idx => $selector) {
                              if ($class == $selector) {
                                  if ($style != '') {
                                      $style .= ';';
                                  }
                                  $style .= trim($mat[2][$idx]);
                                  break;
                              }
                          }
                      }
                      if ($style == '') {
                          $break = null;
                      }
                      $node->removeAttribute('class');
      
                      $node->setAttribute("style", $style);
                  }
              }
          }
      }
      return $dom->saveHTML();      
  }
}