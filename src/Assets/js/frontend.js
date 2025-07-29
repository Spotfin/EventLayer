/**
 * EventLayer Frontend JavaScript
 * 
 * @package EventLayer
 * @since 1.0.0
 */

(function () {
    'use strict';

    // Ensure dataLayer exists
    window.dataLayer = window.dataLayer || [];

    /**
     * EventLayer namespace
     */
    window.EventLayer = window.EventLayer || {};

    /**
     * Push event to dataLayer
     */
    EventLayer.pushEvent = function (eventData) {
        if (typeof eventData === 'object' && eventData !== null) {
            window.dataLayer.push(eventData);

            // Debug logging if enabled
            if (EventLayer.debug) {
                console.log('EventLayer: Pushed event', eventData);
            }
        }
    };

    /**
     * Track page view
     */
    EventLayer.trackPageView = function () {
        EventLayer.pushEvent({
            'event': 'page_view',
            'page_title': document.title,
            'page_location': window.location.href,
            'page_path': window.location.pathname
        });
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-track page view if enabled
        if (EventLayer.autoTrackPageView !== false) {
            EventLayer.trackPageView();
        }
    });

})();
