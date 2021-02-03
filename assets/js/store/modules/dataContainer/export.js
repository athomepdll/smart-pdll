import axios from 'axios';
import api from '../../../config/routes';

const state = {
    loading: false,
};

const getters = {
    'GET_LOADING': (state) => {
        return state.loading;
    }
};

const mutations = {
    'SET_LOADING': (state, loading) => {
        state.loading = loading;
    }
};

const actions = {
    'EXPORT': async ({commit, rootGetters}) => {
        await commit('SET_LOADING', true);
        const form = rootGetters['form/getForm'];
        try {
            await axios.post( process.env.API_HOST + api.export, form);
            await commit('SET_LOADING', false);
        } catch (error) {
            console.log(error);
        }
    }
};

export default {
    namespaced: true,
    getters,
    mutations,
    actions
}