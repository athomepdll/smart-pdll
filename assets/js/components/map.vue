<template>
    <div id="mapContainer">
        <l-map class="map" :options="options" :zoom="zoom" :minZoom="minZoom" :center="center" ref="dataMap">
            <div id="svg-dumper" hidden="hidden"></div>
            <l-tile-layer v-if="showMap" :url="url"></l-tile-layer>
            <l-geo-json :geojson="epcis.data" :optionsStyle="epcis.options">
            </l-geo-json>
            <l-geo-json :geojson="rivers.data" :optionsStyle="rivers.options">
            </l-geo-json>
            <l-geo-json :geojson="cities.data" :optionsStyle="cities.options">
            </l-geo-json>
            <l-geo-json :geojson="departments.data" :optionsStyle="departments.options">
            </l-geo-json>
            <l-marker
                    v-for="indicator in data.indicator.cities"
                    :key="indicator.city_siren"
                    :lat-lng="[indicator.lat, indicator.long]"
                    @click="showSynthesis(indicator.city_siren)"
            >
                <l-tooltip>
                    {{ indicator.name }}
                    <i><small>Cliquer pour détail</small></i>
                </l-tooltip>
                <l-icon
                        :icon-size="[40, 40]"
                >
                    <div :ref="indicator.city_siren"></div>
                </l-icon>
            </l-marker>
            <l-marker
                    v-for="(financial, index) in data.financial.cities"
                    :key="financial.city_id"
                    :lat-lng="[financial.lat, financial.long]"
            >
                <l-tooltip>
                    <strong>{{ financial.city_name }}</strong>
                    <ul>
                        <li v-for="(datum, index) in financial.data">{{ financial.import_models[index] }} :
                            {{ numberFormat(datum) }}
                        </li>
                    </ul>
                </l-tooltip>
                <l-icon
                        :icon-size="[financial.size, financial.size]"
                >
                    <div :ref="financial.city_id"></div>
                </l-icon>
            </l-marker>
            <l-control position="topright">
                <h5>{{ title }}</h5>
            </l-control>
            <l-control position="topleft">
                <input id="showMap" type="checkbox" @click="checkHandler($event)" value="showMap" :checked="showMap">
                <label for="showMap">Carte</label>
            </l-control>
            <l-control-scale position="bottomleft" :imperial="false" :metric="true"/>
            <l-control position="bottomleft">
                <div class="legend-block">
                    <div class="legend-background"></div>
                    <div class="financial-legend-title">
                        <div v-show="data.financial.cities.length">Financements :</div>
                        <div v-show="data.indicator.cities.length">Indicateurs :</div>
                        <ul class="legend-list">
                            <li v-for="(import_model_color, index) in import_models">
                                <div class="d-flex flex-row justify-content-start align-items-start">
                                    <div class="align-self-center">{{ index }}</div>
                                    <div v-if="import_model_color !== null"
                                         class="legend-import-model align-self-center ml-1"
                                         :style="{background: import_model_color}"></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-show="data.financial.cities.length">
                        <div class="financial-legend-title">
                            Montant en euros :
                        </div>
                        <div class="d-flex">
                            <div class="dot-legend"
                                 v-show="data.financial.max !== null && data.financial.cities.length"
                                 :style="{'bottom': sizeMax + 'px'}">
                                {{ numberFormat(data.financial.max) }}
                            </div>
                            <span class="dot"
                                  :style="sizeMaxStyle">
                        </span>
                        </div>
                        <div>
                            <div class="dot-legend"
                                 v-show="data.financial.median !== null && data.financial.cities.length"
                                 :style="{'bottom': sizeMedian + 'px'}">
                                {{ numberFormat(data.financial.median) }}
                            </div>
                            <span class="dot"
                                  :style="sizeMedianStyle">
                        </span>
                        </div>
                        <div>
                            <div class="dot-legend" v-show="data.financial.min !== null && data.financial.cities.length"
                                 :style="{'bottom': sizeMin+ 'px'}">
                                {{ numberFormat(data.financial.min) }}
                            </div>
                            <span class="dot"
                                  :style="sizeMinStyle">
                        </span>
                        </div>
                    </div>
                </div>
            </l-control>
            <l-control position="bottomright">
                <div>
                    © les contributeurs d’<a target="_blank" rel="noopener noreferrer"
                                               href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>
                    <a href="http://leafletjs.com" title="A JS library for interactive maps">Leaflet</a>
                </div>
            </l-control>
            <l-control position="bottomright">
                <img class="navbar__logo mr-0 north-compass" src="../../images/north-compass.png"
                     alt="Nord Géographique"/>
            </l-control>
        </l-map>
    </div>
