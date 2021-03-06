<?php
require_once dirname(__FILE__) . '/../kvs.php';

/**
 * Test class for KVS implementations. The tests are run by implementations of this class, this is just example test data.
 * Generated by PHPUnit on 2011-12-20 at 11:10:05.
 */
abstract class KVSTester extends PHPUnit_Framework_TestCase {

   /**
    * @var KVS
    */
   protected $object;
   private $instanceOf = "KVS";

   protected function setUp() {
      $this->object = new $this->instanceOf();
      $this->object->setOnDemand(false); // must pass onDemand=false, because its useless without a useful implementation.
   }

   private function createTestData(){
      $this->object->put("a", 1);
      $this->object->put("b", 2);
      $this->object->put("c", 3);
      $this->object->put("bool_1", true);
      $this->object->put("bool_2", false);
      $this->object->put("array", array("1", 2, 3));
   }

   /**
    * @covers KVS::get
    */
   public function testGet() {
      $this->createTestData();

      $this->assertEquals($this->object->get("a"), 1);
      $this->assertEquals($this->object->get("b"), 2);
      $this->assertEquals($this->object->get("c"), 3);
      $this->assertEquals($this->object->get("bool_1"), true);
      $this->assertEquals($this->object->get("bool_2"), false);
      $this->assertEquals($this->object->get("array"), array("1", 2, 3));
      $this->assertInternalType("array", $this->object->get("array"));
      $this->assertInternalType("bool", $this->object->get("bool_1"));
      $this->assertInternalType("integer", $this->object->get("a"));
   }

   /**
    * @covers KVS::put
    */
   public function testPut() {
      // from scratch
      $this->object->put("a", 4);
      $this->assertEquals(4, $this->object->get("a"));

      // from overwrite
      $this->createTestData();
      $this->object->put("a", 4);
      $this->assertEquals(4, $this->object->get("a"));

      $this->object->put("a", false);
      $this->assertEquals(false, $this->object->get("a"));
      $this->assertFalse($this->object->get("a") === 0);

      $this->object->put("a", array(0,1,2,3));
      $this->assertNotEquals(false, $this->object->get("a"));
      $this->assertEquals(array(0,1,2,3), $this->object->get("a"));
   }

   /**
    * @covers KVS::get
    */
   public function testBogusGet()
   {
      $this->object->delete("a");
      $result = @$this->object->get("a");
      $this->assertNull($result);

      $result = @$this->object->get(md5(14));   // something that hasn't been set before.
      $this->assertNull($result);
   }

   /**
    * @covers KVS::delete
    */
   public function testDelete() {
      $this->object->delete("a");
      $result = @$this->object->get("a");
      $this->assertNull($result);

      $this->object->delete("z");
      $this->assertNull(@$this->object->get("z"));

      $this->object->put("abc", "data");
      $this->object->delete("abc");
      $this->assertNull(@$this->object->get("abc"));
   }

   /**
    * @covers KVS::sizeOf
    */
   public function testSizeOf()
   {
      $this->object->clear();
      $this->assertEquals(0, $this->object->sizeOf());

      for($i=0; $i<100; $i++)
      {
         $this->object->put((string)$i, md5($i));
         $this->assertEquals($i+1, $this->object->sizeOf());
      }

      $this->object->delete(1);
      $this->assertEquals(99, $this->object->sizeOf());

      $this->object->clear();
      $this->assertEquals(0, $this->object->sizeOf());
   }

   /**
    * @covers KVS::clear
    */
   public function testClear()
   {
      for($i=0; $i<100; $i++)
      {
         $this->object->put((string)$i, $i);
      }
      $this->assertEquals(100, $this->object->sizeOf());

      $this->object->clear();
      $this->assertEquals(0, $this->object->sizeOf());
   }

   /**
    * @covers KVS::export
    * @covers KVS::import
    */
   public function testExportImport() {
      $this->object->clear();
      for($i=0; $i<100; $i++)
      {
         $this->object->put((string)$i, $i);
      }
      $this->assertEquals(100, $this->object->sizeOf()); // make sure failure below isn't due to something else.

      $kvs = new KVS(false);  // we use KVS instead of instanceOf here, because all implementations should be readable by all others.
      $kvs->import($this->object->export());
      $this->assertEquals(100, $kvs->sizeOf());
      $kvs->clear();
      $kvs->import($this->object->export());
      $this->assertEquals(100, $kvs->sizeOf());

      for($i=0; $i<101; $i++) // includes a null case.
      {
         $this->assertEquals(
                 @$this->object->get($i), @$kvs->get($i),
                 "Failed for observation {$i} (original value: " . @$this->object->get($i) . ", found: " . @$kvs->get($i)
         );
      }

      // reverse the problem and do it all again.
      $this->object->clear();
      $this->assertEquals(0, $this->object->sizeOf());
      $this->object->import($kvs->export());
      $this->assertEquals(100, $this->object->sizeOf());
      for($i=0; $i<101; $i++) // includes a null case.
      {
         $this->assertEquals(
                 @$this->object->get($i), @$kvs->get($i),
                 "Failed for observation in backwards duplication {$i} (original value: " . @$this->object->get($i) . ", found: " . @$kvs->get($i)
         );
      }

   }
}


?>
