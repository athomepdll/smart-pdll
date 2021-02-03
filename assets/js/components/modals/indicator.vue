<template>
    <transition name="modal" v-if="city_siren !== null">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-body">
                        <slot name="body">
                            <DataTableIndicator v-for="indicator in data"
                                                :key="indicator.name"
                                                :data="indicator"
                                                :detailsEnabled="0"
                                                :truncate-text="0"
                            >

                            </DataTableIndicator>
                            <a v-if="insee !== null" :href="inseeLink()" target="_blank" rel="noopener">Plus de détails sur le site de l'INSEE</a>
                        </slot>
                    </div>
                    <div class="modal-footer">
                        <slot name="footer">
                            <button class="btn btn-primary" @click="closeModal">
                                OK
                            </button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import {mapGetters} from "vuex";
    import DataTableIndicator from '../dataTable/DataTableIndicator';

    export default {
        name: "modalIndicator",
        components: {DataTableIndicator},
        computed: {
            ...mapGetters('indicator', {
                city_siren: 'GET_CITY_SIREN',
                data: 'GET_DATA',
                insee: 'GET_INSEE'
            })
        },
        methods: {
            closeModal () {
                this.$store.dispatch('indicator/SET_CITY_SIREN', null);
            },
            inseeLink () {
                return this.insee !== null ?  process.env.INSEE_HOST + '?geo=COM-' + this.insee : '';
            }
        }
    }
</script>

<style scoped>

</style>