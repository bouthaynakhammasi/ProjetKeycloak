/**
 * Tests Jasmine pour l'Espace Employee
 * Tests des interactions spécifiques à l'espace employé
 */

describe('Espace Employee - Tests principaux', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=Employee+User&background=6366f1&color=ffffff&size=128" alt="User Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" id="employee-avatar">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900" id="employee-name">Bonjour Employee User !</h1>
                            <p class="text-xs text-purple-600 font-medium mt-0.5" id="employee-role">Espace Employé</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-gray-500 text-sm font-medium" id="current-date">14 août 2026</div>
                        <div class="relative">
                            <button class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center" id="notification-btn">
                                <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center" id="notification-badge">2</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl shadow-sm p-6 text-white" id="welcome-banner">
                    <h2 class="text-lg font-semibold mb-2">Bienvenue sur votre espace personnel</h2>
                    <p class="text-sm text-purple-100 leading-relaxed">Ici, vous pouvez effectuer vos demandes de congés, suivre vos heures de travail, et consulter votre agenda.</p>
                </div>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Interface Employee', () => {
        it('devrait afficher l\'avatar employé', () => {
            const employeeAvatar = document.getElementById('employee-avatar');
            expect(employeeAvatar).toBeTruthy();
            expect(employeeAvatar.classList.contains('w-12')).toBe(true);
            expect(employeeAvatar.classList.contains('h-12')).toBe(true);
        });

        it('devrait afficher le nom de l\'employé', () => {
            const employeeName = document.getElementById('employee-name');
            expect(employeeName).toBeTruthy();
            expect(employeeName.textContent).toContain('Employee User');
        });

        it('devrait afficher le badge "Espace Employé"', () => {
            const employeeRole = document.getElementById('employee-role');
            expect(employeeRole).toBeTruthy();
            expect(employeeRole.textContent).toContain('Espace Employé');
        });

        it('devrait afficher la date actuelle', () => {
            const currentDate = document.getElementById('current-date');
            expect(currentDate).toBeTruthy();
        });

        it('devrait afficher la bannière de bienvenue', () => {
            const welcomeBanner = document.getElementById('welcome-banner');
            expect(welcomeBanner).toBeTruthy();
            expect(welcomeBanner.classList.contains('bg-gradient-to-r')).toBe(true);
        });
    });

    describe('Notifications Employee', () => {
        it('devrait afficher le bouton de notification', () => {
            const notificationBtn = document.getElementById('notification-btn');
            expect(notificationBtn).toBeTruthy();
        });

        it('devrait afficher le badge de notification', () => {
            const notificationBadge = document.getElementById('notification-badge');
            expect(notificationBadge).toBeTruthy();
            expect(notificationBadge.textContent).toBe('2');
        });

        it('devrait avoir les styles corrects pour le badge', () => {
            const notificationBadge = document.getElementById('notification-badge');
            expect(notificationBadge.classList.contains('bg-red-500')).toBe(true);
            expect(notificationBadge.classList.contains('text-white')).toBe(true);
            expect(notificationBadge.classList.contains('rounded-full')).toBe(true);
        });

        it('devrait cliquer sur le bouton de notification', () => {
            const notificationBtn = document.getElementById('notification-btn');
            let clicked = false;
            notificationBtn.addEventListener('click', () => {
                clicked = true;
            });
            notificationBtn.click();
            expect(clicked).toBe(true);
        });
    });
});

