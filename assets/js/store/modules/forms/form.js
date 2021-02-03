import axios from "axios";
import routes from "../../../config/routes";
import {disableClickPropagation} from "leaflet/src/dom/DomEvent";

const state = {
    dataLevel: 'summary',
    yearStart: null,
    yearEnd: null,
    department: null,
    district: null,
    epci: null,
    city: null,
    carryingStructure: null,
    financialImportModels: [],
    indicatorImportModels: [],
    errors: [],
    title: 'Visualisation '
};

const getters = {
    getForm: (state) => {
        return state;
    },
    getDataLevel: (state) => {
        return state.dataLevel;
    },
    getYearStart: (state) => {
        return state.yearStart;
    },
    getYearEnd: (state) => {
        return state.yearEnd;
    },
    getDepartment: (state) => {
        return state.department;
    },
    getDistrict: (state) => {
        return state.district;
    },
    getEpci: (state) => {
        return state.epci;
    },
    getCity: (state) => {
        return state.city;
    },
    getCarryingStructure: (state) => {
        return state.carryingStructure;
    },
    getFinancialImportModels: (state) => {
        return state.financialImportModels;
    },
    getIndicatorImportModels: (state) => {
        return state.indicatorImportModels;
    },
    getErrors: (state) => {
        return state.errors;
    },
    getTitle: () => {
        return state.title;
    }
};

const mutations = {
    setDataLevel: (state, dataLevel) => {
        state.dataLevel = dataLevel;
    },
    setYearStart: (state, yearStart) => {
        state.yearStart = yearStart;
    },
    setYearEnd: (state, yearEnd) => {
        state.yearEnd = yearEnd;
    },
    setDepartment: (state, department) => {
        state.department = department;
    },
    setDistrict: (state, district) => {
        state.district = district;
    },
    setEpci: (state, epci) => {
        state.epci = epci;
    },
    setCity: (state, city) => {
        state.city = city;
    },
    setCarryingStructure: (state, carryingStructure) => {
        state.carryingStructure = carryingStructure;
    },
    setFinancialImportModels: (state, financialImportModels) => {
        state.financialImportModels = financialImportModels;
    },
    setIndicatorImportModels: (state, indicatorImportModels) => {
        state.indicatorImportModels = indicatorImportModels;
    },
    addFinancialImportModel: (state, financialImportModel) => {
        state.financialImportModels.push(financialImportModel);
    },
    addIndicatorImportModel: (state, indicatorImportModel) => {
        state.indicatorImportModels.push(indicatorImportModel);
    },
    removeFinancialImportModel: (state, financialImportModel) => {
        const indexOf = state.financialImportModels.indexOf(financialImportModel);
        state.financialImportModels.splice(indexOf, 1);
    },
    removeIndicatorImportModel: (state, indicatorImportModel) => {
        const indexOf = state.indicatorImportModels.indexOf(indicatorImportModel);
        state.indicatorImportModels.splice(indexOf, 1);
    },
    addError: (state, error) => {
        if (!state.errors.includes(error)) {
            state.errors.push(error);
        }
    },
    removeError: (state, error) => {
        const indexOf = state.indicatorImportModels.indexOf(error);
        state.errors.splice(indexOf, 1);
    },
    setTitle: (state, title) => {
        state.title = title;
    }
};

