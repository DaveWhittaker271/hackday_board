<template>
    <q-page class="flex">
      <ProjectCard
          title="First Project"
          submitted-by="John Smith"
          description="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."
          image-url="https://cdn.quasar.dev/img/mountains.jpg"
      />
      <ProjectCard
          title="Second Project"
          submitted-by="George Clarke"
          description="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."
          image-url="https://cdn.quasar.dev/img/mountains.jpg"
      />
      <ProjectCard
          title="Third Project"
          submitted-by="Jenny Jarvis"
          description="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."
          image-url="https://cdn.quasar.dev/img/mountains.jpg"
      />
    </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import ProjectCard from "layouts/components/ProjectCard.vue";

export default defineComponent({
  name: 'ProjectList',
  components: {
    ProjectCard,
  }
})
</script>
