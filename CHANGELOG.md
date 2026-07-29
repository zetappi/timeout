# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-07-29

**Rollback point.** This tag consolidates all the [0.0.2]-[0.0.14] work below
into the extension's official version line (matching `composer.json` and the
`timeout_version` config value already in the database). Everything from the
MCP permission/template fixes through the karma factor redefinition and the
preset reason dropdown is included here as a single stable, verified state.
Roll back to this tag/commit if a future change needs to be reverted.

## [0.0.14] - 2026-07-29

### Added
- Preset reason dropdown above the timeout reason textarea in the "Apply Timeout"
  form, with two predefined options: "You have been placed in Timeout by moderator
  decision" and "You have been placed in Timeout for repeated Off Topic behavior"
  (EN, IT)
- Selecting a preset replaces the textarea content; selecting "Custom" (the default,
  pre-selected option) restores whatever was in the textarea when the page loaded -
  including the existing auto-generated per-post message ("Questa notifica ti
  avverte...") when arriving from a post link, which remains the unchanged default
  behavior
- New language strings: `TIMEOUT_REASON_PRESET`, `TIMEOUT_REASON_PRESET_CUSTOM`,
  `TIMEOUT_REASON_PRESET_MOD_DECISION`, `TIMEOUT_REASON_PRESET_OFFTOPIC` (EN, IT)
- Purely client-side (no PHP/submit changes): the dropdown only pre-fills the
  existing free-text `timeout_reason` field, which the moderator can still edit
  before submitting - the server continues to receive and store whatever text is
  in the textarea at submit time, same as before this change

## [0.0.13] - 2026-07-29

### Changed
- Redefined the Karma Factor formula from an abstract capped 0-100 "risk index"
  weighted by arbitrary points (ban=5, warning=2, timeout=3 out of a fixed
  max_score=100) to a direct, uncapped cumulative duration multiplier based on
  the user's disciplinary history in the last 2 years (window already enforced
  by `get_user_history_from_log`, unchanged):
  - Each ban: +20%
  - Each warning: +10%
  - Each previous timeout: +10%
  - Example: 2 bans + 1 warning + 1 timeout = 40+20+10+10 = 60% → a 1-hour
    timeout becomes 1h36m
- **The karma percentage is now automatically applied** to the duration selected
  from the dropdown when applying a new timeout (`final = base × (1 + karma/100)`),
  rather than being purely informational. The moderator sees both the base
  duration and the resulting effective duration before confirming.
- The base-duration ACP maximum check still validates the *selected* dropdown
  value, not the karma-inflated final value: karma can legitimately push a
  timeout's actual duration above `timeout_max_duration`, since it reflects the
  user's own history rather than a configuration the moderator is choosing to
  exceed.
- Editing an already-active timeout's duration ("Modifica Timeout") remains an
  exact, non-multiplied value by design - the moderator may need to set a
  precise duration there without karma recalculating it again.
- Renamed and simplified `event/main_listener.php`'s
  `calculate_disciplinary_index()` (weights/max_score parameters, unused
  override capability, debug `error_log()` call) into two small focused
  methods: `calculate_karma_percent()` and `apply_karma_multiplier()`.
- Removed dead debug code from `mcp/main_module.php`'s `main` mode: an
  `error_log("Active template: ...")` call and an unreachable HTML `echo`
  fallback block (both left over from earlier debugging, `$this->tpl_name`
  is always set before this code runs).
- Updated `RISK_INDEX_EXPLAIN` (EN, IT) to describe the actual formula instead
  of the previous "0-100" description, which is no longer accurate now that
  the percentage has no upper bound.
- The risk meter bar's visual width is now clamped to 100% via a new
  `RISK_METER_WIDTH` template var, while the displayed percentage number and
  the real value used for the duration multiplier remain uncapped (a user
  with 140% karma sees "140%" and a full bar, not a misleading "100%").

### Added
- `KARMA_EFFECTIVE_DURATION` (EN, IT) and inline JS in `mcp_timeout_main.html`
  that recalculates and displays the effective duration live as the moderator
  changes the dropdown selection, before submitting.

