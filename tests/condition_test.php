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

namespace availability_stash;

use block_stash\manager;

/**
 * Tests for the stash availability condition.
 *
 * @package    availability_stash
 * @copyright  2026 ISB Bayern
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \availability_stash\condition
 */
final class condition_test extends \advanced_testcase {
    /**
     * A negated condition must invert the result, a plain condition must not.
     */
    #[\PHPUnit\Framework\Attributes\Group('baseline')]
    public function test_is_available_honours_not(): void {
        global $CFG;
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $withitem = $this->getDataGenerator()->create_user();
        $withoutitem = $this->getDataGenerator()->create_user();
        $generator = $this->getDataGenerator()->get_plugin_generator('block_stash');
        $stash = $generator->create_stash(['courseid' => $course->id]);
        $item = $generator->create_item(['stash' => $stash]);
        $generator->create_user_item(['item' => $item, 'userid' => $withitem->id, 'quantity' => 1]);
        manager::get($course->id, true)->set_enabled();

        $condition = new condition((object) ['condition' => '==', 'quantity' => 0, 'objectid' => $item->get_id()]);
        $info = new \core_availability\mock_info($course);

        // Verification: without negation the result is unchanged.
        $this->assertTrue($condition->is_available(false, $info, false, $withoutitem->id));
        $this->assertFalse($condition->is_available(false, $info, false, $withitem->id));

        // Violation: a negated restriction must not release the item to the excluded users.
        $this->assertFalse($condition->is_available(true, $info, false, $withoutitem->id));
        $this->assertTrue($condition->is_available(true, $info, false, $withitem->id));
    }
}
