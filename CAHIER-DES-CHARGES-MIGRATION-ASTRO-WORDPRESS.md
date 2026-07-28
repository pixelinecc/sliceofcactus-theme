# Cahier des charges — Migration Astro vers WordPress

## 1. Objet du projet

Migrer le site **Slice of Cactus** actuellement développé avec Astro vers un **thème WordPress classique sur mesure**, en réutilisant le socle WordPress existant.

Le projet Astro constitue la **référence fonctionnelle, éditoriale et visuelle**.

La mission consiste à **traduire le site existant dans WordPress**, et non à le repenser.

La seule évolution éditoriale majeure ajoutée pendant la migration est la notion de **Résonances**.

---

## 2. Principes non négociables

1. **Astro est la source de vérité.**
2. Ne pas inventer une nouvelle architecture lorsque le fonctionnement existe déjà dans Astro.
3. Ne pas modifier la direction artistique sans demande explicite.
4. Ne pas simplifier ou remplacer arbitrairement les compositions existantes.
5. Réutiliser au maximum le HTML, les styles, les composants, les scripts, les animations et les structures éditoriales du projet Astro.
6. Adapter uniquement ce qui doit l’être pour fonctionner proprement dans WordPress.
7. Conserver le thème WordPress déjà amorcé et ses éléments validés.
8. Travailler par étapes courtes, testables et réversibles.
9. Ne pas démarrer une étape suivante sans avoir terminé et vérifié la précédente.
10. Ne jamais « améliorer » spontanément le projet au prix d’un écart avec Astro.

> Objectif : migrer, pas réinventer.

---

## 3. Périmètre

### Inclus

- audit du projet Astro ;
- audit du thème WordPress existant ;
- correspondance entre les composants Astro et les fichiers WordPress ;
- migration des templates ;
- migration des styles et scripts ;
- intégration des contenus dynamiques avec WordPress et ACF Pro ;
- prise en charge de Gutenberg ;
- ajout des Résonances ;
- responsive ;
- accessibilité de base ;
- SEO technique de base ;
- tests avant livraison.

### Hors périmètre

- nouvelle direction artistique ;
- refonte complète du modèle éditorial ;
- remplacement du design system existant ;
- ajout de fonctionnalités non présentes dans Astro, sauf Résonances ;
- développement d’un thème blocs/FSE ;
- dépendance au dépôt Foundations ;
- reconstruction abstraite du site autour de nouveaux concepts WordPress.

---

## 4. Socle technique WordPress

Le site doit utiliser :

- un **thème classique WordPress** ;
- Gutenberg comme éditeur ;
- les blocs natifs et les compositions Gutenberg lorsque cela suffit ;
- ACF Pro pour les champs spécifiques ;
- ACF JSON comme source versionnée des groupes de champs, CPT et taxonomies configurés dans ACF ;
- PHP, CSS et JavaScript organisés proprement dans le thème ;
- Git pour chaque étape significative.

Éviter les blocs personnalisés lorsque les blocs natifs, les patterns ou les champs ACF simples répondent au besoin.

---

## 5. Architecture éditoriale actée

La navigation principale repose sur :

- **Photo**
- **Créations**
- **Récits**
- **À propos**

### 5.1 Photo

Le CPT doit être nommé clairement **Photo**.

Il regroupe les séries et projets photographiques du site Astro.

Il peut accueillir plusieurs modes de présentation, par exemple :

- série photographique standard ;
- voyage ;
- Projet 52 ;
- Color Your Life ;
- autres formes déjà présentes dans Astro.

Ces différences ne justifient pas nécessairement plusieurs CPT. Elles correspondent principalement à des **templates ou narrations différents**.

Le choix de présentation pourra être piloté par une taxonomie ou un champ ACF, à condition que cette solution reste simple, explicite et fidèle à Astro.

### 5.2 Créations

Le CPT **Création** regroupe :

- les dessins ;
- les coloriages ;
- éventuellement la couture plus tard.

