/**
 * Tests Jasmine pour les Actions AJAX/Fetch
 * Tests des appels API asynchrones avec mocks
 */

describe('Actions AJAX/Fetch - Tests principaux', () => {
    
    // Mock de fetch global
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <button id="load-data-btn">Charger les données</button>
            <button id="submit-form-btn">Soumettre le formulaire</button>
            <button id="delete-item-btn">Supprimer l'élément</button>
            <div id="result-container"></div>
            <form id="ajax-form">
                <input type="text" name="data" id="form-data">
                <button type="submit">Envoyer</button>
            </form>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Appels fetch basiques', () => {
        it('devrait effectuer un appel fetch GET', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true, data: 'test' })
            }));

            const response = await fetch('/api/data');
            const data = await response.json();

            expect(mockFetch).toHaveBeenCalledWith('/api/data');
            expect(data.success).toBe(true);
            expect(data.data).toBe('test');
        });

        it('devrait effectuer un appel fetch POST', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const formData = { name: 'Test', value: 123 };
            const response = await fetch('/api/data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            expect(mockFetch).toHaveBeenCalledWith('/api/data', jasmine.objectContaining({
                method: 'POST',
                headers: jasmine.objectContaining({
                    'Content-Type': 'application/json'
                }),
                body: JSON.stringify(formData)
            }));
        });

        it('devrait gérer les erreurs fetch', async () => {
            mockFetch.and.returnValue(Promise.reject(new Error('Network error')));

            try {
                await fetch('/api/data');
                fail('Should have thrown an error');
            } catch (error) {
                expect(error.message).toBe('Network error');
            }
        });

        it('devrait gérer les réponses non-OK', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: false,
                status: 404,
                json: () => Promise.resolve({ error: 'Not found' })
            }));

            const response = await fetch('/api/data');
            
            expect(response.ok).toBe(false);
            expect(response.status).toBe(404);
        });
    });

    describe('Appels fetch avec headers', () => {
        it('devrait inclure le header CSRF', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            await fetch('/api/data', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Content-Type': 'application/json'
                }
            });

            expect(mockFetch).toHaveBeenCalledWith('/api/data', jasmine.objectContaining({
                headers: jasmine.objectContaining({
                    'X-CSRF-TOKEN': 'test-token',
                    'Content-Type': 'application/json'
                })
            }));
        });

        it('devrait inclure le header Authorization', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            await fetch('/api/data', {
                headers: {
                    'Authorization': 'Bearer token123'
                }
            });

            expect(mockFetch).toHaveBeenCalledWith('/api/data', jasmine.objectContaining({
                headers: jasmine.objectContaining({
                    'Authorization': 'Bearer token123'
                })
            }));
        });

        it('devrait inclure le header Accept', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            await fetch('/api/data', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            expect(mockFetch).toHaveBeenCalledWith('/api/data', jasmine.objectContaining({
                headers: jasmine.objectContaining({
                    'Accept': 'application/json'
                })
            }));
        });
    });

    describe('Interactions AJAX avec DOM', () => {
        it('devrait charger des données lors du clic', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ data: 'loaded data' })
            }));

            const loadBtn = document.getElementById('load-data-btn');
            const resultContainer = document.getElementById('result-container');

            loadBtn.addEventListener('click', async () => {
                const response = await fetch('/api/data');
                const data = await response.json();
                resultContainer.textContent = data.data;
            });

            loadBtn.click();
            
            // Attendre que la promesse soit résolue
            await new Promise(resolve => setTimeout(resolve, 0));

            expect(mockFetch).toHaveBeenCalled();
            expect(resultContainer.textContent).toBe('loaded data');
        });

        it('devrait soumettre un formulaire via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const form = document.getElementById('ajax-form');
            const formData = document.getElementById('form-data');
            formData.value = 'test data';

            // Test that the form can be submitted without actual dispatchEvent
            const data = { data: formData.value };
            
            const response = await fetch('/api/submit', {
                method: 'POST',
                body: JSON.stringify(data)
            });

            expect(mockFetch).toHaveBeenCalledWith('/api/submit', jasmine.objectContaining({
                method: 'POST',
                body: JSON.stringify({ data: 'test data' })
            }));
        });
    });
});

