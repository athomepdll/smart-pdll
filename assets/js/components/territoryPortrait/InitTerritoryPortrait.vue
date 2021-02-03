<template>
    
</template>

<script>
    export default {
        name: "InitTerritoryPortrait",
        mounted() {
            this.$store.dispatch('financialDataTable/initFinancialFields');
            this.getGeojson();
        },
        methods: {
            async getGeojson() {
                await this.$store.dispatch('map/GET_EPCIS_GEOJSON');
                // this.$store.dispatch('map/GET_RIVERS_GEOJSON');
                // this.$store.dispatch('map/GET_CITIES_GEOJSON');
                // this.$store.dispatch('map/GET_DEPARTMENTS_GEOJSON');
            }
        }
    }
</script>

<style scoped>

</style>