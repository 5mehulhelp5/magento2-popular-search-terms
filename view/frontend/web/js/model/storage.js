/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Ilan Parmentier
 */
define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'underscore'
], function($, storage, _) {
    'use strict';

    /**
     * Model for managing recent searches using Magento's customer-data storage
     */
    return {
        /**
         * Default configuration
         */
        defaults: {
            storageKey: 'recent-searches',
            formId: 'search_mini_form',
            inputName: 'q'
        },

        /**
         * Current configuration
         */
        config: {},

        /**
         * Initialize configuration
         *
         * @param {Object} config
         */
        initialize: function(config) {
            this.config = _.extend({}, this.defaults, config || {});
            return this;
        },

        /**
         * Get storage key
         *
         * @returns {String}
         */
        getStorageKey: function() {
            return this.config.storageKey;
        },

        /**
         * Get form selector
         *
         * @returns {String}
         */
        getFormSelector: function() {
            return 'form[id="' + this.config.formId + '"]';
        },

        /**
         * Get input selector
         *
         * @returns {String}
         */
        getInputSelector: function() {
            return 'input[name="' + this.config.inputName + '"]';
        },

        /**
         * Get recent searches from storage
         *
         * @returns {Array}
         */
        getRecentSearches: function() {
            var data = storage.get(this.getStorageKey())();

            if (!data || !data.items) {
                return [];
            }

            return data.items;
        },

        /**
         * Add a search term to recent searches
         *
         * @param {string} term
         * @param {number} maxItems - Maximum number of recent searches to keep
         */
        addRecentSearch: function(term, maxItems) {
            if (!term) {
                return;
            }

            term = term.trim();
            if (term === '') {
                return;
            }

            var recentSearches = this.getRecentSearches();

            // Remove this term if it already exists (to avoid duplicates)
            recentSearches = recentSearches.filter(function(search) {
                return search.query_text.toLowerCase() !== term.toLowerCase();
            });

            // Add the new term at the beginning
            recentSearches.unshift({
                query_text: term,
                timestamp: new Date().getTime()
            });

            // Limit to max number of searches
            if (recentSearches.length > maxItems) {
                recentSearches = recentSearches.slice(0, maxItems);
            }

            // Save back to storage
            storage.set(this.getStorageKey(), {
                items: recentSearches
            });

            // Trigger event so components can update
            $(document).trigger('recentSearchesUpdated', [recentSearches]);
        },

        /**
         * Clear all recent searches
         */
        clearRecentSearches: function() {
            storage.set(this.getStorageKey(), {
                items: []
            });

            $(document).trigger('recentSearchesUpdated', [[]]);
        },

        /**
         * Initialize observer for search form submissions
         *
         * @param {number} maxItems - Maximum number of recent searches to keep
         */
        initSearchObserver: function(maxItems) {
            var self = this;
            var formSelector = this.getFormSelector();
            var inputSelector = this.getInputSelector();

            // Watch for search form submissions
            $(document).on('submit', formSelector, function(e) {
                var searchTerm = $(this).find(inputSelector).val();

                self.addRecentSearch(searchTerm, maxItems);
            });
        }
    };
});