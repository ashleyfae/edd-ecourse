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
                }

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

    EDD_ECourse.init();

});