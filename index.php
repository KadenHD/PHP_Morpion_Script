<?php
function afficherTableau($tableau) {
    echo "\n";
    $num = 1; // Initialiser un compteur pour les positions possibles
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            // Afficher la valeur du tableau ou un espace si vide
            echo ' ' . ($tableau[$i][$j] === '' ? ' ' : $tableau[$i][$j]) . ' ';
            if ($j < 2) echo '|';
        }
        echo "     "; // Espace entre les deux tableaux
        for ($j = 0; $j < 3; $j++) {
            // Afficher le numéro de la position si la case est vide, sinon un espace
            echo ' ' . ($tableau[$i][$j] === '' ? $num : ' ') . ' ';
            if ($j < 2) echo '|';
            $num++; // Incrémenter le numéro de la position
        }
        echo "\n";
        if ($i < 2) echo "---+---+---     ---+---+---\n";
    }
    echo "\n";
}

function verifierGagnant($tableau) {
    // Vérification des lignes
    for ($i = 0; $i < 3; $i++) {
        // Si les trois éléments de la ligne $i sont identiques (et non vides), on retourne le gagnant
        if ($tableau[$i][0] === $tableau[$i][1] && $tableau[$i][1] === $tableau[$i][2] && $tableau[$i][0] !== '') {
            return $tableau[$i][0];
        }
    }
    // Vérification des colonnes
    for ($i = 0; $i < 3; $i++) {
        // Si les trois éléments de la colonne $i sont identiques (et non vides), on retourne le gagnant
        if ($tableau[0][$i] === $tableau[1][$i] && $tableau[1][$i] === $tableau[2][$i] && $tableau[0][$i] !== '') {
            return $tableau[0][$i];
        }
    }
    // Vérification de la première diagonale (de haut à gauche à bas à droite)
    if ($tableau[0][0] === $tableau[1][1] && $tableau[1][1] === $tableau[2][2] && $tableau[0][0] !== '') {
        return $tableau[0][0];
    }
    // Vérification de la deuxième diagonale (de haut à droite à bas à gauche)
    if ($tableau[0][2] === $tableau[1][1] && $tableau[1][1] === $tableau[2][0] && $tableau[0][2] !== '') {
        return $tableau[0][2];
    }
    // Si aucune condition de victoire n'est remplie, retourner une chaîne vide
    return '';
}

function leTableauEstPlein($tableau) {
    // Parcourt chaque ligne du tableau
    foreach ($tableau as $ligne) {
        // Si une des lignes contient une case vide (valeur ''), le tableau n'est pas plein
        if (in_array('', $ligne)) {
            return false; // Retourne false immédiatement si une case vide est trouvée
        }
    }
    // Si aucune case vide n'a été trouvée, le tableau est plein
    return true;
}

function decisionIAAleatoire($tableau, $marque_ia, $marque_adversaire) {
    // Initialisation d'un tableau pour stocker les positions libres
    $positionsLibres = [];
    // Parcours des lignes et des colonnes de la grille
    for ($ligne = 0; $ligne < 3; $ligne++) {
        for ($colonne = 0; $colonne < 3; $colonne++) {
            // Si la case est vide, on l'ajoute à la liste des positions libres
            if ($tableau[$ligne][$colonne] === '') {
                $positionsLibres[] = "$ligne $colonne"; // Format : "ligne colonne"
            }
        }
    }
    // Vérification s'il existe des positions libres
    if (!empty($positionsLibres)) {
        // Sélection aléatoire d'une position parmi les cases disponibles
        return $positionsLibres[array_rand($positionsLibres)];
    }
    // Si aucune position libre n'existe, retourner une chaîne vide
    return '';
}

