<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Fields; // maybe not needed

class Vintel_Zoho_Elementor_Action extends Action_Base {

    public function get_name() {
        return 'vintel_zoho_desk';
    }

    public function get_label() {
        return __( 'Send to Zoho Desk (Vintel)', 'vintel-zoho-desk-connector' );
    }

    public function register_settings_section( $widget ) {
        $widget->start_controls_section(
            'section_vintel_zoho',
            [
                'label' => __( 'Zoho Desk', 'vintel-zoho-desk-connector' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $fields = $this->get_form_fields( $widget );

        $widget->add_control(
            'vintel_zoho_email_field',
            [
                'label'   => __( 'Email Field', 'vintel-zoho-desk-connector' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $fields,
            ]
        );

        $widget->add_control(
            'vintel_zoho_subject_field',
            [
                'label'   => __( 'Subject Field', 'vintel-zoho-desk-connector' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $fields,
            ]
        );

        $widget->add_control(
            'vintel_zoho_message_field',
            [
                'label'   => __( 'Message Field', 'vintel-zoho-desk-connector' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $fields,
            ]
        );

        $widget->add_control(
            'vintel_zoho_department_id',
            [
                'label'       => __( 'Department ID', 'vintel-zoho-desk-connector' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Optional Zoho Desk department ID', 'vintel-zoho-desk-connector' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $widget->end_controls_section();
    }

    private function get_form_fields( $widget ) {
        $fields = [];
        foreach ( $widget->get_form_fields() as $id => $field ) {
            $fields[ $id ] = $field['field_label'];
        }
        return $fields;
    }

  public function run( $record, $ajax_handler ) {
    $settings = $record->get( 'form_settings' );
    $fields   = $record->get( 'fields' );

    $email_field   = $settings['vintel_zoho_email_field'] ?? '';
    $subject_field = $settings['vintel_zoho_subject_field'] ?? '';
    $message_field = $settings['vintel_zoho_message_field'] ?? '';
    $department    = $settings['vintel_zoho_department_id'] ?? '';

    $email       = isset( $fields[ $email_field ] ) ? sanitize_email( $fields[ $email_field ]['value'] ) : '';
    $subject     = isset( $fields[ $subject_field ] ) ? sanitize_text_field( $fields[ $subject_field ]['value'] ) : '';
    $description = isset( $fields[ $message_field ] ) ? sanitize_textarea_field( $fields[ $message_field ]['value'] ) : '';

    $payload = [
        'email'        => $email,
        'subject'      => $subject,
        'description'  => $description,
    ];

    if ( ! empty( $department ) ) {
        $payload['department_id'] = $department;
    }

    vintel_zoho_log( 'Sending ticket to Zoho Desk: ' . print_r( $payload, true ) );

    $api      = new Vintel_Zoho_API();
    $response = $api->create_ticket( $payload );

    if ( is_wp_error( $response ) ) {
        $message = __( 'Zoho Desk Error: ', 'vintel-zoho-desk-connector' ) . $response->get_error_message();
        $ajax_handler->add_error_message( $message );
        $ajax_handler->is_success = false;
        vintel_zoho_log( 'Error response: ' . $response->get_error_message() );
    } else {
        vintel_zoho_log( 'Ticket created: ' . print_r( $response, true ) );
    }
}


    public function on_export( $element ) {
        // No special export handling needed.
    }
}
