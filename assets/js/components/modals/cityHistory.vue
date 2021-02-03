<template>
    <transition name="modal" v-if="cityId !== null">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">

                    <div class="modal-header">
                        <slot name="header">
                            Modifications de périmètre
                        </slot>
                    </div>

                    <div class="modal-body">
                        <slot name="body">
                            {{ cityName }} correspond précédemment à :
                            <ul>
                                <li v-for="city in cityChanges">{{ city.name }} ({{ city.year }})</li>
                            </ul>
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
        name: "modalCity",
        computed: {
            ...mapGetters('cityHistory', {
                cityName: 'getCityName',
                cityChanges: 'getCityChanges',
                cityId: 'getActualCity'
            })
        },
        methods: {
            closeModal () {
                this.$store.commit('cityHistory/setActualCity', null);
            }
        }
    }
</script>

<style scoped>

</style>