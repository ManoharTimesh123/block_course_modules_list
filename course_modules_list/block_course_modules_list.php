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
 * Course summary block
 *
 * @package    block_course_summary
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 class block_course_modules_list extends block_base {
     public function init() {
         $this->title = get_string('course_modules_list', 'block_course_modules_list');
     }
     public function applicable_formats() {
        return array('course' => true);
    }
     public function get_content() {
         global $USER, $DB, $PAGE;
         $course = $PAGE->course;
         $modules = $DB->get_records_sql("
             SELECT cm.id, m.name, cmc.timemodified, cm.added AS creation, cmc.completionstate AS completionstatus
             FROM {course_modules} cm
             JOIN {modules} m ON cm.module = m.id
             LEFT JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid AND cmc.userid = :userid
             LEFT JOIN {course_sections} cs ON cm.section = cs.id
             WHERE cm.course = :courseid
             ORDER BY cs.section ASC, cm.id ASC
         ", array('userid' => $USER->id, 'courseid' => $course->id));
         $content = '';
         foreach ($modules as $module) {
             $cmid = $module->id;
             $name = $module->name;
             $date = date('d-m-Y', $module->creation);
             $completionstatus = '';
             if (!empty($module->timemodified)) {
              
                 $completionstatus = ' - Completed on ' . date('d-m-Y', $module->timemodified);
             }
             $content .= "<a href='" . new moodle_url('/mod/' . $name . '/view.php', array('id' => $cmid)) . "'>" . $cmid . '-' . $name . '-' . $date . $completionstatus . "</a><br>";
         }
        // Create empty content.
        $this->content = new stdClass();
         if($content) {
            
            $this->content->text = $content;
         } else {
             $this->content->text = '';
         }
         return $this->content;
     }

     /**
     * This block shouldn't be added to a page if the completion tracking advanced feature is disabled.
     *
     * @param moodle_page $page
     * @return bool
     */
    public function can_block_be_added(moodle_page $page): bool {
        global $CFG;

        return $CFG->enablecompletion;
    }
 }
 
