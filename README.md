# 🚀 Mars Rover – Laravel + Vue + Docker

App full stack para simular el movimiento de un rover en una cuadrícula con obstáculos.  
Está dividida en un backend REST hecho con **Laravel 12** y un frontend interactivo en **Vue 3**, ambos corriendo con Docker.

---

## 🧩 Tecnologías utilizadas

- **Backend:** Laravel 12 (solo API)
- **Frontend:** Vue 3 + Vite
- **Docker:** Docker Compose con servicios para backend, frontend y base de datos
- **Base de datos:** MySQL 8
- **Comunicación:** Peticiones HTTP con Axios

---

## 📁 Estructura del proyecto

```
mars-rover/
├── mars-rover-backend/      → Laravel (API REST)
├── mars-rover-frontend/     → Vue 3 (interfaz de usuario)
├── docker-compose.yml       → Orquestador de servicios
```

---

## ⚙️ Requisitos previos

- Git
- Docker Desktop

---

## 🧭 Cómo arrancar el proyecto desde cero

1. Clonar el repositorio:

```bash
git clone git@github.com:dannycodev/mars-rover-mission.git
cd mars-rover
```

2. Copiar el archivo de entorno de Laravel:

```bash
cp mars-rover-backend/.env.example mars-rover-backend/.env
```

3. Construir y levantar todos los contenedores:

```bash
docker compose up -d --build
```

4. Ejecutar las migraciones del backend:

```bash
docker exec -it mars-rover-backend-1 php artisan migrate
```

---

## 🌐 Accesos

- **Frontend (Vue):** [http://localhost:5173](http://localhost:5173)
- **API (Laravel):**  
  POST → `http://localhost:8000/api/rover/move`

---

## 🔁 Ejemplo de petición a la API

```json
POST http://localhost:8000/api/rover/move

{
  "commands": "FFRFF",
  "initialPosition": {
    "x": 0,
    "y": 0,
    "direction": "N"
  },
  "obstacles": [
    { "x": 3, "y": 2 }
  ]
}
```

---

## 🧹 Comandos útiles

- Apagar contenedores:

```bash
docker compose down
```

- Ver logs del contenedor:

```bash
docker logs mars-rover-frontend-1
docker logs mars-rover-backend-1
```

---

## 🔒 Exclusiones (ya gestionadas por carpeta en `.gitignore`)

Cada subproyecto (`backend` y `frontend`) tiene su propio `.gitignore` que excluye los archivos innecesarios:

### Backend (Laravel):
- `.env`
- `vendor/`
- `storage/`, `public/build`, `*.cache`, `.idea/`, `.vscode/`

### Frontend (Vue):
- `node_modules/`
- `dist/`, `*.log`, `.vscode/`, `.idea/`

Esto mantiene limpio el repositorio y protege archivos sensibles o generados automáticamente.


## ✍️ Autor

Desarrollado por Daniel Alcocer.  
Este proyecto fue creado como práctica técnica
