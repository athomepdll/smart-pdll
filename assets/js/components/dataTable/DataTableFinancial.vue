<template>
    <section class="col-lg-12 col-md-12 col-sm-12 mb-3 mt-5">
        <h4>{{ data.fullName }}</h4>
        <p v-if="data.actualCityId > 0" class="city-change font-italic" @click="cityModifications">
            La commune a subi des variations de périmètre sur la période</p>
        <SortedTable class="table mb-0" :values="data.data">
            <thead>
            <tr>
                <th class="w-18 align-middle" scope="col">
                    <SortLink name="Porteur du projet">Porteur du projet</SortLink>
                </th>

                <th scope="col" v-for="header in financialFields" class="w-18 align-middle">
                    <SortLink :name="header.value">{{ header.value }}</SortLink>
                </th>
                <th class="detailColumn"></th>
            </tr>
            </thead>
            <tbody slot="body" slot-scope="sort">
            <tr v-for="(row) in sort.values" :key="row['data_view_line']">
                <td>{{ row['carrier_name'] }}</td>
                <td v-for="(header, index) in financialFields">
                    <span v-if="isTooltip(row[header.value])" v-tooltip.top="{content: row[header.value], offset: 10}">
                        {{ castValue(row[header.value]) }}
                    </span>
                    <span v-else>{{ castValue(row[header.value]) }}</span>
                </td>
                <td class="detailColumn">
                    <button v-if="detailsEnabled === 1"
                            @click="detailClicked(row['data_line'])" :class="{'more-active': isDetailActive}"
                            class="button more d-inline d-flex align-items-center">
                        Détail <i class="fas fa-chevron-circle-right fa-noactive "></i> <i
                            class="fas fa-chevron-circle-down fa-active"></i>
                    </button>
                </td>
            </tr>
            <tr class="section-grey">
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ data.totalGrant }}</td>
                <td>{{ data.totalHt }}</td>
            </tr>
            </tbody>
        </SortedTable>
    </section>
</template>

<script>
    import DataTable from './DataTable';
    import {mapGetters} from "vuex";

    export default {
        name: "DataTableFinancial",
        extends: DataTable,
        data: () => ({
            api: {
                data: '/api/data',
            },
            dataLevel: 'detail',
            details: [],
            isDetailActive: false,
        }),
        computed: {
            ...mapGetters('financialDataTable', {
                financialFields: 'getFinancialFields'
            })
        },
        methods: {}
    }
</script>

<style scoped>

</style>