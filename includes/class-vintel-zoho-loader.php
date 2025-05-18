<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Vintel_Zoho_Loader {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_settings_page() {
        add_options_page(
            __( 'Vintel Zoho Desk', 'vintel-zoho-desk-connector' ),
            __( 'Vintel Zoho Desk', 'vintel-zoho-desk-connector' ),
            'manage_options',
            'vintel-zoho-desk',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'vintel_zoho_options', 'vintel_zoho_debug' );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Vintel Zoho Desk Settings', 'vintel-zoho-desk-connector' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields( 'vintel_zoho_options' );
                    do_settings_sections( 'vintel_zoho_options' );
                    $debug = get_option( 'vintel_zoho_debug', 0 );
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Debug Mode', 'vintel-zoho-desk-connector' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="vintel_zoho_debug" value="1" <?php checked( 1, $debug, true ); ?> />
                                <?php esc_html_e( 'Log API requests and responses', 'vintel-zoho-desk-connector' ); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( 'Logs will be saved to wp-content/uploads/zoho-integration.log', 'vintel-zoho-desk-connector' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new Vintel_Zoho_Loader();
