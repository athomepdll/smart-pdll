import axios from 'axios';

const api = {
    cities: '/api/cities',
};

const state = {
    actualCity: null,
    cityName: null,
    cityChanges: []
};

const getters = {
    getActualCity: (state) => {
        return state.actualCity;
    },
    getCityName: (state) => {
        return state.cityName;
    },
    getCityChanges: (state) => {
        return state.cityChanges
    },
};

const mutations = {
    setActualCity: (state, id) => {
        state.actualCity = id;
    },
    setCityName: (state, cityName) => {
        state.cityName = cityName;
    },
    setCityChanges: (state, cityChanges) => {
        state.cityChanges = cityChanges;
    }
};


const actions = {
    setActualCity: ({commit}, actualCity) => {
        commit('setActualCity', actualCity);
    },
    setCityName: ({commit}, cityName) => {
        commit('setCityName', cityName);
    },
    callApiCityChanges: async ({commit, getters}) => {
        let filters = '?actualCity=' + getters['getActualCity'];
        try {
            let response = await axios.get(process.env.API_HOST + api.cities + filters);
            await commit('setCityChanges', response.data.data);
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