function decisionIAProfondeur1($tableau, $marque_ia, $marque_adversaire) {
    if (empty($tableau)) {
        $positionsStrategiques = ['0 0', '0 2', '2 0', '2 2', '1 1'];
        return $positionsStrategiques[array_rand($positionsStrategiques)];
    } else {
        // Vérifie si l'IA peut gagner ou empêcher une victoire imminente
        for ($ligne = 0; $ligne < 3; $ligne++) {
            for ($colonne = 0; $colonne < 3; $colonne++) {
                if ($tableau[$ligne][$colonne] === '') {
                    // Simule un coup pour l'IA
                    $tableau[$ligne][$colonne] = $marque_ia;
                    if (verifierGagnant($tableau) === $marque_ia) {
                        return "$ligne $colonne"; // IA peut gagner
                    }
                    $tableau[$ligne][$colonne] = ''; // Annule la simulation
                }
            }
        }
        for ($ligne = 0; $ligne < 3; $ligne++) {
            for ($colonne = 0; $colonne < 3; $colonne++) {
                if ($tableau[$ligne][$colonne] === '') {
                    // Simule un coup pour l'adversaire
                    $tableau[$ligne][$colonne] = $marque_adversaire;
                    if (verifierGagnant($tableau) === $marque_adversaire) {
                        return "$ligne $colonne"; // Bloque l'adversaire
                    }
                    $tableau[$ligne][$colonne] = ''; // Annule la simulation
                }
            }
        }

        // Si ni victoire ni blocage possible, choisir une position aléatoire
        $positionsLibres = [];
        for ($ligne = 0; $ligne < 3; $ligne++) {
            for ($colonne = 0; $colonne < 3; $colonne++) {
                if ($tableau[$ligne][$colonne] === '') {
                    $positionsLibres[] = "$ligne $colonne";
                }
            }
        }

        // Retourne une position aléatoire parmi les cases libres
        if (!empty($positionsLibres)) {
            return $positionsLibres[array_rand($positionsLibres)];
        }
    }
    return ''; // Aucun coup possible
}

