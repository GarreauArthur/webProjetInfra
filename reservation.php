<?php
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_POST["hotel"])     || !is_numeric($_POST["hotel"])
  || EMPTY($_POST["dateDebut"]) || EMPTY($_POST["dateFin"])
  || EMPTY($_POST["chambre"])
)
{
	// on retourne à la page d'accueil
	header('Location: ./index.php');
}

// on se connecte à la base de données
include_once('./dbconnection.php');

$stm = $connection->prepare(
	"SELECT * FROM Chambres INNER JOIN Hotels ON Hotels.id = Chambres.hotel"
	." WHERE chambres.hotel = :hotel AND chambres.numeroChambre = :chambre"
);
$stm->execute(array(
	                ':hotel'   => $_POST["hotel"],
	                ':chambre'  => $_POST["chambre"]
                    )
			 );
			 
// On récupère le nom de l'hotel

$hotel = $connection->prepare("SELECT nom FROM Hotels WHERE id = :hotel");
$hotel->execute(array(
	':hotel' => $_POST["hotel"]
	)
);
$nomHotel = $hotel->fetch();

// On reformate les dates de dbt et de fin 

$dateDebut = $_POST["dateDebut"];
$dateDebutReformate = date("d-m-Y", strtotime($dateDebut));
$dateFin = $_POST["dateFin"];
$dateFinReformate = date("d-m-Y", strtotime($dateFin));

?>

<script type="text/javascript">

function surligne(champ, erreur) //colorie en rouge les champs invalides
{
   if(erreur)
      champ.style.backgroundColor = "#fba";
   else
      champ.style.backgroundColor = "";
}

function verifText(champ) //vérification champs prenom, nom et téléphone
{
   if(champ.value.length < 2 || champ.value.length > 25)
   {
      surligne(champ, true);
      return false;
   }
   else
   {
      surligne(champ, false);
      return true;
   }
}

function verifMail(champ) //vérification champs mail
{
   var regex = /^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;
   if(!regex.test(champ.value))
   {
      surligne(champ, true);
      return false;
   }
   else
   {
      surligne(champ, false);
      return true;
   }
}

function verifForm(f) //fonction qui vérifie tous les champs
{
   var nomOk = verifText(f.nom);
   var prenomOk = verifText(f.prenom);
   var mailOk = verifMail(f.mail);
   var telephoneOk = verifText(f.telephone);
   
   if(nomOk && prenomOk && mailOk && telephoneOk)
      return true;
   else
   {
      alert("Veuillez remplir correctement tous les champs");
      return false;
   }
}
 
</script>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>One more step</title>
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>
	<h1> Votre réservation :</h1>
	<p> Vous avez choisi la chambre <?php echo $_POST["chambre"]?> de l'hôtel <?php echo $nomHotel["nom"]?> !</p>
	<p> Date de réservation : <?php echo $dateDebutReformate?> à <?php echo $dateFinReformate?>.</p>
	<p> Prix à payer : <?php echo $_POST["prixTotal"]?>.</p>
	<h1>Confirmer la réservation</h1>
	<form action="confirmation.php" method="POST" onsubmit="return verifForm(this)"> 
		<input type="hidden" name="hotel" value="<?= htmlspecialchars($_POST["hotel"]); ?>">
		<input type="hidden" name="chambre" value="<?= htmlspecialchars($_POST["chambre"]); ?>">
		<input type="hidden" name="dateDebut" value="<?= htmlspecialchars($_POST["dateDebut"]); ?>">
		<input type="hidden" name="dateFin" value="<?= htmlspecialchars($_POST["dateFin"]); ?>">
		<input type="hidden" name="prixTotal" value="<?= $_POST["prixTotal"]; ?>">
		<input type="hidden" name="nomHotel" value="<?= $nomHotel["nom"]; ?>">
		<input class="infos" type="text" name="nom" placeholder="Nom" onblur="verifText(this)" />
		<input class="infos" type="text" name="prenom" placeholder="Prenom" onblur="verifText(this)" />
		<input class="infos" type="text" name="mail" placeholder="Mail" onblur="verifMail(this)" />
		<input class="infos" type="text" name="telephone" placeholder="Telephone" onblur="verifText(this)">
		<input type="submit" value="Confirmer la réservation">
	</form>
</body>
</html>