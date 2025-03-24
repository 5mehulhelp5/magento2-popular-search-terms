# Magento 2 Search Terms Module

[![Latest Stable Version](https://img.shields.io/github/v/release/Amadeco/magento2-popular-search-terms)](https://github.com/Amadeco/magento2-popular-search-terms/releases)
[![License](https://img.shields.io/github/license/Amadeco/magento2-popular-search-terms)](https://github.com/Amadeco/magento2-popular-search-terms/blob/main/LICENSE)
[![Magento](https://img.shields.io/badge/Magento-2.4.x-brightgreen.svg)](https://magento.com)
[![PHP](https://img.shields.io/badge/PHP-8.1|8.2|8.3-blue.svg)](https://www.php.net)

A Magento 2 module that enhances the search experience by displaying popular search terms and personal search history. Optimizes product discovery and conversion rates by suggesting relevant terms based on both collective and individual user behaviors, all configurable from the admin and with no performance impact thanks to intelligent caching.

## Features

This professional module for Magento 2 enhances the search experience by combining two powerful features:

- **Popular Search Terms**: Display the most popular search terms on your store, sorted by frequency or search date
- **Recent Searches**: Save and display each visitor's personal search history
- **Smart Caching**: Efficiently caches data using Magento's native collection caching mechanism
- **Easy Configuration**: Fully configurable through admin panel
- **Customizable**: Extensive layout customization options via XML
- **Internationalization**: Complete translations available (en_US, fr_FR)

## Screenshots

[![Capture-d-e-cran-2025-03-24-a-18-25-30.png](https://i.postimg.cc/26SWtsrf/Capture-d-e-cran-2025-03-24-a-18-25-30.png)](https://postimg.cc/NyZL9PKN)

## Requirements

- Magento 2.4.x
- PHP 8.1, 8.2, or 8.3

## Installation

### Via Composer (Recommended)

```bash
composer require amadeco/module-popular-search-terms
bin/magento module:enable Amadeco_PopularSearchTerms
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

### Manual Installation

1. Download the code and extract to `app/code/Amadeco/PopularSearchTerms/`
2. Run the following commands:

```bash
bin/magento module:enable Amadeco_PopularSearchTerms
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

## Configuration

1. Go to **Stores > Configuration > Catalog > Popular Search Terms**
2. Configure the options:
   - **Enable Module**: Activate or deactivate the module
   - **Number of Terms**: Number of search terms to display
   - **Sort Order**: Sort by popularity or recency
   - **Time Period (days)**: Number of days to look back for search terms
   - **Cache Lifetime (seconds)**: Time to cache the search terms data

## Customization

### XML Layout

The module can be extensively customized via layout XML:

```xml
<referenceContainer name="sidebar.additional">
    <block class="Amadeco\PopularSearchTerms\Block\SearchTerms"
           name="amadeco.search.terms"
           ifconfig="catalog/popular_search_terms/enabled">
        <arguments>
            <!-- Recent searches configuration -->
            <argument name="max_recent_searches" xsi:type="number">5</argument>
            
            <!-- Form selectors configuration -->
            <argument name="search_form_id" xsi:type="string">search_mini_form</argument>
            <argument name="search_input_name" xsi:type="string">q</argument>
            <argument name="storage_key" xsi:type="string">recent-searches</argument>
            
            <!-- JS Layout configuration -->
            <argument name="jsLayout" xsi:type="array">
                <item name="components" xsi:type="array">
                    <item name="search-terms" xsi:type="array">
                        <item name="component" xsi:type="string">Amadeco_PopularSearchTerms/js/search-terms</item>
                        <item name="config" xsi:type="array">
                            <item name="template" xsi:type="string">Amadeco_PopularSearchTerms/search-terms-template</item>
                            <item name="hasRecentSearches" xsi:type="boolean">true</item>
                        </item>
                    </item>
                </item>
            </argument>
        </arguments>
    </block>
</referenceContainer>
```

### Configuration Parameters

- **max_recent_searches**: Maximum number of recent searches to display (default: 5)
- **search_form_id**: ID of the search form to monitor (default: "search_mini_form")
- **search_input_name**: Name of the input field containing the search term (default: "q")
- **storage_key**: Key used to store recent searches in client storage (default: "recent-searches")

### Adding to a Custom Location

You can add the module to any custom location using XML layout or with PHP:

```php
<?php echo $block->getLayout()
    ->createBlock('Amadeco\PopularSearchTerms\Block\SearchTerms')
    ->setMaxRecentSearches(3)
    ->toHtml(); ?>
```

### Styling

The module includes LESS styles that can be overridden in your theme. The main styles are defined in:

```
view/frontend/web/css/source/_module.less
```

## Translations

The module includes complete translations for:

- English (en_US)
- French (fr_FR)

To add additional translations, create a CSV file in:

```
app/code/Amadeco/PopularSearchTerms/i18n/your_locale.csv
```

## How it Works

1. **Popular Terms**: Uses Magento's built-in search query collection to retrieve and display the most popular or recent search terms.
2. **Recent Searches**: Utilizes Magento's customer-data storage system to save search queries performed by the current user.
3. **Performance**: Implements intelligent caching using Magento's native mechanisms to ensure minimal performance impact.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

If you encounter any issues or have questions, please [open an issue](https://github.com/Amadeco/magento2-popular-search-terms/issues) on GitHub.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