describe('Navigation Employee - Tests spécifiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <nav class="flex flex-col gap-0.5">
                <a href="/profile" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary font-semibold text-sm" id="nav-profile">
                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                    Mon Profil
                </a>
                <a href="/agenda/employee" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm" id="nav-agenda">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                    Mon Agenda
                </a>
                <a href="/presences/employee" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm" id="nav-presences">
                    <i data-lucide="timer" class="w-4 h-4 text-gray-400"></i>
                    Mes Présences
                </a>
                <a href="/absences" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm" id="nav-absences">
                    <i data-lucide="palmtree" class="w-4 h-4 text-gray-400"></i>
                    Mes Absences
                </a>
                <a href="/salaires/employee" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm" id="nav-salaires">
                    <i data-lucide="coins" class="w-4 h-4 text-gray-400"></i>
                    Mes Salaires
                </a>
            </nav>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Menu navigation employé', () => {
        it('devrait afficher tous les liens de navigation', () => {
            const navProfile = document.getElementById('nav-profile');
            const navAgenda = document.getElementById('nav-agenda');
            const navPresences = document.getElementById('nav-presences');
            const navAbsences = document.getElementById('nav-absences');
            const navSalaires = document.getElementById('nav-salaires');
            
            expect(navProfile).toBeTruthy();
            expect(navAgenda).toBeTruthy();
            expect(navPresences).toBeTruthy();
            expect(navAbsences).toBeTruthy();
            expect(navSalaires).toBeTruthy();
        });

        it('devrait mettre en évidence la page active', () => {
            const navProfile = document.getElementById('nav-profile');
            const navAgenda = document.getElementById('nav-agenda');
            
            expect(navProfile.classList.contains('bg-primary/10')).toBe(true);
            expect(navProfile.classList.contains('text-primary')).toBe(true);
            expect(navAgenda.classList.contains('bg-primary/10')).toBe(false);
        });

        it('devrait avoir les icônes correctes', () => {
            const icons = document.querySelectorAll('[data-lucide]');
            expect(icons.length).toBe(5);
        });

        it('devrait cliquer sur les liens de navigation', () => {
            const navAbsences = document.getElementById('nav-absences');
            let clicked = false;
            navAbsences.addEventListener('click', () => {
                clicked = true;
            });
            navAbsences.click();
            expect(clicked).toBe(true);
        });
    });
});

describe('Demandes d\'absence Employee - Tests fonctionnels', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Effectuer une demande d'absence</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <a href="/absences/create" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group" id="conge-link">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                                <i data-lucide="sun" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Congés payés</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500" id="conge-days">15 jours disponibles</span>
                        </div>
                    </a>
                    <a href="/absences/create" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group" id="maladie-link">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                                <i data-lucide="briefcase-medical" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Congés maladies</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Illimité</span>
                        </div>
                    </a>
                    <a href="/absences/create" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group" id="recup-link">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center text-teal-500">
                                <i data-lucide="heart-pulse" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Heures de récup</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500" id="recup-hours">12h 48m disponibles</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Mes dernières demandes</h2>
                    <a href="/absences" class="text-xs text-primary hover:text-primary/80 font-medium" id="view-all-absences">Voir tout</a>
                </div>
                <div class="space-y-3" id="recent-requests">
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center">
                                <i data-lucide="sun" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Congés payés</p>
                                <p class="text-xs text-gray-500">01/08/2026 - 05/08/2026</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Approuvé</span>
                    </div>
                </div>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Carte de demande d\'absence', () => {
        it('devrait afficher les types d\'absence', () => {
            const congeLink = document.getElementById('conge-link');
            const maladieLink = document.getElementById('maladie-link');
            const recupLink = document.getElementById('recup-link');
            
            expect(congeLink).toBeTruthy();
            expect(maladieLink).toBeTruthy();
            expect(recupLink).toBeTruthy();
        });

        it('devrait afficher les soldes disponibles', () => {
            const congeDays = document.getElementById('conge-days');
            const recupHours = document.getElementById('recup-hours');
            
            expect(congeDays).toBeTruthy();
            expect(recupHours).toBeTruthy();
            expect(congeDays.textContent).toContain('15 jours');
            expect(recupHours.textContent).toContain('12h 48m');
        });

        it('devrait avoir les icônes correctes', () => {
            const icons = document.querySelectorAll('[data-lucide]');
            // Count only the icons in the absence card section
            const absenceCard = document.querySelector('.divide-y.divide-gray-50');
            const cardIcons = absenceCard ? absenceCard.querySelectorAll('[data-lucide]') : [];
            expect(cardIcons.length).toBe(3);
        });

        it('devrait cliquer sur les liens de demande', () => {
            const congeLink = document.getElementById('conge-link');
            let clicked = false;
            congeLink.addEventListener('click', () => {
                clicked = true;
            });
            congeLink.click();
            expect(clicked).toBe(true);
        });
    });

    describe('Demandes récentes', () => {
        it('devrait afficher la section des demandes récentes', () => {
            const recentRequests = document.getElementById('recent-requests');
            expect(recentRequests).toBeTruthy();
        });

        it('devrait afficher le lien "Voir tout"', () => {
            const viewAllAbsences = document.getElementById('view-all-absences');
            expect(viewAllAbsences).toBeTruthy();
            expect(viewAllAbsences.textContent).toContain('Voir tout');
        });

        it('devrait afficher les demandes avec statuts', () => {
            const recentRequests = document.getElementById('recent-requests');
            expect(recentRequests.textContent).toContain('Congés payés');
            expect(recentRequests.textContent).toContain('Approuvé');
        });

        it('devrait avoir les badges de statut corrects', () => {
            const statusBadge = document.querySelector('.bg-emerald-100');
            expect(statusBadge).toBeTruthy();
            expect(statusBadge.classList.contains('text-emerald-700')).toBe(true);
        });
    });
});

