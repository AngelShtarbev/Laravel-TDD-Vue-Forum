<template>

</template>

<script>
    import Replies from './Replies.vue';
    import subscribe from './SubscribeButton.vue';
    export default {
       props: ['initialRepliesCount'],
       components: {Replies, subscribe},
       data() {
          return {
             repliesCount: this.initialRepliesCount
          }
       }
    }
</script>