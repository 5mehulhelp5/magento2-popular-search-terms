/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Ilan Parmentier
 * @copyright  Copyright (c) Amadeco (https://www.amadeco.fr)
 * @license    OSL-3.0
 */
define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/translate',
    'Amadeco_PopularSearchTerms/js/model/storage'
], function ($, ko, Component, $t, storageModel) {
    'use strict';

    /**
     * Search Terms UI Component
     *
     * This component manages the display of popular search terms (injected via SSR)
     * and the user's recent search history (stored in LocalStorage).
     *
     * @api
     */
    return Component.extend({
        /**
         * Component configuration defaults.
         * These values map 1-to-1 with the array returned by the PHP ViewModel:
         * \Amadeco\PopularSearchTerms\ViewModel\SearchTerms::getSearchTermsConfig
         */
        defaults: {
            template: 'Amadeco_PopularSearchTerms/search-terms-template',
            
            /**
             * @type {Array} List of popular terms injected from server (SSR)
             */
            initialTerms: [],

            /**
             * @type {Number} Number of terms to display
             */
            numberOfTerms: 5,

            /**
             * @type {String} Sorting method ('popularity' or 'recency')
             */
            sortOrder: 'popularity',

            /**
             * @type {String} Base URL for the search result page
             */
            searchResultUrl: '',

            /**
             * @type {Number} Maximum number of recent searches to retain in history
             */
            maxRecentSearches: 5,

            /**
             * @type {Object} Configuration for the target search form elements
             */
            searchForm: {
                /** @type {String} HTML ID of the search form */
                formId: 'search_mini_form',
                /** @type {String} Name attribute of the search input */
                inputName: 'q',
                /** @type {String} Key used for LocalStorage persistence */
                storageKey: 'recent-searches'
            }
        },
        
        /**
         * Initialize the component.
         *
         * Sets up observables using the injected configuration and initializes
         * the storage model for tracking recent searches.
         *
         * @returns {Object} Chainable reference to this component
         */
        initialize: function () {
            this._super();

            // Initialize observable data for popular terms
            this.terms = ko.observableArray([]);
            this.loading = ko.observable(true);
            this.error = ko.observable(false);
            this.errorMessage = ko.observable('');

            // Initialize observable data for recent searches
            this.recentSearches = ko.observableArray([]);
            this.hasRecentSearches = ko.computed(function() {
                return this.recentSearches().length > 0;
            }, this);

            // Get configuration parameters
            this.maxRecentSearches = this.getMaxRecentSearches();
            this.storageConfig = this.getStorageConfig();

            // Initialize the storage model with configuration
            storageModel.initialize(this.storageConfig);
            storageModel.initSearchObserver(this.maxRecentSearches);

            // Load data (Performance Fix: Load from injected config, no AJAX)
            this.initPopularTerms();
            this.loadRecentSearches();

            // Listen for updated recent searches
            var self = this;
            $(document).on('recentSearchesUpdated', function(event, searches) {
                self.recentSearches(searches);
            });
        },

        /**
         * Initialize popular terms from window config
         */
        initPopularTerms: function () {
            // Check if terms are provided in the config (Server-Side Rendered)
            if (window.searchTermsConfig && window.searchTermsConfig.initialTerms) {
                this.terms(window.searchTermsConfig.initialTerms);
                this.loading(false);
            } else {
                // Fallback if no terms are found or module disabled
                this.loading(false);
                // Optional: We could set an empty state or error here if strict
            }
        },

        /**
         * Get maximum number of recent searches from config
         *
         * @returns {number}
         */
        getMaxRecentSearches: function() {
            return window.searchTermsConfig && window.searchTermsConfig.maxRecentSearches
                ? parseInt(window.searchTermsConfig.maxRecentSearches, 10)
                : 5;
        },

        /**
         * Get storage configuration
         *
         * @returns {Object}
         */
        getStorageConfig: function() {
            var config = {};

            if (window.searchTermsConfig && window.searchTermsConfig.searchForm) {
                var searchForm = window.searchTermsConfig.searchForm;

                if (searchForm.formId) {
                    config.formId = searchForm.formId;
                }

                if (searchForm.inputName) {
                    config.inputName = searchForm.inputName;
                }

                if (searchForm.storageKey) {
                    config.storageKey = searchForm.storageKey;
                }
            }

            return config;
        },

        /**
         * Load recent searches from the Storage Model into the local observable.
         *
         * @public
         * @return {void}
         */
        loadRecentSearches: function() {
            this.recentSearches(storageModel.getRecentSearches());
        },

        /**
         * Clear all recent searches from LocalStorage and update the UI.
         *
         * @public
         * @return {void}
         */
        clearRecentSearches: function() {
            storageModel.clearRecentSearches();
        },

        /**
         * Generate the full search result URL for a specific term.
         * Uses the 'searchResultUrl' injected via configuration.
         *
         * @public
         * @param {String} term - The search query text
         * @return {String} The complete URL (e.g., "/catalogsearch/result/?q=term")
         */
        getSearchUrl: function (term) {
            return window.searchTermsConfig.searchResultUrl + '?q=' + encodeURIComponent(term);
        },

        /**
         * Format a Unix timestamp into a localized date string.
         *
         * @public
         * @param {Number} timestamp - Unix timestamp in milliseconds
         * @return {String} Localized date string or empty string if invalid
         */
        formatDate: function(timestamp) {
            if (!timestamp) {
                return '';
            }

            var date = new Date(timestamp);
            return date.toLocaleDateString();
        }
    });
});
