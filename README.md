# Plateforme de gestion des ressources humaines

Plateforme complète de gestion des ressources humaines composée de deux applications Laravel distinctes avec authentification SSO Keycloak.

## Architecture

```
Projet-SSO
├── gestion-client/    # Application principale de gestion RH
└── mailbox/          # Système de messagerie interne
```

### Applications

#### 1. Gestion Client
Application principale de gestion des ressources humaines avec :

- **Authentification SSO** : Intégration Keycloak avec OAuth 2.0 / OpenID Connect
- **Gestion des employés** : CRUD complet avec profils détaillés
- **Gestion des absences** : Demandes, validation, rejet par les administrateurs
- **Gestion des présences** : Suivi automatique des heures d'arrivée et de départ
- **Gestion des salaires** : Calcul automatique avec primes et retenues
- **Gestion des documents** : Stockage et organisation des documents employés
- **Dashboard Admin** : Vue d'ensemble pour les administrateurs
- **Dashboard Employee** : Interface personnalisée pour les employés
- **Notifications temps réel** : Système de notifications avec Laravel Broadcasting et Pusher
- **Événements** : UserRegistered, UserValidated, UserRejected, SalaireValidated

#### 2. Mailbox
Système de messagerie interne avec :

- **Boîte de réception** : Messages reçus avec gestion de lecture
- **Messages envoyés** : Historique des messages envoyés
- **Brouillons** : Gestion des messages en cours de rédaction
- **Corbeille** : Gestion des messages supprimés
- **Recherche** : Recherche de messages
- **Favoris** : Marquage des messages importants
- **Réponse/Transfert** : Fonctionnalités de réponse et transfert
- **Communication interne** : Messagerie entre utilisateurs
- **Intégration SSO** : Authentification Keycloak unifiée

## Technologies

### Backend
- **PHP** 8.1+
- **Laravel** 10.10
- **PostgreSQL** (gestion-client)
- **SQLite** (mailbox)
- **PHPUnit** 10.1 (tests backend)
- **Laravel Socialite** (OAuth Keycloak)
- **SocialiteProviders/Keycloak** (intégration Keycloak)
- **Laravel Broadcasting** (notifications temps réel)
- **Pusher** (WebSockets)

### Frontend
- **JavaScript** (ES6+)
- **Jasmine** 5.5.0 (tests unitaires frontend)
- **Playwright** 1.62.1 (tests E2E)
- **Vite** 5.0.0 (build frontend)
- **Bootstrap** (framework CSS)

### Authentification
- **Keycloak** (SSO / OAuth 2.0 / OpenID Connect)
- **Laravel Sanctum** (API authentication)
- **Gestion des rôles** : ROLE_ADMIN, ROLE_EMPLOYEE

## Authentification

### Intégration Keycloak
- **Single Sign-On (SSO)** : Authentification centralisée via Keycloak
- **OAuth 2.0 / OpenID Connect** : Protocoles standard de sécurité
- **Gestion des rôles** : Les rôles sont extraits du JWT Keycloak
- **Auto-activation** : Les utilisateurs avec des rôles valides sont automatiquement activés
- **Session sécurisée** : Configuration avancée des sessions pour éviter les expirations

### Flux d'authentification
1. Redirection vers Keycloak (`/auth/keycloak/redirect`)
2. Authentification utilisateur sur Keycloak
3. Callback avec token JWT (`/auth/callback`)
4. Extraction des rôles et création/mise à jour de l'utilisateur
5. Redirection vers le dashboard approprié

## Tests

### Tests Backend (PHPUnit)
- **gestion-client** : 2 tests passing
- **mailbox** : 30 tests passing (Unit), 17 tests Feature (configuration APP_KEY requise)

### Tests Frontend (Jasmine)
- **gestion-client** : 305 specs passing, 0 failures

### Tests E2E (Playwright)
- **gestion-client** : Suite complète de tests E2E pour :
  - Authentification SSO
  - Gestion des employés
  - Gestion des absences
  - Gestion des salaires
  - WebSockets et notifications

## Installation

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- Node.js et npm
- PostgreSQL (pour gestion-client)
- Keycloak instance
- Pusher (pour les notifications temps réel)

