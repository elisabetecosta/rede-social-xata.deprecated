<?php
//Inicia ou recupera a atual sessão
session_start();

//Inicia o buffer e bloqueia qualquer saída para o navegador para evitar erros de redirecionamento
ob_start();

//Estabelece a conexão à base de dados
include_once 'server/includes/connect_db.php';

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
        $errors['email'] = "<p style='margin-top: 5px; font-size: 14px; color: #DB5A5A;'>Este campo não pode ficar vazio!</p>";
    } else {

        $email = test_input($data['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "<p style='margin-top: 5px; font-size: 14px; color: #DB5A5A;'>Insira um endereço de e-mail válido!</p>";
        }
    }


    if (empty($data['password'])) {
        $errors['password'] = "<p style='margin-top: 5px; font-size: 14px; color: #DB5A5A;'>Este campo não pode ficar vazio!</p>";
    } else {

        //Cria a instrução SQL que seleciona o utilizador correspondente ao inserido no formulário
        $user_query = "SELECT user_id, email, handle, password 
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
                $_SESSION['handle'] = $user_row['handle'];
                header("Location: ".$_SESSION['handle']."/");
                //echo "Utilizador com sessão iniciada!";
            } else {
                $errors['password'] = "<p style='margin-top: 5px; font-size: 14px; color: #DB5A5A;'>E-mail ou palavra-passe inválidos!</p>";
            }
        } 
        
        else {
            $errors['password'] = "<p style='margin-top: 5px; font-size: 14px; color: #DB5A5A;'>E-mail ou palavra-passe inválidos!</p>";
        }
    }
}
?>


<?php
    //Título da página
    $title = htmlentities("Login");

    //Ficheiro CSS da página
    $cssFile = htmlentities("./styles/login_form.css");

    //Ficheiro Javascript da página
    $jsFile = htmlentities("./scripts/login_form.js");

    //Componentes html
    include './components/head.php';               //Cabeçalho do HTML:    <doctype>, <head>, <body>, <link>, <styles defer>
?>


<body>
    <div class="header">
        <img src="./images/logo.svg" alt="logo" id="logo" />
    </div>

    <div class="container">
        <div class="left-side">
            <div class="form">
            <h1>Bem-vindo!</h1>
                <form id="loginForm" name="loginForm" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return checkInputs()">
                    <div class="form-control">
                        <label class="label" for="email">E-mail </label>
                        <input class="input" type="text" size="20" maxlength="50" name="email" id="email" placeholder="123@email.pt" value="<?php echo $email; ?>">

                        <small>Error message</small>
                        <div class="php-error"><?php echo $errors['email']; ?></div>
                    </div>

                    <div class="form-control">
                        <div class="password-container">
                            <label class="label" for="password">Palavra-passe </label>
                            <input class="input" type="password" size="20" maxlength="255" name="password" id="password" placeholder="********">
                            <i class="fa-solid fa-eye" id="eye" onclick="showPassword()"></i>
                            <i class="fa-solid fa-eye-slash" id="eye-slash" onclick="showPassword()"></i>
                        </div>

                        <small>Error message</small>
                        <div class="php-error"><?php echo $errors['password']; ?></div>
                    </div>

                    <div class="extra">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">&nbsp;Lembrar-me</label>
                        <span><i class="fa-solid fa-circle-info"></i> <a href="#">Esqueci-me da palavra-passe</a></span>
                    </div>

                    <button type="submit" name="submitBtn" id="submitBtn" value="Iniciar sessão">Iniciar sessão</button>
                    <span>Ainda não tens uma conta? Regista-te <a href="profile/register">aqui</a>.</span>
                </form>
            </div>
        </div>

        <div class="right-side">
            <img class="cover" src="./images/login-cover.png" alt="imagem de capa">
        </div>
    </div>

    <?php 
        include './components/footer.php';              //Rodapé com links     <footer>
    ?>
</body>
</html>