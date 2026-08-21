@extends('layouts.mailbox')

@section('title', 'Nouveau message')

@section('content')
{{--
    Cette vue n'est pas directement rendue : le formulaire de composition
    est intégré dans la modale du layout principal (layouts/mailbox.blade.php).
    On utilise cette route pour ouvrir automatiquement la modale via JS.
    Le layout détecte Route::routeIs('mailbox.compose') et ouvre la modale.
--}}
<div class="flex flex-col items-center justify-center h-full py-24 text-gray-400 animate-fade-in">
    <svg class="w-10 h-10 mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
    <p class="text-sm">La fenêtre de composition s'ouvre automatiquement…</p>
</div>

@push('scripts')
<script>
    @if(isset($recipientEmail) && $recipientEmail !== '')
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('compose-recipient');
            if (input) {
                input.value = '{{ $recipientEmail }}';
                input.readOnly = true; // Utiliser readonly au lieu de disabled
                input.classList.add('bg-gray-50', 'cursor-not-allowed');
            }

            @if(isset($subject) && $subject !== '')
                const subjectInput = document.getElementById('compose-subject');
                if (subjectInput) {
                    subjectInput.value = '{{ $subject }}';
                }
            @endif
        });
    @endif
</script>
@endpush
@endsection
