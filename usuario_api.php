<?php
// Configuración de la conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "plataforma_trabajo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Revisar la acción que se pide
$accion = $_REQUEST['accion'] ?? '';

if ($accion === "insertar") {
    // INSERTAR USUARIO
    $nombre = $_POST['nombre'];
    $pass = $_POST['password'];

    $sql = "INSERT INTO usuarios (nombre, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $pass);

    if ($stmt->execute()) {
        // Redirigir a index.html (se usará fetch para cargar los datos)
        header("Location: index.html");
        exit();
    } else {
        echo "Error al guardar: " . $stmt->error;
    }
}
elseif ($accion === "listar") {
    // LISTAR USUARIOS
    $sql = "SELECT id, nombre FROM usuarios";
    $resultado = $conn->query($sql);

    $usuarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }

    // Devolver como JSON
    header('Content-Type: application/json');
    echo json_encode($usuarios);
    exit();
}
elseif ($accion === "login") {
    $nombre = $_POST['nombre'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($fila = $resultado->fetch_assoc()) {
        // Validar contraseña
        if($fila['password'] === $pass) { // si luego usan password_hash, cambiar a password_verify
            echo json_encode(['success' => true, 'nombre' => $fila['nombre']]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

$conn->close();
?>