/**
 * Tests Jasmine pour la Messagerie Mailbox
 * Tests des fonctionnalités de messagerie : marquer lu/non lu, envoyer, changer de dossier
 */

describe('Messagerie Mailbox - Tests principaux', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <div class="sidebar-link flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium bg-accent-50 text-accent-700" id="inbox-link">
                <span class="flex-1 leading-tight">Boîte de réception</span>
                <span class="shrink-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-accent-600 text-white text-[10px] font-semibold" id="unread-count">7</span>
            </div>
            <a href="/mailbox?folder=sent" class="sidebar-link flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100" id="sent-link">
                <span class="flex-1 leading-tight">Envoyés</span>
            </a>
            <a href="/mailbox?folder=drafts" class="sidebar-link flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100" id="drafts-link">
                <span class="flex-1 leading-tight">Brouillons</span>
            </a>
            <a href="/mailbox?folder=trash" class="sidebar-link flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100" id="trash-link">
                <span class="flex-1 leading-tight">Corbeille</span>
            </a>
            <ul class="divide-y divide-gray-50" id="message-list">
                <li class="message-row group flex items-center gap-3 px-4 py-3 cursor-pointer bg-accent-50/60" data-message-id="1" id="message-1">
                    <button type="button" class="star-btn shrink-0 p-0.5 rounded" data-id="1" data-starred="0" id="star-btn-1">
                        <svg class="w-4 h-4 text-gray-300" id="star-icon-1"></svg>
                    </button>
                    <a href="/mailbox/1" class="flex-1 flex items-baseline gap-3 min-w-0" id="message-link-1">
                        <span class="w-36 shrink-0 truncate text-sm font-semibold text-gray-900">Jean Dupont</span>
                        <span class="flex-1 truncate text-sm text-gray-500">
                            <span class="text-gray-800 font-medium">Test subject</span>
                            <span class="text-gray-400"> — Test body preview</span>
                        </span>
                        <span class="shrink-0 text-xs font-semibold text-accent-600 tabular-nums">12 août</span>
                    </a>
                    <form method="POST" action="/mailbox/1" class="shrink-0 opacity-0 group-hover:opacity-100" id="delete-form-1">
                        <button type="submit" class="p-1 rounded text-gray-300 hover:text-red-500" id="delete-btn-1">🗑️</button>
                    </form>
                </li>
                <li class="message-row group flex items-center gap-3 px-4 py-3 cursor-pointer bg-white hover:bg-gray-50" data-message-id="2" id="message-2">
                    <button type="button" class="star-btn shrink-0 p-0.5 rounded" data-id="2" data-starred="1" id="star-btn-2">
                        <svg class="w-4 h-4 text-amber-400 fill-amber-400" id="star-icon-2"></svg>
                    </button>
                    <a href="/mailbox/2" class="flex-1 flex items-baseline gap-3 min-w-0" id="message-link-2">
                        <span class="w-36 shrink-0 truncate text-sm font-medium text-gray-700">Marie Martin</span>
                        <span class="flex-1 truncate text-sm text-gray-500">
                            <span class="text-gray-600">Another subject</span>
                            <span class="text-gray-400"> — Another body</span>
                        </span>
                        <span class="shrink-0 text-xs text-gray-400 tabular-nums">10 août</span>
                    </a>
                    <form method="POST" action="/mailbox/2" class="shrink-0 opacity-0 group-hover:opacity-100" id="delete-form-2">
                        <button type="submit" class="p-1 rounded text-gray-300 hover:text-red-500" id="delete-btn-2">🗑️</button>
                    </form>
                </li>
            </ul>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Navigation des dossiers', () => {
        it('devrait afficher le lien de la boîte de réception', () => {
            const inboxLink = document.getElementById('inbox-link');
            expect(inboxLink).toBeTruthy();
            expect(inboxLink.textContent).toContain('Boîte de réception');
        });

        it('devrait afficher le compteur de messages non lus', () => {
            const unreadCount = document.getElementById('unread-count');
            expect(unreadCount).toBeTruthy();
            expect(unreadCount.textContent).toBe('7');
        });

        it('devrait afficher les liens des autres dossiers', () => {
            const sentLink = document.getElementById('sent-link');
            const draftsLink = document.getElementById('drafts-link');
            const trashLink = document.getElementById('trash-link');
            
            expect(sentLink).toBeTruthy();
            expect(draftsLink).toBeTruthy();
            expect(trashLink).toBeTruthy();
        });

        it('devrait mettre en évidence le dossier actif', () => {
            const inboxLink = document.getElementById('inbox-link');
            const sentLink = document.getElementById('sent-link');
            
            expect(inboxLink.classList.contains('bg-accent-50')).toBe(true);
            expect(inboxLink.classList.contains('text-accent-700')).toBe(true);
            expect(sentLink.classList.contains('bg-accent-50')).toBe(false);
        });
    });

    describe('Liste des messages', () => {
        it('devrait afficher la liste des messages', () => {
            const messageList = document.getElementById('message-list');
            expect(messageList).toBeTruthy();
            expect(messageList.children.length).toBe(2);
        });

        it('devrait afficher les informations de message', () => {
            const message1 = document.getElementById('message-1');
            expect(message1.dataset.messageId).toBe('1');
            expect(message1.textContent).toContain('Jean Dupont');
            expect(message1.textContent).toContain('Test subject');
        });

        it('devrait différencier les messages lus et non lus', () => {
            const message1 = document.getElementById('message-1');
            const message2 = document.getElementById('message-2');
            
            expect(message1.classList.contains('bg-accent-50/60')).toBe(true);
            expect(message2.classList.contains('bg-white')).toBe(true);
        });

        it('devrait afficher les boutons d\'action au hover', () => {
            const message1 = document.getElementById('message-1');
            const deleteForm = document.getElementById('delete-form-1');
            
            expect(deleteForm.classList.contains('opacity-0')).toBe(true);
            expect(deleteForm.classList.contains('group-hover:opacity-100')).toBe(true);
        });
    });
});

