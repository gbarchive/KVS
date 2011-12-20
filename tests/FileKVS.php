<?php

require_once dirname(__FILE__) . '/genericTest.php';
require_once dirname(__FILE__) . '/../implementations/filekvs.php';

class FileKVSTest extends KVSTester { function __construct() { $this->instanceOf = "FileKVS"; } }

?>
