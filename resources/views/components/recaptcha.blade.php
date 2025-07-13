@props(['action' => 'submit'])

@error('recaptcha')
    <div class="text-red-600 text-sm mt-2">
        {{ $message }}
    </div>
@enderror

<!-- Debug reCAPTCHA (temporaire) -->
<div id="recaptcha-debug" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4" style="display: none;">
    <strong>Debug reCAPTCHA:</strong>
    <div id="recaptcha-debug-content"></div>
</div>

<!-- Zone d'erreur reCAPTCHA -->
<div id="recaptcha-error" class="text-red-600 text-sm mt-2" style="display: none;"></div>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
<script>
    // Mode debug temporaire
    var DEBUG_RECAPTCHA = true;
    
    function showDebug(message) {
        if (!DEBUG_RECAPTCHA) return;
        
        var debugDiv = document.getElementById('recaptcha-debug');
        var debugContent = document.getElementById('recaptcha-debug-content');
        
        var timestamp = new Date().toLocaleTimeString();
        var logMessage = '[' + timestamp + '] ' + message;
        
        debugContent.innerHTML += '<div>' + logMessage + '</div>';
        debugDiv.style.display = 'block';
        console.log(logMessage);
    }

    function showRecaptchaError(message) {
        var errorDiv = document.getElementById('recaptcha-error');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        showDebug('Error displayed: ' + message);
    }

    function hideRecaptchaError() {
        var errorDiv = document.getElementById('recaptcha-error');
        errorDiv.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        showDebug('reCAPTCHA script loaded');
        showDebug('Environment: {{ config('app.env') }}');
        showDebug('Site key: {{ config('services.recaptcha.site_key') }}');
        
        // Ne s'attacher qu'au formulaire principal de recherche
        var searchForm = document.getElementById('searchForm');
        if (searchForm) {
            showDebug('Found search form, setting up reCAPTCHA');
            
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                showDebug('Search form submission intercepted');
                hideRecaptchaError();
                
                grecaptcha.ready(function() {
                    showDebug('reCAPTCHA ready, executing...');
                    
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: '{{ $action }}'})
                        .then(function(token) {
                            showDebug('reCAPTCHA token received: ' + token.substring(0, 20) + '...');
                            
                            // Supprimer l'ancien token s'il existe
                            var existingToken = searchForm.querySelector('input[name="g-recaptcha-response"]');
                            if (existingToken) {
                                existingToken.remove();
                                showDebug('Removed existing token');
                            }
                            
                            // Créer le nouveau token
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'g-recaptcha-response';
                            input.value = token;
                            searchForm.appendChild(input);
                            showDebug('Token added to form');
                            
                            // Créer FormData pour l'envoi AJAX
                            var formData = new FormData(searchForm);
                            
                            // Envoyer en AJAX
                            fetch(searchForm.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(function(response) {
                                showDebug('Response received, status: ' + response.status);
                                return response.json();
                            })
                            .then(function(data) {
                                showDebug('Response data: ' + JSON.stringify(data));
                                
                                if (data.success === false) {
                                    showRecaptchaError(data.message);
                                    showDebug('reCAPTCHA error: ' + data.message);
                                } else {
                                    // Succès - rediriger ou traiter la réponse
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        // Recharger la page pour afficher les résultats
                                        window.location.reload();
                                    }
                                }
                            })
                            .catch(function(error) {
                                showDebug('Fetch error: ' + error.message);
                                showRecaptchaError('Erreur de connexion. Veuillez réessayer.');
                            });
                        })
                        .catch(function(error) {
                            showDebug('reCAPTCHA error: ' + error.message);
                            showRecaptchaError('Erreur reCAPTCHA: ' + error.message);
                            console.error('reCAPTCHA error:', error);
                        });
                });
            });
        } else {
            showDebug('Search form not found');
        }
    });
</script> 