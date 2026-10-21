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
 * Behat plugin generator.
 *
 * @package    availability_stash
 * @category   test
 * @copyright  2026 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_availability_stash_generator extends behat_generator_base {
    /**
     * Get the list of creatable entities.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'items' => [
                'datagenerator' => 'item',
                'required' => ['course', 'name'],
                'switchids' => ['course' => 'courseid'],
            ],
            'user items' => [
                'datagenerator' => 'user_item',
                'required' => ['user', 'item', 'quantity'],
                'switchids' => ['user' => 'userid', 'item' => 'itemid'],
            ],
            'restrictions' => [
                'datagenerator' => 'restriction',
                'required' => ['activity', 'item', 'condition', 'quantity'],
                'switchids' => ['activity' => 'cmid', 'item' => 'itemid'],
            ],
        ];
    }

    /**
     * Get the item ID.
     *
     * @param string $name Item name.
     * @return int
     */
    protected function get_item_id(string $name): int {
        global $DB;
        return (int) $DB->get_field('block_stash_items', 'id', ['name' => $name], MUST_EXIST);
    }
}
