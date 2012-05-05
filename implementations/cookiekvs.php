<?php
    if(!class_exists("CookieKVS")) {

      require_once dirname(__FILE__) . "/../kvs.php";

      class CookieKVS extends KVS
      {
         private $storage;
         private $expire;
         private $path;
         private $domain;
		
         /**
          * Create a new Cookie-backed key-value store. Stores variables in a cookie with the name specified in $storage. 
          * Expire, path and domain are all parameters as you would pass them to setcookie. Data in a cookie KVS is stored
          * simply serialized with no validation, hashing with a server-side private salt or proper encryption should be used
          * to prevent user tampering if that is important for your application.
          * 
          * onDemand indicates whether updates to the "permanent storage" happen in real time. This is a little iffy on the CookieKVS
          * as multiple setcookie calls send multiple Set-Cookie headers, and the RFC(2616) does NOT require that duplicated values will
          * be overwritten by their last value. Modern browsers do appear to handle this properly.
          * 
          * @param string $storage
          * @param strtotime $expire
          * @param string $path
          * @param string $domain
          * @param boolean $onDemand 
          */
         function __construct($storage = "cookiekvs", $expire="+30 days", $path="/", $domain=null, $onDemand = true)
         {
            $this->expire = strtotime($expire);
            $this->path = $path;
            $this->domain = $domain;
            $this->storage = $storage;
            parent::__construct($onDemand);

            $this->load();
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
    }
?>
