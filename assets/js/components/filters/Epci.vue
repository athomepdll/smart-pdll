<template>
    <div>
        <select v-model="selected"
                name="epci"
                ref="select"
                class="col-lg-12 mt-1 selectpicker filter-select"
                data-live-search="true"
                :disabled="district === null"
        >
            <option :value="null">Epci</option>
            <option v-for="epci in epcis" v-bind:value="epci.siren">
                {{epci.name}}
            </option>
        </select>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "Epci",
        data: function () {
            return {
                api: {
                    get: '/api/epcis',
                },
            }
        },
        computed: {
            selected: {
                get () {
                    return this.$store.getters['form/getEpci'];
                },
                set (value) {
                    this.$store.dispatch('form/setEpciAction', value);
                }
            },
            ...mapGetters('epciFilter', {
                epcis: 'getEpcis',
            }),
            ...mapGetters('form', {
                district: 'getDistrict'
            })
        },
        updated() {
            $(this.$refs.select).selectpicker('refresh')
        },
    }
</script>

<style scoped>

</style>