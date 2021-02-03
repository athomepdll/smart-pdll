<template>
    <div>
        <select v-model="selected"
                name="city"
                ref="select"
                class="col-lg-12 mt-1 selectpicker filter-select"
                data-live-search="true"
                :disabled="district === null"
        >
            <option :value="null">Communes</option>
            <option v-for="city in cities" v-bind:value="city.siren">
                {{city.name}}
            </option>
        </select>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "City",
        data: function () {
            return {
                api: {
                    get: '/api/cities',
                },
            }
        },
        computed: {
            selected: {
                get () {
                    return this.$store.getters['form/getCity'];
                },
                set (value) {
                    this.$store.dispatch('form/setCityAction', value);
                }
            },
            ...mapGetters('cityFilter', {
                cities: 'getCities'
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