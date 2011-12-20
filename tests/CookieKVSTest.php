<?php
require_once dirname(__FILE__) . '/genericTest.php';
require_once dirname(__FILE__) . '/../implementations/cookiekvs.php';

class CookieKVSTest extends KVSTester { function __construct() { $this->instanceOf = "CookieKVS"; } }
?>
