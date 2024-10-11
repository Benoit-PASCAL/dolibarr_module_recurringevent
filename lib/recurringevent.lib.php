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
 *	\file		lib/recurringevent.lib.php
 *	\ingroup	recurringevent
 *	\brief		This file is an example module library
 *				Put some comments here
 */

/**
 * @return array
 */
function recurringeventAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load('recurringevent@recurringevent');

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/recurringevent/admin/recurringevent_extrafields.php", 1);
    $head[$h][1] = $langs->trans("ExtraFields");
    $head[$h][2] = 'extrafields';
    $h++;
    $head[$h][0] = dol_buildpath("/recurringevent/admin/recurringevent_about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    complete_head_from_modules($conf, $langs, $object, $head, $h, 'recurringevent');

    return $head;
}

/**
 * Return array of tabs to used on pages for third parties cards.
 *
 * @param 	RecurringEvent	$object		Object company shown
 * @return 	array				Array of tabs
 */
function recurringevent_prepare_head(RecurringEvent $object)
{
    global $langs, $conf;
    $h = 0;
    $head = array();
    $head[$h][0] = dol_buildpath('/recurringevent/card.php', 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("RecurringEventCard");
    $head[$h][2] = 'card';
    $h++;

    complete_head_from_modules($conf, $langs, $object, $head, $h, 'recurringevent');

    return $head;
}

/**
 * @param Form      $form       Form object
 * @param RecurringEvent  $object     RecurringEvent object
 * @param string    $action     Triggered action
 * @return string
 */
function getFormConfirmRecurringEvent($form, $object, $action)
{
    global $langs, $user;

    $formconfirm = '';

    if ($action === 'valid' && !empty($user->rights->recurringevent->write))
    {
        $body = $langs->trans('ConfirmValidateRecurringEventBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmValidateRecurringEventTitle'), $body, 'confirm_validate', '', 0, 1);
    }
    elseif ($action === 'accept' && !empty($user->rights->recurringevent->write))
    {
        $body = $langs->trans('ConfirmAcceptRecurringEventBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmAcceptRecurringEventTitle'), $body, 'confirm_accept', '', 0, 1);
    }
    elseif ($action === 'refuse' && !empty($user->rights->recurringevent->write))
    {
        $body = $langs->trans('ConfirmRefuseRecurringEventBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmRefuseRecurringEventTitle'), $body, 'confirm_refuse', '', 0, 1);
    }
    elseif ($action === 'reopen' && !empty($user->rights->recurringevent->write))
    {
        $body = $langs->trans('ConfirmReopenRecurringEventBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmReopenRecurringEventTitle'), $body, 'confirm_refuse', '', 0, 1);
    }
    elseif ($action === 'delete' && !empty($user->rights->recurringevent->write))
    {
        $body = $langs->trans('ConfirmDeleteRecurringEventBody');
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmDeleteRecurringEventTitle'), $body, 'confirm_delete', '', 0, 1);
    }
    elseif ($action === 'clone' && !empty($user->rights->recurringevent->write))
    {
        $body = $langs->trans('ConfirmCloneRecurringEventBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmCloneRecurringEventTitle'), $body, 'confirm_clone', '', 0, 1);
    }
    elseif ($action === 'cancel' && !empty($user->rights->recurringevent->write))
    {
        $body = $langs->trans('ConfirmCancelRecurringEventBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmCancelRecurringEventTitle'), $body, 'confirm_cancel', '', 0, 1);
    }

    return $formconfirm;
}

/**
 * Function to reset recurrence to the selected date
 *
 * @param RecurringEvent $object
 * @param int $selectedDate Timestamp of the selected date
 * @return void
 */
function resetRecurringEvent(RecurringEvent $object, $selectedDate)
{
    if ($object->locked) return;

    $object->frequency_unit = 'day'; // Exemple : réinitialisation à une fréquence quotidienne
    $object->frequency = 1;
    $object->date_recurrence = $selectedDate;
    $object->save($user);
}