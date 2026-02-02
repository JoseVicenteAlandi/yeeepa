# SOLUCIONES - EXAMEN DE PRUEBA DESPLIEGUE

**⚠️ NO MIRES ESTO HASTA HABER INTENTADO EL EXAMEN**

---

## PARTE A: TESTS (TaskManagerTest.php)

```php
<?php

use PHPUnit\Framework\TestCase;
use App\TaskManager;

class TaskManagerTest extends TestCase
{
    // Test 1: Verificar que obtenerTareaAleatoria devuelve un array
    public function testObtenerTareaAleatoriaDevuelveArray()
    {
        $manager = new TaskManager();
        $resultado = $manager->obtenerTareaAleatoria();
        
        $this->assertIsArray($resultado);
    }

    // Test 2: Verificar que el array tiene las claves correctas
    public function testTareaTieneClavesCorrectas()
    {
        $manager = new TaskManager();
        $resultado = $manager->obtenerTareaAleatoria();
        
        $this->assertArrayHasKey('id', $resultado);
        $this->assertArrayHasKey('titulo', $resultado);
        $this->assertArrayHasKey('estado', $resultado);
        $this->assertArrayHasKey('prioridad', $resultado);
    }

    // Test 3: Verificar que contarTareas devuelve 4
    public function testContarTareas()
    {
        $manager = new TaskManager();
        
        $this->assertEquals(4, $manager->contarTareas());
    }

    // Test 4: Verificar filtrarPorEstado con 'pendiente'
    public function testFiltrarPorEstadoPendiente()
    {
        $manager = new TaskManager();
        $resultado = $manager->filtrarPorEstado('pendiente');
        
        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    // Test 5: Verificar hayTareasUrgentes
    public function testHayTareasUrgentes()
    {
        $manager = new TaskManager();
        
        $this->assertTrue($manager->hayTareasUrgentes());
    }

    // Test 6: Verificar buscarPorId
    public function testBuscarPorIdExistente()
    {
        $manager = new TaskManager();
        $resultado = $manager->buscarPorId(1);
        
        $this->assertIsArray($resultado);
        $this->assertEquals(1, $resultado['id']);
    }

    public function testBuscarPorIdInexistente()
    {
        $manager = new TaskManager();
        $resultado = $manager->buscarPorId(999);
        
        $this->assertNull($resultado);
    }
}
```

---

## PARTE B: DESPLIEGUE SIN DOCKER

### composer.json

```json
{
    "require-dev": {
        "phpunit/phpunit": "^11.4"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src"
        }
    }
}
```

### .gitignore

```
vendor/
composer.phar
```

### .github/workflows/despliegue.yml

```yaml
name: CI/CD Pipeline for TaskManager

on:
  push:
    branches:
      - main

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v2

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'

      - name: Install dependencies
        run: composer install

      - name: Run tests
        run: ./vendor/bin/phpunit tests

  deploy:
    runs-on: ubuntu-latest
    needs: test
    steps:
      - name: Checkout code
        uses: actions/checkout@v2

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'

      - name: Install dependencies
        run: composer install

      - name: Deploy to server
        env:
          SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
          HOST: ${{ secrets.HOST }}
          USERNAME: ${{ secrets.USERNAME }}
          DEPLOY_PATH: ${{ secrets.DEPLOY_PATH }}
        run: |
          mkdir -p ~/.ssh
          echo "$SSH_PRIVATE_KEY" > ~/.ssh/id_rsa
          chmod 600 ~/.ssh/id_rsa
          ssh -o StrictHostKeyChecking=no $USERNAME@$HOST "mkdir -p $DEPLOY_PATH"
          rsync -avz --delete --no-t --exclude 'tests' --exclude '.git' . $USERNAME@$HOST:$DEPLOY_PATH
          ssh -o StrictHostKeyChecking=no $USERNAME@$HOST "cd $DEPLOY_PATH && composer install --no-dev --optimize-autoloader"
          ssh -o StrictHostKeyChecking=no $USERNAME@$HOST "sudo systemctl restart apache2"
```

