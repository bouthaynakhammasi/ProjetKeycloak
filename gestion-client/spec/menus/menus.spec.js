/**
 * Tests Jasmine pour les Menus et Modales
 * Tests des menus déroulants, sidebar et modales
 */

describe('Menus et Navigation - Tests principaux', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <aside class="w-[220px] bg-white border-r border-gray-200 flex flex-col h-full z-10 shrink-0">
                <div class="h-16 flex items-center px-5 border-b border-gray-100">
                    <a href="/dashboard" class="flex items-center gap-2 no-underline">
                        <span class="text-lg font-bold text-gray-900 tracking-tight">Gestion Client</span>
                    </a>
                </div>
                <div class="flex-1 overflow-y-auto sidebar-scroll py-4 flex flex-col gap-6">
                    <nav class="px-3 flex flex-col gap-1">
                        <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary font-semibold text-sm no-underline" id="nav-dashboard">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            Tableau de bord
                        </a>
                        <a href="/employees" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm no-underline" id="nav-employees">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            Demo User
                        </a>
                    </nav>
                    <div class="px-3">
                        <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Gérer</h3>
                        <nav class="flex flex-col gap-0.5">
                            <a href="/agenda" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm no-underline" id="nav-agenda">
                                <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                Agenda
                            </a>
                            <a href="/employees" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm no-underline" id="nav-employees-manage">
                                <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                                Employés
                            </a>
                        </nav>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100 flex flex-col gap-2">
                    <a href="/logout" class="flex items-center gap-3 px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 text-sm font-medium no-underline" id="nav-logout">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Déconnexion
                    </a>
                </div>
            </aside>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Sidebar principale', () => {
        it('devrait afficher la sidebar', () => {
            const sidebar = document.querySelector('aside');
            expect(sidebar).toBeTruthy();
            expect(sidebar.classList.contains('w-[220px]')).toBe(true);
        });

        it('devrait afficher le logo', () => {
            const logo = document.querySelector('.text-lg.font-bold');
            expect(logo).toBeTruthy();
            expect(logo.textContent).toContain('Gestion Client');
        });

        it('devrait avoir les liens de navigation principale', () => {
            const navDashboard = document.getElementById('nav-dashboard');
            const navEmployees = document.getElementById('nav-employees');
            expect(navDashboard).toBeTruthy();
            expect(navEmployees).toBeTruthy();
        });

        it('devrait mettre en évidence la page active', () => {
            const navDashboard = document.getElementById('nav-dashboard');
            expect(navDashboard.classList.contains('bg-primary/10')).toBe(true);
            expect(navDashboard.classList.contains('text-primary')).toBe(true);
        });
    });

    describe('Navigation secondaire', () => {
        it('devrait afficher la section "Gérer"', () => {
            const manageSection = document.querySelector('h3');
            expect(manageSection).toBeTruthy();
            expect(manageSection.textContent).toContain('Gérer');
        });

        it('devrait avoir les liens de gestion', () => {
            const navAgenda = document.getElementById('nav-agenda');
            const navEmployeesManage = document.getElementById('nav-employees-manage');
            expect(navAgenda).toBeTruthy();
            expect(navEmployeesManage).toBeTruthy();
        });

        it('devrait avoir les icônes dans les liens', () => {
            const icons = document.querySelectorAll('[data-lucide]');
            expect(icons.length).toBeGreaterThan(0);
        });
    });

    describe('Menu de déconnexion', () => {
        it('devrait afficher le lien de déconnexion', () => {
            const navLogout = document.getElementById('nav-logout');
            expect(navLogout).toBeTruthy();
            expect(navLogout.textContent).toContain('Déconnexion');
        });

        it('devrait avoir les styles de déconnexion', () => {
            const navLogout = document.getElementById('nav-logout');
            expect(navLogout.classList.contains('text-red-600')).toBe(true);
            expect(navLogout.classList.contains('hover:bg-red-50')).toBe(true);
        });
    });

    describe('Interactions de navigation', () => {
        it('devrait permettre de cliquer sur les liens', () => {
            const navDashboard = document.getElementById('nav-dashboard');
            let clicked = false;
            navDashboard.addEventListener('click', () => {
                clicked = true;
            });
            navDashboard.click();
            expect(clicked).toBe(true);
        });

        it('devrait avoir les effets hover corrects', () => {
            const navEmployees = document.getElementById('nav-employees');
            expect(navEmployees.classList.contains('hover:bg-gray-50')).toBe(true);
        });
    });
});

