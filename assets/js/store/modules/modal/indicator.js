import axios from 'axios';
import api from '../../../config/routes';

const state = {
    city_siren: null,
    data: [],
    insee: null,
};


const getters = {
    GET_CITY_SIREN: (state) => {
        return state.city_siren;
    },
    GET_DATA: (state) => {
        return state.data;
    },
    GET_INSEE: (state) => {
        return state.insee;
    }
};

const mutations = {
    SET_CITY_SIREN: (state, city_siren) => {
        state.city_siren = city_siren;
    },
    SET_DATA: (state, data) => {
        state.data = data;
    },
    SET_INSEE: (state, insee) => {
        state.insee = insee;
    }
};

const actions = {
    SET_CITY_SIREN: async ({commit, dispatch}, city_siren) => {
        await commit('SET_CITY_SIREN', city_siren);
        dispatch('CALL_API');
    },
    CALL_API: async ({state, rootGetters, commit}) => {
        if (state.city_siren === null) {
            commit('SET_DATA', []);
            return;
        }

        const cityForm = await rootGetters['form/getCity'];
        await commit('form/setCity', state.city_siren, { root: true });
        const form = rootGetters['form/getForm'];
        const response = await axios.post(process.env.API_HOST + api.data_view, form);
        await commit('SET_DATA', response.data.data.indicator);
        await commit('SET_INSEE', response.data.insee);
        await commit('form/setCity', cityForm, { root: true});
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}