import axios from "axios";
import routes from "../../../config/routes";

const state = {
    departments: [],
};

const getters = {
    GET_DEPARTMENTS: (state) => {
        return state.departments;
    },
};

const mutations = {
    SET_DEPARTMENTS: (state, departments) => {
        state.departments = departments;
    },
};

const actions = {
    FETCH_DEPARTMENTS: async ({ commit }) => {
        try {
            let response = await axios.get(process.env.API_HOST + routes.departments);
            commit('SET_DEPARTMENTS', response.data.data);
        } catch (error) {
            throw error;
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}