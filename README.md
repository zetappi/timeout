# User Timeout Extension
Author: Marco Zeppa
email: marcozp@gmail.com
(created for https://forum.carlozampa.com)

## Overview
The User Timeout extension allows moderators and administrators to temporarily time out users, preventing them from posting new content on the forum for a defined period. The extension offers a complete interface for managing timeouts, with multilingual support and advanced filtering and management features.


### Basic Features
- Set timeouts for users with customizable duration
- Display timeout indicators for users in posts
- Manage all timeouts from the Moderator Control Panel (MCP)
- Configurable settings from the Administration Control Panel (ACP)
- Full support for user notifications
- Timeout history for each user

- **Duration selection with radio buttons**: Instead of a numeric field, predefined options are available (15, 30, 60, 120, 180 minutes) plus the option for the maximum value configured in the ACP
- **Default value**: The default duration is set to 60 minutes
- **Maximum duration validation**: Prevents moderators from setting timeouts exceeding the maximum limit configured in the ACP
- **Localized error messages**: All error and success messages are fully localized

#### Timeout Management in ACP
- **Filter by status**: Ability to filter timeouts to display only active or expired ones
- **Filter by username**: Search timeouts by username
- **Actions on timeouts**:
  - Immediately end an active timeout
  - Permanently remove a timeout record
- **Status display**: Visual indicators to easily distinguish active timeouts from expired ones

## Installation
1. Download the extension archive
2. Unzip into the `/ext/marcozp/timeout/` folder
3. Go to ACP -> Customisations -> Manage extensions
4. Find the "User Timeout" extension and click "Enable"


### ACP Settings
After installation, you can configure the extension via the administration panel:

1. Navigate to `ACP -> Extensions -> Timeout -> Settings`
2. Configure the following options:
    - **Enable Timeout**: Globally activates or deactivates the timeout functionality
    - **User Notification**: Sends a notification to users when they are timed out
    - **Maximum Timeout Duration**: Sets the maximum limit (in minutes) for a timeout's duration
    - **Log Timeout Actions**: Logs timeout actions in the moderator log

### Using the MCP Module
Moderators with the appropriate permissions can manage timeouts via the moderator control panel:

1. Navigate to `MCP -> Timeout`
2. To assign a new timeout:
    - Search for a user by their username
    - Select the timeout duration from the available radio buttons
    - Specify the reason for the timeout
    - Click "Submit"

### Timeout Management in ACP
Administrators can manage all timeouts from the administration panel:

1. Navigate to `ACP -> Extensions -> Timeout -> Manage Timeouts`
2. Use the available filters to view timeouts:
    - Filter by status (Active/Expired)
    - Filter by username
3. Available actions:
    - **End Now**: Immediately ends an active timeout
    - **Remove Permanently**: Completely deletes the timeout record from the database

## Permissions
After installation, you can configure permissions using the following roles:
- `m_timeout`: Allows moderators to manage timeouts
- `a_timeout`: Allows administrators to configure the extension

### Localization
The extension is fully localized and supports the following languages:
- Italian
- English

All error messages, notifications, and user interfaces are available in both languages.

### Validation and Security
- **Timeout duration validation**: Prevents setting timeouts exceeding the configured maximum limit
- **CSRF Protection**: All operations use security tokens to prevent CSRF attacks
- **Input sanitization**: All user inputs are sanitized before use
- **Permissions management**: Strict permission control for all operations

### Database
The extension uses the following tables:
- `phpbb_user_timeouts`: Stores all timeout records
- Modifies fields in standard phpBB tables:
  - `USERS_TABLE`: Adds fields to track the user's timeout status
  - `SESSIONS_TABLE`: Adds fields to manage timed-out user sessions

## WARNING #####
This extension is still under development.
Installing this extension may cause problems.
The author assumes no responsibility.

## License
GNU General Public License v2.0
