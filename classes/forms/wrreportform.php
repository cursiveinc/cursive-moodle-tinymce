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
class wrreportform extends moodleform {
    /**
     * Tiny cursive plugin report form.
     */
    public function definition() {
        // Start dropdowns of course, quiz and user email search field in mform.

        $mform = &$this->_form;
        $attributes = '';
        $courseid = $this->_customdata['coursename'];
        $modules = self::get_modules($courseid);
        $options = ['multiple' => false, 'includefrontpage' => false];
        $mform->addElement('course', 'coursename', get_string('coursename', 'tiny_cursive'), $options);
        $mform->addRule('coursename', null, 'required', null, 'client');
        $options = [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'date' => 'Date',
        ];
        $mform->addElement('select', 'orderby', get_string('orderby', 'tiny_cursive'), $options, $attributes);
        $mform->setType('orderby', PARAM_RAW);
        $this->add_action_buttons(false, get_string('submit'));
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
            if (!empty($mform->_submitValues['coursename'])) {
                $data->coursename = $mform->_submitValues['coursename'];
            }
            if (!empty($mform->_submitValues['username'])) {
                $data->username = $mform->_submitValues['username'];
            }
            if (!empty($mform->_submitValues['modulename'])) {
                $data->modulename = $mform->_submitValues['modulename'];
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
        $mdetail[0] = 'All Modules';
        if ($courseid) {
            $modules = $DB->get_records_sql("SELECT id, instance  FROM {course_modules} WHERE course = $courseid ");
            foreach ($modules as $cm) {
                $modinfo = get_fast_modinfo($courseid);
                $cm = $modinfo->get_cm($cm->id);
                $getmodulename = get_coursemodule_from_id($cm->modname, $cm->id, 0, false, MUST_EXIST);
                $mdetail[$cm->id] = $getmodulename->name;
            }
        }

        return $mdetail;
    }
}
