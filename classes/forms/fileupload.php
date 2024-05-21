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

namespace MoodleHQ\editor\tiny\plugins\cursive\classes\forms;
use moodleform;

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
class fileupload extends moodleform {
    /**
     * Tiny cursive plugin.
     *
     * @package tiny_cursive
     * @copyright  CTI <info@cursivetechnology.com>
     * @author eLearningstack
     * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    public function definition() {
        global $DB, $USER;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'draftid', ''); // Add elements to your form.
        $mform->addRule('draftid', get_string('maximumchars', '', 512), 'maxlength', 255, 'client');
    }
}