</template>

<script>
    import {
        LGeoJson,
        LMap,
        LControl,
        LCircleMarker,
        LMarker,
        LTileLayer,
        LTooltip,
        LControlScale,
        LIcon
    } from "vue2-leaflet";
    import * as L from "leaflet";
    import {mapGetters} from "vuex";
    import * as d3 from 'd3';

    export default {
        name: 'Map',
        components: {
            'l-circle-marker': LCircleMarker,
            'l-control': LControl,
            'l-geo-json': LGeoJson,
            'l-map': LMap,
            'l-marker': LMarker,
            'l-tile-layer': LTileLayer,
            'l-tooltip': LTooltip,
            'l-control-scale': LControlScale,
            'l-icon': LIcon,
        },
        data() {
            return {
                options: {
                    attributionControl: false,
                },
                showMap: false,
                url: "https://{s}.tile.osm.org/{z}/{x}/{y}.png",
                zoom: 8,
                minZoom: 6,
                center: L.latLng(47.413220, -1.719482),
                colors: ["#0053b3", "#ff9947", "#03bd5b", "#26353f", "#d63626", "#efaca6", "#cc5c00", "#b4e1fa"],
            }
        },
        computed: {
            ...mapGetters('form', {
                title: 'getTitle',
            }),
            ...mapGetters('map', {
                data: 'GET_DATA',
                epcis: 'GET_EPCIS',
                departments: 'GET_DEPARTMENTS',
                cities: 'GET_CITIES',
                rivers: 'GET_RIVERS',
                updatedDataFinancials: 'GET_UPDATED_DATA_FINANCIALS',
                updatedDataIndicators: 'GET_UPDATED_DATA_INDICATORS',
                perimeterCenter: 'GET_PERIMETER_CENTER',
                perimeterZoom: 'GET_PERIMETER_ZOOM',
            }),
            import_models: function () {
                if (this.data.financial.legend !== null) {
                    return this.data.financial.legend.import_models;
                }
                if (this.data.indicator.legend !== null) {
                    return this.data.indicator.legend.import_models;
                }
            },
            sizeMax: function () {
                return this.data.financial.legend !== null ? this.data.financial.legend.sizeMax : 0;
            },
            sizeMedian: function () {
                return this.data.financial.legend !== null ? this.data.financial.legend.sizeMedian : 0;
            },
            sizeMin: function () {
                return this.data.financial.legend !== null ? this.data.financial.legend.sizeMin : 0;
            },
            sizeMaxStyle: function () {
                const sizeMax = this.data.financial.legend !== null ? this.data.financial.legend.sizeMax : 0;

                return {
                    height: sizeMax + 'px', width: sizeMax + 'px',
                    display: sizeMax === 0 ? 'none' : 'inline-block',
                    left: this.getLeftPixels(sizeMax) + 'px'
                };
            },
            sizeMedianStyle: function () {
                const sizeMedian = this.data.financial.legend !== null ? this.data.financial.legend.sizeMedian : 0;

                return {
                    height: sizeMedian + 'px', width: sizeMedian + 'px',
                    display: sizeMedian === 0 ? 'none' : 'inline-block',
                    left: this.getLeftPixels(sizeMedian) + 'px'
                };
            },
            sizeMinStyle: function () {
                const sizeMin = this.data.financial.legend !== null ? this.data.financial.legend.sizeMin : 0;

                return {
                    height: sizeMin + 'px', width: sizeMin + 'px',
                    display: sizeMin === 0 ? 'none' : 'inline-block',
                    left: this.getLeftPixels(sizeMin) + 'px'
                };
            },
        },
        async mounted() {
            this.initPrint();
            await this.getGeojson();
            setTimeout(this.resizeMap, 2000);
        },
        updated: function () {
            if (this.data.indicator.cities.length && this.updatedDataIndicators === true) {
                this.data.indicator.cities.forEach((element) => {
                    this.generateTriangle(element);
                    this.$store.commit('map/SET_UPDATED_DATA_INDICATORS', false);
                })
            }

            if (this.data.financial.cities.length && this.updatedDataFinancials === true) {
                this.data.financial.cities.forEach((element) => {
                    this.generatePie(element);
                    this.$store.commit('map/SET_UPDATED_DATA_FINANCIALS', false);
                })
            }
        },
        methods: {
            initPrint () {
                L.easyPrint({
                    title: 'Exporter en png',
                    position: 'topleft',
                    exportOnly: true,
                    sizeModes: ['A4Portrait', 'A4Landscape'],
                    hideControlContainer: false
                }).addTo(this.$refs.dataMap.mapObject);
            },
            async getGeojson() {
                await this.$store.dispatch('map/GET_EPCIS_GEOJSON');
                // this.$store.dispatch('map/GET_RIVERS_GEOJSON');
                // this.$store.dispatch('map/GET_CITIES_GEOJSON');
                // this.$store.dispatch('map/GET_DEPARTMENTS_GEOJSON');
            },
            checkHandler($event) {
                const name = $event.target.value;
                if (name === 'epci') {
                    this.epci.visible = $event.target.checked;
                    return;
                }
                if (name === 'communes') {
                    this.communes.visible = $event.target.checked;
                }
                if (name === 'showMap') {
                    this.showMap = $event.target.checked;
                }
            },
            resizeMap() {
                this.$refs.dataMap.mapObject.invalidateSize();
            },
            showSynthesis(city_siren) {
                this.$store.dispatch('indicator/SET_CITY_SIREN', city_siren)
            },
            generatePie(element) {
                let svg = d3.select(this.$refs[element.city_id][0])
                    .append("svg")
                    .attr("width", element.size)
                    .attr("height", element.size)
                    .append("g")
                    .attr("transform", "translate(" + element.size / 2 + "," + element.size / 2 + ")");

                let color = d3.scaleOrdinal()
                    .domain(element.data)
                    .range(element.colors);

                let pie = d3.pie()
                    .value(function (d) {
                        return d.value;
                    });

                let data_ready = pie(d3.entries(element.data));

                svg
                    .selectAll('whatever')
                    .data(data_ready)
                    .enter()
                    .append('path')
                    .attr('d', d3.arc()
                        .innerRadius(0)
                        .outerRadius(element.size / 2)
                    )
                    .attr('fill', function (d) {
                        return (color(d.data.key))
                    })
                    .attr("stroke", "white")
                    .style("stroke-width", "1px")
                    .style("opacity", 1);
            },
            generateTriangle(element) {
                d3.select(this.$refs[element.city_siren][0])
                    .append("svg")
                    .attr("width", 40)
                    .attr("height", 40)
                    .append('path')
                    .attr("d", d3.symbol().type(d3.symbolTriangle).size(500))
                    .attr("transform", function (d) {
                        return "translate(" + 20 + "," + 20 + ")";
                    })
                    .style("fill", "#007abf")
                    .attr("stroke", "white")
                    .style("stroke-width", "1px");
            },
            numberFormat(value) {
                return value !== null ? new Intl.NumberFormat('fr-FR').format(parseInt(value)) : 0;
            },
            getLeftPixels(size) {
                return Math.abs(size / 2 - (150 / 2));
            },
        },
        watch: {
            perimeterZoom: function () {
                if (this.perimeterCenter !== undefined && this.perimeterCenter !== null) {
                    this.$refs.dataMap.mapObject.flyTo(
                        L.latLng(this.perimeterCenter.lat, this.perimeterCenter.long),
                        this.perimeterZoom
                    );
                }
            },
        }
    }

</script>

<style scoped>
    .map {
        background-color: rgba(255, 0, 0, 0.0);
    }
</style>
