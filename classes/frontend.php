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
 * Frontend file.
 *
 * @package    availability_stash
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stash;
defined('MOODLE_INTERNAL') || die();

use block_stash\manager;

/**
 * Frontend class.
 *
 * @package    availability_stash
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {

    /**
     * Decides whether this plugin should be available in a given course. The
     * plugin can do this depending on course or system settings.
     *
     * @param stdClass $course Course object
     * @param \cm_info $cm Course-module currently being edited (null if none)
     * @param \section_info $section Section currently being edited (null if none)
     * @return bool False when adding is disabled.
     */
    protected function allow_add($course, \cm_info $cm = null, \section_info $section = null) {
        $manager = manager::get($course->id);
        return $manager->is_enabled() && $manager->has_items() && $manager->can_manage();
    }

    /**
     * Gets a list of string identifiers required in JS.
     *
     * @return array Array of required string identifiers
     */
    protected function get_javascript_strings() {
        return [
            'condition',
            'object',
            'quantity',
            'theirstashcontains',
        ];
    }

    /**
     * Gets additional parameters for the plugin's initInner function.
     *
     * @param stdClass $course Course objecta
     * @param \cm_info $cm Course-module currently being edited (null if none)
     * @param \section_info $section Section currently being edited (null if none)
     * @return array Array of parameters for the JavaScript function
     */
    protected function get_javascript_init_params($course, \cm_info $cm = null, \section_info $section = null) {
        $manager = manager::get($course->id);
        $items = ($manager->is_enabled() && $manager->can_manage()) ? $manager->get_items() : [];

        return [(object) [
            'conditions' => [
                (object) ['value' => condition::EQUAL, 'name' => get_string('exactly', 'availability_stash')],
                (object) ['value' => condition::LESSTHAN, 'name' => get_string('lessthan', 'availability_stash')],
                (object) ['value' => condition::MORETHAN, 'name' => get_string('morethan', 'availability_stash')],
            ],
            'objects' => array_map(function($item) use ($manager) {
                // Yes, it's very unreadable...
                $name = format_string($item->get_name(), true, ['context' => $manager->get_context()]);
                return (object) ['id' => $item->get_id(), 'name' => $name];
            }, $items)
        ]];
    }

}
