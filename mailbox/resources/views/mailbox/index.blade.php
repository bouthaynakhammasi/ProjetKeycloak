@extends('layouts.mailbox')

@section('title', ucfirst($activeFolder === 'inbox' ? 'Boîte de réception' : ($activeFolder === 'sent' ? 'Envoyés' : ($activeFolder === 'drafts' ? 'Brouillons' : 'Corbeille'))))

@section('content')

<div class="animate-fade-in">

    {{-- ── En-tête de dossier ── --}}
    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
        <h1 class="text-sm font-semibold text-gray-700">
            @php
                $folderLabels = [
                    'inbox'  => 'Boîte de réception',
                    'sent'   => 'Envoyés',
                    'drafts' => 'Brouillons',
                    'trash'  => 'Corbeille',
                ];
            @endphp
            {{ $folderLabels[$activeFolder] ?? 'Messages' }}
            @if($messages->total() > 0)
                <span class="ml-1.5 text-xs font-normal text-gray-400">{{ $messages->total() }}</span>
            @endif
        </h1>

        {{-- Pagination compacte --}}
        @if($messages->hasPages())
            <div class="flex items-center gap-1 text-xs text-gray-400">
                <span>{{ $messages->firstItem() }}–{{ $messages->lastItem() }} sur {{ $messages->total() }}</span>
                <a href="{{ $messages->previousPageUrl() }}" class="{{ $messages->onFirstPage() ? 'pointer-events-none opacity-30' : 'hover:text-gray-600' }} p-1 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <a href="{{ $messages->nextPageUrl() }}" class="{{ $messages->hasMorePages() ? 'hover:text-gray-600' : 'pointer-events-none opacity-30' }} p-1 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif
    </div>

    {{-- ── Liste de messages ── --}}
    @if($messages->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-gray-400">
            <svg class="w-12 h-12 mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm">
                @if($search)
                    Aucun message trouvé pour « <strong>{{ $search }}</strong> ».
                @else
                    Ce dossier est vide.
                @endif
            </p>
        </div>
    @else
        <ul class="divide-y divide-gray-50" role="list">
            @foreach($messages as $message)
                @php $isUnread = !$message->is_read; @endphp
                <li
                    class="message-row group flex items-center gap-3 px-4 py-3 cursor-pointer
                        {{ $isUnread ? 'bg-accent-50/60' : 'bg-white hover:bg-gray-50' }}"
                    data-message-id="{{ $message->id }}"
                >
                    {{-- Bouton étoile --}}
                    <button
                        type="button"
                        class="star-btn shrink-0 p-0.5 rounded focus:outline-none focus:ring-2 focus:ring-accent-300"
                        data-id="{{ $message->id }}"
                        data-starred="{{ $message->is_starred ? '1' : '0' }}"
                        onclick="toggleStar(event, {{ $message->id }})"
                        title="{{ $message->is_starred ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                        aria-label="{{ $message->is_starred ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                        id="star-btn-{{ $message->id }}"
                    >
                        <svg
                            class="w-4 h-4 {{ $message->is_starred ? 'text-amber-400 fill-amber-400' : 'text-gray-300 group-hover:text-gray-400' }}"
                            id="star-icon-{{ $message->id }}"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                            fill="{{ $message->is_starred ? 'currentColor' : 'none' }}"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </button>

                    {{-- Lien vers le message (toute la ligne) --}}
                    <a
                        href="{{ route('mailbox.show', $message->id) }}"
                        class="flex-1 flex items-baseline gap-3 min-w-0"
                        id="message-link-{{ $message->id }}"
                    >
                        {{-- Expéditeur --}}
                        <span class="w-36 shrink-0 truncate text-sm {{ $isUnread ? 'font-semibold text-gray-900' : 'font-medium text-gray-700' }}">
                            {{ $message->sender_name }}
                        </span>

                        {{-- Objet + début du corps --}}
                        <span class="flex-1 truncate text-sm text-gray-500 min-w-0">
                            <span class="{{ $isUnread ? 'text-gray-800 font-medium' : 'text-gray-600' }}">{{ $message->subject }}</span>
                            <span class="text-gray-400"> — {{ Str::limit($message->body, 60) }}</span>
                        </span>

                        {{-- Date --}}
                        <span class="shrink-0 text-xs {{ $isUnread ? 'font-semibold text-accent-600' : 'text-gray-400' }} tabular-nums">
                            {{ $message->relative_date }}
                        </span>
                    </a>

                    {{-- Bouton supprimer (visible au hover) --}}
                    <form method="POST" action="{{ route('mailbox.destroy', $message->id) }}" class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Supprimer ce message ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 rounded text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Supprimer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /**
     * Bascule l'état étoile d'un message via AJAX PATCH.
     */
    async function toggleStar(event, messageId) {
        event.preventDefault();
        event.stopPropagation();

        const btn  = document.getElementById(`star-btn-${messageId}`);
        const icon = document.getElementById(`star-icon-${messageId}`);

        btn.disabled = true;

        try {
            const response = await fetch(`/mailbox/${messageId}/star`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Erreur réseau');

            const data = await response.json();

            // Mise à jour visuelle immédiate
            if (data.starred) {
                icon.setAttribute('fill', 'currentColor');
                icon.classList.remove('text-gray-300', 'group-hover:text-gray-400');
                icon.classList.add('text-amber-400', 'fill-amber-400');
                btn.dataset.starred = '1';
                btn.title = 'Retirer des favoris';
            } else {
                icon.setAttribute('fill', 'none');
                icon.classList.remove('text-amber-400', 'fill-amber-400');
                icon.classList.add('text-gray-300', 'group-hover:text-gray-400');
                btn.dataset.starred = '0';
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
