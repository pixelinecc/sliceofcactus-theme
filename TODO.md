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
- Carte des voyages : page (Page Template), carte Leaflet (CDN) + popups rendus en <template> côté serveur
- Front page : hero, univers/explorer, pellicule à la une, manifeste, récits à la une + effets transversaux (curseur, scroll-reveal, préloader, machine à écrire)
- Résonances : archive transversale (Photo + Création + Récits), pas d'équivalent Astro

## 📌 Décisions d'architecture

- Structure WordPress classique validée (template-parts/ + assets/), pas de dossiers components/ par fonctionnalité. Détail et raisons dans CLAUDE.md.
- Archives Dessin/Coloriage : taxonomie medium rendue publique (slug de rewrite vide) plutôt qu'un rewrite custom — voir taxonomy-medium.php et acf-json/taxonomy_soc_medium.json.
- Récits : le corps du texte utilise the_content() (Gutenberg) plutôt que le tableau `corps` d'Astro. Le champ relationnel `soc_recit_collections` (CPT `collection`) n'est pas câblé : ce CPT n'existe pas encore.
- Projet 52 : semaine/année calculées depuis la date de publication native du post Photo (narration `projet-52`), pas de nouveau champ ACF. Une Page WP de slug `projet-52` doit lui être assignée en admin (modèle « Projet 52 »).
- Color Your Life : ne filtre pas par narration (contrairement à ce que supposait l'audit) — ce sont les mêmes séries que l'archive Photo, re-triées par couleur. Une Page WP de slug `color-your-life` doit lui être assignée en admin (modèle « Color Your Life »).
- Carte des voyages : Leaflet chargé depuis le même CDN qu'Astro (unpkg), pas de bundle npm. CSS `.map`/`.pin`/`.mi-series`/`.map-info` d'Astro non repris : code mort, plus utilisé depuis le passage à Leaflet. Une Page WP de slug `voyage-carte` doit lui être assignée en admin (modèle « Carte des voyages »).
- Front page : le hero et la pellicule « 36 poses à la une » (100% picsum.photos dans Astro, aucune vraie donnée) puisent maintenant dans le contenu réel publié, avec repli gracieux (l'élément disparaît) si une catégorie est vide. Effets transversaux (curseur, boutons magnétiques, scroll-reveal, préloader, machine à écrire, parallaxe) ajoutés à cette occasion — ils n'existaient nulle part dans le thème WP. `[data-reveal]` était déjà posé sur plusieurs pages livrées sans jamais avoir été câblé.
- Bug corrigé au passage : la lightbox Création n'avait jamais ses règles CSS de base (`single-photo.css`, où elles vivaient seules, n'est jamais chargé sur les pages Création). Extraites dans `assets/styles/components/lightbox.css`, partagées par Photo, Création et la home.
- Résonances : bug corrigé sur `taxonomy_soc_resonance.json` (object_type n'incluait pas "photo" malgré la déclaration inverse côté CPT Photo). Taxonomie rendue publique, permalien `/resonances/{slug}/`. Pas de page « toutes les résonances » — à ajouter si besoin.

## 🔄 En cours

- Audit validé

## ⏳ À faire

- Tester tout le site en navigateur (rien n'a pu être vérifié visuellement pendant la migration)
- Importer le vrai contenu (series.json, recits.json) et flusher les permaliens une fois en place
- Assigner les Page Templates créées (Projet 52, Color Your Life, Carte des voyages) à de vraies Pages WP
- Pages statiques restantes (mentions légales, etc.)