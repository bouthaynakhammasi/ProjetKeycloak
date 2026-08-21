# Flow SSO - Spécifications Fonctionnelles

## Vue d'ensemble
Le système utilise l'authentification unique (SSO) via Keycloak pour gérer l'accès à l'application. Les utilisateurs s'authentifient une seule fois via Keycloak et accèdent ensuite à l'application sans avoir à se reconnecter.

## Acteurs
- **Utilisateur** : Employé ou administrateur souhaitant accéder à l'application
- **Keycloak** : Serveur d'authentification centralisé
- **Application** : Système de gestion RH (bellent HR)

## Flux d'Authentification

### 1. Initialisation de la connexion
- L'utilisateur clique sur le bouton de connexion
- L'application redirige l'utilisateur vers la page de connexion Keycloak
- L'application transmet l'URL de retour (callback) à Keycloak

### 2. Authentification Keycloak
- Keycloak affiche le formulaire de connexion
- L'utilisateur saisit ses identifiants (email et mot de passe)
- Keycloak vérifie les identifiants
- Si valides, Keycloak génère un jeton d'authentification

### 3. Retour vers l'application (Callback)
- Keycloak redirige l'utilisateur vers l'application avec le jeton
- L'application reçoit le jeton et les informations utilisateur
- L'application extrait les informations suivantes :
  - Identifiant unique Keycloak
  - Email de l'utilisateur
  - Nom complet
  - Rôles attribués (Admin, Employé)

### 4. Vérification des rôles
- L'application vérifie les rôles de l'utilisateur dans Keycloak
- Si l'utilisateur a le rôle "Admin" : accès immédiat autorisé
- Si l'utilisateur a le rôle "Employé" : vérification du statut local
- Si aucun rôle détecté : compte en attente de validation

### 5. Synchronisation utilisateur
- L'application recherche l'utilisateur dans la base locale par email
- Si l'utilisateur existe :
  - Mise à jour de l'identifiant Keycloak si manquant
  - Mise à jour des informations utilisateur (nom)
- Si l'utilisateur n'existe pas et a le rôle Employé :
  - Création automatique du profil employé
  - Attribution des soldes par défaut (25 jours de congés payés)
- Si l'utilisateur n'existe pas et n'a pas de rôle :
  - Création d'une demande de compte en attente
  - Notification envoyée à l'administrateur

### 6. Création de session
- L'application crée une session utilisateur locale contenant :
  - Identifiant utilisateur
  - Nom complet
  - Email
  - Rôle (Admin ou Employé)
  - Jeton d'authentification
- La session est stockée côté serveur

### 7. Accès à l'application
- L'utilisateur est redirigé vers le tableau de bord
- L'accès aux fonctionnalités est basé sur le rôle :
  - **Admin** : Accès complet à la gestion des employés, absences, etc.
  - **Employé** : Accès limité à ses propres données (absences, soldes)

## Gestion des comptes en attente
- Les utilisateurs sans rôle détecté sont placés en attente
- L'administrateur reçoit une notification
- L'administrateur peut :
  - Approuver le compte (attribution d'un rôle)
  - Rejeter le compte
- Les utilisateurs en attente ne peuvent pas accéder à l'application

## Déconnexion
- L'utilisateur clique sur le bouton de déconnexion
- L'application détruit la session locale
- L'application redirige vers Keycloak pour la déconnexion SSO
- Keycloak déconnecte l'utilisateur de toutes les applications connectées
- Redirection vers la page de connexion

## Sécurité
- Les mots de passe ne sont jamais stockés dans l'application
- L'authentification est entièrement gérée par Keycloak
- Les sessions expirent automatiquement après une période d'inactivité
- Les jetons sont validés à chaque requête sensible

## Rôles et Permissions

### ROLE_ADMIN
- Gestion complète des employés
- Validation des comptes en attente
- Approbation/refus des demandes d'absence
- Accès à tous les tableaux de bord et rapports

### ROLE_EMPLOYEE
- Consultation de ses propres absences
- Création de demandes d'absence
- Consultation de ses soldes de congés
- Accès limité à ses informations personnelles

## Scénarios d'erreur

### Identifiants incorrects
- Keycloak affiche une erreur
- L'utilisateur reste sur la page de connexion

### Compte en attente
- L'utilisateur est redirigé vers une page d'information
- Message indiquant que le compte est en attente de validation

### Compte rejeté
- L'utilisateur est redirigé vers une page d'erreur
- Message indiquant de contacter l'administrateur

### Session expirée
- L'utilisateur est redirigé vers la page de connexion
- Message indiquant que la session a expiré
