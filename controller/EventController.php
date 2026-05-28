<?php
session_start();


class EventController
{
    private $conn;
    private $db_host = 'localhost';
    private $db_user = 'root';
    private $db_pass = '';
    private $db_name = 'mp0487_knockoutzone';

    public function __construct()
    {
        $this->conn = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: /KnockOut_Zone/controller/login.php");
            exit();
        }
    }

    public function create(): void
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Método no permitido");
            }

            $title = htmlspecialchars(trim($_POST['title']));
            $datetime = $_POST['datetime'];
            $location = htmlspecialchars(trim($_POST['location']));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $created_by = $_SESSION['user_id'];

            if (empty($title) || empty($datetime) || empty($location)) {
                throw new Exception("Todos los campos obligatorios deben completarse");
            }

            $formatted_datetime = date('Y-m-d H:i:s', strtotime($datetime));
            $image_path = null;

            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = $this->uploadImage($_FILES['event_image']);
            }

            $stmt = $this->conn->prepare("INSERT INTO events (title, event_date, location, description, image_path, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conn->error);
            }

            $stmt->bind_param("sssssi", $title, $formatted_datetime, $location, $description, $image_path, $created_by);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Evento creado correctamente.";
                header("Location: ../view/events.php");
                exit();
            } else {
                throw new Exception("Error al ejecutar: " . $stmt->error);
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: ../view/events.php?error=1");
            exit();
        }
    }

    public function delete(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            if (!isset($_POST['event_id'])) {
                throw new Exception("Solicitud inválida");
            }

            $event_id = intval($_POST['event_id']);

            $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->conn->error);
            }

            $stmt->bind_param("ii", $event_id, $_SESSION['user_id']);

            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar evento: " . $stmt->error);
            }

            $_SESSION['success_message'] = "Evento eliminado correctamente.";
            header("Location: ../view/events.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: ../view/events.php?error=1");
            exit();
        }
    }

    public function update(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $event_id = intval($_POST['event_id']);
            $title = htmlspecialchars(trim($_POST['title']));
            $datetime = htmlspecialchars(trim($_POST['datetime']));
            $location = htmlspecialchars(trim($_POST['location']));
            $description = htmlspecialchars(trim($_POST['description']));
            $user_id = $_SESSION['user_id'];

            // Verify that the event belongs to the current user
            $check = $this->conn->query("SELECT * FROM events WHERE id = $event_id AND created_by = $user_id");
            if ($check->num_rows === 0) {
                throw new Exception("No tienes permiso para actualizar este evento.");
            }

            $image_path_sql = '';
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = $this->uploadImage($_FILES['event_image']);
                $image_path_escaped = $this->conn->real_escape_string($image_path);
                $image_path_sql = ", image_path = '$image_path_escaped'";
            }

            $title_escaped = $this->conn->real_escape_string($title);
            $datetime_escaped = $this->conn->real_escape_string($datetime);
            $location_escaped = $this->conn->real_escape_string($location);
            $description_escaped = $this->conn->real_escape_string($description);

            $sql = "UPDATE events SET 
                        title = '$title_escaped',
                        event_date = '$datetime_escaped',
                        location = '$location_escaped',
                        description = '$description_escaped'
                        $image_path_sql
                    WHERE id = $event_id AND created_by = $user_id";

            if ($this->conn->query($sql)) {
                $_SESSION['success_message'] = "Evento actualizado correctamente.";
                header("Location: ../view/events.php");
                exit();
            } else {
                throw new Exception("Error al actualizar el evento: " . $this->conn->error);
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: ../view/events.php?error=1");
            exit();
        }
    }

    private function uploadImage($file): ?string
    {
        $upload_dir = '../images/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) {
            throw new Exception("Solo se permiten imágenes JPG, PNG o GIF");
        }

        $file_name = uniqid() . '.' . $ext;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return 'images/' . $file_name;
        }

        throw new Exception("Error al guardar la imagen");
    }

    public function __destruct()
    {
        if (isset($this->conn)) {
            $this->conn->close();
        }
    }
}

// Get action from GET parameter
$action = $_GET['action'] ?? '';

// Create controller instance and call appropriate method
$controller = new EventController();

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'update':
        $controller->update();
        break;
    default:
        $_SESSION['error_message'] = "Acción no válida";
        header("Location: ../view/events.php?error=1");
        exit();
}
?>
