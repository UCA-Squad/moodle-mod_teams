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
 * Teams module main user interface
 *
 * @package    mod_teams
 * @copyright  2020 Université Clermont Auvergne
 */

require_once('../../config.php');
require_once("lib.php");
require_once("$CFG->dirroot/mod/url/locallib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot.'/vendor/autoload.php');

$id = required_param('id', PARAM_INT); // Course Module ID
$u = optional_param('u', 0, PARAM_INT); // URL instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($u) {  // Two ways to specify the module
    $resource = $DB->get_record('teams', array('id' => $u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('teams', $resource->id, $resource->course, false, MUST_EXIST);
} else {
    $cm = get_coursemodule_from_id('teams', $id, 0, false, MUST_EXIST);
    $resource = $DB->get_record('teams', array('id' => $cm->instance), '*', MUST_EXIST);
}
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/teams:view', $context);

$params = array(
    'context' => $context,
    'objectid' => $resource->id
);

$PAGE->set_url('/mod/teams/view.php', array('id' => $cm->id));

$office = get_office();
if ($resource->type == "meeting") { // Online meeting.
    if ($resource->opendate != 0) {
        if(strtotime("now") < $resource->opendate && !has_capability('mod/teams:addinstance', $context)) {
            notice(sprintf(get_string('meetingnotavailable', 'mod_teams'), teams_print_details_dates($resource, "text")) , new moodle_url('/course/view.php', array('id' => $cm->course)));
            die;
        }
    }
    if($resource->closedate != 0 && strtotime("now") > $resource->closedate && !has_capability('mod/teams:addinstance', $context)) {
        notice(sprintf(get_string('meetingnotavailable', 'mod_teams'), teams_print_details_dates($resource, "text")) , new moodle_url('/course/view.php', array('id' => $cm->course)));
        die;
    }

    if (!filter_var($resource->externalurl, FILTER_VALIDATE_URL)) { // Incorrect Teams meeting url ?
        notice(get_string('meetingnotfound', 'mod_teams'), new moodle_url('/course/view.php', array('id' => $cm->course)));
        die;
    }
} else {
    try {
        $team = $office->readTeam($resource->resource_teams_id);
    }
    catch (Exception $e) {
        // Team not found.
        notice(get_string('teamnotfound', 'mod_teams'), new moodle_url('/course/view.php', array('id' => $cm->course)));
        die;
    }
}

$event = \mod_teams\event\course_module_viewed::create($params);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('teams', $resource);
$event->trigger();

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Make sure URL exists before generating output - some older sites may contain empty urls
// Do not use PARAM_URL here, it is too strict and does not support general URIs!
$exturl = trim($resource->externalurl);
if (empty($exturl) or $exturl === 'http://') {
    url_print_header($resource, $cm, $course);
    url_print_heading($resource, $cm, $course);
    url_print_intro($resource, $cm, $course);
    notice(get_string('invalidstoredurl', 'url'), new moodle_url('/course/view.php', array('id' => $cm->course)));
    die;
}
unset($exturl);

$displaytype = url_get_final_display_type($resource);
if ($displaytype == RESOURCELIB_DISPLAY_OPEN) {
    // For 'open' links, we always redirect to the content - except if the user
    // just chose 'save and display' from the form then that would be confusing
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'modedit.php') === false) {
        $redirect = true;
    }
}

if ($redirect) {
    // coming from course page or url index page,
    // the redirection is needed for completion tracking and logging
    $fullurl = str_replace('&amp;', '&', url_get_full_url($resource, $cm, $course));

    if (!course_get_format($course)->has_view_page()) {
        // If course format does not have a view page, add redirection delay with a link to the edit page.
        // Otherwise teacher is redirected to the external URL without any possibility to edit activity or course settings.
        $editurl = null;
        if (has_capability('moodle/course:manageactivities', $context)) {
            $editurl = new moodle_url('/course/modedit.php', array('update' => $cm->id));
            $edittext = get_string('editthisactivity');
        } else if (has_capability('moodle/course:update', $context->get_course_context())) {
            $editurl = new moodle_url('/course/edit.php', array('id' => $course->id));
            $edittext = get_string('editcoursesettings');
        }
        if ($editurl) {
            redirect($fullurl, html_writer::link($editurl, $edittext)."<br/>".
                    get_string('pageshouldredirect'), 10);
        }
    }
    redirect($fullurl);
}

// Display options. We use some functions of the url module.
switch ($displaytype) {
    case RESOURCELIB_DISPLAY_EMBED:
        url_display_embed($resource, $cm, $course);
        break;
    case RESOURCELIB_DISPLAY_FRAME:
        url_display_frame($resource, $cm, $course);
        break;
    default:
        teams_print_workaround($resource, $cm, $course); // Specific display to add custom information.
        break;
}
