# Audit pré-production — Slice of Cactus

Date de l'audit : 2026-07-31.

Statut global : à traiter point par point. Cocher chaque case au fur et à mesure des corrections appliquées, puis reporter dans TODO.md.

## Résumé général

Le thème est dans un état solide et cohérent : architecture éditoriale respectée (Photo / Atelier / Récits / Résonances), templates bien organisés selon la convention `template-parts/` + `assets/`, requêtes WordPress propres (`no_found_rows`, `ignore_sticky_posts`, tax_query correctement indentés), échappement des sorties globalement rigoureux, et bonne discipline sur le `prefers-reduced-motion` dans la quasi-totalité des animations. Le renommage **Création → Atelier** a été vérifié en profondeur (slugs, permaliens, redirections `/creations/` → `/atelier/`, labels publics, CSS, JS) : aucune incohérence résiduelle trouvée — le nettoyage a été fait sérieusement.

Le point le plus sérieux n'est pas un bug de logique métier mais un oubli probable de fin de migration : un mot de passe FTP en clair non protégé par un `.gitignore` (corrigé, voir #1). Le point #2 initialement soulevé (polices jamais chargées) s'est révélé être un faux positif de l'audit statique : les polices sont gérées via la Bibliothèque de polices native de WordPress, une configuration stockée en base et invisible depuis une lecture du thème — vérifié en fonctionnement sur le site en production. Le reste est composé d'incohérences mineures (CSS mort, variables CSS orphelines, un TODO fantôme, un défaut d'accessibilité clavier sur les panneaux au survol) qui se corrigent en quelques minutes chacune.

---

## Observations

### 1 — Critique — `.vscode/sftp.json` (absence de `.gitignore`)
- [x] Corrigé — `.gitignore` ajouté (commit `300f835`). Reste à ta charge : changer le mot de passe FTP chez Infomaniak par précaution.
**Problème constaté** : `.vscode/sftp.json` contient en clair l'hôte, l'identifiant et le mot de passe FTP de production (Infomaniak). Le dépôt ne contient aucun fichier `.gitignore`, nulle part.
**Impact réel** : le fichier est actuellement non suivi par Git (confirmé : jamais commité), donc rien n'a encore fuité. Mais sans `.gitignore`, un simple `git add -A`/`git add .` le committerait, et un futur push vers un remote exposerait publiquement le mot de passe FTP de production.
**Correction recommandée** : ajouter un `.gitignore` excluant au minimum `.vscode/`. Par précaution, envisager de changer ce mot de passe FTP puisqu'il est actuellement stocké en clair sur disque.
**Ampleur** : minime. **Certitude** : confirmé.

### 2 — ~~Critique~~ Faux positif — polices Anton/Cormorant Garamond/Newsreader/Space Mono
- [x] Non applicable — vérifié en production
**Constat initial** : aucun `@font-face` ni lien de police trouvé dans les fichiers du thème (recherche exhaustive sur le dépôt), ce qui laissait supposer que les polices déclarées dans `tokens.css` ne se chargeaient jamais.
**Correction du constat** : les 4 polices sont installées via la Bibliothèque de polices native de WordPress (Apparence → Polices), une donnée stockée en base (`wp_font_face`/`wp_font_family`) et donc invisible depuis une lecture du thème sur disque. Vérification directe sur https://sliceofcactus.fr/ : le `<head>` contient bien 14 règles `@font-face` dans le bloc `global-styles-inline-css`, fichiers `.woff2` servis depuis `wp-content/uploads/fonts/`. Le mécanisme natif fonctionne, rien à corriger dans le thème.
**Certitude** : confirmé (vérifié en production le 2026-07-31).

### 3 — Important — `assets/styles/components/panels.css`, `assets/styles/templates/front-page.css`
- [x] Corrigé — chaque règle `:hover` de `.upanel*`/`.xtile*` a désormais son équivalent `:focus-visible`/`:focus-within`.
**Problème constaté** : les panneaux `.upanel` ("Trois univers" en accueil, "Trois formes" sur À propos) et les tuiles `.xtile` ("Explorer autrement") ne révèlent leur description et leur CTA qu'au `:hover`. Aucune règle `:focus-within`/`:focus-visible` équivalente n'existe.
**Impact réel** : un visiteur au clavier peut activer ces liens mais ne voit jamais le texte descriptif ni le CTA que voit un visiteur à la souris.
**Correction recommandée** : dupliquer chaque règle `:hover` concernée en `:hover, :focus-within` dans `panels.css` et `front-page.css`.
**Ampleur** : minime. **Certitude** : confirmé.

### 4 — Important — `assets/styles/templates/page-a-propos.css` (variables CSS inexistantes)
- [x] Corrigé
**Problème constaté** : `.about-sequence-item .no` utilisait `var(--mono)` et `var(--cyan)` — et ne correspondait même à aucun élément réel : le HTML utilise `class="about-sequence-item__no"` (BEM), pas une classe `.no` imbriquée, donc ce numéro d'étape n'avait aucun style du tout, pas seulement des couleurs manquantes. `.about-sequence-item h3 em` utilisait `var(--serif)`/`var(--magenta)` mais ne correspond à aucun `<em>` réel dans le template (sélecteur mort). La media query mobile utilisait `var(--sec)` pour un padding, variable elle aussi jamais définie.
**Correction appliquée** : sélecteur renommé en `.about-sequence-item__no`, `var(--mono)` → `var(--font-mono)`, `var(--cyan)` → `var(--flash)` (même convention que `.pose__num`/`.frame__num`, numéros sur fond sombre). Règle `.about-sequence-item h3 em` supprimée (sélecteur mort). `var(--sec)` → `4rem`, la valeur de rythme entre sections déjà documentée pour cette page (`.about-forms.universe { padding-top: 4rem }`).
**Ampleur** : minime. **Certitude** : confirmé.

### 5 — Important — `assets/styles/templates/page-a-propos.css` (sélecteurs morts)
- [x] Corrigé — les trois blocs de règles obsolètes ont été supprimés (les deux media queries ne contenaient qu'eux, entièrement retirées ; `.about-invite__link` conservé dans la règle `prefers-reduced-motion`).
**Problème constaté** : `.about-demarche__steps`, `.about-forms__grid`, `.about-forms__card` (lignes ~306–329, dont une règle `prefers-reduced-motion`) ne correspondent à aucun élément du markup réel de `page-a-propos.php`, qui utilise `.about-sequence-item` et `.universe__panels`/`.upanel`.
**Impact réel** : aucun (code mort), résidu d'une version antérieure de la page.
**Correction recommandée** : supprimer ces trois blocs de règles obsolètes.
**Ampleur** : minime. **Certitude** : confirmé.

### 6 — Important — `assets/scripts/front-page.js` (préchargeur et `prefers-reduced-motion`)
- [x] Corrigé — le préchargeur passe directement en `is-done` quand `prefers-reduced-motion: reduce` est actif, sur le même modèle que le bloc machine à écrire du même fichier.
**Problème constaté** : le compteur du préchargeur (36 étapes × 42 ms, ~1,5 s minimum) s'exécute sans condition à chaque chargement de l'accueil, alors que les effets voisins du même fichier (machine à écrire, parallaxe du blob) vérifient explicitement `prefers-reduced-motion`.
**Impact réel** : les visiteurs sensibles au mouvement n'ont aucun moyen d'éviter cette animation précise ; ce délai s'impose aussi à chaque visite, y compris répétée.
**Correction recommandée** : basculer directement en `is-done` quand `prefers-reduced-motion` est actif, et/ou ne jouer le préchargeur qu'une fois par session.
**Ampleur** : minime. **Certitude** : confirmé (code) — effet ressenti à vérifier visuellement.

### 7 — Important — `assets/scripts/single-creation.js` + `assets/styles/templates/single-creation.css` (`.colo-card`)
- [x] Corrigé
**Problème constaté** : `.colo-card` démarre avec `aspect-ratio: 4/5` (cadrage portrait) ; le script ne rajoutait `.colo-card--wide` (bascule en `aspect-ratio: auto`/`object-fit: contain`, carte plus large sur 2 colonnes) qu'une fois l'image chargée (`naturalWidth` mesuré au `load`), causant un saut de mise en page pour les planches au format paysage non mises en cache.
**Correction appliquée** : pas besoin de nouveau champ ACF — WordPress stocke déjà la largeur/hauteur de chaque image. `template-parts/single/creation-contact-sheet.php` calcule maintenant l'orientation via `wp_get_attachment_image_src()` et pose `colo-card--wide` directement dans le HTML rendu ; la mesure côté client (`single-creation.js`) a été retirée, elle est devenue redondante.
**Ampleur** : minime (finalement, une fois la bonne fonction WP identifiée). **Certitude** : confirmé.

### 8 — Important — `template-parts/single/recit-article.php` (insertion de la "planche")
- [x] Corrigé
**Problème constaté** : en présentation "plate", le code cherchait la première occurrence de `</p>` dans tout le HTML rendu de `the_content()` et y insérait la planche juste après. Au-delà du simple mauvais positionnement, le vrai risque était de couper du HTML invalide si ce premier `</p>` se trouvait imbriqué dans un autre bloc (ex. la légende d'une citation), et non un paragraphe de premier niveau.
**Correction appliquée** : la recherche de `</p>` n'a lieu que si le contenu commence réellement par un paragraphe (`stripos( ltrim( $content_html ), '<p' ) === 0`) ; sinon, repli sur le comportement déjà existant (planche avant tout le contenu).
**Ampleur** : minime. **Certitude** : confirmé.

### 9 — Important — `acf-json/post_type_soc_photo.json` (labels admin)
- [x] Corrigé — tous les libellés réécrits avec l'accord féminin correct ("une/la photo"), sur le modèle des CPT Atelier et Récits.
**Problème constaté** : contrairement aux libellés soignés des CPT "Atelier" et "Récits", ceux de "Photos" comportent des fautes de français : « Tous les Photos », « Aucun photos trouvé », « Archives des Photo » / « Attributs des Photo », « Un lien vers un photo ».
**Impact réel** : visible uniquement dans l'admin WordPress, pas sur le site public — mais détonne par rapport au soin apporté ailleurs.
**Correction recommandée** : réécrire le tableau `labels` de `post_type_soc_photo.json` sur le modèle des deux autres CPT.
**Ampleur** : minime. **Certitude** : confirmé.

### 10 — Finition — `footer.php` (commentaire Pinterest orphelin)
- [x] Corrigé — commentaire supprimé.
**Problème constaté** : un commentaire `// TODO: remplacer par l'URL Pinterest réelle...` précède `$instagram_url`, mais aucun lien Pinterest n'existe nulle part dans le footer.
**Impact réel** : source de confusion pour une future intervention.
**Correction recommandée** : supprimer le commentaire orphelin, ou ajouter le lien Pinterest s'il est toujours prévu.
**Ampleur** : minime. **Certitude** : confirmé.

### 11 — Finition — `footer.php` (nommage de variable)
- [x] Corrigé — renommée en `$instagram_dessin_url`, alignement des `=` du bloc de variables refait.
**Problème constaté** : `$instagramdessin_url` (sans underscore) rompt la convention snake_case suivie partout ailleurs.
**Correction recommandée** : renommer en `$instagram_dessin_url`.
**Ampleur** : minime. **Certitude** : confirmé.

### 12 — Finition → Refonte — accents de narration
- [x] Corrigé (architecture changée)
**Vérifié dans l'admin** : les narrations existantes sont `voyage`, `lifestyle`, `portraits`, `projet-52`, `color-your-life` — pas de terme "noir & blanc". `portraits` existe bien et n'avait effectivement pas d'accent dédié.
**Constat initial** : seules "voyage" et "lifestyle" avaient un `--accent`/`--accent-deep` codé en dur dans `single-photo.css`, ce qui demande une modification de code à chaque nouvelle narration.
**Correction appliquée** (décision prise avec Céline, au-delà du simple correctif) : nouveau champ ACF `soc_narration_accent` (color_picker) sur la taxonomie Narration (`acf-json/group_soc_narration.json`), sur le même modèle que `soc_resonance_color`. `inc/template-tags.php` : nouvelle fonction `soc_get_narration_accent_color()` + `soc_get_photo_effective_accent_color()` (couleur propre à la photo → couleur de sa narration → défaut vert), utilisée par `soc_photo_accent_style()` et `soc_photo_theme_color_meta()`. Les règles CSS codées en dur `.soc-narration-voyage`/`.soc-narration-lifestyle` ont été retirées de `single-photo.css`.
**Action requise dans l'admin après déploiement** : renseigner la couleur d'accent sur les termes existants pour ne pas perdre leur teinte actuelle — Voyage `#27513e`, Lifestyle `#e11d74`. Portraits (et toute narration future) peut recevoir sa propre couleur directement dans l'admin, sans intervention de code.
**Ampleur** : modérée (nouveau champ + logique PHP). **Certitude** : confirmé.

### 13 — Finition — `acf-json/taxonomy_narration.json` (`with_front`)
- [x] Corrigé — aligné sur `"0"`, comme les trois autres taxonomies. Confirmé sans effet visible (l'URL réelle testée par Céline, `/narration/voyage/`, ne changeait déjà rien à ce comportement) ; penser à ré-enregistrer les permaliens (Réglages → Permaliens → Enregistrer) après déploiement par bonne pratique.
**Bonus traité au passage** : les mêmes fautes de grammaire que celles corrigées au point #9 pour le CPT Photo (« Tous les Narration », « Aucun narration trouvé »…) ont été corrigées ici aussi, avec l'accord féminin correct ("une/la narration").

### 14 — Finition — Absence de `search.php`
- [ ] Corrigé
**Problème constaté** : les 3 CPT ont `exclude_from_search: false`, mais aucun formulaire de recherche n'existe dans l'interface, et il n'y a pas de `search.php` dédié — `index.php` sert de repli, avec un `<h1>` qui affiche toujours le nom du site plutôt qu'un titre contextuel.
**Impact réel** : `/?s=...` reste techniquement atteignable. Impact faible tant que l'endpoint n'est pas mis en avant.
**Correction recommandée** : optionnel — ajouter un `search.php` simple si la recherche doit un jour être exposée.
**Ampleur** : minime. **Certitude** : confirmé (code), impact réel à vérifier.

### 15 — Finition — `inc/blocks.php` / `inc/patterns.php` (scaffolding sans effet)
- [ ] Corrigé
**Problème constaté** : ces fichiers scannent `/blocks/*/block.json` et enregistrent une catégorie de patterns, mais aucun dossier `/blocks` ni `/patterns` n'existe dans le thème.
**Impact réel** : aucun actuellement (no-op silencieux).
**Correction recommandée** : garder si des blocks/patterns custom sont prévus prochainement, sinon retirer.
**Ampleur** : minime. **Certitude** : confirmé.

### 16 — Finition — `assets/styles/settings/tokens.css` (alias couleur hérités d'Astro)
- [x] Corrigé — `var(--coral)`/`var(--salmon)` remplacés par `var(--accent)` dans les 4 fichiers concernés, les 3 alias supprimés de `tokens.css`.
**Problème constaté** : `--coral`, `--salmon`, `--flash-pink` pointent tous désormais vers `--accent` (même valeur) mais sont encore référencés dans 4 fichiers CSS (footer, navigation, archive-photo, front-page).
**Impact réel** : aucun visuellement (synonymes exacts), mais couche de nommage qui n'apporte plus rien.
**Correction recommandée** : remplacer par `var(--accent)` directement, et supprimer `--flash-pink` si vraiment inutilisé.
**Ampleur** : minime. **Certitude** : confirmé.

### 17 — Finition — `acf-json/group_soc_recit.json` (clé de champ ACF)
- [x] Corrigé — clé renommée en `field_soc_recit_photos` (aucune donnée existante sur ce champ, renommage sans risque). Penser à cliquer "Synchroniser" dans Réglages → Champs personnalisés si l'admin le propose.
**Problème constaté** : le champ "Photos associées" utilise une clé auto-générée (`field_6a6a0e4e0c94b`) au lieu de la convention `field_soc_recit_*` suivie par tous les autres champs du projet.
**Impact réel** : aucun fonctionnellement ; simple incohérence de maintenance.
**Correction recommandée** : à laisser tel quel pour l'instant (renommer une clé ACF sur un champ déjà utilisé est délicat) ; corriger seulement si ce groupe est retouché pour une autre raison.
**Ampleur** : minime (mais risquée si migration). **Certitude** : confirmé.

### 18 — Optionnel — `assets/scripts/front-page.js` (`.is-loaded`)
- [x] Corrigé — les deux appels `document.body.classList.add('is-loaded')` supprimés.
**Problème constaté** : le préchargeur ajoute `document.body.classList.add('is-loaded')`, mais aucune règle CSS dans tout le thème ne lit cette classe.
**Correction recommandée** : supprimer cette ligne, ou l'exploiter si un usage était prévu.
**Ampleur** : minime. **Certitude** : confirmé.

### 19 — Optionnel — Absence de balises SEO/OG dans le thème
- [ ] Corrigé
**Problème constaté** : aucune balise `meta description`, `canonical`, Open Graph ou Twitter Card n'est générée par le thème.
**Impact réel** : cohérent avec la consigne du projet de ne pas dupliquer ce qui relève d'une extension SEO. Mais sans extension active, WordPress natif ne génère rien de tout cela.
**Correction recommandée** : confirmer qu'une extension SEO (Yoast, RankMath, SEOPress…) est bien active en production.
**Ampleur** : n/a. **Certitude** : à vérifier dans WordPress.

### 20 — Optionnel — Curseur personnalisé et animations tierces
- [ ] Corrigé
**Problème constaté** : la boucle `requestAnimationFrame` qui fait suivre le curseur personnalisé (`main.js`) ne vérifie pas `prefers-reduced-motion`. Par ailleurs, la carte des voyages dépend de 3 domaines tiers (unpkg, CARTO, OSM) chargés sans alternative locale.
**Impact réel** : mineur dans les deux cas — le curseur est un remplacement 1:1 discret ; les CDN tiers impliquent des requêtes vers des tiers à chaque visite de `/voyage-carte/` (point RGPD/vie privée mineur).
**Correction recommandée** : aucune action requise sauf si une politique de confidentialité stricte est visée.
**Ampleur** : minime. **Certitude** : confirmé (code), pertinence à évaluer.

### 21 — Important — Aucun template pour l'archive de narration (trouvé par Céline)
- [x] Corrigé
**Problème constaté** : la taxonomie `narration` est publique, mais aucun `taxonomy-narration.php` n'existait. Une URL comme `/narration/voyage/` retombait donc sur `index.php`, le repli générique sans le style magazine-hub (le même repli identifié pour la recherche native, voir #14).
**Correction appliquée** : `taxonomy-narration.php` créé sur le modèle de `archive-photo.php` (mag-runhead, masthead, view-switch, grille de séries), scopé à une seule narration. `projet-52` et `color-your-life` ne l'atteignent jamais (redirigés avant, voir `inc/redirects.php`) ; toutes les autres narrations (voyage, lifestyle, portraits, et toute narration future) l'utilisent.
- `inc/queries.php` : nouvelle fonction `soc_get_photo_archive_series_by_narration()`.
- `inc/assets.php` : le CSS magazine-hub + archive-photo se charge aussi sur `is_tax('narration')`.
- `assets/styles/templates/archive-photo.css` : accent olive par défaut pour ces pages (`body.tax-narration`).
- `inc/template-tags.php` : `soc_narration_archive_accent_style()` applique la couleur d'accent propre à la narration (champ ajouté au point #12) sur sa page d'archive, avec repli sur l'olive par défaut.
**Ampleur** : modérée (nouveau template + petits ajustements). **Certitude** : confirmé.

---

## 5 corrections prioritaires
1. **#1** — Ajouter un `.gitignore` (exclure `.vscode/`), envisager de changer le mot de passe FTP. ✅ fait
2. **#3** — Ajouter `:focus-within` aux panneaux `.upanel`/`.xtile` pour l'accessibilité clavier. ✅ fait
3. **#4** — Corriger les 5 variables CSS cassées de `page-a-propos.css`. ✅ fait
4. **#7** — Traiter le CLS des `.colo-card`. ✅ fait
5. **#9** — Corriger les libellés admin du CPT Photo. ✅ fait

## Petites corrections rapides groupables
En un seul lot "nettoyage" : **#5** (sélecteurs morts page-a-propos), **#10** (TODO Pinterest orphelin), **#11** (nommage `$instagramdessin_url`), **#16** (alias couleur hérités), **#18** (`.is-loaded` mort), **#6** (préchargeur et reduced-motion).

## À ne pas modifier
- La répartition des colonnes du footer (déjà volontaire).
- Le curseur personnalisé et son design (`cursor: none` + `.cursor`), choix assumé de l'identité du site.
- Le CPT `creation` conservant sa clé technique interne malgré le label public "Atelier".
- Leaflet chargé depuis un CDN plutôt que bundlé (décision déjà actée dans TODO.md).
- La structure `template-parts/` + `assets/` (architecture figée selon CLAUDE.md).
- Le fonctionnement des Résonances (relation transversale Photo/Atelier/Récit).

## Ordre d'intervention proposé
1. Sécurité (#1) — trivial, à faire immédiatement. ✅ fait
2. Nettoyage CSS/JS rapide groupé (#5, #6, #10, #11, #16, #18). ✅ fait
3. Accessibilité clavier (#3). ✅ fait
4. Variables cassées page À propos (#4). ✅ fait
5. Labels admin Photo (#9), clé ACF (#17) — sans urgence. #17 fait, #9 restant.
6. Points "à vérifier" (#12, #13, #14, #19, #20) — à trancher une fois le contenu réel confirmé.
7. CLS `.colo-card` (#7). ✅ fait
8. #8 (insertion de la planche récit) — à surveiller, pas de correction de code prévue pour l'instant.

## Tests manuels à effectuer après correction
- Vérifier au DevTools que les 4 polices se chargent bien (onglet Network → Fonts) et que le rendu visuel change sur titres/mastheads/légendes.
- Naviguer au clavier (Tab) dans "Trois univers" (accueil) et "Trois formes" (À propos) : la description et le CTA doivent apparaître au focus comme au survol.
- Recharger l'accueil avec `prefers-reduced-motion: reduce` activé (DevTools → Rendering) : vérifier le comportement du préchargeur.
- Ouvrir une planche au format paysage sur `/atelier/` en réseau throttled : observer si un saut de mise en page se produit.
- Vérifier dans `wp-admin → Photos` que les libellés corrigés s'affichent correctement.
- Confirmer via `git status`/`git log` que `.vscode/sftp.json` reste bien ignoré après ajout du `.gitignore`.
- Vérifier dans l'admin la liste des termes de la taxonomie "Narration" pour trancher #12 et #13.
- Confirmer qu'une extension SEO est active et génère bien meta description/OG sur au moins une fiche Photo/Récit/Atelier.
