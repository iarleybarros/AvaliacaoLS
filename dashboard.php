<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
       body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #d4e3f9; /* Azul claro */
        font-family: Arial, sans-serif;
    }

    .form-container {
        background-color: #ffffff; /* Branco */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
        width: 350px;
        text-align: center;
        border: 2px solid #2c3e50; /* Azul mais escuro */
    }

    .form-container h1,
    .form-container h2 {
        color: #2c3e50; /* Azul mais escuro */
        margin-bottom: 20px;
    }

    .form-container form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-container label {
        color: #2c3e50; /* Azul mais escuro */
        font-weight: bold;
        margin-bottom: 5px;
        align-self: flex-start;
    }

    .form-container input,
    .form-container select {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #bdc3c7; /* Cinza claro */
        width: 100%;
    }

    .form-container input[type="submit"] {
        background-color: #2c3e50; /* Azul mais escuro */
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .form-container input[type="submit"]:hover {
        background-color: #1a252f; /* Azul escuro */
    }

    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    table tr {
        border-bottom: 1px solid #bdc3c7; /* Cinza claro */
    }

    table tr:last-child {
        border-bottom: none;
    }

    table td {
        padding: 10px;
        color: #2c3e50; /* Azul mais escuro */
        text-align: left;
    }

    table td button,
    table td input[type="submit"] {
        background-color: #2c3e50; /* Azul mais escuro */
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    table td button:hover,
    table td input[type="submit"]:hover {
        background-color: #1a252f; /* Azul escuro */
    }

    #formEdit {
        display: none;
        margin-top: 20px;
    }

    #logout {
        margin-top: 20px;
        color: #2c3e50; /* Azul mais escuro */
        text-decoration: none;
        font-weight: bold;
    }

    #logout:hover {
        text-decoration: underline;
    }

    .no-tasks {
        color: #2c3e50; /* Azul mais escuro */
        font-style: italic;
    }

    .logo-container img {
        width: 100px;
        height: auto;
        margin-bottom: 20px;
    }

    </style>
    <script>
        function preencherFormularioEdicao(id, nome, descricao) {
            document.getElementById('formEdit').style.display = 'block';
            document.getElementById('nomeEdit').value = nome;
            document.getElementById('descricaoEdit').value = descricao;
            document.getElementById('idEdit').value = id;
        }

        function validarFormulario(event, formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('input[type="text"]');
            for (let input of inputs) {
                if (input.value.trim() === '') {
                    alert('Todos os campos devem ser preenchidos.');
                    event.preventDefault();
                    return false;
                }
            }
            return true;
        }
    </script>
</head>
<body>

<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sistema");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT nivel_acesso FROM perfil WHERE id = (SELECT perfil_id FROM usuario WHERE username = '" . $_SESSION["username"] . "')";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nivel_acesso = $row["nivel_acesso"];
} else {
    echo "Erro ao obter o nível de acesso do usuário";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["idEdit"])) {
    $id = $_POST["idEdit"];
    $nome = isset($_POST["nomeEdit"]) && !empty($_POST["nomeEdit"]) ? $_POST["nomeEdit"] : null;
    $descricao = isset($_POST["descricaoEdit"]) && !empty($_POST["descricaoEdit"]) ? $_POST["descricaoEdit"] : null;

    $sql = "SELECT nome, descricao FROM tarefas WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nome = $nome !== null ? $nome : $row["nome"];
        $descricao = $descricao !== null ? $descricao : $row["descricao"];
    }

    $sql = "UPDATE tarefas SET nome='$nome', descricao='$descricao' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    } else {
        echo "Erro ao atualizar a tarefa: " . $conn->error;
    }
}
?>

<div class="form-container">
    <div class="logo-container">
        <img src="https://logos-world.net/wp-content/uploads/2023/03/PS2-Logo.png" alt="ps2 Logo">
    </div>
    <?php
    if ($nivel_acesso == "Administrador") {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nome"]) && isset($_POST["descricao"])) {
            $nome = $_POST["nome"];
            $descricao = $_POST["descricao"];
            $sql = "INSERT INTO tarefas (nome, descricao) VALUES ('$nome', '$descricao')";
            if ($conn->query($sql) === TRUE) {
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit;
            } else {
                echo "Erro: " . $sql . "<br>" . $conn->error;
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
            $id = $_POST["id"];
            $sql = "DELETE FROM tarefas WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit;
            } else {
                echo "Erro ao deletar a tarefa: " . $conn->error;
            }
        }

        echo '
            <h1>Formulário de Jogos</h1>
            <form id="createForm" action="" method="post" onsubmit="return validarFormulario(event, \'createForm\')">
                <h2>Registrar Jogo</h2>
                <label for="nome">Nome do jogo:</label>
                <input type="text" id="nome" name="nome">
                <label for="descricao">Descricao:</label>
                <input type="text" id="descricao" name="descricao">
                <input type="submit" value="Criar">
            </form>

            <form id="formEdit" action="" method="post" onsubmit="return validarFormulario(event, \'formEdit\')">
                <h2>Atualizar Jogo</h2>
                <input type="hidden" id="idEdit" name="idEdit">
                <label for="nomeEdit">Jogo:</label>
                <input type="text" id="nomeEdit" name="nomeEdit">
                <label for="descricaoEdit">Descrição:</label>
                <input type="text" id="descricaoEdit" name="descricaoEdit">
                <input type="submit" value="Atualizar">
            </form>
        ';

        $sql = "SELECT id, nome, descricao FROM tarefas";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<table>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>Id: " . $row["id"] . "</td>";
                echo "<td>Partida: " . $row["nome"] . "</td>";
                echo "<td>Local: " . $row["descricao"] . "</td>";
                echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='id' value='" . $row["id"] . "'>
                            <input type='submit' value='Deletar'>
                        </form>
                      </td>";
                echo "<td>
                        <button onclick='preencherFormularioEdicao(\"" . $row["id"] . "\", \"" . $row["nome"] . "\", \"" . $row["descricao"] . "\")'>Editar</button>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-tasks'>Nenhum jogo encontrada</p>";
        }
    } else if ($nivel_acesso == "Cliente") {
        $sql = "SELECT nome, descricao FROM tarefas";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "Partida: " . $row["nome"] . " - Local: " . $row["descricao"] . "<br>";
            }
        } else {
            echo "<p class='no-tasks'>Nenhuma jogo encontrad0</p>";
        }
    } else {
        echo "<p>Você não tem permissão para acessar esta página.</p>";
    }
    echo "<a id='logout' href='logout.php'>Sair</a>";
    $conn->close();
    ?>
</div>
</body>
</html>