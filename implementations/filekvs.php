<?php
   // probably shouldn't use this one "on demand" will be slow.
   class FileKVS extends KVS
   {
      private $storage;

      function __construct($storage = "kvs.serializedata", $onDemand = false)
      {
         $this->storage = $storage;
         parent::__construct($onDemand);
      }

      public function load()
      {
         if(!is_readable($this->storage))
         {
            $this->data = array();
            return false;
         }

         $data = file_get_contents($this->storage);
         return ($this->data = unserialize($data));
      }

      public function save() { return $this->update(); }
      public function update()
      {
         return file_put_contents($this->storage, serialize($this->data));
      }
   }
?>