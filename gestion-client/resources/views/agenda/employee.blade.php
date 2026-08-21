@extends('layouts.app')

@section('title', 'Mon Agenda')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-white/90">Mon Espace</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Mon Agenda</h1>
                <p class="mt-1 text-sm font-medium text-white/80">{{ $monthName }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Navigation mois --}}
                <a href="{{ route('agenda.employee.date', ['year' => $prevDate->year, 'month' => $prevDate->month]) }}"
                   class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-700 bg-gray-900 hover:bg-gray-800 transition-colors">
                    <i data-lucide="chevron-left" class="w-5 h-5 text-white"></i>
                </a>
                <a href="{{ route('agenda.employee.date', ['year' => $nextDate->year, 'month' => $nextDate->month]) }}"
                   class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-700 bg-gray-900 hover:bg-gray-800 transition-colors">
                    <i data-lucide="chevron-right" class="w-5 h-5 text-white"></i>
                </a>
            </div>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-900/50 border border-green-700 text-green-300 rounded-xl text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Events by day --}}
        @forelse($eventsGrouped as $date => $dayEvents)
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-indigo-300"></i>
                </div>
                <h2 class="text-base font-semibold text-white">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l j F Y') }}
                </h2>
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-indigo-500 text-white rounded-full">
                    {{ $dayEvents->count() }}
                </span>
            </div>

            <div class="bg-gray-900 rounded-2xl shadow-sm border border-gray-800 overflow-hidden">
                <div class="divide-y divide-gray-800">
                    @foreach($dayEvents as $event)
                    <div class="flex items-center gap-4 p-4 hover:bg-gray-800 transition-colors">
                        <div class="w-10 h-10 rounded-full {{ $event->type === 'conge' ? 'bg-indigo-900/50 text-indigo-300' : ($event->type === 'formation' ? 'bg-purple-900/50 text-purple-300' : ($event->type === 'entretien' ? 'bg-blue-900/50 text-blue-300' : ($event->type === 'ferie' ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'))) }} flex items-center justify-center shrink-0">
                            @if($event->type === 'conge')
                                <i data-lucide="sun" class="w-5 h-5"></i>
                            @elseif($event->type === 'formation')
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            @elseif($event->type === 'entretien')
                                <i data-lucide="users" class="w-5 h-5"></i>
                            @elseif($event->type === 'ferie')
                                <i data-lucide="gift" class="w-5 h-5"></i>
                            @else
                                <i data-lucide="calendar" class="w-5 h-5"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $event->title }}</p>
                            <p class="text-xs text-gray-400 truncate">
                                @if($event->start_time)
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                @endif
                                @if($event->description)
                                    - {{ $event->description }}
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border
                            {{ $event->type === 'conge' ? 'bg-indigo-900/50 text-indigo-300 border-indigo-700' :
                               ($event->type === 'formation' ? 'bg-purple-900/50 text-purple-300 border-purple-700' :
                               ($event->type === 'entretien' ? 'bg-blue-900/50 text-blue-300 border-blue-700' :
                               ($event->type === 'ferie' ? 'bg-green-900/50 text-green-300 border-green-700' : 'bg-gray-700 text-gray-300 border-gray-600'))) }}">
                            {{ ucfirst($event->type) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="bg-gray-900 rounded-2xl shadow-sm border border-gray-800 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="calendar-x" class="w-8 h-8 text-gray-400"></i>
            </div>
            <p class="text-base font-semibold text-white">Aucun événement ce mois-ci</p>
            <p class="text-sm text-gray-400 mt-2">Votre agenda est vide pour cette période</p>
        </div>
        @endforelse
    </div>
</div>
@endsection