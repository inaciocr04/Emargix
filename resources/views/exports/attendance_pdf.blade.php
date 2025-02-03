@php use App\Models\StudentSignature; @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Présence</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        td img {
            width: 50px;
            height: 50px;
        }
    </style>
</head>
<body>
<h1>Rapport de Présence</h1>

<p><strong>Professeur :</strong> {{ $attendanceForm->teacher->name }}</p>
<p><strong>Événement :</strong> {{ $attendanceForm->event_name }}</p>
<p><strong>Horaire :</strong> {{ $attendanceForm->event_start_hour }} - {{ $attendanceForm->event_end_hour }}</p>
<p><strong>Date :</strong> {{ $attendanceForm->event_date }}</p>
<div style="display: flex; flex-direction: row; justify-content: center; align-items: center;">
    <strong>Signature :</strong>
    <div>
        @if($signatureTeacherData)
            <img src="{{ $signatureTeacherData }}" alt="Signature" style="width: 150px; height: auto;">
        @endif
    </div>
</div>


<table>
    <thead>
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Formation</th>
        <th>Parcours</th>
        <th>Groupe de TD</th>
        <th>Groupe de TP</th>
        <th>Statut de signature</th>
        <th>Absent</th>
        <th>Présent</th>
        <th>Signature</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($students as $student)
        @php
            // Récupérer la signature encodée en base64 pour cet étudiant
            $signatureStudentData = $studentSignaturesData[$student->id] ?? null;
        @endphp
        <tr>
            <td>{{ $student->lastname }}</td>
            <td>{{ $student->firstname }}</td>
            <td>{{ $student->training->name ?? 'N/A' }}</td>
            <td>{{ $student->course->name ?? 'N/A' }}</td>
            <td>{{ $student->td_group->name ?? 'N/A' }}</td>
            <td>{{ $student->tp_group->name ?? 'N/A' }}</td>
            <td>
                @if($signatureStudentData)
                    Présent
                @else
                    Absent
                @endif

            </td>
            <td>{{ $student->student_statu == 'Absent' ? '✔' : '' }}</td>
            <td>{{ $student->student_statu == 'Present' ? '✔' : '' }}</td>
            <td>
                @if($signatureStudentData)
                    <img src="{{ $signatureStudentData }}" alt="Signature" style="width: 50px; height: auto;">
                @else
                    Aucune signature
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
