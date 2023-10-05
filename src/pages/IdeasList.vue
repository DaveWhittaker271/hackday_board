<template>
  <div>
    <div class="flex justify-end full-width">
      <q-btn color="blue" icon="style" label="New idea" :disabled="!userStore.logged_in" @click="() => showAddIdeaDialog()">
        <q-tooltip v-if="!userStore.logged_in">
          Please login to submit an idea
        </q-tooltip>
      </q-btn>
    </div>

    <q-page class="flex flex-center full-width">
      <div class="flex flex-block">
        <q-spinner-cube
            v-if="$apollo.loading"
            color="blue"
            size="5.5em"
        />
        <div v-else class="q-gutter full-width">
          <q-card
              v-for="(idea, index) in totalIdeas"
              class="my-card q-ma-md col-6"
              bordered
              :key="`card-${index}`"
          >
            <q-card-section class="row">
              <div class="text-h5 idea_card_title">{{ idea.title }}</div>
              <q-space />
              <q-btn flat color="red" label="Delete" size="md" @click="deleteIdea(idea.id);"/>
              <q-btn flat color="blue" label="Edit" size="md" @click="showAddIdeaDialog(idea);"/>
            </q-card-section>
            <q-card-section>
              <img v-if="idea.picture_url" :src="idea.picture_url" class="full-width" style="max-width: 500px">
              <q-card-actions align="right">
                <div class="row items-center">
                  <q-avatar size="35px">
                    <img :src="idea.user_image">
                  </q-avatar>

                  <div class="text-subtitle1 q-ml-md items-center">
                    <div>{{ idea.user_name }}</div>
                  </div>
                </div>
                <q-btn color="blue" label="View Idea" size="md" class="q-ml-md" @click="detailsModal = true; selectedIdea = idea"/>
                <q-space />
                <q-btn flat round color="red" icon="favorite" />
                <q-btn flat round color="teal" icon="comment" />
              </q-card-actions>
            </q-card-section>
          </q-card>
        </div>
      </div>
      <q-dialog v-model="detailsModal">
        <q-card style="width: 900px; max-width: 80vw;">
          <q-card-section>
            <div class="text-h4 q-font-bold">{{ selectedIdea.title }}</div>
          </q-card-section>

          <q-card-section v-if="selectedIdea.files && selectedIdea.files.length > 0">
            <div class="q-pa-md">
              <q-carousel
                animated
                v-model="slide"
                arrows
                navigation
                infinite
              >
                <q-carousel-slide
                  v-for="(img, index) in selectedIdea.files"
                  :key="index"
                  :name="1"
                  :img-src="img"
                />
              </q-carousel>
            </div>
          </q-card-section>

          <q-card-section class="q-pt-none">
            <div class="text-body1">{{ selectedIdea.description }}</div>
          </q-card-section>

          <q-card-actions align="right" class="bg-white text-teal">
            <q-btn flat label="Close" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </q-page>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import { reactiveUserStore } from 'stores/user.js';
import { useRoute } from "vue-router";

import AddIdeaDialog from "pages/ideas/AddIdeaDialog.vue";

import deleteIdeaMutation from 'mutation/delete_idea.graphql';
import getIdeasQuery from 'query/get_ideas.graphql';

export default defineComponent({
  name: 'IdeasList',
  data() {
    return {
      userStore: reactiveUserStore,
      detailsModal: false,
      totalIdeas: [],
      selectedIdea: null,
      slide: 1,
      route: useRoute(),
    }
  },
  methods: {
    showAddIdeaDialog(idea) {
      const projectId = Number(this.route.params.id);

      this.$q.dialog({
        component: AddIdeaDialog,
        componentProps: {
          existingIdea: idea,
          projectId: projectId
        }
      }).onOk(() => {
        this.$apollo.queries.totalIdeas.refetch();
      });
    },
    deleteIdea(id) {
      this.$apollo.mutate({
        mutation: deleteIdeaMutation,
        fetchPolicy: 'network-only',
        variables: {
          'id': id,
        }
      }).then(result => {
        if (result.data.delete_idea) {
          this.$apollo.queries.totalIdeas.refetch();
        }
      });
    }
  },
  apollo: {
    totalIdeas: {
      query: getIdeasQuery,
      fetchPolicy: 'network-only',
      variables() {
        return {
          project_id: Number(this.route.params.id)
        }
      },
      update: data => data.get_ideas.ideas
    }
  },
});
</script>
