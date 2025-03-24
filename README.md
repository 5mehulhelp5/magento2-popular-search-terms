# Popular Search Terms Module for Magento 2

This professional module for Magento 2 enhances the search experience by combining two powerful features:

- Display of the most popular search terms on your store, sorted by frequency or search date
- Saving and displaying each visitor's personal search history

Designed to increase engagement and conversion rates, this module helps customers quickly discover relevant products based on collective behaviors (popular searches) and personal preferences (search history).

Key features:
- Responsive and elegant user interface based on Knockout.js
- Flexible configuration via Magento admin (number of terms, time period, sorting)
- Intelligent caching with no performance impact
- Secure storage of recent searches via Magento's native storage system
- Complete customization via XML layout
- Fully compatible with Magento 2.4.x and PHP 8.1/8.2/8.3
- Clean code adhering to Magento 2 development standards

Perfect for merchants looking to improve navigation, increase engagement, and boost conversions by facilitating product discovery.

---------------

Ce module permet d'afficher les termes de recherche populaires sur votre site Magento 2.
Le module charge les termes par AJAX et utilise Knockout.js pour l'affichage.
La mise en cache est implémentée en utilisant le cache de collections standard de Magento pour garantir de bonnes performances.

## Fonctionnalités

- Affichage des termes de recherche les plus populaires ou les plus récents
- Configuration du nombre de termes à afficher
- Choix de la période de temps à considérer (en jours)
- Tri par popularité ou par date
- Mise en cache des données (utilise le cache `collections` de Magento)
- Interface utilisateur réactive avec Knockout.js
- Chargement AJAX pour ne pas ralentir le chargement initial de la page

## Compatibilité

- Magento 2.4.x et supérieur
- PHP 8.1, 8.2, 8.3

## Installation

### Via Composer (recommandé)

```bash
composer require amadeco/module-popular-search-terms
bin/magento module:enable Amadeco_PopularSearchTerms
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

### Installation manuelle

1. Téléchargez le code source et placez-le dans le dossier `app/code/Amadeco/PopularSearchTerms/`
2. Activez le module et mettez à jour votre installation :

```bash
bin/magento module:enable Amadeco_PopularSearchTerms
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

## Configuration

1. Connectez-vous à l'administration de Magento
2. Allez dans **Stores > Configuration > Catalog > Popular Search Terms**
3. Configurez les options suivantes :
   - **Enable Module** : Active ou désactive le module
   - **Number of Terms** : Nombre de termes à afficher
   - **Sort Order** : Tri par popularité ou par date de recherche
   - **Time Period (days)** : Période de temps à considérer (en jours)

## Utilisation

Le module ajoute automatiquement un bloc dans la sidebar supplémentaire. Vous pouvez également ajouter le bloc manuellement dans vos templates en utilisant le code suivant :

```php
<?php echo $block->getLayout()->createBlock('Amadeco\PopularSearchTerms\Block\PopularTerms')->setTemplate('Amadeco_PopularSearchTerms::popular_terms.phtml')->toHtml(); ?>
```

Ou via XML layout :

```xml
<block class="Amadeco\PopularSearchTerms\Block\PopularTerms"
       name="amadeco.popular.search.terms"
       template="Amadeco_PopularSearchTerms::popular_terms.phtml" />
```

## Personnalisation

### Styles CSS

Les styles sont définis dans le fichier `view/frontend/web/css/source/_module.less`. Vous pouvez les surcharger dans votre thème ou les modifier directement.

### Template Knockout

Le template Knockout se trouve dans `view/frontend/web/template/popular-terms-template.html`. Vous pouvez le personnaliser selon vos besoins.

## Cache

Ce module utilise le mécanisme de cache `collections` standard de Magento pour stocker les données des termes de recherche. Pour vider le cache spécifique à ce module, vous pouvez utiliser cette commande :

```bash
bin/magento cache:clean collections
```

## Support

Si vous rencontrez des problèmes ou avez des questions, veuillez ouvrir un ticket sur le dépôt GitHub du module.

## Licence

Ce module est distribué sous licence MIT. Voir le fichier LICENSE pour plus de détails.
