<template>
    <div class="col-lg-12">
        <button :name="name" :id="id" class="btn btn-primary" type="submit" :disabled="loading">
            <span v-show="loading === false" >Importer</span>
            <span v-show="loading === true" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-show="loading === true" class="ml-2">Import en cours ...</span>
        </button>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: "insee",
        components: {
        },
        data() {
            return {
                name: 'insee[submit]',
                id: 'insee_submit',
                loading: false,
                errors: null,
                submitButton: document.querySelector('#insee_submit'),
            }
        },
        mounted() {
            this.isRunning();
            setInterval(() => {
                this.isRunning();
            }, 5000);
        },
        methods: {
            isRunning: function () {
                axios.post(this.path, {type: 'insee'})
                    .then((response) => {
                        this.handleResponse(response);
                    })
                    .catch((error) => {
                        this.showFlash('error');
                        this.loading = false;
                    });
            },
            handleResponse: function (response) {
                if (response.data.loading === this.loading && response.data.errors === this.errors) {
                    return;
                }

                if (response.data.errors !== null) {
                    this.errors = response.data.errors;
                    this.showFlash('error', response.data.errors);
                }

                if (response.data.loading === true) {
                    this.loading = response.data.loading;
                    return;
                }

                this.loading = response.data.loading;
                this.errors = response.data.errors;
                this.showFlash('success');
            },
            showFlash: function (type, message) {
                if (type === 'success') {
                    this.flash('Importation des données réussi !', 'success');
                    return;
                }

                if (type === 'error') {
                    const error = message === null ? 'Un problème est survenu lors de l\'importation des données' : message;
                    this.flash(error, 'error');
                    return;
                }
            }
        },
        props: {
            isLoading: {
                type: Number,
                default: 0
            },
            path: {
                type: String,
                default: ''
            },

        },

    }
</script>

<style>
</style>