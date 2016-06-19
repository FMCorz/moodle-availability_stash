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
 * Condition file.
 *
 * @package    availability_stash
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_stash;
defined('MOODLE_INTERNAL') || die();

use block_stash\manager;
use block_stash\stash;
use moodle_exception;
use restore_dbops;
use backup;
use base_logger;

/**
 * Condition class.
 *
 * @package    availability_stash
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    const EQUAL = '==';
    const LESSTHAN = '<';
    const MORETHAN = '>';

    protected $condition = self::EQUAL;
    protected $quantity = 1;
    protected $objectid = null;

    /**
     * Constructor.
     *
     * @param stdClass $structure Saved data.
     */
    public function __construct($structure) {
        if (isset($structure->condition)) {
            $this->condition = $structure->condition;
        }
        if (isset($structure->quantity)) {
            $this->quantity = $structure->quantity;
        }
        if (isset($structure->objectid)) {
            $this->objectid = $structure->objectid;
        }
    }

    /**
     * Determines whether a particular item is currently available this condition.
     *
     * @param bool $not Set true if we are inverting the condition.
     * @param info $info Item we're checking.
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user).
     * @param int $userid User ID to check availability for.
     * @return bool True if available.
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        $available = false;

        if (!$this->objectid) {
            // We got a problem, this shouldn't happen. Silently ignore.
            return $available;
        }

        $manager = manager::get($info->get_course()->id);
        if (!$manager->is_enabled()) {
            return $available;
        }

        try {
            $item = $manager->get_user_item($userid, $this->objectid);
        } catch (moodle_exception $e) {
            // There was an error, probably the item was deleted but the rule is still present.
            debugging($e->getMessage(), DEBUG_DEVELOPER);
            return $available;
        }

        $quantity = $item->get_quantity();
        $requiredquantity = $this->quantity;
        if ($this->condition == self::EQUAL && $quantity == $requiredquantity) {
            $available = true;
        } else if ($this->condition == self::LESSTHAN && $quantity < $requiredquantity) {
            $available = true;
        } else if ($this->condition == self::MORETHAN && $quantity > $requiredquantity) {
            $available = true;
        }

        return $available;
    }

    /**
     * Obtains a string describing this restriction.
     *
     * Used to obtain information that is displayed to
     * students if the activity is not available to them, and for staff to see
     * what conditions are.
     *
     * @param bool $full Set true if this is the 'full information' view.
     * @param bool $not Set true if we are inverting the condition.
     * @param info $info Item we're checking.
     * @return string Information string (for admin) about all restrictions on this item.
     */
    public function get_description($full, $not, \core_availability\info $info) {
        if ($this->condition == self::EQUAL) {
            if ($this->quantity == 0) {
                $strid = $not ? 'youhavegota' : 'youhaventgota';
            } else if ($this->quantity == 1) {
                $strid = $not ? 'youhaventgotna' : 'youhavegota';
            } else {
                $strid = $not ? 'youhaventgotna' : 'youhavegotna';
            }

        } else if ($this->condition == self::LESSTHAN) {
            if ($this->quantity <= 1) {
                $strid = $not ? 'youhavegota' : 'youhaventgotanya';
            } else {
                $strid = $not ? 'youhaventgotlessthanna' : 'youhavegotlessthanna';
            }

        } else if ($this->condition == self::MORETHAN) {
            if ($this->quantity == 0) {
                $strid = $not ? 'youhaventgotanya' : 'youhavegota';
            } else {
                $strid = $not ? 'youhaventgotmorethanna' : 'youhavegotmorethanna';
            }

        } else {
            return get_string('unknowncondition', 'availability_stash');

        }

        return get_string($strid, 'availability_stash', [
            'quantity' => $this->quantity,
            'object' => $this->get_object_name($info),
        ]);
    }

    /**
     * Obtains a representation of the options of this condition as a string, for debugging.
     *
     * @return string Text representation of parameters.
     */
    protected function get_debug_string() {
        return json_encode([
            'condition' => $this->condition,
            'quantity' => $this->quantity,
            'objectid' => $this->objectid,
        ]);
    }

    /**
     * Get a generic description.
     *
     * @param bool $not Set true if we are inverting the condition.
     * @return string
     */
    protected function get_generic_description($not) {
        $a = [
            'condition' => $this->get_readable_condition(),
            'quantity' => $this->quantity,
            'object' => $this->get_object_name($info),
        ];
        $stringid = $not ? 'objectnnotrequiredtogetaccess' : 'objectnrequiredtogetaccess';
        return get_string($stringid, 'availability_stash', $a);
    }

    /**
     * Get the name of the object in this rule.
     *
     * @return string
     */
    protected function get_object_name(\core_availability\info $info) {
        $manager = manager::get($info->get_course()->id);
        try {
            $item = $manager->get_item($this->objectid);
        } catch (moodle_exception $e) {
            // Whoops.
            return get_string('unknownobject', 'availability_stash');
        }
        return format_string($item->get_name(), true, ['context' => $manager->get_context()]);
    }

    /**
     * Get the readable condition.
     *
     * @return string
     */
    protected function get_readable_condition() {
        if ($this->condition == self::EQUAL) {
            return get_string('exactly', 'availability_stash');
        }
        if ($this->condition == self::LESSTHAN) {
            return get_string('lessthan', 'availability_stash');
        }
        if ($this->condition == self::MORETHAN) {
            return get_string('morethan', 'availability_stash');
        }
        return get_string('unknowncondition', 'availability_stash');
    }

    /**
     * Saves tree data back to a structure object.
     *
     * @return stdClass Structure object (ready to be made into JSON format).
     */
    public function save() {
        return (object) [
            'condition' => $this->condition,
            'quantity' => $this->quantity,
            'objectid' => $this->objectid,

            // Grrr... the type needs to be added manually whereas it is not needed in JS.
            // If not added the structure becomes invalid as it is missing the type. That
            // is particularly annoying when dealing with backup and restore.
            'type' => $this->get_type(),
        ];
    }

    /**
     * Update after restore.
     *
     * @param int $restoreid Restore ID.
     * @param int $courseid Course ID.
     * @param base_logger $logger Backup logger.
     * @param string $name Thing name.
     * @return bool
     */
    public function update_after_restore($restoreid, $courseid, base_logger $logger, $name) {
        $rec = restore_dbops::get_backup_ids_record($restoreid, 'block_stash_item', $this->objectid);
        if (!$rec || !$rec->newitemid) {

            // Check if we happen to be restoring on the same course, if so nothing to do.
            // TODO Make that more solid as IDs could clash across sites.
            if (stash::course_has_item($courseid, $this->objectid)) {
                return false;
            }

            // Otherwise it's a warning.
            $this->objectid = null;
            $logger->process('Restored item (' . $name . ') has availability condition on stash object that was not restored',
                backup::LOG_WARNING);

        } else {
            $this->objectid = (int) $rec->newitemid;
        }

        return true;
    }

}
