<template>
    <div>
        <label>Domaines</label>
        <p v-show="financialImportModels.length > 0" class="text-danger">Aucune donnée financière ne doit être sélectionnée.</p>
        <div v-for="(domain, index) in indicatorImportModels">
            <div @click="showChildren(index)" class="h-pointer">
               <span class="fas" :class="{'fa-arrow-right': !hasIncludedChildren(domain), 'fa-arrow-down': hasIncludedChildren(domain)}"></span> {{domain.domainName}}
            </div>
            <ul v-show="hasIncludedChildren(domain)" class="list-group">
                <li v-for="model in domain.nodes"
                    v-bind:class="{'list-group-item-info': selected.includes(model.id), 'list-group-item-light': !isSelectable(model.id)}"
                    class="list-group-item p-1 px-2 h-pointer" @click="onClickElement(index, model.id)">
                    {{model.name}}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "IndicatorImports",
        data: function () {
            return {
                api: {
                    get: '/api/importmodels/domains'
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
                selected: 'getIndicatorImportModels',
                carryingStructure: 'getCarryingStructure',
                financialImportModels: 'getFinancialImportModels'
            }),
            ...mapGetters('indicatorImportModelsFilter', {
                filteredImportModels: 'getFilteredImportModels',
                indicatorImportModels: 'getIndicatorImportModels'
            }),
        },
        mounted() {
            this.$store.dispatch('indicatorImportModelsFilter/initDefault');
        },
        methods: {
            onClickElement: function (index, modelId) {
                if (this.isIncluded(modelId) && this.financialImportModels.length === 0) {
                    if (this.selected.includes(modelId)) {
                        this.$store.dispatch('form/removeIndicatorImportModel', modelId);
                        return;
                    }
                    this.$store.dispatch('form/addIndicatorImportModel', modelId);
                }
            },
            showChildren: function (index) {
                this.$store.commit('indicatorImportModelsFilter/toggleShowChildren', index);
            },
            hasIncludedChildren: function (domain) {
                let hasIncludedChildren = domain.nodesIds.some(function (importModelId) {
                    return this.filteredIds.includes(importModelId);
                }, {filteredIds: this.filteredImportModels});

                if (hasIncludedChildren && domain.showChildren) {
                    return true;
                }

                if (!hasIncludedChildren && domain.showChildren) {
                    return false;
                }

                if (!hasIncludedChildren && !domain.showChildren) {
                    return false;
                }

                if (hasIncludedChildren && !domain.showChildren) {
                    return false;
                }

                return hasIncludedChildren;
            },
            isIncluded: function (importModelId) {
                return this.filteredImportModels.includes(importModelId);
            },
            isSelectable: function (modelId) {
                return this.isIncluded(modelId) && this.financialImportModels.length === 0;
            }
        },
    }
</script>

<style scoped>

</style>