Une taxonomie ou un champ de type permet de distinguer au minimum :

- Dessin ;
- Coloriage.

Ne pas inclure la photographie dans Créations.

### 5.3 Récits

Le CPT **Récit** contient les contenus éditoriaux textuels :

- textes courts ou longs ;
- réflexions ;
- récits illustrés ;
- image mise en avant facultative ;
- images d’ambiance facultatives.

Un récit n’est pas conçu comme une galerie photo.

### 5.4 Résonances

Les **Résonances** constituent la seule évolution majeure par rapport au projet Astro.

Elles servent à relier transversalement des contenus de natures différentes :

- une photo ;
- une création ;
- un récit ;
- éventuellement une page ou un autre contenu pertinent.

Leur rôle est éditorial et sensible, pas seulement classificatoire.

L’implémentation doit permettre :

- d’associer une ou plusieurs résonances à différents types de contenus ;
- d’afficher les contenus liés sur les pages concernées ;
- de naviguer naturellement d’un univers à l’autre ;
- de conserver une gestion simple dans l’administration.

La forme exacte doit être définie à partir du fonctionnement souhaité, sans sur-ingénierie.

---

## 6. Éléments déjà validés

Les éléments déjà migrés ou validés dans le thème WordPress ne doivent pas être réécrits sans nécessité :

- header ;
- footer ;
- premières fondations CSS du thème ;
- structure générale du thème classique ;
- intégration de Gutenberg ;
- usage d’ACF Pro et d’ACF JSON.

Avant toute modification, vérifier l’état réel du dépôt.

---

## 7. Méthode de migration

### Étape 1 — Audit sans modification

Analyser entièrement :

1. le projet Astro ;
2. le thème WordPress existant.

Produire une table de correspondance :

| Astro | WordPress | Action |
|---|---|---|
| composant ou page Astro | template, partial ou fonction WordPress | reprendre, adapter, créer ou supprimer |

L’audit doit identifier :

- les pages ;
- les layouts ;
- les composants ;
- les collections de contenu ;
- les données ;
- les styles globaux ;
- les scripts ;
- les animations ;
- les assets ;
- les variantes responsive ;
- les éléments déjà migrés.

Ne modifier aucun fichier pendant cet audit.

### Étape 2 — Plan de migration

Créer un plan ordonné, découpé en lots courts et testables.

Ordre recommandé :

1. socle et dépendances ;
2. styles globaux ;
3. header et footer : contrôle uniquement ;
4. page d’accueil ;
5. archive Photo ;
6. single Photo standard ;
7. variantes Photo : Voyage, Projet 52, Color Your Life ;
8. archive et single Créations ;
9. archive et single Récits ;
10. Résonances ;
11. pages statiques, dont À propos ;
12. navigation transversale ;
13. responsive ;
14. accessibilité ;
15. SEO technique ;
16. recette globale.

### Étape 3 — Exécution lot par lot

Pour chaque lot :

1. relire les fichiers Astro concernés ;
2. relire les fichiers WordPress concernés ;
3. annoncer brièvement les modifications prévues ;
4. modifier uniquement les fichiers nécessaires ;
5. lancer les vérifications disponibles ;
6. comparer le résultat à Astro ;
7. corriger les écarts ;
8. résumer les fichiers modifiés ;
9. proposer un commit clair ;
10. s’arrêter avant le lot suivant.

---

## 8. Fidélité visuelle et fonctionnelle

Chaque écran WordPress doit être comparé à son équivalent Astro.

Vérifier notamment :

- structure de page ;
- hiérarchie typographique ;
- espacements ;
- grilles ;
- dimensions d’images ;
- recadrages ;
- transitions ;
- animations ;
- navigation ;
- survols ;
- comportement mobile ;
- comportement tablette ;
- comportement desktop.

Une différence visible doit être corrigée ou explicitement justifiée par une contrainte WordPress.

---

## 9. Contenus et données dynamiques

Pour chaque contenu actuellement codé ou stocké dans Astro, déterminer s’il doit devenir :

