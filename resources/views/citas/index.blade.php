<!-- resources/views/citas/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas</title>
</head>
<body>
    <h1>Lista de Citas</h1>
    <ul>
        @foreach($citas as $cita)
            <li>{{ $cita->fecha_hora }} - Paciente: {{ $cita->paciente_id }} - Terapeuta: {{ $cita->terapeuta_id }}</li>
        @endforeach
    </ul>
</body>
</html>
