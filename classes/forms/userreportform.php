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
 * Tiny cursive plugin.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author eLearningstack
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');

/**
 * Tiny cursive plugin.
 *
 * @package tiny_cursive
 * @copyright  CTI <info@cursivetechnology.com>
 * @author eLearningstack
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userreportform extends moodleform {
    /**
     * Tiny cursive plugin user report form.
     */
    public function definition() {
        // Start dropdowns of course, quiz and user email search field in mform.
        global $PAGE;
        $mform = &$this->_form;
        $attributes = '';
        $courseid = $this->_customdata['courseid'];
        $users = self::get_user($courseid);
        $modules = self::get_modules($courseid);
        $options = ['multiple' => false, 'includefrontpage' => false];
        $mform->addElement('course', 'courseid', get_string('coursename', 'tiny_cursive'), $options);
        if ($courseid) {
            $mform->setDefault('courseid', $courseid);
        }

        $mform->addRule('courseid', null, 'required', null, 'client');
        $mform->addElement('select', 'moduleid', get_string('modulename', 'tiny_cursive'), $modules, $attributes);
        $mform->setType('moduleid', PARAM_TEXT);
        $mform->addElement('select', 'userid', get_string('userename', 'tiny_cursive'), $users, $attributes);
        $mform->setType('userid', PARAM_TEXT);
        $options = [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'date' => 'Date',
        ];
        $mform->addElement('select', 'orderby', get_string('orderby', 'tiny_cursive'), $options, $attributes);
        $mform->setType('orderby', PARAM_TEXT);
        $this->add_action_buttons(false, get_string('submit'));

        if (!is_siteadmin()) {
            $PAGE->requires->js_init_code("
            const courseNameElement = document.querySelector('#id_courseid option[selected]');
            if (!courseNameElement) {
                window.history.back();
            };
            const forumTestingText = courseNameElement.textContent.trim();
            const h5Element = document.createElement('div');
            h5Element.classList.add('row', 'align-items-center', 'pb-4');
            const label = document.createElement('label');
            label.textContent = '" . get_string('coursename', 'tiny_cursive') . "';
            label.classList.add('col-md-3', 'col-form-label', 'd-flex', 'pb-0', 'pr-md-0');
            h5Element.appendChild(label);
            const label2 = document.createElement('label');
            label2.textContent = forumTestingText;
            label2.classList.add('col-md-9', 'col-form-label', 'd-flex', 'pb-0', 'pr-md-0');
            h5Element.appendChild(label2);
            const moduleIdElement = document.getElementById('fitem_id_moduleid');
            const parentElement = moduleIdElement.parentElement;
            parentElement.insertBefore(h5Element, moduleIdElement);
            document.getElementById('fitem_id_courseid').style.display = 'none';
        ");
        }
    }

    /**
     * Tiny cursive plugin user report form data.
     *
     * @return object
     */
    public function get_data() {
        $data = parent::get_data();
        if (!empty($data)) {
            $mform = &$this->_form;
            // Add the studentid properly to the $data object.
            if (!empty($mform->_submitValues['courseid'])) {
                $data->courseid = $mform->_submitValues['courseid'];
            }
            if (!empty($mform->_submitValues['userid'])) {
                $data->userid = $mform->_submitValues['userid'];
            }
            if (!empty($mform->_submitValues['moduleid'])) {
                $data->moduleid = $mform->_submitValues['moduleid'];
            }
            if (!empty($mform->_submitValues['orderby'])) {
                $data->orderby = $mform->_submitValues['orderby'];
            }
        }
        return $data;
    }

    /**
     * Tiny cursive plugin get all modules.
     *
     * @param integer $courseid
     * @return array
     */
    public function get_modules($courseid) {
        // Get users dropdown.
        global $DB;
        $mdetail = [];
        $mdetail[0] = get_string('allmodule', 'tiny_cursive');
        if ($courseid) {
            $sql = "SELECT id, instance
                      FROM {course_modules}
                     WHERE course = :courseid";
            $modules = $DB->get_records_sql($sql, ['courseid' => $courseid]);
            foreach ($modules as $cm) {
                $modinfo = get_fast_modinfo($courseid);
                $cm = $modinfo->get_cm($cm->id);
                $getmodulename = get_coursemodule_from_id($cm->modname, $cm->id, 0, false, MUST_EXIST);
                $mdetail[$cm->id] = $getmodulename->name;
            }
        }
        return $mdetail;
    }

    /**
     * Tiny cursive plugin get all users.
     *
     * @param integer $courseid
     * @return array
     */
    public function get_user($courseid) {
        global $DB;
        $udetail = [];

        $udetail[0] = get_string('alluser', 'tiny_cursive');

        if (!empty($courseid)) {
            $sql = "SELECT ue.id, u.id AS userid, u.firstname, u.lastname
                      FROM {enrol} e
                INNER JOIN {user_enrolments} ue ON e.id = ue.enrolid
                INNER JOIN {user} u ON u.id = ue.userid
                     WHERE e.courseid = :courseid
                           AND u.id != 1";
            $users = $DB->get_records_sql($sql, ['courseid' => $courseid]);

            foreach ($users as $user) {
                $udetail[$user->userid] = $user->firstname . ' ' . $user->lastname;
            }
        }

        return $udetail;
    }
}
