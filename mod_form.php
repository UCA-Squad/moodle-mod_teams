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
 * Teams configuration form
 *
 * @package   mod_teams
 * @copyright 2020 Université Clermont Auvergne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/url/locallib.php');
require_once($CFG->dirroot.'/mod/teams/lib.php');
require_once($CFG->dirroot.'/vendor/autoload.php');

class mod_teams_mod_form extends moodleform_mod
{
    /**
     * Form construction.
     * @throws Exception
     * @throws coding_exception
     */
    function definition() {
        global $CFG, $USER, $COURSE, $OUTPUT;
        $mform = $this->_form;
        $error = null;

        $office = get_office();
        try {
            $userId = $office->getUserId($USER->email);
        }
        catch (Throwable $th) {
            $error = $th->getMessage();
            $userId = null;
        }

        $has_account = (!empty($userId));

        if ($has_account) {
            // Current user had a correct account.
            $edit_ok = true;
            $default_type = ($this->current->id) ? $this->current->type : "team";
            $teamexists = true;
            if (!empty($this->current->id)) {
                // Resource mod edition
                if ($this->current->type != "meeting") {
                    try {
                        $team = $office->readTeam($this->current->resource_teams_id);
                        $teamexists = true;
                    } catch (Exception $e) {
                        $teamexists = false;
                    }

                    if ($this->current->population == "groups") {
                        $this->current->groups = json_decode($this->current->selection);
                    } else {
                        if (!teams_is_owner($this->current, $USER)) {
                            $edit_ok = false;
                        }
                        if ($this->current->population == "users") {
                            $this->current->users = json_decode($this->current->selection);
                        }
                    }
                }
                // Default name, if prefix is used we do not display it here.
                if (strpos($this->current->name, get_string('teams:' . $default_type . '_prefix', 'mod_teams')) == 0) {
                    $this->current->name = str_replace(get_string('teams:' . $default_type . '_prefix', 'mod_teams'), '', $this->current->name);
                }
            }

            if ($edit_ok) {
                if ($teamexists) {
                    $mform->addElement('header', 'general', get_string('general'));

                    $radioarray = array();
                    $radioarray[] = $mform->createElement('radio', 'type', '',
                        "<div>" . get_string('teams:team', 'mod_teams') . '<br/>' . html_writer::tag('img', '', array('alt' => get_string('teams:team', 'mod_teams'), 'src' => $OUTPUT->image_url('i/cohort', 'core'), 'style' => 'height: 96px;margin-right: 50px;')) . "</div>",
                        'team');
                    $radioarray[] = $mform->createElement('radio', 'type', '',
                        "<div>" . get_string('teams:meeting', 'mod_teams') . '<br/>' . html_writer::tag('img', '', array('alt' => get_string('teams:meeting', 'mod_teams'), 'src' => $OUTPUT->image_url('i/calendar', 'core'), 'style' => 'height: 96px;')) . "</div>",
                        'meeting');
                    $mform->addGroup($radioarray, 'type', get_string('teams:type', 'mod_teams'), array(' '), false);
                    $mform->addHelpButton('type', 'teams:type', 'mod_teams');
                    $mform->setDefault('type', $default_type);
                    $mform->disabledIf('type', 'resource_teams_id', 'neq', ''); // Disable if we edit the resource

                    $mform->addElement('text', 'name', get_string('teams:name', 'mod_teams'), 'size=80');
                    $mform->addRule('name', null, 'required', null, 'client');
                    $mform->setType('name', PARAM_TEXT);
                    $mform->addHelpButton('name', 'teams:name', 'mod_teams');

                    $mform->addElement('hidden', 'resource_teams_id');
                    $mform->setType('resource_teams_id', PARAM_TEXT);
                    if ($this->current->id) {
                        $mform->setDefault('resource_teams_id', $this->current->resource_teams_id);
                    }

                    $this->standard_intro_elements();
                    $element = $mform->getElement('introeditor');
                    $attributes = $element->getAttributes();
                    $attributes['rows'] = 5;
                    $element->setAttributes($attributes);

                    /* -- Team creation -- */
                    $groupteamitems = array();
                    $groupteamitems[] =& $mform->createElement('html', get_string('teams:desc', 'mod_teams'));
                    $groupteam = $mform->createElement('group', 'group_team', false, $groupteamitems, null, false);
                    $mform->addElement($groupteam);

                    $populationslist = [
                        'course' => get_string('teams:population_all', 'mod_teams'),
                        'groups' => get_string('teams:population_groups', 'mod_teams'),
                        'users' => get_string('teams:population_users', 'mod_teams'),
                    ];

                    $coursecontext = context_course::instance($COURSE->id);
                    $groups = [];
                    $enrolled = [];
                    if (count(groups_get_all_groups($COURSE->id)) > 0) {
                        foreach (groups_get_all_groups($COURSE->id) as $group) {
                            $groups[$group->id] = $group->name;
                        }
                    } else {
                        unset($populationslist['groups']);
                    }
                    foreach (get_enrolled_users($coursecontext, 'mod/teams:view') as $user) {
                        $enrolled[$user->email] = fullname($user);
                    }

                    $mform->addElement('select', 'population', get_string('teams:population', 'mod_teams'), $populationslist);
                    $mform->addHelpButton('population', 'teams:population', 'mod_teams');

                    $mform->addElement('searchableselector', 'groups', get_string('group'), $groups, array('multiple'));
                    $mform->hideIf('groups', 'population', 'eq', 'course');
                    $mform->hideIf('groups', 'population', 'eq', 'users');

                    $mform->addElement('searchableselector', 'users', get_string('users'), $enrolled, array('multiple'));
                    $mform->hideIf('users', 'population', 'eq', 'course');
                    $mform->hideIf('users', 'population', 'eq', 'groups');

                    $mform->addElement('advcheckbox', 'enrol_managers', get_string('teams:enrol_managers', 'mod_teams'));
                    $mform->setDefault('enrol_managers', false);
                    $mform->hideIf('enrol_managers', 'population', 'eq', 'course');
                    $mform->addHelpButton('enrol_managers', 'teams:enrol_managers', 'mod_teams');

                    $mform->hideIf('group_team', 'type', 'eq', 'meeting');
                    $mform->hideIf('population', 'type', 'eq', 'meeting');
                    $mform->hideIf('groups', 'type', 'eq', 'meeting');
                    $mform->hideIf('users', 'type', 'eq', 'meeting');
                    $mform->hideIf('enrol_managers', 'type', 'eq', 'meeting');

                    /* -- Meeting creation -- */
                    $enableopengroup = array();
                    $enableopengroup[] =& $mform->createElement('date_time_selector', 'opendate', '');
                    $enableopengroup[] =& $mform->createElement('checkbox', 'useopendate', get_string('enable', 'moodle'));
                    $mform->addGroup($enableopengroup, 'enableopengroup', get_string('teams:opendate', 'mod_teams'), ' ', false);
                    $mform->addHelpButton('enableopengroup', 'teams:opendate', 'mod_teams');
                    $mform->disabledIf('enableopengroup', 'useopendate', 'notchecked');

                    $enableclosegroup = array();
                    $enableclosegroup[] =& $mform->createElement('date_time_selector', 'closedate', '');
                    $enableclosegroup[] =& $mform->createElement('checkbox', 'useclosedate', get_string('enable', 'moodle'));
                    $enableclosegroup[] =& $mform->addGroup($enableclosegroup, 'enableclosegroup', get_string('teams:closedate', 'mod_teams'), ' ', false);
                    $mform->addHelpButton('enableclosegroup', 'teams:closedate', 'mod_teams');
                    $mform->disabledIf('enableclosegroup', 'useclosedate', 'notchecked');

                    $mform->hideIf('enableopengroup', 'type', 'eq', 'team');
                    $mform->hideIf('enableclosegroup', 'type', 'eq', 'team');

                    $dateitems = array();
                    $dateitems[] =& $mform->createElement('html', get_string('teams:dates_help', 'mod_teams'));
                    $dategroup = $mform->createElement('group', 'group_date', false, $dateitems, null, false);
                    $mform->addElement($dategroup);
                    $mform->hideIf('group_date', 'type', 'eq', 'team');

                    $this->standard_coursemodule_elements();
                    $this->add_action_buttons();
                } else {
                    // Resource not found (edit mode).
                    $this->standard_hidden_coursemodule_elements();
                    notice(get_string('teamnotfound', 'mod_teams'), new moodle_url('/course/view.php', array('id' => $this->current->course)));
                    die;
                }
            } else {
                // The current user does not have the rights to edit the resource info.
                $this->standard_hidden_coursemodule_elements();
                $mform->addElement('html', '<div class="alert alert-danger">' . get_string('teams:no_owner', 'teams', $error) . '</div>');
                $mform->addElement('cancel', '', get_string('teams:back', 'teams'));
            }
        }
        else {
            // The current user does not have an Azure AD account -> impossible to add a teams instance.
            $this->standard_hidden_coursemodule_elements();
            $identifer = ($error) ? 'teams:error' : 'teams:noaccount';
            $mform->addElement('html', '<div class="alert alert-danger">' . get_string($identifer, 'teams', $error) . '</div>');
            $mform->addElement('cancel', '', get_string('teams:back', 'teams'));
        }
    }

