<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Script/page to display (in JSON) informations of all teams and theirs expected teams members.
 * This JSON will be used by an extern powershell script to update teams members.
 *
 * @package   mod_teams
 * @copyright 2020 Université Clermont Auvergne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/teams/lib.php');

$team_id = (isset($_GET['team_id'])) ? $_GET['team_id'] : null;
$response = new stdClass();
$PAGE->set_url(new moodle_url('/mod/teams/get_infos.php'));

global $DB;

if (isset($team_id)) {
    // We have a team id => return the list of the expected users
    $team = $DB->get_record_sql('SELECT * FROM {teams} WHERE ' . $DB->sql_compare_text('resource_teams_id') . ' = ' . $DB->sql_compare_text(':team') .' AND type = "team"', array('team' => $team_id));

    if ($team) {
        $population = teams_get_population($team, false);
        $response->members = ($team) ? $population->members : [];
        $response->owners = ($team) ? teams_get_owners(get_course($team->course), $team) : [];
        $response->members = array_values(array_diff($response->members, $response->owners)); // Not displays owners in the "members" array
    }
}
else {
    // No team id => List all the created teams
    $teams = [];
    $records = $DB->get_records('teams', array('type' => 'team'));
    foreach ($records as $record) {
        $teams[] = $record->resource_teams_id;
    }
    $response->teams = $teams;
}

// return JSON.
echo json_encode($response, JSON_PRETTY_PRINT);