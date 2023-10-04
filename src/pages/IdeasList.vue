<template>
  <div>
    <div class="flex justify-end" style="width: 100%">
      <q-btn color="blue" icon="style" label="New idea" @click="modalOpen = true"/>
    </div>
    <q-page class="flex flex-center full-width">
      <div >

      </div>
      <div class="flex flex-center">
        Ideas List Page
      </div>

      <q-dialog v-model="modalOpen" persistent transition-show="fade" transition-hide="fade">
        <q-card class="bg-primary text-white" style="width: 700px; max-width: 80vw;">
          <q-bar >
            <div class="text-h3 dialog-header">New Idea</div>
            <q-space />

            <q-btn dense flat icon="close" v-close-popup @click="modalOpen = false">
              <q-tooltip class="bg-white text-primary">Close</q-tooltip>
            </q-btn>
          </q-bar>

          <q-card-section>
            <div class="text-h6">Idea Title</div>
            <q-input v-model="ideaTitle"/>
          </q-card-section>

          <q-card-section>
            <div class="text-h6 q-mb-sm">Idea Description</div>
            <q-editor v-model="ideaDescription" min-height="5rem" placeholder="Description of the idea" style="color: #696969 !important"/>
          </q-card-section>

          <!--  TODO Add image upload functionality -->

          <q-card-section class="flex justify-center full-width">
            <q-btn flat style="color: white" label="Cancel" :outline="true" class="q-mx-sm" @click="modalOpen = false"/>
            <q-btn color="primary" label="Submit" class="q-mx-sm" @click="createIdea"/>
          </q-card-section>
        </q-card>
      </q-dialog>
    </q-page>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import { getUserStore } from 'stores/user.js';
import { useQuasar } from 'quasar';

import createIdeaMutation from 'mutation/create_idea.graphql';

export default defineComponent({
  name: 'IdeasList',
  data() {
    return {
      modalOpen: false,
      ideaTitle: '',
      ideaDescription: '',
      userStore: getUserStore()
    }
  },
  methods: {
    async createIdea() {
      const $q = useQuasar()

      if(this.ideaTitle.length === 0 || this.ideaDescription.length === 0) {
        $q.dialog({
          dark: true,
          title: 'Error',
          message: 'Please make sure a title and description is provided!'
        });
      }

      this.$apollo.mutate({
        mutation: createIdeaMutation,
        fetchPolicy: 'network-only',
        variables: {
          'user_name': this.userStore.name,
          'title': this.ideaTitle,
          'description': this.ideaDescription
        }
      }).then(result => {
        console.log(result.data.create_idea);
        if (result.data.create_idea) {
          this.modalOpen = false;
        }
      });
    }
  }
});
</script>
