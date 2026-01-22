/**
 * Amadeco PopularSearchTerms Module
 *
 * @category    Amadeco
 * @package     Amadeco_PopularSearchTerms
 * @author      Ilan Parmentier
 * @copyright   Copyright (c) Amadeco (https://www.amadeco.fr)
 * @license     OSL-3.0
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
     * This component manages popular search terms provided by the server 
     * and user search history persisted in LocalStorage.
     *
     * @api
     */
    return Component.extend({
        /**
         * Component configuration defaults.
         * These values are automatically overridden by the 'config' array 
         * injected by Amadeco\PopularSearchTerms\Block\SearchTerms::getJsLayout.
         */
        defaults: {
            template: 'Amadeco_PopularSearchTerms/search-terms-template',
            initialTerms: [],
            numberOfTerms: 5,
            sortOrder: 'popularity',
            searchResultUrl: '',
            maxRecentSearches: 5,
            searchForm: {
                formId: 'search_mini_form',
                inputName: 'q',
                storageKey: 'recent-searches'
            }
        },

        /**
         * Initialize the UI Component.
         *
         * @returns {Object} Chainable reference
         */
        initialize: function () {
            this._super();

            // 1. Initialize Observables
            this.terms = ko.observableArray(this.initialTerms);
            this.recentSearches = ko.observableArray([]);
            this.loading = ko.observable(false);
            
            this.hasRecentSearches = ko.pureComputed(function () {
                return this.recentSearches().length > 0;
            }, this);

            // 2. Initialize Persistence Model
            // Values like this.searchForm and this.maxRecentSearches are now 
            // natively available thanks to the Block injection.
            storageModel.initialize(this.searchForm);
            storageModel.initSearchObserver(parseInt(this.maxRecentSearches, 10));

            // 3. Load initial data
            this.loadRecentSearches();

            // 4. Global Event Listener for history updates
            $(document).on('recentSearchesUpdated', function (event, searches) {
                this.recentSearches(searches);
            }.bind(this));

            return this;
        },

        /**
         * Loads history from LocalStorage via the storage model.
         *
         * @public
         * @returns {void}
         */
        loadRecentSearches: function () {
            this.recentSearches(storageModel.getRecentSearches());
        },

        /**
         * Clears search history.
         *
         * @public
         * @returns {void}
         */
        clearRecentSearches: function () {
            storageModel.clearRecentSearches();
        },

        /**
         * Generates the search URL for a given term.
         *
         * @public
         * @param {String} term
         * @returns {String}
         */
        getSearchUrl: function (term) {
            var separator = this.searchResultUrl.indexOf('?') !== -1 ? '&' : '?';
            return this.searchResultUrl + separator + 'q=' + encodeURIComponent(term);
        },

        /**
         * Formats a raw date string or timestamp into a localized string.
         *
         * @public
         * @param {String|Number} dateValue
         * @returns {String}
         */
        formatDate: function (dateValue) {
            if (!dateValue) {
                return '';
            }
            var date = new Date(dateValue);
            return !isNaN(date.getTime()) ? date.toLocaleDateString() : '';
        }
    });
});
