<template>
    <transition name="modal" v-if="dataLine !== null">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">

                    <div class="modal-header">
                        <slot name="header">
                            Détails
                        </slot>
                    </div>

                    <div class="modal-body">
                        <slot name="body">
                            <table v-if="details.length" class="table border-primary">
                                <thead>
                                </thead>
                                <tbody>
                                <tr v-for="detail in details">
                                    <td>{{ detail.field }}</td>
                                    <td>{{ detail.value }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <p v-else>Aucun détails disponible pour cette donnée.</p>
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
    import {mapGetters} from 'vuex';

    export default {
        name: "modalDetails",
        computed: {
            ...mapGetters('details', {
                dataLine: 'getDataLine',
                details: 'getDetails',
            })
        },
        methods: {
            closeModal () {
                this.$store.commit('details/setDataLine', null);
            }
        }
    }
</script>

<style scoped>

</style>