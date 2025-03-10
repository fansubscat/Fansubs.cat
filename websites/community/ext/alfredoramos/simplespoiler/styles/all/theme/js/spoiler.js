/**
 * Simple Spoiler extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function() {
	'use strict';

	// Polyfill for Element.matches()
	// https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
	if (!Element.prototype.matches) {
		Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
	}

	// Polyfill for Element.closest()
	// https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Polyfill
	if (!Element.prototype.closest) {
		Element.prototype.closest = function(s) {
			let el = this;

			do {
				if (el.matches(s)) {
					return el;
				}

				el = el.parentElement || el.parentNode;
			} while (el !== null && el.nodeType === 1);

			return null;
		};
	}

	// Toggle status icon
	document.body.addEventListener('click', function(e) {
		// Trigger event on the spoiler header
		if (!e.target.closest('.spoiler-header')) {
			return;
		}

		// Generate elements
		let elements = {
			container: e.target.closest('.spoiler')
		};

		if (!elements.container) {
			return;
		}

		// Status icon
		elements.icon = elements.container.querySelector('.spoiler-status > .icon');

		if (!elements.icon) {
			return;
		}

		// Check if spoiler is opened
		let isOpen = elements.container.hasAttribute('open');

		// Toggle FontAwesome icon
		elements.icon.classList.toggle('fa-eye', isOpen);
		elements.icon.classList.toggle('fa-eye-slash', !isOpen);
	});
})();
