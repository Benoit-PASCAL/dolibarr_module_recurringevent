<?php
use PHPUnit\Framework\TestCase;
use recurringevent\RecurringEvent;
use actioncomm\ActionComm;
use user\User;
use DoliDB;

class LinkedObjectBlockTest extends TestCase
{
    protected $db;
    protected $user;
    protected $recurringEvent;

    protected function setUp(): void
    {
        // Initialize the database connection for testing
        $this->db = new DoliDB('mysqli://user:password@localhost/dolibarr_test');

        // Create a fake user with necessary permissions
        $this->user = new User($this->db);
        $this->user->id = 1;
        $this->user->rights->recurringevent->read = 1;
        $this->user->rights->recurringevent->write = 1;
        $this->user->rights->recurringevent->delete = 1;

        // Initialize a RecurringEvent for testing
        $this->recurringEvent = new RecurringEvent($this->db);
    }

    public function testLinkedObjectBlockDisplay()
    {
        // Create a linked ActionComm
        $actionComm = new ActionComm($this->db);
        $actionComm->datep = strtotime('2024-04-02'); // Tuesday
        $actionComm->create($this->user);

        // Create a RecurringEvent linked to the ActionComm
        $this->recurringEvent->fk_actioncomm = $actionComm->id;
        $this->recurringEvent->frequency = 1;
        $this->recurringEvent->frequency_unit = 'week';
        $this->recurringEvent->weekday_repeat = [2]; // Tuesday
        $this->recurringEvent->end_type = 'occurrence';
        $this->recurringEvent->end_occurrence = 3;
        $this->recurringEvent->save($this->user);

        // Simulate retrieving linked objects
        $linkedObjects = $this->recurringEvent->getAllChainFromMaster();

        // Assertions to verify that linked objects are correctly retrieved
        $this->assertNotEmpty($linkedObjects, 'Linked objects should not be empty.');
        foreach ($linkedObjects as $linkedObject) {
            $this->assertInstanceOf(ActionComm::class, $linkedObject, 'Each linked object must be an instance of ActionComm.');
        }
    }

    protected function tearDown(): void
    {
        // Clean up the database after tests
        $this->db->query("DELETE FROM ".MAIN_DB_PREFIX."recurringevent WHERE entity = 1");
        $this->db->query("DELETE FROM ".MAIN_DB_PREFIX."actioncomm WHERE entity = 1");
        $this->db->close();
    }
}
?>
