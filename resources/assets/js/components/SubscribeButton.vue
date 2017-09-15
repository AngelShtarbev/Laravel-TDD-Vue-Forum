<template>
    <button :class="classes" @click="subscribe" v-text="isSubscribed"></button>
</template>

<script>
    export default {
        props:['active'],
        computed: {
           classes() {
              return ['btn', this.active ? 'btn-danger' : 'btn-default'];
            },
           isSubscribed() {
              return this.active ? 'Unsubscribe' : 'Subscribe';
            }
        },
        methods: {
            subscribe() {

               let requestType = this.active ? 'delete' : 'post';

               axios[requestType](location.pathname + '/subscriptions');

               this.active = !this.active;
            }
        }
    }
</script>