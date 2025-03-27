<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../config/database.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        try {
            $database = new Database();
            $pdo = $database->connect();
            $this->userModel = new User($pdo);
        } catch (Exception $e) {
            die("❌ Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    // 📌 Registrar un usuario
    public function register($fullName, $username, $email, $password, $birthDate, $phone, $gender, $location, $bio, $profilePicture = null, $role = 'user', $isVerified = false)
    {
        // ✅ Validación básica
        if (empty($fullName) || empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios.'];
        }

        // ✅ Si no se subió una imagen, asignar un avatar por defecto
        if (!$profilePicture) {
            $profilePicture = 'default_avatar.png';
        }

        return $this->userModel->register($fullName, $username, $email, $password, $birthDate, $phone, $gender, $location, $bio, $profilePicture, $role, $isVerified);
    }

    // 📌 Iniciar sesión
    public function login($email, $password)
    {
        $result = $this->userModel->login($email, $password);

        if ($result['success']) {
            // ✅ Guardar datos en la sesión
            session_start();
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['email'] = $result['user']['email'];
            $_SESSION['role'] = $result['user']['role'];
        }

        return $result;
    }

    // 📌 Actualizar perfil
    public function updateProfile($userId, $fullName, $username, $email, $bio, $profilePicture = null)
    {  
        return $this->userModel->updateProfile($userId, $fullName, $username, $email, $bio, $profilePicture);
    }

    // 📌 Cambiar contraseña
    public function changePassword($userId, $newPassword)
    {
        return $this->userModel->changePassword($userId, $newPassword);
    }

    // 📌 Verificar usuario
    public function verifyUser($userId)
    {
        return $this->userModel->verifyUser($userId);
    }

    // 📌 Obtener todos los usuarios
    public function getAllUsers()
    {
        return $this->userModel->getAllUsers();
    }

    // 📌 Eliminar usuario
    public function deleteUser($userId)
    {
        return $this->userModel->deleteUser($userId);
    }
}
