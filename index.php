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
 * Displays information about all the teams modules in the requested course
 *
 * @package   mod_teams
 * @copyright 2021 Université Clermont Auvergne
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/teams/lib.php');
// For this type of page this is the course id.
$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$PAGE->set_url('/mod/teams/index.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');

$params = array(
    'context' => context_course::instance($course->id)
);

// Print the header.
$strplural = get_string("modulenameplural", "teams");
$PAGE->navbar->add($strplural);
$PAGE->set_title($strplural);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($strplural));
require_capability('mod/teams:view', $params['context']);

$teams = get_all_instances_in_course('teams', $course);
if (!$teams) {
    notice('There are no instances of teams resources', "../../course/view.php?id=$course->id");
    die;
}

// Print the table.
$table = new html_table();
$table->head = array(get_string('sectionname', 'format_'.$course->format), get_string('name'), get_string('type', 'core_search'));
$table->align = array('left', 'left', 'center');

foreach ($teams as $team) {
    if (has_capability('mod/teams:view', context_module::instance($team->coursemodule))) {
        if (!$team->visible) {
            // Show dimmed if the mod is hidden.
            $link = '<a class="dimmed" href="view.php?id=' . $team->coursemodule . '">' . format_string($team->name) . '</a>';
        } else {
            // Show normal if the mod is visible.
            $link = '<a href="view.php?id=' . $team->coursemodule . '">' . format_string($team->name) . '</a>';
        }
        $type = get_string('teams:' . $team->type, 'teams');
        $table->data[] = array(get_section_name($course, $team->section), $link, $type);
    }
}

echo html_writer::table($table);
echo $OUTPUT->footer();