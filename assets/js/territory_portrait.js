import '../scss/main.scss';

require('leaflet-easyprint');

import Vue from 'vue';
import store from './store/index';
import SortedTablePlugin from "vue-sorted-table";
import Tooltip from 'vue-directive-tooltip';
import DataTableFinancial from './components/dataTable/DataTableFinancial';
import DataTableIndicator from './components/dataTable/DataTableIndicator';
import FinancialTerritoryMap from './components/territoryPortrait/FinancialTerritoryMap';
import IndicatorTerritoryMap from './components/territoryPortrait/IndicatorTerritoryMap';
import InitTerritoryPortrait from './components/territoryPortrait/InitTerritoryPortrait';

Vue.use(Tooltip);
Vue.use(SortedTablePlugin);

new Vue({
    el: '#app',
    store,
    components: {
        DataTableFinancial,
        DataTableIndicator,
        FinancialTerritoryMap,
        IndicatorTerritoryMap,
        InitTerritoryPortrait
    },
});