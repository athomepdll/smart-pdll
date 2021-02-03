<template>
    <div class="form__group">
        <label>Structure porteuse</label>
        <select :disabled="disabled"
                v-model="selected"
                ref="select"
                name="carryingStructure"
                class="col-lg-12 mb-1 selectpicker filter-select"
                data-live-search="true"
        >
            <option :value="null">Structure porteuse</option>
            <option v-for="structure in carryingStructures" :value="structure.siren">
                {{structure.name}}
            </option>
        </select>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "CarryingStructure",
        data: function () {
            return {
                api: {
                    get: '/api/carryingstructures',
                },
            }
        },
        computed: {
            selected: {
                get () {
                    return this.$store.getters['form/getCarryingStructure'];
                },
                set (value) {
                    this.$store.dispatch('form/setCarryingStructure', value);
                }
            },
            ...mapGetters('form', {
                district: 'getDistrict',
                epci: 'getEpci',
                city: 'getCity'
            }),
            ...mapGetters('carryingStructureFilter', {
                carryingStructures: 'getCarryingStructures',
                disabled: 'getDisabled',
            }),
        },
        updated() {
            $(this.$refs.select).selectpicker('refresh')
        },
    }
</script>

<style scoped>

</style>