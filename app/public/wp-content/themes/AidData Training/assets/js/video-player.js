// Function to format time in hh:mm:ss or mm:ss format
function formatTime(seconds) {
    // Handle edge cases
    if (!isFinite(seconds) || seconds === null || seconds < 0) {
        return '0:00';
    }
    
    // Convert to whole seconds 
    seconds = Math.floor(seconds);
    
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = Math.floor(seconds % 60);
    
    if (h > 0) {
        return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    } else {
        return `${m}:${s.toString().padStart(2, '0')}`;
    }
}

// Add a global function to stop any video player
window.stopVideoPlayer = function(container) {
    console.log('Global stopVideoPlayer called for container:', container);
    
    if (!container) {
        console.error('No container provided to stopVideoPlayer');
        return;
    }
    
    // Find the video element
    const videoEl = container.querySelector('video');
    if (!videoEl) {
        console.error('Video element not found in container');
        return;
    }
    
    console.log('Stopping video with current state - paused:', videoEl.paused, 'currentTime:', videoEl.currentTime);
    
    // Pause the video first
    try {
        videoEl.pause();
        console.log('Video paused');
    } catch (e) {
        console.error('Error pausing video:', e);
    }
    
    // Reset currentTime
    try {
        videoEl.currentTime = 0;
        console.log('Video time reset to 0');
    } catch (e) {
        console.error('Error resetting video time:', e);
    }
    
    // Update UI elements
    try {
        // Update timeline
        const timelineEl = container.querySelector('.video-timeline');
        if (timelineEl) {
            timelineEl.style.width = '0%';
        }
        
        // Update time display
        const currentTimeEl = container.querySelector('.current-time');
        if (currentTimeEl) {
            currentTimeEl.textContent = '0:00';
        }
        
        // Update play/pause buttons
        const playIcon = container.querySelector('.play-icon');
        const pauseIcon = container.querySelector('.pause-icon');
        if (playIcon && pauseIcon) {
            playIcon.style.display = 'block';
            pauseIcon.style.display = 'none';
        }
        
        // Remove playing class
        container.classList.remove('playing');
        
        console.log('Video player UI updated to stopped state');
    } catch (e) {
        console.error('Error updating UI after stop:', e);
    }
    
    console.log('Video player stopped successfully');
    return true;
};