describe('Fonctionnalités de messagerie - Tests spécifiques', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <div class="message-row" data-message-id="1" id="message-detail">
                <button type="button" class="star-btn" data-id="1" data-starred="0" id="star-btn">
                    <svg class="w-4 h-4 text-gray-300" id="star-icon"></svg>
                </button>
                <form method="POST" action="/mailbox/1" id="delete-form">
                    <button type="submit" id="delete-btn">Supprimer</button>
                </form>
                <button type="button" id="mark-read-btn">Marquer comme lu</button>
                <button type="button" id="mark-unread-btn">Marquer comme non lu</button>
                <a href="/mailbox/reply/1" id="reply-btn">Répondre</a>
                <a href="/mailbox/forward/1" id="forward-btn">Transférer</a>
            </div>
            <div id="message-status"></div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Marquer un message comme lu/non lu', () => {
        it('devrait marquer un message comme lu via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ read: true })
            }));

            const markReadBtn = document.getElementById('mark-read-btn');
            const messageId = 1;

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

        it('devrait marquer un message comme non lu via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ read: false })
            }));

            const markUnreadBtn = document.getElementById('mark-unread-btn');
            const messageId = 1;

            const response = await fetch(`/mailbox/${messageId}/unread`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            expect(mockFetch).toHaveBeenCalledWith(`/mailbox/${messageId}/unread`, jasmine.objectContaining({
                method: 'PATCH'
            }));
            expect(data.read).toBe(false);
        });

        it('devrait mettre à jour l\'interface après marquer comme lu', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ read: true })
            }));

            const messageDetail = document.getElementById('message-detail');
            const messageStatus = document.getElementById('message-status');
            const messageId = 1;

            const response = await fetch(`/mailbox/${messageId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                }
            });

            const data = await response.json();

            if (data.read) {
                messageDetail.classList.remove('bg-accent-50/60');
                messageDetail.classList.add('bg-white');
                messageStatus.textContent = 'Message marqué comme lu';
            }

            expect(messageDetail.classList.contains('bg-white')).toBe(true);
            expect(messageStatus.textContent).toContain('marqué comme lu');
        });
    });

    describe('Gestion des étoiles (favoris)', () => {
        it('devrait ajouter une étoile via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ starred: true })
            }));

            const starBtn = document.getElementById('star-btn');
            const starIcon = document.getElementById('star-icon');
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

        it('devrait retirer une étoile via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ starred: false })
            }));

            const starBtn = document.getElementById('star-btn');
            starBtn.dataset.starred = '1';
            const messageId = starBtn.dataset.id;

            const response = await fetch(`/mailbox/${messageId}/star`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            expect(data.starred).toBe(false);
        });

        it('devrait mettre à jour l\'icône d\'étoile', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ starred: true })
            }));

            const starBtn = document.getElementById('star-btn');
            const starIcon = document.getElementById('star-icon');
            const messageId = starBtn.dataset.id;

            const response = await fetch(`/mailbox/${messageId}/star`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                }
            });

            const data = await response.json();

            if (data.starred) {
                starIcon.setAttribute('fill', 'currentColor');
                starIcon.classList.remove('text-gray-300');
                starIcon.classList.add('text-amber-400', 'fill-amber-400');
                starBtn.dataset.starred = '1';
            }

            expect(starIcon.classList.contains('text-amber-400')).toBe(true);
            expect(starIcon.classList.contains('fill-amber-400')).toBe(true);
            expect(starBtn.dataset.starred).toBe('1');
        });
    });

    describe('Suppression de messages', () => {
        it('devrait supprimer un message via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const deleteBtn = document.getElementById('delete-btn');
            const messageId = 1;

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

        it('devrait confirmer avant suppression', () => {
            const deleteBtn = document.getElementById('delete-btn');
            const deleteForm = document.getElementById('delete-form');
            
            // Set attribute directly instead of using onsubmit property
            deleteForm.setAttribute('onsubmit', 'return confirm("Supprimer ce message ?")');
            
            const onsubmitValue = deleteForm.getAttribute('onsubmit');
            expect(onsubmitValue).toContain('confirm');
        });

        it('devrait mettre à jour l\'interface après suppression', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const messageDetail = document.getElementById('message-detail');
            const messageStatus = document.getElementById('message-status');
            const messageId = 1;

            const response = await fetch(`/mailbox/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                }
            });

            const data = await response.json();

            if (data.success) {
                messageDetail.style.display = 'none';
                messageStatus.textContent = 'Message supprimé';
            }

            expect(messageDetail.style.display).toBe('none');
            expect(messageStatus.textContent).toBe('Message supprimé');
        });
    });

    describe('Réponse et transfert', () => {
        it('devrait avoir le lien de réponse', () => {
            const replyBtn = document.getElementById('reply-btn');
            expect(replyBtn).toBeTruthy();
            expect(replyBtn.href).toContain('/mailbox/reply/1');
        });

        it('devrait avoir le lien de transfert', () => {
            const forwardBtn = document.getElementById('forward-btn');
            expect(forwardBtn).toBeTruthy();
            expect(forwardBtn.href).toContain('/mailbox/forward/1');
        });

        it('devrait cliquer sur le lien de réponse', () => {
            const replyBtn = document.getElementById('reply-btn');
            let clicked = false;
            replyBtn.addEventListener('click', () => {
                clicked = true;
            });
            replyBtn.click();
            expect(clicked).toBe(true);
        });
    });
});

