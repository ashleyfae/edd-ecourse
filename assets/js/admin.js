jQuery(document).ready(function ($) {

    // Tooltips
    $('.edd-ecourse-tip').tooltip({
        content: function () {
            return $(this).prop('title');
        },
        tooltipClass: 'edd-ecourse-tooltip',
        position: {
            my: 'center top',
            at: 'center bottom+10',
            collision: 'flipfit'
        },
        hide: false,
        show: false
    });

    /**
     * E-Courses
     */
    var EDD_ECourse = {

        /**
         * Initialize all the things.
         */
        init: function () {
            this.add();
            this.remove();
            this.editTitle();
            this.editSlug();

            $('#course_status').on('change', this.maybeHideDate);
            $('#ecourse-save-status').on('click', this.updateCourseDetails);
        },

        /**
         * Add E-Course
         */
        add: function () {

            $('#edd-ecourse-add').on('submit', 'form', function (e) {

                e.preventDefault();

                var form = $(this);

                // Add spinner.
                form.append('<span class="spinner is-active"></span>');

                // Disable submit.
                form.find('button').attr('disabled', true);

                var data = {
                    action: 'edd_ecourse_add_course',
                    course_name: $('#edd-ecourse-name-new').val(),
                    nonce: $('#edd_ecourse_add_course_nonce').val()
                };

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {

                        console.log(response);

                        if (true === response.success) {

                            // Remove spinner.
                            form.find('.spinner').remove();

                            // Re-enable submit.
                            form.find('button').attr('disabled', false);

                            // Clear course title field.
                            $('#edd-ecourse-name-new').val('');

                            // Load template data.
                            var courseTemplate = wp.template('edd-ecourse-new');
                            $('#edd-ecourse-grid').prepend(courseTemplate(response.data));

                        } else {

                            if (window.console && window.console.log) {
                                console.log(response);
                            }

                        }

                    }
                }).fail(function (response) {
                    if (window.console && window.console.log) {
                        console.log(response);
                    }
                });

            });

        },

        /**
         * Remove E-Course
         */
        remove: function () {

            $(document.body).on('click', '.edd-ecourse-action-delete', function (e) {

                e.preventDefault();

                if (!confirm(edd_ecourse_vars.l10n.confirm_delete_course)) {
                    return false;
                }

                var courseWrap = $(this).parents('.edd-ecourse');
                var actionsWrap = $(this).parents('.edd-ecourse-actions');

                // Add spinner.
                actionsWrap.append('<span class="spinner is-active"></span>');

                // Deactivate all buttons.
                actionsWrap.find('.button').each(function () {
                    $(this).addClass('disabled').attr('disabled', true);
                });

                var course_id = courseWrap.data('course-id');

                var data = {
                    action: 'edd_ecourse_delete_course',
                    course_id: course_id,
                    nonce: $(this).data('nonce')
                };

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {

                        if (true === response.success) {
                            courseWrap.remove();
                        } else {
                            if (window.console && window.console.log) {
                                console.log(response);
                            }
                        }

                    }
                }).fail(function (response) {
                    if (window.console && window.console.log) {
                        console.log(response);
                    }
                });

            });

        },

        /**
         * Edit Course Title
         */
        editTitle: function () {

            $('#edd-ecourse-edit-course-title').on('click', function (e) {

                e.preventDefault();

                var editButton = $(this);
                var wrap = $(this).parent();
                var courseID = wrap.data('course');
                var courseTitleWrap = wrap.find('span');
                var currentTitle = courseTitleWrap.text();

                wrap.addClass('edd-ecourse-is-editing');

                // Turn the title into an input box.
                courseTitleWrap.html('<input type="text" size="30" spellcheck="true" autocomplete="off" value="">');

                var inputBox = courseTitleWrap.find('input');
                inputBox.focus().val(currentTitle);

                // Hide edit button.
                editButton.hide();

                // Add submit and cancel buttons.
                editButton.after('<button href="#" class="button edd-ecourse-cancel-edit-course-title">' + edd_ecourse_vars.l10n.cancel + '</button>');
                editButton.after('<button href="#" class="button button-primary edd-ecourse-submit-edit-course-title">' + edd_ecourse_vars.l10n.save + '</button>');

                /** Cancel Edit **/
                wrap.on('click', '.edd-ecourse-cancel-edit-course-title', function (e) {

                    e.preventDefault();

                    $('.edd-ecourse-cancel-edit-course-title, .edd-ecourse-submit-edit-course-title').remove();
                    editButton.show();

                    courseTitleWrap.html(currentTitle);

                    wrap.removeClass('edd-ecourse-is-editing');

                });

                /** Save Edit **/
                // I'm doing some funky shit here so it will trigger when pressing "enter" or clicking.
                wrap.on('keypress click', function (e) {
                    //wrap.on('click', '.edd-ecourse-submit-edit-course-title', function (e) {

                    if (!wrap.hasClass('edd-ecourse-is-editing')) {
                        return;
                    }

                    if ('click' == e.type && !$(e.target).hasClass('edd-ecourse-submit-edit-course-title')) {
                        return;
                    }

                    if (e.type != 'click' && e.which !== 13) {
                        return;
                    }

                    e.preventDefault();

                    $(this).attr('disabled', true);

                    var data = {
                        action: 'edd_ecourse_update_course_title',
                        course: courseID,
                        title: inputBox.val()
                    };

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        dataType: "json",
                        success: function (response) {

                            $('.edd-ecourse-cancel-edit-course-title, .edd-ecourse-submit-edit-course-title').remove();
                            editButton.show();

                            courseTitleWrap.html(data.title);

                            wrap.removeClass('edd-ecourse-is-editing');

                        }
                    }).fail(function (response) {
                        if (window.console && window.console.log) {
                            console.log(response);
                        }
                    });

                });

            });

        },

        /**
         * Edit Slug
         */
        editSlug: function () {

            // @todo

        },

        /**
         * Update Course
         * @param args
         */
        updateCourse: function (args) {

            var saveWrap = $('#ecourse-save');
            var saveButton = $('#ecourse-save-status');

            saveButton.attr('disabled', true);
            saveWrap.prepend('<span class="spinner is-active" style="float: none"></span>');

            var data = {
                action: 'edd_ecourse_update_course',
                args: args
            };

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                dataType: "json",
                success: function (response) {

                    console.log(response);

                    saveButton.attr('disabled', false);
                    saveWrap.find('.spinner').remove();

                }
            }).fail(function (response) {
                if (window.console && window.console.log) {
                    console.log(response);
                }
            });

        },

        /**
         * Maybe Hide Course Date
         */
        maybeHideDate: function () {

            // First hide it.
            var startDateWrap = $('#ecourse-start-date-wrap');
            startDateWrap.hide();

            var courseStatus = $('#course_status').find('option:selected').val();

            if ('future' == courseStatus) {
                startDateWrap.slideDown();
            } else {
                $('course-start-date').val(''); // empty value
            }

        },

        /**
         * Update Course Details
         * @param e
         */
        updateCourseDetails: function (e) {
            e.preventDefault();

            var args = {
                ID: $('#edd-ecourse-title').data('course'),
                post_status: $('#course_status').find('option:selected').val()
            };

            var startDate = $('#course-start-date').val();

            if (startDate) {
                args.post_date = startDate;
            }

            EDD_ECourse.updateCourse(args);
        }

    };

    var EDD_ECourse_Module = {

        /**
         * Initialize all the things.
         */
        init: function () {
            this.sort();
            this.sortLessons();
            this.add();
            this.editTitle();
        },

        /**
         * Sort Modules
         * Change the order of the modules and save.
         */
        sort: function () {
            $('#edd-ecourse-module-sortables').sortable({
                items: '.postbox:not(.edd-ecourse-add-module)',
                handle: '.hndle'
            }).on('sortstop', function (event, ui) {

                var modules = [];

                $('.edd-ecourse-module-group').each(function () {
                    modules.push($(this).data('module'));
                });

                // Save positioning.
                var data = {
                    action: 'edd_ecourse_save_module_positions',
                    modules: modules,
                    nonce: $('#edd_ecourse_manage_course_nonce').val()
                };

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {

                        console.log(response);

                        if (true !== response.success) {

                            if (window.console && window.console.log) {
                                console.log(response);
                            }

                        }

                    }
                }).fail(function (response) {
                    if (window.console && window.console.log) {
                        console.log(response);
                    }
                });

            });
        },

        /**
         * Sort Lessons
         * Change the order of the lessons and save.
         */
        sortLessons: function () {

            $('.edd-ecourse-lesson-list').sortable()
                .on('sortstop', function (event, ui) {

                    var lessons = [];

                    $('.edd-ecourse-lesson-list > li').each(function () {
                        lessons.push($(this).data('id'));
                    });

                    // Save positioning.
                    var data = {
                        action: 'edd_ecourse_save_lesson_positions',
                        lessons: lessons,
                        nonce: $('#edd_ecourse_manage_course_nonce').val()
                    };

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        dataType: "json",
                        success: function (response) {

                            console.log(response);

                            if (true !== response.success) {

                                if (window.console && window.console.log) {
                                    console.log(response);
                                }

                            }

                        }
                    }).fail(function (response) {
                        if (window.console && window.console.log) {
                            console.log(response);
                        }
                    });

                });

        },

        /**
         * Add Module
         */
        add: function () {

            $('#edd-ecourse-add-module-form').on('submit', function (e) {

                e.preventDefault();

                var form = $(this);
                var wrap = $('.edd-ecourse-add-module');

                // Add spinner.
                form.append('<span class="spinner is-active"></span>');

                // Disable submit.
                form.find('button').attr('disabled', true);

                var data = {
                    action: 'edd_ecourse_add_module',
                    title: $('#edd-ecourse-module-name').val(),
                    course_id: $('#edd-ecourse-id').val(),
                    position: ($('.edd-ecourse-module-group').length) + 1,
                    nonce: $('#edd_ecourse_add_module_nonce').val()
                };

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {

                        console.log(response);

                        if (true === response.success) {

                            // Remove spinner.
                            form.find('.spinner').remove();

                            // Re-enable submit.
                            form.find('button').attr('disabled', false);

                            // Clear course title field.
                            $('#edd-ecourse-module-name').val('');

                            // Load template data.
                            var moduleTemplate = wp.template('edd-ecourse-new-module');
                            wrap.before(moduleTemplate(response.data));

                        } else {

                            if (window.console && window.console.log) {
                                console.log(response);
                            }

                        }

                    }
                }).fail(function (response) {
                    if (window.console && window.console.log) {
                        console.log(response);
                    }
                });

            });

        },

        /**
         * Edit Module Title
         */
        editTitle: function () {

            $('.edd-ecourse-edit-module-title').on('click', function (e) {

                e.preventDefault();

                var editButton = $(this);
                var wrap = $(this).parents('.edd-ecourse-module-group');
                var addLessonButton = wrap.find('.edd-ecourse-add-module-lesson');
                var moduleID = wrap.data('module');
                var moduleTitleWrap = wrap.find('.edd-ecourse-module-title');
                var currentTitle = moduleTitleWrap.text();

                wrap.addClass('edd-ecourse-is-editing');

                // Turn the title into an input box.
                moduleTitleWrap.html('<input type="text" value="">');

                var inputBox = moduleTitleWrap.find('input');
                inputBox.focus().val(currentTitle);

                // Hide edit button.
                editButton.hide();
                // Hide lesson button.
                addLessonButton.hide();

                // Add submit and cancel buttons.
                editButton.after('<button href="#" class="button edd-ecourse-cancel-edit-module-title">' + edd_ecourse_vars.l10n.cancel + '</button>');
                editButton.after('<button href="#" class="button button-primary edd-ecourse-submit-edit-module-title">' + edd_ecourse_vars.l10n.save + '</button>');

                /** Cancel Edit **/
                wrap.on('click', '.edd-ecourse-cancel-edit-module-title', function (e) {

                    e.preventDefault();

                    $('.edd-ecourse-cancel-edit-module-title, .edd-ecourse-submit-edit-module-title').remove();
                    editButton.show();
                    addLessonButton.show();

                    moduleTitleWrap.html(currentTitle);

                    wrap.removeClass('edd-ecourse-is-editing');

                });

                /** Save Edit **/
                wrap.on('click', '.edd-ecourse-submit-edit-module-title', function (e) {

                    e.preventDefault();

                    $(this).attr('disabled', true);

                    var data = {
                        action: 'edd_ecourse_update_module_title',
                        module: moduleID,
                        title: inputBox.val()
                    };

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        dataType: "json",
                        success: function (response) {

                            console.log(response);

                            $('.edd-ecourse-cancel-edit-module-title, .edd-ecourse-submit-edit-module-title').remove();
                            editButton.show();
                            addLessonButton.show();

                            moduleTitleWrap.html(data.title);

                            wrap.removeClass('edd-ecourse-is-editing');

                        }
                    }).fail(function (response) {
                        if (window.console && window.console.log) {
                            console.log(response);
                        }
                    });

                });

            });

        }

    };

    var EDD_ECourse_Lesson = {

        /**
         * Initialize all the things.
         */
        init: function () {
            this.addBackButton();
            this.changeModule();
        },

        /**
         * Add Back Button
         * Adds a 'back to course' button to the page title actions.
         */
        addBackButton: function () {
            $('body.post-php.post-type-ecourse_lesson').find('.page-title-action').after('<a href="" class="page-title-action edd-ecourse-back-to-manage">' + edd_ecourse_vars.l10n.back_to_course + '</a>');

            var currentCourse = $('#lesson_details').find('#course').val();

            this.changeBackURL(currentCourse);
        },

        /**
         * Change Back URL
         *
         * Updates the 'back to course' button with the correct course ID.
         * @param course_id
         */
        changeBackURL: function (course_id) {
            var baseURL = edd_ecourse_vars.manage_course_url;
            var newURL = baseURL.replace(/course=([0-9]+)$/, 'course=' + course_id);

            $('.edd-ecourse-back-to-manage').attr('href', newURL);
        },

        /**
         * Change module select dropdown when the course dropdown changes.
         */
        changeModule: function () {

            $('#lesson_details').on('change', '#course', function (e) {

                var courseID = $(this).val();
                var moduleWrap = $('#lesson_details').find('#module');

                // Change back button.
                EDD_ECourse_Lesson.changeBackURL(courseID);

                var data = {
                    action: 'edd_ecourse_update_course_module_list',
                    course: courseID,
                    nonce: $('#save_lesson_details_nonce').val()
                };

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {

                        console.log(response);

                        console.log(typeof response.data);

                        if (response.data && 'object' == typeof response.data) {
                            var moduleOptions = '';

                            $.each(response.data, function (index, value) {
                                moduleOptions = moduleOptions + '<option value="' + index + '">' + value + '</option>';
                            });

                            moduleWrap.parent().show();
                            moduleWrap.empty().append(moduleOptions);
                        } else {
                            moduleWrap.parent().hide();
                        }

                    }
                }).fail(function (response) {
                    if (window.console && window.console.log) {
                        console.log(response);
                    }
                });

            });

        }

    };

    EDD_ECourse.init();
    EDD_ECourse_Module.init();
    EDD_ECourse_Lesson.init();

});