# Slice of Cactus — Instructions Claude Code

## Mission

Migrer le projet **sliceofcactus-astro** vers le thème **sliceofcactus-theme**.

Le projet Astro est la référence fonctionnelle, éditoriale et visuelle.

Le thème WordPress est la destination.

L'objectif est de reproduire fidèlement le projet Astro dans WordPress.

Ne pas réinventer le site.

---

# Philosophie

Toujours privilégier :

Astro → adaptation → WordPress

Jamais :

Astro → nouvelle idée → nouvelle architecture.

---

# Architecture éditoriale

Cette architecture est figée.

## Photo

Contient tous les projets photographiques :

- séries
- voyages
- Projet 52
- Color Your Life

Ne jamais déplacer les photos dans Création.

## Créations

Contient :

- dessins
- coloriages
- couture (plus tard)

Jamais de photographie.

## Récits

Contenus éditoriaux.

Utiliser Gutenberg pour le contenu.

## Résonances

Les Résonances relient :

- Photo
- Création
- Récit

C'est la seule évolution fonctionnelle par rapport au projet Astro.

---

# Règles de migration

Toujours chercher d'abord si le code existe déjà dans Astro.

Si oui :

- le réutiliser
- l'adapter
- éviter toute réécriture inutile

Ne jamais créer un composant si son équivalent Astro existe déjà.

---

# Développement

Travailler par lots.

Ne jamais commencer un nouveau lot sans validation du précédent.

Pour chaque lot :

1. analyser Astro
2. analyser le thème WordPress
3. modifier uniquement les fichiers concernés
4. tester
5. comparer avec Astro
6. proposer un commit

---

# ACF

Utiliser les champs natifs WordPress dès qu'ils suffisent.

Créer un champ ACF uniquement lorsqu'il apporte une vraie valeur.

Préférer une galerie ACF pour les séries Photo.

Ne jamais créer un nouveau CPT sans validation explicite.

---

# Organisation des fichiers

Décision validée le 2026-07-28.

Structure WordPress classique. Jamais de dossier `components/` par fonctionnalité.

Templates dans `template-parts/<contexte>/`.

CSS dans `assets/styles/`.

JavaScript dans `assets/scripts/`.

## Convention de nommage

Un même radical dans les trois arbres pour une même fonctionnalité.

Exemple :

template-parts/single/photo-contact-sheet.php
assets/styles/templates/single-photo.css
assets/scripts/single-photo.js

## Pourquoi

Astro ne s'organise pas en dossiers de composants par fonctionnalité : ce sont des fichiers de route avec script inline et un design system CSS partagé. La structure classique WordPress est donc la plus proche d'Astro, pas l'inverse.

Co-localiser CSS + JS + PHP dans un dossier `components/` n'apporte aucun bénéfice technique en WordPress classique (pas de compilateur, pas de scoping, pas de bundling). Ce serait de la complexité ajoutée sans contrepartie.

---

# CSS

Réutiliser le CSS Astro autant que possible.

Conserver les noms de classes lorsqu'ils restent cohérents.

Ne pas réécrire un composant CSS déjà fonctionnel.

---

# JavaScript

Conserver la logique Astro lorsqu'elle fonctionne.

Adapter uniquement :

- les sélecteurs
- les données
- les hooks WordPress

---

# Git

Un lot = un commit.

Toujours proposer un message de commit.

Ne jamais modifier plusieurs fonctionnalités dans le même commit.

---

# Foundations

Ne jamais utiliser le dépôt Foundations.

Il ne fait pas partie de cette migration.

---

# Si une décision est ambiguë

Toujours privilégier la solution :

- la plus simple
- la plus proche d'Astro
- la plus facile à maintenir

Ne jamais complexifier le projet sans raison.

---

# Objectif final

Le code n'est pas la finalité.

Le but est de permettre de publier rapidement :

- des photographies
- des créations
- des récits

Le code doit s'effacer derrière le contenu.