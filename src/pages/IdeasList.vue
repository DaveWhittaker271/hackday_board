<template>
  <div class="full-width">
    <div class="flex justify-end" style="width: 100%">
      <q-btn color="blue" icon="style" label="New idea" @click="modalOpen = true"/>
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
              <q-btn flat color="blue" label="Edit" size="md" @click="editModal(idea);"/>
            </q-card-section>
            <q-card-section>
              <img src="https://cdn.quasar.dev/img/mountains.jpg" class="full-width">
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

      <q-dialog v-model="modalOpen" persistent transition-show="fade" transition-hide="fade">
        <q-card class="bg-primary text-white" style="width: 700px; max-width: 80vw;">
          <q-bar >
            <div class="text-h3 dialog-header">New Idea</div>
            <q-space />

            <q-btn dense flat icon="close" v-close-popup @click="dismissModal">
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
            <q-btn flat style="color: white" label="Cancel" :outline="true" class="q-mx-sm" @click="dismissModal"/>
            <q-btn color="primary" label="Submit" class="q-mx-sm" @click="createIdea"/>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="detailsModal">
        <q-card style="width: 900px; max-width: 80vw;">
          <q-card-section>
            <div class="text-h4 q-font-bold">{{ selectedIdea.title }}</div>
          </q-card-section>

          <q-card-section>
            <div class="q-pa-md">
              <q-carousel
                  animated
                  v-model="slide"
                  arrows
                  navigation
                  infinite
              >
                <!--  TODO: Pull through images from ideas into the carousel  -->
                <q-carousel-slide :name="1" img-src="https://cdn.quasar.dev/img/mountains.jpg" />
                <q-carousel-slide :name="2" img-src="https://cdn.quasar.dev/img/parallax1.jpg" />
                <q-carousel-slide :name="3" img-src="https://cdn.quasar.dev/img/parallax2.jpg" />
                <q-carousel-slide :name="4" img-src="https://cdn.quasar.dev/img/quasar.jpg" />
              </q-carousel>
            </div>
          </q-card-section>

          <q-card-section class="q-pt-none">
            <div class="text-body1" v-html="selectedIdea.description"/>
          </q-card-section>

          <q-card-section>
            <q-expansion-item
                expand-separator
                icon="message"
                label="Comments"
                class="q-font-bold"
            >
              <q-card bordered>
                <div>
                  <q-card-section v-if="showTextArea">
                    <div class="text-h6 q-mb-sm">Add a comment</div>
                    <q-editor v-model="newCommentText" min-height="5rem" placeholder="Comment text" style="color: #696969 !important"/>
                  </q-card-section>

                  <q-btn
                    color="primary"
                    :label="!showTextArea ? 'Add Comment' : 'Close'"
                    class="q-ma-sm"
                    @click="showTextArea = !showTextArea"
                  />

                  <q-btn
                    v-if="showTextArea"
                    color="primary"
                    label="Submit"
                    class="q-ma-sm"
                    @click="submitComment"
                  />
                  <q-space />
                </div>
                <q-card v-for="comment in selectedIdea.comments" :key="comment.id" bordered>
                  <q-card-section class="flex column">
                    <div class="row items-center">
                      <q-avatar size="35px">
                        <img :src="comment.user_image">
                      </q-avatar>

                      <div class="text-subtitle1 q-ml-md items-center">
                        <div>{{ comment.name }}</div>
                      </div>
                      <q-space />

                      <div class="text-weight-light">
                        {{ getDateString(comment.timecreated) }}
                      </div>
                    </div>
                    <div class="text-body1 q-my-md">
                      {{ comment.text }}
                    </div>
                  </q-card-section>
                </q-card>
              </q-card>
            </q-expansion-item>
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
import { getUserStore } from 'stores/user.js';

import createIdeaMutation from 'mutation/create_idea.graphql';
import deleteIdeaMutation from 'mutation/delete_idea.graphql';
import addCommentMutation from 'mutation/add_comment.graphql';
import getIdeasQuery from 'query/get_ideas.graphql';

export default defineComponent({
  name: 'IdeasList',
  data() {
    return {
      modalOpen: false,
      detailsModal: false,
      showTextArea: false,
      ideaId: null,
      newCommentText: '',
      ideaTitle: '',
      ideaDescription: '',
      userStore: getUserStore(),
      totalIdeas: [],
      selectedIdea: null,
      slide: 1
    }
  },
  apollo: {
    totalIdeas: {
      query: getIdeasQuery,
      fetchPolicy: 'network-only',
      variables() {
        return {
          project_id: 1
        }
      },
      update: data => data.get_ideas.ideas
    }
  },
  methods: {
    async createIdea() {
      if(this.ideaTitle.length === 0 || this.ideaDescription.length === 0) {
        return;
      }

      this.$apollo.mutate({
        mutation: createIdeaMutation,
        fetchPolicy: 'network-only',
        variables: {
          'id': this.ideaId,
          'user_name': this.userStore.name,
          'title': this.ideaTitle,
          'description': this.ideaDescription
        }
      }).then(result => {
        if (result.data.create_idea) {
          this.modalOpen = false;
          this.$apollo.queries.totalIdeas.refetch();
        }
      });
    },
    dismissModal() {
      this.ideaId = null;
      this.ideaTitle = '';
      this.ideaDescription = '';

      this.modalOpen = false;
    },
    editModal(idea) {
      this.ideaId = idea.id;
      this.ideaTitle = idea.title;
      this.ideaDescription = idea.description;

      this.modalOpen = true;
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
          this.modalOpen = false;
          this.$apollo.queries.totalIdeas.refetch();
        }
      });
    },
    submitComment() {
      if (this.newCommentText === '') {
        return;
      }

      this.$apollo.mutate({
        mutation: addCommentMutation,
        fetchPolicy: 'network-only',
        variables: {
          'idea_id': this.selectedIdea.id,
          'text': this.newCommentText
        }
      }).then(result => {
        if (result.data.add_comment) {
          this.detailsModal = false;
          this.newCommentText = '';
          this.selectedIdea = null;
          this.$apollo.queries.totalIdeas.refetch();
        }
      });
    },
    getDateString(timestamp) {
      let newDate = new Date();
      newDate.setTime(timestamp * 1000);
      return newDate.toUTCString();
    }
  }
});
</script>
