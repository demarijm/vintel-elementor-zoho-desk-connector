# Vintel Elementor to Zoho Desk Connector

This WordPress plugin adds a new **Action After Submit** option to Elementor forms which sends submissions to Zoho Desk as support tickets.

## Features
- Per-form field mapping for email, subject and message
- OAuth2 authentication with Zoho Desk
- Automatic token refresh handling
- Optional debug logging

## Installation
1. Copy this plugin directory into your WordPress `wp-content/plugins` folder.
2. Activate **Vintel Elementor to Zoho Desk Connector** from the WordPress Plugins page.
3. Navigate to **Settings > Vintel Zoho Desk** and enter your Zoho API credentials (Client ID, Client Secret and Redirect URI).
4. Click **Connect to Zoho** to authorize the plugin.

## Usage
1. Edit an Elementor form.
2. In **Actions After Submit** choose **Send to Zoho Desk (Vintel)**.
3. Map the form fields for email, subject and message. Optionally provide a department ID.
4. Save the form.

Submissions will now create tickets in Zoho Desk.

## Development
Unit tests can be run with PHP:

```bash
php tests/run.php
```

Logging can be enabled on the settings page. Logs are written to `wp-content/uploads/zoho-integration.log`.

