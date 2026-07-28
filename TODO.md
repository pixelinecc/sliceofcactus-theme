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

## 📌 Décisions d'architecture

- Structure WordPress classique validée (template-parts/ + assets/), pas de dossiers components/ par fonctionnalité. Détail et raisons dans CLAUDE.md.
- Archives Dessin/Coloriage : taxonomie medium rendue publique (slug de rewrite vide) plutôt qu'un rewrite custom — voir taxonomy-medium.php et acf-json/taxonomy_soc_medium.json.
- Récits : le corps du texte utilise the_content() (Gutenberg) plutôt que le tableau `corps` d'Astro. Le champ relationnel `soc_recit_collections` (CPT `collection`) n'est pas câblé : ce CPT n'existe pas encore.

## 🔄 En cours

- Audit validé

## ⏳ À faire

- Front page
- Projet 52
- Color Your Life
- Carte des voyages
- Résonances