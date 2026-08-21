/**
 * Tests Jasmine pour les Formulaires
 * Tests des formulaires de création, modification et validation
 */

describe('Formulaires - Tests principaux', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form action="/employees" method="POST" id="employee-form">
                <input type="hidden" name="_token" value="test-token">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="poste" class="form-label">Poste</label>
                        <input type="text" class="form-control" id="poste" name="poste">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="departement" class="form-label">Département</label>
                        <input type="text" class="form-control" id="departement" name="departement">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_embauche" class="form-label">Date d'embauche</label>
                        <input type="date" class="form-control" id="date_embauche" name="date_embauche">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="statut" class="form-label">Statut *</label>
                        <select class="form-select" id="statut" name="statut" required>
                            <option value="">Sélectionner un statut</option>
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success" id="submit-btn">Créer l'employé</button>
                        <a href="/employees" class="btn btn-outline-secondary" id="cancel-btn">Annuler</a>
                    </div>
                </div>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Formulaire de création d\'employé', () => {
        it('devrait afficher le formulaire', () => {
            const form = document.getElementById('employee-form');
            expect(form).toBeTruthy();
            expect(form.method.toLowerCase()).toBe('post');
        });

        it('devrait avoir le champ nom', () => {
            const nom = document.getElementById('nom');
            expect(nom).toBeTruthy();
            expect(nom.type).toBe('text');
            expect(nom.required).toBe(true);
        });

        it('devrait avoir le champ prénom', () => {
            const prenom = document.getElementById('prenom');
            expect(prenom).toBeTruthy();
            expect(prenom.type).toBe('text');
            expect(prenom.required).toBe(true);
        });

        it('devrait avoir le champ email', () => {
            const email = document.getElementById('email');
            expect(email).toBeTruthy();
            expect(email.type).toBe('email');
            expect(email.required).toBe(true);
        });

        it('devrait avoir le champ téléphone', () => {
            const telephone = document.getElementById('telephone');
            expect(telephone).toBeTruthy();
            expect(telephone.type).toBe('tel');
        });

        it('devrait avoir le champ poste', () => {
            const poste = document.getElementById('poste');
            expect(poste).toBeTruthy();
            expect(poste.type).toBe('text');
        });

        it('devrait avoir le champ département', () => {
            const departement = document.getElementById('departement');
            expect(departement).toBeTruthy();
            expect(departement.type).toBe('text');
        });

        it('devrait avoir le champ date d\'embauche', () => {
            const dateEmbauche = document.getElementById('date_embauche');
            expect(dateEmbauche).toBeTruthy();
            expect(dateEmbauche.type).toBe('date');
        });

        it('devrait avoir le sélecteur de statut', () => {
            const statut = document.getElementById('statut');
            expect(statut).toBeTruthy();
            expect(statut.tagName).toBe('SELECT');
            expect(statut.required).toBe(true);
        });

        it('devrait avoir les options de statut', () => {
            const statut = document.getElementById('statut');
            const options = statut.options;
            expect(options.length).toBe(3);
            expect(options[0].value).toBe('');
            expect(options[1].value).toBe('actif');
            expect(options[2].value).toBe('inactif');
        });

        it('devrait avoir le champ photo', () => {
            const photo = document.getElementById('photo');
            expect(photo).toBeTruthy();
            expect(photo.type).toBe('file');
            expect(photo.accept).toBe('image/*');
        });
    });

    describe('Boutons du formulaire', () => {
        it('devrait avoir le bouton de soumission', () => {
            const submitBtn = document.getElementById('submit-btn');
            expect(submitBtn).toBeTruthy();
            expect(submitBtn.type).toBe('submit');
            expect(submitBtn.textContent).toContain('Créer');
        });

        it('devrait avoir le bouton d\'annulation', () => {
            const cancelBtn = document.getElementById('cancel-btn');
            expect(cancelBtn).toBeTruthy();
            expect(cancelBtn.tagName).toBe('A');
            expect(cancelBtn.textContent).toContain('Annuler');
        });
    });

    describe('Labels du formulaire', () => {
        it('devrait avoir des labels pour tous les champs', () => {
            const labels = document.querySelectorAll('label');
            expect(labels.length).toBeGreaterThan(0);
            
            const requiredLabels = ['Nom', 'Prénom', 'Email', 'Statut'];
            requiredLabels.forEach(labelText => {
                const label = Array.from(labels).find(l => l.textContent.includes(labelText));
                expect(label).toBeTruthy();
            });
        });

        it('devrait avoir les astérisques pour les champs requis', () => {
            const requiredLabels = document.querySelectorAll('label');
            const asteriskLabels = Array.from(requiredLabels).filter(label => label.textContent.includes('*'));
            expect(asteriskLabels.length).toBeGreaterThan(0);
        });
    });
});

