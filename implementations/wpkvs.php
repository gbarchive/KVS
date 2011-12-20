<?php
   class WPKVS extends KVS
   {
      private $storage;
      function __construct($storage = "wpkvs_", $onDemand = true)
      {
         $this->storage = $storage;
         parent::__construct($onDemand);

         $this->load();
      }

      public function load()
      {
         return ($this->data = get_option($this->storage, false));
      }

      public function save() { return $this->update(); }
      public function update()
      {
         return update_option($this->storage, $this->data);
      }
   }
?>