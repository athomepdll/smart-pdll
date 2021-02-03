<template>
    <div class="form-group">
        <label>Département</label>
        <div>
            <select v-model="selected"
                    id="department"
                    ref="select"
                    :name="formName"
                    class="w-100 selectpicker"
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
    import Department from "../filters/Department";

    export default {
        name: "Department",
        extends: Department,
        computed: {
            selected: {
                get () {
                    return this.$store.getters['userAccountForm/GET_DEPARTMENT'];
                },
                set (value) {
                    this.$store.dispatch('userAccountForm/SET_DEPARTMENT_ACTION', value);
                }
            },
        },
        updated() {
            $(this.$refs.select).selectpicker('refresh')
        },
        async mounted() {
            await this.updateData();
            await this.$store.dispatch('userAccountForm/SET_PREFERENCES');
        },
        props: {
            formName: {
                type: String,
                default: null
            },
        }
    }
</script>

<style scoped>

</style>