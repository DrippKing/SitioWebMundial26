<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Hash de Contraseñas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #45a049;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            border-radius: 5px;
            word-wrap: break-word;
        }
        .result strong {
            color: #2e7d32;
        }
        .info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .code {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Generador de Hash de Contraseñas</h1>
        
        <div class="info">
            <strong>💡 Información:</strong><br>
            Esta herramienta genera hashes seguros para las contraseñas de usuarios usando PHP's password_hash().
            Usa estos hashes al insertar usuarios en la base de datos.
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="password">Contraseña a hashear:</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Ingresa la contraseña">
            </div>
            
            <div class="form-group">
                <label for="show_password">
                    <input type="checkbox" id="show_password" onclick="togglePassword()">
                    Mostrar contraseña
                </label>
            </div>
            
            <button type="submit">Generar Hash</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
            $password = $_POST['password'];
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            echo '<div class="result">';
            echo '<strong>✅ Hash generado:</strong><br>';
            echo '<div class="code">' . htmlspecialchars($hash) . '</div>';
            echo '<br><strong>📋 SQL de ejemplo:</strong>';
            echo '<div class="code">';
            echo "INSERT INTO usuarios (nombre, apellido1, usuario, email, contrasena, foto_perfil) VALUES<br>";
            echo "('Nombre', 'Apellido', 'usuario1', 'email@test.com', '" . htmlspecialchars($hash) . "', 'default.jpg');";
            echo '</div>';
            echo '<br><small>💡 La contraseña original para login sería: <strong>' . htmlspecialchars($password) . '</strong></small>';
            echo '</div>';
        }
        ?>

        <div style="margin-top: 30px; text-align: center; color: #999; font-size: 14px;">
            <p>🔒 Los hashes son únicos y seguros usando bcrypt</p>
            <p><a href="test_chat.php">← Volver al Test del Chat</a></p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const checkbox = document.getElementById('show_password');
            passwordInput.type = checkbox.checked ? 'text' : 'password';
        }
    </script>
</body>
</html>
