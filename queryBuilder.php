<?php

namespace myc;

class UtilsQueryBuilder {

  public static function appendBracket($str, $bracket = '`') {
    $out = '';
    $clearStr = trim($str);
    if (strpos($str, '.') !== false) {
      list($tableName, $fieldName) = explode('.', $clearStr);

      $bracketTableName = self::addBracket($tableName, $bracket);
      $bracketFieldName = self::addBracket($fieldName, $bracket);
      $out = $bracketTableName . '.' . $bracketFieldName;
    } else {
      $out = self::addBracket($clearStr, $bracket);
    }

    return $out;
  }

  public static function addBracket($str, $bracket = '`') {
    return $bracket . $str . $bracket;
  }

  public static function toAlias($alias) {
    $clearAlias = trim($alias);
    if (strpos($clearAlias, '.')) {
      $parsedAlias = explode('.', $clearAlias);
      return $parsedAlias[1];
    }

    return $clearAlias;
  }

}

class RawQueryBuilder {

  private $output = null;

  public function getOutput() {
    return '('.$this->output.')';
  }

  private function setOutput($out) {
    $this->output = $out;
  }

  public static function raw($str, $args = []) {
    $pattern = '/(\?\?|\?)/im';
    preg_match_all($pattern, $str, $matchesQuery);
    $replacedArr = [];
    if (count($args) === count($matchesQuery[0])) {
      foreach ($matchesQuery[0] as $key => $value) {
        if ($value == '?') {
          $replacedArr[] = UtilsQueryBuilder::addBracket($args[$key], '\'');
        } else if ($value == '??') {
          $replacedArr[] = $args[$key];
        }
      }
    }

    $fotmattedStr = preg_replace_callback($pattern, function($matches) use (&$replacedArr) {
      return array_shift($replacedArr);
    }, $str);

    $instance = new RawQueryBuilder();
    $instance->setOutput($fotmattedStr);
    return $instance;
  }

}

class QueryBuilder {

  private $selects = [];

  public function select($field, $alias = null) {

    if ($field instanceof RawQueryBuilder) {
      
       $this->selects[] = [$field->getOutput(), UtilsQueryBuilder::toAlias($alias)];
      
    } else {
      $clearField = trim($field);

      // birden fazla select varsa
      if (strpos($clearField, ',') !== false) {
        $allFields = explode(',', $clearField);

        $tmpField = null;
        $tmpAlias = null;
        foreach ($allFields as $parsedField) {
          $clearParsedField = trim(preg_replace('/\s\s+/', ' ', $parsedField));
          // as kullanıldıysa 
          if (strpos($clearParsedField, ' ') !== false) {
            $parsedAliasField = explode(' ', $clearParsedField);

            $tmpField = UtilsQueryBuilder::appendBracket($parsedAliasField[0]);
            if (strpos($tmpField, '*') !== false) {
              $tmpAlias = null;
            } else {
              $tmpAlias = UtilsQueryBuilder::toAlias($parsedAliasField[count($parsedAliasField) - 1]);
            }
          } else {
            $tmpField = UtilsQueryBuilder::appendBracket($clearParsedField);
            if (strpos($tmpField, '*') !== false) {
              $tmpAlias = null;
            } else {
              $tmpAlias = UtilsQueryBuilder::toAlias($clearParsedField);
            }
          }
          $this->selects[] = [$tmpField, $tmpAlias];
        }
      } else {
        if ($alias == null) {
          if (strpos($clearField, '*') !== false) {
            $this->selects[] = [UtilsQueryBuilder::appendBracket($clearField), null];
          } else {
            $this->selects[] = [UtilsQueryBuilder::appendBracket($clearField), UtilsQueryBuilder::toAlias($clearField)];
          }
        } else {
          $this->selects[] = [UtilsQueryBuilder::appendBracket($clearField), UtilsQueryBuilder::toAlias($alias)];
        }
      }
    }

    return $this;
  }
  
  public function distinct($field, $alias = null) {
    $clearField = trim($field);
    
    $tmpField='DISTINCT '.UtilsQueryBuilder::appendBracket($clearField);
    
    if ($alias == null) {
        $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }
  
  public function max($field, $alias = null) {
    $clearField = trim($field);
    
    $tmpField='MAX('.UtilsQueryBuilder::appendBracket($clearField).')';
    
    if ($alias == null) {
        $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }
  
  public function min($field, $alias = null) {
    $clearField = trim($field);
    
    $tmpField='MIN('.UtilsQueryBuilder::appendBracket($clearField).')';
    
    if ($alias == null) {
        $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }
  
  public function sum($field, $alias = null) {
    $clearField = trim($field);
    
    $tmpField='SUM('.UtilsQueryBuilder::appendBracket($clearField).')';
    
    if ($alias == null) {
        $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }
  
  public function avg($field, $alias = null) {
    $clearField = trim($field);
    
    $tmpField='AVG('.UtilsQueryBuilder::appendBracket($clearField).')';
    
    if ($alias == null) {
        $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }
  
  public function count($field, $alias = null) {
    $clearField = trim($field);
    
    $tmpField='COUNT('.UtilsQueryBuilder::appendBracket($clearField).')';
    
    if ($alias == null) {
        $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }

  public function getSelects() {
    return $this->selects;
  }

  public function getQuery() {
    return $this->query;
  }

  public function setQuery($query) {
    $this->query = $query;
  }

  public static function newInstance() {
    return new QueryBuilder;
  }

  public static function raw($str, $args = []) {
    return RawQueryBuilder::raw($str, $args);
  }

}
