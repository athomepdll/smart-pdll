<template>
    <div>
        <span v-if="canGeneratePortrait === true"
              v-tooltip.top="{content: helpMessage(), offset: 10}"
              class="mb-2"
        >
                <i class="fas fa-question-circle"></i>
        </span>
        <button class="btn btn-outline-dark col-lg-12"
                :disabled="canGeneratePortrait"
                @click="launchMapGeneration"
        >
            Portrait de territoire
        </button>
    </div>

</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "TerritoryPortrait",
        computed: {
            ...mapGetters('form', {
                department: 'getDepartment',
                epci: 'getEpci',
                city: 'getCity',
                yearEnd: 'getYearEnd',
                yearStart: 'getYearStart'
            }),
            canGeneratePortrait: function () {
                return !(this.yearStart !== null && (this.epci !== null || this.city !== null));
            }
        },
        methods: {
            launchMapGeneration: function () {
                this.$store.dispatch('territoryPortrait/LAUNCH_MAP_GENERATION');
            },
            helpMessage: function () {
                return 'Pour générer un portrait de territoire, il faut obligatoirement sélectionner un epci ou une commune.';
            }
        }
    }
</script>

<style scoped>

</style>