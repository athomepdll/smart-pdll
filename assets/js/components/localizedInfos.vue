<template>
    <div v-if="localizedInfos !== null && localizedInfos !== undefined "
         class="mt-2 d-flex justify-content-between">
        <ul>
            <li>{{ countLocalized() }}</li>
            <li>{{ sumTotal() }}</li>
        </ul>
        <ul>
            <li>{{ sumLocalized() }}</li>
            <li>{{ sumNotLocalized() }}</li>
        </ul>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "localizedInfos",
        computed: {
            ...mapGetters('map', {
                localizedInfos: 'GET_LOCALIZED_INFOS',
            }),
        },
        methods: {
            numberFormat(value, fractionDigit = 0) {
                return value !== null ? new Intl.NumberFormat('fr-FR', {maximumFractionDigits: 2})
                    .format(value) : 0;
            },
            countLocalized() {
                return this.numberFormat(this.localizedInfos.count_localized) + ' financements localisés sur '
                    + this.numberFormat(this.localizedInfos.count_total)
                    + ' ('
                    + this.numberFormat((this.localizedInfos.count_localized / this.localizedInfos.count_total) * 100)
                    + ' % ) ';
            },
            sumTotal() {
                return 'Total des financements considérés : ' + this.numberFormat(this.localizedInfos.total_sum) + ' Euros'
            },
            sumLocalized() {
                return 'Total des financements localisés ('
                    + this.numberFormat((this.localizedInfos.total_sum_localized / this.localizedInfos.total_sum) * 100)
                    + ' %) : '
                    + this.numberFormat(this.localizedInfos.total_sum_localized, 2) + ' Euros';
            },
            sumNotLocalized() {
                return 'Total des financements non localisés ( '
                    + this.numberFormat((this.localizedInfos.total_sum_not_localized / this.localizedInfos.total_sum) * 100)
                    + ' %) : '
                    + this.numberFormat(this.localizedInfos.total_sum_not_localized) + ' Euros'
            }
        }
    }
</script>

<style scoped>

</style>