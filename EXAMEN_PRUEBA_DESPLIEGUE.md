# EXAMEN FINAL - DESPLIEGUE DE APLICACIONES WEB

## Datos del examen
- **Duración:** 2 horas 30 minutos
- **Puntuación:** 10 puntos
- **Material permitido:** Guías propias, apuntes, documentación online

---

## ENUNCIADO

Una empresa de gestión de tareas te ha contratado para desplegar su aplicación "TaskManager". La aplicación permite gestionar tareas con diferentes estados y prioridades.

Te proporcionan el código de la clase principal y necesitan que:

1. **PARTE A (3 puntos):** Crees los tests unitarios con PHPUnit
2. **PARTE B (3.5 puntos):** Despliegues la aplicación SIN Docker usando GitHub Actions
3. **PARTE C (3.5 puntos):** Despliegues la aplicación CON Docker usando GitHub Actions

---

## CÓDIGO PROPORCIONADO

### Archivo: src/TaskManager.php

```php
<?php
namespace App;
    
class TaskManager
{
    private $tareas;

    public function __construct()
    {
        $this->tareas = [
            [
                'id' => 1,
                'titulo' => 'Revisar código',
                'estado' => 'pendiente',
                'prioridad' => 'alta'
            ],
            [
                'id' => 2,
                'titulo' => 'Escribir documentación',
                'estado' => 'en_progreso',
                'prioridad' => 'media'
            ],
            [
                'id' => 3,
                'titulo' => 'Hacer backup',
                'estado' => 'completada',
                'prioridad' => 'baja'
            ],
            [
                'id' => 4,
                'titulo' => 'Actualizar dependencias',
                'estado' => 'pendiente',
                'prioridad' => 'alta'
            ],
        ];
    }

    /**
     * Devuelve una tarea aleatoria
     * @return array
     */
    public function obtenerTareaAleatoria()
    {
        $indice = array_rand($this->tareas);
        return $this->tareas[$indice];
    }

    /**
     * Cuenta el total de tareas
     * @return int
     */
    public function contarTareas()
    {
        return count($this->tareas);
    }

    /**
     * Filtra tareas por estado
     * @param string $estado (pendiente, en_progreso, completada)
     * @return array
     */
    public function filtrarPorEstado($estado)
    {
        $resultado = [];
        foreach ($this->tareas as $tarea) {
            if ($tarea['estado'] === $estado) {
                $resultado[] = $tarea;
            }
        }
        return $resultado;
    }

    /**
     * Verifica si hay tareas de alta prioridad pendientes
     * @return bool
     */
    public function hayTareasUrgentes()
    {
        foreach ($this->tareas as $tarea) {
            if ($tarea['prioridad'] === 'alta' && $tarea['estado'] === 'pendiente') {
                return true;
            }
        }
        return false;
    }

    /**
     * Busca una tarea por su ID
     * @param int $id
     * @return array|null
     */
    public function buscarPorId($id)
    {
        foreach ($this->tareas as $tarea) {
            if ($tarea['id'] === $id) {
                return $tarea;
            }
        }
        return null;
    }
}
```

### Archivo: index.php (para la parte web)

```php
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
```

---

## PARTE A: CREAR TESTS (3 puntos)

Crea el archivo `tests/TaskManagerTest.php` con tests que verifiquen:

1. **(0.5 pts)** Que `obtenerTareaAleatoria()` devuelve un array
2. **(0.5 pts)** Que el array devuelto tiene las claves: id, titulo, estado, prioridad
3. **(0.5 pts)** Que `contarTareas()` devuelve 4
4. **(0.5 pts)** Que `filtrarPorEstado('pendiente')` devuelve un array con 2 elementos
5. **(0.5 pts)** Que `hayTareasUrgentes()` devuelve true
6. **(0.5 pts)** Que `buscarPorId(1)` devuelve un array y `buscarPorId(999)` devuelve null

---

## PARTE B: DESPLIEGUE SIN DOCKER (3.5 puntos)

Configura el despliegue automático SIN Docker:

1. **(0.5 pts)** Configura el servidor AWS EC2 con Apache y PHP
2. **(0.5 pts)** Configura los secretos en GitHub (SSH_PRIVATE_KEY, HOST, USERNAME, DEPLOY_PATH)
3. **(1.5 pts)** Crea el archivo `despliegue.yml` con:
   - Job de test que ejecute PHPUnit
   - Job de deploy que solo se ejecute si los tests pasan
4. **(1 pt)** Verifica que la aplicación funciona en http://IP_ELASTICA/taskmanager/

---

## PARTE C: DESPLIEGUE CON DOCKER (3.5 puntos)

Configura el despliegue automático CON Docker:

1. **(1 pt)** Crea el `Dockerfile` para la aplicación PHP
2. **(0.5 pts)** Crea el archivo `compose.yml`
3. **(1.5 pts)** Crea el archivo `deploy.yml` con:
   - Build y push de la imagen a Docker Hub
   - Despliegue en el servidor EC2
4. **(0.5 pts)** Verifica que la aplicación funciona en http://IP_ELASTICA:8080

---

## ESTRUCTURA DE CARPETAS ESPERADA

### Para SIN Docker:
```
taskmanager/
├── .github/
│   └── workflows/
│       └── despliegue.yml
├── src/
│   └── TaskManager.php
├── tests/
│   └── TaskManagerTest.php
├── vendor/
├── .gitignore
├── composer.json
├── composer.lock
└── index.php
```

### Para CON Docker:
```
taskmanager-docker/
├── .github/
│   └── workflows/
│       └── deploy.yml
├── php/
│   ├── Dockerfile
│   ├── src/
│   │   └── TaskManager.php
│   ├── composer.json
│   └── index.php
├── .gitignore
└── compose.yml
```

---

## CRITERIOS DE EVALUACIÓN

| Criterio | Puntos |
|----------|--------|
| Tests correctos y completos | 3 |
| Workflow sin Docker funcional | 3.5 |
| Workflow con Docker funcional | 3.5 |
| **TOTAL** | **10** |

**Penalizaciones:**
- Tests que no pasan: -0.5 por cada test fallido
- Errores de sintaxis en YAML: -0.5
- Secretos mal configurados: -0.5
- Aplicación no accesible: -1

---

## NOTAS IMPORTANTES

⚠️ **Recuerda:**
- Ejecutar `composer dump-autoload` después de crear los archivos
- Probar los tests localmente antes de hacer push
- Verificar que la rama sea `main` o `master` según corresponda
- Los nombres de los repositorios en Docker Hub deben ser en minúsculas

**¡Buena suerte!**
