








# Audit complet de migration Astro → WordPress

## 1. Inventaire du projet Astro (référence)

### 1.1 Pages principales

| Page Astro | URL | Description |
|------------|-----|-------------|
| [index.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/index.astro:0:0-0:0) | `/` | Accueil avec hero polaroids, 3 univers, filmstrip, manifeste |
| `photo/index.astro` | [/photo](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/photo:0:0-0:0) | Archive photos avec filtres par rubrique |
| `photo/[id].astro` | `/photo/{id}` | Single photo avec planche-contact masonry |
| [voyage-carte.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/voyage-carte.astro:0:0-0:0) | `/voyage-carte` | Carte Leaflet interactive des voyages |
| [color-your-life.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/color-your-life.astro:0:0-0:0) | `/color-your-life` | Photos filtrées par couleur dominante |
| [projet-52.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/projet-52.astro:0:0-0:0) | `/projet-52` | Grille 52 semaines avec années |
| `dessin/index.astro` | [/dessin](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/dessin:0:0-0:0) | Archive dessins par technique |
| `dessin/[id].astro` | `/dessin/{id}` | Single dessin avec galerie |
| `coloriage/index.astro` | [/coloriage](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/coloriage:0:0-0:0) | Archive livres à colorier |
| `coloriage/[id].astro` | `/coloriage/{id}` | Single coloriage avec crédit livre |
| `recits/index.astro` | [/recits](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/recits:0:0-0:0) | Archive récits style journal |
| `recits/[id].astro` | `/recits/{id}` | Single récit avec article |
| [mentions-legales.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/mentions-legales.astro:0:0-0:0) | `/mentions-legales` | Page légale |

### 1.2 Composants Astro

| Composant | Fichier | Usage |
|-----------|---------|-------|
| Header | [Header.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/components/Header.astro:0:0-0:0) | Navigation principale |
| Footer | [Footer.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/components/Footer.astro:0:0-0:0) | Footer 3 colonnes + email |
| Base | [Base.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/layouts/Base.astro:0:0-0:0) | Layout HTML de base |

### 1.3 Styles CSS (810 lignes)

**Système complet dans** [public/css/style.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/public/css/style.css:0:0-0:0) :

- **Variables** : couleurs par univers, fonts, easings
- **Curseur personnalisé** : `.cursor` avec effet hover
- **Préloader** : compteur 00→36 pellicule
- **Navigation** : `.nav` fixe avec scroll hide
- **Hero** : blobs animés, polaroids, typewriter
- **Boutons** : `.btn--fill`, `.btn--line`
- **Sections** : `.section-head`, `.cards`, `.serie-grid`
- **Lightbox** : `.lightbox` avec navigation
- **Planche-contact** : `.contact-sheet`, `.sheet-grid`, `.pose` (masonry)
- **Magazine** : `.mag-runhead`, `.mag-masthead`, `.mag-sommaire`
- **View toggle** : `.view-toggle` (onglets)
- **Filtres** : `.rubchips`, `.spectrum` (couleurs)
- **Carte** : `.map`, `.pin`, `.dest-chips`
- **Projet 52** : `.p52-grid`, `.wk`, `.p52lb`
- **Coloriages** : `.colo-book`, `.colo-grid`, `.colo-card`, `.book-grid`
- **Récits** : `.journal-name`, `.journal-lead`, `.journal-cols`, `.article`
- **Home** : `.polas`, `.upanel`, `.xtile`, `.typewriter`, `.manifeste`
- **Reveal** : `[data-reveal]` avec IntersectionObserver
- **Responsive** : breakpoints 960px, 720px, 520px

### 1.4 JavaScript (186 lignes)

**Fichier** [public/js/main.js](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/public/js/main.js:0:0-0:0) :

1. **Préloader** (lignes 7-25) : compteur 00→36
2. **Curseur** (lignes 27-47) : suivi fluide souris
3. **Magnétique** (lignes 49-60) : effet sur `[data-magnetic]`
4. **Nav scroll** (lignes 62-71) : hide/solid au scroll
5. **Menu mobile** (lignes 73-85) : burger toggle
6. **Scroll reveal** (lignes 87-100) : IntersectionObserver

