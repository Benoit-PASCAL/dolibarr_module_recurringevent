<?php
use PHPUnit\Framework\TestCase;
use recurringevent\LibFunctions;
use DoliDB;

class LibFunctionsTest extends TestCase
{
    protected $db;

    protected function setUp(): void
    {
        // Initialize the database connection for testing
        $this->db = new DoliDB('mysqli://user:password@localhost/dolibarr_test');
    }

    public function testFormatRecurrenceFrequency()
    {
        // Example function in recurringevent.lib.php to test
        $frequency = 2;
        $unit = 'week';
        $formatted = LibFunctions::formatRecurrenceFrequency($frequency, $unit);

        // Verify correct formatting
        $this->assertEquals('2 semaines', $formatted, 'The frequency must be correctly formatted.');
    }

    public function testValidateRecurrenceData()
    {
        // Example of recurrence data validation
        $data = [
            'frequency' => 1,
            'frequency_unit' => 'month',
            'weekday_repeat' => [1, 3],
            'end_type' => 'occurrence',
            'end_occurrence' => 5
        ];

        $isValid = LibFunctions::validateRecurrenceData($data);

        // Verify that the data is valid
        $this->assertTrue($isValid, 'Recurrence data must be valid.');
    }

    protected function tearDown(): void
    {
        // Clean up the database after tests if necessary
        $this->db->close();
    }
}
?>
