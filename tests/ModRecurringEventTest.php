<?php
use PHPUnit\Framework\TestCase;
use recurringevent\ModRecurringEvent;
use DoliDB;

class ModRecurringEventTest extends TestCase
{
    protected $db;
    protected $module;

    protected function setUp(): void
    {
        // Initialize the database connection for testing
        $this->db = new DoliDB('mysqli://user:password@localhost/dolibarr_test');

        // Initialize the module
        $this->module = new ModRecurringEvent($this->db);
    }

    public function testModuleEnabling()
    {
        // Simulate enabling the module
        $result = $this->module->init($options = '');

        // Verify that initialization was successful
        $this->assertEquals(1, $result, 'Module initialization must succeed.');
    }

    public function testModuleDisabling()
    {
        // Simulate disabling the module
        $result = $this->module->remove($options = '');

        // Verify that disabling was successful
        $this->assertEquals(1, $result, 'Module disabling must succeed.');
    }

    protected function tearDown(): void
    {
        // Clean up the database after tests
        $this->db->query("DELETE FROM ".MAIN_DB_PREFIX."recurringevent WHERE entity = 1");
        $this->db->close();
    }
}
?>
