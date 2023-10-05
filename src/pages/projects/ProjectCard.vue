<template>
  <div class="q-pa-md row items-start q-gutter-md">
    <q-card class="my-card">
      <img :src="imageUrl">

      <q-card-section>
        <div class="text-h6">{{ title }}</div>
        <div class="text-subtitle2">by {{ submittedBy }}</div>
      </q-card-section>

      <q-card-section class="q-pt-none">
        {{ description }}
      </q-card-section>

      <q-card-actions class="justify-center">

        <router-link
          :to="`/project/${projectId}/ideas}`"
          custom
          v-slot="{ navigate }"
        >
          <q-btn flat @click="navigate">View</q-btn>
        </router-link>
      </q-card-actions>
    </q-card>
  </div>
</template>

<script>
export default {
  name: 'ProjectCard',
  props: {
    title: String,
    submittedBy: String,
    description: String,
    imageUrl: String,
  },
}
</script>

<style lang="scss" scoped>
  .my-card {
    width: 100%;
    max-width: 250px;
  }
</style>