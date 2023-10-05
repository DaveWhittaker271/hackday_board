<template>
  <q-dialog ref="dialog" persistent transition-show="fade" transition-hide="fade">
    <q-card class="bg-primary text-white" style="width: 700px; max-width: 80vw;">
      <q-bar >
        <div class="text-h3 dialog-header">New Project</div>
        <q-space />

        <q-btn dense flat icon="close" v-close-popup @click="closeModal">
          <q-tooltip class="bg-white text-primary">Close</q-tooltip>
        </q-btn>
      </q-bar>

      <q-card-section>
        <div class="text-h6">Project Title</div>
        <q-input v-model="projectTitle" borderless class="bg-white text-grey-8 q-px-md q-border"/>
      </q-card-section>

      <q-card-section>
        <div class="text-h6 q-mb-sm">Project Description</div>
        <q-editor v-model="projectDescription" min-height="7.5rem" placeholder="Description of the project" class="text-grey-8"/>
      </q-card-section>
      <q-card-section>
        <div class="text-h6 q-mb-sm">Attach Files</div>
        <FileUploader
          ref="uploader"
          :extra-fields="uploaderExtraFields()"
          @uploaded="uploadFinished"
        />
      </q-card-section>

      <q-card-section class="flex justify-center full-width">
        <q-btn flat color="white" label="Cancel" :outline="true" class="q-mx-sm" @click="closeModal"/>
        <q-btn color="primary" label="Submit" class="q-mx-sm" @click="createProject"/>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import FileUploader from "components/FileUploader.vue";

import createProjectMutation from 'mutation/create_project.graphql';

export default {
  components: {
    FileUploader,
  },
  props: {
    existingProject: Object,
  },
  data() {
    return {
      projectId: null,
      projectTitle: '',
      projectDescription: '',
      savingDialog: null,
      savedProjectId: null,
    }
  },
  mounted() {
    if (this.existingProject !== undefined) {
      console.log('existing');
      this.projectId = this.existingProject.id;
      this.projectTitle = this.existingProject.title;
      this.projectDescription = this.existingProject.description;
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
    uploaderExtraFields() {
      return [
        {name: 'project_id', value: this.savedProjectId}
      ];
    },
    async createProject() {
      if(this.projectTitle.length === 0 || this.projectDescription.length === 0) {
        this.$q.dialog({
          dark: true,
          title: 'Error',
          message: 'Please make sure a title and description are provided!'
        });

        return;
      }

      this.savingDialog = this.$q.dialog({
        message: 'Saving project...',
        progress: true,
        persistent: true,
        ok: false
      })

      await this.$apollo.mutate({
        mutation: createProjectMutation,
        fetchPolicy: 'network-only',
        variables: {
          'id': this.projectId,
          'title': this.projectTitle,
          'description': this.projectDescription
        }
      }).then(async result => {
        this.savedProjectId = result.data.create_project;

        if (!this.savedProjectId) {
          this.$q.dialog({
            dark: true,
            title: 'Error',
            message: 'Error submitting project, please try again...'
          });
        }

        const $fileUploads = this.$refs.uploader;
        const hasFiles = $fileUploads.hasFiles();

        if (!hasFiles) {
          this.savingDialog.hide();
          this.$emit('ok')
          this.hide();

          return;
        }

        this.$forceUpdate;
        await this.$nextTick();

        this.savingDialog.update({'message': 'Uploading files...'});
        $fileUploads.upload();
      });
    },
    uploadFinished() {
      this.savingDialog.hide();
      this.$emit('ok')
      this.hide();
    },
    closeModal() {
      this.$emit('hide');
    }
  }
}
</script>