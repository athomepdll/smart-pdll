import axios from "axios";
import routes from "../../../config/routes";

const state = {
    department: null,
    district: null,
    districts: []
};

const getters = {
    'GET_DEPARTMENT': (state) => {
        return state.department;
    },
    'GET_DISTRICT': (state) => {
        return state.district;
    },
    'GET_DISTRICTS': (state) => {
        return state.districts;
    }
};

const mutations = {
    'SET_DEPARTMENT': (state, department) => {
        state.department = department;
    },
    'SET_DISTRICT': (state, district) => {
        state.district = district;
    },
    'SET_DISTRICTS': (state, districts) => {
        state.districts = districts;
    }
};

const actions = {
    'SET_DEPARTMENT_ACTION': async ({dispatch, commit}, value) => {
        await commit('SET_DEPARTMENT', value);
        dispatch('FETCH_DISTRICTS_ACTION');
    },
    'SET_PREFERENCES': async ({dispatch, commit}) => {
        const response = await axios.get(process.env.API_HOST + routes.user_preferences);
        await commit('SET_DEPARTMENT', response.data.department);
        await dispatch('FETCH_DISTRICTS_ACTION');
        commit('SET_DISTRICT', response.data.district);
    },
    'FETCH_DISTRICTS_ACTION': async ({commit, getters}) => {
        let filters = '';

        if (state.department !== null) {
            filters = '?department=' + state.department;
        }

        try {
            const response = await axios.get(process.env.API_HOST + routes.district + filters);
            commit('SET_DISTRICTS', response.data.data);
            commit('SET_DISTRICT', null);
        } catch (error) {
            throw error;
        }
    }
};

export default {
    namespaced: true,
    getters,
    actions,
    mutations,
    state,
}