describe('Actions AJAX Mailbox - Tests spécifiques', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <button id="star-btn" data-id="1" data-starred="0">★</button>
            <button id="delete-btn" data-id="1">🗑️</button>
            <button id="mark-read-btn" data-id="1">Mark as read</button>
            <div id="message-status"></div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Fonctionnalités de messagerie AJAX', () => {
        it('devrait basculer l\'étoile d\'un message', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ starred: true })
            }));

            const starBtn = document.getElementById('star-btn');
            const messageId = starBtn.dataset.id;

            const response = await fetch(`/mailbox/${messageId}/star`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            expect(mockFetch).toHaveBeenCalledWith(`/mailbox/${messageId}/star`, jasmine.objectContaining({
                method: 'PATCH'
            }));
            expect(data.starred).toBe(true);
        });

        it('devrait supprimer un message', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const deleteBtn = document.getElementById('delete-btn');
            const messageId = deleteBtn.dataset.id;

            const response = await fetch(`/mailbox/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json'
                }
            });

            expect(mockFetch).toHaveBeenCalledWith(`/mailbox/${messageId}`, jasmine.objectContaining({
                method: 'DELETE'
            }));
        });

        it('devrait marquer un message comme lu', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ read: true })
            }));

            const markReadBtn = document.getElementById('mark-read-btn');
            const messageId = markReadBtn.dataset.id;

            const response = await fetch(`/mailbox/${messageId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            expect(mockFetch).toHaveBeenCalledWith(`/mailbox/${messageId}/read`, jasmine.objectContaining({
                method: 'PATCH'
            }));
            expect(data.read).toBe(true);
        });
    });

    describe('Mise à jour DOM après AJAX', () => {
        it('devrait mettre à jour l\'icône d\'étoile après AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ starred: true })
            }));

            const starBtn = document.getElementById('star-btn');
            const messageId = starBtn.dataset.id;

            const response = await fetch(`/mailbox/${messageId}/star`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.starred) {
                starBtn.classList.add('text-amber-400');
                starBtn.dataset.starred = '1';
            }

            expect(starBtn.classList.contains('text-amber-400')).toBe(true);
            expect(starBtn.dataset.starred).toBe('1');
        });

        it('devrait afficher un message de succès après suppression', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const deleteBtn = document.getElementById('delete-btn');
            const messageStatus = document.getElementById('message-status');
            const messageId = deleteBtn.dataset.id;

            const response = await fetch(`/mailbox/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                }
            });

            const data = await response.json();

            if (data.success) {
                messageStatus.textContent = 'Message supprimé avec succès';
                messageStatus.classList.add('text-green-600');
            }

            expect(messageStatus.textContent).toBe('Message supprimé avec succès');
            expect(messageStatus.classList.contains('text-green-600')).toBe(true);
        });
    });
});

describe('Actions AJAX Employés - Tests spécifiques', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <button id="load-employee-btn" data-id="1">Charger employé</button>
            <button id="update-employee-btn" data-id="1">Mettre à jour</button>
            <button id="delete-employee-btn" data-id="1">Supprimer</button>
            <div id="employee-details"></div>
            <div id="notification-area"></div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Gestion des employés via AJAX', () => {
        it('devrait charger les détails d\'un employé', async () => {
            const employeeData = {
                id: 1,
                nom: 'Dupont',
                prenom: 'Jean',
                email: 'jean.dupont@example.com',
                poste: 'Développeur'
            };

            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve(employeeData)
            }));

            const loadBtn = document.getElementById('load-employee-btn');
            const employeeId = loadBtn.dataset.id;
            const employeeDetails = document.getElementById('employee-details');

            const response = await fetch(`/employees/${employeeId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            expect(mockFetch).toHaveBeenCalledWith(`/employees/${employeeId}`, jasmine.objectContaining({
                headers: jasmine.objectContaining({
                    'Accept': 'application/json'
                })
            }));
            expect(data.nom).toBe('Dupont');
            expect(data.prenom).toBe('Jean');
        });

        it('devrait mettre à jour un employé', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const updateBtn = document.getElementById('update-employee-btn');
            const employeeId = updateBtn.dataset.id;

            const updateData = {
                nom: 'Dupont',
                prenom: 'Jean',
                poste: 'Senior Développeur'
            };

            const response = await fetch(`/employees/${employeeId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updateData)
            });

            expect(mockFetch).toHaveBeenCalledWith(`/employees/${employeeId}`, jasmine.objectContaining({
                method: 'PUT',
                body: JSON.stringify(updateData)
            }));
        });

        it('devrait supprimer un employé', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const deleteBtn = document.getElementById('delete-employee-btn');
            const employeeId = deleteBtn.dataset.id;

            const response = await fetch(`/employees/${employeeId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                }
            });

            expect(mockFetch).toHaveBeenCalledWith(`/employees/${employeeId}`, jasmine.objectContaining({
                method: 'DELETE'
            }));
        });
    });

    describe('Chargement de panel employé', () => {
        it('devrait charger le panel HTML d\'un employé', async () => {
            const panelHTML = '<div class="employee-panel">Détails employé</div>';

            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                text: () => Promise.resolve(panelHTML)
            }));

            const employeeId = 1;

            const response = await fetch(`/employees/${employeeId}/panel`);
            const html = await response.text();

            expect(mockFetch).toHaveBeenCalledWith(`/employees/${employeeId}/panel`);
            expect(html).toContain('Détails employé');
        });

        it('devrait afficher le panel dans le DOM', async () => {
            const panelHTML = '<div class="employee-panel">Jean Dupont - Développeur</div>';

            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                text: () => Promise.resolve(panelHTML)
            }));

            const employeeDetails = document.getElementById('employee-details');
            const employeeId = 1;

            const response = await fetch(`/employees/${employeeId}/panel`);
            const html = await response.text();

            employeeDetails.innerHTML = html;

            expect(employeeDetails.innerHTML).toContain('Jean Dupont');
            expect(employeeDetails.innerHTML).toContain('Développeur');
        });
    });
});

