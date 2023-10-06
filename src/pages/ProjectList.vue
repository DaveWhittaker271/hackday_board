<template>
  <div class="flex justify-end full-width">
    <q-btn color="blue" icon="style" label="New Project" :disabled="!userStore.logged_in" @click="() => showAddProjectDialog()">
      <q-tooltip v-if="!userStore.logged_in">
        Please login to submit a project
      </q-tooltip>
    </q-btn>
  </div>
  <q-page class="flex">
    <template
      v-for="(project, index) in projects"
      :key="index"
    >
      <ProjectCard
        :project-id="project.id"
        :title="project.title"
        :description="project.description"
        :submitted-by="project.submitted_by_name"
        :picture-url="project.picture_url"
      />
    </template>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import ProjectCard from "pages/projects/ProjectCard.vue";

import getProjectsQuery from "query/get_projects.graphql";
import {reactiveUserStore} from "stores/user";
import AddProjectDialog from "pages/projects/AddProjectDialog.vue";

export default defineComponent({
  name: 'ProjectList',
  components: {
    ProjectCard
  },
  props: {
    projects: Object,
  },
  data() {
    return {
      userStore: reactiveUserStore,
    }
  },
  methods: {
    showAddProjectDialog(project) {

      this.$q.dialog({
        component: AddProjectDialog,
        componentProps: {
          existingIdea: project,
        }
      }).onOk(() => {
        this.$apollo.queries.projects.refetch();
      });
    },
  },
  apollo: {
    projects: {
      query: getProjectsQuery,
      fetchPolicy: 'network-only',
      update: data => data.get_projects.projects
    }
  },
})
</script>
