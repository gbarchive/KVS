<?php
   class CookieKVS extends KVS
   {
      private $storage;
      private $expire;
      private $path;
      private $domain;

      function __construct($storage = "wpkvs_", $expire="+30 days", $path="/", $domain=null, $onDemand = true)
      {
         $this->expire = strtotime($expire);
         $this->path = $path;
         $this->domain = $domain;
         $this->storage = $storage;
         parent::__construct($onDemand);
      }

      public function load()
      {
         if(!isset($_COOKIE[$this->storage]))
         {
            $this->data = array();
            return false;
         }

         $data = $_COOKIE[$this->storage];
         return ($this->data = unserialize($data));
      }

      public function save() { return $this->update(); }
      public function update()
      {
         return setcookie($this->storage, serialize($this->data), $this->expire, $this->path, $this->domain);
      }
   }
?>