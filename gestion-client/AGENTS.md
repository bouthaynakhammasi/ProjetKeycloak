# Configuration et Dépannage du Projet

## Correction de l'erreur "Session expirée"

L'erreur "Session expirée, veuillez réessayer" provenait d'une exception `InvalidStateException` dans le callback OAuth Keycloak. Cela arrive quand le paramètre d'état OAuth expire ou ne correspond pas entre la demande et la réponse Keycloak.

### Corrections apportées :

1. **Augmentation de la durée de vie de la session** (config/session.php)
   - Changé `SESSION_LIFETIME` de 120 à 480 minutes (8 heures)
   - Cela donne plus de temps pour compléter le flux OAuth

2. **Configuration des cookies de session** (config/session.php)
   - Changé `same_site` de 'lax' à null
   - Cela évite les problèmes de compatibilité avec les cookies OAuth dans certains navigateurs

3. **Gestion améliorée de InvalidStateException** (app/Http/Controllers/AuthController.php)
   - Ajout de `session()->flush()` pour nettoyer la session corrompue
   - Message d'erreur plus clair pour l'utilisateur
   - Ajout de vérification que la session est démarrée avant la redirection OAuth

4. **Configuration de l'URL de redirect** (config/services.php)
   - Correction de l'URL par défaut pour correspondre à APP_URL (http://localhost)

### Configuration requise dans .env :
```
APP_URL=http://localhost
SESSION_DRIVER=file
SESSION_LIFETIME=480
KEYCLOAK_REDIRECT_URI=http://localhost/auth/callback
```

### Commandes de vérification :
```bash
php artisan config:clear
php artisan cache:clear
php artisan session:table
```

## Gestion des Présences Employees

Le système de présence a été amélioré pour enregistrer automatiquement les heures d'arrivée et de départ des employés.

### Structure de la table presences :
- `employe_id` : ID de l'employé
- `date` : Date de la présence
- `heure_connexion` : Heure d'arrivée (anciennement nommé ainsi)
- `heure_depart` : Heure de départ (nouveau champ ajouté)
- `statut` : present, retard, absent
- `remarque` : Commentaire optionnel

### Comportement automatique :

**LOGIN Employee** (AuthController.php - callback method) :
- Trouve l'employé via keycloak_id
- Crée ou récupère la présence du jour
- Enregistre `heure_connexion` si elle n'existe pas déjà
- Réutilise la présence existante si l'employé se reconnecte le même jour

**LOGOUT Employee** (AuthController.php - logout method) :
- Avant de détruire la session et rediriger vers Keycloak
- Trouve la présence du jour de l'employé connecté
- Enregistre `heure_depart` = maintenant si elle n'existe pas déjà
- Ne l'écrase pas si déjà enregistrée

### Affichage :

**Page "Mes Présences"** (presences/employee.blade.php) :
- Affiche : Date | Heure d'arrivée | Heure de départ | Statut
- Format d'affichage : "12 août 2026 | 08:30 | 17:15 | Présent"

**Page Admin Présences** (presences/index.blade.php) :
- Affiche : Employé | Arrivée | Départ | Statut
- L'admin peut modifier les heures et le statut via un formulaire inline

### Migration ajoutée :
- `2026_08_12_000000_add_heure_depart_to_presences_table.php` : Ajout du champ heure_depart

### Modèle mis à jour :
- `app/Models/Presence.php` : Ajout de `heure_depart` dans fillable

## Architecture SSO Keycloak

Le projet utilise Laravel Socialite avec Keycloak pour l'authentification SSO :

- **Route de login** : `/auth/keycloak/redirect` → Redirection vers Keycloak
- **Route de callback** : `/auth/callback` → Traitement du retour Keycloak
- **Gestion des rôles** : Les rôles sont extraits du JWT Keycloak (realm_access.roles, resource_access)
- **Auto-activation** : Les utilisateurs avec des rôles valides dans Keycloak sont automatiquement activés

## Structure des rôles

- **ROLE_ADMIN** : Accès complet au dashboard admin
- **ROLE_EMPLOYEE** : Accès au dashboard employé
- **Aucun rôle** : Compte en attente de validation par l'admin

## Événements de temps réel

Le projet utilise Laravel Broadcasting pour les notifications en temps réel :
- `UserRegistered` : Nouvelle inscription en attente
- `UserValidated` : Compte validé par l'admin
- `UserRejected` : Compte rejeté par l'admin
