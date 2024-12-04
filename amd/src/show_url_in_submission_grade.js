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
 * @module     tiny_cursive/show_url_in_submission_grade
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str", "core/templates", "./replay", "./analytic_button", "./analytic_events"], function(
    $,
    AJAX,
    str,
    templates,
    Replay,
    analyticButton,
    AnalyticEvents
) {
    const replayInstances = {};
    // eslint-disable-next-line
    window.video_playback = function (mid, filepath) {
        if (filepath !== '') {
            // $("#playback" + mid).show();
            const replay = new Replay(
                'content' + mid,
                filepath,
                10,
                false,
                'player_' + mid
            );
            replayInstances[mid] = replay;
        } else {
            // eslint-disable-next-line
            templates.render('tiny_cursive/no_submission').then(html => {
                $('#content' + mid).html(html);
            }).catch(e => window.console.error(e));
        }
        return false;

    };

    var usersTable = {
        init: function(scoreSetting, showcomment) {
            $(document).ready(function() {
                $('#page-mod-assign-grader').addClass('tiny_cursive_mod_assign_grader');
            });
            str
                .get_strings([
                    {key: "field_require", component: "tiny_cursive"},
                ])
                .done(function() {
                    usersTable.appendSubmissionDetail(scoreSetting, showcomment);
                });
        },
        appendSubmissionDetail: function(scoreSetting, showcomment) {
            $(document).ready(function($) {

                var divElement = $('.path-mod-assign [data-region="grade-panel"]')[0];
                var previousContextId = window.location.href;
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function() {

                        var currentContextId = window.location.href;
                        if (currentContextId !== previousContextId) {
                            window.location.reload();
                            previousContextId = currentContextId;
                        }
                    });
                });
                // Configuration of the observer:
                var config = {childList: true, subtree: true};
                // Start observing the target node for configured mutations
                observer.observe(divElement, config);

                let subUrl = window.location.href;
                let parm = new URL(subUrl);
                let userid = parm.searchParams.get('userid');
                var cmid = parm.searchParams.get('id');

                let args = {id: userid, modulename: "assign", 'cmid': cmid};
                let methodname = 'cursive_get_assign_grade_comment';
                let com = AJAX.call([{methodname, args}]);
                com[0].done(function(json) {
                    var data = JSON.parse(json);
                    var filepath = '';
                    if (data.data.filename) {
                        filepath = data.data.filename;
                    }

                    if (data.usercomment != 'comments' && parseInt(showcomment)) {

                        let container = $('<div>');
                        let row = $('<div>').addClass('row');
                        let chatbox = $('<div>').addClass('tiny_cursive-chatbox tiny_cursive-chatbox22 tiny_cursive-chatbox--tray');

                        let chatboxTitle = $('<div>').addClass('tiny_cursive-chatbox__title');
                        let titleH5 = $('<h5 class="text-white">').text("References");


                        chatboxTitle.append(titleH5);
                        let cbody = $('<div>').addClass('tiny_cursive-chatbox__body');
                        chatbox.append(chatboxTitle, cbody);
                        row.append(chatbox);
                        container.append(row);
                        $('div[data-region="grade-actions-panel"]').before(container);

                        var $chatbox = $('.tiny_cursive-chatbox'),
                            $chatboxTitle = $('.tiny_cursive-chatbox__title'),
                            $chatboxTitleClose = $('.tiny_cursive-chatbox__title__close');
                        $chatboxTitle.on('click', function() {
                            $chatbox.toggleClass('tiny_cursive-chatbox--tray');
                        });
                        $chatboxTitleClose.on('click', function(e) {
                            e.stopPropagation();
                            $chatbox.addClass('tiny_cursive-chatbox--closed');
                        });
                        $chatbox.on('transitionend', function() {
                            if ($chatbox.hasClass('tiny_cursive-chatbox--closed')) {
                                $chatbox.remove();
                            }
                        });

                        $(document).ready(function() {
                            $('div[data-region="grade-panel"]').append('<div class="dropdown">');
                            data.usercomment.forEach(element => {
                                cbody.append('<div class="border p-2 shadow-sm">' + element.usercomment + '</div>');

                            });
                        });
                    }

                    let analyticButtonDiv = document.createElement('div');
                    analyticButtonDiv.append(analyticButton(userid));
                    analyticButtonDiv.classList.add('text-center', 'mt-2');

                    $('div[data-region="grade-actions"]').before(analyticButtonDiv);

                    $('div[data-region="review-panel"]').addClass('cursive_review_panel_path_mod_assign');

                    $('div[data-region="grading-navigation-panel"]').addClass('cursive_grading-navigation-panel_path_mod_assign');

                    $('div[data-region="grade-panel"]').addClass('cursive_grade-panel_path_mod_assign');

                    $('div[data-region="grade-actions-panel"]').addClass('cursive_grade-actions-panel_path_mod_assign');


                    let myEvents = new AnalyticEvents();
                    var context = {
                        tabledata: data.data,
                        formattime: myEvents.formatedTime(data.data),
                        page: scoreSetting,
                        userid: userid,
                    };

                    let authIcon = myEvents.authorshipStatus(data.data.first_file, data.data.score, scoreSetting);
                    myEvents.createModal(userid, context, '', authIcon);
                    myEvents.analytics(userid, templates, context, '', replayInstances, '', authIcon);
                    myEvents.checkDiff(userid, data.data.file_id, '', replayInstances);
                    myEvents.replyWriting(userid, filepath, '', replayInstances);
                    myEvents.quality(userid, templates, context, '', replayInstances, M.cfg.contextInstanceId);

                });
                return com.usercomment;
            });
        },
    };
    return usersTable;
});