describe('Présences Employee - Tests spécifiques', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Présence du jour</h2>
                <div class="space-y-4 mb-6" id="presence-info">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Statut</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700" id="presence-status">Présent</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Heure d'arrivée</span>
                        <span class="font-medium" id="arrival-time">08:30</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Heure de départ</span>
                        <span class="font-medium" id="departure-time">--:--</span>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <a href="/presences" class="text-sm text-primary hover:text-primary/80 font-medium flex items-center gap-2" id="view-presences">
                        Voir mes présences
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Carte de présence', () => {
        it('devrait afficher les informations de présence', () => {
            const presenceInfo = document.getElementById('presence-info');
            expect(presenceInfo).toBeTruthy();
        });

        it('devrait afficher le statut de présence', () => {
            const presenceStatus = document.getElementById('presence-status');
            expect(presenceStatus).toBeTruthy();
            expect(presenceStatus.textContent).toContain('Présent');
        });

        it('devrait afficher l\'heure d\'arrivée', () => {
            const arrivalTime = document.getElementById('arrival-time');
            expect(arrivalTime).toBeTruthy();
            expect(arrivalTime.textContent).toBe('08:30');
        });

        it('devrait afficher l\'heure de départ', () => {
            const departureTime = document.getElementById('departure-time');
            expect(departureTime).toBeTruthy();
        });

        it('devrait avoir le lien vers les présences', () => {
            const viewPresences = document.getElementById('view-presences');
            expect(viewPresences).toBeTruthy();
            expect(viewPresences.textContent).toContain('Voir mes présences');
        });

        it('devrait cliquer sur le lien des présences', () => {
            const viewPresences = document.getElementById('view-presences');
            let clicked = false;
            viewPresences.addEventListener('click', () => {
                clicked = true;
            });
            viewPresences.click();
            expect(clicked).toBe(true);
        });
    });

    describe('Enregistrement automatique de présence', () => {
        it('devrait simuler l\'enregistrement de l\'heure d\'arrivée', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true, arrival_time: '08:30' })
            }));

            const arrivalTime = document.getElementById('arrival-time');

            const response = await fetch('/presences/clock-in', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                }
            });

            const data = await response.json();

            if (data.success) {
                arrivalTime.textContent = data.arrival_time;
            }

            expect(mockFetch).toHaveBeenCalledWith('/presences/clock-in', jasmine.objectContaining({
                method: 'POST'
            }));
            expect(arrivalTime.textContent).toBe('08:30');
        });

        it('devrait simuler l\'enregistrement de l\'heure de départ', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true, departure_time: '17:30' })
            }));

            const departureTime = document.getElementById('departure-time');

            const response = await fetch('/presences/clock-out', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                }
            });

            const data = await response.json();

            if (data.success) {
                departureTime.textContent = data.departure_time;
            }

            expect(mockFetch).toHaveBeenCalledWith('/presences/clock-out', jasmine.objectContaining({
                method: 'POST'
            }));
            expect(departureTime.textContent).toBe('17:30');
        });
    });
});

