import axios from "axios";
import api from '../../../config/routes';


const state = {
    isLoading: true,
    filters: ''
};

const getters = {
    GET_IS_LOADING: (state) => {
        return state.isLoading;
    },
    GET_FILTERS: (state) => {
        return state.filters;
    }
};

const mutations = {
    SET_IS_LOADING: (state, isLoading) => {
        state.isLoading = isLoading;
    },
    SET_FILTERS: (state, filters) => {
        state.filters = filters;
    }
};

const actions = {
    LAUNCH_MAP_GENERATION: async ({rootGetters, getters, dispatch}) => {
        await dispatch('COMPUTE_FILTERS');
        window.open(process.env.API_HOST + api.territory_portrait + getters['GET_FILTERS']);
    },
    COMPUTE_FILTERS: ({rootGetters, commit}) => {
        let stateFilter = '?dataLevel=summary';

        if (rootGetters['form/getYearStart'] !== null) {
            stateFilter = stateFilter + '&yearStart=' + rootGetters['form/getYearStart'];
        }

        if (rootGetters['form/getYearEnd'] !== null) {
            stateFilter = stateFilter + '&yearEnd=' + rootGetters['form/getYearEnd'];
        }

        if (rootGetters['form/getDepartment'] !== null) {
            stateFilter = stateFilter + '&department=' + rootGetters['form/getDepartment'];
        }

        if (rootGetters['form/getDistrict'] !== null) {
            stateFilter = stateFilter + '&district=' + rootGetters['form/getDistrict'];
        }

        if (rootGetters['form/getEpci'] !== null) {
            stateFilter = stateFilter + '&epci=' + rootGetters['form/getEpci'];
        }

        if (rootGetters['form/getCity'] !== null) {
            stateFilter = stateFilter + '&city=' + rootGetters['form/getCity'];
        }

        if (rootGetters['form/getCarryingStructure'] !== null) {
            stateFilter = stateFilter + '&carryingStructure=' + rootGetters['form/getCarryingStructure'];
        }

        if (rootGetters['financialImportModelsFilter/getFilteredImportModels'].length) {
            stateFilter = rootGetters['financialImportModelsFilter/getFilteredImportModels'].reduce(
                (stateFilter, id) => stateFilter + '&financialImportModels[' + id + ']=' + id, stateFilter
            );

        }

        if (rootGetters['indicatorImportModelsFilter/getFilteredImportModels'].length) {
            stateFilter = rootGetters['indicatorImportModelsFilter/getFilteredImportModels'].reduce(
                (stateFilter, id) => stateFilter + '&indicatorImportModels[' + id + ']=' + id, stateFilter
            );
        }

        commit('SET_FILTERS', stateFilter);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
};