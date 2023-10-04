<template>
  <q-dialog ref="dialog" persistent transition-show="fade" transition-hide="fade">
    <q-card class="bg-primary text-white" style="width: 700px; max-width: 80vw;">
      <q-bar >
        <div class="text-h3 dialog-header">New Idea</div>
        <q-space />

        <q-btn dense flat icon="close" v-close-popup @click="closeModal">
          <q-tooltip class="bg-white text-primary">Close</q-tooltip>
        </q-btn>
      </q-bar>

      <q-card-section>
        <div class="text-h6">Idea Title</div>
        <q-input v-model="ideaTitle" borderless class="bg-white text-grey-8 q-px-md q-border"/>
      </q-card-section>

      <q-card-section>
        <div class="text-h6 q-mb-sm">Idea Description</div>
        <q-editor v-model="ideaDescription" min-height="7.5rem" placeholder="Description of the idea" class="text-grey-8"/>
      </q-card-section>
      <q-card-section>
        <div class="text-h6 q-mb-sm">Attach Files</div>
        <FileUploader />
      </q-card-section>

      <q-card-section class="flex justify-center full-width">
        <q-btn flat color="white" label="Cancel" :outline="true" class="q-mx-sm" @click="closeModal"/>
        <q-btn color="primary" label="Submit" class="q-mx-sm" @click="createIdea"/>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import FileUploader from "components/FileUploader.vue";

import createIdeaMutation from 'mutation/create_idea.graphql';

export default {
  components: {
    FileUploader,
  },
  data() {
    return {
      ideaTitle: '',
      ideaDescription: '',
    }
  },
  emits: ['ok', 'hide'],
  methods: {
    show () {
      this.$refs.dialog.show();
    },
    hide () {
      this.$refs.dialog.hide();
    },
    async createIdea() {
      if(this.ideaTitle.length === 0 || this.ideaDescription.length === 0) {
        this.$q.dialog({
          dark: true,
          title: 'Error',
          message: 'Please make sure a title and description is provided!'
        });
      }

      this.$apollo.mutate({
        mutation: createIdeaMutation,
        fetchPolicy: 'network-only',
        variables: {
          'title': this.ideaTitle,
          'description': this.ideaDescription
        }
      }).then(result => {
        console.log(result.data.create_idea);
        if (result.data.create_idea) {
          this.$emit('ok')
          this.hide()
        }
      });
    },
    closeModal() {
      this.$emit('hide');
    }
  }
}
</script>