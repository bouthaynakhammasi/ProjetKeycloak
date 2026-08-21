# Module Gestion des Salaires - Guide Complet

## Architecture du Module

### Base de Données
- **Table `salaires`** : Stockage des salaires mensuels
- **Table `primes`** : Stockage des primes (rendement, transport, etc.)
- **Table `retenues`** : Stockage des retenues (absence, retard, etc.)

### Relations
```
Employe (1) ---- (*) Salaire
Employe (1) ---- (*) Prime
Employe (1) ---- (*) Retenue
```

### Calcul Automatique
```
salaire_net = salaire_base + prime - retenue
```

---

## 1. Migrations

### Exécuter les migrations
```bash
php artisan migrate
```

### Tables créées
- `salaires` : id, employe_id, mois, annee, salaire_base, prime, retenue, salaire_net, statut_paiement, date_paiement, notes
- `primes` : id, employe_id, type_prime, montant, description, date
- `retenues` : id, employe_id, type_retenue, montant, description, date

---

## 2. Models

### Salaire.php
- Relations : `employe()`
- Méthodes : `calculerSalaireNet()`
- Scopes : `periode()`, `statut()`, `payes()`, `enAttente()`
- Accesseur : `nom_mois`

### Prime.php
- Relations : `employe()`
- Scopes : `type()`, `periode()`

### Retenue.php
- Relations : `employe()`
- Scopes : `type()`, `periode()`

### Employe.php (mis à jour)
- Relations : `salaires()`, `primes()`, `retenues()`
- Accesseur : `nom_complet`

---

## 3. Controllers

### SalaireController.php
- **index()** : Liste des salaires avec filtres (employé, mois, année, statut)
  - ROLE_ADMIN : voit tous les salaires
  - ROLE_EMPLOYEE : voit uniquement ses salaires
- **create()** : Formulaire de création (admin uniquement)
- **store()** : Création d'un salaire avec calcul automatique
- **show()** : Détails d'un salaire
- **edit()** : Formulaire de modification (admin uniquement)
- **update()** : Modification avec recalcul
- **destroy()** : Suppression (admin uniquement)
- **marquerPaye()** : Marquer comme payé + broadcast WebSocket
- **generatePDF()** : Génération PDF de la fiche de paie

### PrimeController.php
- CRUD complet (admin uniquement)
- Filtrage par employé, type, période

### RetenueController.php
- CRUD complet (admin uniquement)
- Filtrage par employé, type, période

### SalaireDashboardController.php
- **index()** : Statistiques de paie
  - Masse salariale totale
  - Nombre d'employés payés
  - Salaires en attente
  - Salaire moyen
  - Statistiques du mois en cours
  - Derniers salaires
  - Top 5 salaires
  - Primes récentes
  - Retenues récentes
  - Statistiques par département

---

## 4. Routes

### Routes salaires (ROLE_ADMIN + ROLE_EMPLOYEE)
```php
Route::resource('salaires', SalaireController::class);
Route::post('/salaires/{salaire}/marquer-paye', [SalaireController::class, 'marquerPaye'])
    ->name('salaires.marquer-paye');
Route::get('/salaires/{salaire}/pdf', [SalaireController::class, 'generatePDF'])
    ->name('salaires.pdf');
```

### Routes dashboard (ROLE_ADMIN uniquement)
```php
Route::get('/salaires/dashboard', [SalaireDashboardController::class, 'index'])
    ->name('salaires.dashboard');
```

### Routes primes et retenues (ROLE_ADMIN uniquement)
```php
Route::resource('primes', PrimeController::class);
Route::resource('retenues', RetenueController::class);
```

---

## 5. Vues Blade

### salaires/index.blade.php
- Liste des salaires avec filtres
- Tableau avec : employé, mois/année, salaire base, prime, retenue, salaire net, statut, actions
- Actions : Voir, Modifier, Supprimer, Marquer payé, PDF

### salaires/create.blade.php
- Formulaire de création
- Sélection employé
- Saisie : mois, année, salaire base, prime, retenue, notes

### salaires/edit.blade.php
- Formulaire de modification
- Champs : employé, mois, année, salaire base, prime, retenue, statut, date paiement, notes

### salaires/show.blade.php
- Détails complets du salaire
- Informations employé
- Détails période
- Calcul détaillé
- Bouton PDF

### salaires/pdf.blade.php
- Template PDF pour fiche de paie
- Design professionnel
- Informations employé, période, détails salaire

### salaires/dashboard.blade.php
- Dashboard admin avec statistiques
- Cartes : masse salariale, employés payés, en attente, salaire moyen
- Tableaux : derniers salaires, top salaires, primes, retenues
- Graphiques par département

---

