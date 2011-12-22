<?php
   /* KeyValueStore 1.
    *
    * The idea here is to abstract storing data for simple projects that are dependent
    * on other products for storing their data (MySQL, WordPress options, etc.). Makes
    * unit testing easier. Could be coupled with a distributed Key-Value implementation
    * for cool NoSQL problems.
    *
    * This is meant to stay very simple, but it does have some simple "data security" features
    * such as locking (doesn't allow any changes), disallowing overwrite (doesn't allow any overwriting
    * changes - still allows clear/import/delete!) and on demand saving (writes changes immediately)
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
      protected $onDemand;
      protected $data;
      protected $locked = false;
      protected $allowOverwrite = true;

      function __construct($onDemand = true)
      {
         $this->clear();
         $this->onDemand = $onDemand;
      }

      public function setOnDemand($od) { return $this->onDemand = $od; }
      public function getOnDemand() { return $this->onDemand; }
      public function setLocked($lock) { return $this->locked = $locked; }
      public function getLocked() { return $this->locked; }
      public function setAllowOverwrite($ow) { return $this->allowOverwrite = $ow; }
      public function getAllowOverwrite() { return $this->allowOverwrite; }

      public function update() { trigger_error("Update function not implemented (don't use KVS directly).", E_USER_NOTICE); }

      public function clear()
      {
         if($this->locked)
            return false;

         $this->data = array();
      }
      public function sizeOf()
      {
         return count($this->data);
      }

      public function get($key)
      {
         if(isset($this->data[$key]))
            return $this->data[$key];

         return null;
      }

      public function put($key, $value)
      {
         if($this->locked)
            return false;

         if(!$this->allowOverwrite && isset($this->data[$key]))
            return false;

         $this->data[$key] = $value;
         if($this->onDemand)
            $this->update();
      }

      public function delete($key)
      {
         if($this->locked)
            return false;

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
         if($this->locked)
            return false;

         $import_function = "unserialize";
         if(defined("KVS_IMPORT_FUNCTION"))
         {
            $import_function = KVS_IMPORT_FUNCTION;
         }
         $this->data = $import_function($kvs_export);
      }
   }
?>