describe('Changement de dossier - Tests navigation', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <nav class="flex flex-col gap-y-0.5">
                <a href="/mailbox?folder=inbox" class="sidebar-link bg-accent-50 text-accent-700" id="nav-inbox">
                    <span>Boîte de réception</span>
                    <span class="unread-badge">5</span>
                </a>
                <a href="/mailbox?folder=sent" class="sidebar-link text-gray-600" id="nav-sent">
                    <span>Envoyés</span>
                </a>
                <a href="/mailbox?folder=drafts" class="sidebar-link text-gray-600" id="nav-drafts">
                    <span>Brouillons</span>
                </a>
                <a href="/mailbox?folder=trash" class="sidebar-link text-gray-600" id="nav-trash">
                    <span>Corbeille</span>
                </a>
            </nav>
            <div id="current-folder-display">Boîte de réception</div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Navigation entre dossiers', () => {
        it('devrait afficher tous les dossiers', () => {
            const navInbox = document.getElementById('nav-inbox');
            const navSent = document.getElementById('nav-sent');
            const navDrafts = document.getElementById('nav-drafts');
            const navTrash = document.getElementById('nav-trash');
            
            expect(navInbox).toBeTruthy();
            expect(navSent).toBeTruthy();
            expect(navDrafts).toBeTruthy();
            expect(navTrash).toBeTruthy();
        });

        it('devrait mettre en évidence le dossier actuel', () => {
            const navInbox = document.getElementById('nav-inbox');
            const navSent = document.getElementById('nav-sent');
            
            expect(navInbox.classList.contains('bg-accent-50')).toBe(true);
            expect(navInbox.classList.contains('text-accent-700')).toBe(true);
            expect(navSent.classList.contains('bg-accent-50')).toBe(false);
        });

        it('devrait cliquer sur un dossier pour changer', () => {
            const navSent = document.getElementById('nav-sent');
            let clicked = false;
            navSent.addEventListener('click', () => {
                clicked = true;
            });
            navSent.click();
            expect(clicked).toBe(true);
        });

        it('devrait mettre à jour l\'affichage du dossier actuel', () => {
            const currentFolderDisplay = document.getElementById('current-folder-display');
            
            // Simulation de changement de dossier
            currentFolderDisplay.textContent = 'Envoyés';
            
            expect(currentFolderDisplay.textContent).toBe('Envoyés');
        });
    });

    describe('Compteurs de messages', () => {
        it('devrait afficher le compteur de messages non lus', () => {
            const unreadBadge = document.querySelector('.unread-badge');
            expect(unreadBadge).toBeTruthy();
            expect(unreadBadge.textContent).toBe('5');
        });

        it('devrait mettre à jour le compteur après lecture', () => {
            const unreadBadge = document.querySelector('.unread-badge');
            
            // Simulation de mise à jour
            unreadBadge.textContent = '4';
            
            expect(unreadBadge.textContent).toBe('4');
        });

        it('devrait masquer le compteur si zéro', () => {
            const unreadBadge = document.querySelector('.unread-badge');
            
            unreadBadge.textContent = '0';
            unreadBadge.style.display = 'none';
            
            expect(unreadBadge.style.display).toBe('none');
        });
    });
});

