<?php
session_start();


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST["reg_username"];
    $password = $_POST["reg_password"];
    $nivel_acesso = intval($_POST["nivel_acesso"]);

    
    $conn = new mysqli("localhost", "root", "", "sistema");

    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $check_username = "SELECT id FROM usuario WHERE username = '$username'";
    $result = $conn->query($check_username);
    if ($result->num_rows > 0) {
        $error = "Erro: Nome de usuário já está em uso.";
    } else {
        
        $sql = "INSERT INTO usuario (username, password, perfil_id) VALUES ('$username', '$password', '$nivel_acesso')";
        if ($conn->query($sql) === TRUE) {
            $success = "Usuário registrado com sucesso!";
        } else {
            $error = "Erro: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Registro</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #ecf0f1; /* Azul claro */
            font-family: Arial, sans-serif;
        }

        .form-container {
            background-color: #ffffff; /* Branco */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
            border: 2px solid #3498db; /* Azul mais escuro para a borda */
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50; /* Azul escuro para o texto */
        }

        .form-container input,
        .form-container select {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #bdc3c7; /* Cinza claro para a borda dos campos */
            font-size: 16px;
        }

        .form-container input[type="submit"] {
            background-color: #3498db; /* Azul mais escuro */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container input[type="submit"]:hover {
            background-color: #2980b9; /* Azul mais escuro ao passar o mouse */
        }

        .form-container a {
            color: #3498db; /* Azul mais escuro */
            text-decoration: none;
        }

        .form-container a:hover {
            color: #1abc9c; /* Verde turquesa ao passar o mouse */
        }

        .error-message {
            color: #e74c3c; /* Vermelho mais escuro para mensagens de erro */
            margin-bottom: 10px;
        }

        .success-message {
            color: #27ae60; /* Verde mais escuro para mensagens de sucesso */
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="reg_username">Usuário:</label>
            <input type="text" id="reg_username" name="reg_username" required>
            <label for="reg_password">Senha:</label>
            <input type="password" id="reg_password" name="reg_password" required>
            <label for="nivel_acesso">Nível de Acesso:</label>
            <select id="nivel_acesso" name="nivel_acesso" required>
                <option value="1">Administrador</option>
                <option value="2">Cliente</option>
            </select>
            <input type="submit" name="register" value="Registrar">
        </form>
        <?php if (!empty($error)) : ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (!empty($success)) : ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php endif; ?>
        <a href="login.php">Fazer login</a>
    </div>
</body>

</html>
