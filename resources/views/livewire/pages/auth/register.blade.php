<?php

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $roles = []; // Pour stocker les rôles sélectionnés
    public string $selectedProfessor = '';
    public $professors = []; // Pour stocker la liste des professeurs
    public $trainees = [];


    public function mount(): void
    {
        try {
            $apiController = new ApiController();
            $sessionId = $apiController->connect();
            $apiController->setProject(7);
            $response = $apiController->getAllProf($sessionId);
            // Extraction des instructeurs dans un tableau simple
            $this->professors = [];
            foreach ($response as $instructor) {
                $this->professors[] = [
                    'id' => (string)$instructor['id'], // Récupère l'ID
                    'name' => (string)$instructor['name'], // Récupère le nom
                    'email' => (string)$instructor['email'], // Récupère l'email
                ];
            }

            usort($this->professors, function($a, $b) {
                return strcmp($a['name'], $b['name']);  // Cela trie par ordre alphabétique du nom
            });
            // Debug
            //dd($this->professors);  // Affiche le tableau des instructeurs

            $responseTrainees = $apiController->getAllTraining($sessionId);
            $this->trainees = [];
            foreach ($responseTrainees as $trainee) {
                $this->trainees[] = [
                    'id' => (string)$trainee['id'], // Récupère l'ID
                    'name' => (string)$trainee['name'], // Récupère le nom
                ];
            }

            usort($this->trainees, function($a, $b) {
                return strcmp($a['name'], $b['name']);  // Cela trie par ordre alphabétique du nom
            });
        } catch (\Exception $e) {
            dd('Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        // Validation des champs
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._%+-]+@(etu\.unistra\.fr|unistra\.fr|[a-zA-Z0-9._%+-])$/'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Logique de détermination du rôle en fonction de l'email
        $role = null;

        if (str_ends_with($this->email, '@etu.unistra.fr')) {
            $role = 'student'; // Si l'email se termine par @etu.unistra.fr, rôle 'student'
        } elseif (str_ends_with($this->email, '@unistra.fr')) {
            // Si l'email se termine par @unistra.fr, on vérifie les rôles choisis
            if (count($this->roles) > 1) {
                // Si plusieurs rôles sont sélectionnés, renvoyer une erreur
                $this->addError('roles', 'Veuillez choisir un seul rôle (enseignant ou gestionnaire).');
                return;
            }

            // Si un rôle est sélectionné, utiliser ce rôle
            if (in_array('enseignant', $this->roles)) {
                $role = 'teacher';
            } elseif (in_array('manager', $this->roles)) {
                $role = 'manager';
            } else {
                $this->addError('roles', 'Veuillez sélectionner un rôle valide.');
                return;
            }
        } else {
            // Si l'email ne correspond à aucun des deux cas
            $this->addError('email', 'L\'email doit se terminer par @etu.unistra.fr ou @unistra.fr.');
            return;
        }

        // Hachage du mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // Affecter le rôle à l'utilisateur
        $validated['role'] = $role;

        // Création de l'utilisateur
        event(new Registered($user = User::create($validated)));

        // Connexion de l'utilisateur et redirection
        Auth::login($user);

        if ($role === 'teacher' && $this->selectedProfessor) {
            // Trouver le professeur sélectionné
            $professor = collect($this->professors)->firstWhere('id', $this->selectedProfessor);

            if ($professor) {
                // Insérer les données du professeur dans la table `teachers` avec `user_id`
                DB::table('teachers')->insert([
                    'user_id' => $user->id,  // Lier l'utilisateur à l'entrée teacher
                    'name' => $professor['name'],
                    'email' => $professor['email'],
                    'professor_id' => $professor['id'], // L'id du professeur sélectionné
                ]);
            }
        }

        // Rediriger l'utilisateur vers le dashboard
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div x-data="{ isTeacherChecked: false }">
    <form wire:submit.prevent="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Roles (Checkboxes) -->
        <div class="mt-4">
            <x-input-label for="roles" :value="__('Select Roles')" />

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" x-model="isTeacherChecked" wire:model="roles" value="enseignant" class="form-checkbox" />
                    <span class="ml-2">{{ __('Teacher') }}</span>
                </label>
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="roles" value="manager" class="form-checkbox" />
                    <span class="ml-2">{{ __('Manager') }}</span>
                </label>
            </div>

            <x-input-error :messages="$errors->get('roles')" class="mt-2" />

            <div class="mt-4" x-show="isTeacherChecked" x-transition>
                <x-input-label for="professor-select" :value="__('Choose a Professor')"/>
                <select id="professor-select" class="form-control" wire:model="selectedProfessor">
                    <option value="">Choisir un professeur</option>
                    @foreach ($professors as $professor)
                        <option value="{{ $professor['id'] }}">{{ $professor['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <x-input-label :value="__('Choose a training')"/>
            <select class="form-control">
                <option value="">Choisir une formation</option>
                @foreach ($trainees as $trainee)
                    <option value="{{ $trainee['id'] }}">{{ $trainee['name'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
