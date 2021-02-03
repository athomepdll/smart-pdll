import axios from "axios";
import routes from "../../../config/routes";

const state = {
    districts: [],
};

const getters = {
    getDistricts: (state) => {
        return state.districts;
    },
};

const mutations = {
    setDistricts: (state, districts) => {
        state.districts = districts;
    },
};

const actions = {
    callApi: async ({ dispatch, rootGetters }) => {
        let department = rootGetters['form/getDepartment'];
        let filters = '';
        if (department !== null) {
            // dispatch('setData', []);
            filters = '?department=' + department;
        }

        try {
            let response = await axios.get(process.env.API_HOST + routes.district + filters);
            dispatch('setData', response.data.data);
        } catch (error) {
            throw error;
        }
    },
    setData: async ({ dispatch, commit}, districts) => {
        await commit('setDistricts', districts);
        await commit('form/setDistrict', null, { root: true });
        dispatch('epciFilter/callApi', null, { root: true });
        dispatch('carryingStructureFilter/callApi', null, { root: true });
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}