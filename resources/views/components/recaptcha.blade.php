@props(['action' => 'submit'])

@error('recaptcha')
    <div class="text-red-600 text-sm mt-2">
        {{ $message }}
    </div>
@enderror

@guest
    <!-- Zone d'erreur reCAPTCHA -->
    <div id="recaptcha-error" class="text-red-600 text-sm mt-2" style="display: none;"></div>

    <!-- reCAPTCHA v2 visible -->
    <div class="g-recaptcha mb-4" 
         data-sitekey="{{ config('services.recaptcha.site_key') }}" 
         data-callback="onRecaptchaSuccess"
         data-size="normal">
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        var isSubmitting = false;

        function showRecaptchaError(message) {
            var errorDiv = document.getElementById('recaptcha-error');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        function hideRecaptchaError() {
            var errorDiv = document.getElementById('recaptcha-error');
            errorDiv.style.display = 'none';
        }

        // Callback quand reCAPTCHA est complété
        function onRecaptchaSuccess(token) {
            // rien
        }

        document.addEventListener('DOMContentLoaded', function() {
            var searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (isSubmitting) {
                        return;
                    }
                    hideRecaptchaError();
                    var recaptchaResponse = grecaptcha.getResponse();
                    if (!recaptchaResponse) {
                        showRecaptchaError('Veuillez compléter le reCAPTCHA');
                        return;
                    }
                    isSubmitting = true;
                    var existingToken = searchForm.querySelector('input[name="g-recaptcha-response"]');
                    if (existingToken) {
                        existingToken.remove();
                    }
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'g-recaptcha-response';
                    input.value = recaptchaResponse;
                    searchForm.appendChild(input);
                    var formData = new FormData(searchForm);
                    fetch(searchForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        console.log('Response data:', data);
                        if (data.success === false) {
                            showRecaptchaError(data.message);
                            grecaptcha.reset();
                        } else {
                            console.log('Redirecting to:', data.redirect);
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(function(error) {
                        showRecaptchaError('Erreur de connexion. Veuillez réessayer.');
                        grecaptcha.reset();
                    })
                    .finally(function() {
                        isSubmitting = false;
                    });
                });
            }
        });
    </script>
@endguest 