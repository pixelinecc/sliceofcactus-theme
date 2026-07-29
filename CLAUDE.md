# Slice of Cactus — Instructions Claude Code

## Référence du projet

Le thème WordPress est désormais la référence officielle de Slice of Cactus.

Le projet Astro est conservé uniquement comme archive historique.

Il ne doit être consulté que lorsqu'un comportement ou un choix de conception passé doit être vérifié.

Toute nouvelle fonctionnalité est conçue directement pour WordPress.

---

# Philosophie

Les décisions d'architecture sont figées.

Ne pas remettre en question les choix structurels du projet sans demande explicite.

Avant de proposer une modification d'architecture, se demander si elle répond à un besoin réel ou si elle complexifie inutilement le projet.

Toujours privilégier la solution WordPress native la plus simple.

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

# Mon rôle

Céline est la conceptrice du projet.

Les choix concernant :

- les CPT ;
- les taxonomies ;
- les champs ACF ;
- les relations ;
- les templates ;
- les URLs ;
- l'organisation du thème ;

sont des décisions fonctionnelles. Ne pas les remettre en question sauf demande explicite d'une réflexion d'architecture.

---

# Rôle de Claude Code

Assister le développement du thème :

- implémenter les fonctionnalités ;
- simplifier le code lorsque c'est possible ;
- détecter les bugs ;
- proposer des améliorations de qualité ;
- respecter les conventions déjà en place.

Éviter d'introduire :

- des abstractions inutiles ;
- des couches supplémentaires ;
- des règles de rewrite ;
- des helpers génériques ;
- des mécanismes anticipant des besoins hypothétiques.

Chaque ajout doit répondre à un besoin concret.

---

# Développement

Travailler par lots.

Ne jamais commencer un nouveau lot sans validation du précédent.

Pour chaque lot :

1. analyser le thème WordPress existant
2. modifier uniquement les fichiers concernés
3. tester
4. proposer un commit

---

## Principe de simplicité

Toujours utiliser en priorité les fonctionnalités natives de WordPress.

Avant d'ajouter du code personnalisé, vérifier si le besoin peut être couvert par :

- les menus WordPress
- la hiérarchie native des templates
- les archives de CPT
- les taxonomies
- Gutenberg
- les fonctions WordPress existantes

Ne pas ajouter :

- de règles de rewrite
- de routeur personnalisé
- de dispatcher complexe
- de helper abstrait
- de couche intermédiaire

sans démontrer que la solution native ne suffit pas.

Pour chaque ajout structurel, expliquer :

1. le besoin concret ;
2. pourquoi WordPress natif ne suffit pas ;
3. le coût de maintenance ajouté.

---

# ACF

Utiliser les champs natifs WordPress dès qu'ils suffisent.

Créer un champ ACF uniquement lorsqu'il apporte une vraie valeur.

Préférer une galerie ACF pour les séries Photo.

Ne jamais créer un nouveau CPT sans validation explicite.

---

## Organisation des fichiers

Décision figée le 2026-07-28.

Utiliser une structure WordPress classique.

- Templates : `template-parts/<contexte>/`
- CSS : `assets/styles/`
- JavaScript : `assets/scripts/`

Ne jamais créer de dossier `components/` regroupant PHP, CSS et JavaScript.

Pour une même fonctionnalité, conserver un radical de nommage cohérent entre les différents arbres.

Exemple :

- `template-parts/single/photo-contact-sheet.php`
- `assets/styles/templates/single-photo.css`
- `assets/scripts/single-photo.js`

Les assets restent chargés avec les fonctions WordPress d’enqueue.

Ne pas créer un fichier CSS ou JavaScript pour chaque petit template part. Regrouper les assets au niveau de la page ou de la fonctionnalité principale.

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

Respecter le design system existant (`assets/styles/settings/tokens.css` et les fichiers déjà en place).

Conserver les noms de classes lorsqu'ils restent cohérents.

Ne pas réécrire un composant CSS déjà fonctionnel.

---

# JavaScript

Conserver la logique déjà en place lorsqu'elle fonctionne.

Adapter uniquement ce qui est nécessaire à la nouvelle fonctionnalité.

---

# Git

Un lot = un commit.

Toujours proposer un message de commit.

Ne jamais modifier plusieurs fonctionnalités dans le même commit.

---

# Foundations

Ne jamais utiliser le dépôt Foundations.

Il ne fait pas partie de ce projet.

---

# Si une décision est ambiguë

Toujours privilégier la solution :

- la plus simple
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