- un champ natif WordPress ;
- le contenu Gutenberg ;
- une image mise en avant ;
- une taxonomie ;
- un champ ACF ;
- une relation ACF ;
- une option globale ;
- un menu WordPress.

Ne pas créer de champ ACF lorsqu’un champ natif ou Gutenberg suffit.

Les champs doivent être nommés en français côté administration, avec des noms techniques cohérents et stables.

---

## 10. Templates Photo

Le CPT Photo doit disposer d’un template par défaut.

Les présentations particulières doivent être gérées sans multiplier artificiellement les CPT.

Exemples :

- Photo standard ;
- Voyage ;
- Projet 52 ;
- Color Your Life.

Le mécanisme peut être une taxonomie de narration ou un champ ACF de choix de template.

Le choix final doit privilégier :

1. la simplicité d’administration ;
2. la lisibilité du code ;
3. la stabilité des URLs ;
4. la fidélité au projet Astro.

Le système doit prévoir un fallback vers le template Photo standard.

---

## 11. Gutenberg et expérience d’édition

L’administration doit être agréable et compréhensible.

Objectifs :

- éviter les écrans surchargés ;
- limiter les champs obligatoires ;
- utiliser Gutenberg pour les contenus éditoriaux ;
- réserver ACF aux données réellement structurées ;
- proposer des libellés clairs ;
- masquer les options WordPress inutiles selon le CPT ;
- éviter que l’éditrice ait besoin de comprendre le code ou la structure technique.

Le résultat attendu est un outil de publication, pas un back-office technique.

---

## 12. SEO technique

Prévoir :

- URLs lisibles ;
- titres de pages corrects ;
- un seul H1 par page ;
- balisage sémantique ;
- images avec texte alternatif ;
- métadonnées compatibles avec une extension SEO ;
- archives maîtrisées ;
- fil d’Ariane si prévu dans Astro ou utile à la navigation ;
- données structurées uniquement si pertinentes ;
- bonnes performances ;
- absence de duplication évidente.

Structure publique envisagée :

- `/photo/`
- `/photo/nom-du-projet/`
- `/creations/`
- `/creations/nom-de-la-creation/`
- `/recits/`
- `/recits/nom-du-recit/`

La stabilité et la clarté priment sur l’optimisation artificielle des mots-clés dans les URLs.

---

## 13. Performance

La migration ne doit pas dégrader sensiblement les performances d’Astro.

Prévoir :

- chargement conditionnel des scripts ;
- limitation des dépendances ;
- images responsives WordPress ;
- formats modernes lorsque disponibles ;
- lazy loading approprié ;
- CSS et JavaScript raisonnablement découpés ;
- absence de bibliothèques ajoutées sans nécessité ;
- animations respectueuses de `prefers-reduced-motion`.

---

## 14. Accessibilité

Minimum attendu :

- navigation clavier ;
- focus visible ;
- contrastes corrects ;
- structure de titres logique ;
- textes alternatifs ;
- boutons et liens correctement identifiés ;
- menus utilisables sur mobile ;
- respect de `prefers-reduced-motion` ;
- formulaires correctement étiquetés s’il y en a.

---

## 15. Git et sécurité de travail

- Travailler sur une branche dédiée.
- Faire un commit par lot cohérent.
- Ne pas mélanger plusieurs migrations dans un même commit.
- Ne pas modifier le dépôt Astro.
- Le dépôt Astro reste une référence en lecture.
- Ne jamais supprimer une implémentation existante avant d’avoir validé son remplacement.
- Ne pas toucher au dépôt Foundations.
- Ne pas copier aveuglément du code ancien sans vérifier son utilité.
- Ne pas committer de secrets, fichiers locaux ou configurations sensibles.

Exemples de commits :

- `feat(photo): migrate archive from Astro`
- `feat(photo): add project-52 template`
- `feat(creations): migrate creation singles`
- `feat(resonances): add transversal content relations`
- `fix(responsive): align mobile layouts with Astro`

---