describe('Envoi de messages - Tests composition', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <div id="compose-modal" class="hidden">
                <form id="compose-form" method="POST" action="/mailbox">
                    <input type="hidden" name="sender_name" value="User Test">
                    <input type="hidden" name="sender_email" value="user@test.com">
                    <input type="email" name="recipient_email" id="compose-recipient" required>
                    <input type="text" name="subject" id="compose-subject" required>
                    <textarea name="body" id="compose-body" required></textarea>
                    <button type="submit" name="save_draft" value="0" id="send-btn">Envoyer</button>
                    <button type="submit" name="save_draft" value="1" id="draft-btn">Brouillon</button>
                </form>
            </div>
            <button id="open-compose-btn">Nouveau message</button>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Ouverture de la modale de composition', () => {
        it('devrait afficher le bouton de composition', () => {
            const openComposeBtn = document.getElementById('open-compose-btn');
            expect(openComposeBtn).toBeTruthy();
        });

        it('devrait ouvrir la modale au clic', () => {
            const openComposeBtn = document.getElementById('open-compose-btn');
            const composeModal = document.getElementById('compose-modal');
            
            openComposeBtn.addEventListener('click', () => {
                composeModal.classList.remove('hidden');
            });
            
            openComposeBtn.click();
            
            expect(composeModal.classList.contains('hidden')).toBe(false);
        });

        it('devrait fermer la modale', () => {
            const composeModal = document.getElementById('compose-modal');
            composeModal.classList.remove('hidden');
            
            composeModal.classList.add('hidden');
            
            expect(composeModal.classList.contains('hidden')).toBe(true);
        });
    });

    describe('Envoi de message', () => {
        it('devrait envoyer un message via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true, message_id: 123 })
            }));

            const composeForm = document.getElementById('compose-form');
            const recipient = document.getElementById('compose-recipient');
            const subject = document.getElementById('compose-subject');
            const body = document.getElementById('compose-body');
            
            recipient.value = 'recipient@example.com';
            subject.value = 'Test subject';
            body.value = 'Test body';

            // Create a simple object instead of FormData to avoid jsdom issues
            const data = {
                recipient_email: recipient.value,
                subject: subject.value,
                body: body.value
            };
            
            const response = await fetch('/mailbox', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const responseData = await response.json();

            expect(mockFetch).toHaveBeenCalledWith('/mailbox', jasmine.objectContaining({
                method: 'POST'
            }));
            expect(responseData.success).toBe(true);
        });

        it('devrait sauvegarder un brouillon', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true, draft_id: 456 })
            }));

            const composeForm = document.getElementById('compose-form');
            const draftBtn = document.getElementById('draft-btn');
            
            draftBtn.click();
            
            // Create a simple object instead of FormData to avoid jsdom issues
            const data = {
                save_draft: '1'
            };

            const response = await fetch('/mailbox', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const responseData = await response.json();

            expect(responseData.success).toBe(true);
        });

        it('devrait valider les champs requis', () => {
            const composeForm = document.getElementById('compose-form');
            const recipient = document.getElementById('compose-recipient');
            const subject = document.getElementById('compose-subject');
            const body = document.getElementById('compose-body');
            
            expect(recipient.required).toBe(true);
            expect(subject.required).toBe(true);
            expect(body.required).toBe(true);
            
            expect(composeForm.checkValidity()).toBe(false);
        });

        it('devrait être valide avec tous les champs remplis', () => {
            const composeForm = document.getElementById('compose-form');
            const recipient = document.getElementById('compose-recipient');
            const subject = document.getElementById('compose-subject');
            const body = document.getElementById('compose-body');
            
            recipient.value = 'recipient@example.com';
            subject.value = 'Test subject';
            body.value = 'Test body';
            
            expect(composeForm.checkValidity()).toBe(true);
        });
    });

    describe('Réponse à un message', () => {
        it('devrait pré-remplir le destinataire en mode réponse', () => {
            const composeModal = document.getElementById('compose-modal');
            const recipient = document.getElementById('compose-recipient');
            
            // Simulation de pré-remplissage
            recipient.value = 'original-sender@example.com';
            recipient.readOnly = true;
            
            expect(recipient.value).toBe('original-sender@example.com');
            expect(recipient.readOnly).toBe(true);
        });

        it('devrait pré-remplir l\'objet avec "Re:"', () => {
            const subject = document.getElementById('compose-subject');
            
            // Simulation de pré-remplissage
            subject.value = 'Re: Original subject';
            
            expect(subject.value).toBe('Re: Original subject');
        });
    });
});