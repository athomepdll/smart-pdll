<template>
    <div>
        <div v-for="(notification, index) in notifications" :class="notification.class"
             class="alert alert-dismissible fade show" role="alert">
            <strong>{{ notification.message }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                    @click="removeNotification(index)">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="row">
            <div class="col">
                <textarea name="help_markdown[markdown]" v-model="markdown" class="form-control" rows="25"></textarea>
            </div>
            <div class="col">
                <div v-html="compiledMarkdown"></div>
            </div>
        </div>
        <div class="p-2">
            <button class="btn btn-primary" type="button" @click="send">
                Enregistrer
            </button>
        </div>
    </div>
</template>

<script>
    require('../../vendor/marked');
    import routes from '../../config/routes';
    import axios from 'axios';

    export default {
        name: "Markdown",
        data: () => ({
            markdown: '',
            notifications: []
        }),
        mounted() {
            this.markdown = this.input;
        },
        computed: {
            compiledMarkdown: function () {
                return marked(this.markdown, {sanitize: true});
            }
        },
        methods: {
            send: async function () {
                try {
                    await axios.patch(process.env.API_HOST + routes.enumeration, {
                        discr: 'enum',
                        name: 'help_page',
                        value: this.markdown
                    });
                    this.addNotification({class: 'alert-success', message: 'Enregistrement réussi.'})
                } catch (e) {
                    this.addNotification({
                        class: 'alert-danger',
                        message: 'Erreur lors de l\'enregistrement.' + e.message
                    })
                }
            },
            addNotification: function (notification) {
                this.notifications.push(notification);
            },
            removeNotification: function (index) {
                this.notifications.splice(index, 1);
            }
        },
        props: {
            input: {
                type: String,
                default: '# Configuration de la page d\'aide.'
            }
        }
    }
</script>

<style scoped>

</style>