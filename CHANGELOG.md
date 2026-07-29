# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.0.10] - 2026-07-29

### Added
- "Modifica" link next to "Termina Ora" in the Active Timeouts page, opening an inline
  mini-form (no page reload) to change an active timeout's duration
- New duration is always calculated from now (new `timeout_end` = current time +
  selected duration), not extended/reduced from the original `timeout_start`; same
  preset dropdown (30min-30days) used elsewhere, defaulting to 2 hours
- Confirmation prompt before submitting the new duration, summarizing username and
  selected duration (same pattern as the apply/end-timeout confirmations)
- New PHP branch handling `action=edit` in the `active` mode: validates the duration
  against the same whitelist and ACP maximum used by the apply-timeout form, updates
  `user_timeouts.timeout_end`, `USERS_TABLE.user_timeout_end` and
  `SESSIONS_TABLE.session_timeout_end` (mirrors the existing remove-timeout logic,
  but sets a new expiry instead of ending it), and logs the change via the moderator log
- New language strings (EN, IT): `TIMEOUT_NEW_DURATION`, `TIMEOUT_CONFIRM_EDIT`,
  `TIMEOUT_EDIT_SUCCESS`
- New template variables `TIMEOUT_ID` and `EDIT_HASH` (link-hash scoped to
  `timeout_edit_<id>`, separate from the `timeout_remove_<id>` hash already used
  by the remove action) assigned in both the `active` block var loop

### Security
- The inline edit form is protected by both a form token (`check_form_key`, shared
  across all rows since it only validates the key name) and a per-timeout link hash
  (`check_link_hash` against `timeout_edit_<id>`, unique per row) - mirrors the
  existing remove-timeout protection pattern

## [0.0.9] - 2026-07-29

### Added
- Confirmation prompt before ending an active timeout via "End Now", in both the
  "Active Timeouts" and "Timeout History" MCP pages, summarizing the target username
- New template variable `RAW_USERNAME` (plain text, no HTML) assigned alongside the
  existing `USERNAME` (which contains `get_username_string('full', ...)` HTML markup)
  so the confirmation message can safely read the username via a `data-username`
  attribute instead of embedding raw HTML/unescaped text inside inline JavaScript
- `TIMEOUT_CONFIRM_END` (EN, IT) updated to include a `%1$s` username placeholder;
  this string previously existed but was dead code, never referenced by any template
- New string `NO_TIMEOUT_HISTORY` (EN, IT) for the empty-state message on the
  Timeout History page (the previous Twig template referenced `NO_TIMEOUT_RECORDS`,
  a key that was never defined anywhere and would have rendered as a raw key name)

### Changed
- Rewrote `mcp_timeout_active.html` and `mcp_timeout_history.html` from Twig to
  classic phpBB template syntax (same rationale as `mcp_timeout_main.html` in a
  previous release: MCP templates use classic syntax, and several variable names
  in the old Twig templates did not match what the PHP side actually assigns,
  e.g. `loops.timeouts` vs the real block name `timeouts`, `TIMEOUT_MODERATOR`/
  `NO_TIMEOUT_RECORDS` which were never defined in any language file)
- Removed `data-ajax="true"`/`data-refresh="true"` from the "End Now" links: the
  AJAX flow bypassed the new confirmation prompt entirely (phpBB's AJAX confirm
  mechanism is server-driven via `confirm_box()`/JSON, not a static client-side
  attribute), so the link now performs a normal navigation with a full page reload
  after confirming

## [0.0.8] - 2026-07-29