    /**
     * Form validation.
     * @param array $data
     * @param array $files
     * @return array
     */
    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if ($data['type'] == "team") { // Teams creation.
            // Checks if at least one group is selected.
            if (isset($data['population']) && $data['population'] == "groups" && count($data['groups']) < 1) {
                $errors['groups'] = get_string('teams:error_groups', 'mod_teams');
            }

            // Checks if at least one user is selected.
            if (isset($data['population']) && $data['population'] == "users" && count($data['users']) < 1) {
                $errors['users'] = get_string('teams:error_users', 'mod_teams');
            }
        } else { // Online meeting creation.
            // Checks dates choice consistency.
            if (isset($data['useopendate']) && isset($data['useclosedate']) && $data['closedate'] < $data['opendate']) {
                $errors['enableclosegroup'] = get_string('teams:error_dates', 'mod_teams');
            }
        }

        return $errors;
    }

    /**
     * Fix default values for date fields.
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        global $DB;
        if (empty($defaultvalues['opendate'])) {
            $defaultvalues['useopendate'] = 0;
        } else {
            $defaultvalues['useopendate'] = 1;
        }
        if (empty($defaultvalues['closedate'])) {
            $defaultvalues['useclosedate'] = 0;
        } else {
            $defaultvalues['useclosedate'] = 1;
        }
    }

}
