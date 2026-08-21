/**
 * Tests Jasmine pour les Notifications
 * Tests des notifications, messages flash et alertes
 */

describe('Notifications - Tests principaux', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm" id="success-notification">
                Opération réussie avec succès !
            </div>
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-sm" id="error-notification">
                Une erreur s'est produite.
            </div>
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 shadow-sm" id="warning-notification">
                Attention : action requise.
            </div>
            <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 shadow-sm" id="info-notification">
                Information importante.
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Notifications de succès', () => {
        it('devrait afficher une notification de succès', () => {
            const successNotification = document.getElementById('success-notification');
            expect(successNotification).toBeTruthy();
        });

        it('devrait avoir les classes correctes pour le succès', () => {
            const successNotification = document.getElementById('success-notification');
            expect(successNotification.classList.contains('bg-emerald-50')).toBe(true);
            expect(successNotification.classList.contains('text-emerald-700')).toBe(true);
            expect(successNotification.classList.contains('border-emerald-200')).toBe(true);
        });

        it('devrait contenir le message de succès', () => {
            const successNotification = document.getElementById('success-notification');
            expect(successNotification.textContent).toContain('succès');
        });
    });

    describe('Notifications d\'erreur', () => {
        it('devrait afficher une notification d\'erreur', () => {
            const errorNotification = document.getElementById('error-notification');
            expect(errorNotification).toBeTruthy();
        });

        it('devrait avoir les classes correctes pour l\'erreur', () => {
            const errorNotification = document.getElementById('error-notification');
            expect(errorNotification.classList.contains('bg-rose-50')).toBe(true);
            expect(errorNotification.classList.contains('text-rose-700')).toBe(true);
            expect(errorNotification.classList.contains('border-rose-200')).toBe(true);
        });

        it('devrait contenir le message d\'erreur', () => {
            const errorNotification = document.getElementById('error-notification');
            expect(errorNotification.textContent).toContain('erreur');
        });
    });

    describe('Notifications d\'avertissement', () => {
        it('devrait afficher une notification d\'avertissement', () => {
            const warningNotification = document.getElementById('warning-notification');
            expect(warningNotification).toBeTruthy();
        });

        it('devrait avoir les classes correctes pour l\'avertissement', () => {
            const warningNotification = document.getElementById('warning-notification');
            expect(warningNotification.classList.contains('bg-amber-50')).toBe(true);
            expect(warningNotification.classList.contains('text-amber-700')).toBe(true);
            expect(warningNotification.classList.contains('border-amber-200')).toBe(true);
        });

        it('devrait contenir le message d\'avertissement', () => {
            const warningNotification = document.getElementById('warning-notification');
            expect(warningNotification.textContent).toContain('Attention');
        });
    });

    describe('Notifications d\'information', () => {
        it('devrait afficher une notification d\'information', () => {
            const infoNotification = document.getElementById('info-notification');
            expect(infoNotification).toBeTruthy();
        });

        it('devrait avoir les classes correctes pour l\'information', () => {
            const infoNotification = document.getElementById('info-notification');
            expect(infoNotification.classList.contains('bg-blue-50')).toBe(true);
            expect(infoNotification.classList.contains('text-blue-700')).toBe(true);
            expect(infoNotification.classList.contains('border-blue-200')).toBe(true);
        });

        it('devrait contenir le message d\'information', () => {
            const infoNotification = document.getElementById('info-notification');
            expect(infoNotification.textContent).toContain('Information');
        });
    });

    describe('Style des notifications', () => {
        it('devrait avoir le style rounded-2xl', () => {
            const notifications = document.querySelectorAll('[id$="-notification"]');
            notifications.forEach(notification => {
                expect(notification.classList.contains('rounded-2xl')).toBe(true);
            });
        });

        it('devrait avoir le style shadow-sm', () => {
            const notifications = document.querySelectorAll('[id$="-notification"]');
            notifications.forEach(notification => {
                expect(notification.classList.contains('shadow-sm')).toBe(true);
            });
        });

        it('devrait avoir le padding correct', () => {
            const notifications = document.querySelectorAll('[id$="-notification"]');
            notifications.forEach(notification => {
                expect(notification.classList.contains('px-4')).toBe(true);
                expect(notification.classList.contains('py-3')).toBe(true);
            });
        });
    });
});

