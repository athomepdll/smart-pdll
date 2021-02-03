import Vue from 'vue'
import Vuex from 'vuex'
import carryingStructureFilter from './modules/filters/carryingStructureFilter';
import cityFilter from './modules/filters/cityFilter';
import cityHistory from './modules/modal/cityHystory';
import data from './modules/dataContainer/data';
import departmentFilter from "./modules/filters/departmentFilter";
import details from './modules/modal/details';
import districtFilter from './modules/filters/districtFilter';
import epciFilter from './modules/filters/epciFilter';
import exportModule from './modules/dataContainer/export';
import form from './modules/forms/form';
import financialDataTable from './modules/dataContainer/financialDataTable';
import financialImportModelsFilter from './modules/filters/financialImportModelsFilter';
import indicator from './modules/modal/indicator';
import indicatorImportModelsFilter from './modules/filters/indicatorImportModelsFilter';
import localizedInfos from "../components/localizedInfos";
import map from './modules/map/map';
import notification from "./modules/notification/notification";
import territoryPortrait from './modules/filters/territoryPortrait';
import userAccountForm from "./modules/forms/userAccountForm";

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
    modules: {
        carryingStructureFilter,
        cityFilter,
        cityHistory,
        data,
        departmentFilter,
        details,
        districtFilter,
        epciFilter,
        exportModule,
        form,
        financialDataTable,
        financialImportModelsFilter,
        indicator,
        indicatorImportModelsFilter,
        localizedInfos,
        map,
        notification,
        territoryPortrait,
        userAccountForm
    },
    strict: debug,
})