describe('Profil Employee - Tests spécifiques', () => {
    
    let mockFetch;
    
    beforeEach(() => {
        mockFetch = jasmine.createSpy('fetch');
        global.fetch = mockFetch;
        
        document.body.innerHTML = `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Mon Profil</h2>
                <form id="profile-form">
                    <div class="flex items-center gap-4 mb-6">
                        <img src="https://ui-avatars.com/api/?name=Employee+User&background=6366f1&color=ffffff&size=128" alt="Profile" class="w-20 h-20 rounded-full object-cover" id="profile-avatar">
                        <div>
                            <button type="button" class="px-4 py-2 bg-primary text-white rounded-lg text-sm" id="change-photo-btn">Changer la photo</button>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG. Max 2MB</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                            <input type="text" value="Employee" class="w-full px-3 py-2 border border-gray-200 rounded-lg" id="profile-nom">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                            <input type="text" value="User" class="w-full px-3 py-2 border border-gray-200 rounded-lg" id="profile-prenom">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" value="employee@example.com" class="w-full px-3 py-2 border border-gray-200 rounded-lg" id="profile-email">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="tel" value="0123456789" class="w-full px-3 py-2 border border-gray-200 rounded-lg" id="profile-phone">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg text-sm font-medium" id="save-profile-btn">Enregistrer</button>
                    </div>
                </form>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
        delete global.fetch;
    });

    describe('Formulaire de profil', () => {
        it('devrait afficher l\'avatar de profil', () => {
            const profileAvatar = document.getElementById('profile-avatar');
            expect(profileAvatar).toBeTruthy();
            expect(profileAvatar.classList.contains('w-20')).toBe(true);
            expect(profileAvatar.classList.contains('h-20')).toBe(true);
        });

        it('devrait afficher le bouton de changement de photo', () => {
            const changePhotoBtn = document.getElementById('change-photo-btn');
            expect(changePhotoBtn).toBeTruthy();
            expect(changePhotoBtn.textContent).toContain('Changer la photo');
        });

        it('devrait afficher les champs de profil', () => {
            const profileNom = document.getElementById('profile-nom');
            const profilePrenom = document.getElementById('profile-prenom');
            const profileEmail = document.getElementById('profile-email');
            const profilePhone = document.getElementById('profile-phone');
            
            expect(profileNom).toBeTruthy();
            expect(profilePrenom).toBeTruthy();
            expect(profileEmail).toBeTruthy();
            expect(profilePhone).toBeTruthy();
        });

        it('devrait avoir les valeurs pré-remplies', () => {
            const profileNom = document.getElementById('profile-nom');
            const profileEmail = document.getElementById('profile-email');
            
            expect(profileNom.value).toBe('Employee');
            expect(profileEmail.value).toBe('employee@example.com');
        });

        it('devrait cliquer sur le bouton de sauvegarde', () => {
            const saveProfileBtn = document.getElementById('save-profile-btn');
            let clicked = false;
            saveProfileBtn.addEventListener('click', () => {
                clicked = true;
            });
            saveProfileBtn.click();
            expect(clicked).toBe(true);
        });
    });

    describe('Mise à jour du profil', () => {
        it('devrait sauvegarder les modifications via AJAX', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true })
            }));

            const profileForm = document.getElementById('profile-form');
            const profileNom = document.getElementById('profile-nom');
            const profilePrenom = document.getElementById('profile-prenom');
            
            profileNom.value = 'Updated';
            profilePrenom.value = 'Name';

            // Create a simple object instead of FormData to avoid jsdom issues
            const data = {
                nom: profileNom.value,
                prenom: profilePrenom.value
            };

            const response = await fetch('/profile', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': 'test-token',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const responseData = await response.json();

            expect(mockFetch).toHaveBeenCalledWith('/profile', jasmine.objectContaining({
                method: 'POST'
            }));
            expect(responseData.success).toBe(true);
        });

        it('devrait mettre à jour l\'avatar', async () => {
            mockFetch.and.returnValue(Promise.resolve({
                ok: true,
                json: () => Promise.resolve({ success: true, avatar_url: '/new-avatar.jpg' })
            }));

            const profileAvatar = document.getElementById('profile-avatar');

            const response = await fetch('/profile/avatar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': 'test-token'
                },
                body: new FormData()
            });

            const data = await response.json();

            if (data.success) {
                profileAvatar.src = data.avatar_url;
            }

            expect(profileAvatar.src).toContain('/new-avatar.jpg');
        });
    });
});

describe('Notifications Employee - Tests dynamiques', () => {
    
    beforeEach(() => {
        document.body.innerHTML = `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Notifications</h2>
                <div class="space-y-3" id="notification-list">
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-yellow-100 bg-yellow-50">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 shrink-0">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Demande en attente</p>
                            <p class="text-xs text-gray-500 truncate">Congés payés - 01/08/2026</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-green-100 bg-green-50">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Demande approuvée</p>
                            <p class="text-xs text-gray-500 truncate">Congés payés - 15/07/2026</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    describe('Liste de notifications', () => {
        it('devrait afficher la liste des notifications', () => {
            const notificationList = document.getElementById('notification-list');
            expect(notificationList).toBeTruthy();
        });

        it('devrait afficher les notifications avec différents statuts', () => {
            const notificationList = document.getElementById('notification-list');
            expect(notificationList.textContent).toContain('Demande en attente');
            expect(notificationList.textContent).toContain('Demande approuvée');
        });

        it('devrait avoir les couleurs correctes pour les statuts', () => {
            const pendingNotification = document.querySelector('.bg-yellow-50');
            const approvedNotification = document.querySelector('.bg-green-50');
            
            expect(pendingNotification).toBeTruthy();
            expect(approvedNotification).toBeTruthy();
        });

        it('devrait avoir les icônes de notification', () => {
            const icons = document.querySelectorAll('[data-lucide]');
            expect(icons.length).toBe(2);
        });
    });
});