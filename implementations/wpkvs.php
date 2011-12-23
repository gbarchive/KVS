<?php
   require_once dirname(__FILE__) . "/../kvs.php";

   class WPKVS extends KVS
   {
      private $identifier;
      private $storage;
      private $item_id;
      private $storage_type;

      // allowed types are "option", "postmeta", "commentmeta"
      function __construct($storage = "wpkvs_", $type="option", $item_id=null, $onDemand = true)
      {
         $this->identifier = uniqid();

         if($type != "option")
         {
            if($item_id === null || !is_numeric($item_id))
               trigger_error ("You must specify a numeric item ID if you are not going to use the 'option' type of storage. We need to know what object to store things against.", E_USER_ERROR);
         }

         $this->storage = $storage;
         $this->item_id = $item_id;
         $this->storage_type = $type;
         parent::__construct($onDemand);

         $this->load();
      }
      public function getIdentifier() { return $this->identifier; }
      public function load()
      {
         return ($this->data = $this->getDataMethod());
      }

      public function save() { return $this->update(); }
      public function update()
      {
         return $this->setDataMethod();
      }

      public function get($key){
         $result = parent::get($key);

         return apply_filters("wpkvs_get", $result, $key, $this->identifier);
      }


      public function put($key, $value){
         $value = apply_filters("wpkvs_put", $value, $key, $this->identifier);
         $result = parent::put($key, $value);
         return $result;
      }


      /*
       * Gets the data from the appropriate method (options or meta fields)
       */
      private function getDataMethod()
      {
         switch($this->storage_type)
         {
            case "option":
               $result = get_option($this->storage, null);
            break;
            case "comment": case "cm": case "commentmeta":
               $result = get_comment_meta($this->item_id, $this->storage, true);
            break;
            case "post": case "pm": case "postmeta":
               $result = get_post_meta($this->item_id, $this->storage, true);
            break;
         }

         if(!is_array($result))
            return false;
         return $result;
      }

      /*
       * Updates the data to the appropriate method (options or meta fields)
       */
      private function setDataMethod()
      {
         switch($this->storage_type)
         {
            case "option":
               $result = update_option($this->storage, $this->data);
            break;
            case "comment": case "cm": case "commentmeta":
               // this method takes a "previous" value, which we could theoretically know
               // not sure if there's a good reason to though, should be using unique storage names.
               $result = update_comment_meta($this->item_id, $this->storage, $this->data);
            break;
            case "post": case "pm": case "postmeta":
               $result = update_post_meta($this->item_id, $this->storage, $this->data);
            break;
         }

         return $result;
      }
   }
?>