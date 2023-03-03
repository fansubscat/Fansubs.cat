function applyDoubleSliderFrom(fromSlider, toSlider, fromInput) {
	const [from, to] = getDoubleSliderParsedValue(fromSlider, toSlider);
	fillDoubleSlider(fromSlider, toSlider, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSlider);
	if (from > to) {
		fromSlider.value = to;
		formatDoubleSliderInput(fromInput, to);
	} else {
		formatDoubleSliderInput(fromInput, from);
	}
}

function applyDoubleSliderTo(fromSlider, toSlider, toInput) {
	const [from, to] = getDoubleSliderParsedValue(fromSlider, toSlider);
	fillDoubleSlider(fromSlider, toSlider, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSlider);
	setDoubleSliderToggleAccessible(fromSlider, toSlider);
	if (from <= to) {
		toSlider.value = to;
		formatDoubleSliderInput(toInput, to);
	} else {
		toSlider.value = from;
		formatDoubleSliderInput(toInput, from);
	}
}

function getDoubleSliderParsedValue(currentFrom, currentTo) {
	const from = parseInt(currentFrom.value, 10);
	const to = parseInt(currentTo.value, 10);
	return [from, to];
}

function fillDoubleSlider(fromSlider, toSlider, sliderColor, rangeColor, controlSlider) {
	const rangeDistance = toSlider.max-toSlider.min;
	const fromPosition = fromSlider.value - toSlider.min;
	const toPosition = toSlider.value - toSlider.min;
	controlSlider.style.background = `linear-gradient(
	to right,
	${sliderColor} 0%,
	${sliderColor} ${(fromPosition)/(rangeDistance)*100}%,
	${rangeColor} ${((fromPosition)/(rangeDistance))*100}%,
	${rangeColor} ${(toPosition)/(rangeDistance)*100}%, 
	${sliderColor} ${(toPosition)/(rangeDistance)*100}%, 
	${sliderColor} 100%)`;
}

function setDoubleSliderToggleAccessible(fromSlider, toSlider) {
	if (Number(toSlider.value) <= Number(fromSlider.min) ) {
		toSlider.style.zIndex = 2;
	} else {
		toSlider.style.zIndex = 0;
	}
}