const actions = {
    addError: ({commit}, error) => {
        commit('addError', error);
    },
    removeError: ({commit}, error) => {
        commit('removeError', error);
    },
    setYearStart: async ({commit, dispatch}, yearStart) => {
        await commit('setYearStart', yearStart);
        commit('removeError', 'period');
        await dispatch('financialImportModelsFilter/callApi', null, { root: true });
        await dispatch('indicatorImportModelsFilter/callApi', null, { root: true });
        dispatch('postForm');
    },
    setYearEnd: async ({commit, dispatch}, yearEnd) => {
        await commit('setYearEnd', yearEnd);
        await dispatch('financialImportModelsFilter/callApi', null, { root: true });
        await dispatch('indicatorImportModelsFilter/callApi', null, { root: true });
        dispatch('postForm');
    },
    setDepartmentAction: async ({commit, dispatch}, department) => {
        await commit('setDepartment', department);
        await dispatch('fetchFilters');
        dispatch('postForm');
    },
    setDistrictAction: async ({commit, dispatch}, district) => {
        await commit('setDistrict', district);
        await dispatch('epciFilter/callApi', null, { root: true });
        await dispatch('carryingStructureFilter/callApi', null, { root: true });
        await dispatch('financialImportModelsFilter/callApi', null, { root: true });
        await dispatch('indicatorImportModelsFilter/callApi', null, { root: true });
        dispatch('postForm');
    },
    setEpciAction: async ({commit, dispatch}, epci) => {
        await commit('setEpci', epci);
        await dispatch('cityFilter/callApi', null, { root: true });
        await dispatch('carryingStructureFilter/callApi', null, { root: true });
        await dispatch('financialImportModelsFilter/callApi', null, { root: true });
        await dispatch('indicatorImportModelsFilter/callApi', null, { root: true });
        dispatch('postForm');
    },
    setCityAction: async ({commit, dispatch}, city) => {
        await commit('setCity', city);
        await dispatch('carryingStructureFilter/callApi', null, { root: true });
        await dispatch('financialImportModelsFilter/callApi', null, { root: true });
        await dispatch('indicatorImportModelsFilter/callApi', null, { root: true });
        dispatch('postForm');
    },
    addFinancialImportModel: async ({commit, dispatch}, financialImportModel) => {
        await commit('addFinancialImportModel',financialImportModel);
        await dispatch('data/callApi', null, { root: true });
        dispatch('map/FETCH_MAP_DATA', null, { root: true });
    },
    addIndicatorImportModel: async ({commit, dispatch}, indicatorImportModel) => {
        await commit('addIndicatorImportModel',indicatorImportModel);
        await dispatch('data/callApi', null, { root: true });
        dispatch('map/FETCH_MAP_DATA', null, { root: true });
    },
    removeFinancialImportModel: async ({commit, dispatch}, financialImportModel) => {
        await commit('removeFinancialImportModel', financialImportModel);
        await dispatch('data/callApi', null, { root: true });
        dispatch('map/FETCH_MAP_DATA', null, { root: true });
    },
    removeIndicatorImportModel: async ({commit, dispatch}, indicatorImportModel) => {
        await commit('removeIndicatorImportModel', indicatorImportModel);
        await dispatch('data/callApi', null, { root: true });
        dispatch('map/FETCH_MAP_DATA', null, { root: true });
    },
    setCarryingStructure: async ({commit, dispatch}, carryingStructure) => {
        await commit('setCarryingStructure', carryingStructure);
        dispatch('postForm');
    },
    postForm: async ({dispatch}) => {
        dispatch('computeTitle');
        if (canPostForm()) {
            await dispatch('data/callApi', null, { root: true });
            dispatch('map/FETCH_MAP_DATA', null, { root: true });
        }
    },
    setPreferences: async ({dispatch, commit, rootGetters}) => {
        const notification = rootGetters['notification/GET_STATE'];
        if (notification.yearStart === null) {
            const response = await axios.get(process.env.API_HOST + routes.user_preferences);
            await dispatch('setDepartmentAction', response.data.department);
            dispatch('setDistrictAction', response.data.district);
            return;
        }

        commit('setYearStart', notification.yearStart);
        dispatch('setDepartmentAction', notification.department);

        if (notification.importModelFinancial !== null) {
              commit('addFinancialImportModel', notification.importModelFinancial);
        }

        // if (notification.importModelIndicator !== null) {
        //     commit('addIndicatorImportModel', notification.importModelIndicator);
        // }
    },
    eraseFilters: async ({commit, dispatch}) => {
        await commit('setYearStart', null);
        await commit('setYearEnd', null);
        await commit('setDepartment', null);
        await commit('setDistrict', null);
        await commit('setEpci', null);
        await commit('setCity', null);
        await commit('setCarryingStructure', null);
        await commit('setFinancialImportModels', []);
        await commit('setIndicatorImportModels', []);
        await dispatch('fetchFilters');
        await dispatch('data/callApi', null, { root: true });
        dispatch('map/FETCH_MAP_DATA', null, { root: true });
    },
    fetchFilters: async ({dispatch}) => {
        await dispatch('districtFilter/callApi', null, { root: true });
        await dispatch('epciFilter/callApi', null, { root: true });
        await dispatch('cityFilter/callApi', null, { root: true });
        await dispatch('carryingStructureFilter/callApi', null, { root: true });
        await dispatch('financialImportModelsFilter/callApi', null, { root: true });
        await dispatch('indicatorImportModelsFilter/callApi', null, { root: true });
    },
    computeTitle: async ({commit, rootGetters, state}) => {
        const periodTitle = await computePeriodTitle();
        let departments = rootGetters['departmentFilter/GET_DEPARTMENTS'];
        let districts = rootGetters['districtFilter/getDistricts'];
        let epcis = rootGetters['epciFilter/getEpcis'];
        let cities = rootGetters['cityFilter/getCities'];
        let carryingStructures = rootGetters['carryingStructureFilter/getCarryingStructures'];

        const geographicTitle = [
            await computeIdFilterTitle(departments, state.department),
            await computeIdFilterTitle(districts, state.district),
            await computeSirenFilterTitle(epcis, state.epci),
            await computeSirenFilterTitle(cities, state.city),
            await computeSirenFilterTitle(carryingStructures, state.carryingStructure),
        ].filter(value => value !== null).join(', ');

        commit('setTitle', 'Visualisation ' + periodTitle + ' ' + geographicTitle);
    }
};

const canPostForm = function () {
    return (state.yearStart !== null) && (state.financialImportModels.length || state.indicatorImportModels.length);
};

const computePeriodTitle = function () {
    const start = state.yearStart === null ? '' : state.yearStart;
    const end = state.yearEnd === null ? '' : state.yearEnd;
    let separator = state.yearStart !== null && state.yearEnd !== null ? 'à' : '';
    return `${start} ${separator} ${end}`;
};
const computeIdFilterTitle = function (objects, id) {
    if (id === null) {
        return null;
    }
    const filteredElement = objects.filter(object => object.id === id)[0];
    return filteredElement === null ? '' : filteredElement.name;
};
const computeSirenFilterTitle = function (objects, siren) {
    if (siren === null) {
        return null;
    }
    const filteredElement = objects.filter(object => object.siren === siren)[0];
    return filteredElement === null ? '' : filteredElement.name;
};
export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}