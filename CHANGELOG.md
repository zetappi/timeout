# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
