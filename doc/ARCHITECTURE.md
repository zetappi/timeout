# Architecture Overview - User Timeout Extension

## Project Structure

```
timeout/
├── acp/                    # Admin Control Panel (ACP) modules
│   └── main_module.php    # ACP main interface
├── adm/                    # Admin backend logic
├── config/                 # Configuration files
├── controller/             # Main controller classes
│   └── main_controller.php # Primary request handler
├── core/                   # Core business logic
│   └── timeout_manager.php # Timeout application and validation
├── event/                  # Event listeners
│   └── main_listener.php   # phpBB event hooks
├── language/               # Localization files
│   ├── en/                 # English strings
│   └── it/                 # Italian strings
├── mcp/                    # Moderator Control Panel (MCP) modules
│   └── main_module.php    # MCP interface
├── migrations/             # Database schema migrations
│   └── install_*.php      # Installation/upgrade scripts
├── notification/           # User notifications
│   └── timeout_notification.php
├── styles/                 # CSS stylesheets
├── tests/                  # Unit and integration tests
├── composer.json           # Composer metadata
├── ext.php                 # Extension entry point
└── FUNZIONI_E_SUGGERIMENTI.md  # Feature suggestions
```

## Key Components

### 1. Event Listener (`event/main_listener.php`)
- Hooks into phpBB events to inject timeout logic
- Sets template variables for button visibility
- Validates user permissions before rendering UI

### 2. Controller (`controller/main_controller.php`)
- Handles HTTP requests from MCP/ACP
- Validates input and applies business logic
- Returns responses (JSON for AJAX, HTML for page views)

### 3. Timeout Manager (`core/timeout_manager.php`)
- Core business logic for timeouts
- Methods for creating, retrieving, updating, deleting timeouts
- Duration validation and maximum timeout enforcement

### 4. Database Tables
- `phpbb_user_timeouts`: Main timeout records
- `USERS_TABLE` (modified): Tracks user timeout status
- `SESSIONS_TABLE` (modified): Manages timed-out user sessions

## Data Flow

### Applying a Timeout
1. Moderator submits form in MCP
2. Controller validates input (user exists, permissions OK, duration valid)
3. Timeout Manager creates record in `phpbb_user_timeouts`
4. User is notified (if enabled in ACP settings)
5. Moderator log entry created
6. Success message shown to moderator

### Checking Timeout Status
1. User attempts to post or perform action
2. Event listener checks if user has active timeout
3. If timeout exists: prevent action, display message
4. If expired: mark as inactive, allow action

## Security Measures

- **CSRF Protection**: All forms use phpBB's `$this->form_token`
- **Input Sanitization**: All user inputs sanitized via phpBB utilities
- **Permission Checks**: ACL-based access control (`m_timeout`, `a_timeout`)
- **SQL Injection Prevention**: Parameterized queries via phpBB ORM

## Localization

Strings stored in:
- `language/en/common.php` - English
- `language/it/common.php` - Italian

Template variables injected via event listener for multilingual UI.

## Template Integration

Timeout button rendered in postrow area with visibility controlled by:
- `S_AUTH_TIMEOUT` (boolean): User has `m_timeout` permission
- `S_SHOW_TIMEOUT_BUTTON` (boolean): Additional conditions met

Button locations:
- Post view: `viewtopic_body_post_buttons_before.html`
- Member profile: `memberlist_view_content_append.html`
