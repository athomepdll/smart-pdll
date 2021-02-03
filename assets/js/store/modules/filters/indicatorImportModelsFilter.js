import axios from "axios";

const api = {
    get: '/api/importmodels/domains',
};
const state = {
    indicatorImportModels: [],
    filteredImportModels: [],
    filters: '',
};


const getters = {
    getIndicatorImportModels: (state) => {
        return state.indicatorImportModels;
    },
    getFilteredImportModels: (state) => {
        return state.filteredImportModels;
    },
    getFilters: (state) => {
        return state.filters;
    }
};

const mutations = {
    setIndicatorImportModels: (state, indicatorImportModels) => {
        state.indicatorImportModels = indicatorImportModels;
    },
    setFilteredImportModels: (state, filteredImportModels) => {
        state.filteredImportModels = filteredImportModels;
    },
    setFilters: (state, filters) => {
        state.filters = filters;
    },
    toggleShowChildren: (state, index) => {
        state.indicatorImportModels[index].showChildren = !state.indicatorImportModels[index].showChildren;
    },
    setShowChildren: (state, data) => {
        state.indicatorImportModels[data.index].showChildren = data.value;
    }
};

const actions = {
    initDefault: async({commit}) => {
        try {
            let response = await axios.get(process.env.API_HOST + api.get + '?isFinancial=false');
            commit('setIndicatorImportModels', response.data.data);
        } catch (error) {
            throw error;
        }
    },
    callApi: async ({commit, dispatch, getters, rootGetters}) => {
        await dispatch('computeFilters');

        if (rootGetters['form/getDepartment'] === null || rootGetters['form/getYearStart'] == null) {
            commit('setFilteredImportModels', []);
            return;
        }

        try {
            let response = await axios.get(process.env.API_HOST + api.get + getters['getFilters']);

            const arrayIds = response.data.data.reduce((acc, domain) => { return acc.concat(domain.nodesIds) }, []);

            commit('setFilteredImportModels', arrayIds);
        } catch (error) {
            throw error;
        }
    },
    computeFilters: ({commit, rootGetters}) => {
        let stateFilter = '?isFinancial=false';

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



