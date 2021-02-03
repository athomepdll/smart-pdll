require('../scss/main.scss');
require('leaflet-easyprint');

import Vue from 'vue';
import insee from './importInsee';
import FiltersContainer from './components/FiltersContainer';
import DataContainer from './components/DataContainer';
import ModalCity from './components/modals/cityHistory';
import ModalDetails from './components/modals/details';
import ModalIndicator from './components/modals/indicator';
import ColumnComponent from './components/columnsComponent';
import VueFlashMessage from 'vue-flash-message';
import AxiosDefault from 'axios/lib/defaults';
import store from './store/index';
import bsCustomFileInput from 'bs-custom-file-input'
import SortedTablePlugin from 'vue-sorted-table';
import Tooltip from 'vue-directive-tooltip';
import DataTableFinancial from './components/dataTable/DataTableFinancial';
import DataTableIndicator from './components/dataTable/DataTableIndicator';
import FinancialTerritoryMap from './components/territoryPortrait/FinancialTerritoryMap';
import IndicatorTerritoryMap from './components/territoryPortrait/IndicatorTerritoryMap';
import InitTerritoryPortrait from './components/territoryPortrait/InitTerritoryPortrait';
import Department from './components/user/Department';
import District from './components/user/District';
import Markdown from "./components/admin/Markdown";
import Help from "./components/Help";

import $ from 'jquery'

window.jQuery = $;
window.$ = $;

// require( 'bootstrap-select/js/bootstrap-select');
//
// $.fn.selectpicker.Constructor.BootstrapVersion = '4.3.6';

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);

AxiosDefault.baseURL = process.env.API_HOST;

Vue.use(Tooltip);
Vue.use(VueFlashMessage);
Vue.use(SortedTablePlugin);

new Vue({
    el: '#app',
    store,
    components: {
        insee,
        VueFlashMessage,
        ColumnComponent,
        FiltersContainer,
        DataContainer,
        Department,
        District,
        ModalCity,
        ModalDetails,
        ModalIndicator,
        DataTableFinancial,
        DataTableIndicator,
        FinancialTerritoryMap,
        IndicatorTerritoryMap,
        InitTerritoryPortrait,
        Markdown,
        Help
    },
});

$(document).ready(() => {
    bsCustomFileInput.init();
});