/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Senior PHP Developer
 */
define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/translate',
    'Amadeco_PopularSearchTerms/js/model/storage'
], function ($, ko, Component, $t, storageModel) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
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

            // Load data when component initializes
            this.loadTerms();
            this.loadRecentSearches();

            // Listen for updated recent searches
            var self = this;
            $(document).on('recentSearchesUpdated', function(event, searches) {
                self.recentSearches(searches);
            });
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
         * Load popular search terms via AJAX
         */
        loadTerms: function () {
            var self = this;

            self.loading(true);
            self.error(false);

            $.ajax({
                url: window.searchTermsConfig.ajaxUrl,
                type: 'GET',
                dataType: 'json',
                cache: true,
                success: function (response) {
                    self.loading(false);

                    if (response.success && response.terms) {
                        self.terms(response.terms);
                    } else {
                        self.error(true);
                        self.errorMessage(response.message || $t('Failed to load popular search terms.'));
                    }
                },
                error: function (xhr, status, error) {
                    self.loading(false);
                    self.error(true);
                    self.errorMessage($t('An error occurred while loading popular search terms.'));
                }
            });
        },

        /**
         * Load recent searches from storage
         */
        loadRecentSearches: function() {
            this.recentSearches(storageModel.getRecentSearches());
        },

        /**
         * Clear recent searches
         */
        clearRecentSearches: function() {
            storageModel.clearRecentSearches();
        },

        /**
         * Get search URL for term
         *
         * @param {String} term
         * @return {String}
         */
        getSearchUrl: function (term) {
            return window.searchTermsConfig.searchResultUrl + '?q=' + encodeURIComponent(term);
        },

        /**
         * Format date for display
         *
         * @param {Number} timestamp
         * @return {String}
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