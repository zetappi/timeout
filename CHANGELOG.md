# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
