$(document).ready(function() {
	var flair_tooltip = undefined;
	var flair = $('.flair-icon');
	flair.mouseenter(function(e) {
		var x = (e.pageX + 16);
		var y = (e.pageY + 16);

		flair_tooltip = document.createElement('div');
		flair_tooltip.className = 'flair-tooltip';
		flair_tooltip.style.left = x+'px';
		flair_tooltip.style.top = y+'px';

		var icon = document.createElement('img');
		icon.src = this.dataset.image;
		flair_tooltip.appendChild(icon);

		var title = document.createElement('h4');
		title.innerHTML = this.dataset.title;
		flair_tooltip.appendChild(title);

		var description = document.createElement('div');
		description.innerHTML = this.dataset.description;
		flair_tooltip.appendChild(description);

		document.body.appendChild(flair_tooltip);

	}).mousemove(function(e) {
		var x = (e.pageX + 16);
		var y = (e.pageY + 16);

		var rect = document.body.getBoundingClientRect();
		var max_x = rect.width - (400 + 16);
		if (x > max_x) {
			x = max_x;
		}

		flair_tooltip.style.left = x + 'px';
		flair_tooltip.style.top = y + 'px';

	}).mouseleave(function(e) {
		if (flair_tooltip !== undefined) {
			flair_tooltip.parentNode.removeChild(flair_tooltip);
		}
	});
});
