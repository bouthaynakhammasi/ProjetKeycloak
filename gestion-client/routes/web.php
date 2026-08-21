<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestAuthController;
use App\Models\Employe;
use App\Models\Presence;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Page d'accueil publique (site vitrine)
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'login'])->name('login');

// Test login endpoint for E2E testing (only in testing/local environment)
Route::post('/test-login', [TestAuthController::class, 'testLogin'])
    ->name('test.login')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Action : redirection vers Keycloak SSO
Route::get('/auth/keycloak/redirect', [AuthController::class, 'login'])->name('keycloak.redirect');

// Callback Keycloak après authentification
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('keycloak.callback');

// Déconnexion
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/pending', function () {
    return view('pending');
})->name('pending');

Route::get('/dashboard', function () {
    if (!session()->has('user_id')) {
        return redirect('/login');
    }

    $role = session('user_role');

    $comptesEnAttenteCount = DB::table('comptes_en_attente')
        ->where('statut', 'en_attente')
        ->count();

    if ($role === 'ROLE_ADMIN') {
        // Charger les absences en attente pour le dashboard admin
        $pendingAbsences = \App\Models\Absence::with('employe')
            ->where('statut', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Charger les absences d'aujourd'hui pour la liste des absences
        $today = \Carbon\Carbon::today();
        $todayAbsences = \App\Models\Absence::with('employe')
            ->where('statut', 'approved')
            ->where('date_debut', '<=', $today)
            ->where('date_fin', '>=', $today)
            ->orderBy('date_debut', 'desc')
            ->get();

        return view('dashboard.admin', compact('comptesEnAttenteCount', 'pendingAbsences', 'todayAbsences'));
    } elseif ($role === 'ROLE_EMPLOYEE') {
        // Get the logged-in employee
        $employee = Employe::where('keycloak_id', session('user_id'))->first();
        
        if (!$employee) {
            return redirect()->route('pending')->with('error', 'Employé non trouvé');
        }
        
        // Fetch employee's absence requests
        $myAbsences = \App\Models\Absence::where('employe_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Fetch today's presence data for time tracking
        $today = \Carbon\Carbon::today();
        $todayPresence = Presence::where('employe_id', $employee->id)
            ->where('date', $today)
            ->first();
        
        return view('dashboard.employee', compact('employee', 'myAbsences', 'todayPresence'));
    }

    return redirect()->route('pending');
})->name('dashboard');


Route::middleware(['role:ROLE_ADMIN,ROLE_EMPLOYEE'])->group(function () {
    Route::get('/employees', [App\Http\Controllers\EmployeController::class, 'index'])
        ->name('employees.index');
    Route::get('/employees/{userId}', [App\Http\Controllers\ProfileController::class, 'show'])
        ->name('employees.profile.show');
    Route::post('/employees/{userId}/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])
        ->name('employees.profile.photo.update');
    Route::post('/employees/{userId}/personal', [App\Http\Controllers\ProfileController::class, 'updatePersonal'])
        ->name('employees.profile.personal.update');
    Route::post('/employees/{userId}/location', [App\Http\Controllers\ProfileController::class, 'updateLocation'])
        ->name('employees.profile.location.update');
    Route::post('/employees/{userId}/bio', [App\Http\Controllers\ProfileController::class, 'updateBio'])
        ->name('employees.profile.bio.update');
    Route::post('/employees/{userId}/deactivate', [App\Http\Controllers\ProfileController::class, 'deactivate'])
        ->name('employees.profile.deactivate');

    Route::get('/dashboard/profile', [App\Http\Controllers\ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::post('/dashboard/profile/photo', [App\Http\Controllers\ProfileController::class, 'updateCurrentPhoto'])
        ->name('profile.photo.update');
    Route::post('/dashboard/profile/personal', [App\Http\Controllers\ProfileController::class, 'updateCurrentPersonal'])
        ->name('profile.personal.update');
    Route::post('/dashboard/profile/location', [App\Http\Controllers\ProfileController::class, 'updateCurrentLocation'])
        ->name('profile.location.update');
    Route::post('/dashboard/profile/bio', [App\Http\Controllers\ProfileController::class, 'updateCurrentBio'])
        ->name('profile.bio.update');

    Route::get('/employees/{employe}/panel', [App\Http\Controllers\EmployeController::class, 'panel'])
        ->name('employees.panel');
    Route::resource('employes', App\Http\Controllers\EmployeController::class);

    // ---- Dashboard Paie (Admin uniquement) ----
    Route::middleware(['role:ROLE_ADMIN'])->group(function () {
        Route::get('/salaires/dashboard', [App\Http\Controllers\SalaireDashboardController::class, 'index'])
            ->name('salaires.dashboard');
    });

    // ---- Gestion des Salaires ----
    Route::get('/salaires/employee', [App\Http\Controllers\SalaireController::class, 'employee'])
        ->name('salaires.employee');
    Route::resource('salaires', App\Http\Controllers\SalaireController::class);
    Route::post('/salaires/{salaire}/marquer-paye', [App\Http\Controllers\SalaireController::class, 'marquerPaye'])
        ->name('salaires.marquer-paye');
    Route::get('/salaires/{salaire}/pdf', [App\Http\Controllers\SalaireController::class, 'generatePDF'])
        ->name('salaires.pdf');

    // ---- Gestion des Primes (Admin uniquement) ----
    Route::middleware(['role:ROLE_ADMIN'])->group(function () {
        Route::resource('primes', App\Http\Controllers\PrimeController::class);
        Route::resource('retenues', App\Http\Controllers\RetenueController::class);
    });

    // ---- Gestion des Absences ----
    Route::resource('absences', App\Http\Controllers\AbsenceController::class);
    Route::post('/absences/{absence}/approve', [App\Http\Controllers\AbsenceController::class, 'approve'])
        ->name('absences.approve');
    Route::post('/absences/{absence}/reject', [App\Http\Controllers\AbsenceController::class, 'reject'])
        ->name('absences.reject');
    Route::get('/absences/export', [App\Http\Controllers\AbsenceController::class, 'export'])
        ->name('absences.export');
    Route::get('/absences/dashboard', [App\Http\Controllers\AbsenceController::class, 'dashboard'])
        ->name('absences.dashboard');

    // ---- Gestion de Présence ----
    Route::get('/presences', [App\Http\Controllers\PresenceController::class, 'index'])
        ->name('presences.index');
    Route::get('/presences/employee', [App\Http\Controllers\PresenceController::class, 'employee'])
        ->name('presences.employee');
    Route::post('/presences', [App\Http\Controllers\PresenceController::class, 'store'])
        ->name('presences.store');
    Route::post('/presences/marquer-absents', [App\Http\Controllers\PresenceController::class, 'marquerAbsents'])
        ->name('presences.marquer-absents');

    // ---- Agenda RH ----
    Route::get('/agenda', [App\Http\Controllers\EventController::class, 'index'])
        ->name('agenda.index');
    Route::get('/agenda/{year}/{month}', [App\Http\Controllers\EventController::class, 'index'])
        ->name('agenda.month');
    Route::get('/agenda/employee', [App\Http\Controllers\EventController::class, 'employee'])
        ->name('agenda.employee');
    Route::get('/agenda/employee/{year}/{month}', [App\Http\Controllers\EventController::class, 'employee'])
        ->name('agenda.employee.date');
    Route::post('/agenda', [App\Http\Controllers\EventController::class, 'store'])
        ->name('agenda.store');
    Route::delete('/agenda/{id}', [App\Http\Controllers\EventController::class, 'destroy'])
        ->name('agenda.destroy');

    // ---- Gestion des Congés (Leave Requests) ----
    Route::get('/leave-requests', [App\Http\Controllers\LeaveRequestController::class, 'index'])
        ->name('leave-requests.index');
    Route::post('/leave-requests', [App\Http\Controllers\LeaveRequestController::class, 'store'])
        ->name('leave-requests.store');
    Route::put('/leave-requests/{id}/approve', [App\Http\Controllers\LeaveRequestController::class, 'approve'])
        ->name('leave-requests.approve');
    Route::put('/leave-requests/{id}/reject', [App\Http\Controllers\LeaveRequestController::class, 'reject'])
        ->name('leave-requests.reject');

    // ---- Admin & User Management Routes ----
    Route::middleware(['role:ROLE_ADMIN'])->group(function () {
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])
            ->name('admin.users.index');

        Route::post('/admin/pending/{pendingUser}/validate', [\App\Http\Controllers\Admin\UserManagementController::class, 'validateAndCreate'])
            ->name('admin.users.validate');

        Route::post('/admin/pending/{pendingUser}/reject', [\App\Http\Controllers\Admin\UserManagementController::class, 'reject'])
            ->name('admin.users.reject');

        Route::post('/admin/users/{user}/assign-role', [\App\Http\Controllers\Admin\UserManagementController::class, 'assignRole'])
            ->name('admin.users.assign-role');

        Route::post('/admin/users/{user}/update-role', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRole'])
            ->name('admin.users.update-role');

        Route::post('/admin/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])
            ->name('admin.users.toggle-status');

        Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::get('/admin/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'show'])
            ->name('admin.users.show');

        Route::post('/admin/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserManagementController::class, 'resetPassword'])
            ->name('admin.users.reset-password');

        Route::get('/admin/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications');

        Route::post('/admin/users/{user}/reactivate', [\App\Http\Controllers\Admin\UserManagementController::class, 'reactivate'])
            ->name('admin.users.reactivate');
    });
})->withoutMiddleware([\App\Http\Middleware\KeycloakAuthenticate::class]);
