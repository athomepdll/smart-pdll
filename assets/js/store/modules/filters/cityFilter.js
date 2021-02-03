import axios from "axios";

const api = {
    get: '/api/cities',
};

const state = {
    cities: [],
};

const getters = {
    getCities: (state) => {
        return state.cities;
    }
};

const mutations = {
    setCities: (state, cities) => {
        state.cities = cities;
    }
};

const actions = {
    callApi: async ({ dispatch, rootGetters }) => {
        const district = rootGetters['form/getDistrict'];
        const epci = rootGetters['form/getEpci'];
        let filters = '';

        if (district !== null) {
            filters += '?district=' + district;
        }

        if (epci !== null) {
            let type = "&";
            if (district === null) {
                type = "?"
            }
            filters += type + 'epci=' + epci;
        }

        try {
            let response = await axios.get(process.env.API_HOST + api.get + filters);
            dispatch('setData', response.data.data);
        } catch (error) {
            throw error;
        }
    },
    setData: ({ dispatch, commit}, cities) => {
        commit('setCities', cities);
        commit('form/setCity', null, { root: true });
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
};