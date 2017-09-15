<template>
   <div v-if="signedIn">
     <div class="form-group">
        <textarea name="body" id="body" class="form-control" placeholder="Share your thoughts here." rows="5" required v-model="body"></textarea>
     </div>
     <button class="btn btn-primary" @click="addReply">Post</button>
   </div>
   <p class="text-center" v-else>
       Please <a href="/login">sign in</a> to participate in this discussion.
   </p>
</template>

<script>
    import 'at.js';
    import 'jquery.caret';
    export default {
       props: ['endpoint'],
       data() {
          return {
              body: ''
          };
       },
       mounted() {
        $('#body').atwho({
            at: "@",
            delay: 750,
            callbacks: {
            remoteFilter: function(query, callback) {
                $.getJSON("/api/users", {name: query}, function(usernames) {
                     callback(usernames)
                    });
                }
            }
         });
       },
       computed: {
         signedIn() {
            return window.App.signedIn;
         }
       },
       methods: {
          addReply() {
             axios.post(this.endpoint, {body:this.body})
                  .catch(error => {
                     flash(error.response.data,'danger');
                   })
                  .then(response => {
                     this.body = '';
                     this.$emit('created', response.data);
               });
          }
       }
    }
</script>