**Scripts inline dans les pages** :
- Lightbox (photo, coloriage, dessin)
- Masonry grid (planche-contact)
- Filtres côté client (photo, couleurs)
- Carte Leaflet (voyage-carte)
- Projet 52 (grille + lightbox)
- Typewriter (accueil)

### 1.5 Données JSON

- `public/data/series.json` : séries photo/dessin/coloriage
- `public/data/recits.json` : récits textuels

**Structure série** :
```json
{
  "id": "...",
  "titre": "...",
  "rubrique": "voyage|lifestyle|noir-et-blanc|dessin|coloriage",
  "accroche": "...",
  "couverture": "...",
  "images": [{src, alt}],
  "nb_poses": 36,
  "lieu": {nom, pays, lat, lng},
  "date": "YYYY-MM",
  "couleur_dominante": {nom, hex},
  "technique": {nom, medium},
  "carnet": {titre, auteur, editeur, jaquette}
}
```

---

## 2. Inventaire du thème WordPress (socle existant)

### 2.1 CPT et taxonomies (ACF JSON)

**CPT créés** :
- ✅ **Photo** ([post_type_soc_photo.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/post_type_soc_photo.json:0:0-0:0)) - slug `/photos/`
- ✅ **Création** ([post_type_soc_creation.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/post_type_soc_creation.json:0:0-0:0)) - slug `/creations/`
- ✅ **Récit** ([post_type_soc_recit.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/post_type_soc_recit.json:0:0-0:0)) - slug [/recits/](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/recits:0:0-0:0)

**Taxonomies créées** :
- ✅ **Résonance** ([taxonomy_soc_resonance.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/taxonomy_soc_resonance.json:0:0-0:0)) - associée à photo, création, récit
- ✅ **Narration** ([taxonomy_narration.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/taxonomy_narration.json:0:0-0:0)) - associée à photo uniquement
- ✅ **Medium** ([taxonomy_soc_medium.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/taxonomy_soc_medium.json:0:0-0:0)) - associée à création
- ✅ **Creation Type** ([taxonomy_soc_creation_type.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/taxonomy_soc_creation_type.json:0:0-0:0)) - associée à création

### 2.2 Groupes de champs ACF

**Photo** ([group_soc_photo.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/group_soc_photo.json:0:0-0:0)) :
- `soc_photo_intro` (textarea, 300 car)
- `soc_photo_type` (permanent/temporary)
- `soc_photo_state` (active/complete/paused)
- `soc_photo_resonances` (taxonomy multi-select)
- Autres champs à vérifier

**Création** ([group_soc_creation.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/group_soc_creation.json:0:0-0:0)) :
- Champs pour dessins/coloriages
- Résonances

**Récit** ([group_soc_recit.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/group_soc_recit.json:0:0-0:0)) :
- Champs éditoriaux
- Résonances

**Résonance** ([group_soc_resonance.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/group_soc_resonance.json:0:0-0:0)) :
- Métadonnées des termes

**Content Card** ([group_soc_content_card.json](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/acf-json/group_soc_content_card.json:0:0-0:0)) :
- Champs pour cartes de contenu

### 2.3 Templates PHP

**Fichiers racine** :
- ✅ [header.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/header.php:0:0-0:0) - navigation migrée
- ✅ [footer.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/footer.php:0:0-0:0) - footer migré
- ✅ [front-page.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/front-page.php:0:0-0:0) - page d'accueil (vide, 286 bytes)
- ✅ [index.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/index.php:0:0-0:0) - fallback
- ✅ [singular.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/singular.php:0:0-0:0) - fallback single
- ✅ [page.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/page.php:0:0-0:0) - pages statiques
- ✅ [single-photo.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/single-photo.php:0:0-0:0) - single photo (543 bytes)
- ✅ [404.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/404.php:0:0-0:0)

**Template parts** :
- [template-parts/content.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/template-parts/content.php:0:0-0:0) - contenu générique
- [template-parts/content-none.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/template-parts/content-none.php:0:0-0:0) - aucun résultat
- [template-parts/single/photo.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/template-parts/single/photo.php:0:0-0:0) - dispatcher photo
- [template-parts/single/photo-serie-classique.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/template-parts/single/photo-serie-classique.php:0:0-0:0) - série standard
- [template-parts/single/photo-voyage.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/template-parts/single/photo-voyage.php:0:0-0:0) - voyage
- [template-parts/single/photo-contact-sheet.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/template-parts/single/photo-contact-sheet.php:0:0-0:0) - planche-contact (6931 bytes)

