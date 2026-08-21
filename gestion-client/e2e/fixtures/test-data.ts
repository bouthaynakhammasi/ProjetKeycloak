export const testUsers = {
  admin: {
    email: 'admin@test.com',
    password: 'Admin123!',
    name: 'Admin User',
    role: 'ROLE_ADMIN',
    keycloakId: 'admin-keycloak-id-123'
  },
  employee: {
    email: 'employee@test.com',
    password: 'Employee123!',
    name: 'Employee User',
    role: 'ROLE_EMPLOYEE',
    keycloakId: 'employee-keycloak-id-456'
  },
  pendingUser: {
    email: 'pending@test.com',
    password: 'Pending123!',
    name: 'Pending User',
    role: null,
    keycloakId: 'pending-keycloak-id-789'
  }
};

export const testEmployees = [
  {
    nom: 'Dupont',
    prenom: 'Jean',
    email: 'jean.dupont@test.com',
    poste: 'Développeur',
    departement: 'IT',
    date_embauche: '2023-01-15',
    salaire_base: 3500,
    solde_conges: 25
  },
  {
    nom: 'Martin',
    prenom: 'Marie',
    email: 'marie.martin@test.com',
    poste: 'Designer',
    departement: 'Marketing',
    date_embauche: '2023-03-20',
    salaire_base: 3200,
    solde_conges: 25
  }
];

export const testSalaries = [
  {
    mois: 1,
    annee: 2024,
    salaire_base: 3500,
    prime: 200,
    retenue: 50,
    statut_paiement: 'en_attente'
  },
  {
    mois: 2,
    annee: 2024,
    salaire_base: 3500,
    prime: 150,
    retenue: 0,
    statut_paiement: 'paye'
  }
];

export const testAbsences = [
  {
    type_absence: 'conge_paye',
    date_debut: '2024-02-01',
    date_fin: '2024-02-05',
    motif: 'Vacances famille',
    statut: 'en_attente'
  },
  {
    type_absence: 'maladie',
    date_debut: '2024-03-10',
    date_fin: '2024-03-12',
    motif: 'Grippe',
    statut: 'approuve'
  }
];
