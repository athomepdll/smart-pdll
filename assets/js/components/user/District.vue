<template>
    <div class="form-group">
        <label>Arrondissement</label>
        <div >
            <select v-model="selected"
                    :name="formName"
                    ref="select"
                    class="w-100 selectpicker"
                    data-live-search="true"
            >
                <option :value="null">Arrondissement</option>
                <option v-for="district in districts" v-bind:value="district.id">
                    {{district.name}}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
    import District from "../filters/District";
    import {mapGetters} from "vuex";

    export default {
        name: "District",
        extends: District,
        computed: {
            selected: {
                get () {
                    return this.$store.getters['userAccountForm/GET_DISTRICT'];
                },
                set (value) {
                    this.$store.commit('userAccountForm/SET_DISTRICT', value);
                }
            },
            ...mapGetters('userAccountForm', {
                districts: 'GET_DISTRICTS',
            }),
        },
        updated() {
            $(this.$refs.select).selectpicker('refresh')
        },
        props: {
            formName: {
                type: String,
                default: null
            }
        }
    }
</script>

<style scoped>

</style>