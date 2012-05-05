<?php
   /*
    * Copyright (c) Giuseppe Burtini.
    */
    if(!class_exists("WPKVS")) {
         require_once dirname(__FILE__) . "/../kvs.php";

         class FancyWPKVS extends WPKVS
         {
            protected $defaults;

            /**
             * This KVS, unlike the non-fancy version, supports default values in a variety of places. Importantly, you can pass a defaults array to the
             * constructor, and you can also pass it to the get method. The put method also supports a sanitizer method. It *is* intercompatible with the
             * WPKVS (the data can be read/written by either).
             * 
             * @param string $storage
             * @param array $defaults
             * @param string $type
             * @param int $item_id
             * @param bool $onDemand 
             */
            function __construct($storage = "wpkvs_", $defaults=false, $type="option", $item_id=null, $onDemand = true)
            {
               $this->defaults = $defaults;
               parent::__construct($storage, $type, $item_id, $onDemand);
            }

            public function get($key, $default=null, $suppress_filters=false){
               $result = parent::get($key, $suppress_filters);
               if($result === null)
                   return $default;
               
               return $result;
            }


            public function put($key, $value, $sanitizer=null, $suppress_filters=false){
               if(function_exists($sanitizer))
                   $value = $sanitizer($value);
                
               $result = parent::put($key, $value, $suppress_filters);
               return $result;
            }


            protected function getDataMethod()
            {
               $result = parent::getDataMethod();

               if(!is_array($result))
                  return $this->defaults;
               return $result;
            }

         }
    }
?>
