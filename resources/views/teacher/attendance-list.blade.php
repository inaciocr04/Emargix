<x-app-layout>
    <div class="container">
        <h1>Mes formulaires d'émargement</h1>

        @if($attendanceForms->isEmpty())
            <p>Aucun formulaire d'émargement créé.</p>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Nom de l'événement</th>
                    <th>Date de l'événement</th>
                    <th>Date de création</th>
                </tr>
                </thead>
                <tbody>
                @foreach($attendanceForms as $form)
                    <tr>
                        <td>{{ $form->event_name }}</td>
                        <td>{{ $form->event_date }}</td>
                        <td>{{ $form->created_at }}</td>
                        <td><a href="{{route('attendance.list', ['eventId' => $form->event_id])}}">Liste des présents</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
