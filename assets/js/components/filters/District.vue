<template>
    <div class="form__group">
        <label>Périmètre géographique</label>
        <select v-model="selected"
                name="district"
                ref="select"
                class="col-lg-12 selectpicker filter-select"
                data-live-search="true"
        >
            <option :value="null">Arrondissements</option>
            <option v-for="district in districts" v-bind:value="district.id">
                {{district.name}}
            </option>
        </select>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "District",
        data: function () {
            return {
                api: {
                    get: '/api/districts',
                },
            }
        },
        computed: {
            selected: {
                get () {
                    return this.$store.getters['form/getDistrict'];
                },
                set (value) {
                    this.$store.dispatch('form/setDistrictAction', value);
                }
            },
            ...mapGetters('form', {
                department: 'getDepartment',
            }),
            ...mapGetters('districtFilter', {
                districts: 'getDistricts',
            }),
        },
        updated() {
            $(this.$refs.select).selectpicker('refresh')
        },
        props: {
            preferenceDepartment: {
                type: String,
                default: null
            }
        }
    }
</script>

<style scoped>

</style>