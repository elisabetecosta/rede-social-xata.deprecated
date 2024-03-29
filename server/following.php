<?php
//============= PÁGINA DOS PERFIS QUE O UTILIZADOR SEGUE ========================

    //Ficheiro PHP com as classes e funções      
    include 'session.php';

    //Variáveis deste documento:
    $focusposts = "";                //Aba dos posts: sem classe   
    $focusfave = "";                 //Aba dos favorites: sem classe
    $focusflwing = "class='focus'";  //Aba dos following: acrescentamos a classe ".focus" à respectiva <a> âncora
    $focusflwers =  "";              //Aba dos followers: sem classe
    //Resultado: a aba dos following aparece sublinhada a azul

    //Título da página
    $title = htmlentities("Contas que a {$userProfile->userData['name']} segue");

    //Ficheiro CSS da página
    $cssFile = htmlentities("../styles/profile.css");

    //Ficheiro Javascript da página
    $jsFile = htmlentities("../scripts/posts_form.js");

    //Componentes html
    include '../components/head.php';               //Cabeçalho do HTML:    <doctype>, <head>, <body>, <link>, <styles defer>
    include '../components/navbar.php';             //Barra de navegação:   <header> <nav>
    include '../components/profile.header.php';     //Cabeçalho do perfil:  <main>
    include '../components/profile.following.php';  //Conteúdo: FOLLOWING   </main>
    include '../components/footer.php';             //Roda-pé com links     <footer>
?>

</body>
</html>
 
