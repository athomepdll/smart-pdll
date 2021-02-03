const url = new URL(document.URL);
const state = {
    department: url.searchParams.get('department'),
    importModelFinancial: url.searchParams.has('importModelFinancial')
        ? parseInt(url.searchParams.get('importModelFinancial')) : null,
    importModelIndicator: url.searchParams.has('importModelIndicator')
        ? parseInt(url.searchParams.get('importModelIndicator')) : null,
    yearStart: url.searchParams.get('yearStart'),
};

const getters = {
    'GET_DEPARTMENT': (state) => {
        return state.department;
    },
    'GET_IMPORT_MODEL': (state) => {
        return state.importModel;
    },
    'GET_IMPORT_MODEL_TYPE': (state) => {
        return state.importModelType;
    },
    'GET_YEAR_START': (state) => {
        return state.yearStart;
    },
    'GET_STATE': (state) => {
        return state;
    }
};

const mutations = {
    'SET_DEPARTMENT': (state, value) => {
        state.department = value;
    },
    'SET_IMPORT_MODEL': (state, value) => {
        state.importModel = value;
    },
    'SET_IMPORT_MODEL_TYPE': (state, value) => {
        state.importModelType = value;
    },
    'SET_YEAR_START': (state, value) => {
        state.yearStart = value;
    }
};


const actions = {

};

export default {
    namespaced: true,
    getters,
    actions,
    mutations,
    state
}