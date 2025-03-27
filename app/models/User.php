<?php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 📌 Registrar un usuario
    public function register($full_name, $username, $email, $password, $birth_date, $phone, $gender, $location, $bio, $profile_picture, $role, $is_verified)
    {
        // Verificar si el correo ya está registrado
        if ($this->getUserByEmail($email)) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }

        $sql = "INSERT INTO users (full_name, username, email, password, birth_date, phone, gender, location, bio, profile_picture, role, is_verified) 
                VALUES (:full_name, :username, :email, :password, :birth_date, :phone, :gender, :location, :bio, :profile_picture, :role, :is_verified)";
        
        $stmt = $this->pdo->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $is_verified = $is_verified ? 1 : 0; // Asegurar que solo almacene 1 o 0

        $success = $stmt->execute([
            ':full_name' => $full_name,
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':birth_date' => $birth_date,
            ':phone' => $phone,
            ':gender' => $gender,
            ':location' => $location,
            ':bio' => $bio,
            ':profile_picture' => $profile_picture,
            ':role' => $role,
            ':is_verified' => $is_verified
        ]);

        if ($success) {
            return ['success' => true, 'user_id' => $this->pdo->lastInsertId()];
        } else {
            return ['success' => false, 'message' => 'Error al registrar usuario'];
        }
    }

    // 📌 Iniciar sesión
    public function login($email, $password)
    {
        $sql = "SELECT id, username, email, role, is_verified, password FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Eliminar la contraseña antes de devolver los datos
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Credenciales incorrectas'];
    }

    // 📌 Obtener usuario por email
    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 📌 Obtener usuario por ID
    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 📌 Actualizar perfil
    public function updateProfile($id, $full_name, $username, $email, $bio, $profile_picture)
    {
        if (!$this->getUserById($id)) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        $sql = "UPDATE users SET full_name = :full_name, username = :username, email = :email, bio = :bio, profile_picture = :profile_picture WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            ':id' => $id,
            ':full_name' => $full_name,
            ':username' => $username,
            ':email' => $email,
            ':bio' => $bio,
            ':profile_picture' => $profile_picture
        ]);

        if ($success) {
            return ['success' => true, 'message' => 'Perfil actualizado'];
        }
        return ['success' => false, 'message' => 'Error al actualizar perfil'];
    }

    // 📌 Cambiar contraseña
    public function changePassword($id, $newPassword)
    {
        if (!$this->getUserById($id)) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        if ($stmt->execute([':id' => $id, ':password' => $hashedPassword])) {
            return ['success' => true, 'message' => 'Contraseña actualizada'];
        }
        return ['success' => false, 'message' => 'Error al actualizar la contraseña'];
    }

    // 📌 Verificar usuario
    public function verifyUser($id)
    {
        if (!$this->getUserById($id)) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        $sql = "UPDATE users SET is_verified = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([':id' => $id])) {
            return ['success' => true, 'message' => 'Usuario verificado'];
        }
        return ['success' => false, 'message' => 'Error al verificar usuario'];
    }

    // 📌 Obtener todos los usuarios
    public function getAllUsers()
    {
        $sql = "SELECT id, full_name, username, email, role, is_verified FROM users";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📌 Eliminar usuario
    public function deleteUser($id)
    {
        if (!$this->getUserById($id)) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([':id' => $id])) {
            return ['success' => true, 'message' => 'Usuario eliminado'];
        }
        return ['success' => false, 'message' => 'Error al eliminar usuario'];
    }
}
