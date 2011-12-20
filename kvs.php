<?php
   /* KeyValueStore 1.
    *
    * The idea here is to abstract storing data for simple projects that are dependent
    * on other products for storing their data (MySQL, WordPress options, etc.). Makes
    * unit testing easier. Could be coupled with a distributed Key-Value implementation
    * for cool NoSQL problems.
    *
    * Copyright © 2011 Giuseppe Burtini <joe@truephp.com>. All rights reserved.
    */

   interface KeyValueStore
   {
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
      private $onDemand;
      private $data;

      function __construct($onDemand = true)
      {
         $this->clear();
         $this->onDemand = $onDemand;
      }

      public function update() { trigger_error("Update function not implemented (don't use KVS directly).", E_USER_NOTICE); }

      public function clear()
      {
         $this->data = array();
      }
      public function sizeOf()
      {
         return count($this->data);
      }

      public function get($key)
      {
         return $this->data[$key];
      }

      public function put($key, $value)
      {
         $this->data[$key] = $value;
         if($this->onDemand)
            $this->update();
      }

      public function delete($key)
      {
         unset($this->data[$key]);
         if($this->onDemand)
            $this->update();
      }

      public function export() {
         $export_function = "serialize";
         if(defined("KVS_EXPORT_FUNCTION"))
         {
            $export_function = KVS_EXPORT_FUNCTION;
         }
         return $export_function($this->data);
      }

      public function import($kvs_export) {
         $import_function = "unserialize";
         if(defined("KVS_IMPORT_FUNCTION"))
         {
            $import_function = KVS_IMPORT_FUNCTION;
         }
         $this->data = $import_function($kvs_export);
      }
   }
?>