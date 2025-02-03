<div>
    <button wire:click="toggleAccess" class="btn btn-{{ $attendanceForm->access ? 'success' : 'danger' }}">
        {{ $attendanceForm->access ? 'Accès activé' : 'Accès désactivé' }}
    </button>
</div>
