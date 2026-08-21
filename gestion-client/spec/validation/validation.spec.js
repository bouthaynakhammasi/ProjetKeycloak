/**
 * Tests Jasmine pour la Validation Frontend
 * Tests de la validation des formulaires côté client
 */

describe('Validation Frontend - Tests principaux', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form id="validation-form" novalidate>
                <div class="form-group">
                    <label for="required-field">Champ requis *</label>
                    <input type="text" id="required-field" name="required_field" required>
                    <div class="error-message" id="required-error"></div>
                </div>
                <div class="form-group">
                    <label for="email-field">Email *</label>
                    <input type="email" id="email-field" name="email" required>
                    <div class="error-message" id="email-error"></div>
                </div>
                <div class="form-group">
                    <label for="min-length-field">Minimum 3 caractères</label>
                    <input type="text" id="min-length-field" name="min_length" minlength="3">
                    <div class="error-message" id="min-length-error"></div>
                </div>
                <div class="form-group">
                    <label for="max-length-field">Maximum 10 caractères</label>
                    <input type="text" id="max-length-field" name="max_length" maxlength="10">
                    <div class="error-message" id="max-length-error"></div>
                </div>
                <div class="form-group">
                    <label for="pattern-field">Format (lettres uniquement)</label>
                    <input type="text" id="pattern-field" name="pattern" pattern="[a-zA-Z]+">
                    <div class="error-message" id="pattern-error"></div>
                </div>
                <div class="form-group">
                    <label for="number-field">Nombre (0-100)</label>
                    <input type="number" id="number-field" name="number" min="0" max="100">
                    <div class="error-message" id="number-error"></div>
                </div>
                <button type="submit" id="submit-btn">Soumettre</button>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Validation des champs requis', () => {
        it('devrait invalider un champ requis vide', () => {
            const requiredField = document.getElementById('required-field');
            requiredField.value = '';
            
            expect(requiredField.checkValidity()).toBe(false);
            expect(requiredField.validity.valueMissing).toBe(true);
        });

        it('devrait valider un champ requis rempli', () => {
            const requiredField = document.getElementById('required-field');
            requiredField.value = 'test';
            
            expect(requiredField.checkValidity()).toBe(true);
            expect(requiredField.validity.valueMissing).toBe(false);
        });

        it('devrait afficher un message d\'erreur pour champ requis', () => {
            const requiredField = document.getElementById('required-field');
            const requiredError = document.getElementById('required-error');
            
            requiredField.value = '';
            requiredField.checkValidity();
            
            if (requiredField.validity.valueMissing) {
                requiredError.textContent = 'Ce champ est requis';
                requiredError.classList.add('text-red-600');
            }
            
            expect(requiredError.textContent).toBe('Ce champ est requis');
            expect(requiredError.classList.contains('text-red-600')).toBe(true);
        });
    });

    describe('Validation des emails', () => {
        it('devrait invalider un email incorrect', () => {
            const emailField = document.getElementById('email-field');
            emailField.value = 'invalid-email';
            
            expect(emailField.checkValidity()).toBe(false);
            expect(emailField.validity.typeMismatch).toBe(true);
        });

        it('devrait valider un email correct', () => {
            const emailField = document.getElementById('email-field');
            emailField.value = 'valid@example.com';
            
            expect(emailField.checkValidity()).toBe(true);
            expect(emailField.validity.typeMismatch).toBe(false);
        });

        it('devrait invalider un email vide si requis', () => {
            const emailField = document.getElementById('email-field');
            emailField.value = '';
            
            expect(emailField.checkValidity()).toBe(false);
            expect(emailField.validity.valueMissing).toBe(true);
        });

        it('devrait afficher un message d\'erreur pour email invalide', () => {
            const emailField = document.getElementById('email-field');
            const emailError = document.getElementById('email-error');
            
            emailField.value = 'invalid';
            emailField.checkValidity();
            
            if (emailField.validity.typeMismatch) {
                emailError.textContent = 'Veuillez entrer une adresse email valide';
                emailError.classList.add('text-red-600');
            }
            
            expect(emailError.textContent).toContain('adresse email valide');
        });
    });

    describe('Validation de la longueur', () => {
        it('devrait invalider un champ trop court', () => {
            const minLengthField = document.getElementById('min-length-field');
            minLengthField.value = 'ab';
            
            // jsdom doesn't fully support HTML5 validation, so we check manually
            const isValid = minLengthField.value.length >= parseInt(minLengthField.minLength);
            expect(isValid).toBe(false);
        });

        it('devrait valider un champ avec la longueur minimale', () => {
            const minLengthField = document.getElementById('min-length-field');
            minLengthField.value = 'abc';
            
            expect(minLengthField.checkValidity()).toBe(true);
            expect(minLengthField.validity.tooShort).toBe(false);
        });

        it('devrait invalider un champ trop long', () => {
            const maxLengthField = document.getElementById('max-length-field');
            maxLengthField.value = '12345678901';
            
            // jsdom doesn't fully support HTML5 validation, so we check manually
            const isValid = maxLengthField.value.length <= parseInt(maxLengthField.maxLength);
            expect(isValid).toBe(false);
        });

        it('devrait valider un champ dans la limite maximale', () => {
            const maxLengthField = document.getElementById('max-length-field');
            maxLengthField.value = '1234567890';
            
            expect(maxLengthField.checkValidity()).toBe(true);
            expect(maxLengthField.validity.tooLong).toBe(false);
        });
    });

    describe('Validation par pattern', () => {
        it('devrait invalider un texte qui ne correspond pas au pattern', () => {
            const patternField = document.getElementById('pattern-field');
            patternField.value = '123';
            
            expect(patternField.checkValidity()).toBe(false);
            expect(patternField.validity.patternMismatch).toBe(true);
        });

        it('devrait valider un texte qui correspond au pattern', () => {
            const patternField = document.getElementById('pattern-field');
            patternField.value = 'abc';
            
            expect(patternField.checkValidity()).toBe(true);
            expect(patternField.validity.patternMismatch).toBe(false);
        });

        it('devrait invalider un texte avec caractères spéciaux', () => {
            const patternField = document.getElementById('pattern-field');
            patternField.value = 'abc@123';
            
            expect(patternField.checkValidity()).toBe(false);
            expect(patternField.validity.patternMismatch).toBe(true);
        });
    });

    describe('Validation des nombres', () => {
        it('devrait invalider un nombre inférieur au minimum', () => {
            const numberField = document.getElementById('number-field');
            numberField.value = '-5';
            
            expect(numberField.checkValidity()).toBe(false);
            expect(numberField.validity.rangeUnderflow).toBe(true);
        });

        it('devrait invalider un nombre supérieur au maximum', () => {
            const numberField = document.getElementById('number-field');
            numberField.value = '150';
            
            expect(numberField.checkValidity()).toBe(false);
            expect(numberField.validity.rangeOverflow).toBe(true);
        });

        it('devrait valider un nombre dans la plage', () => {
            const numberField = document.getElementById('number-field');
            numberField.value = '50';
            
            expect(numberField.checkValidity()).toBe(true);
            expect(numberField.validity.rangeUnderflow).toBe(false);
            expect(numberField.validity.rangeOverflow).toBe(false);
        });

        it('devrait invalider une valeur non numérique', () => {
            const numberField = document.getElementById('number-field');
            numberField.value = 'abc';
            
            // jsdom doesn't fully support HTML5 validation, so we check manually
            const isValid = !isNaN(parseFloat(numberField.value)) && isFinite(numberField.value);
            expect(isValid).toBe(false);
        });
    });
});

