<template>
    <div class="form__group">
        <label>Départements</label>
        <div>
            <select v-model="selected"
                    id="department"
                    ref="select"
                    name="department"
                    class="w-100 selectpicker filter-select"
                    data-live-search="true"
            >
                <option :value="null">Département</option>
                <option v-for="department in departments" v-bind:value="department.id">
                    {{department.name}}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import {mapGetters} from "vuex";

    export default {
        name: "Department",
        data: function () {
            return {
                api: {
                    'get': '/api/departments'
                },
            }
        },
        computed: {
            ...mapGetters('departmentFilter', {
                departments: 'GET_DEPARTMENTS'
            }),
            selected: {
                get () {
                    return this.$store.getters['form/getDepartment'];
                },
                set (value) {
                    this.$store.dispatch('form/setDepartmentAction', value);
                }
            },
        },
        updated() {
            $(this.$refs.select).selectpicker('refresh')
        },
        mounted() {
            // this.updateData();
            this.$store.dispatch('departmentFilter/FETCH_DEPARTMENTS');
        },
        methods: {
            // updateData: async function () {
            //     try {
            //         let response = await axios.get(process.env.API_HOST + this.api.get);
            //         this.departments = response.data.data;
            //         // this.selected = null;
            //     } catch (error) {
            //         throw error;
            //     }
            // },
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