/**
 * AidData LMS Admin JavaScript
 * Course Builder functionality with premium-course-enhanced.html integration
 */

jQuery(document).ready(function ($) {
    'use strict';

    // Initialize the course builder
    var AidDataCourseBuilder = {
        lessons: [],
        quizzes: [],
        currentLessonIndex: -1,
        currentQuizIndex: -1,

        init: function () {
            this.bindEvents();
            this.initSortable();
            this.loadExistingData();
        },

        bindEvents: function () {
            // Sidebar navigation
            $('.structure-item').on('click', this.switchSection);

            // Lesson management
            $('.add-lesson-btn').on('click', this.showLessonModal);
            $('.save-lesson').on('click', this.saveLesson);
            $('.cancel-lesson').on('click', this.hideLessonModal);

            // Quiz management
            $('.add-quiz-btn').on('click', this.showQuizModal);
            $('.save-quiz').on('click', this.saveQuiz);
            $('.cancel-quiz').on('click', this.hideQuizModal);

            // Modal management
            $('.aiddata-modal-close').on('click', this.hideModals);
            $(document).on('click', '.aiddata-modal', function (e) {
                if (e.target === this) {
                    AidDataCourseBuilder.hideModals();
                }
            });

            // Image upload
            $('.upload-image-btn').on('click', this.uploadImage);

            // Dynamic actions
            $(document).on('click', '.edit-lesson', this.editLesson);
            $(document).on('click', '.delete-lesson', this.deleteLesson);
            $(document).on('click', '.edit-quiz', this.editQuiz);
            $(document).on('click', '.delete-quiz', this.deleteQuiz);

            // Form submission
            $('.aiddata-lms-course-form').on('submit', this.saveCourse);

            // Preview functionality
            $('.preview-course').on('click', this.previewCourse);
        },

        switchSection: function (e) {
            e.preventDefault();

            var $this = $(this);
            var section = $this.data('section');

            // Update sidebar active state
            $('.structure-item').removeClass('active');
            $this.addClass('active');

            // Show corresponding content section
            $('.builder-section').removeClass('active');
            $('#' + section).addClass('active').addClass('fade-in');

            // Update URL without reload
            if (history.pushState) {
                var url = new URL(window.location);
                url.searchParams.set('section', section);
                history.pushState(null, '', url);
            }
        },

        initSortable: function () {
            if ($.fn.sortable) {
                $('#lessons-sortable').sortable({
                    handle: '.lesson-item',
                    placeholder: 'lesson-placeholder',
                    update: function (event, ui) {
                        AidDataCourseBuilder.updateLessonOrder();
                    }
                });
            }
        },

        showLessonModal: function (e) {
            e.preventDefault();
            AidDataCourseBuilder.currentLessonIndex = -1;
            $('#lesson-form')[0].reset();
            $('#lesson-modal').show().addClass('fade-in');
        },

        hideLessonModal: function () {
            $('#lesson-modal').hide().removeClass('fade-in');
        },

        showQuizModal: function (e) {
            e.preventDefault();
            AidDataCourseBuilder.currentQuizIndex = -1;
            $('#quiz-form')[0].reset();
            $('#quiz-modal').show().addClass('fade-in');
        },

        hideQuizModal: function () {
            $('#quiz-modal').hide().removeClass('fade-in');
        },

        hideModals: function () {
            $('.aiddata-modal').hide().removeClass('fade-in');
        },

        saveLesson: function (e) {
            e.preventDefault();

            var lessonData = {
                title: $('#lesson_title').val(),
                content: $('#lesson_content').val(),
                video_url: $('#lesson_video_url').val(),
                duration: $('#lesson_duration').val(),
                free: $('#lesson_free').is(':checked'),
                order: AidDataCourseBuilder.lessons.length
            };

            if (!lessonData.title) {
                alert(aiddata_lms_admin.strings.error);
                return;
            }

            if (AidDataCourseBuilder.currentLessonIndex >= 0) {
                // Edit existing lesson
                AidDataCourseBuilder.lessons[AidDataCourseBuilder.currentLessonIndex] = lessonData;
            } else {
                // Add new lesson
                AidDataCourseBuilder.lessons.push(lessonData);
            }

            AidDataCourseBuilder.renderLessons();
            AidDataCourseBuilder.hideLessonModal();
            AidDataCourseBuilder.updateLessonCount();
        },

        saveQuiz: function (e) {
            e.preventDefault();

            var quizData = {
                title: $('#quiz_title').val(),
                description: $('#quiz_description').val(),
                passing_grade: $('#quiz_passing_grade').val(),
                time_limit: $('#quiz_time_limit').val(),
                questions: []
            };

            if (!quizData.title) {
                alert(aiddata_lms_admin.strings.error);
                return;
            }

            if (AidDataCourseBuilder.currentQuizIndex >= 0) {
                // Edit existing quiz
                AidDataCourseBuilder.quizzes[AidDataCourseBuilder.currentQuizIndex] = quizData;
            } else {
                // Add new quiz
                AidDataCourseBuilder.quizzes.push(quizData);
            }

            AidDataCourseBuilder.renderQuizzes();
            AidDataCourseBuilder.hideQuizModal();
            AidDataCourseBuilder.updateQuizCount();
        },

        renderLessons: function () {
            var $container = $('#lessons-sortable');
            $container.empty();

            AidDataCourseBuilder.lessons.forEach(function (lesson, index) {
                var lessonHtml = AidDataCourseBuilder.getLessonHtml(lesson, index);
                $container.append(lessonHtml);
            });
        },

        renderQuizzes: function () {
            var $container = $('#quizzes-list');
            $container.empty();

            AidDataCourseBuilder.quizzes.forEach(function (quiz, index) {
                var quizHtml = AidDataCourseBuilder.getQuizHtml(quiz, index);
                $container.append(quizHtml);
            });
        },

        getLessonHtml: function (lesson, index) {
            return `
                <div class="lesson-item" data-index="${index}">
                    <div class="lesson-header">
                        <div class="lesson-title">${lesson.title}</div>
                        <div class="lesson-actions">
                            <button type="button" class="button button-small edit-lesson" data-index="${index}">Edit</button>
                            <button type="button" class="button button-small delete-lesson" data-index="${index}">Delete</button>
                        </div>
                    </div>
                    <div class="lesson-meta">
                        <span><strong>Duration:</strong> ${lesson.duration || 'Not set'}</span>
                        <span><strong>Video:</strong> ${lesson.video_url ? 'Yes' : 'No'}</span>
                        <span><strong>Free:</strong> ${lesson.free ? 'Yes' : 'No'}</span>
                    </div>
                </div>
            `;
        },

        getQuizHtml: function (quiz, index) {
            return `
                <div class="quiz-item" data-index="${index}">
                    <div class="quiz-header">
                        <div class="quiz-title">${quiz.title}</div>
                        <div class="quiz-actions">
                            <button type="button" class="button button-small edit-quiz" data-index="${index}">Edit</button>
                            <button type="button" class="button button-small delete-quiz" data-index="${index}">Delete</button>
                        </div>
                    </div>
                    <div class="quiz-meta">
                        <span><strong>Passing Grade:</strong> ${quiz.passing_grade}%</span>
                        <span><strong>Time Limit:</strong> ${quiz.time_limit || 'Unlimited'}</span>
                        <span><strong>Questions:</strong> ${quiz.questions.length}</span>
                    </div>
                </div>
            `;
        },

        editLesson: function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            var lesson = AidDataCourseBuilder.lessons[index];

            AidDataCourseBuilder.currentLessonIndex = index;

            $('#lesson_title').val(lesson.title);
            $('#lesson_content').val(lesson.content);
            $('#lesson_video_url').val(lesson.video_url);
            $('#lesson_duration').val(lesson.duration);
            $('#lesson_free').prop('checked', lesson.free);

            $('#lesson-modal').show().addClass('fade-in');
        },

        deleteLesson: function (e) {
            e.preventDefault();

            if (!confirm(aiddata_lms_admin.strings.confirm_delete)) {
                return;
            }

            var index = $(this).data('index');
            AidDataCourseBuilder.lessons.splice(index, 1);
            AidDataCourseBuilder.renderLessons();
            AidDataCourseBuilder.updateLessonCount();
        },

        editQuiz: function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            var quiz = AidDataCourseBuilder.quizzes[index];

            AidDataCourseBuilder.currentQuizIndex = index;

            $('#quiz_title').val(quiz.title);
            $('#quiz_description').val(quiz.description);
            $('#quiz_passing_grade').val(quiz.passing_grade);
            $('#quiz_time_limit').val(quiz.time_limit);

            $('#quiz-modal').show().addClass('fade-in');
        },

        deleteQuiz: function (e) {
            e.preventDefault();

            if (!confirm(aiddata_lms_admin.strings.confirm_delete)) {
                return;
            }

            var index = $(this).data('index');
            AidDataCourseBuilder.quizzes.splice(index, 1);
            AidDataCourseBuilder.renderQuizzes();
            AidDataCourseBuilder.updateQuizCount();
        },

        updateLessonCount: function () {
            $('.lessons-section .structure-count').text(AidDataCourseBuilder.lessons.length);
        },

        updateQuizCount: function () {
            $('.quizzes-section .structure-count').text(AidDataCourseBuilder.quizzes.length);
        },

        updateLessonOrder: function () {
            $('#lessons-sortable .lesson-item').each(function (index) {
                var lessonIndex = $(this).data('index');
                if (AidDataCourseBuilder.lessons[lessonIndex]) {
                    AidDataCourseBuilder.lessons[lessonIndex].order = index;
                }
            });
        },

        uploadImage: function (e) {
            e.preventDefault();

            var mediaUploader = wp.media({
                title: 'Choose Course Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('input[name="course_image_id"]').val(attachment.id);
                $('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto;">');
            });

            mediaUploader.open();
        },

        saveCourse: function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('.save-course');

            // Add lessons and quizzes data to form
            $('<input>').attr({
                type: 'hidden',
                name: 'course_lessons',
                value: JSON.stringify(AidDataCourseBuilder.lessons)
            }).appendTo($form);

            $('<input>').attr({
                type: 'hidden',
                name: 'course_quizzes',
                value: JSON.stringify(AidDataCourseBuilder.quizzes)
            }).appendTo($form);

            // Show saving state
            $submitBtn.text(aiddata_lms_admin.strings.saving).prop('disabled', true);

            // Submit form
            $form.off('submit').submit();
        },

        previewCourse: function (e) {
            e.preventDefault();

            // Create preview data
            var previewData = {
                title: $('#course_title').val(),
                description: $('#course_description').val(),
                price: $('#course_price').val(),
                duration: $('#course_duration').val(),
                difficulty: $('#course_difficulty').val(),
                lessons: AidDataCourseBuilder.lessons,
                quizzes: AidDataCourseBuilder.quizzes
            };

            // Open preview in new window
            var previewWindow = window.open('', 'course-preview', 'width=1200,height=800');
            previewWindow.document.write(AidDataCourseBuilder.generatePreviewHtml(previewData));
        },

        generatePreviewHtml: function (data) {
            return `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Course Preview - ${data.title}</title>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 0; padding: 20px; background: #ffffff; }
                        .preview-container { max-width: 800px; margin: 0 auto; background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                        .course-header { text-align: center; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 2px solid #e5e7eb; }
                        .course-title { font-size: 2rem; color: #004E38; margin-bottom: 1rem; }
                        .course-meta { display: flex; justify-content: center; gap: 2rem; margin-bottom: 1rem; }
                        .meta-item { text-align: center; }
                        .meta-label { font-size: 0.875rem; color: #666; text-transform: uppercase; }
                        .meta-value { font-weight: bold; color: #004E38; }
                        .course-description { color: #495965; line-height: 1.6; }
                        .section { margin-bottom: 2rem; }
                        .section-title { font-size: 1.25rem; color: #004E38; margin-bottom: 1rem; }
                        .item-list { display: flex; flex-direction: column; gap: 0.5rem; }
                        .item { padding: 0.75rem; background: #ffffff; border-radius: 6px; }
                    </style>
                </head>
                <body>
                    <div class="preview-container">
                        <div class="course-header">
                            <h1 class="course-title">${data.title}</h1>
                            <div class="course-meta">
                                <div class="meta-item">
                                    <div class="meta-label">Price</div>
                                    <div class="meta-value">$${data.price || '0'}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Duration</div>
                                    <div class="meta-value">${data.duration || 'Not set'}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Level</div>
                                    <div class="meta-value">${data.difficulty}</div>
                                </div>
                            </div>
                            <p class="course-description">${data.description}</p>
                        </div>

                        <div class="section">
                            <h2 class="section-title">Lessons (${data.lessons.length})</h2>
                            <div class="item-list">
                                ${data.lessons.map(lesson => `<div class="item">${lesson.title} ${lesson.free ? '(Free)' : ''}</div>`).join('')}
                            </div>
                        </div>

                        <div class="section">
                            <h2 class="section-title">Quizzes (${data.quizzes.length})</h2>
                            <div class="item-list">
                                ${data.quizzes.map(quiz => `<div class="item">${quiz.title} (Passing: ${quiz.passing_grade}%)</div>`).join('')}
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `;
        },

        loadExistingData: function () {
            // Load existing lessons and quizzes if editing
            // This would be populated from PHP when editing an existing course
            AidDataCourseBuilder.updateLessonCount();
            AidDataCourseBuilder.updateQuizCount();
        }
    };

    // Initialize the course builder
    AidDataCourseBuilder.init();

    // URL-based section switching
    var urlParams = new URLSearchParams(window.location.search);
    var section = urlParams.get('section');
    if (section) {
        $('.structure-item[data-section="' + section + '"]').click();
    }

    // Auto-save functionality
    var autoSaveTimer;
    $('.aiddata-lms-course-form input, .aiddata-lms-course-form textarea, .aiddata-lms-course-form select').on('change input', function () {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function () {
            // Auto-save logic here if needed
            console.log('Auto-saving...');
        }, 2000);
    });
});
