<template>
    <div id="export">
        <div>
            <button @click="exportData"
                    class="btn btn-primary ">
                <span class="fa fa-file-export"></span>
            </button>
            <button @click="exportDataPdf"
                    class="btn btn-primary ml-2">
                <span class="fa fa-print"></span>
            </button>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import axios from "axios";
    import api from "../config/routes";

    export default {
        name: "Export",
        computed: {
            ...mapGetters('exportModule', {
                loading: 'GET_LOADING',
            }),
        },
        methods: {
            async exportData() {
                await this.$store.commit('exportModule/SET_LOADING', true);
                const form = this.$store.getters['form/getForm'];
                try {
                    const response = await axios.post( process.env.API_HOST + api.export, form);
                    window.location.href = process.env.API_HOST + api.downloadExport + "?filename=" + response.data.data;
                    await this.$store.commit('exportModule/SET_LOADING', false);
                } catch (error) {
                    console.log(error);
                }
            },
            async exportDataPdf() {
                window.print();
                return false;
            },
        }
    }
</script>

<style scoped>

</style>