<?php
session_start();
// s'il n'y a pas de paramètre, ou si c'est une mauvaise valeur
if ( EMPTY($_SESSION["hotel"])     || !is_numeric($_SESSION["hotel"])
  || EMPTY($_SESSION["dateDebut"]) || EMPTY($_SESSION["dateFin"])
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
	                ':hotel'   => $_SESSION["hotel"],
	                ':chambre'  => $_POST["chambre"]
                    )
			 );
			 
// On récupère le nom de l'hotel

$hotel = $connection->prepare("SELECT nom FROM Hotels WHERE id = :hotel");
$hotel->execute(array(
	':hotel' => $_SESSION["hotel"]
	)
);
$nomHotel = $hotel->fetch();

// On reformate les dates de dbt et de fin 

$dateDebut = $_SESSION["dateDebut"];
$dateDebutReformate = date("d-m-Y", strtotime($dateDebut));
$dateFin = $_SESSION["dateFin"];
$dateFinReformate = date("d-m-Y", strtotime($dateFin));

// on stocke les variables dans la session
$_SESSION["chambre"]   = $_POST["chambre"];
$_SESSION["prixTotal"] = $_POST["prixTotal"];
$_SESSION["nomHotel"]  = $nomHotel["nom"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>One more step</title>
	<link rel="stylesheet" type="text/css" href="./lecss.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>
   <header>
      <h1> Votre réservation !</h1>
      <div class="recap">
         <p> Vous avez choisi la chambre <?php echo htmlspecialchars($_POST["chambre"])?> de l'hôtel <?php echo $nomHotel["nom"]?> !</p>
         <p> Date de réservation : <?php echo $dateDebutReformate?> à <?php echo $dateFinReformate?>.</p>
         <p> Prix à payer : <?php echo htmlspecialchars($_POST["prixTotal"]) ?>.</p>
      </div>
   </header>
   <h2 style="margin:30px 0 10px 0">Confirmer la réservation</h2>
   
   <form class="form-infos" action="confirmation.php" method="POST" onsubmit="return verifForm(this)">
      <input class="infos" type="text" name="nom" placeholder="Nom" onblur="verifText(this)" />
      <input class="infos" type="text" name="prenom" placeholder="Prenom" onblur="verifText(this)" />
      <input class="infos" type="text" name="mail" placeholder="Mail" onblur="verifMail(this)" />
      <input class="infos" type="text" name="telephone" placeholder="Telephone" onblur="verifText(this)">
      <input class="submit-confirm" type="submit" value="Confirmer la réservation">
   </form>
   
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

</body>
</html>