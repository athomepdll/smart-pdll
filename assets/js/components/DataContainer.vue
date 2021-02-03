<template>
    <div id="dataContent" class="col-lg-10 col-md-9 col-sm-9">
        <div class="panel mb-2">
            <div class="d-flex justify-content-between">
                <div class="col-auto">
<!--                    <h3 v-if="yearStart === null && yearEnd === null">Visualisation</h3>-->
<!--                    <h3 v-if="yearStart !== null && yearEnd === null" class="mb-0">Visualisation de {{ yearStart }}</h3>-->
<!--                    <h3 v-if="yearEnd !== null" class="mb-0">Visualisation de {{ yearStart }} à {{ yearEnd }}</h3>-->
                    <h3>{{ title }}</h3>
                </div>

                <div id="ink" class="col-auto">
                    <a v-show="financials.length" class="button text-white" href="#financialsData">
                        <i class="fas fa-euro-sign fa-lg"></i>
                    </a>
                    <a v-show="indicators.length" class="button text-white" href="#indicatorsData">
                        <i class="fas fa-tachometer-alt fa-lg"></i>
                    </a>
                </div>
            </div>
            <div class="d-flex justify-content-beetween">
                <div class="col-auto">
                    <LocalizedInfos></LocalizedInfos>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#dataPanel">
                        Vue tableau
                        <span v-show="dataLoading === true" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </a>
                </li>
                <li id="mapNav" class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#mapPanel">
                        Vue cartographique
                        <span v-show="mapLoading === true" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </a>
                </li>
            </ul>
            <div class="panel tab-content">
                <div id="dataPanel" role="tabpanel" class="tab-pane fade">
                    <div id="financialsData" v-show="financials.length" class="panel__header">
                        <div class="col-12">
                            <h2 class="mb-0 text-blue">
                                Données financières
                                <span class="">
                                <i class="fas fa-euro-sign"></i>
                                <Export v-show="financials.length"></Export>
                            </span>
                            </h2>
                        </div>
                    </div>
                    <DataTableFinancial v-for="financial in financials" :key="financial.name" :data="financial"></DataTableFinancial>
                    <div id="indicatorsData" v-show="indicators.length" class="panel__header">
                        <div class="col-12">
                            <h2 class="mb-0 text-blue">
                                Indicateurs
                                <span class="">
                                <i class="fas fa-tachometer-alt"></i>
                                <Export v-show="indicators.length && !financials.length"></Export>
                            </span>
                            </h2>
                        </div>
                    </div>
                    <DataTableIndicator v-for="indicator in indicators" :key="indicator.name" :data="indicator"></DataTableIndicator>
                </div>
                <div id="mapPanel" role="tabpanel" class="tab-pane fade active show">
                    <div class="col-lg-12 pb-2">
                        <Export v-show="indicators.length || financials.length"></Export>
                    </div>
                    <Map></Map>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import routes from '../config/routes';
    import DataTableFinancial from './dataTable/DataTableFinancial';
    import DataTableIndicator from './dataTable/DataTableIndicator';
    import Export from './Export';
    import Map from './map';
    import {mapGetters} from 'vuex';
    import LocalizedInfos from "./localizedInfos";

    export default {
        name: "DataContainer",
        components: {LocalizedInfos, Export, DataTableFinancial, DataTableIndicator, Map},
        data: () => ({
            api: {
                get: '/api/data',
                cities: '/api/cities'
            },
        }),
        computed: {
            ...mapGetters('form', {
                yearStart: 'getYearStart',
                yearEnd: 'getYearEnd',
                department: 'getDepartment',
                district: 'getDistrict',
                epci: 'getEpci',
                city: 'getCity',
                title: 'getTitle'
            }),
            ...mapGetters('data', {
                dataLoading: 'getLoading',
                indicators: 'getIndicators',
                financials: 'getFinancials'
            }),
            ...mapGetters('map', {
                mapLoading: 'GET_LOADING',
            }),
        },
        mounted() {
            this.$root.$on('postFormData', (form) => {
                this.postFormData(form);
            });
            this.$store.dispatch('financialDataTable/initFinancialFields');
            setInterval(() => this.checkSession(), 100000);
        },
        methods: {
            async checkSession() {
                try {
                    const response = await axios.get(process.env.API_HOST + routes.session_check);
                    if (response.data !== 'ok') {
                        document.location.reload();
                    }
                } catch (e) {
                    document.location.reload();
                }
            }
        }
    }
</script>

<style scoped>

</style>