## [0.0.12] - 2026-07-29

### Fixed (pre-deploy verification pass)
- Added missing English translation `TIMEOUT_POST` in `language/en/common.php`
  (existed only in Italian; the Timeout History table's "Related Post" column
  header would have rendered as the raw key `TIMEOUT_POST` on an English-language
  board)
- Added missing `ALL` and `FILTER` strings (EN, IT) in `language/{en,it}/info_acp_timeout.php`,
  used by the ACP "Manage Timeouts" status filter dropdown and its submit button
  (pre-existing gap, not introduced by this session's work, caught by a full
  cross-check of every `{L_*}` placeholder referenced in every template against
  every key actually defined in the language files the corresponding module loads)
- Fixed `migrations/fix_mcp_auth_prefix.php`'s `revert_data()`: it called
  `fix_module_auth_prefix()` again (the same method that applies the fix forward)
  instead of being a no-op, meaning a real `extension:purge` would have re-applied
  the fix instead of reverting it. Corrected to an explicit no-op with a comment
  explaining why (mirrors the "fix-type-migration revert" pattern for schema fixes)

### Verified (pre-deploy)
- `php -l` clean on every `.php` file in the extension (module, controller, event
  listener, migrations, notification)
- Full `extension:disable` → `cache:purge` → `extension:enable` cycle completes
  without errors; all 5 migrations report `migration_schema_done=1` and
  `migration_data_done=1` with a consistent `depends_on()` chain
  (`install_extension` → `fix_mcp_auth` → `fix_mcp_auth_prefix`)
- Confirmed via phpBB core source (`phpbb/db/migrator.php::revert_do()`) that
  `install_extension.php` correctly needs no explicit `revert_data()`: the
  Migrator automatically reverses every `config.add`/`permission.add`/
  `permission.permission_set`/`module.add` step in `update_data()` on a real
  `extension:purge` (via `reverse_update_data()`) - an explicit `revert_data()`
  duplicating those same removals was considered and rejected as redundant/risky
- Live request verification (curl with a real session's sid/cookie/User-Agent,
  not just `php -l`) confirms MCP main/active/history pages render without
  fatal errors or PHP warnings after the full disable/enable cycle
- Every `{L_XXX}` placeholder in every MCP and ACP template cross-checked
  against the language files its module actually loads (not assumed) - two
  real gaps found and fixed (above); remaining unmatched keys confirmed to
  exist in phpBB core language files (`COLON`, `SUBMIT`, `USERNAME`, `ACTION`,
  `CONFIRM_OPERATION`, `NO`, `RESET`, `YES`)
- Confirmed no JavaScript-breaking unescaped ASCII apostrophes remain in any
  language string embedded inside inline `<script>` blocks (`TIMEOUT_CONFIRM_END`,
  `TIMEOUT_CONFIRM_APPLY`, `TIMEOUT_CONFIRM_EDIT`)

### Known limitations (not fixed, documented for awareness)
- `timeout_max_duration` in this environment's database currently holds a
  stale test value (30000) above the 43200 (30-day) ceiling introduced in
  v0.0.2; harmless (the ACP form's own max/min still enforce 1-43200 on
  save) but worth resetting via ACP before relying on the configured value
- A real `extension:purge` has never been executed in this environment
  (only `disable`/`enable` cycles) - the reverse-data behavior described
  above is verified against phpBB core source, not by an actual purge test,
  since purging would destroy this environment's test data

## [0.0.11] - 2026-07-29

### Changed
- Replaced the "Termina Ora"/"Modifica Timeout" text links with Font Awesome icons
  (red X for end-now, pencil for edit) in the Active Timeouts and Timeout History
  pages, following the exact icon pattern already used elsewhere in phpBB's native
  templates (`icon fa-times icon-red`, matching `posting_pm_header.html`)
- Link text is preserved for accessibility via `title` attribute (tooltip) and a
  visually-hidden `<span class="sr-only">` (screen readers still announce the
  action), same pattern phpBB itself uses for icon-only actions

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
