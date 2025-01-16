<?php
function afficherTableau($tableau) {
    echo "\n";
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            echo ' ' . ($tableau[$i][$j] === '' ? ' ' : $tableau[$i][$j]) . ' ';
            if ($j < 2) echo '|';
        }
        echo "\n";
        if ($i < 2) echo "---+---+---\n";
    }
    echo "\n";
}

function verifierGagnant($tableau) {
    for ($i = 0; $i < 3; $i++) {
        if ($tableau[$i][0] === $tableau[$i][1] && $tableau[$i][1] === $tableau[$i][2] && $tableau[$i][0] !== '') {
            return $tableau[$i][0];
        }
    }
    for ($i = 0; $i < 3; $i++) {
        if ($tableau[0][$i] === $tableau[1][$i] && $tableau[1][$i] === $tableau[2][$i] && $tableau[0][$i] !== '') {
            return $tableau[0][$i];
        }
    }
    if ($tableau[0][0] === $tableau[1][1] && $tableau[1][1] === $tableau[2][2] && $tableau[0][0] !== '') {
        return $tableau[0][0];
    }
    if ($tableau[0][2] === $tableau[1][1] && $tableau[1][1] === $tableau[2][0] && $tableau[0][2] !== '') {
        return $tableau[0][2];
    }
    return '';
}

function leTableauEstPlein($tableau) {
    foreach ($tableau as $ligne) {
        if (in_array('', $ligne)) {
            return false;
        }
    }
    return true;
}

function afficherChoix() {
    echo "+-------------------------------------------------+\n";
    echo "|    Ecrit par - > CLEMENT Pierre, ADOUX Gabin    |\n";
    echo "|    Date - > Janvier 2025                        |\n";
    echo "|    Réalisé en - > php:8.4                       |\n";
    echo "+-------------------------------------------------+\n";
    echo "|    R - > Règle du jeu                           |\n";
    echo "|    J - > Jeu unique (2 joueurs)                 |\n";
    echo "|    C - > Challenge de 3 parties (2 joueurs)     |\n";
    echo "|    O - > Contre l'ordinateur                    |\n";
    echo "|    Q - > Quitter                                |\n";
    echo "+-------------------------------------------------+\n";
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

function decisionIAAleatoire($tableau, $marque_ia, $marque_adversaire) {
    $positionsLibres = [];
    for ($ligne = 0; $ligne < 3; $ligne++) {
        for ($colonne = 0; $colonne < 3; $colonne++) {
            if ($tableau[$ligne][$colonne] === '') {
                $positionsLibres[] = "$ligne $colonne";
            }
        }
    }
    if (!empty($positionsLibres)) {
        return $positionsLibres[array_rand($positionsLibres)];
    }
    return '';
}

function decisionIAProfondeur1($tableau, $marque_ia, $marque_adversaire) {
    if (empty($tableau)) {
        // retourner coin et centre en random
        echo "tableau vide";
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

function decisionIA($tableau, $marque_ia, $marque_adversaire, $mode) {
    $mode = intval($mode);
    switch ($mode) {
        case 0:
            return decisionIAAleatoire($tableau, $marque_ia, $marque_adversaire);
        case 1:
            return decisionIAProfondeur1($tableau, $marque_ia, $marque_adversaire);
        default:
            return '';
    }
}

function traductionEmplacement($position) {
    if ($position < 1 || $position > 9) {
        return '';
    }
    $ligne = intval(($position - 1) / 3);
    $colonne = ($position - 1) % 3;
    return "$ligne $colonne";
}

function jouer_morpion($n_parties, $ia, $mode) {
    $scoreJoueur1 = 0;
    $scoreJoueur2 = 0;
    $scoreNul = 0;
    $joueur1 = 'X';
    $joueur2 = 'O';

    for ($i = 1; $i <= $n_parties; $i++) {
        if ($i>1) {
            echo "Appuyez sur n'importe quelle touche pour lancer la partie ".$i."...";
            $input = trim(fgets(STDIN));
        }

        $joueurActuel = ($i % 2 == 0) ? $joueur2 : $joueur1;
        $tableau = [
            ['', '', ''],
            ['', '', ''],
            ['', '', '']
        ];

        echo "\n=== Partie $i ===\n";
        while (true) {
            afficherTableau($tableau);

            if ($ia && $joueurActuel === $joueur2) {
                list($ligne, $colonne) = explode(" ", decisionIA($tableau, $joueur2, $joueur1, $mode));
                echo "IA ($joueur2) joue : $ligne $colonne\n";
            } else {
                echo "Joueur $joueurActuel, entrez la position 1-9 pour placer votre marque : ";
                $input = trim(fgets(STDIN));
                $input = traductionEmplacement($input);
                list($ligne, $colonne) = explode(" ", $input);
            }

            if (isset($tableau[$ligne][$colonne]) && $tableau[$ligne][$colonne] === '') {
                $tableau[$ligne][$colonne] = $joueurActuel;
                $gagnant = verifierGagnant($tableau);

                if ($gagnant) {
                    afficherTableau($tableau);
                    echo "Le joueur $gagnant a gagné !\n";
                    if ($gagnant === $joueur1) {
                        $scoreJoueur1++;
                    } elseif ($gagnant === $joueur2) {
                        $scoreJoueur2++;
                    }
                    break;
                }

                if (leTableauEstPlein($tableau)) {
                    afficherTableau($tableau);
                    echo "C'est un match nul !\n";
                    $scoreNul++;
                    break;
                }

                $joueurActuel = ($joueurActuel === $joueur1) ? $joueur2 : $joueur1;
            } else {
                echo "La case est déjà occupée ou invalide, essayez une autre case.\n";
            }
        }

        echo "\nScores après $i partie(s) :\n";
        echo "Joueur ".$joueur1." : $scoreJoueur1\n";
        echo "Joueur ".$joueur2." : $scoreJoueur2\n";
        echo "Matchs nuls : $scoreNul\n";
    }

    echo "\n=== Résultat final ===\n";
    echo "Joueur ".$joueur1." : $scoreJoueur1\n";
    echo "Joueur ".$joueur2." : $scoreJoueur2\n";
    echo "Matchs nuls : $scoreNul\n";

    if ($scoreJoueur1 > $scoreJoueur2) {
        echo "Le grand gagnant est le Joueur ".$joueur1." !\n";
    } elseif ($scoreJoueur2 > $scoreJoueur1) {
        echo "Le grand gagnant est le Joueur ".$joueur2." !\n";
    } else {
        echo "Aucun gagnant : c'est une égalité générale !\n";
    }

    echo "Appuyez sur n'importe quelle touche pour quitter les résultats...";
    $input = trim(fgets(STDIN));
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
                echo "Saisissez le mode Aléatoire (0) ou Prédiction (1) : ";
                $input = trim(fgets(STDIN));
                jouer_morpion(1, true, $input);
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
