<template>
    <section class="col-lg-12 col-md-12 col-sm-12 mb-3 mt-5">
        <h4>{{ data.fullName }}</h4>
        <p v-if="data.actualCityId > 0" @click="cityModifications">La commune a subi des variations de périmètre sur
            la période</p>
        <div class="table-responsive">
            <SortedTable class="table " :values="data.data">
                <thead>
                <tr>
                    <th scope="col" v-for="header in data.headers" class="col-auto">
                        <SortLink :name="header">{{ header }}</SortLink>
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody slot="body" slot-scope="sort">
                <tr v-for="(row) in sort.values" :key="row['data_view_line']">
                    <td v-for="(header, index) in data.headers">{{ castValue(row[header]) }}</td>


                    <td class="text-right">
                        <button v-if="detailsEnabled === 1"
                                @click="detailClicked(row['data_line'])"
                                :class="{'more-active': isDetailActive}"
                                class="button more d-inline d-flex align-items-center float-right">
                            Détail <i class="fas fa-chevron-circle-right fa-noactive "></i>
                            <i class="fas fa-chevron-circle-down fa-active"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </SortedTable>
        </div>
    </section>
</template>

<script>
    import DataTable from './DataTable';

    export default {
        name: "DataTableIndicator",
        extends: DataTable,
        data: () => ({
            api: {
                data: '/api/data',
            },
            dataLevel: 'detail',
            details: [],
            isDetailActive: false,
        }),
    }
</script>

<style scoped>

</style>