<template>
    <div :id="'reply-'+id" class="panel" :class="isBest ? 'panel-success': 'panel-default'">
        <div class="panel-heading">
            <div class="level">
                <h4 class="flex"><a :href="'/profiles/'+data.owner.name" v-text="data.owner.name"></a> said <span v-text="ago"></span></h4>

                <div v-if="signedIn">
                    <favorite :reply="data"></favorite>
                </div>

            </div>
        </div>
        <div class="panel-body">
            <div v-if="editing" class="body">
                <form @submit.prevent="update">
                   <div class="form-group">
                      <textarea class="form-control" v-model="body" required></textarea>
                   </div>
                   <button class="btn btn-primary">Update</button>
                   <button class="btn btn-link" @click="editing = false" type="button">Cancel</button>
                </form>
            </div>
            <div v-else v-html="body" class="body"></div>
        </div>
        <div class="panel-footer">
        <div v-if="canUpdate">
            <button class="btn btn-primary" @click="editing = true">Edit</button>
            <button class="btn btn-danger" @click="destroy">Delete</button>
        </div>
        <div>
           <button class="btn btn-default" @click="markBestReply" v-show="!isBest">Best Reply?</button>
        </div>
      </div>
    </div>
</template>
<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
       props: ['data'],
       components: {Favorite},
       data() {
           return {
             editing: false,
             id: this.data.id,
             body: this.data.body,
             isBest: false
           };
       },
       computed: {
         ago() {
           return moment(this.data.created_at).fromNow();
         },
         signedIn() {
           return window.App.signedIn;
         },
         canUpdate() {
           return this.authorize(user => this.data.user_id == user.id);
         }
       },
       methods: {
          update() {
              axios.patch('/replies/' + this.data.id, {
                  body: this.body
              }).catch(error => {
                   flash(error.response.data, 'danger');
               }).then(response => {
                   this.editing = false;
               });

          },
          destroy() {
              axios.delete('/replies/' + this.data.id);

              this.$emit('deleted', this.data.id);

               flash('Reply successfully deleted !');
          },
          markBestReply() {
             this.isBest = true;
          }
       }
    }
</script>
