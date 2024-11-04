<?php
/* Copyright (C) 2019 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_recurringevent.class.php
 * \ingroup recurringevent
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsRecurringEvent
 */
class ActionsRecurringEvent
{
    /**
     * @var DoliDb        Database handler (result of a new DoliDB)
     */
    public $db;

    /**
     * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
     */
    public $results = array();

    /**
     * @var string String displayed by executeHook() immediately after return
     */
    public $resprints;

    /**
     * @var array Errors
     */
    public $errors = array();

    /**
     * Constructor
     * @param DoliDB $db Database connector
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Overloading the doActions function : replacing the parent's function with the one below
     *
     * @param array()         $parameters     Hook metadatas (context, etc...)
     * @param CommonObject $object The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param string $action Current action (if set). Generally create or edit or null
     * @param HookManager $hookmanager Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
     */
    public function doActions($parameters, &$object, &$action, $hookmanager)
    {
        return 0;
    }

    /**
     * Overloading the doActions function : replacing the parent's function with the one below
     *
     * @param array()         $parameters     Hook metadatas (context, etc...)
     * @param CommonObject $object The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
     * @param string $action Current action (if set). Generally create or edit or null
     * @param HookManager $hookmanager Hook manager propagated to allow calling another hook
     * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
     */
    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        switch ($parameters['currentcontext']) {
            case 'externalaccesspage':
                return $this->formObjectExternalAccess($object);
            case 'actioncard':
                return $this->formObjectActionCard($object);
        }

