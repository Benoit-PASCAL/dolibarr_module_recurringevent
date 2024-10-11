<?php
use PHPUnit\Framework\TestCase;
require_once dirname(__FILE__).'/../class/recurringevent.class.php';

class RecurringEventTest extends TestCase
{
    protected $db;
    protected $user;

    protected function setUp(): void
    {
        // Initialize the connection to the test database
        $this->db = new DoliDB('mysqli://user:password@localhost/test_db');
        $this->user = new User($this->db);
        $this->user->id = 1; // Admin user
    }

    public function testDefaultRecurrenceDay()
    {
        $recurringEvent = new RecurringEvent($this->db);
        $recurringEvent->fk_actioncomm = 100; // ID of an existing actioncomm
        $recurringEvent->frequency = 1;
        $recurringEvent->frequency_unit = 'week';
        $recurringEvent->weekday_repeat = [2]; // Tuesday
        $recurringEvent->end_type = 'occurrence';
        $recurringEvent->end_occurrence = 5;
        $recurringEvent->actioncomm_datep = strtotime('2024-04-02'); // Tuesday

        $recurringEvent->save($this->user);

        $this->assertNotEmpty($recurringEvent->id, 'The recurring event must be created.');

        // Verify that the first recurring event is indeed a Tuesday
        $this->assertEquals(2, date('w', $recurringEvent->actioncomm_datep), 'The first event must be a Tuesday.');
    }

    public function testChangeRecurrenceDay()
    {
        $recurringEvent = new RecurringEvent($this->db);
        $recurringEvent->fk_actioncomm = 101; // ID of an existing actioncomm
        $recurringEvent->frequency = 1;
        $recurringEvent->frequency_unit = 'week';
        $recurringEvent->weekday_repeat = [3]; // Wednesday
        $recurringEvent->end_type = 'occurrence';
        $recurringEvent->end_occurrence = 3;
        $recurringEvent->actioncomm_datep = strtotime('2024-04-03'); // Wednesday

        $recurringEvent->save($this->user);

        $this->assertNotEmpty($recurringEvent->id, 'The recurring event must be created.');

        // Change the recurrence day to Thursday
        $recurringEvent->weekday_repeat = [4]; // Thursday
        $recurringEvent->update($this->user);

        // Retrieve the updated event
        $updatedEvent = new RecurringEvent($this->db);
        $updatedEvent->fetch($recurringEvent->id);

        $this->assertEquals(4, date('w', $updatedEvent->actioncomm_datep), 'The recurrence day must be changed to Thursday.');
    }

    public function testNumberOfRecurrences()
    {
        $recurringEvent = new RecurringEvent($this->db);
        $recurringEvent->fk_actioncomm = 102; // ID of an existing actioncomm
        $recurringEvent->frequency = 2;
        $recurringEvent->frequency_unit = 'week';
        $recurringEvent->weekday_repeat = [1, 3]; // Monday and Wednesday
        $recurringEvent->end_type = 'occurrence';
        $recurringEvent->end_occurrence = 4;
        $recurringEvent->number = 3; // Number of events to create
        $recurringEvent->actioncomm_datep = strtotime('2024-04-01'); // Monday

        $recurringEvent->save($this->user);

        $this->assertNotEmpty($recurringEvent->id, 'The recurring event must be created.');

        // Verify that only 3 recurring events are created
        $relatedEvents = $recurringEvent->getAllChainFromMaster();
        $this->assertCount(3, $relatedEvents, 'The number of recurring events must be equal to 3.');
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        $this->db->close();
    }
}
?>