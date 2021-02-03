import axios from 'axios';

const CancelToken = axios.CancelToken;
let source = CancelToken.source();
let newCall;

const api = {
    geojson: '/geojson',
    data: '/api/data/maps',
    centroidPoint: '/api/places',
};

const state = {
    localizedInfos: null,
    loading: false,
    data: {
        financial: {
            min: null,
            max: null,
            median: null,
            legend: null,
            cities: [],
        },
        indicator: {
            legend: null,
            cities: [],
        },
    },
    perimeterZoom: 8,
    perimeterCenter: {
        lat: 47.413220,
        long: -1.719482
    },
    updatedDataFinancials: false,
    updatedDataIndicators: false,
    epcis: {
        data: null,
        options: {
            "weight": 1,
            "opacity": 1.8,
            "color": '#cb0009',
            "fill": false
        },
    },
    departments: {
        data: null,
        options: {
            "weight": 1.2,
            "opacity": 1,
            "color": '#000000',
            "fill": false
        },
    },
    cities: {
        data: null,
        options: {
            "weight": 0.3,
            "opacity": 0.9,
            "color": '#c9cacb',
            "fill": false
        },
    },
    rivers: {
        data: null,
        options: {
            "weight": 1,
            "opacity": 1,
            "color": '#2725cb',
            "fill": false
        },
    },
    ocean: {
        data: null,
        options: {
            "weight": 1,
            "opacity": 1,
            "color": '#757677',
            "fill": false
        },
    },
};

const getters = {
    GET_LOCALIZED_INFOS: (state) => {
        return state.localizedInfos;
    },
    GET_LOADING: (state) => {
        return state.loading;
    },
    GET_DATA: (state) => {
        return state.data;
    },
    GET_PERIMETER_CENTER: (state) => {
        return state.perimeterCenter;
    },
    GET_PERIMETER_ZOOM: (state) => {
        return state.perimeterZoom;
    },
    GET_UPDATED_DATA_FINANCIALS: (state) => {
        return state.updatedDataFinancials;
    },
    GET_UPDATED_DATA_INDICATORS: (state) => {
        return state.updatedDataIndicators;
    },
    GET_EPCIS: (state) => {
        return state.epcis;
    },
    GET_DEPARTMENTS: (state) => {
        return state.departments;
    },
    GET_CITIES: (state) => {
        return state.cities;
    },
    GET_RIVERS: (state) => {
        return state.rivers;
    },
    GET_OCEAN: (state) => {
        return state.ocean;
    }
};

const mutations = {
    SET_LOCALIZED_INFOS: (state, localizedInfos) => {
        state.localizedInfos = localizedInfos;
    },
    SET_LOADING: (state, loadingValue) => {
        state.loading = loadingValue;
    },
    SET_DATA: (state, data) => {
        state.data.indicator = data.indicator.cities !== undefined ? data.indicator : {cities: [], legend: null};
        state.data.financial = data.financial.cities !== undefined ? data.financial : {cities: [], legend: null};
    },
    SET_PERIMETER_CENTER: (state, perimeterCenter) => {
        state.perimeterCenter.lat = perimeterCenter !== null ? parseFloat(perimeterCenter.lat) : 47.413220;
        state.perimeterCenter.long = perimeterCenter !== null ? parseFloat(perimeterCenter.long) : -1.719482;
    },
    SET_PERIMETER_ZOOM: (state, perimeterZoom) => {
        state.perimeterZoom = perimeterZoom;
    },
    SET_UPDATED_DATA_FINANCIALS: (state, updated) => {
        state.updatedDataFinancials = updated;
    },
    SET_UPDATED_DATA_INDICATORS: (state, updated) => {
        state.updatedDataIndicators = updated;
    },
    SET_EPCIS: (state, epcis) => {
        state.epcis.data = epcis;
    },
    SET_DEPARTMENTS: (state, departments) => {
        state.departments.data = departments;
    },
    SET_CITIES: (state, cities) => {
        state.cities.data = cities;
    },
    SET_RIVERS: (state, rivers) => {
        state.rivers.data = rivers;
    },
    SET_OCEAN: (state, ocean) => {
        state.ocean.data = ocean;
    }
};

