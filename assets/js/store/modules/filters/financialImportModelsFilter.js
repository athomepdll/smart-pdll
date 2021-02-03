import axios from "axios";

const api =  {
    get: '/api/importmodels'
};

const state = {
    financialImportModels: [],
    filteredImportModels: [],
    financialImportModelsId: [],
    filters: '',
};


const getters = {
    getFinancialImportModels: (state) => {
        return state.financialImportModels;
    },
    getFilteredImportModels: (state) => {
        return state.filteredImportModels;
    },
    getFinancialImportModelsId: (state) => {
        return state.financialImportModelsId;
    },
    getFilters: (state) => {
        return state.filters;
    }
};

const mutations = {
    setFinancialImportModels: (state, financialImportModels) => {
        state.financialImportModels = financialImportModels;
    },
    setFilteredImportModels: (state, filteredImportModels) => {
        state.filteredImportModels = filteredImportModels;
    },
    setFinancialImportModelsId: (state, financialImportModelsId) => {
        state.financialImportModelsId = financialImportModelsId;
    },
    setFilters: (state, filters) => {
        state.filters = filters;
    }
};

const actions = {
    initDefault: async({commit}) => {
        try {
            let response = await axios.get(process.env.API_HOST + api.get + '?isFinancial=true');
            commit('setFinancialImportModels', response.data.data);
            commit('setFinancialImportModelsId', response.data.data.map(importModel => importModel.id));
        } catch (error) {
            throw error;
        }
    },
    callApi: async ({commit, dispatch, getters, rootGetters}) => {
        await dispatch('computeFilters');

        if (rootGetters['form/getYearStart'] === null) {
            dispatch('form/addError', 'period', { root: true })
            commit('setFilteredImportModels', []);
            return;
        }

        if (rootGetters['form/getDepartment'] === null) {
            commit('setFilteredImportModels', []);
            return;
        }

        try {
            let response = await axios.get(process.env.API_HOST + api.get + getters['getFilters']);
            commit('setFilteredImportModels', response.data.data.map(importModel => importModel.id));
        } catch (error) {
            throw error;
        }
    },
    computeFilters: ({commit, rootGetters}) => {
        let stateFilter = '?isFinancial=true';

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

        commit('setFilters', stateFilter);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}