### 2.4 Styles CSS WordPress

**Architecture** ([assets/styles/main.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/main.css:0:0-0:0)) :
```css
@import "settings/tokens.css";
@import "base/reset.css";
@import "base/typography.css";
@import "base/elements.css";
@import "layout/containers.css";
@import "components/header.css";
@import "components/navigation.css";
@import "components/footer.css";
@import "utilities/utilities.css";
```

**Fichiers présents** :
- ✅ [settings/tokens.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/settings/tokens.css:0:0-0:0) - variables CSS
- ✅ [base/reset.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/reset.css:0:0-0:0), [typography.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/typography.css:0:0-0:0), [elements.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/elements.css:0:0-0:0)
- ✅ [layout/containers.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/layout/containers.css:0:0-0:0)
- ✅ [components/header.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/header.css:0:0-0:0), [navigation.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/navigation.css:0:0-0:0), [footer.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/footer.css:0:0-0:0)
- ✅ [utilities/utilities.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/utilities/utilities.css:0:0-0:0)
- ✅ [templates/single-photo.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/templates/single-photo.css:0:0-0:0) - styles photo
- ✅ [editor.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/editor.css:0:0-0:0) - styles Gutenberg

### 2.5 JavaScript WordPress

**Fichiers** :
- ✅ `assets/scripts/main.js` - header + footer interactions (147 lignes)
- ✅ `assets/scripts/editor.js` - scripts éditeur

**Fonctionnalités migrées** :
- ✅ Header scroll behavior
- ✅ Menu mobile
- ✅ Footer smooth scroll
- ✅ Effet magnétique

**Manquants** :
- ❌ Curseur personnalisé
- ❌ Scroll reveal
- ❌ Préloader
- ❌ Lightbox
- ❌ Filtres côté client
- ❌ Masonry
- ❌ Typewriter

### 2.6 Fonctions PHP

**Fichiers** ([inc/](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-theme/inc:0:0-0:0)) :
- `setup.php` - theme supports, menus
- `assets.php` - enqueue CSS/JS
- [blocks.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/inc/blocks.php:0:0-0:0) - register blocks
- [block-editor.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/inc/block-editor.php:0:0-0:0) - catégorie blocs
- [patterns.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/inc/patterns.php:0:0-0:0) - catégorie patterns
- [template-tags.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/inc/template-tags.php:0:0-0:0) - helpers
- [queries.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/inc/queries.php:0:0-0:0) - requêtes réutilisables

---

## 3. Table de correspondance Astro ↔ WordPress

### 3.1 Architecture éditoriale

| Astro | WordPress | Statut |
|-------|-----------|--------|
| Séries photo (voyage, lifestyle, N&B) | CPT **Photo** + taxonomie **Narration** | ✅ Structure créée |
| Projet 52 | CPT **Photo** + narration "projet-52" | ✅ Structure créée |
| Color Your Life | CPT **Photo** + narration "color-your-life" | ✅ Structure créée |
| Dessins | CPT **Création** + taxonomy **Medium** "dessin" | ✅ Structure créée |
| Coloriages | CPT **Création** + taxonomy **Medium** "coloriage" | ✅ Structure créée |
| Récits textuels | CPT **Récit** | ✅ Structure créée |
| *(nouveau)* Résonances | Taxonomie **Résonance** | ✅ Structure créée |

### 3.2 Pages et templates