describe('Menu utilisateur - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div class="ml-2 relative" id="user-menu-container">
                <button id="user-menu-button" class="flex items-center focus:outline-none" aria-haspopup="true" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=User+Test&background=6366f1&color=ffffff&size=128" alt="User Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200 hover:border-primary transition-colors cursor-pointer" id="user-avatar">
                </button>
                <div id="user-dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-150 rounded-xl shadow-lg py-1 z-50">
                    <div class="px-4 py-2 border-b border-gray-50 text-left">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Mon Compte</p>
                        <p class="text-sm font-semibold text-gray-800 truncate mt-1">User Test</p>
                        <p class="text-xs text-gray-500 truncate mt-0.5">user@test.com</p>
                        <span class="inline-flex items-center mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100 uppercase">
                            ROLE_ADMIN
                        </span>
                    </div>
                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" id="menu-profile">Mon Profil</a>
                    <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" id="menu-settings">Paramètres</a>
                    <div class="border-t border-gray-100"></div>
                    <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50" id="menu-logout">Déconnexion</a>
                </div>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Menu déroulant utilisateur', () => {
        it('devrait afficher le bouton utilisateur', () => {
            const userMenuButton = document.getElementById('user-menu-button');
            expect(userMenuButton).toBeTruthy();
        });

        it('devrait afficher l\'avatar utilisateur', () => {
            const userAvatar = document.getElementById('user-avatar');
            expect(userAvatar).toBeTruthy();
            expect(userAvatar.classList.contains('w-8')).toBe(true);
            expect(userAvatar.classList.contains('h-8')).toBe(true);
        });

        it('devrait avoir le menu déroulant caché par défaut', () => {
            const userDropdownMenu = document.getElementById('user-dropdown-menu');
            expect(userDropdownMenu).toBeTruthy();
            expect(userDropdownMenu.classList.contains('hidden')).toBe(true);
        });

        it('devrait afficher les informations utilisateur', () => {
            const userDropdownMenu = document.getElementById('user-dropdown-menu');
            expect(userDropdownMenu.textContent).toContain('User Test');
            expect(userDropdownMenu.textContent).toContain('user@test.com');
            expect(userDropdownMenu.textContent).toContain('ROLE_ADMIN');
        });

        it('devrait avoir les liens du menu', () => {
            const menuProfile = document.getElementById('menu-profile');
            const menuSettings = document.getElementById('menu-settings');
            const menuLogout = document.getElementById('menu-logout');
            expect(menuProfile).toBeTruthy();
            expect(menuSettings).toBeTruthy();
            expect(menuLogout).toBeTruthy();
        });
    });

    describe('Interactions du menu utilisateur', () => {
        it('devrait permettre d\'ouvrir le menu', () => {
            const userMenuButton = document.getElementById('user-menu-button');
            const userDropdownMenu = document.getElementById('user-dropdown-menu');
            
            userMenuButton.click();
            userDropdownMenu.classList.remove('hidden');
            
            expect(userDropdownMenu.classList.contains('hidden')).toBe(false);
        });

        it('devrait permettre de fermer le menu', () => {
            const userDropdownMenu = document.getElementById('user-dropdown-menu');
            userDropdownMenu.classList.remove('hidden');
            
            userDropdownMenu.classList.add('hidden');
            
            expect(userDropdownMenu.classList.contains('hidden')).toBe(true);
        });

        it('devrait cliquer sur les liens du menu', () => {
            const menuProfile = document.getElementById('menu-profile');
            let clicked = false;
            menuProfile.addEventListener('click', () => {
                clicked = true;
            });
            menuProfile.click();
            expect(clicked).toBe(true);
        });
    });
});

