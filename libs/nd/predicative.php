<?php

namespace Nd;

class predicative {
    protected $column = "";
    protected $filterType = "";
    protected $filterValue = "";
    protected $negate = null;
    protected $and_others = array();
    protected $or_others = array();
    protected $sort_fields = array();

    public function __construct($column, $filterType, $filterValue) {
        $this->column = $column;
        $this->filterType = $filterType;
        $this->filterValue = $filterValue;
    }

    public function generateSQL() {
        $query = $this->filterToSql();
        $and_array = array();
        $and_query = "";
        $or_array = array();
        $or_query = "";

        foreach ($this->and_others as $pred) {
            array_push($and_array, $pred->generateSQL());
        };
        $and_query = join($and_array, " AND ");

        foreach ($this->or_others as $pred) {
            array_push($or_array, $pred->generateSQL());
        };
        $or_query = join($or_array, " OR ");

        $query = "(" . $query . ")";
        if ($and_query) $query .= " AND (" . $and_query . ")";
        if ($or_query) $query .= " OR (" . $or_query . ")";
        if (!is_null($this->negate)) $query .= " AND NOT (" . $this->negate->generateSQL() . ")";
        $query = $query . $this->orderToSql();
        return $query;
    }

    protected function filterToSql() {
        $query = $this->column;
        switch ($this->filterType) {
            case "startWith":
                $query .= " LIKE '" . $this->filterValue . "%'";
                return $query; //<-------- special case, REFACTOR!
                break;
            case "endsWith":
                $query .= " LIKE '%";
                break;
            case "like":
                  $query .=  " LIKE '%";
                break;
            case "equal":
                  $query .= " LIKE '%";
                break;
            case "=":
                $query .= is_numeric($this->filterValue) ? " = " : " LIKE '";
                break;
            default:
                $query .= $this->filterType;
                break;
        };
        $query .= $this->filterValue;
        if (!is_numeric($this->filterValue)) $query .= "'";
        return $query;
    }

    protected function orderToSql() {
        if (!count($this->sort_fields)) return "";

        $query = "ORDER BY ";
        $sortitems = array();

        foreach ($this->sort_fields as $value) {
            array_push($sortitems, "`" . $value["field"] . "`" . " " . ($value["order"] ? "ASC" : "DESC"));
        }
        return $query . join($sortitems, ", ");
    }

    public function logical_and(predicative $other) {
        array_push($this->and_others, $other);
    }

    public function logical_or(predicative $other) {
        array_push($this->or_others, $other);
    }

    public function logical_not(predicative $other) {
        $this->negate = $other;
    }

    public function sortBy($field, $asc = true) {
        $sortvalue = array();
        $sortvalue["field"] = $field;
        $sortvalue["order"] = $asc ? true : false;
        array_push($this->sort_fields, $sortvalue);
    }
};

?>