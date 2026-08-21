# Configuration Real-Time avec Soketi + Laravel Echo

## Architecture du système

```
Laravel Backend
    ↓
Events (UserRegistered, UserValidated, UserRejected)
    ↓
Broadcasting (Pusher driver)
    ↓
Soketi WebSocket Server (port 6001)
    ↓
Laravel Echo (Frontend JavaScript)
    ↓
Mise à jour automatique du dashboard
```

## 1. Configuration .env

Ajoutez ces variables à votre fichier `.env` :

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

## 2. Configuration Laravel

### 2.1 config/broadcasting.php
Déjà configuré pour Soketi (host: 127.0.0.1, port: 6001, scheme: http)

### 2.2 Packages installés
- `pusher/pusher-php-server` (côté backend)
- `laravel-echo` (côté frontend)
- `pusher-js` (client WebSocket)

### 2.3 Events créés

#### UserRegistered.php
- **Channel** : `admin-users`
- **Event** : `user.registered`
- **Données** : id, email, nom, prenom, statut, created_at
- **Déclenchement** : Quand un utilisateur s'inscrit sans rôle valide

#### UserValidated.php
- **Channel** : `admin-users`
- **Event** : `user.validated`
- **Données** : id, keycloak_id, email, name, role, status, activated_at
- **Déclenchement** : Quand l'admin valide un compte

#### UserRejected.php
- **Channel** : `admin-users`
- **Event** : `user.rejected`
- **Données** : id, keycloak_id, email, name, status
- **Déclenchement** : Quand l'admin rejette un compte

### 2.4 Controllers modifiés

#### AuthController.php
- Déclenche `UserRegistered` lors de l'inscription d'un utilisateur sans rôle

#### UserManagementController.php
- Déclenche `UserValidated` après validation d'un compte
- Déclenche `UserRejected` après rejet d'un compte

## 3. Configuration Frontend

### 3.1 resources/js/bootstrap.js
Laravel Echo est configuré pour se connecter à Soketi :
```javascript
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY ?? 'app-key',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? '127.0.0.1',
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### 3.2 resources/views/admin/users/index.blade.php
JavaScript pour écouter les events et mettre à jour le tableau sans reload :

- **UserRegistered** : Ajoute automatiquement la ligne dans le tableau "Comptes en attente"
- **UserValidated** : Supprime de "Comptes en attente" et ajoute dans "Comptes actifs"
- **UserRejected** : Supprime de "Comptes en attente" et ajoute dans "Comptes rejetés"
- **updateCounts()** : Met à jour les compteurs en temps réel

## 4. Soketi - Serveur WebSocket

### 4.1 Démarrage via Docker (recommandé)

```bash
docker run -p 6001:6001 -p 6002:6002 quay.io/soketi/soketi:latest
```

### 4.2 Démarrage via npm

```bash
npm install -g @soketi/soketi
soketi start
```

### 4.3 Vérification du serveur WebSocket

Ouvrez votre navigateur sur : `http://127.0.0.1:6001`

Vous devriez voir une page Soketi avec les informations de connexion.

### 4.4 Dashboard Soketi

Le dashboard est accessible sur : `http://127.0.0.1:6002`

Vous pouvez y voir :
- Les channels actifs
- Les connexions en temps réel
- Les messages broadcastés

## 5. Compilation des assets

```bash
npm run build
```

## 6. Étapes de test complètes

### 6.1 Préparation

1. **Démarrer Soketi** :
   ```bash
   docker run -p 6001:6001 -p 6002:6002 quay.io/soketi/soketi:latest
   ```

2. **Configurer .env** :
   ```env
   BROADCAST_DRIVER=pusher
   PUSHER_HOST=127.0.0.1
   PUSHER_PORT=6001
   PUSHER_SCHEME=http
   ```

3. **Clear cache** :
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Compiler les assets** :
   ```bash
   npm run build
   ```

5. **Démarrer Laravel** :
   ```bash
   php artisan serve
   ```

### 6.2 Test UserRegistered

1. Ouvrez le dashboard admin sur `http://localhost:8000/admin/users`
2. Ouvrez un second navigateur sur la page d'inscription Keycloak
3. Inscrivez un nouvel utilisateur
4. **Résultat attendu** : L'utilisateur apparaît automatiquement dans "Comptes en attente" sans refresh

### 6.3 Test UserValidated

1. Cliquez sur "Valider" pour un compte en attente
2. Choisissez un rôle (Employé, Admin, Client)
3. **Résultat attendu** :
   - L'utilisateur disparaît de "Comptes en attente"
   - L'utilisateur apparaît dans "Comptes actifs"
   - Les compteurs se mettent à jour
   - Tout sans refresh de page

### 6.4 Test UserRejected

1. Cliquez sur "Refuser" pour un compte en attente
2. **Résultat attendu** :
   - L'utilisateur disparaît de "Comptes en attente"
   - L'utilisateur apparaît dans "Comptes rejetés"
   - Les compteurs se mettent à jour
   - Tout sans refresh de page

### 6.5 Test multi-dashboard

1. Ouvrez 2 ou 3 navigateurs sur le dashboard admin
2. Effectuez une action (validation/rejet) dans un navigateur
3. **Résultat attendu** : Tous les dashboards se mettent à jour simultanément

## 7. Logs de debugging

Les events sont loggés dans `storage/logs/laravel.log` :
```
UserRegistered event created
UserValidated event created
UserRejected event created
```

Console du navigateur (F12) :
```
New user registered: {id: 1, email: "...", ...}
User validated: {id: 1, email: "...", ...}
User rejected: {id: 1, email: "...", ...}
```

## 8. Dépannage

### Les événements ne s'affichent pas en temps réel

1. Vérifiez que Soketi est démarré :
   ```bash
   docker ps
   ```

2. Vérifiez que `BROADCAST_DRIVER=pusher` dans `.env`

3. Clear cache :
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

4. Ouvrez la console du navigateur pour voir les logs Echo

5. Vérifiez que les assets sont compilés : `npm run build`

### Erreur de connexion WebSocket

1. Vérifiez que `PUSHER_HOST` et `PUSHER_PORT` sont corrects (127.0.0.1:6001)

2. Vérifiez qu'aucun firewall ne bloque le port 6001

3. Vérifiez la configuration dans `config/broadcasting.php`

4. Testez la connexion Soketi : `http://127.0.0.1:6001`

### Mode log pour développement

Si vous n'avez pas de serveur WebSocket, utilisez `BROADCAST_DRIVER=log` pour voir les événements dans les logs Laravel sans WebSocket réel.

## 9. Résumé du flow

```
User Register
    ↓
AuthController callback
    ↓
UserRegistered event broadcast
    ↓
Soketi WebSocket
    ↓
Laravel Echo (admin dashboard)
    ↓
addPendingUserToTable() → Mise à jour UI sans reload

Admin Validate
    ↓
UserManagementController validateAndCreate
    ↓
UserValidated event broadcast
    ↓
Soketi WebSocket
    ↓
Laravel Echo (admin dashboard)
    ↓
removePendingUserFromTable() + addActiveUserToTable() → Mise à jour UI sans reload

Admin Reject
    ↓
UserManagementController reject
    ↓
UserRejected event broadcast
    ↓
Soketi WebSocket
    ↓
Laravel Echo (admin dashboard)
    ↓
removePendingUserFromTable() + addRejectedUserToTable() → Mise à jour UI sans reload
```
