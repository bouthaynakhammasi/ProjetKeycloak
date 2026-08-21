# Configuration de l'envoi d'emails - Mailbox

## Nouvelle fonctionnalité : Emails externes

Le système de mailbox supporte maintenant l'envoi de messages à des **adresses email externes** (utilisateurs non enregistrés) en plus des utilisateurs internes.

### Comportement :

- **Utilisateurs enregistrés** : Le message apparaît dans leur boîte de réception interne + email de notification
- **Utilisateurs externes** : Uniquement email de notification (pas de boîte de réception interne)

## Configuration requise dans .env

Ajoutez ou modifiez les lignes suivantes dans votre fichier `.env` :

```env
# Configuration Mail (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-d-application
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="Mailbox"

# Configuration Queue
QUEUE_CONNECTION=sync
```

## Étapes de configuration Gmail

1. **Activer l'authentification à deux facteurs** sur votre compte Google
2. **Générer un mot de passe d'application** :
   - Allez dans Google Account > Sécurité
   - Cherchez "Mot de passe d'application"
   - Sélectionnez "Mail" et "Autre (nom personnalisé)"
   - Copiez le mot de passe généré (16 caractères)
3. **Utilisez ce mot de passe** pour `MAIL_PASSWORD` dans votre .env

## Mode Queue

### Pour le développement (recommandé) :
```env
QUEUE_CONNECTION=sync
```
Les emails sont envoyés immédiatement pendant la requête HTTP.

### Pour la production :
```env
QUEUE_CONNECTION=database
```
Vous devrez créer la table des jobs et lancer un worker :
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## Test de l'envoi d'emails

### Test avec utilisateur enregistré :
```bash
php artisan tinker
```

```php
$recipient = App\Models\KeycloakUser::first();
$message = App\Models\Message::create([
    'sender_id' => 'test-sender',
    'keycloak_user_id' => $recipient->keycloak_id,
    'recipient_id' => $recipient->keycloak_id,
    'recipient_email' => $recipient->email,
    'sender_name' => 'Test Sender',
    'sender_email' => 'test@example.com',
    'subject' => 'Test Email',
    'body' => 'Ceci est un test d\'envoi d\'email.',
    'folder' => 'sent',
    'is_read' => true,
    'is_starred' => false,
]);

$messageUrl = route('mailbox.show', $message->id);
\Mail::to($recipient->email)->send(new \App\Mail\NewMessageMail($message, 'Test Sender', $messageUrl));
```

### Test avec email externe :
```php
$message = App\Models\Message::create([
    'sender_id' => 'test-sender',
    'keycloak_user_id' => 'test-sender',
    'recipient_id' => null,
    'recipient_email' => 'external@example.com',
    'sender_name' => 'Test Sender',
    'sender_email' => 'test@example.com',
    'subject' => 'Test External Email',
    'body' => 'Ceci est un test vers un email externe.',
    'folder' => 'sent',
    'is_read' => true,
    'is_starred' => false,
]);

$messageUrl = route('mailbox.show', $message->id);
\Mail::to('external@example.com')->send(new \App\Mail\NewMessageMail($message, 'Test Sender', $messageUrl));
```

## Dépannage

### Email non reçu :
- Vérifiez les logs Laravel : `storage/logs/laravel.log`
- Vérifiez que les identifiants SMTP sont corrects
- Essayez avec `QUEUE_CONNECTION=sync` pour voir les erreurs immédiatement

### Erreur d'authentification SMTP :
- Vérifiez que vous utilisez un mot de passe d'application Gmail
- Vérifiez que l'authentification 2FA est activée sur le compte Google

### Erreur "Could not open socket" :
- Vérifiez que le pare-feu n'est pas bloquant le port 587
- Vérifiez que MAIL_HOST et MAIL_PORT sont corrects