### 1. Cloner le repository
```bash
git clone https://github.com/bouthaynakhammasi/ProjetKeycloak.git
cd Projet-SSO
```

### 2. Installation de gestion-client
```bash
cd gestion-client
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 3. Configuration gestion-client (.env)
```bash
APP_NAME="Gestion Client"
APP_URL=http://localhost:8000

# Base de données PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gestion_client
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Configuration Keycloak
KEYCLOAK_CLIENT_ID=gestion-client
KEYCLOAK_CLIENT_SECRET=your_keycloak_client_secret
KEYCLOAK_REDIRECT_URI=http://localhost:8000/auth/callback
KEYCLOAK_REALM=CompanyRealm
KEYCLOAK_BASE_URL=http://localhost:8080
KEYCLOAK_SCOPES=openid,profile,email

# Configuration Pusher (notifications temps réel)
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
BROADCAST_DRIVER=pusher
```

### 4. Migration de la base de données gestion-client
```bash
php artisan migrate
php artisan db:seed
```

### 5. Installation de mailbox
```bash
cd ../mailbox
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 6. Configuration mailbox (.env)
```bash
APP_NAME="Mailbox"
APP_URL=http://localhost:8001

# Base de données SQLite
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Configuration Keycloak
KEYCLOAK_CLIENT_ID=mailbox
KEYCLOAK_CLIENT_SECRET=your_keycloak_client_secret
KEYCLOAK_REDIRECT_URI=http://localhost:8001/auth/callback
KEYCLOAK_REALM=CompanyRealm
KEYCLOAK_BASE_URL=http://localhost:8080
KEYCLOAK_SCOPES=openid,profile,email
```

### 7. Migration de la base de données mailbox
```bash
php artisan migrate
php artisan db:seed
```

### 8. Lancement des applications

**Terminal 1 - gestion-client :**
```bash
cd gestion-client
php artisan serve
npm run dev
```

**Terminal 2 - mailbox :**
```bash
cd mailbox
php artisan serve --port=8001
npm run dev
```

**Terminal 3 - Pusher (pour les notifications temps réel) :**
```bash
cd gestion-client
npx pusher-cli start
```

### 9. Configuration Keycloak

Créer deux clients Keycloak :

**Client gestion-client :**
- Client ID: `gestion-client`
- Client Secret: `your_keycloak_client_secret`
- Valid Redirect URIs: `http://localhost:8000/auth/callback`
- Enabled: true

**Client mailbox :**
- Client ID: `mailbox`
- Client Secret: `your_keycloak_client_secret`
- Valid Redirect URIs: `http://localhost:8001/auth/callback`
- Enabled: true

Créer les rôles dans le realm :
- `ROLE_ADMIN`
- `ROLE_EMPLOYEE`

## Tests

### Lancer les tests backend PHPUnit
```bash
# gestion-client
cd gestion-client
php artisan test

# mailbox
cd mailbox
php artisan test
```

### Lancer les tests frontend Jasmine
```bash
cd gestion-client
npm test
```

### Lancer les tests E2E Playwright
```bash
cd gestion-client
npx playwright test
```

## Structure des rôles

- **ROLE_ADMIN** : Accès complet au dashboard admin, gestion des employés, validation des absences, gestion des salaires
- **ROLE_EMPLOYEE** : Accès au dashboard employé, demande d'absences, consultation des présences, visualisation des salaires
- **Aucun rôle** : Compte en attente de validation par l'administrateur

## Sécurité

- **Authentification SSO** : Centralisée via Keycloak
- **Gestion des sessions** : Configuration avancée pour éviter les expirations
- **Protection CSRF** : Activée par défaut dans Laravel
- **Validation des rôles** : Middleware de vérification des rôles Keycloak
- **Isolation des données** : Chaque utilisateur ne voit que ses propres données (mailbox)

## Configuration des sessions

Pour éviter les erreurs de session expirée lors du flux OAuth :

- `SESSION_LIFETIME=480` (8 heures)
- `SESSION_DRIVER=file`
- `SESSION_SAME_SITE=lax`

## Support

Pour toute question ou problème, veuillez contacter l'équipe de développement.

## Licence

MIT License