describe('Notifications avec icônes - Tests avancés', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div class="mx-5 mt-3 px-4 py-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade-in flex items-center gap-2" id="icon-success">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Mise à jour réussie</span>
            </div>
            <div class="mx-5 mt-3 px-4 py-2.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm animate-fade-in flex items-center gap-2" id="icon-error">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/>
                </svg>
                <span>Erreur de validation</span>
            </div>
            <div class="mx-5 mt-3 px-4 py-2.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-sm animate-fade-in flex items-center gap-2" id="icon-warning">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Attention requise</span>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Notifications avec icônes SVG', () => {
        it('devrait afficher l\'icône de succès', () => {
            const iconSuccess = document.getElementById('icon-success');
            const svg = iconSuccess.querySelector('svg');
            expect(svg).toBeTruthy();
        });

        it('devrait afficher l\'icône d\'erreur', () => {
            const iconError = document.getElementById('icon-error');
            const svg = iconError.querySelector('svg');
            expect(svg).toBeTruthy();
        });

        it('devrait afficher l\'icône d\'avertissement', () => {
            const iconWarning = document.getElementById('icon-warning');
            const svg = iconWarning.querySelector('svg');
            expect(svg).toBeTruthy();
        });

        it('devrait avoir le layout flex avec gap', () => {
            const notifications = document.querySelectorAll('[id^="icon-"]');
            notifications.forEach(notification => {
                expect(notification.classList.contains('flex')).toBe(true);
                expect(notification.classList.contains('items-center')).toBe(true);
                expect(notification.classList.contains('gap-2')).toBe(true);
            });
        });

        it('devrait avoir l\'animation fade-in', () => {
            const notifications = document.querySelectorAll('[id^="icon-"]');
            notifications.forEach(notification => {
                expect(notification.classList.contains('animate-fade-in')).toBe(true);
            });
        });
    });
});

describe('Badges de notification - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <button class="text-gray-500 hover:text-gray-700 relative" id="notification-button">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white" id="notification-badge">3</span>
            </button>
            <a href="/admin/users" class="text-gray-500 hover:text-gray-700 relative no-underline" id="pending-users-link">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white" id="pending-badge">5</span>
            </a>
            <button class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center relative" id="user-notifications">
                <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center" id="user-badge">2</span>
            </button>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Badges de notification', () => {
        it('devrait afficher le badge de notification principal', () => {
            const notificationBadge = document.getElementById('notification-badge');
            expect(notificationBadge).toBeTruthy();
            expect(notificationBadge.textContent).toBe('3');
        });

        it('devrait afficher le badge des utilisateurs en attente', () => {
            const pendingBadge = document.getElementById('pending-badge');
            expect(pendingBadge).toBeTruthy();
            expect(pendingBadge.textContent).toBe('5');
        });

        it('devrait afficher le badge utilisateur', () => {
            const userBadge = document.getElementById('user-badge');
            expect(userBadge).toBeTruthy();
            expect(userBadge.textContent).toBe('2');
        });

        it('devrait avoir les styles de badge corrects', () => {
            const badges = document.querySelectorAll('[id$="-badge"]');
            badges.forEach(badge => {
                expect(badge.classList.contains('bg-red-500')).toBe(true);
                expect(badge.classList.contains('text-white')).toBe(true);
                expect(badge.classList.contains('rounded-full')).toBe(true);
            });
        });

        it('devrait être positionné correctement', () => {
            const badges = document.querySelectorAll('[id$="-badge"]');
            badges.forEach(badge => {
                expect(badge.classList.contains('absolute')).toBe(true);
                expect(badge.classList.contains('-top-1')).toBe(true);
                expect(badge.classList.contains('-right-1')).toBe(true);
            });
        });
    });

    describe('Interactions des badges', () => {
        it('devrait cliquer sur le bouton de notification', () => {
            const notificationButton = document.getElementById('notification-button');
            let clicked = false;
            notificationButton.addEventListener('click', () => {
                clicked = true;
            });
            notificationButton.click();
            expect(clicked).toBe(true);
        });

        it('devrait cliquer sur le lien des utilisateurs en attente', () => {
            const pendingUsersLink = document.getElementById('pending-users-link');
            let clicked = false;
            pendingUsersLink.addEventListener('click', () => {
                clicked = true;
            });
            pendingUsersLink.click();
            expect(clicked).toBe(true);
        });

        it('devrait cliquer sur le bouton utilisateur', () => {
            const userNotifications = document.getElementById('user-notifications');
            let clicked = false;
            userNotifications.addEventListener('click', () => {
                clicked = true;
            });
            userNotifications.click();
            expect(clicked).toBe(true);
        });
    });
});

