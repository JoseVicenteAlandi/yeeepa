<?php
require_once 'vendor/autoload.php';

use App\TaskManager;

$manager = new TaskManager();
$tareaAleatoria = $manager->obtenerTareaAleatoria();
$totalTareas = $manager->contarTareas();
$tareasUrgentes = $manager->hayTareasUrgentes();
$tareasPendientes = $manager->filtrarPorEstado('pendiente');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskManager - Gestión de Tareas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-box {
            background: #007bff;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box.urgente {
            background: #dc3545;
        }
        .stat-box.ok {
            background: #28a745;
        }
        .tarea {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .prioridad-alta { border-left-color: #dc3545; }
        .prioridad-media { border-left-color: #ffc107; }
        .prioridad-baja { border-left-color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 TaskManager</h1>
        
        <div class="stats">
            <div class="stat-box">
                <strong><?php echo $totalTareas; ?></strong><br>
                Total Tareas
            </div>
            <div class="stat-box <?php echo $tareasUrgentes ? 'urgente' : 'ok'; ?>">
                <strong><?php echo $tareasUrgentes ? '⚠️ SÍ' : '✅ NO'; ?></strong><br>
                Tareas Urgentes
            </div>
            <div class="stat-box">
                <strong><?php echo count($tareasPendientes); ?></strong><br>
                Pendientes
            </div>
        </div>

        <h2>🎲 Tarea Aleatoria</h2>
        <div class="tarea prioridad-<?php echo $tareaAleatoria['prioridad']; ?>">
            <strong><?php echo htmlspecialchars($tareaAleatoria['titulo']); ?></strong><br>
            Estado: <?php echo $tareaAleatoria['estado']; ?> | 
            Prioridad: <?php echo $tareaAleatoria['prioridad']; ?>
        </div>

        <h2>📝 Tareas Pendientes</h2>
        <?php foreach ($tareasPendientes as $tarea): ?>
        <div class="tarea prioridad-<?php echo $tarea['prioridad']; ?>">
            <strong><?php echo htmlspecialchars($tarea['titulo']); ?></strong><br>
            Prioridad: <?php echo $tarea['prioridad']; ?>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>