<template>
    
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
        name: 'TerritoryMap',
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
                    zoomControl: false
                },
                zoom: 8,
                center: L.latLng(47.413220, -1.719482),
            }
        },
        computed: {
            ...mapGetters('map', {
                epcis: 'GET_EPCIS',
                departments: 'GET_DEPARTMENTS',
                cities: 'GET_CITIES',
                rivers: 'GET_RIVERS',
                updatedData: 'GET_UPDATED_DATA',
                perimeterZoom: 'GET_PERIMETER_ZOOM',
            }),
            import_models: function () {
                return this.data.legend !== null ? this.data.legend.import_models : [];
            },
            sizeMax: function () {
                return this.data.legend !== null ? this.data.legend.sizeMax : 0;
            },
            sizeMedian: function () {
                return this.data.legend !== null ? this.data.legend.sizeMedian : 0;
            },
            sizeMin: function () {
                return this.data.legend !== null ? this.data.legend.sizeMin : 0;
            },
            sizeMaxStyle: function () {
                const sizeMax = this.data.legend !== null ? this.data.legend.sizeMax : 0;

                return {
                    height: sizeMax + 'px', width: sizeMax + 'px',
                    display: sizeMax === 0 ? 'none' : 'inline-block',
                    left: this.getLeftPixels(sizeMax) + 'px'
                };
            },
            sizeMedianStyle: function () {
                const sizeMedian = this.data.legend !== null ? this.data.legend.sizeMedian : 0;

                return {
                    height: sizeMedian + 'px', width: sizeMedian + 'px',
                    display: sizeMedian === 0 ? 'none' : 'inline-block',
                    left: this.getLeftPixels(sizeMedian) + 'px'
                };
            },
            sizeMinStyle: function () {
                const sizeMin = this.data.legend !== null ? this.data.legend.sizeMin : 0;

                return {
                    height: sizeMin + 'px', width: sizeMin + 'px',
                    display: sizeMin === 0 ? 'none' : 'inline-block',
                    left: this.getLeftPixels(sizeMin) + 'px'
                };
            },
        },
        mounted() {
            this.forcePerimeterZoom();
            setTimeout(this.resizeMap, 2000);
        },
        methods: {
            computePie() {
                if(this.data.cities !== undefined) {
                    this.data.cities.forEach((element) => {
                        this.generatePie(element);
                    })
                }
            },
            computeTriangle() {
                if(this.data !== undefined) {
                    this.data.cities.forEach((element) => {
                        this.generateTriangle(element);
                    })
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
            forcePerimeterZoom() {
                if (this.data.perimeter_center !== undefined && this.data.perimeter_center !== null) {
                    this.$refs.dataMap.mapObject.flyTo(
                        L.latLng(this.data.perimeter_center.lat, this.data.perimeter_center.long),
                        this.data.perimeter_zoom
                    );
                }
            }
        },
        watch: {},
        props: {
            data: {
                type: Object,
                default: {
                    financial: {
                        min: null,
                        max: null,
                        median: null,
                        legend: null,
                        cities: [],
                    },
                    indicator: [],
                },
            }
        }
    }

</script>

<style scoped>
    .map {
        background-color: rgba(255, 0, 0, 0.0);
    }
</style>