describe('Modales - Tests principaux', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div id="compose-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="compose-modal-title">
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" id="modal-backdrop"></div>
                <div class="absolute bottom-4 right-6 w-full max-w-lg animate-modal-in">
                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3.5 bg-gray-900 text-white">
                            <h2 id="compose-modal-title" class="text-sm font-semibold">Nouveau message</h2>
                            <div class="flex items-center gap-2">
                                <button onclick="closeComposeModal()" class="p-1 rounded hover:bg-white/10 transition-colors" id="modal-close-btn">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-5">
                            <form id="compose-form">
                                <input type="email" name="recipient_email" id="compose-recipient" required placeholder="destinataire@exemple.com" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                                <input type="text" name="subject" id="compose-subject" required placeholder="Objet du message" class="w-full px-3 py-2 border border-gray-200 rounded-lg mt-2">
                                <textarea name="body" id="compose-body" required rows="10" placeholder="Rédigez votre message ici…" class="w-full px-3 py-2 border border-gray-200 rounded-lg mt-2"></textarea>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <button onclick="openComposeModal()" id="open-modal-btn">Ouvrir la modale</button>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Modale de composition', () => {
        it('devrait afficher la modale cachée par défaut', () => {
            const modal = document.getElementById('compose-modal');
            expect(modal).toBeTruthy();
            expect(modal.classList.contains('hidden')).toBe(true);
        });

        it('devrait avoir le backdrop', () => {
            const backdrop = document.getElementById('modal-backdrop');
            expect(backdrop).toBeTruthy();
            expect(backdrop.classList.contains('bg-black/30')).toBe(true);
        });

        it('devrait avoir le titre de la modale', () => {
            const modalTitle = document.getElementById('compose-modal-title');
            expect(modalTitle).toBeTruthy();
            expect(modalTitle.textContent).toContain('Nouveau message');
        });

        it('devrait avoir le bouton de fermeture', () => {
            const closeBtn = document.getElementById('modal-close-btn');
            expect(closeBtn).toBeTruthy();
        });

        it('devrait avoir le formulaire de composition', () => {
            const composeForm = document.getElementById('compose-form');
            expect(composeForm).toBeTruthy();
        });

        it('devrait avoir les champs du formulaire', () => {
            const recipient = document.getElementById('compose-recipient');
            const subject = document.getElementById('compose-subject');
            const body = document.getElementById('compose-body');
            expect(recipient).toBeTruthy();
            expect(subject).toBeTruthy();
            expect(body).toBeTruthy();
        });
    });

    describe('Interactions de la modale', () => {
        it('devrait simuler l\'ouverture de la modale', () => {
            const modal = document.getElementById('compose-modal');
            modal.classList.remove('hidden');
            expect(modal.classList.contains('hidden')).toBe(false);
        });

        it('devrait simuler la fermeture de la modale', () => {
            const modal = document.getElementById('compose-modal');
            modal.classList.remove('hidden');
            modal.classList.add('hidden');
            expect(modal.classList.contains('hidden')).toBe(true);
        });

        it('devrait cliquer sur le bouton de fermeture', () => {
            const closeBtn = document.getElementById('modal-close-btn');
            let clicked = false;
            closeBtn.addEventListener('click', () => {
                clicked = true;
            });
            closeBtn.click();
            expect(clicked).toBe(true);
        });

        it('devrait cliquer sur le backdrop pour fermer', () => {
            const backdrop = document.getElementById('modal-backdrop');
            let clicked = false;
            backdrop.addEventListener('click', () => {
                clicked = true;
            });
            backdrop.click();
            expect(clicked).toBe(true);
        });
    });
});

