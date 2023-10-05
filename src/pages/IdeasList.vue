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
          <q-card-section class="flex row">
            <div class="text-h4 q-font-bold">{{ selectedIdea.title }}</div>
            <q-space />
            <q-btn
              color="primary"
              label="Register Interest"
              class="q-ma-sm"
              @click="registerInterest"
            />
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
            <div class="text-body1" v-html="selectedIdea.description"/>
          </q-card-section>

          <q-card-section>
            <q-expansion-item
              expand-separator
              icon="person"
              label="Interested"
              class="q-font-bold"
            >
              <q-card v-for="(interest, index) in selectedIdea.interested" bordered :key="index">
                <q-card-section class="flex row items-center">
                  <q-avatar size="35px">
                    <img :src="interest.user_image">
                  </q-avatar>
                  <div class="text-subtitle1 q-ml-md items-center">
                    <div>{{ interest.user_name }}</div>
                  </div>
                  <q-space />
                </q-card-section>
              </q-card>
            </q-expansion-item>
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
                    <div class="text-body1 q-my-md" v-html="comment.text"/>
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
import { reactiveUserStore } from 'stores/user.js';

import AddIdeaDialog from "pages/ideas/AddIdeaDialog.vue";

import deleteIdeaMutation from 'mutation/delete_idea.graphql';
import addCommentMutation from 'mutation/add_comment.graphql';
import registerInterestMutation from 'mutation/register_interest.graphql';
import getIdeasQuery from 'query/get_ideas.graphql';

export default defineComponent({
  name: 'IdeasList',
  data() {
    return {
      userStore: reactiveUserStore,
      detailsModal: false,
      showTextArea: false,
      ideaId: null,
      newCommentText: '',
      ideaTitle: '',
      ideaDescription: '',
      totalIdeas: [],
      selectedIdea: null,
      slide: 1
    }
  },
  methods: {
    showAddIdeaDialog(idea) {
      this.$q.dialog({
        component: AddIdeaDialog,
        componentProps: {
          existingIdea: idea
        }
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
          this.modalOpen = false;
          this.$apollo.queries.totalIdeas.refetch();
        }
      });
    },
    registerInterest() {
      this.$apollo.mutate({
        mutation: registerInterestMutation,
        fetchPolicy: 'network-only',
        variables: {
          'idea_id': this.selectedIdea.id,
        }
      }).then(result => {
        if (result.data.register_interest) {
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
});
</script>