        return 0;
    }

    private function formObjectActionCard(CommonObject &$object): int
    {
        global $langs;

        $langs->load('recurringevent@recurringevent');
        if (!defined('INC_FROM_DOLIBARR')) {
            define('INC_FROM_DOLIBARR', 1);
        }
        dol_include_once('recurringevent/class/recurringevent.class.php');
        $recurringEvent = new RecurringEvent($this->db);
        $recurringEvent->fetchBy($object->id, 'fk_actioncomm');

        $this->resprints = '
                <tr class="trextrafieldseparator trextrafieldseparator_recurringevent_start"><td colspan="2"><strong>' . $langs->trans(
                'RecurringEventSeparatorStart'
            ) . '</strong></td></tr>

                <tr id="" class="recurringevent">
                    <td class=""><b>' . $langs->trans('RecurringEventDefineEventAsRecurrent') . '</b></td>
                    <td id="" class="action_extras_agf_site" colspan="3">
                        <input id="" onchange="$(\'.recurring-options\').toggleClass(\'hideobject\')" name="is_recurrent" type="checkbox" class="custom-control-input" ' . (!empty($recurringEvent->id) ? 'checked' : '') . '>
                    </td>
                </tr>

                <tr>
                    <td class="">' . $langs->trans('RecurringEventFrequencyUnit') . '</td>
                    <td id="" class="action_extras_agf_site" colspan="3">
                        <select id="frequency_unit" name="frequency_unit" class="custom-select d-block w-100" onchange="toggleRecurringOptions()">
                            <option value="day" ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'day' ? 'selected' : '') . '>' . $langs->trans('RecurringEventDay') . '</option>
                            <option value="week" ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'week' ? 'selected' : '') . '>' . $langs->trans('RecurringEventWeek') . '</option>
                            <option value="month" ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'month' ? 'selected' : '') . '>' . $langs->trans('RecurringEventMonth') . '</option>
                            <option value="year" ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'year' ? 'selected' : '') . '>' . $langs->trans('RecurringEventYear') . '</option>
                        </select>
                    </td>
                </tr>

                <tr id="recurring-days-of-week" class="recurringevent recurring-options ' . (!empty($recurringEvent->id) && ($recurringEvent->frequency_unit == 'day' || $recurringEvent->frequency_unit == 'week') ? '' : 'hideobject') . '">
                    <td class="">' . $langs->trans('RecurringEventRepeatThe') . ' 
                        <br>
                        <a href="#" id="reset_recurrence_btn" >
                            <span class="fa fa-sync valignmiddle paddingleft" title="'.$langs->trans("RecurringEventResetFirstDay").'"></span>
                        </a>
                    </td>
                    <td id="" class="" colspan="3">
                        <div class="pull-left minwidth100">
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                1,
                $recurringEvent->weekday_repeat
            ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckLun" name="weekday_repeat[]" value="1">
                                <label class="custom-control-label" for="customCheckLun">' . $langs->trans(
                'RecurringEventMondayShort'
            ) . '</label>
                            </div>
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                2,
                $recurringEvent->weekday_repeat
            ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckMar" name="weekday_repeat[]" value="2">
                                <label class="custom-control-label" for="customCheckMar">' . $langs->trans(
                'RecurringEventTuesdayShort'
            ) . '</label>
                            </div>
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                3,
                $recurringEvent->weekday_repeat
            ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckMer" name="weekday_repeat[]" value="3">
                                <label class="custom-control-label" for="customCheckMer">' . $langs->trans(
                'RecurringEventWednesdayShort'
            ) . '</label>
                            </div>
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                4,
                $recurringEvent->weekday_repeat
            ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckJeu" name="weekday_repeat[]" value="4">
                                <label class="custom-control-label" for="customCheckJeu">' . $langs->trans(
                'RecurringEventThursdayShort'
            ) . '</label>
                            </div>
                        </div>

                        <div class="pull-left minwidth100">
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                5,
                $recurringEvent->weekday_repeat
            ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckVen" name="weekday_repeat[]" value="5">
                                <label class="custom-control-label" for="customCheckVen">' . $langs->trans(
                'RecurringEventFridayShort'
            ) . '</label>
                            </div>
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                6,
                $recurringEvent->weekday_repeat
            ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckSam" name="weekday_repeat[]" value="6">
                                <label class="custom-control-label" for="customCheckSam">' . $langs->trans(
                'RecurringEventSaturdayShort'
            ) . '</label>
                            </div>
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                0,
                $recurringEvent->weekday_repeat
            ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckDim" name="weekday_repeat[]" value="0">
                                <label class="custom-control-label" for="customCheckDim">' . $langs->trans(
                'RecurringEventSundayShort'
            ) . '</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr id="recurring-months" class="recurringevent recurring-options ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'month' ? '' : 'hideobject') . '">
                    <td class="">' . $langs->trans('RecurringEventRepeatOnMonths') . '</td>
                    <td id="" class="action_extras_agf_site" colspan="3">
                        <div id="recurring-months" class="form-group pl-4 ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'month' ? '' : 'd-none') . '">
                            <div class="form-row">
                                <label for="month_day" class="col-sm-2 col-form-label">' . $langs->trans('RecurringEventRepeatOnDay') . '</label>
                                <div class="col-sm-10">
                                    <select id="month_day" name="month_day" class="custom-select">
                                        ' . implode('', array_map(function ($day) use ($recurringEvent) {
                                            return '<option value="' . $day . '" ' . (!empty($recurringEvent->id) && $recurringEvent->month_day == $day ? 'selected' : '') . '>' . $day . '</option>';
                                        }, range(1, 31))) . '
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr id="recurring-years" class="recurringevent recurring-options ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'year' ? '' : 'hideobject') . '">
                    <td class="">' . $langs->trans('RecurringEventRepeatOnYears') . '</td>
                    <td id="" class="action_extras_agf_site" colspan="3">
                        <div class="form-row">
                            <div class="col-sm-6">
                                <select id="year_month" name="year_month" class="custom-select">
                                    <option value="">' . $langs->trans('RecurringEventMonth') . '</option>
                                    <option value="1" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 1 ? 'selected' : '') . '>' . $langs->trans('January') . '</option>
                                    <option value="2" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 2 ? 'selected' : '') . '>' . $langs->trans('February') . '</option>
                                    <option value="3" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 3 ? 'selected' : '') . '>' . $langs->trans('March') . '</option>
                                    <option value="4" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 4 ? 'selected' : '') . '>' . $langs->trans('April') . '</option>
                                    <option value="5" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 5 ? 'selected' : '') . '>' . $langs->trans('May') . '</option>
                                    <option value="6" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 6 ? 'selected' : '') . '>' . $langs->trans('June') . '</option>
                                    <option value="7" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 7 ? 'selected' : '') . '>' . $langs->trans('July') . '</option>
                                    <option value="8" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 8 ? 'selected' : '') . '>' . $langs->trans('August') . '</option>
                                    <option value="9" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 9 ? 'selected' : '') . '>' . $langs->trans('September') . '</option>
                                    <option value="10" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 10 ? 'selected' : '') . '>' . $langs->trans('October') . '</option>
                                    <option value="11" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 11 ? 'selected' : '') . '>' . $langs->trans('November') . '</option>
                                    <option value="12" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 12 ? 'selected' : '') . '>' . $langs->trans('December') . '</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select id="year_day" name="year_day" class="custom-select">
                                    <option value="">' . $langs->trans('RecurringEventDay') . '</option>
                                    ' . implode('', array_map(function ($day) use ($recurringEvent) {
                                        return '<option value="' . $day . '" ' . (!empty($recurringEvent->id) && $recurringEvent->year_day == $day ? 'selected' : '') . '>' . $day . '</option>';
                                    }, range(1, 31))) . '
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr id="" class="recurringevent recurring-options ' . (!empty($recurringEvent->id) ? '' : 'hideobject') . '">
                    <td class="">' . $langs->trans('RecurringEventFinishAt') . '</td>
                    <td id="" class="action_extras_agf_site" colspan="3">
                        <div class="col-sm-10 ">
                            <div class="form-inline mb-3">
                                <input class="form-check-input" type="radio" name="end_type" id="end_type_date" value="date" ' . ((!empty($recurringEvent->id) && $recurringEvent->end_type == 'date' || empty($recurringEvent->id)) ? 'checked' : '') . '>
                                <label class="form-check-label" for="end_type_date">
                                ' . $langs->trans('RecurringEventThe') . '
                                </label>
                                <input type="date" class="form-control ml-2" name="end_date" ' . ((!empty($recurringEvent->id) && !empty($recurringEvent->end_date)) ? 'value="' . date(
                    'Y-m-d',
                    $recurringEvent->end_date
                ) . '"' : '') . ' onchange="$(\'#end_type_date\').prop(\'checked\', true)" />
                            </div>
                            <div class="form-inline">
                                <input class="form-check-input" type="radio" name="end_type" id="end_type_occurrence" value="occurrence" ' . (!empty($recurringEvent->id) && $recurringEvent->end_type == 'occurrence' ? 'checked' : '') . '>
                                <label class="form-check-label" for="end_type_occurrence">
                                ' . $langs->trans('RecurringEventAfter') . '
                                </label>
                                <input type="number" class="form-control mx-2 col-2 maxwidth50" size="2" placehoder="5" name="end_occurrence" value="' . (!empty($recurringEvent->id) ? $recurringEvent->end_occurrence : '') . '" onchange="$(\'#end_type_occurrence\').prop(\'checked\', true)" />
                                ' . $langs->trans('RecurringEventoccurrences') . '
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="trextrafieldseparator trextrafieldseparator_recurringevent_end"><td colspan="2"></td></tr>
            ';

        $this->addJsToUpdateCheckedBoxes($recurringEvent);

        $this->resprints .= <<<JS
        <script>
        $(document).ready(function() {
            // Afficher/masquer les jours de la semaine selon les cases cochées
            function toggleWeekdays() {
                if ($('#frequency_unit_week').is(':checked') || $('#frequency_unit_day').is(':checked')) {
                    $('#recurring-day-of-week').removeClass('hideobject');
                } else {
                    $('#recurring-day-of-week').addClass('hideobject');
                }
            }
            
            // Au chargement et au changement des cases �� cocher
            toggleWeekdays();
            $('input[name="frequency_unit[]"]').change(toggleWeekdays);
        });

        function toggleRecurringOptions() {
            var frequencyUnit = $('#frequency_unit').val();
            $('#recurring-days-of-week').toggleClass('hideobject', frequencyUnit !== 'day' && frequencyUnit !== 'week');
            $('#recurring-months').toggleClass('hideobject', frequencyUnit !== 'month');
            $('#recurring-years').toggleClass('hideobject', frequencyUnit !== 'year');
        }
        </script>
    JS;

        return 0;
    }

    private function addJsToUpdateCheckedBoxes(CommonObject $object)
    {
        $isModified = !empty($object->id) ? 'true' : 'false';

        $this->resprints .= '<script type="text/javascript">';

        $this->resprints .= <<<JS

            let isModified = $isModified;
            let elDateSelector = $('#ap');
            let elDaysChecboxes = $('#customCheckLun, #customCheckMar, #customCheckMer, #customCheckJeu, #customCheckVen, #customCheckSam, #customCheckDim');
            let elResetButton = $('#reset_recurrence_btn');
            
            const findWeekDate = (date) => {
                date = date.split('/').reverse().join('-');
                
                const d = new Date(date);
                return d.getDay();
            }
            
            const getWeekDate = () => {
                return elDateSelector.valueOf();
            }
            
            const checkDayBox = (findWeekDay) => {
                
                let days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
                
                days.forEach((day) => {
                    let el = $('#customCheck' + day);
                    if (el) {
                        el.prop('checked', false);
                    }
                });
                
                let el = $('#customCheck' + days[findWeekDay]);
                if (el) {
                    el.prop('checked', true);
                }
            } 
            
            const resetRecurringEvent = () => {
                const selectedDate = getWeekDate();
                checkDayBox(selectedDate);
            }
            
            const handleDateSelectorChange = (event) => {
                let weekDay = findWeekDate(event.target.value);
                
                checkDayBox(weekDay);
            }
            
            const handleManualCheckboxChange = () => {
                elDateSelector.off('change');
            }
            
            const resetButtonHandler = () => {
                resetRecurringEvent();
            }

            const dynamicCheckboxesHandler = () => {
                if(!isModified)
                {
                    elDateSelector.on('change', handleDateSelectorChange);
                    elDaysChecboxes.on('change', handleManualCheckboxChange);
                }
                elResetButton.on('click', resetButtonHandler);
            }
            
            $(document).ready(dynamicCheckboxesHandler);

JS;

        $this->resprints .= '</script>';
    }

    private function formObjectExternalAccess(CommonObject &$object): int
    {
        global $langs;

        $context = Context::getInstance();
        if ($context->controller === 'agefodd_event_other') {
            $langs->load('recurringevent@recurringevent');
            if (!defined('INC_FROM_DOLIBARR')) {
                define('INC_FROM_DOLIBARR', 1);
            }
            dol_include_once('recurringevent/class/recurringevent.class.php');
            $recurringEvent = new RecurringEvent($this->db);
            $recurringEvent->fetchBy($object->id, 'fk_actioncomm');

            $this->resprints = '
                <!-- DEBUT form récurrence : ceci devrait être externalisé dans un module puis remplacé par l\'appel d\'un hook -->
                <div class="form-row my-3">
                    <div class="custom-control custom-checkbox">
                        <input onchange="$(\'#recurring-options\').toggleClass(\'d-block\')" id="toggle-recurrence" name="is_recurrent" type="checkbox" class="custom-control-input" ' . (!empty($recurringEvent->id) ? 'checked' : '') . '>
                        <label class="custom-control-label" for="toggle-recurrence">' . $langs->trans(
                    'RecurringEventDefineEventAsRecurrent'
                ) . '</label>
                    </div>
                </div>

                <div id="recurring-options" class="form-group my-3 ' . (!empty($recurringEvent->id) ? '' : 'd-none') . '">

                    <div class="form-row my-3 pl-4">
                        <div class="col-auto">
                            <label for="country">' . $langs->trans('RecurringEventRepeatEventEach') . '</label>
                        </div>
                        <div class="col-2">
                            <input type="number" class="form-control" value="' . (!empty($recurringEvent->id) ? $recurringEvent->frequency : 1) . '" name="frequency" size="4" />
                        </div>
                        <div class="col-auto">
                            <select id="frequency_unit" name="frequency_unit" class="custom-select d-block w-100" onchange="if (this.value !== \'week\') { $(\'#recurring-day-of-week\').addClass(\'d-none\'); } else { $(\'#recurring-day-of-week\').removeClass(\'d-none\'); }">
                                <option value="day" ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'day' ? 'selected' : '') . '>' . $langs->trans(
                    'RecurringEventRepeatEventEachDay'
                ) . '</option>
                                <option value="week"  ' . ((!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'week' || empty($recurringEvent->id)) ? 'selected' : '') . '>' . $langs->trans(
                    'RecurringEventRepeatEventEachWeek'
                ) . '</option>
                                <option value="month" ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'month' ? 'selected' : '') . '>' . $langs->trans(
                    'RecurringEventRepeatEventEachMonth'
                ) . '</option>
                                <option value="year" ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'year' ? 'selected' : '') . '>' . $langs->trans(
                    'RecurringEventRepeatEventEachYear'
                ) . '</option>
                            </select>
                        </div>
                    </div>

                    <fieldset id="recurring-day-of-week" class="form-group pl-4">
                        <div class="row">
                            <legend class="col-form-label col-sm-2 pt-0">' . $langs->trans('RecurringEventRepeatThe') . '</legend>
                            <div class="col-sm-3">
                                <div class="form-check custom-control custom-checkbox">
                                    <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                    1,
                    $recurringEvent->weekday_repeat
                ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckLun" name="weekday_repeat[]" value="1">
                                    <label class="custom-control-label" for="customCheckLun">' . $langs->trans(
                    'RecurringEventMondayShort'
                ) . '</label>
                                </div>
                                <div class="form-check custom-control custom-checkbox">
                                    <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                    2,
                    $recurringEvent->weekday_repeat
                ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckMar" name="weekday_repeat[]" value="2">
                                    <label class="custom-control-label" for="customCheckMar">' . $langs->trans(
                    'RecurringEventTuesdayShort'
                ) . '</label>
                                </div>
                                <div class="form-check custom-control custom-checkbox">
                                    <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                    3,
                    $recurringEvent->weekday_repeat
                ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckMer" name="weekday_repeat[]" value="3">
                                    <label class="custom-control-label" for="customCheckMer">' . $langs->trans(
                    'RecurringEventWednesdayShort'
                ) . '</label>
                                </div>
                                <div class="form-check custom-control custom-checkbox">
                                    <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                    4,
                    $recurringEvent->weekday_repeat
                ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckJeu" name="weekday_repeat[]" value="4">
                                    <label class="custom-control-label" for="customCheckJeu">' . $langs->trans(
                    'RecurringEventThursdayShort'
                ) . '</label>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-check custom-control custom-checkbox">
                                    <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                    5,
                    $recurringEvent->weekday_repeat
                ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckVen" name="weekday_repeat[]" value="5">
                                    <label class="custom-control-label" for="customCheckVen">' . $langs->trans(
                    'RecurringEventFridayShort'
                ) . '</label>
                                </div>
                                <div class="form-check custom-control custom-checkbox">
                                    <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                    6,
                    $recurringEvent->weekday_repeat
                ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckSam" name="weekday_repeat[]" value="6">
                                    <label class="custom-control-label" for="customCheckSam">' . $langs->trans(
                    'RecurringEventSaturdayShort'
                ) . '</label>
                                </div>
                                <div class="form-check custom-control custom-checkbox">
                                    <input type="checkbox" ' . (!empty($recurringEvent->id) && in_array(
                    0,
                    $recurringEvent->weekday_repeat
                ) ? 'checked' : '') . ' class="custom-control-input" id="customCheckDim" name="weekday_repeat[]" value="0">
                                    <label class="custom-control-label" for="customCheckDim">' . $langs->trans(
                    'RecurringEventSundayShort'
                ) . '</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group pl-4">
                        <div class="row">
                            <legend class="col-form-label col-sm-2">' . $langs->trans('RecurringEventFinishAt') . '</legend>
                            <div class="col-sm-10 ">
                                <div class="form-inline mb-3">
                                    <input class="form-check-input" type="radio" name="end_type" id="end_type_date" value="date" ' . ((!empty($recurringEvent->id) && $recurringEvent->end_type == 'date' || empty($recurringEvent->id)) ? 'checked' : '') . '>
                                    <label class="form-check-label" for="end_type_date">
                                    ' . $langs->trans('RecurringEventThe') . '
                                    </label>
                                    <input type="date" class="form-control ml-2" name="end_date" ' . ((!empty($recurringEvent->id) && !empty($recurringEvent->end_date)) ? 'value="' . date(
                        'Y-m-d',
                        $recurringEvent->end_date
                    ) . '"' : '') . ' onchange="$(\'#end_type_date\').prop(\'checked\', true)" />
                                </div>
                                <div class="form-inline">
                                    <input class="form-check-input" type="radio" name="end_type" id="end_type_occurrence" value="occurrence" ' . (!empty($recurringEvent->id) && $recurringEvent->end_type == 'occurrence' ? 'checked' : '') . '>
                                    <label class="form-check-label" for="end_type_occurrence">
                                    ' . $langs->trans('RecurringEventAfter') . '
                                    </label>
                                    <input type="number" class="form-control mx-2 col-2" size="2" placehoder="5" name="end_occurrence" value="' . (!empty($recurringEvent->id) ? $recurringEvent->end_occurrence : '') . '" onchange="$(\'#end_type_occurrence\').prop(\'checked\', true)" />
                                    ' . $langs->trans('RecurringEventoccurrences') . '
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div id="recurring-months" class="form-group pl-4 ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'month' ? '' : 'd-none') . '">
                        <div class="form-row">
                            <label for="month_day" class="col-sm-2 col-form-label">' . $langs->trans('RecurringEventRepeatOnDay') . '</label>
                            <div class="col-sm-10">
                                <select id="month_day" name="month_day" class="custom-select">
                                    ' . implode('', array_map(function ($day) use ($recurringEvent) {
                                        return '<option value="' . $day . '" ' . (!empty($recurringEvent->id) && $recurringEvent->month_day == $day ? 'selected' : '') . '>' . $day . '</option>';
                                    }, range(1, 31))) . '
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="recurring-years" class="form-group pl-4 ' . (!empty($recurringEvent->id) && $recurringEvent->frequency_unit == 'year' ? '' : 'd-none') . '">
                        <div class="form-row">
                            <label class="col-form-label col-sm-2 pt-0">' . $langs->trans('RecurringEventRepeatOnYears') . '</label>
                            <div class="col-sm-10">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <select id="year_month" name="year_month" class="custom-select">
                                            <option value="">' . $langs->trans('RecurringEventMonth') . '</option>
                                            <option value="1" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 1 ? 'selected' : '') . '>' . $langs->trans('January') . '</option>
                                            <option value="2" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 2 ? 'selected' : '') . '>' . $langs->trans('February') . '</option>
                                            <option value="3" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 3 ? 'selected' : '') . '>' . $langs->trans('March') . '</option>
                                            <option value="4" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 4 ? 'selected' : '') . '>' . $langs->trans('April') . '</option>
                                            <option value="5" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 5 ? 'selected' : '') . '>' . $langs->trans('May') . '</option>
                                            <option value="6" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 6 ? 'selected' : '') . '>' . $langs->trans('June') . '</option>
                                            <option value="7" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 7 ? 'selected' : '') . '>' . $langs->trans('July') . '</option>
                                            <option value="8" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 8 ? 'selected' : '') . '>' . $langs->trans('August') . '</option>
                                            <option value="9" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 9 ? 'selected' : '') . '>' . $langs->trans('September') . '</option>
                                            <option value="10" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 10 ? 'selected' : '') . '>' . $langs->trans('October') . '</option>
                                            <option value="11" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 11 ? 'selected' : '') . '>' . $langs->trans('November') . '</option>
                                            <option value="12" ' . (!empty($recurringEvent->id) && $recurringEvent->year_month == 12 ? 'selected' : '') . '>' . $langs->trans('December') . '</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select id="year_day" name="year_day" class="custom-select">
                                            <option value="">' . $langs->trans('RecurringEventDay') . '</option>
                                            ' . implode('', array_map(function ($day) use ($recurringEvent) {
                                                return '<option value="' . $day . '" ' . (!empty($recurringEvent->id) && $recurringEvent->year_day == $day ? 'selected' : '') . '>' . $day . '</option>';
                                            }, range(1, 31))) . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <fieldset class="form-group pl-4">
                        ...
                    </fieldset>
                    ...

                </div>
                <!-- FIN form récurrence -->
                ';
        }

        return 0;
    }
}