## 6. Système de Calcul Salaire Net

### Méthode calculerSalaireNet()
```php
public function calculerSalaireNet()
{
    $this->salaire_net = $this->salaire_base + $this->prime - $this->retenue;
    $this->save();
}
```

### Déclenchement automatique
- Après création (store)
- Après modification (update)

---

## 7. Génération PDF

### Installation DomPDF
```bash
composer require dompdf/dompdf
```

### Méthode generatePDF()
- Permissions : ROLE_ADMIN (tous) / ROLE_EMPLOYEE (ses propres)
- Template : salaires/pdf.blade.php
- Format : A4 portrait
- Nom fichier : fiche_paie_{nom}_{mois}_{annee}.pdf

### Contenu PDF
- En-tête : FICHE DE PAIE
- Informations employé : nom, email, poste, département
- Période : mois, année, statut, date paiement
- Détails salaire : base, primes, retenues, net
- Notes (si présentes)
- Pied de page : informations légales

---

## 8. Permissions Keycloak

### ROLE_ADMIN
- ✅ Créer, modifier, supprimer salaires
- ✅ Voir tous les salaires
- ✅ Gérer primes et retenues
- ✅ Accéder dashboard statistiques
- ✅ Marquer salaires comme payés
- ✅ Générer PDFs de tous les employés

### ROLE_EMPLOYEE
- ✅ Voir uniquement ses propres salaires
- ✅ Télécharger sa fiche de paie PDF
- ❌ Créer, modifier, supprimer salaires
- ❌ Voir salaires des autres employés
- ❌ Accéder dashboard statistiques
- ❌ Gérer primes et retenues

### Implémentation
```php
// Dans SalaireController
private function authorizeAdmin()
{
    if (session('user_role') !== 'ROLE_ADMIN') {
        abort(403, 'Accès non autorisé');
    }
}

// Vérification ROLE_EMPLOYEE
if ($userRole === 'ROLE_EMPLOYEE') {
    $employe = Employe::where('keycloak_id', session('user_id'))->first();
    if (!$employe || $salaire->employe_id !== $employe->id) {
        return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
    }
}
```

---

## 9. Events WebSocket

### SalaireValidated Event
- **Channel** : `salaries` (public) + `employee.{keycloak_id}` (private)
- **Event** : `salaire.validated`
- **Déclenchement** : Quand admin marque un salaire comme payé

### Données broadcastées
```php
[
    'salaire_id' => $salaire->id,
    'employe_id' => $salaire->employe_id,
    'employe_nom' => $employe->nom_complet,
    'employe_email' => $employe->email,
    'mois' => $salaire->mois,
    'nom_mois' => $salaire->nom_mois,
    'annee' => $salaire->annee,
    'salaire_net' => $salaire->salaire_net,
    'statut_paiement' => $salaire->statut_paiement,
    'date_paiement' => $salaire->date_paiement->format('d/m/Y'),
]
```

### Notification Employee
```
Admin valide salaire
        ↓
Broadcast SalaireValidated
        ↓
Employee reçoit notification :
"Votre fiche de paie du mois X est disponible"
```

---

## 10. Étapes de Test

### Test 1 : Installation et Migration
```bash
# Installer DomPDF pour la génération PDF
composer require dompdf/dompdf

# Exécuter les migrations
php artisan migrate

# Vérifier les tables créées
php artisan tinker
>>> Schema::getColumnListing('salaires')
>>> Schema::getColumnListing('primes')
>>> Schema::getColumnListing('retenues')
```

### Test 2 : Création d'un salaire (Admin)
1. Connectez-vous en tant qu'admin
2. Allez sur `/salaires/create`
3. Sélectionnez un employé
4. Remplissez :
   - Mois : 1
   - Année : 2024
   - Salaire base : 1500
   - Prime : 200
   - Retenue : 50
5. Cliquez sur "Enregistrer"
6. **Résultat attendu** : Salaire créé avec salaire_net = 1650 (1500 + 200 - 50)

### Test 3 : Liste et filtres (Admin)
1. Allez sur `/salaires`
2. Vérifiez que le salaire apparaît
3. Testez les filtres :
   - Filtrer par employé
   - Filtrer par mois
   - Filtrer par année
   - Filtrer par statut (en_attente)
4. **Résultat attendu** : Filtres fonctionnent correctement

### Test 4 : Modification d'un salaire (Admin)
1. Cliquez sur "Modifier" sur un salaire
2. Changez le salaire base : 1600
3. Cliquez sur "Mettre à jour"
4. **Résultat attendu** : Salaire_net recalculé automatiquement (1600 + 200 - 50 = 1750)

