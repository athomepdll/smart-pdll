import axios from 'axios';

const api = {
    get: '/api/data',
};

const state = {
    dataLine: null,
    details: [],
    dataLevel: 'detail'
};

const getters = {
    getDataLine: (state) => {
        return state.dataLine;
    },
    getDetails: (state) => {
        return state.details;
    }
};

const mutations = {
   setDataLine: (state, dataLine) => {
       state.dataLine = dataLine;
   },
    setDetails: (state, details) => {
       state.details = details;
    }
};


const actions = {
    callApi: async ({commit, state}, dataLine) => {
        await commit('setDataLine', dataLine);
        let filters = '?dataLine=' + state.dataLine + '&dataLevel=' + state.dataLevel;
        try {
            const response = await axios.get(process.env.API_HOST + api.get + filters);
            commit('setDetails', response.data.data);
        } catch (error) {
            throw error;
        }
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}