| Page Astro | Template WordPress | Statut |
|------------|-------------------|--------|
| `/` (accueil) | [front-page.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/front-page.php:0:0-0:0) | 🟡 Fichier existe (vide) |
| [/photo](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/photo:0:0-0:0) (archive) | `archive-photo.php` | ❌ À créer |
| `/photo/{id}` (single) | [single-photo.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/single-photo.php:0:0-0:0) + partials | 🟡 Structure amorcée |
| `/voyage-carte` | `page-voyage-carte.php` | ❌ À créer |
| `/color-your-life` | `page-color-your-life.php` | ❌ À créer |
| `/projet-52` | `page-projet-52.php` | ❌ À créer |
| [/dessin](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/dessin:0:0-0:0) (archive) | `archive-creation.php` (filtré) | ❌ À créer |
| `/dessin/{id}` | `single-creation.php` (dessin) | ❌ À créer |
| [/coloriage](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/coloriage:0:0-0:0) (archive) | `archive-creation.php` (filtré) | ❌ À créer |
| `/coloriage/{id}` | `single-creation.php` (coloriage) | ❌ À créer |
| [/recits](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/recits:0:0-0:0) (archive) | `archive-recit.php` | ❌ À créer |
| `/recits/{id}` | `single-recit.php` | ❌ À créer |
| `/mentions-legales` | `page-mentions-legales.php` | ❌ À créer |

### 3.3 Composants

| Composant Astro | WordPress | Statut |
|-----------------|-----------|--------|
| [Header.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/components/Header.astro:0:0-0:0) | [header.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/header.php:0:0-0:0) | ✅ Migré |
| [Footer.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/components/Footer.astro:0:0-0:0) | [footer.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/footer.php:0:0-0:0) | ✅ Migré |
| [Base.astro](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/layouts/Base.astro:0:0-0:0) | Structure thème | ✅ Migré |

### 3.4 Styles CSS

| Styles Astro | WordPress | Statut |
|--------------|-----------|--------|
| Variables (`:root`) | [settings/tokens.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/settings/tokens.css:0:0-0:0) | ✅ Migré |
| Reset | [base/reset.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/reset.css:0:0-0:0) | ✅ Migré |
| Typography | [base/typography.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/typography.css:0:0-0:0) | ✅ Migré |
| Elements | [base/elements.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/elements.css:0:0-0:0) | ✅ Migré |
| Containers | [layout/containers.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/layout/containers.css:0:0-0:0) | ✅ Migré |
| Header | [components/header.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/header.css:0:0-0:0) | ✅ Migré |
| Navigation | [components/navigation.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/navigation.css:0:0-0:0) | ✅ Migré |
| Footer | [components/footer.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/footer.css:0:0-0:0) | ✅ Migré |
| **Curseur** | ❌ Manquant | ❌ À migrer |
| **Préloader** | ❌ Manquant | ❌ À migrer |
| **Hero** | ❌ Manquant | ❌ À migrer |
| **Boutons** | ❌ Manquant | ❌ À migrer |
| **Lightbox** | ❌ Manquant | ❌ À migrer |
| **Planche-contact** | [templates/single-photo.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/templates/single-photo.css:0:0-0:0) (partiel) | 🟡 Partiel |
| **Magazine** | ❌ Manquant | ❌ À migrer |
| **View toggle** | ❌ Manquant | ❌ À migrer |
| **Filtres** | ❌ Manquant | ❌ À migrer |
| **Carte** | ❌ Manquant | ❌ À migrer |
| **Projet 52** | ❌ Manquant | ❌ À migrer |
| **Coloriages** | ❌ Manquant | ❌ À migrer |
| **Récits** | ❌ Manquant | ❌ À migrer |
| **Home (polaroids, univers, etc.)** | ❌ Manquant | ❌ À migrer |
| **Reveal** | ❌ Manquant | ❌ À migrer |

### 3.5 JavaScript

| Script Astro | WordPress | Statut |
|--------------|-----------|--------|
| Header scroll | `assets/scripts/main.js` | ✅ Migré |
| Menu mobile | `assets/scripts/main.js` | ✅ Migré |
| Footer scroll | `assets/scripts/main.js` | ✅ Migré |
| Magnétique | `assets/scripts/main.js` | ✅ Migré |
| **Préloader** | ❌ Manquant | ❌ À migrer |
| **Curseur** | ❌ Manquant | ❌ À migrer |
| **Scroll reveal** | ❌ Manquant | ❌ À migrer |
| **Lightbox** | ❌ Manquant | ❌ À migrer |
| **Masonry** | ❌ Manquant | ❌ À migrer |
| **Filtres photo** | ❌ Manquant | ❌ À migrer |
| **Filtres couleur** | ❌ Manquant | ❌ À migrer |
| **Carte Leaflet** | ❌ Manquant | ❌ À migrer |
| **Projet 52** | ❌ Manquant | ❌ À migrer |
| **Typewriter** | ❌ Manquant | ❌ À migrer |

