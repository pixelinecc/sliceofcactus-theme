# Migration

## ✅ Terminé

- Header
- Footer
- Structure du thème
- CPT
- Taxonomies
- Single Photo
- Archive Photo
- Créations (Dessin + Coloriage) : single, archives `/dessin/` et `/coloriage/` (taxonomie medium native), permaliens singles
- Récits : archive (journal, à la une + colonnes) et single (article Gutenberg)
- Projet 52 : page (Page Template), grille par année dérivée de la date de publication des photos
- Color Your Life : page (Page Template), séries de l'archive Photo re-triées par teinte dominante

## 📌 Décisions d'architecture

- Structure WordPress classique validée (template-parts/ + assets/), pas de dossiers components/ par fonctionnalité. Détail et raisons dans CLAUDE.md.
- Archives Dessin/Coloriage : taxonomie medium rendue publique (slug de rewrite vide) plutôt qu'un rewrite custom — voir taxonomy-medium.php et acf-json/taxonomy_soc_medium.json.
- Récits : le corps du texte utilise the_content() (Gutenberg) plutôt que le tableau `corps` d'Astro. Le champ relationnel `soc_recit_collections` (CPT `collection`) n'est pas câblé : ce CPT n'existe pas encore.
- Projet 52 : semaine/année calculées depuis la date de publication native du post Photo (narration `projet-52`), pas de nouveau champ ACF. Une Page WP de slug `projet-52` doit lui être assignée en admin (modèle « Projet 52 »).
- Color Your Life : ne filtre pas par narration (contrairement à ce que supposait l'audit) — ce sont les mêmes séries que l'archive Photo, re-triées par couleur. Une Page WP de slug `color-your-life` doit lui être assignée en admin (modèle « Color Your Life »).

## 🔄 En cours

- Audit validé

## ⏳ À faire

- Front page
- Carte des voyages
- Résonances