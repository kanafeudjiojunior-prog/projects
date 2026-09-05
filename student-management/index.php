<?php

session_start();

include 'connec.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['name'] ?? '');
    $password = $_POST['pass'] ?? '';

    if (!empty($nom) && !empty($password)) {
        // Préparation de la requête SQL pour éviter les injections SQL
        $sql = "SELECT * FROM user WHERE name = :name LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['name' => $nom]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification de l'utilisateur et du mot de passe haché
        if ($user && $password === $user['pass']) {
            echo "welcome back " . htmlspecialchars($user['name']);
            // Redirection vers le tableau de bord ou la page d'accueil
            header('Location: departement.php');
            exit();
        } else {
            echo "user name or password is incorrect.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="boostrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/3.0.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Form</title>
</head>

<body >
    <div class="container-fluid">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header  text-white" style="background-color: rgb(2, 48, 91);">
                    <h4 class="modal-title" id="contactLabel">welcome</h4>
                </div>
                <div class="modal-body">
                    <div id="alert-status" class="alert d-none"></div>
                    <form id="contact-form" class="text-dark"
                        action="index.php" method="post">
                        <div class="mb-3">
                            <label for="name" title="votre nom">NAME</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                placeholder="admin">
                        </div>
                        <div class="mb-3">
                            <label for="pass">password</label>
                            <input type="password" class="form-control" id="pass" name="pass"
                                required placeholder="admin">
                        </div>
                        <div class="mb-3 px-0 pb-0">

                            <button type="submit" class="btn btn-primary form-control"
                                id="btn-submit">LOGIN</button>
                            
                        </div>

                    </form>
                </div>
                <div class="modal-footer" style="background-color: rgb(2, 48, 91);">
                    <div class="row">
                        <div class="col-12 text-center gap-2 ">
                            <a href="https://www.linkedin.com/in/kana-feudjio-junior-466a363a8?utm_source=share_via&utm_content=profile&utm_medium=member_ios"
                                class="btn btn-primary ms-2 rounded-circle stat-card"><i
                                    class="fa-brands fa-linkedin"></i></a>
                            <a href="https://wa.me/237696599882?text=Bonjour%20Kana,%20je%20souhaite%20un%20devis%20pour%20un%20projet%20web."
                                class="btn btn-primary ms-2 rounded-circle stat-card"><i
                                    class="fa-brands fa-whatsapp"></i></a>
                            <a href="https://t.me/Kfj237"
                                class="btn btn-primary ms-2 rounded-circle stat-card"><i
                                    class="fa-brands fa-telegram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row bg-dark text-light">

            <div class="col-12">
                <footer>
                    <div class="row">
                        <div class="col-sm-6 col-12">
                            <h4 style="margin-top: 5px;" class="text-center">my motto</h4>
                            <div class="row">
                                <div class="col-12">
                                    <div style=" margin: 10px;">
                                        <a href="#"><img src="media/LOGO-KFJ237.png" class="img-fluid rounded-circle"
                                                alt="LOGO" width="70" height="70"></a>
                                    </div>
                                    <div style=" margin: 10px;">
                                        <p>your goals, my code:</p>
                                        <p>transforming complex technical needs into simple, secure, and seamless
                                            digital
                                            experience
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <h4 style="margin-top: 5px;" class="text-center">my services</h4>
                            <div class="row text-light">
                                <div class="col-sm-6 col-12">
                                    <h3 class="text-light">Web development</h3>
                                    <ul>
                                        <li>professional showcase websites(vitrine)</li>
                                        <li>custom web application and portals</li>
                                        <li>landing page and sales tunnels</li>
                                        <li>portfolio and interactive resume sites</li>
                                        <li>e-commerce and online product catalogs</li>
                                        <li>membership portals and online course platforms</li>
                                        <li>dynamic forms and booking systems</li>
                                    </ul>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <h3 class="text-light">social network</h3>
                                    <ul>
                                        <li>tiktok monetizable account</li>
                                        <li>content creation training</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr style=" color: whitesmoke;">
                    <div style="padding: 5px; text-align: center;">
                        <p>Copyright &COPY; 2026 kana junior(KFJ237) </p>
                    </div>
                </footer>
            </div>
        </div>
    </div>



    <script src="jquery/jquery-4.0.0.js"></script>
    <script src="boostrap5/js/bootstrap.bundle.min.js"></script>
</body>

</html>