### 3.6 Données

| Données Astro | WordPress | Statut |
|---------------|-----------|--------|
| `series.json` | CPT Photo + Création | ❌ À importer |
| `recits.json` | CPT Récit | ❌ À importer |
| Métadonnées série | Champs ACF | 🟡 Structure créée, données manquantes |

---

## 4. Champs ACF manquants (vs Astro)

| Donnée Astro | Champ ACF WordPress | Statut |
|--------------|---------------------|--------|
| `rubrique` (voyage/lifestyle/N&B) | Taxonomie **Narration** ou champ ACF | ✅ Narration existe |
| `couleur_dominante` {nom, hex} | ❌ Manquant | ❌ À créer |
| `lieu` {nom, pays, lat, lng} | ❌ Manquant | ❌ À créer |
| `date` (YYYY-MM) | ❌ Manquant | ❌ À créer |
| `nb_poses` | ❌ Manquant | ❌ Calculé ou champ |
| `technique` {nom, medium} | Taxonomie Medium | ✅ Existe |
| `carnet` {titre, auteur, editeur, jaquette} | ❌ Manquant | ❌ À créer |
| `images` (galerie) | Gutenberg Gallery ou ACF Gallery | 🟡 À définir |

---

## 5. Tableau d'avancement de migration

### 5.1 Fondations