document.addEventListener('DOMContentLoaded', () => {
    // Add click handler for all stop buttons on the page
    document.addEventListener('click', function(e) {
        // Check if clicked element is a stop button or contains a stop button
        const stopBtn = e.target.closest('.stop-btn');
        if (stopBtn) {
            console.log('Stop button clicked via global handler', stopBtn);
            // Find the container
            const container = stopBtn.closest('.custom-video-container');
            if (container) {
                // Call the global stop function
                window.stopVideoPlayer(container);
                e.preventDefault();
                e.stopPropagation();
            }
        }
    });
    
    const players = document.querySelectorAll('.custom-video-player');
    
    players.forEach(video => {
        const container = video.closest('.custom-video-container');
        const playPauseOverlay = container.querySelector('.play-pause-overlay');
        const playPauseBtn = container.querySelector('.play-pause-btn');
        const stopBtn = container.querySelector('.stop-btn');
        const timelineContainer = container.querySelector('.video-timeline-container');
        const timeline = container.querySelector('.video-timeline');
        const timelinePreview = container.querySelector('.timeline-preview');
        const previewThumbnail = container.querySelector('.preview-thumbnail');
        const currentTimeEl = container.querySelector('.current-time');
        const totalTimeEl = container.querySelector('.total-time');
        const timeTooltip = container.querySelector('.time-tooltip');
        const volumeBtn = container.querySelector('.volume-btn');
        const volumeSlider = container.querySelector('.volume-slider');
        const volumeProgress = container.querySelector('.volume-slider-progress');
        const fullscreenBtn = container.querySelector('.fullscreen-btn');
        const controlsContainer = container.querySelector('.video-controls-container');
        const speedBtn = container.querySelector('.speed-btn');
        const speedLabel = container.querySelector('.speed-label');
        const speedDropdown = container.querySelector('.speed-dropdown');
        const speedOptions = container.querySelectorAll('.speed-option');
        
        // Icon elements
        const playIcon = playPauseBtn?.querySelector('.play-icon');
        const pauseIcon = playPauseBtn?.querySelector('.pause-icon');
        const muteIcon = volumeBtn?.querySelector('.mute-icon');
        const unmuteIcon = volumeBtn?.querySelector('.unmute-icon');
        const fullscreenIcon = fullscreenBtn?.querySelector('.fullscreen-icon');
        const exitFullscreenIcon = fullscreenBtn?.querySelector('.exit-fullscreen-icon');
        
        let controlsTimeout;
        let durationUpdateAttempts = 0;
        let durationUpdateTimer = null;
        
        // Setup event listeners
        if (playPauseBtn) {
            playPauseBtn.addEventListener('click', togglePlay);
        }
        
        if (stopBtn) {
            stopBtn.addEventListener('click', stopVideo);
            console.log('Stop button event listener attached');
        }
        
        video.addEventListener('click', togglePlay);
        video.addEventListener('play', updatePlayPauseState);
        video.addEventListener('pause', updatePlayPauseState);
        video.addEventListener('loadedmetadata', setupVideo);
        video.addEventListener('timeupdate', updateProgress);
        // Add a more frequent time update for smoother current time display
        setInterval(() => {
            if (!video.paused) {
                updateProgress();
            }
        }, 100); // Update every 100ms for smoother time display
        
        video.addEventListener('waiting', showLoading);
        video.addEventListener('canplay', hideLoading);
        video.addEventListener('error', handleVideoError);
        video.addEventListener('canplaythrough', checkAndUpdateDuration);
        video.addEventListener('durationchange', checkAndUpdateDuration);
        video.addEventListener('playing', checkAndUpdateDuration);
        
        if (volumeBtn) {
            volumeBtn.addEventListener('click', toggleMute);
        }
        
        if (volumeSlider) {
            volumeSlider.addEventListener('input', handleVolumeChange);
            volumeSlider.addEventListener('mousedown', () => {
                volumeSlider.addEventListener('mousemove', handleVolumeChange);
            });
            document.addEventListener('mouseup', () => {
                volumeSlider.removeEventListener('mousemove', handleVolumeChange);
            });
        }
        
        if (timelineContainer) {
            timelineContainer.addEventListener('mousedown', startTimelineUpdate);
            timelineContainer.addEventListener('mousemove', previewTimelineUpdate);
            timelineContainer.addEventListener('mouseover', showTimeTooltip);
            timelineContainer.addEventListener('mouseout', hideTimeTooltip);
            timelineContainer.addEventListener('mouseenter', () => {
                clearTimeout(controlsTimeout);
            });
            timelineContainer.addEventListener('mouseleave', () => {
                if (!video.paused) {
                    startHideControlsTimer();
                }
                hideTimeTooltip();
            });
        }
        
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', toggleFullscreen);
        }
        
        if (speedBtn) {
            speedBtn.addEventListener('click', toggleSpeedDropdown);
            
            // Close speed dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!speedBtn.contains(e.target) && speedDropdown) {
                    speedDropdown.classList.remove('active');
                }
            });
            
            // Setup speed options
            if (speedOptions) {
                speedOptions.forEach(option => {
                    option.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const speed = parseFloat(option.getAttribute('data-speed'));
                        setPlaybackSpeed(speed);
                        speedDropdown.classList.remove('active');
                    });
                });
            }
        }
        
        container.addEventListener('mousemove', showControls);
        container.addEventListener('mouseleave', () => {
            if (!video.paused) {
                startHideControlsTimer();
            }
        });
        
        // Initial setup
        if (volumeProgress) {
            updateVolumeProgress();
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', handleKeyboardShortcut);
        
        // Setup preview canvas for thumbnail generation
        const previewCanvas = document.createElement('canvas');
        previewCanvas.width = 160;
        previewCanvas.height = 90;
        const previewContext = previewCanvas.getContext('2d');
        
        function handleKeyboardShortcut(e) {
            // Only process if this video is the active one (in viewport or modal is open)
            if (!isVideoActive()) return;
            
            switch(e.key.toLowerCase()) {
                case ' ':
                case 'k':
                    // Space or K key for play/pause
                    e.preventDefault();
                    togglePlay();
                    break;
                case 'j':
                    // J key for rewind 10 seconds
                    video.currentTime = Math.max(0, video.currentTime - 10);
                    break;
                case 'l':
                    // L key for forward 10 seconds
                    video.currentTime = Math.min(video.duration, video.currentTime + 10);
                    break;
                case 'arrowleft':
                    // Left arrow for rewind 5 seconds
                    e.preventDefault();
                    video.currentTime = Math.max(0, video.currentTime - 5);
                    break;
                case 'arrowright':
                    // Right arrow for forward 5 seconds
                    e.preventDefault();
                    video.currentTime = Math.min(video.duration, video.currentTime + 5);
                    break;
                case 'arrowup':
                    // Up arrow to increase volume
                    e.preventDefault();
                    video.volume = Math.min(1, video.volume + 0.1);
                    video.muted = false;
                    updateMuteState();
                    updateVolumeProgress();
                    break;
                case 'arrowdown':
                    // Down arrow to decrease volume
                    e.preventDefault();
                    video.volume = Math.max(0, video.volume - 0.1);
                    if (video.volume === 0) video.muted = true;
                    updateMuteState();
                    updateVolumeProgress();
                    break;
                case 'm':
                    // M key to toggle mute
                    toggleMute();
                    break;
                case '0':
                case 'home':
                    // 0 or Home to go to start
                    video.currentTime = 0;
                    break;
                case 'end':
                    // End to go to end
                    video.currentTime = video.duration;
                    break;
                case 'f':
                    // F for fullscreen
                    toggleFullscreen();
                    break;
                case 's':
                    // S to stop
                    stopVideo();
                    break;
                case 'shift+>':
                case '>':
                    // Shift+> to increase speed
                    e.preventDefault();
                    setPlaybackSpeed(Math.min(2, video.playbackRate + 0.25));
                    break;
                case 'shift+<':
                case '<':
                    // Shift+< to decrease speed
                    e.preventDefault();
                    setPlaybackSpeed(Math.max(0.25, video.playbackRate - 0.25));
                    break;
            }
        }
        
        function isVideoActive() {
            // Check if video is in a modal that's currently active
            const modal = video.closest('.modal.active, .info-modal.active');
            if (modal) return true;
            
            // Check if video is visible in viewport
            const rect = video.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
        
        // Functions
        function setupVideo() {
            console.log('Setting up video with readyState:', video.readyState, 'duration:', video.duration);
            
            // Force an immediate check for duration
            if (totalTimeEl) {
                // For videos that already have duration info
                if (isFinite(video.duration) && video.duration > 0) {
                    totalTimeEl.textContent = formatTime(video.duration);
                    console.log(`Duration available immediately: ${video.duration}s`);
                } else {
                    // If duration isn't available, set a placeholder and try to detect it
                    totalTimeEl.textContent = "--:--";
                    console.log('Duration not yet available, setting placeholder');
                    
                    // Try to force pre-loading to get the duration faster
                    if (video.preload !== 'auto') {
                        video.preload = 'auto';
                    }
                    
                    // Try seeking slightly ahead to trigger metadata load
                    try {
                        if (video.readyState >= 1) {
                            video.currentTime = 0.1;
                        }
                    } catch (e) {
                        console.log('Unable to seek to trigger duration detection');
                    }
                    
                    // Try loading the video directly
                    try {
                        video.load();
                    } catch (e) {
                        console.log('Error calling load() directly:', e);
                    }
                    
                    // Set up backup detection systems
                    startDurationDetection();
                }
            }
            
            // Initialize the current time display
            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(video.currentTime || 0);
                console.log('Initialized current time display:', formatTime(video.currentTime || 0));
                
                // Force an immediate update of the progress bar
                updateProgress();
            }
            
            updatePlayPauseState();
        }
        
        function togglePlay() {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        }
        
        function stopVideo() {
            console.log('Original stopVideo function called');
            try {
                // Use the global function for consistent behavior
                window.stopVideoPlayer(container);
            } catch (error) {
                console.error('Error in stopVideo function:', error);
            }
        }
        
        function updatePlayPauseState() {
            if (playIcon && pauseIcon) {
                if (video.paused) {
                    playIcon.style.display = 'block';
                    pauseIcon.style.display = 'none';
                    container.classList.remove('playing');
                } else {
                    playIcon.style.display = 'none';
                    pauseIcon.style.display = 'block';
                    container.classList.add('playing');
                    startHideControlsTimer();
                }
            }
        }
        
        function updateProgress() {
            console.log('Updating progress, currentTime:', video.currentTime);
            if (timeline) {
                const percent = (video.currentTime / video.duration) * 100;
                timeline.style.width = `${percent}%`;
            }
            
            if (currentTimeEl) {
                const formattedTime = formatTime(video.currentTime);
                currentTimeEl.textContent = formattedTime;
                console.log('Updated current time display:', formattedTime);
            } else {
                console.warn('Current time element not found');
            }
            
            // Dispatch custom event for other components to use
            const progressEvent = new CustomEvent('videoProgressUpdate', {
                detail: {
                    currentTime: video.currentTime,
                    duration: video.duration,
                    percent: video.duration ? (video.currentTime / video.duration) * 100 : 0
                }
            });
            video.dispatchEvent(progressEvent);
        }
        
        function startTimelineUpdate(e) {
            video.pause();
            updateTimelinePosition(e);
            document.addEventListener('mousemove', updateTimelinePosition);
            document.addEventListener('mouseup', stopTimelineUpdate);
        }
        
        function updateTimelinePosition(e) {
            const rect = timelineContainer.getBoundingClientRect();
            const percent = Math.min(Math.max(0, e.clientX - rect.left), rect.width) / rect.width;
            timeline.style.width = `${percent * 100}%`;
            video.currentTime = percent * video.duration;
            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(video.currentTime);
            }
        }
        
        function previewTimelineUpdate(e) {
            if (e.buttons !== 1) { 
                // Show time tooltip at current position when not dragging
                showTimeTooltip(e);
                return;
            }
            
            // Only run when mouse button is pressed
            updateTimelinePosition(e);
        }
        
        function stopTimelineUpdate() {
            document.removeEventListener('mousemove', updateTimelinePosition);
            document.removeEventListener('mouseup', stopTimelineUpdate);
            video.play();
        }
        
        function toggleMute() {
            video.muted = !video.muted;
            updateMuteState();
        }
        
        function updateMuteState() {
            if (muteIcon && unmuteIcon) {
                if (video.muted || video.volume === 0) {
                    muteIcon.style.display = 'none';
                    unmuteIcon.style.display = 'block';
                } else {
                    muteIcon.style.display = 'block';
                    unmuteIcon.style.display = 'none';
                }
            }
            if (volumeProgress) {
                updateVolumeProgress();
            }
        }
        
        function handleVolumeChange(e) {
            const rect = volumeSlider.getBoundingClientRect();
            const percent = Math.min(Math.max(0, e.clientX - rect.left), rect.width) / rect.width;
            video.volume = percent;
            video.muted = percent === 0;
            updateMuteState();
            updateVolumeProgress();
        }
        
        function updateVolumeProgress() {
            if (video.muted) {
                volumeProgress.style.width = '0%';
            } else {
                volumeProgress.style.width = `${video.volume * 100}%`;
            }
        }
        
        function toggleFullscreen() {
            if (document.fullscreenElement) {
                document.exitFullscreen();
                updateFullscreenState(false);
            } else {
                container.requestFullscreen();
                updateFullscreenState(true);
            }
        }
        
        function updateFullscreenState(isFullscreen) {
            if (fullscreenIcon && exitFullscreenIcon) {
                if (isFullscreen) {
                    fullscreenIcon.style.display = 'none';
                    exitFullscreenIcon.style.display = 'block';
                } else {
                    fullscreenIcon.style.display = 'block';
                    exitFullscreenIcon.style.display = 'none';
                }
            }
        }
        
        document.addEventListener('fullscreenchange', () => {
            updateFullscreenState(!!document.fullscreenElement);
        });
        
        function showControls() {
            if (controlsContainer) {
                controlsContainer.classList.add('visible');
                clearTimeout(controlsTimeout);
                if (!video.paused) {
                    startHideControlsTimer();
                }
            }
        }
        
        function startHideControlsTimer() {
            clearTimeout(controlsTimeout);
            controlsTimeout = setTimeout(() => {
                if (controlsContainer) {
                    controlsContainer.classList.remove('visible');
                }
            }, 3000);
        }
        
        function showLoading() {
            container.classList.add('loading');
        }
        
        function hideLoading() {
            container.classList.remove('loading');
        }
        
        function handleVideoError() {
            console.error('Video error occurred:', video.error);
            container.classList.add('error');
            container.classList.remove('loading');
            
            // Show error message
            const errorMessage = container.querySelector('.video-error-message');
            if (errorMessage) {
                errorMessage.style.display = 'flex';
                
                // Setup retry button
                const retryButton = errorMessage.querySelector('.retry-button');
                if (retryButton) {
                    retryButton.addEventListener('click', () => {
                        console.log('Retry button clicked, reloading video');
                        errorMessage.style.display = 'none';
                        container.classList.remove('error');
                        container.classList.add('loading');
                        
                        // Reload the video
                        const source = video.querySelector('source');
                        if (source) {
                            const currentSrc = source.src;
                            source.src = '';
                            video.load();
                            
                            // Short delay before setting the source again
                            setTimeout(() => {
                                source.src = currentSrc;
                                video.load();
                                
                                // Try to play after load
                                video.oncanplay = () => {
                                    console.log('Video can play after retry');
                                    container.classList.remove('loading');
                                    video.play()
                                        .then(() => {
                                            console.log('Video playback started successfully');
                                        })
                                        .catch(err => {
                                            console.error('Error starting video playback:', err);
                                            // Try again with user interaction
                                            container.querySelector('.play-pause-overlay').style.display = 'flex';
                                        });
                                };
                            }, 500);
                        }
                    });
                }
            }
        }
        
        function showTimeTooltip(e) {
            if (!timelineContainer || !video.duration) return;
            
            const rect = timelineContainer.getBoundingClientRect();
            const percent = Math.min(Math.max(0, e.clientX - rect.left), rect.width) / rect.width;
            const previewTime = percent * video.duration;
            
            if (timeTooltip) {
                timeTooltip.textContent = formatTime(previewTime);
                timeTooltip.style.display = 'block';
                timeTooltip.style.left = `${e.clientX - rect.left}px`;
            }
            
            // Generate and show thumbnail preview if video is loaded
            if (previewThumbnail && video.readyState >= 2) {
                // Set the video to the preview time without playing
                const currentTime = video.currentTime;
                
                try {
                    // Draw current frame at preview time to canvas
                    video.currentTime = previewTime;
                    
                    // Use a setTimeout to wait for the currentTime to update
                    setTimeout(() => {
                        try {
                            // Draw the video frame to canvas
                            previewContext.drawImage(video, 0, 0, previewCanvas.width, previewCanvas.height);
                            previewThumbnail.style.backgroundImage = `url(${previewCanvas.toDataURL()})`;
                            
                            // Position the preview
                            timelinePreview.style.display = 'block';
                            timelinePreview.style.left = `${percent * 100}%`;
                            
                            // Reset video to original position
                            video.currentTime = currentTime;
                        } catch (err) {
                            console.warn('Error generating thumbnail preview:', err);
                        }
                    }, 50);
                } catch (err) {
                    console.warn('Error setting currentTime for preview:', err);
                    // Reset the video to original position
                    video.currentTime = currentTime;
                }
            }
        }
        
        function hideTimeTooltip() {
            if (timeTooltip) {
                timeTooltip.style.display = 'none';
            }
            
            if (timelinePreview) {
                timelinePreview.style.display = 'none';
            }
        }
        
        function toggleSpeedDropdown(e) {
            e.stopPropagation();
            speedDropdown.classList.toggle('active');
        }
        
        function setPlaybackSpeed(speed) {
            video.playbackRate = speed;
            if (speedLabel) {
                speedLabel.textContent = `${speed}x`;
            }
            
            // Highlight the selected speed option
            if (speedOptions) {
                speedOptions.forEach(option => {
                    const optionSpeed = parseFloat(option.getAttribute('data-speed'));
                    if (optionSpeed === speed) {
                        option.classList.add('active');
                    } else {
                        option.classList.remove('active');
                    }
                });
            }
        }
        
        // Function to check and update duration
        function checkAndUpdateDuration() {
            if (!totalTimeEl) return;
            
            // Always get the latest duration
            const currentDuration = video.duration;
            const isValidDuration = isFinite(currentDuration) && currentDuration > 0;
            
            console.log('Checking duration:', currentDuration, 'valid:', isValidDuration, 
                        'readyState:', video.readyState);
            
            if (isValidDuration) {
                totalTimeEl.textContent = formatTime(currentDuration);
                console.log(`✓ Updated duration display: ${formatTime(currentDuration)}`);
                
                // Clear interval if we've successfully updated the duration
                if (durationUpdateTimer) {
                    console.log('Clearing duration timer after successful detection');
                    clearInterval(durationUpdateTimer);
                    durationUpdateTimer = null;
                }
            } else if (!durationUpdateTimer) {
                console.log('No valid duration yet, starting detection timer');
                startDurationDetection();
            }
        }
        
        // Set up a backup method to get duration if metadata fails
        function startDurationDetection() {
            // Clear any existing timer
            if (durationUpdateTimer) {
                clearInterval(durationUpdateTimer);
            }
            
            console.log('Starting duration detection timer');
            durationUpdateAttempts = 0;
            
            // Set up timer to check duration periodically
            durationUpdateTimer = setInterval(() => {
                durationUpdateAttempts++;
                
                // Get latest duration
                const currentDuration = video.duration;
                const isValidDuration = isFinite(currentDuration) && currentDuration > 0;
                
                console.log(`Duration check attempt ${durationUpdateAttempts}:`, 
                           currentDuration, 'valid:', isValidDuration);
                
                // Try to get duration
                if (isValidDuration) {
                    if (totalTimeEl) {
                        totalTimeEl.textContent = formatTime(currentDuration);
                        console.log(`✓ Duration detected via timer: ${currentDuration}s`);
                    }
                    clearInterval(durationUpdateTimer);
                    durationUpdateTimer = null;
                } else if (durationUpdateAttempts >= 20) {
                    // After 10 seconds (20 * 500ms), give up and show a placeholder
                    clearInterval(durationUpdateTimer);
                    durationUpdateTimer = null;
                    if (totalTimeEl) {
                        console.warn('Unable to detect video duration after multiple attempts');
                        totalTimeEl.textContent = '--:--'; // Better placeholder
                    }
                } else {
                    // Try additional detection methods based on readyState
                    if (video.readyState >= 1) {
                        try {
                            // Sometimes seeking to a position can trigger metadata load
                            if (durationUpdateAttempts % 2 === 0 && video.seekable.length > 0) {
                                // Try to seek to the beginning to force duration detection
                                const wasPlaying = !video.paused;
                                const originalTime = video.currentTime;
                                
                                // Only seek if we're not already at 0
                                if (video.currentTime > 0.5) {
                                    console.log('Attempting to seek to trigger duration detection');
                                    video.currentTime = 0.1;
                                    // If seeking changed the time, restore it
                                    setTimeout(() => {
                                        if (wasPlaying) video.play();
                                        if (originalTime > 0.5) video.currentTime = originalTime;
                                    }, 100);
                                }
                            }
                            
                            // Try direct read one more time after other methods
                            if (isFinite(video.duration) && video.duration > 0) {
                                if (totalTimeEl) {
                                    totalTimeEl.textContent = formatTime(video.duration);
                                    console.log(`✓ Direct duration read success: ${video.duration}s`);
                                    clearInterval(durationUpdateTimer);
                                    durationUpdateTimer = null;
                                }
                            }
                        } catch (e) {
                            console.warn('Error in duration detection attempt:', e);
                        }
                    }
                }
            }, 500); // Check every 500ms
        }

        // Start duration detection when video starts loading
        video.addEventListener('loadstart', function() {
            console.log('Video loading started, initializing duration detection');
            startDurationDetection();
        });

        // In the createVideoPlayer function, add a direct timeupdate event listener
        video.addEventListener('timeupdate', function() {
            // Update current time display
            const currentTimeEl = container.querySelector('.current-time');
            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(video.currentTime || 0);
            }
            
            // Update progress bar
            const timeline = container.querySelector('.video-timeline');
            if (timeline && isFinite(video.duration) && video.duration > 0) {
                const percent = (video.currentTime / video.duration) * 100;
                timeline.style.width = `${percent}%`;
            }
        });

        // Add progress polling for fallback
        let progressInterval;
        video.addEventListener('play', function() {
            // Clear any existing interval
            if (progressInterval) {
                clearInterval(progressInterval);
            }
            
            // Set up a polling interval for updating the time
            progressInterval = setInterval(function() {
                const currentTimeEl = container.querySelector('.current-time');
                if (currentTimeEl) {
                    currentTimeEl.textContent = formatTime(video.currentTime || 0);
                }
                
                // Update progress bar
                const timeline = container.querySelector('.video-timeline');
                if (timeline && isFinite(video.duration) && video.duration > 0) {
                    const percent = (video.currentTime / video.duration) * 100;
                    timeline.style.width = `${percent}%`;
                }
            }, 100); // Update 10 times per second
        });

        video.addEventListener('pause', function() {
            // Clear the interval when paused
            if (progressInterval) {
                clearInterval(progressInterval);
                progressInterval = null;
            }
        });

        video.addEventListener('ended', function() {
            // Clear the interval when ended
            if (progressInterval) {
                clearInterval(progressInterval);
                progressInterval = null;
            }
        });

        // Add event listener to prevent auto-play when stopped
        video.addEventListener('timeupdate', function(e) {
            // Don't auto-play if we've just stopped the video
            if (video.dataset.forceStopped === 'true') {
                if (!video.paused) {
                    console.log('Preventing auto-restart after stop');
                    video.pause();
                }
            }
        });
    });
});

