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