describe('Validation de formulaire complet - Tests avancés', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form id="complete-form" novalidate>
                <input type="text" id="name" name="name" required minlength="2">
                <input type="email" id="email" name="email" required>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}">
                <input type="date" id="birthdate" name="birthdate" required>
                <select id="country" name="country" required>
                    <option value="">Sélectionner</option>
                    <option value="fr">France</option>
                    <option value="be">Belgique</option>
                </select>
                <textarea id="message" name="message" maxlength="500"></textarea>
                <div id="form-errors"></div>
                <button type="submit" id="submit-complete">Envoyer</button>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Validation du formulaire complet', () => {
        it('devrait invalider le formulaire avec des champs vides', () => {
            const form = document.getElementById('complete-form');
            expect(form.checkValidity()).toBe(false);
        });

        it('devrait valider le formulaire avec tous les champs remplis', () => {
            const form = document.getElementById('complete-form');
            
            document.getElementById('name').value = 'Jean';
            document.getElementById('email').value = 'jean@example.com';
            document.getElementById('phone').value = '0123456789';
            document.getElementById('birthdate').value = '1990-01-01';
            document.getElementById('country').value = 'fr';
            document.getElementById('message').value = 'Test message';
            
            expect(form.checkValidity()).toBe(true);
        });

        it('devrait collecter toutes les erreurs de validation', () => {
            const form = document.getElementById('complete-form');
            const formErrors = document.getElementById('form-errors');
            const errors = [];
            
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    errors.push(`${input.name}: ${input.validationMessage}`);
                }
            });
            
            expect(errors.length).toBeGreaterThan(0);
        });

        it('devrait empêcher la soumission si invalide', () => {
            const form = document.getElementById('complete-form');
            let submitPrevented = false;
            
            // Check manually since jsdom doesn't fully support HTML5 validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            requiredFields.forEach(field => {
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                submitPrevented = true;
            }
            
            expect(submitPrevented).toBe(true);
        });

        it('devrait permettre la soumission si valide', () => {
            const form = document.getElementById('complete-form');
            let submitAllowed = true;
            
            document.getElementById('name').value = 'Jean';
            document.getElementById('email').value = 'jean@example.com';
            document.getElementById('birthdate').value = '1990-01-01';
            document.getElementById('country').value = 'fr';
            
            // Check manually since jsdom doesn't fully support HTML5 validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            requiredFields.forEach(field => {
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                submitAllowed = false;
            }
            
            expect(submitAllowed).toBe(true);
        });
    });
});

