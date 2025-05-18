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
        // TODO: Implement API call to Zoho Desk.
    }

    public function on_export( $element ) {
        // No special export handling needed.
    }
}