function decisionIAProfondeur2($tableau, $marque_ia, $marque_adversaire) {
    // Si le tableau est vide (début de partie), jouer une position stratégique
    if (empty($tableau)) {
        $positionsStrategiques = ['0 0', '0 2', '2 0', '2 2', '1 1']; // Coins et centre
        return $positionsStrategiques[array_rand($positionsStrategiques)]; // Choix aléatoire parmi ces positions
    } else {
        // Étape 1 : Vérifier si l'IA peut gagner immédiatement
        for ($ligne = 0; $ligne < 3; $ligne++) {
            for ($colonne = 0; $colonne < 3; $colonne++) {
                if ($tableau[$ligne][$colonne] === '') { // Si la case est vide
                    $tableau[$ligne][$colonne] = $marque_ia; // Simuler un coup de l'IA
                    if (verifierGagnant($tableau) === $marque_ia) { // Vérifier si ce coup mène à une victoire
                        return "$ligne $colonne"; // Retourner ce coup gagnant
                    }
                    $tableau[$ligne][$colonne] = ''; // Annuler la simulation
                }
            }
        }
        // Étape 2 : Bloquer un coup gagnant de l'adversaire
        for ($ligne = 0; $ligne < 3; $ligne++) {
            for ($colonne = 0; $colonne < 3; $colonne++) {
                if ($tableau[$ligne][$colonne] === '') { // Si la case est vide
                    $tableau[$ligne][$colonne] = $marque_adversaire; // Simuler un coup de l'adversaire
                    if (verifierGagnant($tableau) === $marque_adversaire) { // Vérifier si l'adversaire gagne
                        return "$ligne $colonne"; // Bloquer ce coup
                    }
                    $tableau[$ligne][$colonne] = ''; // Annuler la simulation
                }
            }
        }
        // Étape 3 : Simuler la profondeur 2 (évaluation des scénarios gagnants et perdants)
        $tabgagnant = []; // Nombre de scénarios gagnants
        $tabperdant = []; // Nombre de scénarios perdants
        $index = 0;
        for ($ligne = 0; $ligne < 3; $ligne++) {
            for ($colonne = 0; $colonne < 3; $colonne++) {
                if ($tableau[$ligne][$colonne] === '') {
                    $tableau[$ligne][$colonne] = $marque_ia; // Simule un coup IA
                    $tabperdant[$index] = 0; // Initialise les scénarios perdants
                    $tabgagnant[$index] = 0; // Initialise les scénarios gagnants

                    // Simule les coups adverses et les scénarios suivants
                    for ($ligne2 = 0; $ligne2 < 3; $ligne2++) {
                        for ($colonne2 = 0; $colonne2 < 3; $colonne2++) {
                            if ($tableau[$ligne2][$colonne2] === '') {
                                $tableau[$ligne2][$colonne2] = $marque_adversaire; // Simule un coup adversaire
                                for ($ligne3 = 0; $ligne3 < 3; $ligne3++) {
                                    for ($colonne3 = 0; $colonne3 < 3; $colonne3++) {
                                        if ($tableau[$ligne3][$colonne3] === '') {
                                            $tableau[$ligne3][$colonne3] = $marque_ia; // Simule un deuxième coup IA
                                            if (verifierGagnant($tableau) === $marque_ia) {
                                                $tabgagnant[$index]++; // Compte un scénario gagnant
                                            } else {
                                                // Simule un coup suivant pour l'adversaire
                                                for ($ligne4 = 0; $ligne4 < 3; $ligne4++) {
                                                    for ($colonne4 = 0; $colonne4 < 3; $colonne4++) {
                                                        if ($tableau[$ligne4][$colonne4] === '') {
                                                            $tableau[$ligne4][$colonne4] = $marque_adversaire;
                                                            if (verifierGagnant($tableau) === $marque_adversaire) {
                                                                $tabperdant[$index]++; // Compte un scénario perdant
                                                            }
                                                            $tableau[$ligne4][$colonne4] = ''; // Annule la simulation
                                                        }
                                                    }
                                                }
                                            }
                                            $tableau[$ligne3][$colonne3] = ''; // Annule la simulation
                                        }
                                    }
                                }
                                $tableau[$ligne2][$colonne2] = ''; // Annule la simulation
                            }
                        }
                    }
                    $tableau[$ligne][$colonne] = ''; // Annule la simulation
                    $index++; // Passe à la case suivante
                }
            }
        }
        // Étape 4 : Choisir la meilleure position
        $minValue = min($tabperdant); // Trouve le minimum des scénarios perdants
        $tabindex = array_keys($tabperdant, $minValue); // Indices des cases avec le moins de scénarios perdants
        $maxValue = PHP_INT_MIN; // Initialise le maximum
        $indexMax = -1;
        foreach ($tabindex as $index) {
            if ($tabgagnant[$index] > $maxValue) { // Priorise les scénarios gagnants
                $maxValue = $tabgagnant[$index];
                $indexMax = $index;
            }
        }
        // Retourne les coordonnées correspondant à $indexMax
        $compteur = 0;
        for ($ligne = 0; $ligne < 3; $ligne++) {
            for ($colonne = 0; $colonne < 3; $colonne++) {
                if ($tableau[$ligne][$colonne] === '') {
                    if ($compteur === $indexMax) {
                        return "$ligne $colonne";
                    }
                    $compteur++;
                }
            }
        }
    }
}

function decisionIA($tableau, $marque_ia, $marque_adversaire, $mode) {
    $mode = intval($mode);
    switch ($mode) {
        case 0:
            return decisionIAAleatoire($tableau, $marque_ia, $marque_adversaire);
        case 1:
            return decisionIAProfondeur1($tableau, $marque_ia, $marque_adversaire);
        case 2:
            return decisionIAProfondeur2($tableau, $marque_ia, $marque_adversaire);
        default:
            return '';
    }
}