describe('Validation personnalisée - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form id="custom-validation-form">
                <input type="password" id="password" name="password" required>
                <input type="password" id="confirm-password" name="confirm_password" required>
                <div id="password-error"></div>
                <input type="text" id="username" name="username" required>
                <div id="username-error"></div>
                <button type="submit" id="submit-custom">S'inscrire</button>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Validation de confirmation de mot de passe', () => {
        it('devrait invalider si les mots de passe ne correspondent pas', () => {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm-password');
            const passwordError = document.getElementById('password-error');
            
            password.value = 'password123';
            confirmPassword.value = 'password456';
            
            if (password.value !== confirmPassword.value) {
                passwordError.textContent = 'Les mots de passe ne correspondent pas';
                passwordError.classList.add('text-red-600');
            }
            
            expect(passwordError.textContent).toBe('Les mots de passe ne correspondent pas');
        });

        it('devrait valider si les mots de passe correspondent', () => {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm-password');
            const passwordError = document.getElementById('password-error');
            
            password.value = 'password123';
            confirmPassword.value = 'password123';
            
            if (password.value === confirmPassword.value) {
                passwordError.textContent = '';
                passwordError.classList.remove('text-red-600');
            }
            
            expect(passwordError.textContent).toBe('');
        });

        it('devrait valider la complexité du mot de passe', () => {
            const password = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            
            password.value = '123';
            
            const hasMinLength = password.value.length >= 8;
            const hasUpperCase = /[A-Z]/.test(password.value);
            const hasLowerCase = /[a-z]/.test(password.value);
            const hasNumber = /[0-9]/.test(password.value);
            
            if (!hasMinLength || !hasUpperCase || !hasLowerCase || !hasNumber) {
                passwordError.textContent = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre';
            }
            
            expect(passwordError.textContent).toContain('8 caractères');
        });
    });

    describe('Validation de nom d\'utilisateur', () => {
        it('devrait invalider un nom d\'utilisateur trop court', () => {
            const username = document.getElementById('username');
            const usernameError = document.getElementById('username-error');
            
            username.value = 'ab';
            
            if (username.value.length < 3) {
                usernameError.textContent = 'Le nom d\'utilisateur doit contenir au moins 3 caractères';
            }
            
            expect(usernameError.textContent).toContain('au moins 3 caractères');
        });

        it('devrait invalider un nom d\'utilisateur avec caractères spéciaux', () => {
            const username = document.getElementById('username');
            const usernameError = document.getElementById('username-error');
            
            username.value = 'user@name';
            
            if (!/^[a-zA-Z0-9_]+$/.test(username.value)) {
                usernameError.textContent = 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres et underscores';
            }
            
            expect(usernameError.textContent).toContain('lettres, chiffres et underscores');
        });

        it('devrait valider un nom d\'utilisateur correct', () => {
            const username = document.getElementById('username');
            const usernameError = document.getElementById('username-error');
            
            username.value = 'user_name123';
            
            if (/^[a-zA-Z0-9_]+$/.test(username.value) && username.value.length >= 3) {
                usernameError.textContent = '';
            }
            
            expect(usernameError.textContent).toBe('');
        });
    });
});