### Secretos de GitHub necesarios:

| Nombre | Valor |
|--------|-------|
| SSH_PRIVATE_KEY | Contenido completo del archivo .pem |
| HOST | IP elástica de AWS |
| USERNAME | ubuntu |
| DEPLOY_PATH | /var/www/html/taskmanager |

---

## PARTE C: DESPLIEGUE CON DOCKER

### php/Dockerfile

```dockerfile
FROM php:8.2-apache

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos de la aplicación
COPY . /var/www/html/

# Instalar dependencias (sin dev)
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Exponer puerto
EXPOSE 80
```

### php/composer.json

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src"
        }
    }
}
```

### compose.yml (en la raíz)

```yaml
services:
  app:
    image: TU_USUARIO_DOCKERHUB/taskmanager:v1
    container_name: taskmanager-app
    ports:
      - "8080:80"
    restart: unless-stopped
```

**⚠️ IMPORTANTE:** Cambia `TU_USUARIO_DOCKERHUB` por tu usuario real de Docker Hub.

### .github/workflows/deploy.yml

```yaml
name: Deploy TaskManager with Docker

on:
  push:
    branches:
      - main

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Log in to DockerHub
        uses: docker/login-action@v2
        with:
          username: ${{ secrets.DOCKER_USERNAME }}
          password: ${{ secrets.DOCKER_PASSWORD }}

      - name: Build and push Docker image
        run: |
          cd php
          docker build -t ${{ secrets.DOCKER_USERNAME }}/taskmanager:v1 .
          docker push ${{ secrets.DOCKER_USERNAME }}/taskmanager:v1

      - name: Transfer compose.yml to remote server
        uses: appleboy/scp-action@master
        with:
          host: ${{ secrets.REMOTE_HOST }}
          username: ${{ secrets.REMOTE_USER }}
          key: ${{ secrets.REMOTE_KEY }}
          source: ./compose.yml
          target: ~/deploy/

      - name: Deploy with Docker Compose
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.REMOTE_HOST }}
          username: ${{ secrets.REMOTE_USER }}
          key: ${{ secrets.REMOTE_KEY }}
          script: |
            cd ~/deploy
            sudo chmod 666 /var/run/docker.sock
            docker compose down || true
            docker compose pull
            docker compose up -d
```

### Secretos de GitHub necesarios:

| Nombre | Valor |
|--------|-------|
| DOCKER_USERNAME | Tu usuario de Docker Hub |
| DOCKER_PASSWORD | Tu contraseña de Docker Hub |
| REMOTE_HOST | IP elástica de AWS |
| REMOTE_USER | ubuntu |
| REMOTE_KEY | Contenido completo del archivo .pem |

### .gitignore

```
vendor/
.env
*.log
```

---

## ESTRUCTURA FINAL DE CARPETAS

### SIN Docker:
```
taskmanager/
├── .github/
│   └── workflows/
│       └── despliegue.yml
├── src/
│   └── TaskManager.php
├── tests/
│   └── TaskManagerTest.php
├── .gitignore
├── composer.json
└── index.php
```

### CON Docker:
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

## VERIFICACIÓN

### Sin Docker:
```
http://TU_IP_ELASTICA/taskmanager/index.php
```

### Con Docker:
```
http://TU_IP_ELASTICA:8080
```

---

## ERRORES COMUNES A EVITAR

1. **Olvidar `composer dump-autoload`** después de crear los archivos
2. **Namespace incorrecto** - Verificar que sea `App` en todos los archivos
3. **Nombre de rama** - Verificar si es `main` o `master`
4. **Docker Hub en minúsculas** - El nombre del repo debe ser en minúsculas
5. **Permisos en EC2** - Ejecutar `sudo chown -R ubuntu:ubuntu /var/www/html`
6. **Puertos no abiertos** - Verificar grupo de seguridad en AWS (22, 80, 8080)

**¡Buena suerte con tu práctica!**