### Test 5 : Marquer comme payé (Admin)
1. Cliquez sur "Marquer payé"
2. **Résultat attendu** :
   - Statut passe à "paye"
   - Date paiement définie
   - Event WebSocket broadcasté
   - Notification envoyée à l'employé

### Test 6 : Génération PDF (Admin)
1. Cliquez sur "PDF" sur un salaire
2. **Résultat attendu** : PDF téléchargé avec fiche de paie complète

### Test 7 : Permissions Employee
1. Connectez-vous en tant qu'employé
2. Allez sur `/salaires`
3. **Résultat attendu** : Uniquement vos propres salaires
4. Essayez d'accéder à `/salaires/create`
5. **Résultat attendu** : Accès refusé (403)
6. Cliquez sur "PDF" sur votre salaire
7. **Résultat attendu** : PDF téléchargé

### Test 8 : Dashboard Statistiques (Admin)
1. Allez sur `/salaires/dashboard`
2. **Résultat attendu** :
   - Masse salariale totale
   - Nombre d'employés payés
   - Salaires en attente
   - Salaire moyen
   - Statistiques du mois en cours
   - Derniers salaires
   - Top 5 salaires

### Test 9 : Gestion des Primes (Admin)
1. Allez sur `/primes/create`
2. Créez une prime :
   - Employé : sélectionné
   - Type prime : rendement
   - Montant : 100
   - Description : Prime performance
3. **Résultat attendu** : Prime créée et visible dans la liste

### Test 10 : Gestion des Retenues (Admin)
1. Allez sur `/retenues/create`
2. Créez une retenue :
   - Employé : sélectionné
   - Type retenue : absence
   - Montant : 50
   - Description : Absence non justifiée
3. **Résultat attendu** : Retenue créée et visible dans la liste

### Test 11 : Historique des salaires
1. Créez plusieurs salaires pour le même employé sur différents mois
2. Allez sur `/salaires` et filtrez par cet employé
3. **Résultat attendu** : Historique chronologique des salaires

### Test 12 : WebSocket Notification
1. Assurez-vous que Soketi est démarré
2. Connectez-vous en tant qu'employé
3. Admin marque un salaire comme payé
4. **Résultat attendu** : Notification temps réel reçue par l'employé

### Test 13 : Unicité des salaires
1. Essayez de créer un salaire pour le même employé/mois/année
2. **Résultat attendu** : Erreur "Un salaire existe déjà pour cet employé pour cette période"

### Test 14 : Suppression (Admin)
1. Cliquez sur "Supprimer" sur un salaire
2. Confirmez
3. **Résultat attendu** : Salaire supprimé avec succès

---

## 11. Dépannage

### Erreur : "Table 'salaires' doesn't exist"
```bash
php artisan migrate
```

### Erreur : "Class 'Dompdf\Dompdf' not found"
```bash
composer require dompdf/dompdf
```

### Erreur : "Accès non autorisé"
- Vérifiez que vous êtes connecté avec le bon rôle Keycloak
- Vérifiez que le middleware est correctement configuré

### Erreur : "Salaire net incorrect"
- Vérifiez que la méthode `calculerSalaireNet()` est appelée après création/modification

### WebSocket ne fonctionne pas
- Vérifiez que Soketi est démarré
- Vérifiez que `BROADCAST_DRIVER=pusher` dans `.env`
- Vérifiez la configuration Laravel Echo

---

## 12. Résumé de l'implémentation

### Fonctionnalités implémentées
✅ CRUD complet pour les salaires
✅ Gestion des primes
✅ Gestion des retenues
✅ Calcul automatique du salaire net
✅ Historique des salaires par employé
✅ Génération PDF des fiches de paie
✅ Dashboard admin avec statistiques
✅ Permissions Keycloak (Admin/Employee)
✅ Events WebSocket pour notifications temps réel
✅ Filtrage par employé, mois, année, statut
✅ Validation d'unicité (employé/mois/année)

### Technologies utilisées
- Laravel 10
- PostgreSQL
- Blade + Bootstrap
- Keycloak (authentification et rôles)
- Laravel Broadcasting
- Soketi (WebSocket)
- DomPDF (génération PDF)

### Sécurité
- Middleware Keycloak auth
- Vérification des rôles
- Protection CSRF
- Validation des données
- Autorisation basée sur les rôles

---

## 13. Prochaines étapes (optionnelles)

- [ ] Ajouter des graphiques Chart.js dans le dashboard
- [ ] Implémenter l'export Excel des salaires
- [ ] Ajouter des notifications email automatiques
- [ ] Créer un système d'approbation des salaires
- [ ] Ajouter la gestion des heures supplémentaires
- [ ] Implémenter les congés et absences
- [ ] Créer des rapports personnalisés
