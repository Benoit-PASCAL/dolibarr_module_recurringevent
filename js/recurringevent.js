document.addEventListener("DOMContentLoaded", function() {
    // Function to handle the change of frequency unit
    function handleFrequencyUnitChange(unit) {
        var dayOfWeekField = document.getElementById("recurring-day-of-week");
        if (unit === "week") {
            dayOfWeekField.classList.remove("d-none");
        } else {
            dayOfWeekField.classList.add("d-none");
        }
    }

    // Listener for frequency unit change
    var frequencyUnitSelect = document.querySelector("select[name='frequency_unit']");
    if (frequencyUnitSelect) {
        frequencyUnitSelect.addEventListener("change", function() {
            handleFrequencyUnitChange(this.value);
        });

        // Initialize based on the current value
        handleFrequencyUnitChange(frequencyUnitSelect.value);
    }

    // Function to handle date selection
    function handleDateSelection(selectedDate) {
        var isLocked = document.getElementById("recurrence_locked").value;
        if (isLocked === "1") {
            console.log("Recurrence is locked. JS actions are disabled.");
            return;
        }
        // Additional logic based on the selected date
        console.log("Selected date:", selectedDate);
        // Example: Update the selected day of the week
        var date = new Date(selectedDate);
        var dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, etc.
        var checkbox = document.querySelector("input[name='weekday_repeat[]'][value='" + dayOfWeek + "']");
        if (checkbox) {
            checkbox.checked = true;
        }
    }

    // Add an event listener for the end date selection
    var endDateInput = document.querySelector("input[name='end_date']");
    if (endDateInput) {
        endDateInput.addEventListener("change", function() {
            handleDateSelection(this.value);
        });

        // Initialize based on the current value
        if (endDateInput.value) {
            handleDateSelection(endDateInput.value);
        }
    }

    // Function to reset the recurrence
    function resetRecurrence() {
        var isLocked = document.getElementById("recurrence_locked").value;
        if (isLocked === "1") {
            alert("Recurrence is locked. Actions are disabled.");
            return;
        }
        // Reset recurrence fields
        var frequencyInput = document.querySelector("input[name='frequency']");
        if (frequencyInput) frequencyInput.value = 1;
        var frequencyUnitSelect = document.querySelector("select[name='frequency_unit']");
        if (frequencyUnitSelect) frequencyUnitSelect.value = "week";
        // Uncheck all weekday checkboxes
        var checkboxes = document.querySelectorAll("input[name='weekday_repeat[]']");
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });
        // Check the day corresponding to the selected date
        var selectedDate = document.querySelector("input[name='end_date']").value;
        if (selectedDate) {
            var date = new Date(selectedDate);
            var dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, etc.
            var checkbox = document.querySelector("input[name='weekday_repeat[]'][value='" + dayOfWeek + "']");
            if (checkbox) {
                checkbox.checked = true;
            }
        }
        // Reset the end type
        var endTypeDate = document.getElementById("end_type_date");
        var endTypeOccurrence = document.getElementById("end_type_occurrence");
        if (endTypeDate && endTypeOccurrence) {
            endTypeDate.checked = true;
            document.getElementById("end_date_field").classList.remove("d-none");
            document.getElementById("end_occurrence_field").classList.add("d-none");
        }
        // Reset the number of events
        var numberInput = document.querySelector("input[name='number']");
        if (numberInput) numberInput.value = 1;
        console.log("Recurrence has been reset.");
    }

    // Add a listener for the reset button
    var resetButton = document.getElementById("reset-recurrence-button");
    if (resetButton) {
        resetButton.addEventListener("click", resetRecurrence);
    }

    // Disable fields if recurrence is locked
    var isLocked = document.getElementById("recurrence_locked").value;
    if (isLocked === "1") {
        document.getElementById("toggle-recurrence").disabled = true;

        var frequencyInputs = document.querySelectorAll("input[name='frequency'], select[name='frequency_unit']");
        frequencyInputs.forEach(function(input) {
            input.disabled = true;
        });

        var weekdayCheckboxes = document.querySelectorAll("input[name='weekday_repeat[]']");
        weekdayCheckboxes.forEach(function(checkbox) {
            checkbox.disabled = true;
        });

        var endTypeRadios = document.querySelectorAll("input[name='end_type']");
        endTypeRadios.forEach(function(radio) {
            radio.disabled = true;
        });

        if (endDateInput) endDateInput.disabled = true;
        var endOccurrenceInput = document.querySelector("input[name='end_occurrence']");
        if (endOccurrenceInput) endOccurrenceInput.disabled = true;
        if (resetButton) resetButton.disabled = true;
    }
});
