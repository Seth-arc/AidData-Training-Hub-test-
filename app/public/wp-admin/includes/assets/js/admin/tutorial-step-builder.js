/**
 * Tutorial Step Builder JavaScript
 *
 * Handles dynamic step creation, editing, reordering, and management
 * for the tutorial builder interface.
 *
 * @package AidData_LMS
 * @since 2.0.0
 */

(function($) {
  'use strict';

  const StepBuilder = {
    steps: [],
    currentStep: null,
    currentStepIndex: null,

    /**
     * Initialize step builder
     */
    init: function() {
      this.loadSteps();
      this.bindEvents();
      this.initSortable();
    },

    /**
     * Load steps from hidden field
     */
    loadSteps: function() {
      const stepsData = $('#tutorial-steps-data').val();
      if (stepsData) {
        try {
          this.steps = JSON.parse(stepsData);
        } catch (e) {
          console.error('Error parsing steps data:', e);
          this.steps = [];
        }
      }
    },

    /**
     * Bind event handlers
     */
    bindEvents: function() {
      // Add step buttons
      $('#add-step').on('click', () => this.addStep());
      $('.add-step-template').on('click', (e) => this.addStepFromTemplate(e));

      // Step actions
      $(document).on('click', '.edit-step', (e) => this.editStep(e));
      $(document).on('click', '.duplicate-step', (e) => this.duplicateStep(e));
      $(document).on('click', '.delete-step', (e) => this.deleteStep(e));

      // Modal actions
      $('#save-step-edit').on('click', () => this.saveStepEdit());
      $('#cancel-step-edit').on('click', () => this.closeModal());
      $('.modal-close').on('click', () => this.closeModal());
      $('.modal-overlay').on('click', () => this.closeModal());

      // Prevent modal content click from closing
      $('.modal-content').on('click', function(e) {
        e.stopPropagation();
      });
    },

    /**
     * Initialize sortable functionality
     */
    initSortable: function() {
      if (typeof $.fn.sortable !== 'undefined') {
        $('#sortable-steps').sortable({
          handle: '.step-handle',
          placeholder: 'step-placeholder',
          axis: 'y',
          update: () => this.updateStepOrder()
        });
      }
    },

    /**
     * Add a new step
     */
    addStep: function(type = 'text') {
      const step = {
        id: this.generateId(),
        type: type,
        title: aiddataTutorialMeta.strings.stepTitle || 'New Step',
        description: '',
        content: this.getDefaultContent(type),
        required: true,
        estimated_time: 0,
        order: this.steps.length
      };

      this.steps.push(step);
      this.renderStepInList(step);
      this.updateHiddenField();

      // Remove no-steps message if exists
      $('.no-steps-message').remove();

      // Initialize sortable if needed
      if ($('#sortable-steps').length && !$('#sortable-steps').hasClass('ui-sortable')) {
        this.initSortable();
      }

      // Open editor for new step
      this.editStepById(step.id);
    },

    /**
     * Add step from template button
     */
    addStepFromTemplate: function(e) {
      const type = $(e.currentTarget).data('type');
      this.addStep(type);
    },

    /**
     * Get default content for step type
     */
    getDefaultContent: function(type) {
      switch (type) {
        case 'video':
          return {
            platform: '',
            video_url: '',
            video_id: '',
            thumbnail_url: '',
            duration: 0,
            autoplay: false,
            completion_threshold: 90,
            description: '',
            transcript: ''
          };
        case 'text':
          return {
            content: '',
            format: 'html',
            attachments: [],
            allow_comments: false
          };
        case 'interactive':
          return {
            interaction_type: 'iframe',
            embed_code: '',
            url: '',
            height: 600,
            instructions: '',
            completion_trigger: 'manual'
          };
        case 'resource':
          return {
            resources: [],
            instructions: '',
            required_downloads: []
          };
        case 'quiz':
          return {
            quiz_id: 0,
            questions: []
          };
        default:
          return {};
      }
    },

    /**
     * Edit step
     */
    editStep: function(e) {
      const $stepItem = $(e.currentTarget).closest('.step-item');
      const stepId = $stepItem.data('step-id');
      this.editStepById(stepId);
    },

    /**
     * Edit step by ID
     */
    editStepById: function(stepId) {
      const stepIndex = this.steps.findIndex(s => s.id === stepId);
      if (stepIndex === -1) return;

      this.currentStep = $.extend(true, {}, this.steps[stepIndex]);
      this.currentStepIndex = stepIndex;

      this.loadStepEditor(this.currentStep);
      this.showModal();
    },

    /**
     * Load step editor based on type
     */
    loadStepEditor: function(step) {
      const editorHtml = this.getEditorTemplate(step);
      $('#step-editor-body').html(editorHtml);
      $('#step-editor-title').text('Edit Step: ' + (step.title || 'Untitled'));

      // Initialize WordPress editor if text type
      if (step.type === 'text') {
        this.initTextEditor(step);
      }
    },

    /**
     * Get editor template HTML
     */
    getEditorTemplate: function(step) {
      let html = '<div class="step-editor-form">';

      // Common fields
      html += this.getCommonFields(step);

      // Type-specific fields
      switch (step.type) {
        case 'video':
          html += this.getVideoFields(step);
          break;
        case 'text':
          html += this.getTextField(step);
          break;
        case 'interactive':
          html += this.getInteractiveFields(step);
          break;
        case 'resource':
          html += this.getResourceFields(step);
          break;
        case 'quiz':
          html += this.getQuizFields(step);
          break;
      }

      html += '</div>';
      return html;
    },

    /**
     * Get common fields HTML
     */
    getCommonFields: function(step) {
      return `
        <div class="form-field">
          <label for="step-title">Step Title <span class="required">*</span></label>
          <input type="text" id="step-title" class="regular-text" value="${this.escapeHtml(step.title)}" required>
        </div>
        <div class="form-field">
          <label for="step-description">Description</label>
          <textarea id="step-description" rows="3" class="large-text">${this.escapeHtml(step.description)}</textarea>
        </div>
        <div class="form-field">
          <label for="step-estimated-time">Estimated Time (minutes)</label>
          <input type="number" id="step-estimated-time" class="small-text" value="${step.estimated_time}" min="0">
        </div>
        <div class="form-field">
          <label>
            <input type="checkbox" id="step-required" ${step.required ? 'checked' : ''}>
            Required to complete this step
          </label>
        </div>
      `;
    },

    /**
     * Get video fields HTML
     */
    getVideoFields: function(step) {
      const content = step.content || {};
      return `
        <h3>Video Settings</h3>
        <div class="form-field">
          <label for="video-url">Video URL <span class="required">*</span></label>
          <input type="url" id="video-url" class="regular-text" value="${this.escapeHtml(content.video_url || '')}" required>
          <p class="description">Enter YouTube, Vimeo, Panopto, or HTML5 video URL</p>
        </div>
        <div class="form-field">
          <label for="video-description">Video Description</label>
          <textarea id="video-description" rows="3" class="large-text">${this.escapeHtml(content.description || '')}</textarea>
        </div>
        <div class="form-field">
          <label for="video-completion-threshold">Completion Threshold (%)</label>
          <input type="number" id="video-completion-threshold" class="small-text" value="${content.completion_threshold || 90}" min="0" max="100">
          <p class="description">Percentage of video that must be watched</p>
        </div>
        <div class="form-field">
          <label>
            <input type="checkbox" id="video-autoplay" ${content.autoplay ? 'checked' : ''}>
            Autoplay video
          </label>
        </div>
        <div class="form-field">
          <label for="video-transcript">Transcript (optional)</label>
          <textarea id="video-transcript" rows="5" class="large-text">${this.escapeHtml(content.transcript || '')}</textarea>
        </div>
      `;
    },

    /**
     * Get text field HTML
     */
    getTextField: function(step) {
      const content = step.content || {};
      return `
        <h3>Text Content</h3>
        <div class="form-field">
          <label for="text-content">Content</label>
          <textarea id="text-content" rows="10" class="large-text">${this.escapeHtml(content.content || '')}</textarea>
          <p class="description">Use the editor to format your content</p>
        </div>
        <div class="form-field">
          <label>
            <input type="checkbox" id="text-allow-comments" ${content.allow_comments ? 'checked' : ''}>
            Allow comments on this step
          </label>
        </div>
      `;
    },

    /**
     * Get interactive fields HTML
     */
    getInteractiveFields: function(step) {
      const content = step.content || {};
      return `
        <h3>Interactive Content</h3>
        <div class="form-field">
          <label for="interactive-type">Interaction Type</label>
          <select id="interactive-type" class="regular-text">
            <option value="iframe" ${content.interaction_type === 'iframe' ? 'selected' : ''}>Iframe Embed</option>
            <option value="embed" ${content.interaction_type === 'embed' ? 'selected' : ''}>HTML Embed</option>
            <option value="simulation" ${content.interaction_type === 'simulation' ? 'selected' : ''}>Simulation</option>
          </select>
        </div>
        <div class="form-field">
          <label for="interactive-url">URL</label>
          <input type="url" id="interactive-url" class="regular-text" value="${this.escapeHtml(content.url || '')}">
        </div>
        <div class="form-field">
          <label for="interactive-embed">Embed Code</label>
          <textarea id="interactive-embed" rows="5" class="large-text">${this.escapeHtml(content.embed_code || '')}</textarea>
        </div>
        <div class="form-field">
          <label for="interactive-height">Height (pixels)</label>
          <input type="number" id="interactive-height" class="small-text" value="${content.height || 600}" min="0">
        </div>
        <div class="form-field">
          <label for="interactive-instructions">Instructions</label>
          <textarea id="interactive-instructions" rows="3" class="large-text">${this.escapeHtml(content.instructions || '')}</textarea>
        </div>
      `;
    },

    /**
     * Get resource fields HTML
     */
    getResourceFields: function(step) {
      const content = step.content || {};
      const resources = content.resources || [];
      
      let html = `
        <h3>Downloadable Resources</h3>
        <div class="form-field">
          <label for="resource-instructions">Instructions</label>
          <textarea id="resource-instructions" rows="3" class="large-text">${this.escapeHtml(content.instructions || '')}</textarea>
        </div>
        <div class="form-field">
          <label>Resources</label>
          <div id="resources-list">
      `;
      
      resources.forEach((resource, index) => {
        html += this.getResourceItem(resource, index);
      });
      
      html += `
          </div>
          <button type="button" class="button" id="add-resource">Add Resource</button>
        </div>
      `;
      
      return html;
    },

    /**
     * Get single resource item HTML
     */
    getResourceItem: function(resource, index) {
      return `
        <div class="resource-item" data-index="${index}">
          <input type="text" class="resource-title regular-text" placeholder="Resource Title" value="${this.escapeHtml(resource.title || '')}">
          <input type="number" class="resource-file-id small-text" placeholder="File ID" value="${resource.file_id || ''}">
          <button type="button" class="button button-small remove-resource">Remove</button>
        </div>
      `;
    },

    /**
     * Get quiz fields HTML
     */
    getQuizFields: function(step) {
      return `
        <h3>Quiz Settings</h3>
        <div class="form-field">
          <p class="description">Quiz functionality will be available in Phase 4.</p>
          <label for="quiz-id">Quiz ID</label>
          <input type="number" id="quiz-id" class="small-text" value="${step.content.quiz_id || 0}" readonly>
        </div>
      `;
    },

    /**
     * Initialize text editor
     */
    initTextEditor: function(step) {
      // For now, use simple textarea. WordPress editor can be initialized if needed
      // In production, you might want to use wp.editor.initialize() or tinymce
    },

    /**
     * Save step edit
     */
    saveStepEdit: function() {
      if (!this.currentStep || this.currentStepIndex === null) {
        return;
      }

      // Collect form data
      const formData = this.collectStepData();

      // Validate
      if (!this.validateStepData(formData)) {
        return;
      }

      // Update step
      Object.assign(this.currentStep, formData);
      this.steps[this.currentStepIndex] = this.currentStep;

      // Re-render step in list
      this.renderStepInList(this.currentStep, true);

      // Update hidden field
      this.updateHiddenField();

      // Close modal
      this.closeModal();
    },

    /**
     * Collect step data from form
     */
    collectStepData: function() {
      const step = {
        id: this.currentStep.id,
        type: this.currentStep.type,
        title: $('#step-title').val().trim(),
        description: $('#step-description').val().trim(),
        estimated_time: parseInt($('#step-estimated-time').val(), 10) || 0,
        required: $('#step-required').is(':checked'),
        order: this.currentStep.order
      };

      // Type-specific content
      switch (step.type) {
        case 'video':
          step.content = {
            platform: '', // Will be detected
            video_url: $('#video-url').val().trim(),
            video_id: '', // Will be extracted
            thumbnail_url: '',
            duration: 0,
            autoplay: $('#video-autoplay').is(':checked'),
            completion_threshold: parseInt($('#video-completion-threshold').val(), 10) || 90,
            description: $('#video-description').val().trim(),
            transcript: $('#video-transcript').val().trim()
          };
          break;

        case 'text':
          step.content = {
            content: $('#text-content').val(),
            format: 'html',
            attachments: [],
            allow_comments: $('#text-allow-comments').is(':checked')
          };
          break;

        case 'interactive':
          step.content = {
            interaction_type: $('#interactive-type').val(),
            embed_code: $('#interactive-embed').val(),
            url: $('#interactive-url').val().trim(),
            height: parseInt($('#interactive-height').val(), 10) || 600,
            instructions: $('#interactive-instructions').val().trim(),
            completion_trigger: 'manual'
          };
          break;

        case 'resource':
          const resources = [];
          $('#resources-list .resource-item').each(function() {
            const title = $(this).find('.resource-title').val().trim();
            const fileId = parseInt($(this).find('.resource-file-id').val(), 10);
            if (title && fileId) {
              resources.push({
                file_id: fileId,
                title: title,
                description: '',
                file_type: '',
                file_size: 0,
                download_url: ''
              });
            }
          });
          step.content = {
            resources: resources,
            instructions: $('#resource-instructions').val().trim(),
            required_downloads: []
          };
          break;

        case 'quiz':
          step.content = {
            quiz_id: parseInt($('#quiz-id').val(), 10) || 0,
            questions: []
          };
          break;
      }

      return step;
    },

    /**
     * Validate step data
     */
    validateStepData: function(step) {
      if (!step.title) {
        alert('Step title is required');
        $('#step-title').focus();
        return false;
      }

      if (step.type === 'video' && !step.content.video_url) {
        alert('Video URL is required');
        $('#video-url').focus();
        return false;
      }

      return true;
    },

    /**
     * Duplicate step
     */
    duplicateStep: function(e) {
      const $stepItem = $(e.currentTarget).closest('.step-item');
      const stepId = $stepItem.data('step-id');
      const stepIndex = this.steps.findIndex(s => s.id === stepId);

      if (stepIndex === -1) return;

      const step = $.extend(true, {}, this.steps[stepIndex]);
      step.id = this.generateId();
      step.title += ' (Copy)';
      step.order = this.steps.length;

      this.steps.push(step);
      this.renderStepInList(step);
      this.updateHiddenField();
    },

    /**
     * Delete step
     */
    deleteStep: function(e) {
      if (!confirm(aiddataTutorialMeta.strings.confirmDelete)) {
        return;
      }

      const $stepItem = $(e.currentTarget).closest('.step-item');
      const stepId = $stepItem.data('step-id');

      this.steps = this.steps.filter(s => s.id !== stepId);
      $stepItem.remove();
      this.updateHiddenField();

      if (this.steps.length === 0) {
        this.showNoStepsMessage();
      }
    },

    /**
     * Update step order after drag-drop
     */
    updateStepOrder: function() {
      $('.step-item').each((index, element) => {
        const stepId = $(element).data('step-id');
        const step = this.steps.find(s => s.id === stepId);
        if (step) {
          step.order = index;
        }
      });

      this.steps.sort((a, b) => a.order - b.order);
      this.updateHiddenField();
    },

    /**
     * Render step in list
     */
    renderStepInList: function(step, replace = false) {
      const $stepHtml = this.buildStepHtml(step);

      if (replace) {
        $(`.step-item[data-step-id="${step.id}"]`).replaceWith($stepHtml);
      } else {
        $('.no-steps-message').remove();
        if ($('#sortable-steps').length === 0) {
          $('.step-builder-container').html('<div class="steps-list" id="sortable-steps"></div>');
          this.initSortable();
        }
        $('#sortable-steps').append($stepHtml);
      }
    },

    /**
     * Build step HTML
     */
    buildStepHtml: function(step) {
      const typeIcons = {
        video: 'video-alt3',
        text: 'text-page',
        interactive: 'welcome-widgets-menus',
        resource: 'download',
        quiz: 'list-view'
      };

      const icon = typeIcons[step.type] || 'admin-page';
      const requiredBadge = step.required ? `<span class="step-required-badge">Required</span>` : '';
      const timeDisplay = step.estimated_time > 0 ? `<span class="step-time"><span class="dashicons dashicons-clock"></span>${step.estimated_time} min</span>` : '';

      return $(`
        <div class="step-item" data-step-id="${step.id}" data-step-type="${step.type}">
          <div class="step-handle">
            <span class="dashicons dashicons-menu"></span>
          </div>
          <div class="step-icon">
            <span class="dashicons dashicons-${icon}"></span>
          </div>
          <div class="step-content">
            <div class="step-title">
              ${this.escapeHtml(step.title)}
              ${requiredBadge}
            </div>
            <div class="step-meta">
              <span class="step-type">${step.type.charAt(0).toUpperCase() + step.type.slice(1)}</span>
              ${timeDisplay}
            </div>
          </div>
          <div class="step-actions">
            <button type="button" class="button button-small edit-step" title="Edit Step">
              <span class="dashicons dashicons-edit"></span>
            </button>
            <button type="button" class="button button-small duplicate-step" title="Duplicate Step">
              <span class="dashicons dashicons-admin-page"></span>
            </button>
            <button type="button" class="button button-small delete-step" title="Delete Step">
              <span class="dashicons dashicons-trash"></span>
            </button>
          </div>
        </div>
      `);
    },

    /**
     * Update hidden field with steps data
     */
    updateHiddenField: function() {
      $('#tutorial-steps-data').val(JSON.stringify(this.steps));
    },

    /**
     * Show modal
     */
    showModal: function() {
      $('#step-editor-modal').fadeIn(200);
      $('body').addClass('modal-open');
    },

    /**
     * Close modal
     */
    closeModal: function() {
      $('#step-editor-modal').fadeOut(200);
      $('body').removeClass('modal-open');
      this.currentStep = null;
      this.currentStepIndex = null;
    },

    /**
     * Show no steps message
     */
    showNoStepsMessage: function() {
      $('.step-builder-container').html(
        '<div class="no-steps-message"><p>No steps added yet. Click "Add Step" to begin building your tutorial.</p></div>'
      );
    },

    /**
     * Generate unique ID
     */
    generateId: function() {
      return 'step_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    },

    /**
     * Escape HTML
     */
    escapeHtml: function(text) {
      if (!text) return '';
      return text.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }
  };

  // Initialize on document ready
  $(document).ready(function() {
    if ($('.aiddata-step-builder').length) {
      StepBuilder.init();
    }
  });

})(jQuery);

