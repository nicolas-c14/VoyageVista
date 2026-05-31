<?php

session_start();

require_once __DIR__ . "/models/destinationModel.php";

$search = trim($_GET['q'] ?? '');

$destinations = getAllDestinations($search);
$randomDestinations = getRandomDestinations();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVista</title>
    <link rel="icon" href="assets/images/VoyageVistaLogo.png" type="image/png">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- HERO SECTION -->
    <section class="hero-section">

        <div class="hero-overlay"></div>

        <div class="container hero-content text-center">

            <h1 class="display-4 fw-bold">
                Explorez le monde avec VoyageVista
            </h1>

            <p class="lead">
                Réservez vos destinations, hôtels et activités en quelques clics.
            </p>

            <!-- SEARCH BAR -->
            <div class="search-box mt-4">

                <form id="searchForm"
                      class="row g-2 justify-content-center"
                      action="destinations.php"
                      method="GET">

                    <div class="col-md-5">
                        <input type="text"
                            id="searchInput"
                            name="q"
                            class="form-control form-control-lg"
                            placeholder="Rechercher une destination"
                            value="<?= htmlspecialchars($search); ?>">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary btn-lg" type="submit">
                            Rechercher
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </section>

    <!-- DESTINATIONS -->
    <section class="container my-5">

        <div class="text-center mb-5">
            <h2 class="fw-bold">Destinations populaires</h2>
            <p>Choisissez votre prochaine aventure.</p>
        </div>

        <?php if ($search !== ''): ?>

            <div class="alert alert-info mb-4">

                Résultats pour : <strong><?= htmlspecialchars($search); ?></strong>

            </div>

        <?php endif; ?>

        <div class="row g-4">

            <?php if (empty($destinations)): ?>

                <div class="col-12">

                    <div class="alert alert-warning mb-0">

                        Aucune destination ne correspond à votre recherche.

                    </div>

                </div>

            <?php endif; ?>

            <?php foreach($randomDestinations as $destination): ?>

                <div class="col-md-4">

                    <div class="card destination-card h-100">

                        <img src="assets/images/<?= $destination["image"]; ?>"
                            class="card-img-top"
                            alt="<?= $destination["name"]; ?>">

                        <div class="card-body">

                            <h5 class="card-title">

                                <?= $destination["name"]; ?>,
                                <?= $destination["country"]; ?>

                            </h5>

                            <p class="card-text">

                                <?= $destination["description"]; ?>

                            </p>

                            <p class="fw-bold text-primary">

                                À partir de
                                <?= $destination["price"]; ?> €

                            </p>

                            <a href="destination.php?id=<?= $destination["id"]; ?>"
                            class="btn btn-outline-primary">

                                Découvrir

                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </section>

    <!-- ABOUT US -->
    <section class="container my-5">
        <div class="row align-items-center g-4">

            <div class="col-md-6">
                <img src="assets/images/about-us.jpg" class="img-fluid rounded" alt="About Us">
            </div>

            <div class="col-md-6">
                <h2 class="fw-bold">À propos de VoyageVista</h2>
                <p>VoyageVista est votre partenaire de confiance pour toutes vos aventures de voyage. Nous offrons une plateforme facile à utiliser pour réserver des destinations, des hôtels et des activités dans le monde entier. Notre mission est de rendre le voyage accessible et agréable pour tous, en fournissant des offres compétitives et un service client exceptionnel.</p>
                <a href="#" class="btn btn-primary">
                    En savoir plus
                </a>
            </div>

        </div>
    </section>

    <!-- CONTACT -->
    <section class="container my-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Contactez-nous</h2>
            <p>Nous sommes là pour vous aider à planifier votre prochain voyage.</p>
        </div>

        <!-- CONTACT FORM -->
        <form id="contactForm" class="row g-4 justify-content-center" action="contact_process.php" method="POST">

            <div class="col-md-6">
                <input type="text" name="name" id="contactName" class="form-control form-control-lg" placeholder="Votre nom" required>
            </div>

            <div class="col-md-6">
                <input type="email" name="email" id="contactEmail" class="form-control form-control-lg" placeholder="Votre email" required>
            </div>

            <div class="col-12">
                <textarea name="message" class="form-control form-control-lg" rows="5" placeholder="Votre message" required></textarea>
            </div>

            <div class="col-12 text-center">
                <button class="btn btn-primary btn-lg" type="submit">
                    Envoyer
                </button>
            </div>
        </form>
    </section>


    <!-- FOOTER -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- JS -->
    <script src="assets/js/script.js"></script>

</body>

</html>