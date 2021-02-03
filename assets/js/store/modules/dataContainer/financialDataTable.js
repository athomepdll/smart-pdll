
import axios from "axios";

const api = {
    get: '/api/enumerations',
};

const state = {
    financialFields: [],
};

const getters = {
    getFinancialFields: (state) => {
        return state.financialFields;
    }
};

const mutations = {
    setFinancialFields: (state, financialFields) => {
        state.financialFields = financialFields;
    }
};

const actions = {
    initFinancialFields: async ({commit}) => {
        try {
            let filters = '?discr=financial_field';
            let financialFields = await axios.get(process.env.API_HOST + api.get + filters);
            commit('setFinancialFields', financialFields.data.enumerations);
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