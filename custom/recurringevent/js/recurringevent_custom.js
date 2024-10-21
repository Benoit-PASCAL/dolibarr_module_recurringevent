   // recurringevent_custom.js

   document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('recurrence_date'); // Adjust the ID as per your form
    const daySelect = document.getElementById('recurrence_day'); // Adjust the ID as per your form
    const lockedInput = document.getElementById('recurrence_locked');

    if (dateInput && daySelect) {
        dateInput.addEventListener('change', function() {
            if (lockedInput && lockedInput.value === '1') {
                console.log('Recurrence is locked. JS actions disabled.');
                return;
            }

            const selectedDate = new Date(this.value);
            const selectedDay = selectedDate.getDay(); // Get the day of the week (0-6)

            // Set the recurrence day based on the selected date
            daySelect.value = selectedDay;
        });

        daySelect.addEventListener('change', function() {
            if (lockedInput && lockedInput.value === '1') {
                console.log('Recurrence is locked. JS actions disabled.');
                return;
            }

            const selectedDay = parseInt(this.value, 10);
            const currentDate = new Date(dateInput.value);
            const currentDay = currentDate.getDay();

            // Adjust the date to match the selected day
            const dayDifference = selectedDay - currentDay;
            currentDate.setDate(currentDate.getDate() + dayDifference);
            dateInput.value = currentDate.toISOString().split('T')[0]; // Update the date input
        });
    }

    const resetButton = document.getElementById('reset_recurrence_btn');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            if (lockedInput && lockedInput.value === '1') {
                alert('Recurrence is locked. Resetting is disabled.');
                return;
            }

            const selectedDate = dateInput.value;
            if (!selectedDate) {
                alert('Please select a date first.');
                return;
            }

            // Reset recurrence logic
            console.log('Resetting recurrence to date:', selectedDate);
            // Implement actual reset logic, possibly via AJAX or form submission
        });
    }
});

$(document).ready(function() {
    const resetButton = $('#reset_recurrence_btn');
    if (resetButton.length) {
        resetButton.css('cursor', 'pointer'); // Indiquer que c'est cliquable
        resetButton.on('click', function(event) {
            event.preventDefault();
            // Logique de réinitialisation
            resetRecurringEvent();
        });
    }

    function resetRecurringEvent() {
        // Implémentez la logique de réinitialisation ici
        console.log('Bouton de réinitialisation cliqué.');
        // Exemple : Réinitialiser les champs de formulaire
        $('#ap').val('');
        $('#customCheckLun, #customCheckMar, #customCheckMer, #customCheckJeu, #customCheckVen, #customCheckSam, #customCheckDim').prop('checked', false);
    }
});
