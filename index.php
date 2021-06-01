<?php 
// pour se connecter à la base de données
include("config/configuration.php");

// connection
try {
  $dbh = new PDO($dsn, $user, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Échec lors de la connexion : ' . $e->getMessage();
}

/* Récupération des 3 dernieres annonces pour générer la page */
$requete='SELECT * FROM annonces ORDER BY date LIMIT 3';
$resultats= $dbh -> query($requete);
$tableau3Annonces = $resultats->fetchAll(PDO::FETCH_ASSOC);
$resultats -> closeCursor();

/* Récuperation des départements ayant des annonces pour générer la liste de sélection */
$requete='SELECT * FROM departements WHERE id IN (SELECT DISTINCT(departement) FROM annonces)';
$resultats= $dbh -> query($requete);
$tableauDepartements = $resultats->fetchAll(PDO::FETCH_ASSOC);
$resultats -> closeCursor();

// récupération des annonces si on a un département
if(isset($_GET["departement"])):
  // Récupération des annonces de ce département pour générer la page 
  $requete='SELECT * FROM annonces WHERE departement='.$_GET["departement"];
  // on modifie la requête s'il y a une contrainte de classement
  if(isset($_GET["classement"])){
    $requete=$requete.' ORDER BY '.$_GET["classement"];
  }
  
  $resultats= $dbh -> query($requete);
  $tableauAnnonces = $resultats->fetchAll(PDO::FETCH_ASSOC);
  $nbAnnonces=count($tableauAnnonces);
  $resultats -> closeCursor();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Petites annonces</title>
  <meta name="description" content=""/>
  
  
  
  <!-- amélioration de la cohérence entre les navigateurs dans le style par défaut des éléments HTML-->
  <link rel="stylesheet" media="screen" href="css/normalize.css">
  
  <!-- utilisation de Boostrap (via un CDN) -->
  <!-- Latest compiled and minified CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
  
  <!-- style CSS perso-->
  <link rel="stylesheet" media="screen" href="css/style.css">
  
</head>
<body>
  <div class="container">
    <div class="row">
      <h1>Site de petites annonces</h1>
    </div>
    <div class="row">
      <h2>Les dernières petites annonces postées</h2>
      <?php
      for($i=0;$i<count($tableau3Annonces);$i++){
        ?>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
            <h3 class="card-title"><?php echo $tableau3Annonces[$i]["titre"];?></h3>
            <p class="card-text"><?php echo $tableau3Annonces[$i]["descriptif"];?></p>
          </div>
        </div>
        </div>          
        <?php
      }
      ?>
    </div>
    
    <hr/>
    <form method="GET">
      <div class="mb-3">
        <h3>Rechercher une annonce</h3>
        <label for="departement" class="form-label">Département</label>
        <select name="departement">
          <?php
          foreach($tableauDepartements as $dep){
            echo '<option value="'.$dep["id"].'">'.$dep["nom"].'</option>';
          }
           ?>
        </select>
      </div>
      <div class="mb-3">
        Classer par: <br/>
        <input class="form-check-input" type="radio" name="classement" id="classement1" value="date">
        <label class="form-check-label" for="classement1">
          date
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="classement" id="classement2" value="popularite" >
        <label class="form-check-label" for="classement2">
          popularite
        </label>
      </div>
      <button type="submit" class="btn btn-primary">Voir les annonces</button>
    </form>
    <hr/>
    <?php
    // affichage des annonces si on a un département
    if(isset($_GET["departement"])):
      ?>    
    <div class="row">
      <h2>Les annonces de votre département</h2>
    <?php
    // affichage des annonces si on a un département
      if($nbAnnonces==0){
        ?>
        Aucune annonce ne correspond à vos critères
        <?php
      }
      else{
        for($i=0;$i<$nbAnnonces;$i++){
          ?>
          <div class="col-lg-4">
            <h3><?php echo $tableauAnnonces[$i]["titre"];?></h3>
            <p><?php echo $tableauAnnonces[$i]["descriptif"];?></p>
          </div>          
          <?php
        }
      }
      ?>
      </div>      
      <?php
    endif;
    ?>
    
  </div>
  
  
  <!-- utilisation de Boostrap (via un CDN) -->
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
  
  
</body>
</html>