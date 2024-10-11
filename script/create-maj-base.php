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
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 */

if(!defined('INC_FROM_DOLIBARR')) {
	define('INC_FROM_CRON_SCRIPT', true);

	require '../config.php';
} else {
	global $db;
}

dol_include_once('/recurringevent/class/recurringevent.class.php');

$o=new RecurringEvent($db);
$o->init_db_by_vars();

// {{ edit_1 }} : Ajout de la colonne 'number' pour définir le nombre de récurrences
$sql = "CREATE TABLE ".MAIN_DB_PREFIX."recurringevent (
    rowid int PRIMARY KEY AUTO_INCREMENT,
    entity int NOT NULL,
    fk_actioncomm int NOT NULL,
    fk_actioncomm_master int DEFAULT 0,
    frequency int NOT NULL,
    frequency_unit varchar(50) NOT NULL,
    weekday_repeat text,
    end_type varchar(30) NOT NULL,
    end_date date,
    end_occurrence int,
    actioncomm_datep date,
    actioncomm_datef date,
    import_key varchar(14),
    number int DEFAULT 1,
    locked tinyint(1) DEFAULT 0, // {{ edit_2 }} Ajout de la colonne 'locked' pour verrouiller la récurrence
    /* autres champs */
) ENGINE=InnoDB;";