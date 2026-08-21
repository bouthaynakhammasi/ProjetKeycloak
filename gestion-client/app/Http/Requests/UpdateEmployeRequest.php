<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'un employé.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('employes', 'email')->ignore($this->route('employe'))],
            'telephone' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+()\s-]*$/'],
            'poste' => ['nullable', 'string', 'max:255'],
            'departement' => ['nullable', 'string', 'max:255'],
            'date_embauche' => ['nullable', 'date'],
            'statut' => ['required', Rule::in(['actif', 'inactif'])],
            'photo' => ['nullable', 'image', 'max:2048'],
            'keycloak_id' => ['nullable', 'string', 'max:255', Rule::unique('employes', 'keycloak_id')->ignore($this->route('employe'))],
        ];
    }

    /**
     * Messages de validation personnalisés en français.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email n\'est pas valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.regex' => 'Le téléphone contient des caractères invalides.',
            'statut.in' => 'Le statut doit être actif ou inactif.',
            'photo.image' => 'La photo doit être une image valide.',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'keycloak_id.unique' => 'Cette liaison Keycloak est déjà utilisée.',
        ];
    }
}
