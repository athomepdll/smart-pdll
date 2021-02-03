<template>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "DataTable",
        data: () => ({
            api: {
                data: '/api/data',
            },
            dataLevel: 'detail',
            details: [],
            isDetailActive: false,
        }),
        computed: {
            ...mapGetters('details', {
                dataLine: 'getDataLine'
            })
        },
        mounted() {
        },
        methods: {
            detailClicked: function (dataLineId) {
                // this.isDetailActive === false ? this.showDetails(dataLineId) :
                //     this.dataLine === dataLineId ? this.hideDetails() : this.showDetails(dataLineId);
                this.dataLine === null ? this.showDetails(dataLineId) : this.hideDetails();
            },
            showDetails: async function (dataLineId) {
               await this.$store.dispatch('details/callApi', dataLineId);
            },
            hideDetails: function () {
                this.$store.commit('details/setDataLine', null);
                this.$store.commit('details/setDetails', []);
            },
            cityModifications: async function () {
                await this.$store.dispatch('cityHistory/setActualCity', this.data.actualCityId);
                await this.$store.dispatch('cityHistory/setCityName', this.data.actualCityName);
                await this.$store.dispatch('cityHistory/callApiCityChanges');
            },
            isTooltip: function (value) {
                if (value === undefined || value === null) {
                    return false;
                }

                if (this.isNumber(value)) {
                    return false;
                }

                return value.length > 20;
            },
            isNumber: function (value) {
                const regex = /^-?\d+\.?\d*$/;
                return regex.exec(value);
            },
            castValue: function (value) {
                if (value === undefined || value === null) {
                    return value;
                }

                if (this.isNumber(value)) {
                    return new Intl.NumberFormat('fr-FR').format(parseFloat(value));
                }

                if (value.length > 20 && this.truncateText === 1) {
                    return value.slice(0,50) + '...';
                }

                return value
            }
        },
        props: {
            data: {
                type: Object,
            },
            detailsEnabled: {
                type: Number,
                default: 1
            },
            truncateText: {
                type: Number,
                default: 1
            }
        },
    }
</script>

<style scoped>

</style>