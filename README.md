# CRUD PHP POO MVC

Este es un proyecto de un CRUD (Crear, Leer, Actualizar, Eliminar) desarrollado en PHP utilizando el paradigma de Programación Orientada a Objetos (POO) y el patrón de arquitectura Modelo-Vista-Controlador (MVC).

## 🚀 Estado del Proyecto

**⚠️ En Desarrollo:** Este proyecto se encuentra actualmente en desarrollo. Muchas de las funcionalidades principales aún no están implementadas o están incompletas.

## ✨ Características (Actuales y Planeadas)

*   **Gestión de Usuarios:**
    *   [x] Listar usuarios
    *   [x] Crear nuevos usuarios
    *   [ ] Actualizar usuarios
    *   [ ] Eliminar usuarios
    *   [ ] Buscar usuarios
*   **Sistema de Login:**
    *   [x] Inicio de sesión
    *   [ ] Cierre de sesión
    *   [ ] Protección de rutas
*   **Estructura MVC:**
    *   **Modelos:** Para la interacción con la base de datos.
    *   **Vistas:** Para la presentación de la interfaz de usuario.
    *   **Controladores:** Para manejar la lógica de la aplicación.

## 🛠️ Tecnologías Utilizadas

*   **Backend:** PHP
*   **Frontend:** HTML, CSS, JavaScript
*   **Framework CSS:** Bulma
*   **Base de Datos:** MySQL (o cualquier otra compatible con PDO)
*   **Librerías:**
    *   SweetAlert2 para alertas amigables.

## 🏁 Cómo Empezar

A continuación, se detallan los pasos para configurar el proyecto en un entorno de desarrollo local.

### Prerrequisitos

*   Un servidor web local (XAMPP, WAMP, Laragon, etc.).
*   PHP 7.4 o superior.
*   MySQL o MariaDB.

### Instalación

1.  **Clona el repositorio:**
    ```bash
    git clone https://URL-DEL-REPOSITORIO.git
    ```
2.  **Navega al directorio del proyecto:**
    ```bash
    cd NOMBRE-DEL-DIRECTORIO
    ```
3.  **Configura la base de datos:**
    *   Importa el archivo `.sql` (si se proporciona) a tu gestor de base de datos (ej. phpMyAdmin).
    *   Configura los detalles de la conexión a la base de datos en el archivo `Config/Server.php`.

4.  **Inicia tu servidor web** y accede al proyecto desde tu navegador (ej. `http://localhost/NOMBRE-DEL-DIRECTORIO/`).

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Si deseas mejorar este proyecto, por favor sigue estos pasos:

1.  Haz un "Fork" del repositorio.
2.  Crea una nueva rama para tu funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3.  Realiza tus cambios y haz "commit" (`git commit -m 'Añade nueva funcionalidad'`).
4.  Haz "push" a tu rama (`git push origin feature/nueva-funcionalidad`).
5.  Abre un "Pull Request".

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles (si existe).
