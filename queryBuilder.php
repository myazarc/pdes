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
    return '(' . $this->output . ')';
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
  private $tables = [];
  private $wheres = [];
  private $whereDelimiterIndex = -1;

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

    $tmpField = 'DISTINCT ' . UtilsQueryBuilder::appendBracket($clearField);

    if ($alias == null) {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }

  public function max($field, $alias = null) {
    $clearField = trim($field);

    $tmpField = 'MAX(' . UtilsQueryBuilder::appendBracket($clearField) . ')';

    if ($alias == null) {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }

  public function min($field, $alias = null) {
    $clearField = trim($field);

    $tmpField = 'MIN(' . UtilsQueryBuilder::appendBracket($clearField) . ')';

    if ($alias == null) {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }

  public function sum($field, $alias = null) {
    $clearField = trim($field);

    $tmpField = 'SUM(' . UtilsQueryBuilder::appendBracket($clearField) . ')';

    if ($alias == null) {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }

  public function avg($field, $alias = null) {
    $clearField = trim($field);

    $tmpField = 'AVG(' . UtilsQueryBuilder::appendBracket($clearField) . ')';

    if ($alias == null) {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($clearField)];
    } else {
      $this->selects[] = [$tmpField, UtilsQueryBuilder::toAlias($alias)];
    }
    return $this;
  }

  public function count($field, $alias = null) {
    $clearField = trim($field);

    $tmpField = 'COUNT(' . UtilsQueryBuilder::appendBracket($clearField) . ')';

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

  public function getWheres() {
    return $this->wheres;
  }

  public function from($tableName, $alias = null) {

    if ($tableName instanceof RawQueryBuilder) {

      $this->tables[] = [$tableName->getOutput(), UtilsQueryBuilder::toAlias($alias)];
    } else {
      $clearTableName = trim($tableName);

      $tmpTableName = UtilsQueryBuilder::appendBracket($clearTableName);

      if ($alias == null) {
        $this->tables[] = [$tmpTableName, null];
      } else {
        $this->tables[] = [$tmpTableName, UtilsQueryBuilder::toAlias($alias)];
      }
    }
    return $this;
  }

  public function where($field, $value = null, $op = '=', $type = 'and') {

    if ($field instanceof RawQueryBuilder) {
      $row = [$field->getOutput(), null, null, $type];

      if ($this->whereDelimiterIndex > -1) {
        $this->wheres[$this->whereDelimiterIndex][] = $row;
      } else {
        $this->wheres[] = [$row];
      }
    } else {
      $tmpField = UtilsQueryBuilder::appendBracket($field);

      $row = [$type, $tmpField, $value, $op];
      if ($this->whereDelimiterIndex > -1) {
        $this->wheres[$this->whereDelimiterIndex][] = $row;
      } else {
        $this->wheres[] = [$row];
      }
    }

    return $this;
  }

  public function whereGroup(callable $func) {
    if (is_callable($func)) {
      $this->whereDelimiterIndex = count($this->wheres);
      $func($this);
      $this->whereDelimiterIndex = -1;
    }
    return $this;
  }

  public function whereOr($field, $value=null, $op = '=') {
    $this->where($field, $value, $op, 'or');
    return $this;
  }
  
  public function whereBetween($field, $value1, $value2) {
    $this->where($field, [$value1,$value2], 'between', 'and');
    return $this;
  }
  
  public function whereBetweenOr($field, $value1, $value2) {
    $this->where($field, [$value1,$value2], 'between', 'or');
    return $this;
  }
  
  public function whereIn($field,array $values) {
    $this->where($field, $values, 'in', 'and');
    return $this;
  }
  
  public function whereInOr($field, $values) {
    $this->where($field, $values, 'in', 'or');
    return $this;
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
