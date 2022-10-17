<?php
//Inicia ou recupera a atual sessão
session_start();

//Inicia o buffer e bloqueia qualquer saída para o navegador para evitar erros de redirecionamento
ob_start();

//Estabelece a conexão à base de dados
include_once 'includes/connect_db.php';

//Recebe os dados do formulário
$data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//Inicializa as variáveis antes do formulário ser submetido para que os dados inseridos pelo utilizador não sejam perdidos na submissão se ocorrer um erro
$email = '';

//Se o formulário for submetido através do método POST, testa os dados introduzidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = test_input($data['email']);
    $password = test_input($data['password']);
}

//Função que testa os dados removendo espaços e barras e convertendo caracteres especiais em entidades html para evitar injeção de código malicioso
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//Cria um array de erros
$errors = array('email' => '', 'password' => '');

//Verifica se o utilizador clicou no botão de submissão do formulário
if (!empty($data['submitBtn'])) {
    //var_dump($data);

    //Faz as validações dos campos

    if (empty($data['email'])) {
        $errors['email'] = "<p style='color: red;'>Este campo não pode ficar vazio!</p>";
    } else {

        $email = test_input($data['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "<p style='color: red;'>Insira um endereço de e-mail válido!</p>";
        }
    }


    if (empty($data['password'])) {
        $errors['password'] = "<p style='color: red;'>Este campo não pode ficar vazio!</p>";
    } else {
        //Cria a instrução SQL que seleciona o utilizador correspondente ao inserido no formulário
        $user_query = "SELECT user_id, email, password 
            FROM users 
            WHERE email = :email 
            LIMIT 1";

        //A base de dados prepara a query
        $user_result = $connection->prepare($user_query);

        //Faz a ligação entre os dados inseridos no formulário e os campos da tabela
        $user_result->bindParam(':email', $data['email'], PDO::PARAM_STR);

        //Executa a query
        $user_result->execute();


        //Verifica se os dados coincidem e se o utilizador existe na base de dados
        if (($user_result) and ($user_result->rowCount() != 0)) {
            //Procura o utilizador e devolve o resultado
            $user_row = $user_result->fetch(PDO::FETCH_ASSOC);

            //var_dump($user_row);

            //Verifica se a palavra-passe inserida corresponde à armazenada na base de dados
            if ($data['password'] == $user_row['password']) {
                $_SESSION['user_id'] = $user_row['user_id'];
                $_SESSION['name'] = $user_row['name'];
                header("Location: profile.php");
                //echo "Utilizador com sessão iniciada!";
            } else {
                $errors['password'] = "<p style='color: red;'>E-mail ou palavra-passe inválidos!</p>";
            }
        } 
        
        else {
            $errors['password'] = "<p style='color: red;'>E-mail ou palavra-passe inválidos!</p>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!--Importação do ficheiro css-->
    <link href="styles/form.css" rel="stylesheet" type="text/css">
    <!--Importação do ficheiro javascript-->
    <script src="./scripts/login_form.js" type="text/javascript" defer></script>

    <!--Importação das fontes-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!--Importação da biblioteca de ícones font awesome-->
    <script src="https://kit.fontawesome.com/4489f75108.js" crossorigin="anonymous" defer></script>
</head>

<body>
    <div class="container">

        <div class="left-side">
            <div class="header">
                <img src="images/logo_0092caff.svg" alt="logo" id="logo" />
                <h1>Bem-vindo!</h1>
            </div>

            <div class="form">
                <form id="loginForm" name="loginForm" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return checkInputs()">
                    <div class="form-control">
                        <label class="label" for="email">E-mail </label>
                        <input class="input" type="text" size="20" maxlength="50" name="email" id="email" placeholder="123@email.pt" value="<?php echo $email; ?>">
                        <i class="fa-solid fa-circle-check"></i>
                        <i class="fa-solid fa-circle-xmark"></i>

                        <small>Error message</small>
                        <div class="php-error"><?php echo $errors['email']; ?></div>
                    </div>

                    <div class="form-control">
                        <label class="label" for="password">Palavra-passe </label>
                        <input class="input" type="password" size="20" maxlength="255" name="password" id="password" placeholder="********">
                        <i class="fa-solid fa-circle-check"></i>
                        <i class="fa-solid fa-circle-xmark"></i>

                        <small>Error message</small>
                        <div class="php-error"><?php echo $errors['password']; ?></div>
                    </div>

                    <input type="checkbox" name="remember-me" value="1" />&nbsp;Lembrar-me
                    <span>&#x1F6C8; <a href="#">Esqueci-me da password</a></span><br><br>

                    <button type="submit" name="submitBtn" id="submitBtn" value="Iniciar sessão">Iniciar sessão</button><br><br>
                    <span>Ainda não tens uma conta? Regista-te <a href="register.php">aqui</a>.</span>
                </form>
            </div>
        </div>

        <div class="right-side">
            <img src="images/login-cover.png" alt="image" height="300px">
            <!--imagem deve ser otimizada-->
        </div>
</body>

</html>