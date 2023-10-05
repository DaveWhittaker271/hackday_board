<template>
  <q-layout view="hHh lpr fFf">
    <q-header class="header-bar">
      <q-toolbar class="q-px-sm">
        <router-link
          to="/"
          custom
          v-slot="{ navigate }"
        >
          <q-toolbar-title class="row items-center cursor-pointer" shrink @click="navigate">
            <q-icon name="bi-lightbulb" class="q-pa-sm"/>
            <span class="logo vertical-middle">
              Hackday Ideas
            </span>
          </q-toolbar-title>
        </router-link>

        <div class="col">
          <q-btn-dropdown v-if="$route.path !== '/'"  :unelevated="true" label="Projects" class="q-mx-lg full-height">
            <q-list>
              <template v-for="project in projects" :key="project.id">
                <router-link
                  :to="`/project/${project.id}/ideas`"
                  custom
                  v-slot="{ navigate }"
                >
                  <q-item clickable v-close-popup @click="navigate">
                    <q-item-section>
                      <q-item-label>{{ project.title }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </router-link>
              </template>
            </q-list>
          </q-btn-dropdown>
        </div>

        <LoginArea />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="flex q-pa-lg justify-center full-width">
        <router-view/>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue'

import LoginArea from 'layouts/components/LoginArea.vue';

import getProjectsQuery from "query/get_projects.graphql";

export default defineComponent({
  name: 'MainLayout',
  components: {
    LoginArea
  },
  data() {
    return {
      projects: [],
    }
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
