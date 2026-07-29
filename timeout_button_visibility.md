# Timeout Button Visibility Documentation

## Overview
This document explains the method used to control the visibility of the "Timeout" button in the phpBB extension `marcozp/timeout`. The goal was to ensure that the button is visible only to moderators with the appropriate permissions (`m_timeout`) and not to regular users.

## Method Used for Button Visibility

The visibility of the "Timeout" button is controlled using phpBB template variables and conditional statements within the extension's template files. The key components involved are:

- **Template Variables**: 
  - `S_AUTH_TIMEOUT`: A boolean variable indicating whether the current user has the `m_timeout` permission. This is set in the PHP code based on the user's access control list (ACL).
  - `S_SHOW_TIMEOUT_BUTTON`: A variable used to determine if the button should be shown based on additional conditions (e.g., whether the target user is already in timeout).

- **Conditional Logic in Templates**: 
  - The button is displayed only if certain conditions are met, typically using `<!-- IF ... -->` statements in the HTML templates. For example, `<!-- IF S_AUTH_TIMEOUT and S_SHOW_TIMEOUT_BUTTON -->` ensures the button is shown only to users with the appropriate permissions and under the right circumstances.

- **PHP Backend**: 
  - In the PHP event listener (`main_listener.php`), the `S_AUTH_TIMEOUT` variable is assigned based on the permission check `$this->auth->acl_get('m_timeout')`. This ensures that only moderators with the correct permissions have the button rendered in their view.

## Issues Encountered
Initially, the "Timeout" button was visible to all users, not just moderators. This was due to inconsistencies in how the permission variables were used across different templates and a potential issue with how `S_AUTH_TIMEOUT` was assigned (as a string instead of a boolean in some cases).

## Fixes Applied
To resolve the visibility issue, the following steps were taken:

1. **Uniform Assignment of `S_AUTH_TIMEOUT`**: 
   - In `main_listener.php`, I ensured that `S_AUTH_TIMEOUT` is consistently assigned as a boolean value using `(bool) $this->auth->acl_get('m_timeout')`. This corrected an earlier inconsistency where it was assigned as a string (`'true'` or `'false'`), which could cause issues in template condition evaluations.

2. **Template Logic Correction**: 
   - I reviewed and updated the conditional logic in all relevant template files to ensure that the button is only shown when `S_AUTH_TIMEOUT` is true (indicating the user has moderator permissions). The affected templates include:
     - `viewtopic_body_postrow_content_before.html` (commented out to prevent duplication)
     - `viewtopic_body_postrow_custom.html` (commented out to prevent duplication)
     - `posting_editor_buttons_after.html`
     - `memberlist_view_content_append.html`
     - `viewtopic_body_post_buttons_before.html`
   - The condition was standardized to `<!-- IF S_AUTH_TIMEOUT and ... -->` where applicable, ensuring visibility is restricted to moderators. Additionally, to prevent the button from appearing in multiple locations, I commented out the button code in `viewtopic_body_postrow_content_before.html` and `viewtopic_body_postrow_custom.html`, keeping it only beside moderation buttons in `viewtopic_body_post_buttons_before.html`.

3. **Testing Different Scenarios**: 
   - Throughout the process, I made iterative changes to test visibility for different user roles (regular users and moderators). Initially, the logic was inverted to display the button only to non-moderators as a test, and then reverted to the correct logic for moderators only.

## Final Configuration
After the fixes, the "Timeout" button is now configured to be visible only to moderators with the `m_timeout` permission. The standardized use of `S_AUTH_TIMEOUT` as a boolean and the consistent application of conditional logic in templates ensure that regular users do not see the button.

## Recommendations
- **Testing**: Perform thorough testing with different user roles (guest, regular user, moderator, admin) to confirm the button visibility behaves as expected across all contexts.
- **Cache Clearing**: If the button visibility does not update immediately, consider clearing the phpBB template cache to ensure the changes take effect.
- **Future Modifications**: Any future changes to permission logic or template variables should maintain the consistency of `S_AUTH_TIMEOUT` as a boolean and ensure all relevant templates use the same conditional checks.

If further issues arise, please refer to this documentation or contact the developer for assistance.
