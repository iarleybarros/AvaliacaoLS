<?php

session_start();


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    
    $conn = new mysqli("localhost", "root", "", "sistema");

    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $sql = "SELECT id FROM usuario WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        header("location: dashboard.php");
    } else {
        $error = "Usuário ou senha incorretos";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
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

        .form-container input {
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
            margin-top: 10px;
            display: inline-block;
        }

        .form-container a:hover {
            color: #1abc9c; /* Verde turquesa ao passar o mouse */
        }

        .form-container p {
            color: #e74c3c; /* Vermelho mais escuro para mensagens de erro */
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <a href="registro.php">Registrar</a>
        <?php
        if (!empty($error)) {
            echo "<p>" . $error . "</p>";
        }
        ?>
    </div>
</body>

</html>