function traductionEmplacement($position) {
    // Vérifie si la position est valide (entre 1 et 9)
    if ($position < 1 || $position > 9) {
        return ''; // Retourne une chaîne vide si la position est invalide
    }
    // Calcule la ligne en divisant (position - 1) par 3, et en prenant la partie entière
    $ligne = intval(($position - 1) / 3);
    // Calcule la colonne en prenant le reste de la division de (position - 1) par 3
    $colonne = ($position - 1) % 3;
    // Retourne les coordonnées ligne et colonne sous forme de chaîne "ligne colonne"
    return "$ligne $colonne";
}

function jouer_morpion($n_parties, $ia, $mode) {
    // Initialisation des scores pour les joueurs et les matchs nuls
    $scoreJoueur1 = 0;
    $scoreJoueur2 = 0;
    $scoreNul = 0;
    // Les marques des joueurs
    $joueur1 = 'X';
    $joueur2 = 'O';
    // Boucle pour jouer un nombre défini de parties
    for ($i = 1; $i <= $n_parties; $i++) {
        if ($i > 1) {
            // Pause entre les parties
            echo "Appuyez sur n'importe quelle touche pour lancer la partie ".$i."...";
            $input = trim(fgets(STDIN));
        }
        // Choix aléatoire du joueur qui commence
        $joueurActuel = random_int(0, 1) ? $joueur2 : $joueur1;
        // Initialisation d'un tableau vide pour une nouvelle partie
        $tableau = [
            ['', '', ''],
            ['', '', ''],
            ['', '', '']
        ];
        echo "\n=== Partie $i ===\n";
        // Boucle principale pour chaque tour de jeu
        while (true) {
            // Affiche l'état actuel du tableau
            afficherTableau($tableau);
            // Si l'IA est activée et que c'est son tour
            if ($ia && $joueurActuel === $joueur2) {
                // L'IA décide d'une position à jouer
                list($ligne, $colonne) = explode(" ", decisionIA($tableau, $joueur2, $joueur1, $mode));
                echo "IA ($joueur2) joue : $ligne $colonne\n";
            } else {
                // Le joueur humain entre sa position
                echo "Joueur $joueurActuel, entrez la position 1-9 pour placer votre marque : ";
                $input = trim(fgets(STDIN));
                // Convertir l'entrée du joueur en coordonnées
                $input = traductionEmplacement($input);
                list($ligne, $colonne) = explode(" ", $input);
            }
            // Vérification si la case est valide et vide
            if (isset($tableau[$ligne][$colonne]) && $tableau[$ligne][$colonne] === '') {
                // Placement de la marque du joueur actuel
                $tableau[$ligne][$colonne] = $joueurActuel;
                // Vérification si un joueur a gagné
                $gagnant = verifierGagnant($tableau);
                if ($gagnant) {
                    afficherTableau($tableau);
                    echo "Le joueur $gagnant a gagné !\n";
                    // Mise à jour des scores
                    if ($gagnant === $joueur1) {
                        $scoreJoueur1++;
                    } elseif ($gagnant === $joueur2) {
                        $scoreJoueur2++;
                    }
                    break; // Fin de la partie
                }
                // Vérification si le tableau est plein (match nul)
                if (leTableauEstPlein($tableau)) {
                    afficherTableau($tableau);
                    echo "C'est un match nul !\n";
                    $scoreNul++;
                    break; // Fin de la partie
                }
                // Changement de joueur
                $joueurActuel = ($joueurActuel === $joueur1) ? $joueur2 : $joueur1;
            } else {
                // Si la case est occupée ou l'entrée invalide
                echo "La case est déjà occupée ou invalide, essayez une autre case.\n";
            }
        }
        // Affichage des scores après chaque partie
        echo "\nScores après $i partie(s) :\n";
        echo "Joueur ".$joueur1." : $scoreJoueur1\n";
        echo "Joueur ".$joueur2." : $scoreJoueur2\n";
        echo "Matchs nuls : $scoreNul\n";
    }
    // Affichage des résultats finaux
    echo "\n=== Résultat final ===\n";
    echo "Joueur ".$joueur1." : $scoreJoueur1\n";
    echo "Joueur ".$joueur2." : $scoreJoueur2\n";
    echo "Matchs nuls : $scoreNul\n";
    // Déclaration du grand gagnant ou égalité
    if ($scoreJoueur1 > $scoreJoueur2) {
        echo "Le grand gagnant est le Joueur ".$joueur1." !\n";
    } elseif ($scoreJoueur2 > $scoreJoueur1) {
        echo "Le grand gagnant est le Joueur ".$joueur2." !\n";
    } else {
        echo "Aucun gagnant : c'est une égalité générale !\n";
    }
    // Pause avant de quitter
    echo "Appuyez sur n'importe quelle touche pour quitter les résultats...";
    $input = trim(fgets(STDIN));
}

