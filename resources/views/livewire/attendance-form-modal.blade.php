<div>
    <!-- Si une formation est sélectionnée, afficher les boutons pour QR Code et Modifier -->
    @if($selectedTraining)
            <!-- Bouton pour voir le QR Code -->
            <button wire:click="generateAttendanceForm" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">
                Voir le QR Code
            </button>
    @endif
        <!-- Bouton pour afficher la modale -->
        <button type="button" class="bg-yellow-500 text-white px-4 py-2 rounded mt-4" wire:click="$set('showModal', true)">
            @if($selectedTraining)
                Modifier le QR code
            @else
                Génerer un QR code
            @endif
        </button>

        <!-- Modal pour générer le formulaire d'émargement -->
        @if($showModal)
            <div class="fixed inset-0 flex items-center justify-center z-50">
                <div class="modal-overlay absolute inset-0 bg-gray-900 opacity-50"></div>

                <div class="modal-container bg-white p-8 rounded-lg w-1/3 z-10">
                    <div class="modal-header flex justify-between items-center">
                        <h3 class="text-xl">Générer un formulaire d'émargement</h3>
                        <button wire:click="$set('showModal', false)" class="text-red-500">X</button>
                    </div>

                    <form wire:submit.prevent="generateAttendanceForm">
                        <!-- Sélectionner la formation -->
                        <div class="mb-4">
                            <label for="selectedTraining" class="block">Formation</label>
                            <select wire:model="selectedTraining" id="selectedTraining" class="w-full p-2 border rounded">
                                <option value="">Sélectionner une formation</option>
                                @foreach($trainings as $training)
                                    <option value="{{ $training->id }}">{{ $training->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedTraining') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Sélectionner le parcours -->
                        <div class="mb-4">
                            <label for="selectedCourse" class="block">Parcours</label>
                            <select wire:model="selectedCourse" id="selectedCourse" class="w-full p-2 border rounded">
                                <option value="">Sélectionner un parcours</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="selectedTdGroup" class="block">Groupe</label>
                            <select wire:model="selectedTdGroup" id="selectedTdGroup" class="w-full p-2 border rounded">
                                <option value="">Sélectionner un groupe de TD</option>
                                @foreach($tdgroups as $tdgroup)
                                    <option value="{{ $tdgroup->id }}">{{ $tdgroup->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sélectionner le groupe TP -->
                        <div class="mb-4">
                            <label for="selectedTpGroup" class="block">Groupe</label>
                            <select wire:model="selectedTpGroup" id="selectedTpGroup" class="w-full p-2 border rounded">
                                <option value="">Sélectionner un groupe de TP</option>
                                @foreach($tpgroups as $tpgroup)
                                    <option value="{{ $tpgroup->id }}">{{ $tpgroup->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="modal-footer flex justify-end">
                            <button type="submit" class="btn btn-success bg-green-500 text-white p-2 rounded">Générer le formulaire</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
</div>
