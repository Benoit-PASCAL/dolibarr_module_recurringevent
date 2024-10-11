<?php
use PHPUnit\Framework\TestCase;
use recurringevent\RecurringEvent;
use actioncomm\ActionComm;
use user\User;
use DoliDB;

class RecurringEventTriggerTest extends TestCase
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

    public function testTriggerOnActionCreate()
    {
        // Simulate the creation of an ActionComm
        $actionComm = new ActionComm($this->db);
        $actionComm->datep = strtotime('2024-04-03'); // Wednesday
        $actionComm->create($this->user);

        // Simulate the event triggered by the creation
        $parameters = ['currentcontext' => 'actioncard'];
        $hookmanager = new HookManager(); // Ensure that HookManager is correctly included
        $trigger = new RecurringEventTrigger($this->db);
        $result = $trigger->run_trigger('ACTION_CREATE', $actionComm, $this->user, $langs, $conf);

        // Verify that the trigger has created a RecurringEvent
        $this->recurringEvent->fetchBy($actionComm->id, 'fk_actioncomm');
        $this->assertNotEmpty($this->recurringEvent->id, 'A RecurringEvent must be created.');
    }

    public function testTriggerOnActionModify()
    {
        // Simulate the creation and modification of an ActionComm
        $actionComm = new ActionComm($this->db);
        $actionComm->datep = strtotime('2024-04-04'); // Thursday
        $actionComm->create($this->user);

        // Initial creation of the RecurringEvent
        $this->recurringEvent->fk_actioncomm = $actionComm->id;
        $this->recurringEvent->frequency = 1;
        $this->recurringEvent->frequency_unit = 'week';
        $this->recurringEvent->weekday_repeat = [4]; // Thursday
        $this->recurringEvent->end_type = 'occurrence';
        $this->recurringEvent->end_occurrence = 2;
        $this->recurringEvent->save($this->user);

        // Modify the ActionComm to trigger the modification
        $actionComm->datep = strtotime('2024-04-11'); // Next Thursday
        $actionComm->update($this->user);

        // Simulate the event triggered by the modification
        $parameters = ['currentcontext' => 'actioncard'];
        $hookmanager = new HookManager(); // Ensure that HookManager is correctly included
        $trigger = new RecurringEventTrigger($this->db);
        $result = $trigger->run_trigger('ACTION_MODIFY', $actionComm, $this->user, $langs, $conf);

        // Verify that the RecurringEvent has been updated
        $this->recurringEvent->fetchBy($actionComm->id, 'fk_actioncomm');
        $this->assertEquals(strtotime('2024-04-11'), $this->recurringEvent->actioncomm_datep, 'The recurrence date must be updated.');
    }

    public function testTriggerOnActionDelete()
    {
        // Simulate the creation of an ActionComm and a RecurringEvent
        $actionComm = new ActionComm($this->db);
        $actionComm->datep = strtotime('2024-04-05'); // Friday
        $actionComm->create($this->user);

        $this->recurringEvent->fk_actioncomm = $actionComm->id;
        $this->recurringEvent->frequency = 1;
        $this->recurringEvent->frequency_unit = 'week';
        $this->recurringEvent->weekday_repeat = [5]; // Friday
        $this->recurringEvent->end_type = 'occurrence';
        $this->recurringEvent->end_occurrence = 1;
        $this->recurringEvent->save($this->user);

        // Delete the ActionComm
        $actionComm->delete($this->user);

        // Simulate the event triggered by the deletion
        $parameters = ['currentcontext' => 'actioncard'];
        $hookmanager = new HookManager(); // Ensure that HookManager is correctly included
        $trigger = new RecurringEventTrigger($this->db);
        $result = $trigger->run_trigger('ACTION_DELETE', $actionComm, $this->user, $langs, $conf);

        // Verify that the RecurringEvent has been deleted
        $this->recurringEvent->fetchBy($actionComm->id, 'fk_actioncomm');
        $this->assertEmpty($this->recurringEvent->id, 'The RecurringEvent must be deleted.');
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