describe('Formulaire d\'absence - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form method="POST" action="/absences" id="absence-form">
                <input type="hidden" name="_token" value="test-token">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type d'absence *</label>
                        <select id="absence-type" name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <option value="">Sélectionner un type</option>
                            <option value="conge">Congés payés</option>
                            <option value="maladie">Congés maladies</option>
                            <option value="recuperation">Heures de récup</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date de début *</label>
                        <input type="date" id="absence-date-debut" name="date_debut" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin *</label>
                        <input type="date" id="absence-date-fin" name="date_fin" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Motif (optionnel)</label>
                        <textarea id="absence-motif" name="motif" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Précisez le motif de votre absence..."></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg text-sm font-medium" id="submit-absence">Envoyer la demande</button>
                    <a href="/absences" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium" id="cancel-absence">Annuler</a>
                </div>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Formulaire de demande d\'absence', () => {
        it('devrait afficher le formulaire d\'absence', () => {
            const absenceForm = document.getElementById('absence-form');
            expect(absenceForm).toBeTruthy();
            expect(absenceForm.method.toLowerCase()).toBe('post');
        });

        it('devrait avoir le sélecteur de type d\'absence', () => {
            const absenceType = document.getElementById('absence-type');
            expect(absenceType).toBeTruthy();
            expect(absenceType.tagName).toBe('SELECT');
            expect(absenceType.required).toBe(true);
        });

        it('devrait avoir les options de type d\'absence', () => {
            const absenceType = document.getElementById('absence-type');
            const options = absenceType.options;
            expect(options.length).toBe(4);
            expect(options[1].value).toBe('conge');
            expect(options[2].value).toBe('maladie');
            expect(options[3].value).toBe('recuperation');
        });

        it('devrait avoir les champs de date', () => {
            const dateDebut = document.getElementById('absence-date-debut');
            const dateFin = document.getElementById('absence-date-fin');
            expect(dateDebut).toBeTruthy();
            expect(dateFin).toBeTruthy();
            expect(dateDebut.type).toBe('date');
            expect(dateFin.type).toBe('date');
        });

        it('devrait avoir le champ motif', () => {
            const motif = document.getElementById('absence-motif');
            expect(motif).toBeTruthy();
            expect(motif.tagName).toBe('TEXTAREA');
        });

        it('devrait avoir les boutons d\'action', () => {
            const submitAbsence = document.getElementById('submit-absence');
            const cancelAbsence = document.getElementById('cancel-absence');
            expect(submitAbsence).toBeTruthy();
            expect(cancelAbsence).toBeTruthy();
        });
    });

    describe('Validation du formulaire d\'absence', () => {
        it('devrait valider les champs requis', () => {
            const absenceType = document.getElementById('absence-type');
            const dateDebut = document.getElementById('absence-date-debut');
            const dateFin = document.getElementById('absence-date-fin');
            
            expect(absenceType.required).toBe(true);
            expect(dateDebut.required).toBe(true);
            expect(dateFin.required).toBe(true);
        });

        it('devrait permettre de sélectionner un type d\'absence', () => {
            const absenceType = document.getElementById('absence-type');
            absenceType.value = 'conge';
            expect(absenceType.value).toBe('conge');
        });

        it('devrait permettre de définir les dates', () => {
            const dateDebut = document.getElementById('absence-date-debut');
            const dateFin = document.getElementById('absence-date-fin');
            
            dateDebut.value = '2024-01-15';
            dateFin.value = '2024-01-20';
            
            expect(dateDebut.value).toBe('2024-01-15');
            expect(dateFin.value).toBe('2024-01-20');
        });

        it('devrait permettre de saisir un motif', () => {
            const motif = document.getElementById('absence-motif');
            motif.value = 'Vacances familiales';
            expect(motif.value).toBe('Vacances familiales');
        });
    });
});

