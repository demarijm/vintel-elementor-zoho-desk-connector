
# Vintel Elementor to Zoho Desk Plugin  
**Revised Implementation Plan (Action After Submit Integration)**

## Objective  
Develop a custom WordPress plugin that registers as a native “Action After Submit” option in Elementor forms, enabling per-form integration with Zoho Desk. The plugin will authenticate with Zoho using OAuth 2.0, store tokens securely, and submit form data to Zoho Desk’s ticket API based on user-defined field mappings.

---

## Features

| Feature                         | Description |
|---------------------------------|-------------|
| Native Elementor Action         | Appears in the “Actions After Submit” dropdown as “Send to Zoho Desk (Vintel)”. |
| Per-Form Field Mapping          | User can choose which fields correspond to email, subject, and message for each form. |
| Global OAuth Configuration      | Admin settings page to authenticate with Zoho and manage access tokens. |
| Automatic Token Refresh         | Handles token expiration and refreshes access tokens automatically. |
| Logging                         | Logs API calls and errors for debugging purposes. |

---

## Implementation Phases

### Phase 1: Plugin Bootstrapping (1 hour)
- Create plugin structure: `main.php`, `includes/`, `admin/`, etc.
- Add plugin headers, activation hooks, and init routines.

### Phase 2: Register Elementor Action (5 hours)
- Hook into `elementor_pro/forms/actions/register`.
- Register a class extending `ElementorPro\Modules\Forms\Classes\Action_Base`.
- Implement methods:
  - `get_name()` — internal action key.
  - `get_label()` — "Send to Zoho Desk (Vintel)".
  - `register_settings_section()` — expose email/subject/message field mapping.
  - `run()` — triggered on form submit to handle API interaction.

### Phase 3: Admin Settings Page (3 hours)
- Settings available under "Settings > Vintel Zoho Desk".
- Fields:
  - Zoho Client ID
  - Zoho Client Secret
  - Redirect URI
  - “Connect to Zoho” button to authorize
- Display token status and expiration.
- Store values in the WordPress options table.

### Phase 4: OAuth2 Authentication & Token Management (6 hours)
- Handle initial OAuth flow:
  - Build Zoho auth URL with required scopes.
  - Process redirect using `admin-post.php?action=zoho_auth`.
  - Exchange auth code for access and refresh tokens.
- Store:
  - `zoho_access_token`
  - `zoho_refresh_token`
  - `zoho_token_expires_at`
- Implement automatic refresh logic before each API call.

### Token Refresh Handling (2 hours)
- Before every API call, check if the current time is greater than `zoho_token_expires_at`.
- If expired, send a refresh request using:
  - `grant_type=refresh_token`
- Update stored access token and expiration.

### Phase 5: Ticket Creation Logic (4 hours)
- Use `wp_remote_post()` to send form data to `https://desk.zoho.com/api/v1/tickets`.
- Build request payload based on field mapping.
- Include department ID if supplied.
- Gracefully handle errors and refresh tokens when needed.

### Phase 6: Logging & Debug Mode (1.5 hours)
- Save debug logs to `wp-content/uploads/zoho-integration.log`.
- Add optional toggle in admin panel for enabling debug mode.

### Phase 7: Testing & UX Polish (2.5 hours)
- Test OAuth flow, token refresh, and form submissions.
- Test multiple forms with different mappings.
- Ensure logs work as expected.
- Polish admin UI and Elementor integration labels.

---

## Time Estimate Summary

| Task                             | Hours |
|----------------------------------|-------|
| Plugin Structure                 | 1     |
| Elementor Action Integration     | 5     |
| Admin Settings Page              | 3     |
| OAuth2 Flow                      | 6     |
| Token Refresh Logic              | 2     |
| Zoho API Integration             | 4     |
| Logging and Debug Mode           | 1.5   |
| Testing and Final Polish         | 2.5   |
| **Total Estimated Time**         | **25 hours** |

---

## Requirements

1. **Zoho API Credentials**
   - Client ID, Client Secret, Redirect URI
   - Generated from [Zoho API Console](https://api-console.zoho.com/)

2. **Zoho API Scopes**
   - Minimum scope: `Desk.tickets.ALL`

3. **Elementor Pro**
   - Required to access `Actions After Submit` functionality

---

## Future Enhancements

- Support file uploads via Zoho attachments endpoint
- Handle custom Zoho fields via dynamic form mapping
- Add ticket status tracking inside WordPress
- Retry logic for failed API requests
