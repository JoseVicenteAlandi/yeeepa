<?php

use PHPUnit\Framework\TestCase;
use App\TaskManager;  // Cambia por el namespace y nombre de la clase real

class TaskManagerTest extends TestCase
{
    // Aquí van los métodos de test
    public function testManager()
{
    // 1. ARRANGE: Crear el objeto
    $objeto = new TaskManager();
    
    // 2. ACT: Llamar al método
    $resultado = $objeto->obtenerTareaAleatoria();

    $resultado2 = $objeto->contarTareas();

    $resultado3 = $objeto->filtrarPorEstado('pendiente');

    $resultado4 = $objeto->hayTareasUrgentes();

    $resultado5 = $objeto->buscarPorId(1);

    $resultado6 = $objeto->buscarPorId(999);
    

    $this->assertIsArray($resultado);

    $this->assertArrayHasKey('id', $resultado);

    $this->assertArrayHasKey('titulo', $resultado);

    $this->assertArrayHasKey('estado', $resultado);
    
    $this->assertArrayHasKey('prioridad',  $resultado);

    $this->assertEquals(4, $resultado2);

    $this->assertCount(2, $resultado3);

    $this->assertTrue($resultado4);

    $this->assertIsArray($resultado5);

    $this->assertNull($resultado6);







}

}
