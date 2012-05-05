<?php

/* KeyValueStore 1.
 *
 * The idea here is to abstract storing data for simple projects that are dependent
 * on other products for storing their data (MySQL, WordPress options, etc.). Makes
 * unit testing easier. Could be coupled with a distributed Key-Value implementation
 * for some NoSQL-type problems.
 *
 * You should note that if you do plan to use this with a RDBMS (MySQL, etc.), you are
 * probably doing something wrong and should consider your use case in more detail.
 *
 * This is meant to stay very simple, but it does have some simple "data security" features
 * such as locking (doesn't allow any changes), disallowing overwrite (doesn't allow any overwriting
 * changes - still allows clear/import/delete!) and on demand saving (writes changes immediately)
 * Locking does NOT persist over import/export. Import/export use serialize/unserialize in the basic
 * implementation, which is hugely overkill for many uses.
 *
 * Copyright © 2011 Giuseppe Burtini <joe@truephp.com>. All rights reserved.
 */
if (!interface_exists("KeyValueStore")) {
    interface KeyValueStore {

        public function get($key);

        public function put($key, $value);

        public function delete($key);

        public function clear();

        public function sizeOf();

        public function import($kvs_export);   // imports a previously exported KVS store

        public function export();              // exports a KVS store, using KVS_*PORT_FUNCTION
    }

    define("KVS_IMPORT_FUNCTION", "unserialize");
    define("KVS_EXPORT_FUNCTION", "serialize");

    class KVS implements KeyValueStore {

        // $onDemand (get/set): indicates whether to update the "permanent storage" space immediately (on put/delete) or wait for a user to call update.
        protected $onDemand; 
        
        // $locked (get/set): indicates if change operations are allowed at the moment.
        protected $locked = false;   
        
        // $allowOverwrite (get/set): indicates whether destructive updates are allowed (a child class might "version" updates making this irrelevant)
        protected $allowOverwrite = true; 
        
        protected $data;

        function __construct($onDemand = true) {
            $this->clear();
            $this->onDemand = $onDemand;
        }

        public function setOnDemand($od) {
            return $this->onDemand = $od;
        }

        public function getOnDemand() {
            return $this->onDemand;
        }

        public function setLocked($lock) {
            return $this->locked = $locked;
        }

        public function getLocked() {
            return $this->locked;
        }

        public function setAllowOverwrite($ow) {
            return $this->allowOverwrite = $ow;
        }

        public function getAllowOverwrite() {
            return $this->allowOverwrite;
        }

        public function update() {
            trigger_error("Update function not implemented (don't use KVS directly).", E_USER_NOTICE);
        }

        public function clear() {
            if ($this->locked)
                return false;

            $this->data = array();
        }

        public function sizeOf() {
            return count($this->data);
        }

        public function get($key) {
            if (isset($this->data[$key]))
                return $this->data[$key];

            return null;
        }

        public function put($key, $value) {
            if ($this->locked)
                return false;

            if (!$this->allowOverwrite && isset($this->data[$key]))
                return false;

            $this->data[$key] = $value;
            if ($this->onDemand)
                $this->update();
        }

        public function delete($key) {
            if ($this->locked)
                return false;

            unset($this->data[$key]);
            if ($this->onDemand)
                $this->update();
        }

        public function export() {
            $export_function = "serialize";
            if (defined("KVS_EXPORT_FUNCTION")) {
                $export_function = KVS_EXPORT_FUNCTION;
            }
            return $export_function($this->data);
        }

        public function import($kvs_export) {
            if ($this->locked)
                return false;

            $import_function = "unserialize";
            if (defined("KVS_IMPORT_FUNCTION")) {
                $import_function = KVS_IMPORT_FUNCTION;
            }
            $this->data = $import_function($kvs_export);
        }

    }

}
?>
