<template>
    <q-page class="flex">
      <template
          v-for="(project, index) in projects"
          :key="index"
      >
        <ProjectCard
          :title="project.title"
          :description="project.description"
          :submitted-by="project.submitted_by_name"
        />
      </template>
    </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import ProjectCard from "layouts/components/ProjectCard.vue";

import getProjectsQuery from "query/get_projects.graphql";

export default defineComponent({
  name: 'ProjectList',
  components: {
    ProjectCard
  },
  props: {
    projects: Object,
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
