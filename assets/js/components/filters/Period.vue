<template>
    <div class="form__group">
        <label>Période</label>
        <div>
            De
            &nbsp;
            <select ref="yearStart" v-model="yearStart" name="yeartStart" class="col-auto">
                <option :value="null"></option>
                <option v-for="year in years" v-bind:value="year">
                    {{year}}
                </option>
            </select>
            &nbsp;
            A
            &nbsp;
            <select ref="yearEnd" v-model="yearEnd" name="yearEnd" class="col-auto">
                <option :value="null"></option>
                <option v-for="year in years" v-bind:value="year">
                    {{year}}
                </option>
            </select>
        </div>
        <p v-show="errors.includes('period')" class="text-danger">Vous devez sélectionner une date de début.</p>
    </div>
</template>

<script>
    import axios from 'axios';
    import {mapGetters} from "vuex";

    export default {
        name: "Period",
        data: function () {
            return {
                api: {
                    get: '/api/importlog/year'
                },
                years: [],
            }
        },
        computed: {
            yearStart: {
                get () {
                    return this.$store.getters['form/getYearStart'];
                },
                set (value) {
                    const setValue = this.yearEnd !== null && this.yearEnd < value ? null : value;
                    this.$store.dispatch('form/setYearStart', setValue);
                    if (setValue === null) {
                        this.$refs.yearStart.value = null;
                    }
                }
            },
            yearEnd: {
                get () {
                    return this.$store.getters['form/getYearEnd'];
                },
                set (value) {
                    let setValue = this.yearStart !== null && this.yearStart > value ? null : value;
                    this.$store.dispatch('form/setYearEnd', setValue);
                    if  (setValue === null) {
                        this.$refs.yearEnd.value = null;
                    }
                }
            },
            ...mapGetters('form', {
                errors: 'getErrors'
            })
        },
        mounted() {
            this.updateData();
        },
        methods: {
            updateData: async function () {
                try {
                    let response = await axios.get(process.env.API_HOST + this.api.get);
                    this.years = response.data.data;
                } catch (error) {
                    throw error;
                }
            },
        }
    }
</script>

<style scoped>

</style>