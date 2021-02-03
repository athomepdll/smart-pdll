import axios from "axios";

const CancelToken = axios.CancelToken;
let source = CancelToken.source();
let newCall;

const api = {
    get: '/api/data',
    cities: '/api/cities'
};

const state = {
    loading: false,
    indicators: [],
    financials: [],
};

const getters = {
    getLoading: (state) => {
        return state.loading;
    },
    getIndicators: (state) => {
        return state.indicators;
    },
    getFinancials: (state) => {
        return state.financials;
    }
};

const mutations = {
    setLoading: (state, loadingValue) => {
        state.loading = loadingValue;
    },
    setIndicators: (state, indicators) => {
        state.indicators = indicators;
    },
    setFinancials: (state, financials) => {
        state.financials = financials;
    }
};

const actions = {
    callApi: async ({commit, rootGetters}) => {
        newCall = CancelToken.source();
        await source.cancel();
        const form = await rootGetters['form/getForm'];

        if (form.department === null) {
            return;
        }

        await commit('setLoading', true);
        axios.post(process.env.API_HOST + api.get, form, {
            cancelToken: newCall.token
        }).then(function (response) {
            commit('setIndicators', response.data.data.indicator);
            commit('setFinancials', response.data.data.financial);
            commit('setLoading', false);
        }).catch(function (error) {
            if (axios.isCancel(error)) {
            } else {
                throw error;
            }
        });

        source = newCall;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
