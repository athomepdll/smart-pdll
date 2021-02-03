import axios from "axios";

const api = {
    get: '/api/carryingstructures',
};

const state = {
    carryingStructures: [],
    disabled: true,
    filters: null,
};

const getters = {
    getCarryingStructures: (state) => {
        return state.carryingStructures;
    },
    getDisabled: (state) => {
        return state.disabled;
    },
    getFilters: (state) => {
        return state.filters;
    }
};

const mutations = {
    setCarryingStructures: (state, carryingStructures) => {
        state.carryingStructures = carryingStructures;
    },
    setDisabled: (state, disabled) => {
        state.disabled = disabled;
    },
    setFilters: (state, filters) => {
        state.filters = filters;
    }
};

const actions = {
    callApi: async ({dispatch, commit, getters, rootGetters, state}) => {
        await dispatch('computeDisabled');
        await dispatch('computeFilters');

        if (state.disabled === true) {
            dispatch('setData', []);
            return;
        }

        try {
            let response = await axios.get(process.env.API_HOST + api.get + getters['getFilters']);
            dispatch('setData', response.data.data);
        } catch (error) {
            throw error;
        }
    },
    computeFilters: ({commit, rootGetters}) => {
        let filters = '';
        if (rootGetters['form/getDepartment'] !== null) {
            filters = filters +  '?department=' + rootGetters['form/getDepartment'];

            if (rootGetters['form/getDistrict'] !== null) {
                filters = filters + '&district=' + rootGetters['form/getDistrict'];
            }
            if (rootGetters['form/getEpci'] !== null) {
                filters = filters + '&epci=' + rootGetters['form/getEpci'];
            }

            if (rootGetters['form/getCity'] !== null) {
                filters = filters + '&city=' + rootGetters['form/getCity'];
            }
        }

        commit('setFilters', filters);
    },
    computeDisabled: ({commit, rootGetters}) => {
        commit('setDisabled', rootGetters['form/getDepartment'] === null);
    },
    setData: ({dispatch, commit}, carryingStructures) => {
        commit('setCarryingStructures', carryingStructures);
        commit('form/setCarryingStructure', null, {root: true});
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}