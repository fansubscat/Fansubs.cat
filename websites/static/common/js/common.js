function addValidation(elementId, text){
	$('#'+elementId).addClass('invalid');
	$('#'+elementId+'_validation').text(text);
}

function addValidationOnlyText(elementId, text){
	$('#'+elementId+'_validation').text(text);
}

function removeValidation(elementId){
	$('#'+elementId).removeClass('invalid');
	$('#'+elementId+'_validation').text('');
}

function removeValidationOnlyText(elementId){
	$('#'+elementId+'_validation').text('');
}

function showUserDropdown(){
	$('#user-dropdown').toggleClass('dropdown-show');
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
	if (!event.target.matches('.dropdown-button')) {
		$('.dropdown-content').removeClass('dropdown-show');
	}
}