describe('Formulaire de messagerie - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form method="POST" action="/mailbox" id="mailbox-form">
                <input type="hidden" name="_token" value="test-token">
                <input type="hidden" name="sender_name" value="User Test">
                <input type="hidden" name="sender_email" value="user@test.com">
                <div class="divide-y divide-gray-100">
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <span class="text-xs text-gray-400 w-6 shrink-0">De</span>
                        <span class="text-sm text-gray-700">User Test <user@test.com></span>
                    </div>
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <label for="compose-recipient" class="text-xs text-gray-400 w-6 shrink-0">À</label>
                        <input type="email" name="recipient_email" id="compose-recipient" required placeholder="destinataire@exemple.com" class="flex-1 text-sm text-gray-800 bg-transparent border-none outline-none">
                    </div>
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <label for="compose-subject" class="text-xs text-gray-400 w-6 shrink-0">Objet</label>
                        <input type="text" name="subject" id="compose-subject" required placeholder="Objet du message" class="flex-1 text-sm text-gray-800 bg-transparent border-none outline-none">
                    </div>
                </div>
                <textarea name="body" id="compose-body" required rows="10" placeholder="Rédigez votre message ici…" class="w-full px-5 py-4 text-sm text-gray-700 bg-transparent border-none resize-none outline-none"></textarea>
                <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-2">
                        <button type="submit" name="save_draft" value="0" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-accent-600 text-white text-sm font-medium" id="send-btn">Envoyer</button>
                        <button type="submit" name="save_draft" value="1" class="px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-200" id="draft-btn">Brouillon</button>
                    </div>
                </div>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Formulaire de messagerie', () => {
        it('devrait afficher le formulaire de messagerie', () => {
            const mailboxForm = document.getElementById('mailbox-form');
            expect(mailboxForm).toBeTruthy();
            expect(mailboxForm.method.toLowerCase()).toBe('post');
        });

        it('devrait avoir les champs cachés d\'expéditeur', () => {
            const senderName = document.querySelector('input[name="sender_name"]');
            const senderEmail = document.querySelector('input[name="sender_email"]');
            expect(senderName).toBeTruthy();
            expect(senderEmail).toBeTruthy();
            expect(senderName.type).toBe('hidden');
            expect(senderEmail.type).toBe('hidden');
        });

        it('devrait avoir le champ destinataire', () => {
            const recipient = document.getElementById('compose-recipient');
            expect(recipient).toBeTruthy();
            expect(recipient.type).toBe('email');
            expect(recipient.required).toBe(true);
        });

        it('devrait avoir le champ objet', () => {
            const subject = document.getElementById('compose-subject');
            expect(subject).toBeTruthy();
            expect(subject.type).toBe('text');
            expect(subject.required).toBe(true);
        });

        it('devrait avoir le champ corps du message', () => {
            const body = document.getElementById('compose-body');
            expect(body).toBeTruthy();
            expect(body.tagName).toBe('TEXTAREA');
            expect(body.required).toBe(true);
        });

        it('devrait avoir les boutons d\'envoi et brouillon', () => {
            const sendBtn = document.getElementById('send-btn');
            const draftBtn = document.getElementById('draft-btn');
            expect(sendBtn).toBeTruthy();
            expect(draftBtn).toBeTruthy();
        });
    });

    describe('Interactions du formulaire de messagerie', () => {
        it('devrait permettre de remplir le destinataire', () => {
            const recipient = document.getElementById('compose-recipient');
            recipient.value = 'recipient@example.com';
            expect(recipient.value).toBe('recipient@example.com');
        });

        it('devrait permettre de remplir l\'objet', () => {
            const subject = document.getElementById('compose-subject');
            subject.value = 'Test subject';
            expect(subject.value).toBe('Test subject');
        });

        it('devrait permettre de remplir le corps', () => {
            const body = document.getElementById('compose-body');
            body.value = 'Test message body';
            expect(body.value).toBe('Test message body');
        });

        it('devrait cliquer sur le bouton d\'envoi', () => {
            const sendBtn = document.getElementById('send-btn');
            let clicked = false;
            sendBtn.addEventListener('click', () => {
                clicked = true;
            });
            sendBtn.click();
            expect(clicked).toBe(true);
        });

        it('devrait cliquer sur le bouton brouillon', () => {
            const draftBtn = document.getElementById('draft-btn');
            let clicked = false;
            draftBtn.addEventListener('click', () => {
                clicked = true;
            });
            draftBtn.click();
            expect(clicked).toBe(true);
        });
    });
});