// Function to create a video player HTML structure
function createVideoPlayer(videoSource, videoTitle, posterImage = null) {
    // Create main container
    const container = document.createElement('div');
    container.className = 'custom-video-container';
    
    // Debug info
    console.log(`Creating video player for source: ${videoSource}`);
    
    // Create video element with correct source handling
    const video = document.createElement('video');
    video.className = 'custom-video-player';
    video.controlsList = 'nodownload';
    video.preload = 'metadata'; // Start with metadata for faster loading
    
    // Add loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'loading-indicator';
    container.appendChild(loadingIndicator);
    container.classList.add('loading');
    
    // Add error message container
    const errorMessage = document.createElement('div');
    errorMessage.className = 'video-error-message';
    errorMessage.innerHTML = `
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <p>Sorry, there was an error loading the video.</p>
        <button class="retry-button">Retry</button>
    `;
    container.appendChild(errorMessage);
    
    // Check video source format
    if (videoSource && (videoSource.toLowerCase().includes('.mp4') || 
                        videoSource.toLowerCase().includes('.webm') || 
                        videoSource.toLowerCase().includes('.ogg') || 
                        videoSource.toLowerCase().includes('.mov'))) {
        // Direct video file
        console.log('Detected direct video file:', videoSource);
        
        // Set a timeout to handle cases where the video takes too long to load
        const loadTimeout = setTimeout(() => {
            if (container.classList.contains('loading')) {
                console.warn('Video loading timeout - showing error');
                container.classList.remove('loading');
                container.classList.add('error');
                const errorText = container.querySelector('.video-error-message p');
                if (errorText) {
                    errorText.textContent = 'Video is taking too long to load. Please try again.';
                }
                const errorMessage = container.querySelector('.video-error-message');
                if (errorMessage) {
                    errorMessage.style.display = 'flex';
                }
            }
        }, 15000); // 15 second timeout
        
        // Clear timeout if video loads successfully
        video.addEventListener('canplay', () => {
            clearTimeout(loadTimeout);
        });
        
        const source = document.createElement('source');
        source.src = videoSource;
        source.type = videoSource.toLowerCase().includes('.mp4') ? 'video/mp4' : 
                    videoSource.toLowerCase().includes('.webm') ? 'video/webm' : 
                    videoSource.toLowerCase().includes('.mov') ? 'video/quicktime' :
                    'video/ogg';
        video.appendChild(source);
        video.controls = false; // We're using custom controls
        video.preload = 'auto'; // Change to auto for better playback
        video.playsInline = true; // Better mobile support
        video.muted = false;
        
        // Critical event listeners for debugging and error handling
        video.addEventListener('loadedmetadata', () => {
            console.log(`Video metadata loaded for source: ${videoSource}`);
            container.classList.remove('loading');
            
            // Ensure total time is updated
            const totalTimeEl = container.querySelector('.total-time');
            if (totalTimeEl && isFinite(video.duration)) {
                totalTimeEl.textContent = formatTime(video.duration);
                console.log(`Set duration from metadata: ${video.duration}s`);
            } else {
                console.log('Duration not available in metadata or invalid:', video.duration);
            }
        });
        
        video.addEventListener('durationchange', () => {
            console.log(`Video duration updated: ${video.duration}s`);
            const totalTimeEl = container.querySelector('.total-time');
            if (totalTimeEl && isFinite(video.duration) && video.duration > 0) {
                totalTimeEl.textContent = formatTime(video.duration);
                console.log(`Updated duration display from change event: ${formatTime(video.duration)}`);
            } else {
                console.log('Duration still not available or invalid in change event');
            }
        });
        
        video.addEventListener('canplay', () => {
            console.log('Video can play now');
            container.classList.remove('loading');
        });
        
        video.addEventListener('error', (e) => {
            console.error('Video error occurred:', video.error, e);
            container.classList.add('error');
            container.classList.remove('loading');
            
            // Show clear error message based on error code
            const errorText = container.querySelector('.video-error-message p');
            if (errorText) {
                if (video.error) {
                    switch (video.error.code) {
                        case 1:
                            errorText.textContent = 'Video loading aborted.';
                            break;
                        case 2:
                            errorText.textContent = 'Network error occurred. Please check your connection.';
                            break;
                        case 3:
                            errorText.textContent = 'Error decoding video. The file may be corrupted.';
                            break;
                        case 4:
                            errorText.textContent = 'Video format not supported by your browser.';
                            break;
                        default:
                            errorText.textContent = 'An error occurred while playing the video.';
                    }
                } else {
                    errorText.textContent = 'Could not load the video. Please try again.';
                }
            }
        });
    } else {
        // Assume it's an iframe source (YouTube, Vimeo, Panopto, etc.)
        const iframe = document.createElement('iframe');
        iframe.src = videoSource;
        iframe.frameBorder = "0";
        iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen";
        iframe.allowFullscreen = true;

        // Add proper styling for iframe to fill container
        iframe.style.position = "absolute";
        iframe.style.top = "0";
        iframe.style.left = "0";
        iframe.style.width = "100%";
        iframe.style.height = "100%";
        iframe.style.border = "1px solid #464646";
        iframe.style.boxSizing = "border-box";

        // Replace the video player with iframe
        container.appendChild(iframe);
        container.classList.remove('loading');
        return container;
    }
    
    if (posterImage) {
        video.poster = posterImage;
    }
    
    container.appendChild(video);
    
    // Create video title
    if (videoTitle) {
        const titleEl = document.createElement('div');
        titleEl.className = 'video-title';
        titleEl.textContent = videoTitle;
        container.appendChild(titleEl);
    }
    
    // Create top controls
    const topControls = document.createElement('div');
    topControls.className = 'video-top-controls';
    container.appendChild(topControls);
    
    // Create controls container
    const controlsContainer = document.createElement('div');
    controlsContainer.className = 'video-controls-container';
    
    // Create timeline
    const timelineContainer = document.createElement('div');
    timelineContainer.className = 'video-timeline-container';
    
    const timelinePreview = document.createElement('div');
    timelinePreview.className = 'timeline-preview';
    timelinePreview.innerHTML = '<div class="preview-thumbnail"></div>';
    timelineContainer.appendChild(timelinePreview);
    
    const timeline = document.createElement('div');
    timeline.className = 'video-timeline';
    timelineContainer.appendChild(timeline);
    controlsContainer.appendChild(timelineContainer);
    
    // Create controls
    const controls = document.createElement('div');
    controls.className = 'video-controls';
    
    // Left controls
    const leftControls = document.createElement('div');
    leftControls.className = 'video-left-controls';
    
    const playPauseBtn = document.createElement('button');
    playPauseBtn.className = 'video-control-button play-pause-btn';
    playPauseBtn.innerHTML = `
        <svg class="play-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="5 3 19 12 5 21" fill="currentColor"/>
        </svg>
        <svg class="pause-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="6" y="4" width="4" height="16" fill="currentColor"/>
            <rect x="14" y="4" width="4" height="16" fill="currentColor"/>
        </svg>
    `;
    leftControls.appendChild(playPauseBtn);
    
    // Add stop button with completely new implementation
    const stopBtn = document.createElement('button');
    stopBtn.className = 'video-control-button stop-btn';
    stopBtn.setAttribute('aria-label', 'Stop video');
    stopBtn.innerHTML = `
        <svg class="stop-icon" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <rect x="5" y="5" width="14" height="14" fill="white"/>
        </svg>
    `;
    
    // Add explicit styling for better clickability
    stopBtn.style.cursor = 'pointer';
    stopBtn.style.position = 'relative';
    stopBtn.style.zIndex = '20';
    stopBtn.style.marginLeft = '5px';
    stopBtn.style.marginRight = '5px';
    stopBtn.style.display = 'flex';
    stopBtn.style.alignItems = 'center';
    stopBtn.style.justifyContent = 'center';
    stopBtn.style.width = '32px';
    stopBtn.style.height = '32px';
    stopBtn.style.border = 'none';
    stopBtn.style.background = 'transparent';
    
    // Add a click handler using the global function
    stopBtn.onclick = function(e) {
        console.log('Stop button direct click handler triggered');
        e.preventDefault();
        e.stopPropagation();
        
        // Call the global stop function
        window.stopVideoPlayer(container);
        return false;
    };
    
    // Add additional event for better capture
    stopBtn.addEventListener('click', function(e) {
        console.log('Stop button addEventListener triggered');
        e.preventDefault();
        e.stopPropagation();
        
        // Call the global stop function
        window.stopVideoPlayer(container);
        return false;
    }, true);
    
    leftControls.appendChild(stopBtn);
    
    const volumeContainer = document.createElement('div');
    volumeContainer.className = 'volume-container';
    
    const volumeBtn = document.createElement('button');
    volumeBtn.className = 'video-control-button volume-btn';
    volumeBtn.innerHTML = `
        <svg class="mute-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19" fill="currentColor"/>
            <path d="M15 8a5 5 0 0 1 0 8" stroke="currentColor"/>
            <path d="M18 5a9 9 0 0 1 0 14" stroke="currentColor"/>
        </svg>
        <svg class="unmute-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19" fill="currentColor"/>
            <line x1="23" y1="9" x2="17" y2="15"/>
            <line x1="17" y1="9" x2="23" y2="15"/>
        </svg>
    `;
    volumeContainer.appendChild(volumeBtn);
    
    const volumeSlider = document.createElement('div');
    volumeSlider.className = 'volume-slider';
    
    const volumeProgress = document.createElement('div');
    volumeProgress.className = 'volume-slider-progress';
    volumeSlider.appendChild(volumeProgress);
    
    volumeContainer.appendChild(volumeSlider);
    leftControls.appendChild(volumeContainer);
    
    const timeDisplay = document.createElement('div');
    timeDisplay.className = 'video-time';
    timeDisplay.innerHTML = '<span class="current-time">0:00</span> / <span class="total-time">0:00</span>';
    
    // Make sure time display is visible
    timeDisplay.style.color = 'white';
    timeDisplay.style.fontWeight = 'bold';
    timeDisplay.style.fontSize = '14px';
    timeDisplay.style.padding = '0 8px';
    
    // Add duration tooltip container
    const timeTooltip = document.createElement('div');
    timeTooltip.className = 'time-tooltip';
    timeTooltip.style.display = 'none';
    timeDisplay.appendChild(timeTooltip);
    
    leftControls.appendChild(timeDisplay);
    
    controls.appendChild(leftControls);
    
    // Right controls
    const rightControls = document.createElement('div');
    rightControls.className = 'video-right-controls';
    
    // Playback speed control
    const speedBtn = document.createElement('button');
    speedBtn.className = 'video-control-button speed-btn';
    speedBtn.innerHTML = `
        <span class="speed-label">1x</span>
    `;
    
    // Speed dropdown menu
    const speedDropdown = document.createElement('div');
    speedDropdown.className = 'speed-dropdown';
    speedDropdown.innerHTML = `
        <div class="speed-option" data-speed="0.25">0.25x</div>
        <div class="speed-option" data-speed="0.5">0.5x</div>
        <div class="speed-option" data-speed="0.75">0.75x</div>
        <div class="speed-option" data-speed="1">1x</div>
        <div class="speed-option" data-speed="1.25">1.25x</div>
        <div class="speed-option" data-speed="1.5">1.5x</div>
        <div class="speed-option" data-speed="1.75">1.75x</div>
        <div class="speed-option" data-speed="2">2x</div>
    `;
    speedBtn.appendChild(speedDropdown);
    rightControls.appendChild(speedBtn);
    
    const fullscreenBtn = document.createElement('button');
    fullscreenBtn.className = 'video-control-button fullscreen-btn';
    fullscreenBtn.innerHTML = `
        <svg class="fullscreen-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
        </svg>
        <svg class="exit-fullscreen-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 14h3v3m14-3h-3v3M4 10h3V7m14 3h-3V7"/>
        </svg>
    `;
    rightControls.appendChild(fullscreenBtn);
    
    controls.appendChild(rightControls);
    controlsContainer.appendChild(controls);
    
    container.appendChild(controlsContainer);
    
    // Add event listener for loadeddata which might have more reliable duration
    video.addEventListener('loadeddata', () => {
        console.log('Video loadeddata event fired');
        const totalTimeEl = container.querySelector('.total-time');
        if (totalTimeEl && isFinite(video.duration) && video.duration > 0) {
            totalTimeEl.textContent = formatTime(video.duration);
            console.log(`✓ Duration from loadeddata: ${video.duration}s`);
        } else {
            console.log('Duration still not available in loadeddata event');
        }
    });
    
    // Try triggering video.load() directly after a short delay
    setTimeout(() => {
        try {
            console.log('Triggering explicit video.load()');
            video.load();
        } catch (e) {
            console.error('Error in explicit load:', e);
        }
    }, 100);
    
    return container;
}

