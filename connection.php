<!DOCTYPE html>
<html>
<?php include("commun/head.php"); ?>
<header>
<?php include("commun/navbar.php"); ?>
</header>

<!--     formulaire  -->
<?php

if (!empty($_POST)){

		
	try
	{
		$bdd = new PDO('mysql:host=localhost;dbname=inscription;charset=utf8', 'root', '');
	}
	catch(Exception $e)
	{
			die('Erreur : '.$e->getMessage());
	}

	// inscription utilisateur
	// controle des données et enregistrement en base

	if (isset($_POST['inscription'])) {
		if (empty($_POST['pseudo']) || empty($_POST['mail']) || empty($_POST['mdp'])){
			$err = true;
			
			$msg = "Merci de remplir tous les champs du formulaire";
			
		} else {

			if(strlen($_POST['mdp']) < 6 || $_POST['mdp'] !== $_POST['mdp2']){

			$msg = 'Mot de passe trop court ! ou ne sont pas identiques';

			} else {
			
			//recup variables form POST (all data protect by htmlspecialchars)
		
			function verify_input($input){
				$input = trim($input);
				$input = stripcslashes($input);
				$input = htmlspecialchars($input);
				return $input;
			}

			// check si l'utilisateur existe
			$pseudo = verify_input($_POST['pseudo']);
			$mail = verify_input($_POST['mail']);
			$mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
			

			$requser = $bdd->prepare("SELECT * FROM membres WHERE pseudo = :pseudo AND mail = :mail");
			$requser->execute(array(
				'pseudo' => $pseudo, 
				'mail' => $mail
			));
			$userexist = $requser->rowCount();
			
			
			if($userexist === 1){
				$msg = ' Vous etes déja inscrit à la prochaine sessions de formation ! ';
				}
			else
				{
				// send message in database
				$req = $bdd->prepare('INSERT INTO membres(pseudo, mail, mdp) VALUES(:pseudo, :mail, :mdp)');
				$req->execute([
						'pseudo' => $pseudo,
						'mail' => $mail,
						'mdp' => $mdp
						]);
			
				$msg = 'Votre inscription a bien été prise en compte ! Nous reviendrons vers vous très bientot !';
			
				}
			}
		$msg;
	}
	}	
	
};

?>

  <div class="container-fluid">
<div class="row">
<div class="col">
<h3>Inscrivez-vous !</h3>
<form method="POST"	action="connection.php">
<?php if(isset($msg)): ?>
<p class="alert-warning py-4 text-danger font-weight-bold"><?= $msg ?></p>
<?php endif; ?>

  <div class="form-group">
    <label for="pseudo">Votre pseudo :</label>
    <input type="text" class="form-control" id="pseudo" name="pseudo" aria-describedby="pseudohelp" placeholder="Entrer votre pseudo">
	<div class="form-group">
    <label for="mail">Votre mail :</label>
    <input type="text" class="form-control" id="mail" name="mail" aria-describedby="mailhelp" placeholder="Entrer votre mail">
  </div>
  <div class="form-group">
    <label for="mdp">Mot de passe : </label>
    <input type="password" class="form-control" id="mdp" name="mdp" placeholder="Mot de passe">
  </div>
  <div class="form-group">
    <label for="mdp2">Confirmer votre mot de passe :</label>
    <input type="password" class="form-control" id="mdp2" name="mdp2" placeholder="Mot de passe">
  </div>
  <button type="submit" name="inscription" class="btn btn-primary">Inscription</button>
</form>