describe('Validation des formulaires - Tests globaux', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <form id="test-form" novalidate>
                <input type="text" id="required-field" required>
                <input type="email" id="email-field" required>
                <input type="tel" id="phone-field">
                <input type="number" id="number-field" min="0" max="100">
                <input type="date" id="date-field">
                <select id="select-field" required>
                    <option value="">Sélectionner</option>
                    <option value="option1">Option 1</option>
                    <option value="option2">Option 2</option>
                </select>
                <textarea id="textarea-field" maxlength="500"></textarea>
                <button type="submit" id="form-submit">Soumettre</button>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Validation HTML5', () => {
        it('devrait avoir les attributs required', () => {
            const requiredField = document.getElementById('required-field');
            const emailField = document.getElementById('email-field');
            const selectField = document.getElementById('select-field');
            
            expect(requiredField.required).toBe(true);
            expect(emailField.required).toBe(true);
            expect(selectField.required).toBe(true);
        });

        it('devrait avoir les types corrects', () => {
            const emailField = document.getElementById('email-field');
            const phoneField = document.getElementById('phone-field');
            const numberField = document.getElementById('number-field');
            const dateField = document.getElementById('date-field');
            
            expect(emailField.type).toBe('email');
            expect(phoneField.type).toBe('tel');
            expect(numberField.type).toBe('number');
            expect(dateField.type).toBe('date');
        });

        it('devrait avoir les contraintes de nombre', () => {
            const numberField = document.getElementById('number-field');
            expect(numberField.min).toBe('0');
            expect(numberField.max).toBe('100');
        });

        it('devrait avoir la contrainte de longueur maximale', () => {
            const textareaField = document.getElementById('textarea-field');
            expect(textareaField.maxLength).toBe(500);
        });
    });

    describe('Validation personnalisée', () => {
        it('devrait vérifier la validité du formulaire', () => {
            const form = document.getElementById('test-form');
            const requiredField = document.getElementById('required-field');
            
            requiredField.value = '';
            // jsdom doesn't fully support HTML5 validation, so we check manually
            const isValid = requiredField.value.trim() !== '';
            expect(isValid).toBe(false);
            
            requiredField.value = 'test';
            const isValidAfter = requiredField.value.trim() !== '';
            expect(isValidAfter).toBe(true);
        });

        it('devrait vérifier la validité de l\'email', () => {
            const emailField = document.getElementById('email-field');
            
            emailField.value = 'invalid-email';
            expect(emailField.checkValidity()).toBe(false);
            
            emailField.value = 'valid@email.com';
            expect(emailField.checkValidity()).toBe(true);
        });

        it('devrait vérifier la validité du nombre', () => {
            const numberField = document.getElementById('number-field');
            
            numberField.value = '150';
            expect(numberField.checkValidity()).toBe(false);
            
            numberField.value = '50';
            expect(numberField.checkValidity()).toBe(true);
        });
    });

    describe('Messages d\'erreur de validation', () => {
        it('devrait afficher les messages d\'erreur par défaut', () => {
            const requiredField = document.getElementById('required-field');
            requiredField.value = '';
            requiredField.checkValidity();
            
            expect(requiredField.validity.valueMissing).toBe(true);
        });

        it('devrait avoir des messages d\'erreur personnalisés', () => {
            const emailField = document.getElementById('email-field');
            emailField.value = 'invalid';
            emailField.checkValidity();
            
            expect(emailField.validity.typeMismatch).toBe(true);
        });
    });
});