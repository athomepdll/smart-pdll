<template>
    <l-map class="map" :zoom="zoom" :center="center" ref="dataMap" :options="options">
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
                v-for="indicator in data.cities"
                :key="indicator.city_siren"
                :lat-lng="[indicator.lat, indicator.long]"
                @click="showSynthesis(indicator.city_siren)"
        >
            <l-tooltip :options="{permanent: true, direction: 'top'}">
                {{ indicator.name }}
            </l-tooltip>
            <l-icon
                    :icon-size="[40, 40]"
            >
                <div :ref="indicator.city_siren"></div>
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
                                <div v-if="import_model_color !== null"
                                     class="legend-import-model align-self-center ml-1"
                                     :style="{background: import_model_color}"></div>
                            </div>
                        </li>
                    </ul>
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
        name: 'IndicatorTerritoryMap',
        extends: TerritoryMap,
        mounted() {
            setTimeout(this.resizeMap, 2000);
            setTimeout(this.computeTriangle, 2000);
            this.forcePerimeterZoom();
        },
    }
</script>