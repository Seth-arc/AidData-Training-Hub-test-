/**
 * AidData LMS Simulation JavaScript
 * Based on securing_funding simulation functionality
 */

var AidDataSimulation = {
    currentStep: 0,
    sessionData: {},
    steps: [],
    simulationId: 0,
    totalSteps: 0,
    
    init: function() {
        this.simulationId = window.simulationData.id;
        this.steps = window.simulationData.steps || [];
        this.currentStep = window.simulationData.currentStep || 0;
        this.sessionData = JSON.parse(window.simulationData.sessionData || '{}');
        this.totalSteps = this.steps.length;
        
        this.setupInterface();
        this.loadCurrentStep();
        this.updateProgress();
    },
    
    setupInterface: function() {
        this.renderStepsList();
        this.updateNavigationButtons();
    },
    
    renderStepsList: function() {
        var $stepsList = jQuery('#stepsList');
        $stepsList.empty();
        
        for (var i = 0; i < this.steps.length; i++) {
            var step = this.steps[i];
            var isCompleted = i < this.currentStep;
            var isCurrent = i === this.currentStep;
            var isAccessible = i <= this.currentStep;
            
            var stepClass = 'step-item';
            if (isCompleted) stepClass += ' completed';
            if (isCurrent) stepClass += ' current';
            if (!isAccessible) stepClass += ' locked';
            
            var stepHtml = '<div class="' + stepClass + '" data-step="' + i + '">';
            stepHtml += '<div class="step-number">' + (i + 1) + '</div>';
            stepHtml += '<div class="step-title">' + (step.title || 'Step ' + (i + 1)) + '</div>';
            if (isCompleted) stepHtml += '<div class="step-status">✓</div>';
            stepHtml += '</div>';
            
            $stepsList.append(stepHtml);
        }
        
        // Add click handlers for accessible steps
        $stepsList.find('.step-item:not(.locked)').on('click', function() {
            var stepIndex = parseInt(jQuery(this).data('step'));
            AidDataSimulation.goToStep(stepIndex);
        });
    },
    
    loadCurrentStep: function() {
        if (this.currentStep >= this.steps.length) {
            this.showCompletionScreen();
            return;
        }
        
        var step = this.steps[this.currentStep];
        this.renderStepContent(step);
        this.updateNavigationButtons();
    },
    
    renderStepContent: function(step) {
        var $container = jQuery('#contentContainer');
        
        var contentHtml = '<div class="step-content fade-in">';
        contentHtml += '<div class="step-header">';
        contentHtml += '<h2 class="step-title">' + (step.title || 'Step ' + (this.currentStep + 1)) + '</h2>';
        if (step.subtitle) {
            contentHtml += '<p class="step-subtitle">' + step.subtitle + '</p>';
        }
        contentHtml += '</div>';
        
        contentHtml += '<div class="step-body">';
        
        // Render based on step type
        switch (step.type) {
            case 'information':
                contentHtml += this.renderInformationStep(step);
                break;
            case 'question':
                contentHtml += this.renderQuestionStep(step);
                break;
            case 'analysis':
                contentHtml += this.renderAnalysisStep(step);
                break;
            case 'decision':
                contentHtml += this.renderDecisionStep(step);
                break;
            case 'data_exploration':
                contentHtml += this.renderDataExplorationStep(step);
                break;
            default:
                contentHtml += this.renderDefaultStep(step);
        }
        
        contentHtml += '</div>';
        contentHtml += '</div>';
        
        $container.html(contentHtml);
        
        // Initialize step-specific functionality
        this.initializeStepInteractions();
    },
    
    renderInformationStep: function(step) {
        var html = '<div class="information-step">';
        
        if (step.content) {
            html += '<div class="step-content-text">' + step.content + '</div>';
        }
        
        if (step.image) {
            html += '<div class="step-image">';
            html += '<img src="' + step.image + '" alt="Step illustration" />';
            html += '</div>';
        }
        
        if (step.key_points && step.key_points.length > 0) {
            html += '<div class="key-points">';
            html += '<h4>Key Points:</h4>';
            html += '<ul>';
            step.key_points.forEach(function(point) {
                html += '<li>' + point + '</li>';
            });
            html += '</ul>';
            html += '</div>';
        }
        
        html += '</div>';
        return html;
    },
    
    renderQuestionStep: function(step) {
        var html = '<div class="question-step">';
        
        html += '<div class="question-content">';
        html += '<h3>' + step.question + '</h3>';
        
        if (step.description) {
            html += '<p class="question-description">' + step.description + '</p>';
        }
        
        html += '<div class="question-options">';
        
        step.options.forEach(function(option, index) {
            var isSelected = AidDataSimulation.sessionData['step_' + AidDataSimulation.currentStep + '_answer'] === index;
            var selectedClass = isSelected ? ' selected' : '';
            
            html += '<div class="option-item' + selectedClass + '" data-option="' + index + '">';
            html += '<div class="option-content">';
            html += '<h4>' + option.title + '</h4>';
            if (option.description) {
                html += '<p>' + option.description + '</p>';
            }
            html += '</div>';
            html += '</div>';
        });
        
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        return html;
    },
    
    renderAnalysisStep: function(step) {
        var html = '<div class="analysis-step">';
        
        html += '<div class="analysis-content">';
        if (step.scenario) {
            html += '<div class="scenario-box">';
            html += '<h4>Scenario:</h4>';
            html += '<p>' + step.scenario + '</p>';
            html += '</div>';
        }
        
        if (step.data_table) {
            html += '<div class="data-table-container">';
            html += '<h4>Data Analysis:</h4>';
            html += this.renderDataTable(step.data_table);
            html += '</div>';
        }
        
        if (step.analysis_questions && step.analysis_questions.length > 0) {
            html += '<div class="analysis-questions">';
            html += '<h4>Analysis Questions:</h4>';
            step.analysis_questions.forEach(function(question, index) {
                var answer = AidDataSimulation.sessionData['step_' + AidDataSimulation.currentStep + '_analysis_' + index] || '';
                html += '<div class="analysis-question">';
                html += '<label>' + question + '</label>';
                html += '<textarea data-analysis="' + index + '" placeholder="Enter your analysis...">' + answer + '</textarea>';
                html += '</div>';
            });
            html += '</div>';
        }
        
        html += '</div>';
        html += '</div>';
        
        return html;
    },
    
    renderDecisionStep: function(step) {
        var html = '<div class="decision-step">';
        
        html += '<div class="decision-content">';
        html += '<div class="decision-scenario">';
        html += '<h3>' + step.decision_prompt + '</h3>';
        
        if (step.context) {
            html += '<p class="decision-context">' + step.context + '</p>';
        }
        
        html += '</div>';
        
        html += '<div class="decision-options">';
        step.options.forEach(function(option, index) {
            var isSelected = AidDataSimulation.sessionData['step_' + AidDataSimulation.currentStep + '_decision'] === index;
            var selectedClass = isSelected ? ' selected' : '';
            
            html += '<div class="decision-option' + selectedClass + '" data-decision="' + index + '">';
            html += '<div class="option-header">';
            html += '<h4>' + option.title + '</h4>';
            if (option.impact_score) {
                html += '<div class="impact-score">Impact: ' + option.impact_score + '/10</div>';
            }
            html += '</div>';
            html += '<p>' + option.description + '</p>';
            
            if (option.pros && option.pros.length > 0) {
                html += '<div class="pros-cons">';
                html += '<div class="pros">';
                html += '<h5>Pros:</h5>';
                html += '<ul>';
                option.pros.forEach(function(pro) {
                    html += '<li>' + pro + '</li>';
                });
                html += '</ul>';
                html += '</div>';
                
                if (option.cons && option.cons.length > 0) {
                    html += '<div class="cons">';
                    html += '<h5>Cons:</h5>';
                    html += '<ul>';
                    option.cons.forEach(function(con) {
                        html += '<li>' + con + '</li>';
                    });
                    html += '</ul>';
                    html += '</div>';
                }
                html += '</div>';
            }
            html += '</div>';
        });
        html += '</div>';
        
        // Show rationale input if decision is made
        var selectedDecision = this.sessionData['step_' + this.currentStep + '_decision'];
        if (selectedDecision !== undefined) {
            var rationale = this.sessionData['step_' + this.currentStep + '_rationale'] || '';
            html += '<div class="rationale-section">';
            html += '<h4>Explain your reasoning:</h4>';
            html += '<textarea id="decisionRationale" placeholder="Explain why you chose this option...">' + rationale + '</textarea>';
            html += '</div>';
        }
        
        html += '</div>';
        html += '</div>';
        
        return html;
    },
    
    renderDataExplorationStep: function(step) {
        var html = '<div class="data-exploration-step">';
        
        html += '<div class="exploration-content">';
        html += '<h3>' + step.title + '</h3>';
        
        if (step.instructions) {
            html += '<div class="instructions">' + step.instructions + '</div>';
        }
        
        if (step.data_visualization) {
            html += '<div class="data-viz-container">';
            html += '<div id="dataVisualization" class="data-visualization"></div>';
            html += '</div>';
        }
        
        if (step.interactive_elements) {
            html += '<div class="interactive-elements">';
            step.interactive_elements.forEach(function(element, index) {
                html += AidDataSimulation.renderInteractiveElement(element, index);
            });
            html += '</div>';
        }
        
        html += '</div>';
        html += '</div>';
        
        return html;
    },
    
    renderDefaultStep: function(step) {
        var html = '<div class="default-step">';
        html += '<div class="step-content-text">';
        html += step.content || 'Step content goes here.';
        html += '</div>';
        html += '</div>';
        return html;
    },
    
    renderDataTable: function(tableData) {
        var html = '<div class="data-table">';
        html += '<table>';
        
        // Header
        if (tableData.headers) {
            html += '<thead><tr>';
            tableData.headers.forEach(function(header) {
                html += '<th>' + header + '</th>';
            });
            html += '</tr></thead>';
        }
        
        // Body
        if (tableData.rows) {
            html += '<tbody>';
            tableData.rows.forEach(function(row) {
                html += '<tr>';
                row.forEach(function(cell) {
                    html += '<td>' + cell + '</td>';
                });
                html += '</tr>';
            });
            html += '</tbody>';
        }
        
        html += '</table>';
        html += '</div>';
        return html;
    },
    
    renderInteractiveElement: function(element, index) {
        var html = '';
        
        switch (element.type) {
            case 'slider':
                var value = this.sessionData['interactive_' + index] || element.default_value || 0;
                html += '<div class="interactive-slider">';
                html += '<label>' + element.label + '</label>';
                html += '<input type="range" min="' + element.min + '" max="' + element.max + '" value="' + value + '" data-interactive="' + index + '">';
                html += '<span class="slider-value">' + value + '</span>';
                html += '</div>';
                break;
                
            case 'dropdown':
                var selectedValue = this.sessionData['interactive_' + index] || '';
                html += '<div class="interactive-dropdown">';
                html += '<label>' + element.label + '</label>';
                html += '<select data-interactive="' + index + '">';
                element.options.forEach(function(option) {
                    var selected = option.value === selectedValue ? ' selected' : '';
                    html += '<option value="' + option.value + '"' + selected + '>' + option.label + '</option>';
                });
                html += '</select>';
                html += '</div>';
                break;
        }
        
        return html;
    },
    
    initializeStepInteractions: function() {
        var self = this;
        
        // Question options
        jQuery('.option-item').on('click', function() {
            var optionIndex = jQuery(this).data('option');
            jQuery('.option-item').removeClass('selected');
            jQuery(this).addClass('selected');
            self.sessionData['step_' + self.currentStep + '_answer'] = optionIndex;
            self.saveSessionData();
        });
        
        // Decision options
        jQuery('.decision-option').on('click', function() {
            var decisionIndex = jQuery(this).data('decision');
            jQuery('.decision-option').removeClass('selected');
            jQuery(this).addClass('selected');
            self.sessionData['step_' + self.currentStep + '_decision'] = decisionIndex;
            
            // Show rationale input
            if (jQuery('.rationale-section').length === 0) {
                var rationaleHtml = '<div class="rationale-section fade-in">';
                rationaleHtml += '<h4>Explain your reasoning:</h4>';
                rationaleHtml += '<textarea id="decisionRationale" placeholder="Explain why you chose this option..."></textarea>';
                rationaleHtml += '</div>';
                jQuery('.decision-content').append(rationaleHtml);
            }
            
            self.saveSessionData();
        });
        
        // Analysis questions
        jQuery('textarea[data-analysis]').on('blur', function() {
            var analysisIndex = jQuery(this).data('analysis');
            var value = jQuery(this).val();
            self.sessionData['step_' + self.currentStep + '_analysis_' + analysisIndex] = value;
            self.saveSessionData();
        });
        
        // Decision rationale
        jQuery(document).on('blur', '#decisionRationale', function() {
            var value = jQuery(this).val();
            self.sessionData['step_' + self.currentStep + '_rationale'] = value;
            self.saveSessionData();
        });
        
        // Interactive elements
        jQuery('input[data-interactive], select[data-interactive]').on('change', function() {
            var interactiveIndex = jQuery(this).data('interactive');
            var value = jQuery(this).val();
            self.sessionData['interactive_' + interactiveIndex] = value;
            
            // Update slider display
            if (jQuery(this).is('input[type="range"]')) {
                jQuery(this).siblings('.slider-value').text(value);
            }
            
            self.saveSessionData();
        });
    },
    
    nextStep: function() {
        if (this.currentStep < this.totalSteps - 1) {
            this.currentStep++;
            this.loadCurrentStep();
            this.updateProgress();
            this.saveProgress();
        } else {
            this.completeSimulation();
        }
    },
    
    previousStep: function() {
        if (this.currentStep > 0) {
            this.currentStep--;
            this.loadCurrentStep();
            this.updateProgress();
        }
    },
    
    goToStep: function(stepIndex) {
        if (stepIndex >= 0 && stepIndex <= this.currentStep && stepIndex < this.totalSteps) {
            this.currentStep = stepIndex;
            this.loadCurrentStep();
            this.updateProgress();
        }
    },
    
    updateProgress: function() {
        var progressPercent = Math.round((this.currentStep / this.totalSteps) * 100);
        jQuery('#progressPercent').text(progressPercent);
        jQuery('#progressBar').css('width', progressPercent + '%');
        
        this.renderStepsList();
        this.updateNavigationButtons();
    },
    
    updateNavigationButtons: function() {
        var $prevBtn = jQuery('#prevBtn');
        var $nextBtn = jQuery('#nextBtn');
        
        $prevBtn.prop('disabled', this.currentStep === 0);
        
        if (this.currentStep === this.totalSteps - 1) {
            $nextBtn.text('Complete Simulation');
        } else {
            $nextBtn.text('Next →');
        }
    },
    
    saveSessionData: function() {
        // Auto-save session data locally
        localStorage.setItem('aiddata_simulation_' + this.simulationId, JSON.stringify(this.sessionData));
    },
    
    saveProgress: function() {
        var self = this;
        var progressPercent = Math.round((this.currentStep / this.totalSteps) * 100);
        
        jQuery.ajax({
            url: window.simulationData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aiddata_lms_simulation_update',
                simulation_id: this.simulationId,
                session_data: JSON.stringify(this.sessionData),
                current_step: this.currentStep,
                completion_percent: progressPercent,
                nonce: window.simulationData.nonce
            },
            success: function(response) {
                if (response.success) {
                    self.showNotification('Progress saved successfully!', 'success');
                }
            },
            error: function() {
                self.showNotification('Error saving progress', 'error');
            }
        });
    },
    
    completeSimulation: function() {
        var finalScore = this.calculateFinalScore();
        var progressPercent = 100;
        
        var self = this;
        jQuery.ajax({
            url: window.simulationData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aiddata_lms_simulation_update',
                simulation_id: this.simulationId,
                session_data: JSON.stringify(this.sessionData),
                current_step: this.currentStep,
                completion_percent: progressPercent,
                nonce: window.simulationData.nonce
            },
            success: function(response) {
                if (response.success) {
                    self.showCompletionScreen(finalScore);
                }
            }
        });
    },
    
    calculateFinalScore: function() {
        // Calculate score based on decisions and responses
        var totalPoints = 0;
        var maxPoints = 0;
        
        this.steps.forEach(function(step, index) {
            if (step.type === 'question' || step.type === 'decision') {
                maxPoints += 10;
                
                var userAnswer = AidDataSimulation.sessionData['step_' + index + '_answer'] || 
                               AidDataSimulation.sessionData['step_' + index + '_decision'];
                               
                if (userAnswer !== undefined && step.options && step.options[userAnswer]) {
                    totalPoints += step.options[userAnswer].points || 0;
                }
            }
        });
        
        return maxPoints > 0 ? Math.round((totalPoints / maxPoints) * 100) : 0;
    },
    
    showCompletionScreen: function(score) {
        var html = '<div class="completion-screen fade-in">';
        html += '<div class="completion-header">';
        html += '<div class="completion-icon">🎉</div>';
        html += '<h2>Simulation Complete!</h2>';
        html += '</div>';
        
        html += '<div class="completion-content">';
        html += '<div class="score-display">';
        html += '<div class="score-circle">';
        html += '<div class="score-number">' + score + '%</div>';
        html += '<div class="score-label">Final Score</div>';
        html += '</div>';
        html += '</div>';
        
        var passed = score >= window.simulationData.passingScore;
        html += '<div class="completion-status ' + (passed ? 'passed' : 'failed') + '">';
        if (passed) {
            html += '<h3>🏆 Congratulations!</h3>';
            html += '<p>You have successfully completed the simulation with a passing score.</p>';
        } else {
            html += '<h3>📚 Keep Learning!</h3>';
            html += '<p>You can retake the simulation to improve your score.</p>';
        }
        html += '</div>';
        
        html += '<div class="completion-actions">';
        html += '<button class="btn btn-secondary" onclick="window.location.href=\'' + window.location.href.split('?')[0] + '\'">View Overview</button>';
        if (!passed) {
            html += '<button class="btn btn-primary" onclick="location.reload()">Retake Simulation</button>';
        }
        html += '</div>';
        
        html += '</div>';
        html += '</div>';
        
        jQuery('#contentContainer').html(html);
        jQuery('#nextBtn, #prevBtn').hide();
    },
    
    showNotification: function(message, type) {
        var notificationClass = 'notification ' + (type || 'info');
        var html = '<div class="' + notificationClass + '">' + message + '</div>';
        
        jQuery('body').append(html);
        
        setTimeout(function() {
            jQuery('.' + notificationClass).fadeOut();
        }, 3000);
    }
};

// Global functions for navigation
function nextStep() {
    AidDataSimulation.nextStep();
}

function previousStep() {
    AidDataSimulation.previousStep();
}

function saveProgress() {
    AidDataSimulation.saveProgress();
}