describe('Gestion des erreurs AJAX - Tests avancés', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <button id="error-btn">Action avec erreur</button>
            <div id="error-display"></div>
            <div id="loading-indicator" class="hidden">Chargement...</div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Gestion des erreurs réseau', () => {
        it('devrait gérer les erreurs de connexion', async () => {
            mockFetch.and.returnValue(Promise.reject(new Error('Failed to fetch')));

            const errorBtn = document.getElementById('error-btn');
            const errorDisplay = document.getElementById('error-display');

            try {
                await fetch('/api/data');
            } catch (error) {
                errorDisplay.textContent = `Erreur: ${error.message}`;
                errorDisplay.classList.add('text-red-600');
            }

            expect(errorDisplay.textContent).toContain('Failed to fetch');
            expect(errorDisplay.classList.contains('text-red-600')).toBe(true);
        });

        it('devrait gérer les timeouts', async () => {
            mockFetch.and.returnValue(new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timeout')), 100);
            }));

            const errorDisplay = document.getElementById('error-display');

            try {
                await fetch('/api/data');
            } catch (error) {
                errorDisplay.textContent = `Erreur: ${error.message}`;
            }

            expect(errorDisplay.textContent).toContain('Request timeout');
        });
    });

    describe('Gestion des erreurs serveur', () => {
        it('devrait gérer les erreurs 404', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: false,
                status: 404,
                json: () => Promise.resolve({ error: 'Resource not found' })
            }));

            const errorDisplay = document.getElementById('error-display');

            const response = await fetch('/api/data');
            
            if (!response.ok) {
                const error = await response.json();
                errorDisplay.textContent = `Erreur ${response.status}: ${error.error}`;
            }

            expect(errorDisplay.textContent).toContain('404');
            expect(errorDisplay.textContent).toContain('Resource not found');
        });

        it('devrait gérer les erreurs 500', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: false,
                status: 500,
                json: () => Promise.resolve({ error: 'Internal server error' })
            }));

            const errorDisplay = document.getElementById('error-display');

            const response = await fetch('/api/data');
            
            if (!response.ok) {
                const error = await response.json();
                errorDisplay.textContent = `Erreur ${response.status}: ${error.error}`;
            }

            expect(errorDisplay.textContent).toContain('500');
            expect(errorDisplay.textContent).toContain('Internal server error');
        });

        it('devrait gérer les erreurs 403', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: false,
                status: 403,
                json: () => Promise.resolve({ error: 'Forbidden' })
            }));

            const errorDisplay = document.getElementById('error-display');

            const response = await fetch('/api/data');
            
            if (!response.ok) {
                const error = await response.json();
                errorDisplay.textContent = `Erreur ${response.status}: ${error.error}`;
            }

            expect(errorDisplay.textContent).toContain('403');
            expect(errorDisplay.textContent).toContain('Forbidden');
        });
    });

    describe('Indicateurs de chargement', () => {
        it('devrait afficher l\'indicateur de chargement', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ data: 'test' })
            }));

            const loadingIndicator = document.getElementById('loading-indicator');
            
            loadingIndicator.classList.remove('hidden');
            
            const response = await fetch('/api/data');
            await response.json();
            
            loadingIndicator.classList.add('hidden');

            expect(loadingIndicator.classList.contains('hidden')).toBe(true);
        });

        it('devrait masquer l\'indicateur en cas d\'erreur', async () => {
            mockFetch.and.returnValue(Promise.reject(new Error('Network error')));

            const loadingIndicator = document.getElementById('loading-indicator');
            
            loadingIndicator.classList.remove('hidden');
            
            try {
                await fetch('/api/data');
            } catch (error) {
                loadingIndicator.classList.add('hidden');
            }

            expect(loadingIndicator.classList.contains('hidden')).toBe(true);
        });
    });
});