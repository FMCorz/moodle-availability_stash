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

use block_stash\manager;

/**
 * Data generator.
 *
 * @package    availability_stash
 * @copyright  2026 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_stash_generator extends component_generator_base {
    /**
     * Create a Stash item.
     *
     * @param array $data Item data.
     * @return \block_stash\item
     */
    public function create_item($data) {
        $manager = manager::get($data['courseid'], true);
        $generator = $this->datagenerator->get_plugin_generator('block_stash');
        return $generator->create_item(['stash' => $manager->get_stash(), 'name' => $data['name'], 'detail' => '']);
    }

    /**
     * Set a user's item quantity, updating any existing empty inventory record.
     *
     * @param array $data User item data.
     * @return \block_stash\user_item
     */
    public function create_user_item($data) {
        $manager = manager::get_by_itemid($data['itemid']);
        $item = $manager->get_user_item($data['userid'], $data['itemid']);
        $item->set_quantity((int) $data['quantity']);
        $item->update();
        return $item;
    }

    /**
     * Create an activity restriction without using the JavaScript editor.
     *
     * @param array $data Restriction data.
     */
    public function create_restriction($data) {
        global $DB;

        $availability = json_encode([
            'op' => '&',
            'showc' => [false],
            'c' => [[
                'type' => 'stash',
                'condition' => $data['condition'],
                'quantity' => (int) $data['quantity'],
                'objectid' => (int) $data['itemid'],
            ]],
        ]);
        $DB->set_field('course_modules', 'availability', $availability, ['id' => $data['cmid']]);
    }
}