const actions = {
    SET_DATA: async ({commit}, data) => {
        await commit('SET_UPDATED_DATA_FINANCIALS', data.financial.cities !== undefined);
        await commit('SET_UPDATED_DATA_INDICATORS', data.indicator.cities !== undefined);

        commit('SET_DATA', data);
        commit('SET_LOCALIZED_INFOS', data.localizedInfos);
    },
    SET_PERIMETER_CENTER: async ({commit, rootGetters}) => {
        let filters = '';
        let zoom = 8;

        if (rootGetters['form/getDepartment'] !== null) {
            filters = '?department=' + rootGetters['form/getDepartment'];
            zoom = 9.3;
        }

        if (rootGetters['form/getDistrict'] !== null) {
            filters = '?district=' + rootGetters['form/getDistrict'];
            zoom = 9.6;
        }

        if (rootGetters['form/getEpci'] !== null) {
            filters = '?epci=' + rootGetters['form/getEpci'];
            zoom = 10;
        }

        if (rootGetters['form/getCity'] !== null) {
            filters = '?city=' + rootGetters['form/getCity'];
            zoom = 11;
        }
        try {
            const response = await axios.get(process.env.API_HOST + api.centroidPoint + filters);
            await commit('SET_PERIMETER_CENTER', response.data.data);
            if (response.data.data === null) {
                zoom = 8;
            }
            commit('SET_PERIMETER_ZOOM', zoom);
        } catch (error) {
            throw error;
        }
    },
    SET_EPCIS: ({commit}, epcis) => {
        commit('SET_EPCIS', epcis);
    },
    SET_DEPARTMENTS: ({commit}, departments) => {
        commit('SET_DEPARTMENTS', departments);
    },
    SET_CITIES: ({commit}, cities) => {
        commit('SET_CITIES', cities);
    },
    SET_RIVERS: ({commit}, rivers) => {
        commit('SET_RIVERS', rivers);
    },
    SET_OCEAN: ({commit}, ocean) => {
        commit('SET_OCEAN', ocean);
    },
    GET_EPCIS_GEOJSON: async ({dispatch}) => {
        try {
            let response = await axios.get(process.env.API_HOST + api.geojson + '?type=epcis');
            dispatch('SET_EPCIS', JSON.parse(response.data.data));
        } catch (error) {
            throw error;
        }
    },
    GET_CITIES_GEOJSON: async ({dispatch}) => {
        try {
            const response = await axios.get(process.env.API_HOST + api.geojson + '?type=cities');
            dispatch('SET_CITIES', JSON.parse(response.data.data));
        } catch (error) {
            throw error;
        }
    },
    GET_DEPARTMENTS_GEOJSON: async ({dispatch}) => {
        try {
            const response = await axios.get(process.env.API_HOST + api.geojson + '?type=departments');
            dispatch('SET_DEPARTMENTS', JSON.parse(response.data.data));
        } catch (error) {
            throw error;
        }
    },
    GET_RIVERS_GEOJSON: async ({dispatch}) => {
        try {
            let response = await axios.get(process.env.API_HOST + api.geojson + '?type=rivers');
            dispatch('SET_RIVERS', JSON.parse(response.data.data));
        } catch (error) {
            throw error;
        }
    },
    FETCH_MAP_DATA: async ({commit, dispatch, rootGetters}) => {
        newCall = CancelToken.source();
        source.cancel('map cancel request.');

        await dispatch('SET_DATA', {indicator: [], financial: []});
        const form = await rootGetters['form/getForm'];

        if (form.department === null) {
            return;
        }

        await commit('SET_LOADING', true);
        axios.post(process.env.API_HOST + api.data, form, {
            cancelToken: newCall.token
        }).then(function (response) {
            dispatch('SET_DATA', response.data.data);
            dispatch('SET_PERIMETER_CENTER');
            commit('SET_LOADING', false);
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