## 16. Critères d’acceptation

La migration est considérée comme terminée lorsque :

- les pages principales d’Astro existent dans WordPress ;
- leur rendu est fidèle ;
- les contenus sont administrables ;
- Photo, Créations et Récits fonctionnent ;
- les variantes Photo nécessaires fonctionnent ;
- les Résonances relient effectivement les contenus ;
- le header et le footer restent conformes ;
- le responsive est validé ;
- aucun avertissement PHP ou erreur JavaScript significative n’est présent ;
- les URLs et permaliens fonctionnent ;
- l’édition ne nécessite pas de modifier le code ;
- la migration a été testée avec plusieurs contenus réels ;
- la documentation minimale est présente.

---

## 17. Livrables

1. Thème WordPress fonctionnel.
2. ACF JSON versionné.
3. Templates migrés.
4. Styles et scripts migrés.
5. Résonances fonctionnelles.
6. Documentation courte : installation, structure du thème, gestion des contenus, choix des templates Photo et gestion des Résonances.
7. Liste des éventuels écarts avec Astro.
8. Historique Git propre.

---

## 18. Consigne initiale à donner à l’agent de code

```text
Le projet Astro est la référence fonctionnelle, éditoriale et visuelle.

Le but n’est pas de concevoir un nouveau site, mais de migrer fidèlement
le projet Astro vers le thème WordPress classique existant.

La seule évolution éditoriale majeure à ajouter est la notion de Résonances.

Commence par analyser les deux dépôts sans modifier aucun fichier.
Identifie précisément ce qui existe déjà dans le thème WordPress.
Établis une correspondance entre les pages et composants Astro et les
templates, partials, styles et scripts WordPress.

Respecte le cahier des charges de migration.
Ne travaille pas avec le dépôt Foundations.
Ne propose pas une nouvelle architecture éditoriale.
Ne commence aucun développement avant d’avoir présenté un plan de migration
court, ordonné et vérifiable.
```

---

## 19. Prompt type pour chaque lot

```text
Exécute uniquement le lot suivant du plan validé : [NOM DU LOT].

Avant toute modification :
- relis les fichiers Astro de référence ;
- relis les fichiers WordPress concernés ;
- indique brièvement ce que tu vas modifier.

Contraintes :
- Astro reste la référence ;
- conserve le socle WordPress existant ;
- ne modifie que les fichiers nécessaires ;
- ne change pas l’architecture éditoriale ;
- ne touche pas au dépôt Foundations ;
- ne commence pas le lot suivant.

Après modification :
- lance les vérifications disponibles ;
- compare le résultat à Astro ;
- résume les fichiers modifiés ;
- signale les écarts ou décisions nécessaires ;
- propose un message de commit.

Décisions déjà prises :
- CPT : photo
- galerie : champ ACF soc_photo_gallery
- texte : éditeur Gutenberg
- taxonomie : resonance
- aucune règle de rewrite personnalisée
- aucun dispatcher
- un template single-photo.php
- styles dans assets/styles/templates/single-photo.css

Ne remets pas ces décisions en question.
Commence par lister les fichiers à modifier, puis attends ma validation.
```
---
## 19 bis
```text
Cherche d'abord la solution WordPress native la plus simple.

Ne crée aucune règle de rewrite, abstraction ou mécanisme personnalisé sans me demander validation avant.

Si un simple menu, une archive native, une taxonomie ou un template WordPress suffit, utilise cette solution.
```
---

## 20. Boussole du projet

Ce site n’est pas développé pour multiplier les choix techniques.

Il doit permettre de publier rapidement :

- des photographies ;
- des projets visuels ;
- des dessins ;
- des coloriages ;
- des récits ;
- des liens sensibles entre ces contenus.

Le code doit se faire oublier au profit du contenu.

## 21. Nouvelles règles
Avant d'écrire un nouveau code,
chercher s'il existe déjà dans Astro.

Si oui :

→ le réutiliser.

Ne jamais réinventer un composant déjà présent.
