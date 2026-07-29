# Timeout Extension for phpBB

## Overview
This extension adds a timeout feature to phpBB 3.3.15, allowing moderators to temporarily restrict users' ability to post or interact within the forum.

## Updates and Modifications

### Residual Time Display Enhancement
**Date:** 2025-06-10

**Description:** Enhanced the display of remaining timeout duration for users in timeout on the `viewtopic.php` page. The time is now shown in a more readable format, displaying days only if greater than 0, followed by hours and minutes.

**Modified Files:**
- `event/main_listener.php`: Updated to calculate and pass separate values for days, hours, and minutes to the template.
- `styles/all/template/event/viewtopic_body_postrow_rank_after.html`: Adjusted the template to display the remaining time in the format '1 giorno e hh:mm' if days are present, otherwise 'hh:mm'.
- `language/it/info_acp_timeout.php`: Added translation for 'TIMEOUT_DAY' as 'giorni' and 'TIMEOUT_USER_CANNOT_INTERACT' as 'L’utente in modalità timeout non può interagire fino alla scadenza del tempo.'.
- `language/en/info_acp_timeout.php`: Added translation for 'TIMEOUT_USER_CANNOT_INTERACT' as 'The user in timeout mode cannot interact until the time expires.'.
- `styles/all/template/event/viewtopic_body_post_buttons_list_after.html`: Added a notice for users in timeout indicating they cannot interact.

**Impact:** This update improves user experience by providing a clearer representation of timeout duration directly in forum posts and informs users about interaction restrictions during timeout.
