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

        }

    };

    var EDD_ECourse_Module = {

        init: function () {
            this.editTitle();
        },

        /**
         * Edit Module Title
         */
        editTitle: function () {

            $('.edd-ecourse-edit-module-title').on('click', function (e) {

                e.preventDefault();

                var editButton = $(this);
                var wrap = $(this).parents('.edd-ecourse-module-group');
                var moduleID = wrap.data('module');
                var moduleTitleWrap = wrap.find('.edd-ecourse-module-title');
                var currentTitle = moduleTitleWrap.text();

                wrap.addClass('edd-ecourse-is-editing');

                // Turn the title into an input box.
                moduleTitleWrap.html('<input type="text" value="' + currentTitle + '">');

                // Hide edit button.
                editButton.hide();

                // Add submit and cancel buttons.
                editButton.after('<button href="#" class="button edd-ecourse-cancel-edit-module-title">' + edd_ecourse_vars.l10n.cancel + '</button>');
                editButton.after('<button href="#" class="button button-primary edd-ecourse-submit-edit-module-title">' + edd_ecourse_vars.l10n.save + '</button>');

                /** Cancel Edit **/
                wrap.on('click', '.edd-ecourse-cancel-edit-module-title', function (e) {

                    e.preventDefault();

                    $('.edd-ecourse-cancel-edit-module-title, .edd-ecourse-submit-edit-module-title').remove();
                    editButton.show();

                    moduleTitleWrap.html(currentTitle);

                    wrap.removeClass('edd-ecourse-is-editing');

                });

                /** Save Edit **/
                wrap.on('click', '.edd-ecourse-submit-edit-module-title', function (e) {

                    $(this).attr('disabled', true);

                    var data = {
                        action: 'edd_ecourse_update_module_title',
                        module: moduleID,
                        title: wrap.find('input').val()
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

    EDD_ECourse.init();
    EDD_ECourse_Module.init();

});