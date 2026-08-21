<?php

namespace App\Http\Controllers;

use App\Models\Salaire;
use App\Models\Prime;
use App\Models\Retenue;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaireDashboardController extends Controller
{
    /**
     * Afficher le dashboard avec les statistiques de paie (données réelles)
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $userName = session('user_name', 'Admin');

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Mois précédent
        $prevDate = $now->copy()->subMonth();
        $prevMonth = $prevDate->month;
        $prevYear = $prevDate->year;

        // ---- Calculs Masse Salariale ----
        $masseSalarialeCurrent = Salaire::periode($currentMonth, $currentYear)->sum('salaire_net');
        $masseSalarialePrev = Salaire::periode($prevMonth, $prevYear)->sum('salaire_net');
        $masseSalarialeVar = $this->computeVariation($masseSalarialeCurrent, $masseSalarialePrev);

        // ---- Calculs Nombre de Salaires ----
        $nbSalairesCurrent = Salaire::periode($currentMonth, $currentYear)->count();
        $nbSalairesPrev = Salaire::periode($prevMonth, $prevYear)->count();
        $nbSalairesVar = $this->computeVariation($nbSalairesCurrent, $nbSalairesPrev);

        // ---- Calculs Primes ----
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d');
        $startOfPrevMonth = $prevDate->copy()->startOfMonth()->format('Y-m-d');
        $endOfPrevMonth = $prevDate->copy()->endOfMonth()->format('Y-m-d');

        $primesTotalCurrent = Prime::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('montant');
        $primesTotalPrev = Prime::whereBetween('date', [$startOfPrevMonth, $endOfPrevMonth])->sum('montant');
        $primesVar = $this->computeVariation($primesTotalCurrent, $primesTotalPrev);

        // ---- Calculs Retenues ----
        $retenuesCurrent = Retenue::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('montant');
        $retenuesPrev = Retenue::whereBetween('date', [$startOfPrevMonth, $endOfPrevMonth])->sum('montant');
        $retVar = $this->computeVariation($retenuesCurrent, $retenuesPrev);

        // KPIs
        $kpis = [
            [
                'title' => 'Masse Salariale',
                'value' => number_format($masseSalarialeCurrent, 2, ',', ' ') . ' DT',
                'badge' => $masseSalarialeVar['label'],
                'badge_positive' => $masseSalarialeVar['positive'],
                'vs_last_month' => 'vs ' . $prevDate->translatedFormat('F Y'),
                'icon' => 'wallet'
            ],
            [
                'title' => 'Bulletins émis',
                'value' => $nbSalairesCurrent,
                'badge' => $nbSalairesVar['label'],
                'badge_positive' => $nbSalairesVar['positive'],
                'vs_last_month' => 'vs ' . $prevDate->translatedFormat('F Y'),
                'icon' => 'file-text'
            ],
            [
                'title' => 'Primes versées',
                'value' => number_format($primesTotalCurrent, 2, ',', ' ') . ' DT',
                'badge' => $primesVar['label'],
                'badge_positive' => $primesVar['positive'],
                'vs_last_month' => 'vs ' . $prevDate->translatedFormat('F Y'),
                'icon' => 'gift'
            ],
            [
                'title' => 'Retenues',
                'value' => number_format($retenuesCurrent, 2, ',', ' ') . ' DT',
                'badge' => $retVar['label'],
                'badge_positive' => !$retVar['positive'], // Less retenues = positive
                'vs_last_month' => 'vs ' . $prevDate->translatedFormat('F Y'),
                'icon' => 'minus-circle'
            ]
        ];

        // ---- Funnel / Résumé RH ----
        $employesActifs = Employe::where('statut', 'actif')->count();
        $salairesGeneres = Salaire::periode($currentMonth, $currentYear)->count();
        $primesAttribuees = Prime::whereBetween('date', [$startOfMonth, $endOfMonth])->count();
        $retenuesAppliquees = Retenue::whereBetween('date', [$startOfMonth, $endOfMonth])->count();

        $maxFunnel = max($employesActifs, 1);
        $funnel = [
            ['step' => 'Employés Actifs', 'value' => $employesActifs, 'percent' => 100],
            ['step' => 'Bulletins générés', 'value' => $salairesGeneres, 'percent' => round(($salairesGeneres / $maxFunnel) * 100)],
            ['step' => 'Primes attribuées', 'value' => $primesAttribuees, 'percent' => round(($primesAttribuees / $maxFunnel) * 100)],
            ['step' => 'Retenues appliquées', 'value' => $retenuesAppliquees, 'percent' => round(($retenuesAppliquees / $maxFunnel) * 100)],
        ];

        // ---- Taux de traitement (conversion rate) ----
        $tauxTraitement = $employesActifs > 0
            ? round(($salairesGeneres / $employesActifs) * 100, 1)
            : 0;

        $salairesGeneresPrev = Salaire::periode($prevMonth, $prevYear)->count();
        $employesActifsPrev = max($employesActifs, 1); // approximation
        $tauxTraitementPrev = $employesActifsPrev > 0
            ? round(($salairesGeneresPrev / $employesActifsPrev) * 100, 1)
            : 0;

        $tauxVar = $this->computeVariation($tauxTraitement, $tauxTraitementPrev);

        $conversionRate = [
            'rate' => $tauxTraitement . '%',
            'variation' => $tauxVar['label'],
            'variation_positive' => $tauxVar['positive']
        ];

        // ---- Derniers bulletins de salaire ----
        $products = Salaire::with('employe')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($salaire) {
                $nomComplet = $salaire->employe ? $salaire->employe->nom_complet : 'N/A';
                return [
                    'id' => $salaire->id,
                    'name' => $nomComplet,
                    'image' => $salaire->employe && $salaire->employe->photo
                        ? asset('storage/' . $salaire->employe->photo)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($nomComplet) . '&background=6366f1&color=ffffff',
                    'periode' => $salaire->nom_mois . ' ' . $salaire->annee,
                    'salaire_brut' => number_format($salaire->salaire_brut, 2, ',', ' '),
                    'deductions' => number_format($salaire->deductions, 2, ',', ' '),
                    'salaire_net' => number_format($salaire->salaire_net, 2, ',', ' '),
                ];
            });

        // ---- Données graphique mensuel (6 derniers mois) ----
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = $now->copy()->subMonths($i);
            $total = Salaire::periode($d->month, $d->year)->sum('salaire_net');
            $chartData[] = [
                'label' => $d->translatedFormat('M Y'),
                'value' => round($total, 2),
            ];
        }

        // ---- Stats performance ----
        $totalSalairesAllTime = Salaire::sum('salaire_net');
        $totalPrimesAllTime = Prime::sum('montant');
        $totalRetenuesAllTime = Retenue::sum('montant');
        $totalBrut = $totalSalairesAllTime + $totalPrimesAllTime;

        $performanceRate = $totalBrut > 0
            ? round(($totalSalairesAllTime / ($totalBrut ?: 1)) * 100)
            : 0;
        $tasksRate = $employesActifs > 0
            ? min(round(($salairesGeneres / $employesActifs) * 100), 100)
            : 0;

        $premiumStats = [
            'performance' => $performanceRate,
            'tasks' => $tasksRate,
        ];

        // ---- Overall Sales = masse salariale totale ----
        $masseSalarialeTotal = Salaire::sum('salaire_net');
        $salesData = [
            'total' => number_format($masseSalarialeTotal, 2, ',', ' '),
            'variation' => $masseSalarialeVar['label'],
            'variation_positive' => $masseSalarialeVar['positive'],
        ];

        return view('salaires.dashboard', compact(
            'userName',
            'kpis',
            'funnel',
            'products',
            'salesData',
            'conversionRate',
            'premiumStats',
            'chartData'
        ));
    }

    /**
     * Calculer la variation en pourcentage entre deux valeurs.
     */
    private function computeVariation($current, $previous): array
    {
        if ($previous == 0 && $current == 0) {
            return ['label' => '0%', 'positive' => true];
        }

        if ($previous == 0) {
            return ['label' => '+100%', 'positive' => true];
        }

        $variation = (($current - $previous) / $previous) * 100;
        $sign = $variation >= 0 ? '+' : '';
        return [
            'label' => $sign . round($variation, 1) . '%',
            'positive' => $variation >= 0,
        ];
    }

    /**
     * Vérifier que l'utilisateur est admin
     */
    private function authorizeAdmin()
    {
        if (session('user_role') !== 'ROLE_ADMIN') {
            abort(403, 'Accès non autorisé');
        }
    }
}