| Élément | Statut | Détails |
|---------|--------|---------|
| **CPT Photo** | ✅ | Créé, slug `/photos/`, supports title/editor/thumbnail/excerpt/revisions |
| **CPT Création** | ✅ | Créé, slug `/creations/`, taxonomies medium + creation_type |
| **CPT Récit** | ✅ | Créé, slug [/recits/](cci:9://file:///C:/Users/celin/Shokola/sliceofcactus-astro/src/pages/recits:0:0-0:0) |
| **Taxonomie Résonance** | ✅ | Créée, associée aux 3 CPT |
| **Taxonomie Narration** | ✅ | Créée, associée à Photo uniquement |
| **Taxonomie Medium** | ✅ | Créée, associée à Création |
| **Taxonomie Creation Type** | ✅ | Créée, associée à Création |
| **Groupes ACF Photo** | 🟡 | Créé mais incomplet (manque lieu, couleur, date) |
| **Groupes ACF Création** | 🟡 | Créé mais à vérifier vs Astro |
| **Groupes ACF Récit** | 🟡 | Créé mais à vérifier vs Astro |

### 5.2 Composants de base

| Composant | Statut | Fichier |
|-----------|--------|---------|
| **Header** | ✅ | [header.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/header.php:0:0-0:0) - navigation migrée |
| **Footer** | ✅ | [footer.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/footer.php:0:0-0:0) - footer migré |
| **Structure HTML** | ✅ | Layout WordPress standard |

### 5.3 Styles globaux

| Styles | Statut | Fichier |
|--------|--------|---------|
| **Variables CSS** | ✅ | [settings/tokens.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/settings/tokens.css:0:0-0:0) |
| **Reset** | ✅ | [base/reset.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/reset.css:0:0-0:0) |
| **Typography** | ✅ | [base/typography.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/typography.css:0:0-0:0) |
| **Elements** | ✅ | [base/elements.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/base/elements.css:0:0-0:0) |
| **Containers** | ✅ | [layout/containers.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/layout/containers.css:0:0-0:0) |
| **Header** | ✅ | [components/header.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/header.css:0:0-0:0) |
| **Navigation** | ✅ | [components/navigation.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/navigation.css:0:0-0:0) |
| **Footer** | ✅ | [components/footer.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/components/footer.css:0:0-0:0) |
| **Utilities** | ✅ | [utilities/utilities.css](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/assets/styles/utilities/utilities.css:0:0-0:0) |

### 5.4 JavaScript de base

| Script | Statut | Fichier |
|--------|--------|---------|
| **Header scroll** | ✅ | `assets/scripts/main.js` |
| **Menu mobile** | ✅ | `assets/scripts/main.js` |
| **Footer scroll** | ✅ | `assets/scripts/main.js` |
| **Magnétique** | ✅ | `assets/scripts/main.js` |

### 5.5 Page d'accueil

| Élément | Statut | Détails |
|---------|--------|---------|
| **Template** | 🟡 | [front-page.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/front-page.php:0:0-0:0) existe (vide) |
| **Hero polaroids** | ❌ | HTML + CSS + JS à migrer |
| **Blobs animés** | ❌ | CSS animations à migrer |
| **Typewriter** | ❌ | JS à migrer |
| **Section "3 univers"** | ❌ | `.upanel` CSS + HTML à migrer |
| **Section "Explorer"** | ❌ | `.xtile` CSS + HTML à migrer |
| **Filmstrip** | ❌ | `.filmstrip` + `.frame` CSS à migrer |
| **Manifeste** | ❌ | `.manifeste` CSS + HTML à migrer |
| **Récits à la une** | ❌ | `.home-recits` CSS + requête WP à migrer |

### 5.6 Archive Photo

| Élément | Statut | Détails |
|---------|--------|---------|
| **Template** | ❌ | `archive-photo.php` à créer |
| **Mag runhead** | ❌ | `.mag-runhead` CSS à migrer |
| **Mag masthead** | ❌ | `.mag-masthead` CSS à migrer |
| **View toggle** | ❌ | `.view-toggle` CSS + JS à migrer |
| **Filtres rubrique** | ❌ | `.rubchips` CSS + JS à migrer |
| **Grille séries** | ❌ | `.serie-grid` + `.serie-cell` CSS à migrer |

### 5.7 Single Photo

| Élément | Statut | Détails |
|---------|--------|---------|
| **Template dispatcher** | 🟡 | [single-photo.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/single-photo.php:0:0-0:0) + partials existent |
| **Hero série** | ❌ | `.serie-hero` CSS à migrer |
| **Planche-contact** | 🟡 | [photo-contact-sheet.php](cci:7://file:///C:/Users/celin/Shokola/sliceofcactus-theme/template-parts/single/photo-contact-sheet.php:0:0-0:0) existe, CSS partiel |
| **Masonry grid** | ❌ | JS masonry à migrer |
| **Lightbox** | ❌ | `.lightbox` CSS + JS à migrer |
| **Séparateurs pellicule** | ❌ | `.pellicule-sep` CSS à migrer |
| **Suggestions** | ❌ | `.more-series` CSS + requête WP à migrer |

### 5.8 Pages spéciales Photo

| Page | Statut | Détails |
|------|--------|---------|
| **Voyage - La carte** | ❌ | `page-voyage-carte.php` + Leaflet + CSS à créer |
| **Color Your Life** | ❌ | `page-color-your-life.php` + filtres couleur + CSS à créer |
| **Projet 52** | ❌ | `page-projet-52.php` + grille + lightbox + CSS à créer |

### 5.9 Créations (Dessin + Coloriage)

| Élément | Statut | Détails |
|---------|--------|---------|
| **Archive dessins** | ❌ | Template + `.book-grid` CSS à créer |
| **Archive coloriages** | ❌ | Template + `.book-grid` CSS à créer |
| **Single dessin** | ❌ | Template + galerie + CSS à créer |
| **Single coloriage** | ❌ | Template + crédit livre + galerie + CSS à créer |
| **Lightbox coloriage** | ❌ | Lightbox spécifique + détection orientation à créer |

### 5.10 Récits

| Élément | Statut | Détails |
|---------|--------|---------|
| **Archive** | ❌ | `archive-recit.php` + `.journal-name` + `.journal-cols` CSS à créer |
| **Single** | ❌ | `single-recit.php` + `.article` CSS à créer |
| **Mise en avant** | ❌ | `.journal-lead` CSS à créer |
| **Drop cap** | ❌ | `.drop` CSS à migrer |

### 5.11 Fonctionnalités transversales

| Fonctionnalité | Statut | Détails |
|----------------|--------|---------|
| **Curseur personnalisé** | ❌ | `.cursor` CSS + JS à migrer |
| **Scroll reveal** | ❌ | `[data-reveal]` CSS + IntersectionObserver JS à migrer |
| **Préloader** | ❌ | `.preloader` CSS + JS compteur à migrer |
| **Lightbox générique** | ❌ | Composant réutilisable à créer |
| **Résonances** | 🟡 | Structure créée, affichage à développer |

### 5.12 Données

| Donnée | Statut | Détails |
|--------|--------|---------|
| **Import series.json** | ❌ | Script d'import vers Photo + Création à créer |
| **Import recits.json** | ❌ | Script d'import vers Récit à créer |
| **Champ couleur_dominante** | ❌ | ACF à créer (nom + hex) |
| **Champ lieu** | ❌ | ACF à créer (nom, pays, lat, lng) |
| **Champ date** | ❌ | ACF à créer (YYYY-MM) |
| **Champ carnet** | ❌ | ACF groupe à créer (titre, auteur, éditeur, jaquette) |

---

## 6. Synthèse

### ✅ Déjà migré (≈20%)

**Fondations solides** :
- Architecture éditoriale (3 CPT + 4 taxonomies)
- Composants de base (Header, Footer)
- Styles globaux (tokens, reset, typography, base)
- JavaScript de base (header, footer, menu mobile)
- Structure de fichiers WordPress

### 🟡 Partiellement migré (≈5%)

- Groupes ACF (structure créée, champs incomplets)
- Single Photo (dispatcher créé, templates partiels)
- Planche-contact (HTML existe, CSS partiel, JS manquant)

### ❌ À développer (≈75%)

**Templates** :
- Page d'accueil complète
- Archives (Photo, Création, Récit)
- Singles (Création, Récit)
- Pages spéciales (Carte, Color Your Life, Projet 52)

**Styles CSS** :
- ~600 lignes de CSS Astro à migrer
- Tous les composants spécifiques (hero, polaroids, lightbox, grilles, etc.)

**JavaScript** :
- ~140 lignes de JS Astro à migrer
- Tous les scripts inline des pages
- Lightbox, masonry, filtres, carte, etc.

**Données** :
- Champs ACF manquants
- Import des JSON
- Contenu de test

---

## 7. Incompatibilités techniques identifiées

**Aucune incompatibilité bloquante.**

Toutes les fonctionnalités Astro sont réalisables en WordPress :
- Leaflet fonctionne en WordPress
- Lightbox, masonry, filtres : JS vanilla compatible
- Styles CSS : 100% réutilisables
- Structure de données : mappable vers ACF

**Seules adaptations nécessaires** :
- Remplacer les boucles Astro par des `WP_Query`
- Remplacer les données JSON par des requêtes ACF
- Adapter les URLs (routing WordPress)
- Utiliser les hooks WordPress pour enqueue assets

---

## 8. Recommandations

### Ordre de migration (conforme au CDC)

1. ✅ **Socle** : fait
2. ✅ **Styles globaux** : fait
3. ✅ **Header/footer** : fait
4. ❌ **Page d'accueil** : priorité 1
5. ❌ **Archive Photo** : priorité 2
6. ❌ **Single Photo standard** : priorité 3
7. ❌ **Variantes Photo** (Carte, Projet 52, Couleurs) : priorité 4
8. ❌ **Archive/Single Créations** : priorité 5
9. ❌ **Archive/Single Récits** : priorité 6
10. ❌ **Résonances** (affichage) : priorité 7
11. ❌ **Pages statiques** : priorité 8
12. ❌ **Navigation transversale** : priorité 9
13. ❌ **Responsive** : continu
14. ❌ **Accessibilité** : continu
15. ❌ **SEO** : continu

### Champs ACF à créer en priorité

**Pour Photo** :
```
- soc_photo_lieu (groupe)
  - nom (text)
  - pays (text)
  - latitude (number)
  - longitude (number)
- soc_photo_date (date, format YYYY-MM)
- soc_photo_couleur (groupe)
  - nom (text)
  - hex (color picker)
- soc_photo_nb_poses (number)
```

**Pour Création (coloriage)** :
```
- soc_creation_carnet (groupe)
  - titre (text)
  - auteur (text)
  - editeur (text)
  - jaquette (image)
```

### Prochaine étape immédiate

**Compléter les champs ACF** avant de développer les templates, pour avoir la structure de données complète.

Ensuite attaquer la **page d'accueil** qui donnera le ton visuel du site.

---

**Audit terminé. Aucun fichier modifié.**