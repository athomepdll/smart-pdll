import axios from "axios";

const api = {
    get: '/api/epcis',
};

const state = {
    epcis: [],
};

const getters = {
    getEpcis: (state) => {
        return state.epcis;
    },
};

const mutations = {
    setEpcis: (state, epcis) => {
        state.epcis = epcis;
    }
};

const actions = {
    callApi: async ({ dispatch, rootGetters }) => {
        let district = rootGetters['form/getDistrict'];
        let filters = '?isOwnTax=true';
        if (district !== null) {
            filters += '&district=' + district;
        }

        try {
            let response = await axios.get(process.env.API_HOST + api.get + filters);
            dispatch('setData', response.data.data);
        } catch (error) {
            throw error;
        }

    },
    setData: ({ dispatch, commit}, epcis) => {
        commit('setEpcis', epcis);
        commit('form/setEpci', null, { root: true });
        dispatch('cityFilter/callApi', null, { root: true });
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
};