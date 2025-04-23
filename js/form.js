function showForm(formId) {
			// Hide all forms
			const forms = document.querySelectorAll('.form-content');
			forms.forEach(form => form.style.display = 'none');

			// Show the selected form
			document.getElementById(formId).style.display = 'block';

			// Show the modal
			document.getElementById('popup').style.display = 'block';
		}

		// Close modal when clicking the close button or outside the modal
		window.onload = function() {
			// Get modal element
			const modal = document.getElementById('popup');
			// Get close button
			const span = document.getElementsByClassName('close')[0];

			// When the user clicks the close button, close the modal
			span.onclick = function() {
				modal.style.display = 'none';
			}

			// When the user clicks outside the modal, close it
			window.onclick = function(event) {
				if (event.target == modal) {
					modal.style.display = 'none';
				}
			}
		}
		