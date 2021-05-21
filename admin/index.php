<?php 
// pour se connecter à la base de données
include("../config/configuration.php");

// connection
try {
  $dbh = new PDO($dsn, $user, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Échec lors de la connexion : ' . $e->getMessage();
}

// si on vient du formulaire pour ajouter une annonce, il faut l'ajouter
if(isset($_POST["action"]) && ($_POST["action"]=='ajouter')){
  // on prepare la requete
  $req=$dbh->prepare('INSERT INTO annonces(titre,descriptif,departement,date) VALUES(:titre,:descriptif,:departement,"2020")');
  $req->bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
  $req->bindParam(':descriptif', $_POST['descriptif'], PDO::PARAM_STR);
  $req->bindParam(':departement', $_POST['departement'],PDO::PARAM_INT);
  
  // on essaie de l'executer
  try{
    $req->execute();
    echo "annonce ajoutée";
  } catch (PDOException $e) {
    echo 'Échec lors de l\'ajout : ' . $e->getMessage();
  }
  
} // si on vient du formulaire pour supprimer une annonce, il faut la supprimer
else if(isset($_POST["action"]) && ($_POST["action"]=='supprimer')){
  // on prepare la requete
  $req=$dbh->prepare('DELETE FROM annonces WHERE id=:id');
  $req->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
  // on essaie de l'executer
  try{
    $req->execute();
    echo "annonce supprimée";
  } catch (PDOException $e) {
    echo 'Échec lors de la suppression : ' . $e->getMessage();
  }
}

/* Récuperation des annonces pour générer la liste de suppression*/
$requete='SELECT id,titre FROM annonces';
$resultats= $dbh -> query($requete);
$tableauAnnonces = $resultats->fetchAll(PDO::FETCH_ASSOC);
$resultats -> closeCursor();

/* Récuperation des départements pour générer la liste de sélection dans l'ajout de l'annonce */
$requete='SELECT * FROM departements';
$resultats= $dbh -> query($requete);
$tableauDepartements = $resultats->fetchAll(PDO::FETCH_ASSOC);
$resultats -> closeCursor();


?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ADMIN Petites annonces</title>
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
      <h1>ADMIN</h1>
    </div>
    
    <div class="row">
      <form method="POST">
        <h2>Ajout d'une annonce</h2>
        <div class="mb-3">
          <label for="titre">Titre de l'annonce</label>
          <input type="text" name="titre" required="required"/><br/>
          <textarea name="descriptif" rows="5" cols="40">descriptif</textarea><br/>
          <label for="departement" class="form-label">Département</label>
          <select name="departement">
            <?php
            foreach($tableauDepartements as $dep){
              echo '<option value="'.$dep["id"].'">'.$dep["nom"].'</option>';
            }
            ?>
          </select>
        </div>
        <button type="submit" name="action" value="ajouter" class="btn btn-primary">Ajouter l'annonce</button>
      </form>
      <hr/>    
    </div>
    
    <div class="row">
      <form method="POST">
        <h2>Suppression d'une annonce</h2>
        <div class="mb-3">
          <label for="id" class="form-label">Annonce</label>
          <select name="id">
            <?php
            foreach($tableauAnnonces as $annonce){
              echo '<option value="'.$annonce["id"].'">'.$annonce["titre"].'</option>'."\n";
            }
            ?>
          </select>
        </div>
        <button type="submit" name="action" value="supprimer" class="btn btn-primary">Supprimer l'annonce</button>
      </form>
      <hr/>    
    </div>
    
    
    <!-- utilisation de Boostrap (via un CDN) -->
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    
    
  </body>
  </html>