@extends('layouts.mailbox')

@section('title', $message->subject)

@section('content')

<div class="animate-fade-in max-w-3xl mx-auto px-5 py-6">

    {{-- ── Bouton retour ── --}}
    <a
        href="{{ route('mailbox.index', ['folder' => $message->folder]) }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-accent-600 mb-5 group transition-colors"
        id="back-link"
    >
        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour
    </a>

    {{-- ── Carte du message ── --}}
    <article class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">

        {{-- En-tête du message --}}
        <header class="px-6 pt-6 pb-5 border-b border-gray-100">

            <div class="flex items-start justify-between gap-4">
                <h1 class="text-lg font-semibold text-gray-900 leading-snug flex-1">
                    {{ $message->subject }}
                </h1>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    {{-- Étoile --}}
                    <button
                        type="button"
                        onclick="toggleStar({{ $message->id }})"
                        id="star-btn"
                        class="star-btn p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                        title="{{ $message->is_starred ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                        aria-label="{{ $message->is_starred ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                    >
                        <svg
                            id="star-icon"
                            class="w-5 h-5 {{ $message->is_starred ? 'text-amber-400 fill-amber-400' : 'text-gray-400' }}"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                            fill="{{ $message->is_starred ? 'currentColor' : 'none' }}"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </button>

                    {{-- Supprimer --}}
                    <form method="POST" action="{{ route('mailbox.destroy', $message->id) }}" onsubmit="return confirm('Déplacer ce message vers la corbeille ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Supprimer" id="delete-btn">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Méta-données expéditeur --}}
            <div class="flex items-center gap-3 mt-4">
                {{-- Avatar expéditeur --}}
                @php
                    $words = array_filter(explode(' ', trim($message->sender_name)));
                    $initials = count($words) >= 2
                        ? strtoupper(mb_substr($words[array_key_first($words)], 0, 1) . mb_substr(end($words), 0, 1))
                        : strtoupper(mb_substr($message->sender_name, 0, 2));

                    // Couleur déterministe basée sur le nom
                    $colors = ['bg-violet-500','bg-blue-500','bg-emerald-500','bg-orange-500','bg-rose-500','bg-cyan-500'];
                    $colorClass = $colors[crc32($message->sender_name) % count($colors)];
                @endphp
                <div class="w-9 h-9 rounded-full {{ $colorClass }} flex items-center justify-center text-white text-xs font-semibold shrink-0">
                    {{ $initials }}
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $message->sender_name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $message->sender_email }}</p>
                </div>

                <time class="ml-auto text-xs text-gray-400 shrink-0" datetime="{{ $message->created_at->toIso8601String() }}" title="{{ $message->created_at->format('d/m/Y H:i') }}">
                    {{ $message->created_at->translatedFormat('l d F Y à H:i') }}
                </time>
            </div>
        </header>

        {{-- Corps du message --}}
        <div class="px-6 py-6">
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $message->body }}</div>
        </div>

        {{-- Pied du message --}}
        <footer class="px-6 py-4 border-t border-gray-100 flex items-center gap-3">
            <a
                href="{{ route('mailbox.reply', $message->id) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-accent-50 text-accent-700 text-sm font-medium hover:bg-accent-100 transition-colors"
                id="reply-btn"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Répondre
            </a>

            <a
                href="{{ route('mailbox.index', ['folder' => $message->folder]) }}"
                class="px-3.5 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 transition-colors"
                id="back-footer-link"
            >
                ← Retour à la liste
            </a>
        </footer>
    </article>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function toggleStar(messageId) {
        const btn  = document.getElementById('star-btn');
        const icon = document.getElementById('star-icon');

        btn.disabled = true;

        try {
            const response = await fetch(`/mailbox/${messageId}/star`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Erreur réseau');
            const data = await response.json();

            if (data.starred) {
                icon.setAttribute('fill', 'currentColor');
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-amber-400', 'fill-amber-400');
                btn.title = 'Retirer des favoris';
            } else {
                icon.setAttribute('fill', 'none');
                icon.classList.remove('text-amber-400', 'fill-amber-400');
                icon.classList.add('text-gray-400');
                btn.title = 'Ajouter aux favoris';
            }
        } catch (err) {
            console.error('Erreur toggleStar :', err);
        } finally {
            btn.disabled = false;
        }
    }
</script>
@endpush
