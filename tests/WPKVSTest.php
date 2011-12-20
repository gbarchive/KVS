<?php
require_once dirname(__FILE__) . '/genericTest.php';

/*
 * This is a bad solution, but it allows quick test runs of the WPKVS implementation
 * without loading all of WordPress and a mock database.
 */
global $temp_storage;
$temp_storage = array();
function get_option($from, $default)
{
   global $temp_storage;
   if(isset($temp_storage[$from]))
      return unserialize($temp_storage[$from]);
   else
      return $default;

}
function update_option($to, $new_value)
{
   global $temp_storage;
   return ($temp_storage[$to] = serialize($new_value));
}
/*
 * end hackish WordPress implementation
 */

require_once dirname(__FILE__) . '/../implementations/wpkvs.php';
class WPKVSTest extends KVSTester { function __construct() { $this->instanceOf = "WPKVS"; } }
?>
