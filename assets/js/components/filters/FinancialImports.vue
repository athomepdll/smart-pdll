<template>
    <div>
        <label>Données financières</label>
        <p v-show="indicatorImportModels.length > 0" class="text-danger">Aucun Indicateur ne doit être sélectionné.</p>
        <ul class="list-group">
            <li v-for="model in financialImportModels"
                v-bind:class="{'list-group-item-info': selected.includes(model.id), 'list-group-item-light': !isSelectable(model.id)}"
                 class="list-group-item p-1 px-2 h-pointer" @click="onClickElement(model.id)">
                {{model.name}}
            </li>

        </ul>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "FinancialImports",
        data: function () {
            return {
                api: {
                    get: '/api/importmodels'
                },
            }
        },
        computed: {
            ...mapGetters('form', {
                yearStart: 'getYearStart',
                yearEnd: 'getYearEnd',
                district: 'getDistrict',
                epci: 'getEpci',
                city: 'getCity',
                selected: 'getFinancialImportModels',
                carryingStructure: 'getCarryingStructure',
                indicatorImportModels: 'getIndicatorImportModels',
            }),
            ...mapGetters('financialImportModelsFilter', {
                filteredImportModels: 'getFilteredImportModels',
                financialImportModels: 'getFinancialImportModels',
                financialImportModelsId: 'getFinancialImportModelsId'
            }),
            included: function () {
                return this.financialImportModelsId.filter(function (importModelId) {
                    return this.filteredIds.includes(importModelId);
                }, {filteredIds: this.filteredImportModels});
            }
        },
        mounted() {
            this.$store.dispatch('financialImportModelsFilter/initDefault');
        },
        methods: {
            onClickElement: function (modelId) {
                if (this.included.includes(modelId) && this.indicatorImportModels.length === 0) {
                    if (this.selected.includes(modelId)) {
                        this.$store.dispatch('form/removeFinancialImportModel', modelId);
                        return;
                    }
                    this.$store.dispatch('form/addFinancialImportModel', modelId);
                }
            },
            isSelectable: function (modelId) {
                return this.included.includes(modelId) && this.indicatorImportModels.length === 0;
            }
        },
    }
</script>

<style scoped>

</style>