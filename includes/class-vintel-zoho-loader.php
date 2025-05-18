<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Vintel_Zoho_Loader {
    private static $instance = null;
    private $oauth;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-oauth.php';
        require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-api.php';
        require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-elementor-action.php';

        $this->oauth = new Vintel_Zoho_OAuth();

        add_action( 'admin_post_zoho_auth', array( $this->oauth, 'handle_auth_code' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'elementor_pro/forms/actions/register', [ $this, 'register_elementor_actions' ] );
    }

    public function get_oauth() {
        return $this->oauth;
    }

    public function register_elementor_actions( $actions ) {
        $actions->register( new Vintel_Zoho_Elementor_Action() );
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
        register_setting( 'vintel_zoho_settings', 'vintel_zoho_client_id' );
        register_setting( 'vintel_zoho_settings', 'vintel_zoho_client_secret' );
        register_setting( 'vintel_zoho_settings', 'vintel_zoho_redirect_uri' );
        register_setting( 'vintel_zoho_settings', 'zoho_access_token' );
        register_setting( 'vintel_zoho_settings', 'zoho_refresh_token' );
        register_setting( 'vintel_zoho_settings', 'zoho_token_expires_at' );
    }

    private function get_auth_url() {
        $client_id = get_option( 'vintel_zoho_client_id' );
        $redirect_uri = get_option( 'vintel_zoho_redirect_uri' );

        if ( ! $client_id || ! $redirect_uri ) {
            return '#';
        }

        $params = array(
            'scope' => 'Desk.tickets.ALL',
            'client_id' => $client_id,
            'response_type' => 'code',
            'redirect_uri' => $redirect_uri,
            'access_type' => 'offline',
            'prompt' => 'consent',
        );

        return 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query( $params, '', '&' );
    }

    public function render_settings_page() {
        $debug = get_option( 'vintel_zoho_debug', 0 );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Vintel Zoho Desk Settings', 'vintel-zoho-desk-connector' ); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields( 'vintel_zoho_settings' ); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="vintel_zoho_client_id"><?php esc_html_e( 'Zoho Client ID', 'vintel-zoho-desk-connector' ); ?></label></th>
                            <td><input name="vintel_zoho_client_id" type="text" id="vintel_zoho_client_id" value="<?php echo esc_attr( get_option( 'vintel_zoho_client_id' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="vintel_zoho_client_secret"><?php esc_html_e( 'Zoho Client Secret', 'vintel-zoho-desk-connector' ); ?></label></th>
                            <td><input name="vintel_zoho_client_secret" type="text" id="vintel_zoho_client_secret" value="<?php echo esc_attr( get_option( 'vintel_zoho_client_secret' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="vintel_zoho_redirect_uri"><?php esc_html_e( 'Redirect URI', 'vintel-zoho-desk-connector' ); ?></label></th>
                            <td><input name="vintel_zoho_redirect_uri" type="text" id="vintel_zoho_redirect_uri" value="<?php echo esc_attr( get_option( 'vintel_zoho_redirect_uri' ) ); ?>" class="regular-text" /></td>
                        </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>

            <hr />

            <?php
            $token      = get_option( 'zoho_access_token' );
            $expires_at = get_option( 'zoho_token_expires_at' );
            if ( $token ) {
                echo '<p>' . sprintf( esc_html__( 'Access Token Expires: %s', 'vintel-zoho-desk-connector' ), esc_html( $expires_at ) ) . '</p>';
            } else {
                echo '<p>' . esc_html__( 'No access token stored.', 'vintel-zoho-desk-connector' ) . '</p>';
            }
            ?>

            <p>
                <a href="<?php echo esc_url( $this->get_auth_url() ); ?>" class="button button-secondary">
                    <?php esc_html_e( 'Connect to Zoho', 'vintel-zoho-desk-connector' ); ?>
                </a>
            </p>

            <hr />

            <form method="post" action="options.php">
                <?php
                settings_fields( 'vintel_zoho_options' );
                do_settings_sections( 'vintel_zoho_options' );
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

Vintel_Zoho_Loader::get_instance();
