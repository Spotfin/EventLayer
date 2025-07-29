<?php

namespace EventLayer\Public;

/**
 * Public-facing functionality for EventLayer.
 * 
 * @package EventLayer\Public
 * @since 1.0.0
 */
class Frontend {

    /**
     * Initialize the public functionality.
     *
     * @return void
     */
    public function init() {
        $this->register_hooks();
    }

    /**
     * Register WordPress hooks.
     *
     * @return void
     */
    private function register_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_head', array( $this, 'output_datalayer_init' ), 1 );
        add_action( 'wp_footer', array( $this, 'output_event_scripts' ) );
    }

    /**
     * Enqueue frontend scripts and styles.
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'eventlayer-frontend',
            plugin_dir_url( __FILE__ ) . '../Assets/js/frontend.js',
            array(),
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'eventlayer-frontend',
            plugin_dir_url( __FILE__ ) . '../Assets/css/frontend.css',
            array(),
            '1.0.0'
        );
    }

    /**
     * Output DataLayer initialization script.
     *
     * @return void
     */
    public function output_datalayer_init() {
        echo '<script>window.dataLayer = window.dataLayer || [];</script>' . "\n";
    }

    /**
     * Output event tracking scripts.
     *
     * @return void
     */
    public function output_event_scripts() {
        $repository = new \EventLayer\Data\EventRuleRepository();
        $rules = $repository->getAll();
        
        if ( empty( $rules ) ) {
            echo '<!-- EventLayer: No event rules found -->' . "\n";
            return;
        }

        echo '<!-- EventLayer Event Scripts -->' . "\n";
        echo '<script>' . "\n";
        
        foreach ( $rules as $rule ) {
            // Parse triggers and parameters (for now, assume they're JSON)
            $triggers = json_decode( $rule->triggers, true );
            $parameters = json_decode( $rule->parameters, true );
            
            if ( ! $triggers || ! $parameters ) {
                continue;
            }

            // Generate JavaScript for this event rule
            echo "// Event: " . esc_js( $rule->event_type ) . "\n";
            echo "document.addEventListener('DOMContentLoaded', function() {\n";
            
            // For now, create a simple click event listener
            if ( isset( $triggers['selector'] ) ) {
                echo "  document.querySelectorAll('" . esc_js( $triggers['selector'] ) . "').forEach(function(element) {\n";
                echo "    element.addEventListener('click', function() {\n";
                echo "      window.dataLayer.push({\n";
                echo "        'event': '" . esc_js( $rule->event_type ) . "'";
                
                foreach ( $parameters as $key => $value ) {
                    echo ",\n        '" . esc_js( $key ) . "': '" . esc_js( $value ) . "'";
                }
                
                echo "\n      });\n";
                echo "      console.log('EventLayer: Pushed " . esc_js( $rule->event_type ) . " event');\n";
                echo "    });\n";
                echo "  });\n";
            }
            
            echo "});\n\n";
        }
        
        echo '</script>' . "\n";
    }
}