// Global function to replace video modal with custom player
function initializeVideoModal() {
    // Find video modal container
    const videoModal = document.getElementById('courseTrailer');
    if (!videoModal) {
        console.error('Video modal not found');
        return;
    }
    
    console.log('Initializing video modal');
    
    // Get close button
    const closeBtn = videoModal.querySelector('.close-modal');
    
    // Create a custom container for the video player
    const customPlayerContainer = document.createElement('div');
    customPlayerContainer.className = 'custom-player-wrapper';
    customPlayerContainer.style.width = '90vw';
    customPlayerContainer.style.maxWidth = '1200px';
    customPlayerContainer.style.aspectRatio = '16/9';
    
    // Add custom player container to modal
    videoModal.insertBefore(customPlayerContainer, closeBtn);
    
    // Remove the original iframe container
    const originalContainer = videoModal.querySelector('.video-container');
    if (originalContainer) {
        originalContainer.remove();
    }
    
    // Update trailer button click event
    const trailerButtons = document.querySelectorAll('.trailer-button');
    console.log(`Found ${trailerButtons.length} trailer buttons`);
    
    trailerButtons.forEach(button => {
        const videoUrl = button.getAttribute('data-video');
        if (!videoUrl) {
            console.warn('Trailer button has no data-video attribute');
            
            // Disable the button visually and prevent clicks
            button.setAttribute('disabled', 'disabled');
            button.style.opacity = '0.5';
            button.style.cursor = 'not-allowed';
            button.setAttribute('title', 'Video not available');
            
            // Add a warning class for styling
            button.classList.add('video-unavailable');
            
            // Add event listener to show message when clicked
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                alert('Sorry, this video is not available.');
            });
            
            return;
        }
        
        console.log(`Trailer button with video URL: ${videoUrl}`);
        
        // Find the course title
        let courseTitle = '';
        const courseCard = button.closest('.featured-course');
        if (courseCard) {
            const titleEl = courseCard.querySelector('.preview-content h3');
            if (titleEl) {
                courseTitle = titleEl.textContent;
            }
        }
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log(`Clicked trailer button for video: ${videoUrl}`);
            
            // Check if the video URL exists and is valid
            if (!videoUrl) {
                console.error('No video URL provided');
                alert('Sorry, there is no video available for this course.');
                return;
            }
            
            // Check if video file exists
            if (videoUrl.includes('.mp4') || videoUrl.includes('.webm') || videoUrl.includes('.ogg')) {
                fetch(videoUrl, { method: 'HEAD' })
                .then(response => {
                    if (!response.ok) {
                        console.error(`Video file not found: ${videoUrl}`);
                        alert('Sorry, the video file could not be found.');
                        return;
                    }
                    
                    // Continue with opening the modal
                    loadVideoInModal(videoUrl, courseTitle);
                })
                .catch(error => {
                    console.error('Error checking video file:', error);
                    // Try loading anyway, the video player will handle errors
                    loadVideoInModal(videoUrl, courseTitle);
                });
            } else {
                // For iframes, just load directly
                loadVideoInModal(videoUrl, courseTitle);
            }
        });
    });
    
    function loadVideoInModal(videoUrl, courseTitle) {
        // Clear previous player
        customPlayerContainer.innerHTML = '';
        console.log(`Loading video in modal: ${videoUrl}, title: ${courseTitle}`);
        
        // Show loading state on modal
        videoModal.classList.add('loading');
        
        // Create and append the custom video player
        const customPlayer = createVideoPlayer(videoUrl, courseTitle);
        customPlayerContainer.appendChild(customPlayer);
        
        // Track load errors
        let loadTimeout = setTimeout(() => {
            console.warn('Video taking too long to load duration');
            // Try to force duration calculation
            const video = customPlayer.querySelector('.custom-video-player');
            const totalTimeEl = customPlayer.querySelector('.total-time');
            if (video && totalTimeEl) {
                if (isFinite(video.duration) && video.duration > 0) {
                    totalTimeEl.textContent = formatTime(video.duration);
                } else {
                    console.log('Still waiting for duration...');
                }
            }
        }, 3000);
        
        // Show modal
        videoModal.classList.add('active');
        // Lock body scroll when modal is open
        document.body.style.overflow = 'hidden';
        
        // Initialize this specific player
        const video = customPlayer.querySelector('.custom-video-player');
        if (video) {
            console.log('Setting up video player events and controls');
            
            // Make sure video is reset
            video.currentTime = 0;
            
            // Manually check for duration
            const checkModalDuration = () => {
                const totalTimeEl = customPlayer.querySelector('.total-time');
                if (totalTimeEl && isFinite(video.duration) && video.duration > 0) {
                    totalTimeEl.textContent = formatTime(video.duration);
                    console.log('Modal video duration detected:', video.duration);
                    return true;
                }
                return false;
            };
            
            // Try to get duration immediately
            if (!checkModalDuration()) {
                // If not available, set up interval to check
                console.log('Setting up modal duration check interval');
                let modalDurationChecks = 0;
                const modalDurationTimer = setInterval(() => {
                    modalDurationChecks++;
                    if (checkModalDuration() || modalDurationChecks > 20) {
                        clearInterval(modalDurationTimer);
                        if (modalDurationChecks > 20) {
                            console.warn('Failed to detect modal video duration after multiple attempts');
                            const totalTimeEl = customPlayer.querySelector('.total-time');
                            if (totalTimeEl) totalTimeEl.textContent = '∞';
                        }
                    }
                }, 500);
            }
            
            // Auto-play with sound when possible
            video.addEventListener('canplay', () => {
                console.log('Video can play - attempting autoplay');
                videoModal.classList.remove('loading');
                clearTimeout(loadTimeout);
                
                // Update duration one more time
                const totalTimeEl = customPlayer.querySelector('.total-time');
                if (totalTimeEl && isFinite(video.duration) && video.duration > 0) {
                    totalTimeEl.textContent = formatTime(video.duration);
                    console.log('Duration updated from canplay event:', video.duration);
                }
                
                // Set up progress update for modal player specifically
                const updateModalProgress = () => {
                    const currentTimeEl = customPlayer.querySelector('.current-time');
                    const timeline = customPlayer.querySelector('.video-timeline');
                    
                    if (currentTimeEl) {
                        currentTimeEl.textContent = formatTime(video.currentTime || 0);
                    }
                    
                    if (timeline && isFinite(video.duration) && video.duration > 0) {
                        const percent = (video.currentTime / video.duration) * 100;
                        timeline.style.width = `${percent}%`;
                    }
                };
                
                // Set initial time display
                updateModalProgress();
                
                // Create a dedicated interval for this modal player
                const progressInterval = setInterval(updateModalProgress, 100);
                
                // Clear interval when modal is closed
                if (closeBtn) {
                    const originalClickHandler = closeBtn.onclick;
                    closeBtn.onclick = function(e) {
                        clearInterval(progressInterval);
                        if (typeof originalClickHandler === 'function') {
                            originalClickHandler.call(this, e);
                        }
                    };
                }
                
                // Try to play with sound
                const playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise
                        .then(() => {
                            console.log('Autoplay started successfully with sound');
                        })
                        .catch(err => {
                            console.log('Autoplay with sound failed, user interaction required', err);
                            // Show play button prominently
                            const playOverlay = customPlayer.querySelector('.play-pause-overlay');
                            if (playOverlay) {
                                playOverlay.style.display = 'flex';
                                playOverlay.style.opacity = '1';
                            }
                        });
                }
            });
            
            // Trigger a fake mouse move to show controls initially
            setTimeout(() => {
                const event = new MouseEvent('mousemove', {
                    view: window,
                    bubbles: true,
                    cancelable: true
                });
                customPlayer.dispatchEvent(event);
            }, 500);
        }
    }
    
    // Update close button event
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            console.log('Close button clicked - stopping all videos');
            videoModal.classList.remove('active');
            
            // Stop any playing videos using the stopVideoPlayer function
            const videoContainers = videoModal.querySelectorAll('.custom-video-container');
            videoContainers.forEach(container => {
                window.stopVideoPlayer(container);
            });
            
            // Also handle any remaining videos directly
            const videos = videoModal.querySelectorAll('video');
            videos.forEach(video => {
                if (video && typeof video.pause === 'function') {
                    video.pause();
                    video.currentTime = 0;
                }
            });
            
            // Also remove body scroll lock
            document.body.style.overflow = '';
        });
    }
    
    // Handle clicking outside the video player to close modal
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            console.log('Backdrop clicked - stopping all videos');
            videoModal.classList.remove('active');
            
            // Stop any playing videos using the stopVideoPlayer function
            const videoContainers = videoModal.querySelectorAll('.custom-video-container');
            videoContainers.forEach(container => {
                window.stopVideoPlayer(container);
            });
            
            // Also handle any remaining videos directly
            const videos = videoModal.querySelectorAll('video');
            videos.forEach(video => {
                if (video && typeof video.pause === 'function') {
                    video.pause();
                    video.currentTime = 0;
                }
            });
            
            document.body.style.overflow = '';
        }
    });
    
    // Handle Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && videoModal.classList.contains('active')) {
            console.log('Escape key pressed - stopping all videos');
            videoModal.classList.remove('active');
            
            // Stop any playing videos using the stopVideoPlayer function
            const videoContainers = videoModal.querySelectorAll('.custom-video-container');
            videoContainers.forEach(container => {
                window.stopVideoPlayer(container);
            });
            
            // Also handle any remaining videos directly
            const videos = videoModal.querySelectorAll('video');
            videos.forEach(video => {
                if (video && typeof video.pause === 'function') {
                    video.pause();
                    video.currentTime = 0;
                }
            });
            
            document.body.style.overflow = '';
        }
    });
}

// Initialize video modal when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeVideoModal); 