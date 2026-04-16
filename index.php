<?php
session_start();

$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$base = "gestionzoo";

$conn = mysqli_connect($serveur, $utilisateur, $motdepasse, $base);

if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if (isset($_POST['login'])) {
    $nom = $_POST['nom'];
    $mdp = $_POST['motdepasse'];

    $sql = "SELECT * FROM utilisateur 
            WHERE nom_utilisateur='$nom' 
            AND mot_de_passe='$mdp'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['nom'] = $user['nom_utilisateur'];
        $_SESSION['role'] = $user['role'];
    } else {
        $erreur = "Nom ou mot de passe incorrect";
    }
}

if (isset($_POST['validerSoin'])) {
    $idAnimal = $_POST['id_animal'];
    $type = $_POST['type_soin'];
    $date = $_POST['date_soin'];

    if ($date > date("Y-m-d")) {
        $sqlVerif = "SELECT * FROM soin
                     WHERE id_animal = $idAnimal
                     AND type_soin = '$type'
                     AND date_soin >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";

        $res = mysqli_query($conn, $sqlVerif);

        if (mysqli_num_rows($res) == 0) {
            $sqlInsert = "INSERT INTO soin(id_animal, type_soin, date_soin)
                          VALUES($idAnimal, '$type', '$date')";
            mysqli_query($conn, $sqlInsert);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion Zoo</title>
</head>
<body>

<?php if (!isset($_SESSION['nom'])) { ?>

    <h2>Connexion</h2>

    <?php if (isset($erreur)) echo $erreur; ?>

    <form method="POST">
        Nom : <input type="text" name="nom"><br><br>
        Mot de passe : <input type="password" name="motdepasse"><br><br>
        <button type="submit" name="login">Se connecter</button>
    </form>

<?php } else { ?>

    <h2>Menu</h2>
    <a href="index.php">Accueil</a> |
    <a href="index.php?page=animaux">Afficher les animaux</a> |
    Bienvenue : <?php echo $_SESSION['nom']; ?>

    <hr>

    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

    if ($page == 'accueil') {

        $sql1 = "SELECT COUNT(*) AS totalAnimaux FROM animal";
        $res1 = mysqli_query($conn, $sql1);
        $animaux = mysqli_fetch_assoc($res1);

        $sql2 = "SELECT COUNT(*) AS totalEnclos FROM enclos";
        $res2 = mysqli_query($conn, $sql2);
        $enclos = mysqli_fetch_assoc($res2);

        $sql3 = "SELECT * FROM soin 
                 WHERE date_soin >= CURDATE() 
                 ORDER BY date_soin ASC";
        $res3 = mysqli_query($conn, $sql3);
    ?>

        <h3>Informations du Zoo</h3>
        <p>Total animaux : <?php echo $animaux['totalAnimaux']; ?></p>
        <p>Total enclos : <?php echo $enclos['totalEnclos']; ?></p>

        <table border="1">
            <tr>
                <th>ID Animal</th>
                <th>Type soin</th>
                <th>Date</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($res3)) { ?>
            <tr>
                <td><?php echo $row['id_animal']; ?></td>
                <td><?php echo $row['type_soin']; ?></td>
                <td><?php echo $row['date_soin']; ?></td>
            </tr>
            <?php } ?>
        </table>

    <?php } ?>

    <?php if ($page == 'animaux') {
        $sql = "SELECT * FROM animal ORDER BY espece";
        $result = mysqli_query($conn, $sql);
    ?>

        <h3>Liste des animaux</h3>

        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Espèce</th>
                <th>Nombre soins</th>
                <th>Programmer soin</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($result)) { 
                $id = $row['id_animal'];

                $sqlSoin = "SELECT COUNT(*) AS total 
                            FROM soin 
                            WHERE id_animal = $id";
                $resSoin = mysqli_query($conn, $sqlSoin);
                $soin = mysqli_fetch_assoc($resSoin);
            ?>

            <tr>
                <td><?php echo $id; ?></td>
                <td><?php echo $row['nom']; ?></td>
                <td><?php echo $row['espece']; ?></td>
                <td><?php echo $soin['total']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id_animal" value="<?php echo $id; ?>">
                        <input type="text" name="type_soin" placeholder="Type soin" required>
                        <input type="date" name="date_soin" required>
                        <button type="submit" name="validerSoin">Valider</button>
                    </form>
                </td>
            </tr>

            <?php } ?>
        </table>

    <?php } ?>

<?php } ?>

</body>
</html>
