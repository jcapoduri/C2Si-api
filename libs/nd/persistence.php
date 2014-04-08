<?php

namespace Nd;

require_once 'error.php';

class persistence {
    protected $nd;
    protected $entities;
    protected $handler;

    static protected $basis = 'CREATE TABLE IF NOT EXISTS `basic` (
                                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                  `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
                                  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  `mtime` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
                                  `dtime` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
                                  PRIMARY KEY (`id`),
                                  KEY `deleted` (`deleted`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';

    static protected $basisrel = 'CREATE TABLE IF NOT EXISTS `basic_rel` (
                                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                  `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
                                  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  `mtime` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
                                  `dtime` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
                                  `father` int(11) NOT NULL,
                                  `child` int(11) NOT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `deleted` (`deleted`),
                                  KEY `father` (`father`,`child`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';

    public function __construct (neodynium $system) {
        $this->nd = $system;
        $this->entities = array();
        $this->handler = $this->nd->handler;
    }

    public function generateJSON($systemname, $appname) {
        $json_generated = array(
                "meta" => array(),
                "config" => array(),
                "objects" => array(),
                "relations" => array(),
                "storages" => array(),
                "apps" => array(),
            );

        //get all tables

        return json_encode($json_generated);
    }

    public function generatePersistence() {
        //start a transaction
        $this->nd->handler->autocommit(false);
        $this->generateBasis();

        $handler = $this->handler->query('SHOW TABLES');
        while ($entity = $handler->fetch_assoc()) array_push($this->entities, $entity);

        //check for all objects;
        $objects = $this->nd->getObjectList();
        $keys = array_keys($objects);
        $len = count($keys);

        for ($i = 0; $i < $len; $i++) {
            $object = $objects[$keys[$i]];
            //var_dump($object);
            $mapname = $this->nd->entityMap($keys[$i]);
            if (isset($this->entities[$mapname])) {
                $this->generateEntity($mapname, $object);
            } else {
                $this->updateEntity($mapname, $object);
            };
        };

        if ($this->handler->commit()) {
            //all ok
            return true;
        } else {
            //fail
        };
        $this->handler->autocommit(true);
    }

    protected function generateBasis () {
        $this->handler->query(self::$basis);
        $this->handler->query(self::$basisrel);
    }

    protected function generateEntity ($table, $obj) {
        $this->handler->query('CREATE TABLE IF NOT EXISTS `' . $table . '` LIKE `basic`');
        //cycle between fields
    }

    protected function updateEntity ($table, $obj) {
        $columns_data = $this->handler->query('SHOW COLUMNS FROM `' . $table . '`');
        while ($column_data = $columns_data->fetch_assoc()) var_dump($column_data);
    }

    protected function generateRelation ($table) {
        return $this->handler->query('CREATE TABLE IF NOT EXISTS `' . $table . '` LIKE `basic_rel`');
    }

};

?>