function afficherChoix() {
    echo "+---------------------------------------------------------+\n";
    echo "|    Ecrit par - > CLEMENT Pierre, ADOUX Gabin            |\n";
    echo "|    Date - > Janvier 2025                                |\n";
    echo "|    Réalisé en - > php:8.4                               |\n";
    echo "+---------------------------------------------------------+\n";
    echo "|    R - > Règle du jeu                                   |\n";
    echo "|    J - > Jeu unique (2 joueurs)                         |\n";
    echo "|    C - > Challenge de 3 parties (2 joueurs)             |\n";
    echo "|    O - > Contre l'ordinateur                            |\n";
    echo "|    P - > Challenge de 3 parties contre l'ordinateur     |\n";
    echo "|    Q - > Quitter                                        |\n";
    echo "+---------------------------------------------------------+\n";
}

function afficherRegle() {
    echo "+--------------------------------------------------------------------------------------------------+\n";
    echo "|    Règles du Morpion :                                                                           |\n";
    echo "+--------------------------------------------------------------------------------------------------+\n";
    echo "|    1. Le jeu se joue sur une grille de 3x3 cases.                                                |\n";
    echo "|    2. Deux joueurs choisissent un symbole : 'X' ou 'O'.                                          |\n";
    echo "|    3. Chaque joueur place son symbole à tour de rôle.                                            |\n";
    echo "|    4. L'objectif est d'aligner trois symboles horizontalement, verticalement ou en diagonale.    |\n";
    echo "|    5. Le premier à aligner trois symboles gagne.                                                 |\n";
    echo "|    6. Si la grille est remplie sans alignement, la partie est nulle.                             |\n";
    echo "+--------------------------------------------------------------------------------------------------+\n";
}

function programme() {
    $run = true;
    while($run) {
        afficherChoix();
        echo "Saisissez votre choix : ";
        $i = trim(fgets(STDIN));
        switch ($i) {
            case 'r':
            case 'R':
                afficherRegle();
                echo "Appuyez sur n'importe quelle touche pour quitter les règles...";
                $input = trim(fgets(STDIN));
                break;
            case 'j':
            case 'J':
                jouer_morpion(1, false, 0);
                break;
            case 'c':
            case 'C':
                jouer_morpion(3, false, 0);
                break;
            case 'o':
            case 'O':
                echo "Saisissez le mode Aléatoire (0), Prédiction niveau 1 (1) ou Prédiction niveau 2 (2) : ";
                $input = trim(fgets(STDIN));
                jouer_morpion(1, true, $input);
                break;
            case 'p':
            case 'P':
                echo "Saisissez le mode Aléatoire (0), Prédiction niveau 1 (1) ou Prédiction niveau 2 (2) : ";
                $input = trim(fgets(STDIN));
                jouer_morpion(3, true, $input);
                break;
            case 'q':
            case 'Q':
                $run = false;
                break;
            default:
                break;
        }
    }
}

programme();
?>
