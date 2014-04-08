<?php

namespace Nd;

class query {
    protected $nd;
    protected $entity;
    protected $relation;
    protected $isRelation = false;
    protected $predicative = null;
    protected $order = null;

    public function __construct(neodynium $nd) {
        $this->nd = $nd;
        return $this;
    }

    /*
    * select and entity to work
    */
    public function entity($entity_name) {
        $this->entity = $entity_name;
        return $this;
    }

    /*
    *
    */
    public function relation($relation_name, $entity_name) {
        $this->isRelation = true;
        $this->entity = $entity_name;
        $this->relation = $relation_name;
        return $this;
    }

    /*
    *
    */
    public function filterBy($column, $filtertype, $value) {
        $this->predicative = new predicative($column, $filtertype, $this->nd->handler->real_escape_string($value));
        return $this;
    }

    /*
    *
    */
    public function andBy($column, $filtertype, $value) {
        if (is_null($this->predicative)) throw new error("and predicative withour predicate");
        $pred = new predicative($column, $filtertype, $this->nd->handler->real_escape_string($value));
        $this->predicative->logical_and($pred);
        return $this;
    }

    /*
    *
    */
    public function orBy($column, $filtertype, $value) {
        if (is_null($this->predicative)) throw new error("or predicative withour predicate");
        $pred = new predicative($column, $filtertype, $this->nd->handler->real_escape_string($value));
        $this->predicative->logical_or($pred);
        return $this;
    }

    /*
    *
    */
    public function notBy($column, $filtertype, $value) {
        if (is_null($this->predicative)) throw new error("not predicative withour predicate");
        $pred = new predicative($column, $filtertype, $this->nd->handler->real_escape_string($value));
        $this->predicative->logical_not($pred);
        return $this;
    }

    /*
    * refactor needed
    */
    public function orderBy($column, $orderType, $query) {
        $this->order = new order($column, $orderType);
        return $this;
    }


    /*
    *
    */
    public function filter() {
        return $this->predicative;
    }

    /*
    * execute current query
    **/
    public function exec() {
        if ($this->isRelation) {
            $relation = $this->nd->getRelationInfo($this->relation);
            $object_to = $relation["object_to"];
            $field_to = "child";
            $object_from = $relation["object_from"];
            $field_from = "father";
            $relation_name = $this->nd->entityMap($relation["name"]);


            if ($relation["object_to"] == $this->entity) {
                $object_to = $relation["object_from"];
                $object_from = $relation["object_to"];
                $field_to = "father";
                $field_from = "child";
            };


            $fields = $this->nd->getObjectFieldList($object_to);
            $object_to = $this->nd->entityMap($object_to);
            $object_from = $this->nd->entityMap($object_from);

            //generate query
            $query = "SELECT `id` FROM " . $object_from;
            if (!is_null($this->predicative)) $query .= " WHERE " . $this->predicative->generateSQL();

            // create object relation
            $query = "SELECT " . $field_to . " FROM " . $relation_name . " WHERE " . $field_from . " IN (" . $query . ")";
            $query = "SELECT `" . join($fields, '`, `') . "` FROM " . $object_to . " WHERE id IN (" . $query . ")";
        } else {
            $fields = $this->nd->getObjectFieldList($this->entity);
            $query = "SELECT `" . join($fields, '`, `') . "` FROM " . $this->nd->entityMap($this->entity);
            if (!is_null($this->predicative)) $query .= " WHERE " . $this->predicative->generateSQL();
            if (!is_null($this->order)) $query .=  $this->order->generateSQL();
        };
        //echo $query;
        return $this->nd->handler->query($query);
    }
};

?>