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
 * Language file.
 *
 * @package    availability_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['condition'] = 'condition';
$string['description'] = 'Require an object in the user\'s stash to gain access.';
$string['exactly'] = 'exactly';
$string['lessthan'] = 'less than';
$string['morethan'] = 'more than';
$string['object'] = 'object';
$string['objectnnotrequiredtogetaccess'] = 'There is not {$a->condition} {$a->quantity}x \'{$a->object}\' in your stash.';
$string['objectnrequiredtogetaccess'] = 'There is {$a->condition} {$a->quantity}x \'{$a->object}\' in your stash.';
$string['pluginname'] = 'Stash availability';
$string['quantity'] = 'quantity';
$string['theirstashcontains'] = 'Their stash contains {$a->conditions} {$a->quantity} {$a->objects}';
$string['title'] = 'Stash object';
$string['unknowncondition'] = '[Unknown condition]';
$string['unknownobject'] = '[Unknown object]';
$string['youhavegota'] = 'You have got \'{$a->object}\'';
$string['youhavegotlessthanna'] = 'You have got less than {$a->quantity}x \'{$a->object}\'';
$string['youhavegotmorethanna'] = 'You have got more than {$a->quantity}x \'{$a->object}\'';
$string['youhavegotna'] = 'You have got {$a->quantity}x \'{$a->object}\'';
$string['youhaventgota'] = 'You haven\'t got \'{$a->object}\'';
$string['youhaventgotanya'] = 'You haven\'t got any \'{$a->object}\'';
$string['youhaventgotlessthanna'] = 'You haven\'t got less than {$a->quantity}x \'{$a->object}\'';
$string['youhaventgotmorethanna'] = 'You haven\'t got more than {$a->quantity}x \'{$a->object}\'';
$string['youhaventgotna'] = 'You haven\'t got {$a->quantity}x \'{$a->object}\'';
