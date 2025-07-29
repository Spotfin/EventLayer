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

    // Initialize settings from WordPress
    if (typeof eventLayerSettings !== 'undefined') {
        EventLayer.debug = eventLayerSettings.debug;
        EventLayer.autoTrackPageView = eventLayerSettings.autoTrackPageView;
    } else {
        // Fallback defaults
        EventLayer.debug = false;
        EventLayer.autoTrackPageView = true;
    }

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
        if (EventLayer.autoTrackPageView) {
            EventLayer.trackPageView();
        }

        // Initialize event rules
        EventLayer.initEventRules();
    });

    /**
     * Initialize event rules from configuration
     */
    EventLayer.initEventRules = function () {
        if (typeof eventLayerConfig === 'undefined' || !Array.isArray(eventLayerConfig)) {
            if (EventLayer.debug) {
                console.log('EventLayer: No event rules configuration found');
            }
            return;
        }

        if (EventLayer.debug) {
            console.log('EventLayer: Initializing', eventLayerConfig.length, 'event rules');
        }

        eventLayerConfig.forEach(function (rule) {
            EventLayer.attachEventListener(rule);
        });
    };

    /**
     * Attach event listener for a rule
     */
    EventLayer.attachEventListener = function (rule) {
        try {
            var elements = document.querySelectorAll(rule.parentSelector);

            if (elements.length === 0) {
                if (EventLayer.debug) {
                    console.log('EventLayer: No elements found for selector:', rule.parentSelector);
                }
                return;
            }

            if (EventLayer.debug) {
                console.log('EventLayer: Attaching listeners to', elements.length, 'elements for rule:', rule.title);
            }

            elements.forEach(function (element) {
                element.addEventListener('click', function (event) {
                    EventLayer.handleRuleEvent(rule, element, event);
                });
            });

        } catch (error) {
            if (EventLayer.debug) {
                console.error('EventLayer: Error attaching listener for rule:', rule.title, error);
            }
        }
    };

    /**
     * Handle rule event trigger
     */
    EventLayer.handleRuleEvent = function (rule, element, event) {
        if (EventLayer.debug) {
            console.log('EventLayer: Rule triggered:', rule.title);
        }

        // Stop propagation if enabled
        if (rule.stopPropagation) {
            event.stopPropagation();
        }

        // Build event data
        var eventData = {
            'event': rule.eventType
        };

        // Add parameters
        if (rule.parameters && Array.isArray(rule.parameters)) {
            rule.parameters.forEach(function (param) {
                var value = EventLayer.extractParameterValue(param, element);
                if (value !== null) {
                    eventData[param.name] = value;
                }
            });
        }

        // Handle delay
        if (rule.triggerDelay > 0) {
            setTimeout(function () {
                EventLayer.pushEvent(eventData);
            }, rule.triggerDelay);
        } else {
            EventLayer.pushEvent(eventData);
        }
    };

    /**
     * Extract parameter value based on target type
     */
    EventLayer.extractParameterValue = function (param, element) {
        try {
            switch (param.targetType) {
                case 'static':
                    return param.defaultValue;

                case 'element_text':
                    if (param.targetSelector) {
                        var targetElement = element.querySelector(param.targetSelector) ||
                            document.querySelector(param.targetSelector);
                        return targetElement ? targetElement.textContent.trim() : param.defaultValue;
                    }
                    return element.textContent.trim() || param.defaultValue;

                case 'element_attribute':
                    if (param.targetSelector) {
                        var attrValue = element.getAttribute(param.targetSelector);
                        return attrValue !== null ? attrValue : param.defaultValue;
                    }
                    return param.defaultValue;

                case 'url_parameter':
                    if (param.targetSelector) {
                        var urlParams = new URLSearchParams(window.location.search);
                        return urlParams.get(param.targetSelector) || param.defaultValue;
                    }
                    return param.defaultValue;

                default:
                    return param.defaultValue;
            }
        } catch (error) {
            if (EventLayer.debug) {
                console.error('EventLayer: Error extracting parameter value:', param.name, error);
            }
            return param.defaultValue;
        }
    };

})();
