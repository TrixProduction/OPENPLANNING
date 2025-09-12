# OPENPLANNING
OPENPLANNING - Open Source For Uni Planning
![image](https://pokendycards.b-cdn.net/openp/fullogo.png)



# OPENPLANNING

Application open-source de consultation de planning universitaire (ciblée Lille) avec une interface proche de HYPERPLANNING, sans code propriétaire.

## Objet

* Reproduire une lecture claire du planning hebdomadaire.
* Offrir une interface familière (jours en colonnes, heures en lignes, blocs colorés).
* Rester simple, légère et autonome.

> Disclaimer — OPENPLANNING n’est pas affilié à HYPERPLANNING ni à l’Université de Lille. Les noms et marques cités appartiennent à leurs propriétaires.

## État actuel

* Prototype fonctionnel en page unique.
* Un seul fichier : `index.html`.
* Lecture locale d’un fichier `.ics` exporté depuis Hyperplanning.
* Aucun serveur requis.

## Fonctionnement

* Chargement d’un ICS standard (iCalendar).
* Normalisation basique des champs et rendu en grille hebdomadaire.
* Gestion des chevauchements par empilement visuel et partage de largeur.
* Affichage des informations clés : matière, enseignant, salle, groupe, statut (annulé).

## Limites connues

* Récurrences et exceptions iCalendar gérées partiellement.
* Parsing des méta-infos dépendant du format réel d’export.
* Impression et export PDF non finalisés.
* Pas d’API ni de persistance distante.
