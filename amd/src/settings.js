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
 * @module     tiny_cursive/settings
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author Brain Station 23 <elearning@brainstation-23.com>
 */

define(["jquery", "core/str"], function(
  $,
  str
) {
  var usersTable = {
    init: function(showcomments, userRole) {
      str
        .get_strings([
          {key: "tiny_cursive", component: "tiny_cursive"},
        ])
        .done(function() {
          usersTable.getToken(showcomments, userRole);
        });
    },
    getToken: function(showcomments, userRole) {
      $(function() {
        var html = "<div id='body' class='body'>";
        $("body").append(html);
        $('#body').prop("class", userRole);
        if (showcomments == 1) {
          $('#body').prop("class", 'intervention ' + userRole);
        }
      });
    },
  };
  return usersTable;
});