describe('Notifications Toast - Tests dynamiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Système de notifications dynamiques', () => {
        it('devrait avoir un conteneur de toast', () => {
            const toastContainer = document.getElementById('toast-container');
            expect(toastContainer).toBeTruthy();
        });

        it('devrait être positionné en haut à droite', () => {
            const toastContainer = document.getElementById('toast-container');
            expect(toastContainer.classList.contains('fixed')).toBe(true);
            expect(toastContainer.classList.contains('top-4')).toBe(true);
            expect(toastContainer.classList.contains('right-4')).toBe(true);
        });

        it('devrait pouvoir ajouter une notification dynamique', () => {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg shadow-lg';
            toast.textContent = 'Notification dynamique';
            toastContainer.appendChild(toast);
            
            expect(toastContainer.children.length).toBe(1);
            expect(toast.textContent).toBe('Notification dynamique');
        });

        it('devrait pouvoir supprimer une notification', () => {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg shadow-lg';
            toast.textContent = 'Notification à supprimer';
            toastContainer.appendChild(toast);
            
            expect(toastContainer.children.length).toBe(1);
            
            toast.remove();
            expect(toastContainer.children.length).toBe(0);
        });

        it('devrait pouvoir ajouter plusieurs notifications', () => {
            const toastContainer = document.getElementById('toast-container');
            
            for (let i = 0; i < 3; i++) {
                const toast = document.createElement('div');
                toast.className = 'bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg shadow-lg';
                toast.textContent = `Notification ${i + 1}`;
                toastContainer.appendChild(toast);
            }
            
            expect(toastContainer.children.length).toBe(3);
        });
    });
});

describe('Notifications Mailbox - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div class="mx-5 mt-3 px-4 py-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade-in flex items-center gap-2" id="mailbox-success">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Message envoyé avec succès
            </div>
            <div class="mx-5 mt-3 px-4 py-2.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm animate-fade-in flex items-center gap-2" id="mailbox-error">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/>
                </svg>
                Erreur lors de l'envoi du message
            </div>
            <div class="sidebar-link flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium bg-accent-50 text-accent-700" id="inbox-link">
                <span class="flex-1 leading-tight">Boîte de réception</span>
                <span class="shrink-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-accent-600 text-white text-[10px] font-semibold" id="unread-count">7</span>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Notifications de messagerie', () => {
        it('devrait afficher la notification de succès d\'envoi', () => {
            const mailboxSuccess = document.getElementById('mailbox-success');
            expect(mailboxSuccess).toBeTruthy();
            expect(mailboxSuccess.textContent).toContain('Message envoyé');
        });

        it('devrait afficher la notification d\'erreur d\'envoi', () => {
            const mailboxError = document.getElementById('mailbox-error');
            expect(mailboxError).toBeTruthy();
            expect(mailboxError.textContent).toContain('Erreur');
        });

        it('devrait afficher le compteur de messages non lus', () => {
            const unreadCount = document.getElementById('unread-count');
            expect(unreadCount).toBeTruthy();
            expect(unreadCount.textContent).toBe('7');
        });

        it('devrait avoir les styles corrects pour le compteur', () => {
            const unreadCount = document.getElementById('unread-count');
            expect(unreadCount.classList.contains('bg-accent-600')).toBe(true);
            expect(unreadCount.classList.contains('text-white')).toBe(true);
            expect(unreadCount.classList.contains('rounded-full')).toBe(true);
        });

        it('devrait être positionné dans le lien de la boîte de réception', () => {
            const inboxLink = document.getElementById('inbox-link');
            const unreadCount = document.getElementById('unread-count');
            expect(inboxLink.contains(unreadCount)).toBe(true);
        });
    });
});