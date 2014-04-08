<?php

namespace Nd;

class orderBy {
    protected $query = "";
    protected $column = "";
    protected $orderType = "";
    
    public function __construct($column, $orderType, $query) {
        $this->column = $column;
        $this->orderType = $orderType;
        $this->query = $query;

    }

    public function generateSQL() {
        $query = $this->OrderToSql();
        $query = "(" . $query . ")";
        return $query;
    }

   protected function OrderToSql() {
        $query =  $this->query;
        switch ($this->orderType) {
            case "ASC":
                $query .= " ORDER BY '" . $this->column . "ASC'";
                return $query; 
                break;
            case "DESC":
                $query .= " ORDER BY '" . $this->column . "DESC'";
                return $query; 
                break;
           
            default:
                $query .= $this->OrderType;
                break;
        };
        return $query;
    }

};

?>