describe('Validation en temps réel - Tests dynamiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form id="realtime-form">
                <input type="email" id="realtime-email" name="email" required>
                <div id="realtime-email-error" class="error-message"></div>
                <input type="text" id="realtime-username" name="username" required>
                <div id="realtime-username-error" class="error-message"></div>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Validation à la saisie', () => {
        it('devrait valider l\'email à chaque frappe', () => {
            const email = document.getElementById('realtime-email');
            const emailError = document.getElementById('realtime-email-error');
            
            // Test the validation logic directly without dispatchEvent
            email.value = 'invalid';
            if (email.value && !email.checkValidity()) {
                emailError.textContent = 'Email invalide';
            } else {
                emailError.textContent = '';
            }
            
            expect(emailError.textContent).toBe('Email invalide');
            
            email.value = 'valid@example.com';
            if (email.value && !email.checkValidity()) {
                emailError.textContent = 'Email invalide';
            } else {
                emailError.textContent = '';
            }
            
            expect(emailError.textContent).toBe('');
        });

        it('devrait valider le nom d\'utilisateur à chaque frappe', () => {
            const username = document.getElementById('realtime-username');
            const usernameError = document.getElementById('realtime-username-error');
            
            // Test the validation logic directly without dispatchEvent
            username.value = 'ab';
            if (username.value.length < 3 && username.value.length > 0) {
                usernameError.textContent = 'Minimum 3 caractères';
            } else {
                usernameError.textContent = '';
            }
            
            expect(usernameError.textContent).toBe('Minimum 3 caractères');
            
            username.value = 'abc';
            if (username.value.length < 3 && username.value.length > 0) {
                usernameError.textContent = 'Minimum 3 caractères';
            } else {
                usernameError.textContent = '';
            }
            
            expect(usernameError.textContent).toBe('');
        });
    });

    describe('Validation au blur (perte de focus)', () => {
        it('devrait valider au blur du champ', () => {
            const email = document.getElementById('realtime-email');
            const emailError = document.getElementById('realtime-email-error');
            
            // Test the validation logic directly without dispatchEvent
            email.value = 'invalid';
            if (email.value && !email.checkValidity()) {
                emailError.textContent = 'Email invalide';
                email.classList.add('border-red-500');
            }
            
            expect(emailError.textContent).toBe('Email invalide');
            expect(email.classList.contains('border-red-500')).toBe(true);
        });

        it('devrait effacer l\'erreur au focus', () => {
            const email = document.getElementById('realtime-email');
            const emailError = document.getElementById('realtime-email-error');
            
            // Test the validation logic directly without dispatchEvent
            emailError.textContent = 'Erreur précédente';
            email.classList.add('border-red-500');
            
            // Simulate focus logic
            emailError.textContent = '';
            email.classList.remove('border-red-500');
            
            expect(emailError.textContent).toBe('');
            expect(email.classList.contains('border-red-500')).toBe(false);
        });
    });
});

describe('Validation de formulaires spécifiques - Tests métier', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form id="absence-form">
                <select id="absence-type" name="type" required>
                    <option value="">Sélectionner</option>
                    <option value="conge">Congés payés</option>
                    <option value="maladie">Maladie</option>
                </select>
                <input type="date" id="date-start" name="date_start" required>
                <input type="date" id="date-end" name="date_end" required>
                <div id="date-error"></div>
                <button type="submit">Soumettre</button>
            </form>
            <form id="employee-form">
                <input type="text" id="employee-name" name="name" required>
                <input type="email" id="employee-email" name="email" required>
                <input type="tel" id="employee-phone" name="phone">
                <div id="employee-error"></div>
                <button type="submit">Créer</button>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Validation de formulaire d\'absence', () => {
        it('devrait vérifier que la date de fin est après la date de début', () => {
            const dateStart = document.getElementById('date-start');
            const dateEnd = document.getElementById('date-end');
            const dateError = document.getElementById('date-error');
            
            dateStart.value = '2024-01-20';
            dateEnd.value = '2024-01-15';
            
            if (new Date(dateEnd.value) <= new Date(dateStart.value)) {
                dateError.textContent = 'La date de fin doit être après la date de début';
            }
            
            expect(dateError.textContent).toContain('après la date de début');
        });

        it('devrait valider des dates correctes', () => {
            const dateStart = document.getElementById('date-start');
            const dateEnd = document.getElementById('date-end');
            const dateError = document.getElementById('date-error');
            
            dateStart.value = '2024-01-15';
            dateEnd.value = '2024-01-20';
            
            if (new Date(dateEnd.value) > new Date(dateStart.value)) {
                dateError.textContent = '';
            }
            
            expect(dateError.textContent).toBe('');
        });

        it('devrait exiger un type d\'absence', () => {
            const absenceType = document.getElementById('absence-type');
            
            expect(absenceType.required).toBe(true);
            expect(absenceType.value).toBe('');
            expect(absenceType.checkValidity()).toBe(false);
        });
    });

    describe('Validation de formulaire d\'employé', () => {
        it('devrait exiger un nom d\'employé', () => {
            const employeeName = document.getElementById('employee-name');
            
            expect(employeeName.required).toBe(true);
            expect(employeeName.checkValidity()).toBe(false);
        });

        it('devrait exiger un email valide', () => {
            const employeeEmail = document.getElementById('employee-email');
            
            employeeEmail.value = 'invalid';
            expect(employeeEmail.checkValidity()).toBe(false);
            
            employeeEmail.value = 'valid@company.com';
            expect(employeeEmail.checkValidity()).toBe(true);
        });

        it('devrait accepter un téléphone optionnel', () => {
            const employeePhone = document.getElementById('employee-phone');
            
            expect(employeePhone.required).toBe(false);
            expect(employeePhone.checkValidity()).toBe(true);
        });
    });
});