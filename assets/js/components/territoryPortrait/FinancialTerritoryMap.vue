<template>
    <l-map class="map" :options="options" :zoom="zoom" :center="center" ref="dataMap">
        <div id="svg-dumper" hidden="hidden"></div>
        <l-geo-json :geojson="epcis.data" :optionsStyle="epcis.options">
        </l-geo-json>
        <l-geo-json :geojson="rivers.data" :optionsStyle="rivers.options">
        </l-geo-json>
        <l-geo-json :geojson="cities.data" :optionsStyle="cities.options">
        </l-geo-json>
        <l-geo-json :geojson="departments.data" :optionsStyle="departments.options">
        </l-geo-json>
        <l-marker
                v-for="(financial, index) in data.cities"
                :key="financial.city_id"
                :lat-lng="[financial.lat, financial.long]"
        >
            <l-tooltip :options="{permanent: true, direction: 'top'}">
                <strong>{{ financial.city_name }}</strong>
                <!--<ul>-->
                    <!--<li v-for="(datum, index) in financial.data">{{ financial.import_models[index] }} :-->
                        <!--{{ numberFormat(datum) }}-->
                    <!--</li>-->
                <!--</ul>-->
            </l-tooltip>
            <l-icon
                    :icon-size="[financial.size, financial.size]"
            >
                <div :ref="financial.city_id"></div>
            </l-icon>
        </l-marker>
        <l-control-scale position="bottomleft" :imperial="false" :metric="true"></l-control-scale>
        <l-control position="bottomleft">
            <div class="legend-block">
                <div class="legend-background"></div>
                <div class="financial-legend-title">
                    Financements :
                    <ul class="legend-list">
                        <li v-for="(import_model_color, index) in import_models">
                            <div class="d-flex flex-row justify-content-start align-items-start">
                                <div class="align-self-center">{{ index }}</div>
                                <div class="legend-import-model align-self-center ml-1"
                                     :style="{background: import_model_color}"></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="financial-legend-title">
                        Montant en euros :
                    </div>
                    <div class="d-flex">
                        <div class="dot-legend"
                             v-show="data.max !== null && data.cities.length"
                             :style="{'bottom': sizeMax + 'px'}">
                            {{ numberFormat(data.max) }}
                        </div>
                        <span class="dot"
                              :style="sizeMaxStyle">
                        </span>
                    </div>
                    <div>
                        <div class="dot-legend"
                             v-show="data.median !== null && data.cities.length"
                             :style="{'bottom': sizeMedian + 'px'}">
                            {{ numberFormat(data.median) }}
                        </div>
                        <span class="dot"
                              :style="sizeMedianStyle">
                        </span>
                    </div>
                    <div>
                        <div class="dot-legend" v-show="data.min !== null && data.cities.length"
                             :style="{'bottom': sizeMin+ 'px'}">
                            {{ numberFormat(data.min) }}
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
            <img class="navbar__logo mr-0 north-compass" src="../../../images/north-compass.png"
                 alt="Nord Géographique"/>
        </l-control>
    </l-map>
</template>

<script>
    import TerritoryMap from './TerritoryMap'

    export default {
        name: 'FinancialTerritoryMap',
        extends: TerritoryMap,
        mounted() {
            setTimeout(this.resizeMap, 2000);
            setTimeout(this.computePie, 2000);
            this.forcePerimeterZoom();
        },
    }

</script>


