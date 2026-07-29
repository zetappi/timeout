/**
 * Timeout slider interactive handler
 * Converts minutes to readable format (days, hours, minutes)
 */

(function() {
	'use strict';

	function formatDuration(minutes) {
		if (minutes < 1) return '0 minutes';

		var days = Math.floor(minutes / (24 * 60));
		var hours = Math.floor((minutes % (24 * 60)) / 60);
		var mins = minutes % 60;

		var parts = [];

		if (days > 0) {
			parts.push(days + ' day' + (days !== 1 ? 's' : ''));
		}
		if (hours > 0) {
			parts.push(hours + ' hour' + (hours !== 1 ? 's' : ''));
		}
		if (mins > 0 || parts.length === 0) {
			parts.push(mins + ' minute' + (mins !== 1 ? 's' : ''));
		}

		return parts.join(', ');
	}

	document.addEventListener('DOMContentLoaded', function() {
		var slider = document.getElementById('suggested_duration');
		var minutesDisplay = document.getElementById('duration_minutes');
		var readableDisplay = document.getElementById('duration_readable');

		if (!slider || !minutesDisplay || !readableDisplay) {
			return;
		}

		function updateDisplay() {
			var minutes = parseInt(slider.value, 10);
			minutesDisplay.textContent = minutes;
			readableDisplay.textContent = formatDuration(minutes);
		}

		slider.addEventListener('input', updateDisplay);

		// Initial update on page load
		updateDisplay();
	});
})();
