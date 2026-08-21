@extends('layouts.bootstrap-creative')

@section('title', 'Boîte de réception')

@section('content')
<div class="container px-4 px-lg-5 py-5">
    <div class="row gx-4 gx-lg-5 justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header">
                    <h3 class="text-center font-weight-light my-4">
                        @if(($activeFolder ?? 'inbox') === 'inbox')
                            <i class="bi bi-inbox"></i> Boîte de réception
                        @elseif(($activeFolder ?? 'inbox') === 'sent')
                            <i class="bi bi-send"></i> Envoyés
                        @elseif(($activeFolder ?? 'inbox') === 'drafts')
                            <i class="bi bi-file-earmark"></i> Brouillons
                        @elseif(($activeFolder ?? 'inbox') === 'trash')
                            <i class="bi bi-trash"></i> Corbeille
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Barre de recherche -->
                    <form method="GET" action="{{ route('mailbox.index') }}" class="mb-4">
                        <input type="hidden" name="folder" value="{{ $activeFolder ?? 'inbox' }}">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $search ?? '' }}" 
                                   class="form-control" placeholder="Rechercher dans les messages...">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Bouton Nouveau message -->
                    <div class="mb-4">
                        <a href="{{ route('mailbox.compose') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nouveau message
                        </a>
                    </div>

                    <!-- Liste des messages -->
                    @if($messages->count() > 0)
                        <div class="list-group">
                            @foreach($messages as $message)
                                <a href="{{ route('mailbox.show', $message->id) }}" 
                                   class="list-group-item list-group-item-action {{ !$message->is_read ? 'list-group-item-primary' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">
                                            {{ $message->sender_name }}
                                            @if(!$message->is_read)
                                                <span class="badge bg-primary">Nouveau</span>
                                            @endif
                                        </h5>
                                        <small>{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $message->subject }}</p>
                                    <small class="text-muted">{{ Str::limit($message->body, 100) }}</small>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $messages->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-3">Aucun message dans ce dossier.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection