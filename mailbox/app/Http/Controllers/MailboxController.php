<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\KeycloakUser;
use App\Mail\NewMessageMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
        $currentUserId = $this->currentUserId();

        // Filtrage spécifique selon le dossier pour garantir l'isolation
        $query = Message::query();
        
        switch ($folder) {
            case 'inbox':
                // Boîte de réception : messages où l'utilisateur est le destinataire
                $query->where('recipient_id', $currentUserId)
                      ->where('folder', 'inbox');
                break;
            case 'sent':
                // Envoyés : messages où l'utilisateur est l'expéditeur
                $query->where('sender_id', $currentUserId)
                      ->where('folder', 'sent');
                break;
            case 'drafts':
                // Brouillons : messages où l'utilisateur est l'expéditeur
                $query->where('sender_id', $currentUserId)
                      ->where('folder', 'drafts');
                break;
            case 'trash':
                // Corbeille : messages où l'utilisateur est l'expéditeur
                $query->where('sender_id', $currentUserId)
                      ->where('folder', 'trash');
                break;
        }

        $query->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(25)->withQueryString();

        // Déterminer le layout à utiliser
        $useBootstrapLayout = $request->routeIs('mailbox.bootstrap');
        $view = $useBootstrapLayout ? 'mailbox.index-bootstrap' : 'mailbox.index';

        return view($view, array_merge($this->sharedViewData($folder), [
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
    public function compose(Request $request, $id = null)
    {
        $originalMessage = null;
        $recipientEmail = null;
        $subject = '';

        // Si appelé via la route reply
        if ($id) {
            $originalMessage = Message::forUser($this->currentUserId())->findOrFail($id);
            $recipientEmail = $originalMessage->sender_email;
            $subject = 'Re: ' . $originalMessage->subject;
        }

        return view('mailbox.compose', array_merge($this->sharedViewData('drafts'), [
            'originalMessage' => $originalMessage,
            'recipientEmail' => $recipientEmail,
            'subject' => $subject,
        ]));
    }

    /**
     * Enregistre un nouveau message (envoi ou brouillon).
     */
    public function store(Request $request)
    {
        try {
            // Logger les données reçues pour débogage
            Log::info('Form submission received', [
                'all_data' => $request->all(),
                'method' => $request->method(),
            ]);

            $validated = $request->validate([
                'sender_name'    => 'required|string|max:255',
                'sender_email'   => 'required|email|max:255',
                'recipient_email' => 'required|email|max:255',
                'subject'         => 'required|string|max:500',
                'body'            => 'required|string',
                'save_draft'      => 'nullable|boolean',
            ]);

            Log::info('Validation passed', [
                'recipient_email' => $validated['recipient_email'],
                'subject' => $validated['subject'],
            ]);

            $folder = $request->boolean('save_draft') ? 'drafts' : 'sent';
            $recipientEmail = $validated['recipient_email'];

            // Normaliser l'email pour la comparaison (insensible à la casse et aux espaces)
            $normalizedEmail = strtolower(trim($recipientEmail));

            // Chercher si l'email correspond à un utilisateur enregistré
            $recipient = KeycloakUser::whereRaw('LOWER(TRIM(email)) = ?', [$normalizedEmail])
                ->where('status', 'active')
                ->first();

            $recipientId = $recipient ? $recipient->keycloak_id : null;
            
            if ($recipient && $folder === 'sent') {
                // Pour les messages internes: créer DEUX entrées
                // 1. Une dans la boîte d'envoi de l'expéditeur
                Message::create([
                    'sender_id'        => $this->currentUserId(),
                    'keycloak_user_id' => $this->currentUserId(), // L'expéditeur voit ce message dans ses envois
                    'recipient_id'     => $recipient->keycloak_id,
                    'recipient_email'  => $recipientEmail,
                    'sender_name'      => $validated['sender_name'],
                    'sender_email'     => $validated['sender_email'],
                    'subject'          => $validated['subject'],
                    'body'             => $validated['body'],
                    'folder'           => 'sent',
                    'is_read'          => true,
                    'is_starred'       => false,
                ]);

                // 2. Une dans la boîte de réception du destinataire
                $inboxMessage = Message::create([
                    'sender_id'        => $this->currentUserId(),
                    'keycloak_user_id' => $recipient->keycloak_id, // Le destinataire voit ce message dans sa boîte de réception
                    'recipient_id'     => $recipient->keycloak_id,
                    'recipient_email'  => $recipientEmail,
                    'sender_name'      => $validated['sender_name'],
                    'sender_email'     => $validated['sender_email'],
                    'subject'          => $validated['subject'],
                    'body'             => $validated['body'],
                    'folder'           => 'inbox',
                    'is_read'          => false, // Non lu pour le destinataire
                    'is_starred'       => false,
                ]);

                $message = $inboxMessage; // Pour l'envoi d'email
            } else {
                // Pour les messages externes ou brouillons: une seule entrée
                $message = Message::create([
                    'sender_id'        => $this->currentUserId(),
                    'keycloak_user_id' => $this->currentUserId(), // L'expéditeur voit ce message
                    'recipient_id'     => $recipientId,
                    'recipient_email'  => $recipientEmail,
                    'sender_name'      => $validated['sender_name'],
                    'sender_email'     => $validated['sender_email'],
                    'subject'          => $validated['subject'],
                    'body'             => $validated['body'],
                    'folder'           => $folder,
                    'is_read'          => true,
                    'is_starred'       => false,
                ]);
            }

            // Envoyer l'email au destinataire (uniquement si ce n'est pas un brouillon)
            if ($folder === 'sent') {
                try {
                    $messageUrl = route('mailbox.show', $message->id);
                    Mail::to($recipientEmail)->send(new NewMessageMail($message, $validated['sender_name'], $messageUrl));
                    Log::info('Email sent successfully', [
                        'message_id' => $message->id,
                        'recipient' => $recipientEmail,
                        'is_registered_user' => $recipient ? true : false,
                    ]);
                } catch (\Exception $e) {
                    // Logger l'erreur mais ne pas bloquer le flux utilisateur
                    Log::error('Failed to send email notification', [
                        'message_id' => $message->id,
                        'recipient' => $recipientEmail,
                        'error' => $e->getMessage(),
                    ]);
                    // Le message est déjà sauvegardé, on continue normalement
                }
            }

            $label = $folder === 'drafts' ? 'Brouillon sauvegardé.' : 'Message envoyé avec succès.';

            return redirect()->route('mailbox.index', ['folder' => $folder])
                ->with('success', $label);
        } catch (\Exception $e) {
            Log::error('Exception in store method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de l\'envoi : ' . $e->getMessage()]);
        }
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