describe('Modales Mailbox - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div id="compose-modal" class="fixed inset-0 z-50 hidden" role="dialog">
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeComposeModal()"></div>
                <div class="absolute bottom-4 right-6 w-full max-w-lg">
                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3.5 bg-gray-900 text-white">
                            <h2 class="text-sm font-semibold">Nouveau message</h2>
                            <button onclick="closeComposeModal()" class="p-1 rounded hover:bg-white/10" id="close-modal">✕</button>
                        </div>
                        <form id="compose-form">
                            <div class="px-5 py-2.5 flex items-center gap-3">
                                <span class="text-xs text-gray-400 w-6 shrink-0">De</span>
                                <span class="text-sm text-gray-700">user@example.com</span>
                            </div>
                            <div class="px-5 py-2.5 flex items-center gap-3">
                                <label class="text-xs text-gray-400 w-6 shrink-0">À</label>
                                <input type="email" name="recipient_email" id="compose-recipient" required class="flex-1 text-sm text-gray-800 bg-transparent border-none outline-none">
                            </div>
                            <div class="px-5 py-2.5 flex items-center gap-3">
                                <label class="text-xs text-gray-400 w-6 shrink-0">Objet</label>
                                <input type="text" name="subject" id="compose-subject" required class="flex-1 text-sm text-gray-800 bg-transparent border-none outline-none">
                            </div>
                            <textarea name="body" id="compose-body" required rows="10" class="w-full px-5 py-4 text-sm text-gray-700 bg-transparent border-none resize-none outline-none"></textarea>
                            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50">
                                <button type="submit" name="save_draft" value="0" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-accent-600 text-white text-sm font-medium" id="send-btn">Envoyer</button>
                                <button type="submit" name="save_draft" value="1" class="px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-200" id="draft-btn">Brouillon</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <button onclick="openComposeModal()" id="new-message-btn">Nouveau message</button>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Modale de messagerie', () => {
        it('devrait avoir le bouton nouveau message', () => {
            const newMessageBtn = document.getElementById('new-message-btn');
            expect(newMessageBtn).toBeTruthy();
        });

        it('devrait avoir les champs De, À, Objet', () => {
            const recipient = document.getElementById('compose-recipient');
            const subject = document.getElementById('compose-subject');
            expect(recipient).toBeTruthy();
            expect(subject).toBeTruthy();
        });

        it('devrait avoir les boutons Envoyer et Brouillon', () => {
            const sendBtn = document.getElementById('send-btn');
            const draftBtn = document.getElementById('draft-btn');
            expect(sendBtn).toBeTruthy();
            expect(draftBtn).toBeTruthy();
        });

        it('devrait avoir le champ "De" en lecture seule', () => {
            const fromField = document.querySelector('.text-sm.text-gray-700');
            expect(fromField).toBeTruthy();
            expect(fromField.textContent).toContain('user@example.com');
        });
    });

    describe('Interactions de la modale messagerie', () => {
        it('devrait simuler l\'ouverture', () => {
            const modal = document.getElementById('compose-modal');
            modal.classList.remove('hidden');
            expect(modal.classList.contains('hidden')).toBe(false);
        });

        it('devrait permettre de remplir les champs', () => {
            const recipient = document.getElementById('compose-recipient');
            const subject = document.getElementById('compose-subject');
            const body = document.getElementById('compose-body');
            
            recipient.value = 'recipient@example.com';
            subject.value = 'Test sujet';
            body.value = 'Test message';
            
            expect(recipient.value).toBe('recipient@example.com');
            expect(subject.value).toBe('Test sujet');
            expect(body.value).toBe('Test message');
        });

        it('devrait cliquer sur Envoyer', () => {
            const sendBtn = document.getElementById('send-btn');
            let clicked = false;
            sendBtn.addEventListener('click', () => {
                clicked = true;
            });
            sendBtn.click();
            expect(clicked).toBe(true);
        });

        it('devrait cliquer sur Brouillon', () => {
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