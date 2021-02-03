<template>
    <div id="filtersContainer" class="col-lg-2 col-md-3 col-sm-3 panel">
        <period></period>
        <hr>
        <department></department>
        <hr>

        <district></district>
        <epci></epci>
        <city></city>
        <hr>
        <carrying-structure></carrying-structure>
        <hr>
        <financial-imports></financial-imports>
        <hr>
        <indicator-imports></indicator-imports>
        <hr>
        <TerritoryPortrait></TerritoryPortrait>
        <hr>
        <button class="btn btn-outline-info col-lg-12" @click="removeFilters">Effacer les filtres</button>
    </div>
</template>

<script>
    import Department from "./filters/Department";
    import Period from "./filters/Period";
    import District from "./filters/District";
    import Epci from "./filters/Epci";
    import City from "./filters/City";
    import FinancialImports from "./filters/FinancialImports";
    import IndicatorImports from "./filters/IndicatorImports";
    import CarryingStructure from "./filters/CarryingStructure";
    import TerritoryPortrait from './filters/TerritoryPortrait';

    export default {
        name: "FiltersContainer",
        components: {
            CarryingStructure,
            IndicatorImports,
            FinancialImports,
            City,
            Epci,
            District,
            Period,
            Department,
            TerritoryPortrait
        },
        data: function () {
            return {}
        },
        mounted() {
            this.$store.dispatch('form/setPreferences');
        },
        methods: {
            removeFilters: function () {
                this.$store.dispatch('form/eraseFilters');
            }
        }
    }
</script>

<style scoped>
</style>