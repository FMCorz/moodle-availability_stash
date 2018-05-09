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
 * @package    availability_stash
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var TPLCONDITIONS = '' +
    '<label>' +
    '    <span class="accesshide">{{get_string "condition" "availability_stash"}}</span>' +
    '    <select name="condition" class="form-control">' +
    '       {{#each conditions}}' +
    '           <option value="{{value}}">{{name}}</option>' +
    '       {{/each}}' +
    '    </select>' +
    '</label>';

var TPLQUANTITY = '' +
    '<label>' +
    '    <span class="accesshide">{{get_string "quantity" "availability_stash"}}</span>' +
    '    <input type="number" name="quantity" class="quantity form-control" min="0" value="1">' +
    '</label>';

var TPLOBJECTS = '' +
    '<label>' +
    '    <span class="accesshide">{{get_string "object" "availability_stash"}}</span>' +
    '    <select name="object" class="form-control">' +
    '       {{#each objects}}' +
    '           <option value="{{id}}">{{name}}</option>' +
    '       {{/each}}' +
    '    </select>' +
    '</label>';

var SELECTORS = {
    CONDITION: 'select[name="condition"]',
    QUANTITY: 'input[name="quantity"]',
    OBJECT: 'select[name="object"]'
};

M.availability_stash = M.availability_stash || {};

M.availability_stash.form = Y.merge(M.core_availability.plugin, {

    _conditions: null,
    _node: null,
    _objects: null,

    initInner: function(params) {
        this._conditions = params.conditions;
        this._objects = params.objects;
    },

    _getDefaultNode: function() {
        var node,
            stra = {},
            objectsNode,
            tpl;

        if (!this._node) {
            node = Y.Node.create('<div>');

            tpl = template = Y.Handlebars.compile(TPLCONDITIONS);
            stra.conditions = tpl({conditions: this._conditions});

            tpl = template = Y.Handlebars.compile(TPLQUANTITY);
            stra.quantity = tpl({});

            tpl = template = Y.Handlebars.compile(TPLOBJECTS);
            stra.objects = tpl({objects: this._objects});

            node.setHTML(M.util.get_string('theirstashcontains', 'availability_stash', stra));
            this._node = node;

            var availabilitynode;

            // Check for the new selector
            if (Y.one('.availability-field') !== null) {
                availabilitynode = Y.one('.availability-field');
            } else {
                // Use the old selector.
                availabilitynode = Y.one('#fitem_id_availabilityconditionsjson');
            }

            availabilitynode.delegate('change', function() {
                M.core_availability.form.update();
            }, '.availability_stash select, .availability_stash input');
        }

        return this._node.cloneNode(true);
    },

    getNode: function(json) {
        var node = this._getDefaultNode(),
            nodes = this._getNodes(node);

        if (typeof json.condition !== 'undefined') {
            nodes.condition.set('value', json.condition);
        }
        if (typeof json.quantity !== 'undefined') {
            nodes.quantity.set('value', json.quantity);
        }
        if (typeof json.objectid !== 'undefined') {
            // TODO What happens when the object was deleted?
            nodes.object.set('value', json.objectid);
        }

        return node;
    },

    _getNodes: function(parent) {
        return {
            condition: parent.one(SELECTORS.CONDITION),
            quantity: parent.one(SELECTORS.QUANTITY),
            object: parent.one(SELECTORS.OBJECT)
        };
    },

    fillValue: function(value, node) {
        var nodes = this._getNodes(node);

        value.condition = nodes.condition.get('value');
        value.quantity = nodes.quantity.get('value');
        value.objectid = nodes.object.get('value');
    },

    fillErrors: function(errors, node) {
        // Errors can only happen when the form is hacked, which we do not need to handle here.
    }
});
