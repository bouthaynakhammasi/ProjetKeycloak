<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MailboxController extends Controller
{
    // ─── Helpers privés ───────────────────────────────────────────────────────

    /**
     * Retourne le sub Keycloak de l'utilisateur connecté.
     */
    private function currentUserId(): string
    {
        return session('keycloak_user.sub');
    }

    /**
     * Retourne le nombre de messages non lus dans la boîte de réception.
     */
    private function unreadCount(): int
    {
        return Message::forUser($this->currentUserId())
            ->inbox()
            ->unread()
            ->count();
    }

    /**
     * Données communes transmises à toutes les vues mailbox.
     */
    private function sharedViewData(string $activeFolder): array
    {
        return [
            'activeFolder'  => $activeFolder,
            'unreadCount'   => $this->unreadCount(),
            'keycloakUser'  => session('keycloak_user'),
        ];
    }

    // ─── Actions publiques ────────────────────────────────────────────────────

    /**
     * Liste les messages du dossier demandé, filtrés par l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $folder = $request->query('folder', 'inbox');
        $validFolders = ['inbox', 'sent', 'drafts', 'trash'];

        if (!in_array($folder, $validFolders)) {
            $folder = 'inbox';
        }

        $search = $request->query('search', '');

        $query = Message::forUser($this->currentUserId())
            ->inFolder($folder)
            ->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(25)->withQueryString();

        return view('mailbox.index', array_merge($this->sharedViewData($folder), [
            'messages' => $messages,
            'search'   => $search,
        ]));
    }

    /**
     * Affiche le détail d'un message et le marque comme lu.
     */
    public function show(int $id)
    {
        $message = Message::forUser($this->currentUserId())->findOrFail($id);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('mailbox.show', array_merge($this->sharedViewData($message->folder), [
            'message' => $message,
        ]));
    }

    /**
     * Active / désactive l'étoile (favori) d'un message — réponse AJAX.
     */
    public function toggleStar(int $id): JsonResponse
    {
        $message = Message::forUser($this->currentUserId())->findOrFail($id);
        $message->update(['is_starred' => !$message->is_starred]);

        return response()->json([
            'starred' => $message->is_starred,
            'message' => $message->is_starred ? 'Message ajouté aux favoris.' : 'Favori retiré.',
        ]);
    }

    /**
     * Affiche le formulaire de composition d'un nouveau message.
     */
    public function compose()
    {
        return view('mailbox.compose', $this->sharedViewData('drafts'));
    }

    /**
     * Enregistre un nouveau message (envoi ou brouillon).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name'  => 'required|string|max:255',
            'sender_email' => 'required|email|max:255',
            'subject'      => 'required|string|max:500',
            'body'         => 'required|string',
            'folder'       => 'in:inbox,sent,drafts,trash',
            'save_draft'   => 'nullable|boolean',
        ]);

        $folder = $request->boolean('save_draft') ? 'drafts' : 'sent';

        Message::create([
            'keycloak_user_id' => $this->currentUserId(),
            'sender_name'      => $validated['sender_name'],
            'sender_email'     => $validated['sender_email'],
            'subject'          => $validated['subject'],
            'body'             => $validated['body'],
            'folder'           => $folder,
            'is_read'          => true,
            'is_starred'       => false,
        ]);

        $label = $folder === 'drafts' ? 'Brouillon sauvegardé.' : 'Message envoyé avec succès.';

        return redirect()->route('mailbox.index', ['folder' => $folder])
            ->with('success', $label);
    }

    /**
     * Déplace un message vers la corbeille.
     */
    public function destroy(int $id)
    {
        $message = Message::forUser($this->currentUserId())->findOrFail($id);

        if ($message->folder === 'trash') {
            $message->delete();
            $label = 'Message supprimé définitivement.';
        } else {
            $message->update(['folder' => 'trash']);
            $label = 'Message déplacé vers la corbeille.';
        }

        return redirect()->route('mailbox.index', ['folder' => 'inbox'])
            ->with('success', $label);
    }
}
