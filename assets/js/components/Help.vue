<template>
    <div class="col">
        <div v-html="compiledMarkdown"></div>
    </div>
</template>

<script>
    require('../vendor/marked');

    export default {
        name: "Help",
        data: () => ({
            markdown: '',
        }),
        mounted() {
            this.markdown = this.input;
        },
        computed: {
           compiledMarkdown: function () {
               return marked(this.markdown, { sanitize: true });
           }
        },
        props: {
            input: {
                type: String,
                default: ''
            }
        }
    }
</script>

<style scoped>

</style>