<?php

// Prepare the environment
if (!defined('TEST_ENV_SETUP')) {
    require_once dirname(__FILE__) . '/_setup.php';
}

use PHPUnit\Framework\TestCase;

class RecurringEventsCreationTest extends TestCase
{
    private array $event;
    private array $recurrence_types;

    protected function setUp(): void
    {
        global $db;
        $this->db = $db;

        $this->event = [
            'label' => 'Event',
            'start_date' => date('Y-m-d H:i:s'),
        ];

        $this->recurrences = [
            'days',
            'weeks',
            'months',
            'years'
        ];

        $this->recurrence_types = [
            'end_date',
            'occurrences'
        ];
    }

    public function testCreatingEvents()
    {
        foreach ($this->recurrences as $recurrence) {
            $this->testCreatingWithEndDate([
                'recurrence' => $recurrence,
                'recurrence_type' => 'end_date'
            ]);

            $this->testCreatingWithOccurences([
                'recurrence' => $recurrence,
                'recurrence_type' => 'occurrences'
            ]);
        }
    }

    private function testCreatingWithEndDate(array $event)
    {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->testEventCreation([
                    'recurrence' => $event['recurrence'],
                    'recurrence_type' => $event['recurrence_type'],
                    'recurrence_interval' => $j,
                    'end_date' => date('Y-m-d', strtotime('+' . $i . ' ' . $event['recurrence']))
                ]);
            }
        }
    }

    private function testCreatingWithOccurences(array $event)
    {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->testEventCreation([
                    'recurrence' => $event['recurrence'],
                    'recurrence_type' => $event['recurrence_type'],
                    'recurrence_interval' => $j,
                    'occurrences' => $i
                ]);
            }
        }
    }

    private function testEventCreation(array $event)
    {
        $this->createEvent($event);

        // We control that every event has been created
        $this->assertEventsExists($event);
    }

    private function createEvent(array $event)
    {
        $newEvent = new ActionComm($this->db);
        $newEvent->datep = date('Y-m-d H:i:s');
        $newEvent->label = 'Event';
    }

    private function assertEventsExists(array $event)
    {
        if ($event['recurrence_type'] === 'end_date') {
            $occurrences = $this->countOccurrences($event);
            foreach ($occurrences as $occurrence) {
                $this->assertEventExists([
                    'recurrence' => $event['recurrence'],
                    'recurrence_interval' => $event['recurrence_interval'],
                    'date' => date('Y-m-d', strtotime('+' . $occurrence . ' ' . $event['recurrence']))
                ]);
            }
        }

        if ($event['recurrence_type'] === 'occurrences') {
            foreach ($event['occurrences'] as $occurrence) {
                $this->assertEventExists([
                    'recurrence' => $event['recurrence'],
                    'recurrence_interval' => $event['recurrence_interval'],
                    'date' => date('Y-m-d', strtotime('+' . $occurrence . ' ' . $event['recurrence']))
                ]);
            }
        }
    }

    private function countOccurrences(array $event)
    {
        // Count occurrences using to start_date end_date, recurrence and recurrence_interval
        $startDate = new DateTime($event['start_date']);
        $endDate = new DateTime($event['end_date']);
        $interval = $event['recurrence_interval'];
        $recurrence = $event['recurrence'];

        $occurrences = 0;

        switch ($recurrence) {
            case 'days':
                $diff = $startDate->diff($endDate)->days;
                $occurrences = floor($diff / $interval);
                break;
            case 'weeks':
                $diff = $startDate->diff($endDate)->days;
                $occurrences = floor($diff / (7 * $interval));
                break;
            case 'months':
                $diff = $startDate->diff($endDate)->m + ($startDate->diff($endDate)->y * 12);
                $occurrences = floor($diff / $interval);
                break;
            case 'years':
                $diff = $startDate->diff($endDate)->y;
                $occurrences = floor($diff / $interval);
                break;
        }

        return $occurrences;
    }

    private function assertEventExists(array $event)
    {
        // Check if the event exists
        $sql = 'SELECT COUNT(*) FROM ' . MAIN_DB_PREFIX . 'actioncomm WHERE label = ? AND datep = ?';
        $resql = $this->db->prepare($sql);
        $resql->execute([$event['label'], $event['start_date']]);

        $this->assertEquals(1, $resql->fetch(PDO::FETCH_COLUMN));
    }
}
