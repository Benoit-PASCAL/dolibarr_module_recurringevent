document.addEventListener("DOMContentLoaded", function() {
    // Fonction pour gérer la modification de l'unité de fréquence
    function handleFrequencyUnitChange(unit) {
        var dayOfWeekField = document.getElementById("recurring-day-of-week");
        if (unit === "week") {
            dayOfWeekField.classList.remove("d-none");
        } else {
            dayOfWeekField.classList.add("d-none");
        }
    }

    // Écouteur pour le changement d'unité de fréquence
    var frequencyUnitSelect = document.querySelector("select[name='frequency_unit']");
    if (frequencyUnitSelect) {
        frequencyUnitSelect.addEventListener("change", function() {
            handleFrequencyUnitChange(this.value);
        });

        // Initialisation en fonction de la valeur actuelle
        handleFrequencyUnitChange(frequencyUnitSelect.value);
    }

    // Fonction pour gérer la sélection de la date
    function handleDateSelection(selectedDate) {
        var isLocked = document.getElementById("recurrence_locked").value;
        if (isLocked === "1") {
            console.log("La récurrence est verrouillée. Les actions JS sont désactivées.");
            return;
        }
        // Logique additionnelle basée sur la date sélectionnée
        console.log("Date sélectionnée :", selectedDate);
        // Exemple : Mettre à jour le jour de la semaine sélectionné
        var date = new Date(selectedDate);
        var dayOfWeek = date.getDay(); // 0 = Dimanche, 1 = Lundi, etc.
        var checkbox = document.querySelector("input[name='weekday_repeat[]'][value='" + dayOfWeek + "']");
        if (checkbox) {
            checkbox.checked = true;
        }
    }

    // Ajout d'un écouteur d'événement pour la sélection de la date de fin
    var endDateInput = document.querySelector("input[name='end_date']");
    if (endDateInput) {
        endDateInput.addEventListener("change", function() {
            handleDateSelection(this.value);
        });

        // Initialisation en fonction de la valeur actuelle
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

    // Ajouter un écouteur pour le bouton de réinitialisation
    var resetButton = document.getElementById("reset-recurrence-button");
    if (resetButton) {
        resetButton.addEventListener("click", resetRecurrence);
    }

    // Désactiver les champs si la récurrence est verrouillée
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
