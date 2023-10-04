<template>
  <div>
    <div class="flex justify-end full-width">
      <q-btn color="blue" icon="style" label="New idea" :disabled="!userStore.logged_in" @click="showAddIdeaDialog">
        <q-tooltip v-if="!userStore.logged_in">
          Please login to submit an idea
        </q-tooltip>
      </q-btn>
    </div>

    <q-page class="flex flex-center full-width">
      <div class="flex flex-center">
        Ideas List Page
      </div>
    </q-page>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import { reactiveUserStore } from 'stores/user.js';

import AddIdeaDialog from "pages/ideas/AddIdeaDialog.vue";

export default defineComponent({
  name: 'IdeasList',
  data() {
    return {
      userStore: reactiveUserStore
    }
  },
  methods: {
    showAddIdeaDialog() {
      this.$q.dialog({
        component: AddIdeaDialog
      });
    },
  }
});
</script>