### Added
- Confirmation prompt (JS `confirm()`) before submitting the timeout form, summarizing
  the target username and the selected duration (e.g. "Apply a timeout of 2 hours to
  user "Janacek"?"), to prevent accidental submissions
- New language string `TIMEOUT_CONFIRM_APPLY` (EN, IT) with `%1$s` (username) and
  `%2$s` (duration label) placeholders substituted client-side from the form fields

## [0.0.7] - 2026-07-29

### Fixed
- Restored auto-generated reason text ("Questa notifica ti avverte che hai ricevuto un
  timeout a causa del post...") when opening the timeout form from a specific post
- The PHP side already computed this text into the `REASON` template variable, but the
  rewritten template rendered an empty `<textarea>` instead of `{REASON}`, so the field
  appeared blank even when arriving from a post link
- Note: a curated list of preset/quick reason texts (separate from this auto-generated
  per-post message) was never implemented — it is listed as a future enhancement in
  FUNZIONI_E_SUGGERIMENTI.md, not something removed by the template rewrite

## [0.0.6] - 2026-07-29

### Changed
- Replaced the interactive range slider for timeout duration with a fixed dropdown
  (`<select>`) offering preset values: 30min, 1h, 2h, 4h, 8h, 24h, 2d, 7d, 14d, 30d
- Default duration changed from 60 minutes to 2 hours (120 minutes)
- Added server-side validation restricting `suggested_duration` to the exact set of
  allowed preset values (rejects any other value, including manipulated POST data)
- Removed `timeout_slider.js` (no longer needed, dropdown requires no JavaScript)
- Removed now-unused language strings `DURATION_MINUTES`, `DURATION_READABLE`,
  `DURATION_RANGE_HELP`; added `DURATION_30MIN` through `DURATION_30D` (EN, IT)

## [0.0.5] - 2026-07-29

### Fixed
- Restored "Karma Factor" (recidivism risk) meter in MCP timeout form, lost when the
  template was rewritten from Twig to phpBB classic syntax
- The PHP side (`mcp/main_module.php`) always computed `NEW_RISK_INDEX` and `RISK_COLOR`
  from ban/warning/timeout history, but the rewritten template only rendered
  `BAN_COUNT`/`WARNING_COUNT`, dropping the visual risk meter and the total timeout count
- Added back the risk meter bar (colored by severity) and `TIMEOUT_COUNT` display in the
  User History panel, using the correct template variable name `NEW_RISK_INDEX`
  (the old Twig template incorrectly referenced a non-existent `RISK_INDEX` template var)

## [0.0.4] - 2026-07-29

### Fixed
- Fixed root cause of "you must be logged in to moderate this forum" error on MCP timeout pages
- `module_auth` in database must use a recognized prefixed token (`acl_XXX`, `aclf_XXX`, etc.)
  per phpBB's `functions_module.php::module_auth()` token whitelist; a bare permission name
  like `m_timeout` matches no valid token pattern and is silently stripped before evaluation,
  always resolving to `false`
- Added migration `fix_mcp_auth_prefix` to correct `module_auth` from `m_timeout` to `acl_m_timeout`
  for all three MCP timeout modules (main, active, history)
- Updated `mcp/main_info.php` to use `acl_m_timeout` for future clean installs

## [0.0.3] - 2026-07-29

### Changed
- Replaced MCP duration input field with interactive HTML5 range slider
- Slider ranges from 30 minutes to 30 days (43,200 minutes)
- Added real-time display of duration in both minutes and readable format (days, hours, minutes)
- Updated MCP template with dynamic slider and JavaScript handler
- Updated language strings (EN, IT) with slider-specific labels

### Added
- JavaScript handler for dynamic duration formatting (`timeout_slider.js`)
- Real-time display of readable duration format while adjusting slider
- Visual feedback with min/max duration range indicator

## [0.0.2] - 2026-07-29

### Changed
- Increased maximum timeout duration limit from 7 days (10,080 minutes) to 30 days (43,200 minutes) in ACP settings
- Updated HTML input field `max` attribute from 10080 to 43200
- Added server-side validation to enforce timeout duration limits (1-43,200 minutes)
- Updated language strings (EN, IT) to reflect new duration limits

### Fixed
- Prevented potential bypass of duration limits by adding stricter server-side validation

## [0.0.1] - 2026-07-29

### Added
- Initial release of the User Timeout extension for phpBB 3.3.x
- Timeout management for users with customizable duration (15, 30, 60, 120, 180 minutes)
- Moderator Control Panel (MCP) interface for applying timeouts
- Administration Control Panel (ACP) interface for managing and configuring timeouts
- Filter timeouts by status (active/expired) and username
- User notifications when timed out
- Timeout history for each user
- Multilingual support (Italian, English)
- CSRF protection and input sanitization
- Permission control (`m_timeout` for moderators, `a_timeout` for admins)
- Timeout button visibility restricted to moderators with appropriate permissions
- Moderator log integration for audit trail

### Known Issues
- Extension is still in early development
- Recommend testing thoroughly before production deployment

---

## Conventions for Future Releases

### Semantic Versioning
- **MAJOR** (1.0.0): Breaking changes to API or configuration
- **MINOR** (0.1.0): New features, backwards compatible
- **PATCH** (0.0.2): Bug fixes

### Commit Message Format
All commits should follow [Conventional Commits](https://www.conventionalcommits.org/):
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation only
- `refactor:` Code refactoring
- `perf:` Performance improvement
- `test:` Test additions or changes
- `chore:` Build, CI/CD, dependency updates
